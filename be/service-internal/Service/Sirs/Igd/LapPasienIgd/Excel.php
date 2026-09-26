<?php

namespace Integrasi\Service\Sirs\Igd\LapPasienIgd;

use Yii;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Service\Sirs\Models\LapPasienIgdView;
use Integrasi\Components\DocoConstants;

class Excel extends \Integrasi\Contracts\DocoImplement
{
    public $pointer = 0;
    public $no = 1;
    public $tmpCache = [];

    public function execute()
    {
        $this->generateObjectData();
        $cacheFiles = Yii::$app->cacheFiles;
        $cacheFiles->set($this->unique_str . '-' . $this->pointer, $this->tmpCache);

        return json_encode([
            'service' => 'Sirs-LapPasienIgd-Excel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str,
        ]);
    }

    private function generateObjectData()
    {
        $dataObject = $this->data()->asArray()->all();
        $this->generateExcel($dataObject);
    }

    private function data()
    {
        $request = $this->filter;
        $model = new LapPasienIgdView;
        $query = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if (isset($request['advanced-filter'])) {
            if (isset($request['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_pendaftaran']);
            }

            if (isset($request['dokter_id'])) {
                $dokter_id = $request['dokter_id'];
                $query->andWhere('(dokter_id = ' . $dokter_id. '
                    OR dokter_jaga_id = ' . $dokter_id. ')');

                unset($request['advanced-filter']['dokter_id']); // Unset Advanced Filter pegawai id / dokter id
            }

            if (isset($request['advanced-filter']['no_rekam_medik'])) {
                $no_rekam_medik = $request['advanced-filter']['no_rekam_medik'];
                $query->andWhere(['no_rekam_medik' => $no_rekam_medik]);
                unset($request['advanced-filter']['no_rekam_medik']);
            }

            if (isset($request['advanced-filter']['no_pendaftaran'])) {
                $no_pendaftaran = $request['advanced-filter']['no_pendaftaran'];
                $query->andWhere(['no_pendaftaran' => $no_pendaftaran]);
                unset($request['advanced-filter']['no_pendaftaran']);
            }

            if (isset($request['advanced-filter']['status_periksa'])) {
                $listStatus = explode(",", $request['advanced-filter']['status_periksa']);
                $query->andWhere(['IN', 'status_periksa', array_filter($listStatus)]);
                unset($request['advanced-filter']['status_periksa']);
            }else{
                $query->andWhere(['<>', 'status_periksa', DocoConstants::STATUS_PERIKSA_BTL_KUNJ]);
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query->orderby('tgl_pendaftaran ASC');

        return $query;
    }

    private function generateExcel($data)
    {
        $row = [];
        foreach ($data as $key => $value) {
            $tmp['No']  = $this->no;
            $value['tgl_pendaftaran'] = date("j F Y", strtotime($value['tgl_pendaftaran']));
                
            unset($value['instalasi_nama']);
            unset($value['jeniskelamin']);
            unset($value['ruangan_nama']);
            $tempdokterjaga = $value['dokter_jaga'];
            unset($value['dokter_jaga']);

            $value['cara_bayar'] = $value['carabayar_nama'];
            unset($value['carabayar_nama']);
            $value['penjamin'] = $value['penjamin_nama'];
            unset($value['penjamin_nama']);
            $value['jenis_kasus_penyakit'] = $value['jeniskasuspenyakit_nama'];
            unset($value['jeniskasuspenyakit_nama']);
            $value['dokter_jaga'] = $tempdokterjaga;
            $value['dokter_penanggungjawab'] = $value['dokter'];
            unset($value['dokter']);
            $value['status'] = $value['status_periksa_nama'];
            unset($value['status_periksa_nama']);
            unset($value['status_periksa']);
            $value['no_telph_1'] = $value['no_telepon_pasien'];
            $value['no_telph_2'] = $value['no_mobile_pasien'];
            unset($value['no_telepon_pasien']);
            unset($value['no_mobile_pasien']);
            $this->tmpCache[] = $value;
            if (($this->no % 50) == 0) {
                $this->flagZero();
            }
            $this->no++;
        }
        return $row;
    }

    private function flagZero()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);
        $cacheFiles->set($this->unique_str . '-' . $this->pointer, $this->tmpCache);
        $this->pointer++;
        $this->tmpCache = [];
    }
}
