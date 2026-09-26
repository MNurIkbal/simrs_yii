<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Sirs\Models\Instalasi;
use Integrasi\Service\Sirs\Models\LaporanPtmEkgV;
use Integrasi\Service\Sirs\Models\LaporanPtmV;

class LaporanPtmExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $request = $this->filter;

        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        $data = $this->loadData($start, $end, $request)->asArray()->all();
        // $data = $this->data;
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) 
        {
            $tgl_registrasi = date('Y-m-d H:i:s', strtotime($value['tgl_registrasi']));
            $value['tgl_registrasi'] = date('d M Y H:i:s', strtotime($value['tgl_registrasi']));
            $value['tgl_pulang'] =  isset($value['tgl_pulang'])?date('d M Y H:i:s', strtotime($value['tgl_pulang'])):'';
            $value['tanggal_lahir'] = date('d M Y', strtotime($value['tanggal_lahir']));

            $value['pemeriksaan_ekg'] = 'Tidak';
            $modelEkg   = LaporanPtmEkgV::find()
                ->where(['pendaftaran_id' => $value['pendaftaran_id']])
                ->andWhere(['instalasi_id' => $value['instalasi_id']])
                ->andWhere(['pemeriksaan_ekg' => 'YA'])
                ->all();
            if(!empty($modelEkg)) {
                $value['pemeriksaan_ekg'] = 'Ya';
            }

            $nik_pasien = json_decode($value['no_identitas_pasien'], true);
            $identitas_pasien = json_decode($value['additional_pasien'], true);
            $no_ktp = '';
            if(is_array($identitas_pasien) && ! empty($identitas_pasien)) {
                foreach ($identitas_pasien as $identitas) {
                    if($identitas['jenisidentitas'] == '94') {
                        $no_ktp = $identitas['no_identitas_pasien'];
                    }
                }
            } else {
                if (!is_array($nik_pasien) && ! empty($nik_pasien)) {
                    $no_ktp = $nik_pasien;
                }
            }

            $diag_utama_kode = isset($value['diag_utama_kode'])?$value['diag_utama_kode']:'';
            $diag_utama = isset($value['diag_utama'])?$value['diag_utama']:'';
            $diagnosa =  $diag_utama_kode. ' - ' .$diag_utama;
            $nama_keluarga = !empty($value['nama_ayah'])?$value['nama_ayah']:$value['nama_ibu'];
            $nama_keluarga = isset($nama_keluarga)?$nama_keluarga:'';

            $queryJmlKunjungan   = (new \yii\db\Query())
                ->select([
                    'COUNT(pasien_id) AS jumlah_kunjungan'
                ])
                ->from('laporanptm_v')
                ->where(['pasien_id' => $value['pasien_id']])
                ->andWhere(['instalasi_id' => $value['instalasi_id']]);
            
            if(isset($request['advanced-filter'])) {
                if (array_key_exists('instalasi_id', $request['advanced-filter'])) {
                    $queryJmlKunjungan->andWhere(['instalasi_id' => $request['advanced-filter']['instalasi_id']]);
                }
                if (array_key_exists('diagnosa_id', $request['advanced-filter'])) {
                    $queryJmlKunjungan->andWhere(['diagnosa_id' => $request['advanced-filter']['diagnosa_id']]);
                }
            }
            $queryJmlKunjungan->andWhere(['between', 'tgl_registrasi', $start, $tgl_registrasi]);
            $jml_kunjungan = $queryJmlKunjungan->one();
            $object = preg_replace("/<br \/>/", " ", $value['object']);
            
            $tmp[1]  = $no;
            $tmp[2]  = '="' . $no_ktp . '"';
            $tmp[3]  = '="' . $value['nopeserta_bpjs'] . '"';
            $tmp[4]  = $value['nama_pasien'];
            $tmp[5]  = '="' . $value['no_rekam_medik'] . '"';
            $tmp[6]  = $value['tanggal_lahir'];
            $tmp[7]  = '="' . $value['no_telepon_pasien'] . '"';
            $tmp[8]  = $value['alamatemail'];
            $tmp[9]  = $value['alamat_pasien'];
            $tmp[10]  = $value['tgl_registrasi'];
            $tmp[11]  = $value['no_registrasi'];
            $tmp[12]  = $diagnosa;
            $tmp[13]  = $object;
            $tmp[14]  = $value['umur'];
            $tmp[15]  = $jml_kunjungan['jumlah_kunjungan'];
            $tmp[16]  = $value['nama_dokter'];
            $tmp[17]  = $value['golongan_darah'];
            $tmp[18]  = $value['pemeriksaan_ekg'];
            $tmp[19]  = $nama_keluarga;
            $tmp[20]  = $value['tgl_pulang'];
            $tmp[21]  = $value['keadaan_sekarang'];

            $tmpCache[] = $tmp;
            if (($no%50) == 0) 
            {
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'export-excel:'.$this->unique_str,
                    'message' => json_encode(['unique_process' => $this->unique_str]),
                ]);
                $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
                $prefix++;
                $tmpCache = [];
            }
            $no++;
        }
        $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);

        return json_encode([
            'service' => 'Sirs-LaporanPtmExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData($start, $end, $request)
    {

        $model   = new LaporanPtmV;
        $query   = $model::find();
        
        if(isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tgl_registrasi'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_registrasi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                //unset($request['advanced-filter']['tgl_registrasi']);
            }

            if (array_key_exists('instalasi_id', $request['advanced-filter'])) {
                $instalasi_nama = Instalasi::findOne($request['advanced-filter']['instalasi_id'])->instalasi_nama;
                $query->andWhere(['instalasi_id' => $request['advanced-filter']['instalasi_id']]);

                //unset($request['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('diagnosa_id', $request['advanced-filter'])) {
                //$diagnosa_nama = Diagnosa::findOne($request['advanced-filter']['diagnosa_id'])->diagnosa_nama;
                $diagnosa_nama = 'by Diagnosa';
                $query->andWhere(['diagnosa_id' => $request['advanced-filter']['diagnosa_id']]);

                //unset($request['advanced-filter']['diagnosa_id']);
            }
        }

        // $query->andWhere(['not', ['diag_utama_kode' => null, 'diag_utama' => null]]);
        $query->andWhere(['between', 'tgl_registrasi', $start, $end]);
        $query->orderBy(['tgl_registrasi' => 'desc']);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
