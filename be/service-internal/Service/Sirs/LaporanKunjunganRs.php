<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Client;
use Integrasi\Service\Sirs\Models\LaporanKunjunganPasienRsDiagnosa;
use Integrasi\Service\Sirs\Models\Lookup;
use Integrasi\Components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

class LaporanKunjunganRs extends \Integrasi\Contracts\DocoImplement
{
    public static $look_exlude = [402,628];

    const DIPERIKSA_PDFTRN = 2;

    const PULANG_PDFTRN = 4;

    const BATAL_PDFTRN = 402;

    const BELUM_PDFTRN = 486;

    const DIPERIKSA_ADMISI = 441;

    const PULANG_ADMISI = 487;

    const BATAL_ADMISI = 453;

    const BELUM_ADMISI = 440;

    public static $look_diperiksa = [self::DIPERIKSA_PDFTRN,self::DIPERIKSA_ADMISI];

    public static $look_pulang = [self::PULANG_PDFTRN,self::PULANG_ADMISI];

    public static $look_batal = [self::BATAL_PDFTRN,self::BATAL_ADMISI];

    public static $look_belum = [self::BELUM_PDFTRN,self::BELUM_ADMISI];

    public function execute()
    {
        $jenisIdentitas = $this->getListJenisIdentitas();
        $dataObject = $this->getObjectData()->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($dataObject as $value) {
            if (!empty($value['diagnosa_penyerta'])) {
                $value['diagnosa_penyerta'] = str_replace('$', ', ', $value['diagnosa_penyerta']);
            }

            $noTlp = isset($value['no_telepon_pasien']) ? $value['no_telepon_pasien'] : $value['no_mobile_pasien'];
            $noIdentitas = null;
            if (!empty($value['additional_pasien'])) {
                $pasienAdds = json_decode($value['additional_pasien'], JSON_UNESCAPED_SLASHES);
                if (!empty($pasienAdds) && isset($pasienAdds[0]) && is_array($pasienAdds[0])) {
                    foreach($pasienAdds as $adds) {
                        if (isset($jenisIdentitas[$adds['jenisidentitas']])) {
                            if (!empty($noIdentitas)) {$noIdentitas .= "\n";}
                            $noIdentitas .= $jenisIdentitas[$adds['jenisidentitas']] . ' - ' . $adds['no_identitas_pasien'];
                        }
                    }
                }
            }

            $tmp[1]  = $no;
            $tmp[2]  = date('d M Y', strtotime($value['tgl_pendaftaran']));
            $tmp[3]  = $value['no_pendaftaran'];
            $tmp[4]  = $value['no_rekam_medik'];
            $tmp[5]  = $value['jenis_kelamin'];
            $tmp[6]  = $value['nama_pasien'];
            $tmp[7]  = $value['tanggal_lahir'];
            $tmp[8]  = $value['umur'];
            $tmp[9]  = $value['alamat_pasien'];
            $tmp[10] = !empty($noIdentitas) ? $noIdentitas : '-';
            $tmp[11] = $value['carabayar_nama'];
            $tmp[12] = $value['penjamin_nama'];
            $tmp[13] = $value['kelaspelayanan_nama'];
            $tmp[14]  = $value['jeniskasuspenyakit_nama'];
            $tmp[15]  = $value['instalasi_nama'];
            $tmp[16]  = $value['ruangan_nama'];
            $tmp[17]  = $value['dokterdpjp_nama'];

            if (!empty($value['diagnosa_penyerta'])) {
                $value['diagnosa_penyerta'] = str_replace('$', ', ', $value['diagnosa_penyerta']);
            }

            $tmp[18] = $value['diagnosa_utama'];
            $tmp[19] = $value['diagnosa_penyerta'];
            $tmp[20] = $value['status_periksa'];
            $tmp[21] = isset($value['no_telepon_pasien'])? $value['no_telepon_pasien'] : $value['no_mobile_pasien'];
            $tmp[22] = $value['kondisikeluar_nama'];
            $tmp[23] = $value['carakeluar_nama'];
            $tmp[24] = $value['kunjungan_nama'];
            $tmp[25] = isset($value['asalrujukan_nama']) ? $value['asalrujukan_nama'] : null;
            $tmp[26] = isset($value['rujukandari_nama']) ? $value['rujukandari_nama'] : null;
            $tmp[27] = isset($value['nama_perujuk']) ? $value['nama_perujuk'] : null;

            $row[] = $tmp;
            $tmpCache[] = $tmp;
            if (($no%50) == 0) {
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
            'service' => 'Sirs-LaporanKunjunganRs',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function getObjectData()
    {
        $request = $this->filter;
        
        $model   = new LaporanKunjunganPasienRsDiagnosa;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_pendaftaran']);
            }

            if(isset($request['advanced-filter']['jenis_kelamin'])) {
                $request['advanced-filter']['jeniskelamin'] = $request['advanced-filter']['jenis_kelamin'];
                unset($request['advanced-filter']['jenis_kelamin']);
            }

            if(isset($request['advanced-filter']['diagnosa_utama'])) {
                $icdUtama = $request['advanced-filter']['diagnosa_utama'];
                unset($request['advanced-filter']['diagnosa_utama']);
                $query->andWhere(['diagnosa_utama_id' => $icdUtama]);
            }

            if(isset($request['advanced-filter']['diagnosa_penyerta'])) {
                $icdPenyerta = $request['advanced-filter']['diagnosa_penyerta'];
                unset($request['advanced-filter']['diagnosa_penyerta']);
                $query->andWhere(['ilike', 'diagnosa_penyerta_id', $icdPenyerta]);
            }

            if(isset($_GET['advanced-filter']['kelaspelayanan_id'])) {
                $kelasPelayanan_id = $_GET['advanced-filter']['kelaspelayanan_id'];
                unset($_GET['advanced-filter']['kelaspelayanan_id']);
                $query->andWhere(['kelaspelayanan_id' => $kelasPelayanan_id]);
            }

            if(isset($request['advanced-filter']['status_periksa'])) {
                $status_periksa = $request['advanced-filter']['status_periksa'];
                unset($request['advanced-filter']['status_periksa']);
                switch ($status_periksa) {
                    case self::DIPERIKSA_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    case self::PULANG_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    case self::BATAL_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    case self::BELUM_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    default:
                        $query->andWhere(['id_status_periksa' => $status_periksa]);
                        break;
                }
            }

            if(isset($request['advanced-filter']['no_identitas'])) {
                $noIdentitas = $request['advanced-filter']['no_identitas'];
                unset($request['advanced-filter']['no_identitas']);
                $query->andWhere(['ilike', 'additional_pasien', $noIdentitas]);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query->andWhere([
            'NOT IN', 'id_status_periksa', [
                DocoConstants::STATUS_PERIKSA_BTL_KUNJ,
                DocoConstants::STATUS_PERIKSA_BTL_PERIKSA,
                DocoConstants::STATUS_PERIKSA_BTL_KONSUL,
                DocoConstants::STATUS_PERIKSA_BTL_RUJUK_RAWAT_INAP,
                DocoConstants::STATUS_RANAP_BATAL_RAWAT
            ]
        ]);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }

    protected function getListJenisIdentitas()
    {
        $jenisIds = [];
        $query = Lookup::find()->select([
            'lookup_id', 'lookup_name', 'lookup_value'
        ])->where([
            'lookup_type' => 'jenis_identitas'
        ])->orderBy(['lookup_urutan' => SORT_ASC])->all();

        if (!empty($query)) {
            foreach ($query as $model) {
                $jenisIds[$model->lookup_id] = $model->lookup_value;
            }
        }
        return $jenisIds;
    }
}