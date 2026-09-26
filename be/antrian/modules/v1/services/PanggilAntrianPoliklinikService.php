<?php

namespace app\modules\v1\services;

use Yii;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

use app\modules\v1\services\Contracts\PanggilAntrianInterface;
use app\modules\v1\services\BaseAntrianService;
use yii\helpers\ArrayHelper;

class PanggilAntrianPoliklinikService extends BaseAntrianService implements PanggilAntrianInterface
{
    public function panggilAntrian($data)
    {
        $pendaftranId = ArrayHelper::getValue($data, 'pendaftaran_id');
        $antrian_id = ArrayHelper::getValue($data, 'antrian_id');
        $hari = date('Y-m-d');
        $hariId = DocoHelpers::getIdHariIni();
        $jenisAntrian = DocoConstants::VAR_JA_P;

        if(isset($antrian_id) && !empty($antrian_id)){
            $data = Yii::$app->db->createCommand("
                SELECT 
                    a.no_antrian,
                    b.pendaftaran_id,
                    c.pasien_id,
                    c.nama_pasien,
                    d.jadwaldokter_id,
                    a.pegawai_id,
                    e.hari
                FROM antrian_t a
                LEFT JOIN pendaftaran_t b ON a.pendaftaran_id = b.pendaftaran_id
                LEFT JOIN pasien_m c ON b.pasien_id = c.pasien_id
                LEFT JOIN jadwaldokter_m d ON d.jadwaldokter_id = a.jadwaldokter_id
                LEFT JOIN jadwalbukapoli_m e ON d.jadwalbukapoli_id = e.jadwalbukapoli_id
                WHERE a.antrian_id = :antrian_id and d.is_active = true and d.is_deleted = false
            ")->bindValue(':antrian_id',$antrian_id)->queryOne();
        }else{
            $data = Yii::$app->db->createCommand("
                SELECT 
                    a.no_antrian,
                    b.pendaftaran_id,
                    c.pasien_id,
                    c.nama_pasien,
                    d.jadwaldokter_id,
                    a.pegawai_id,
                    e.hari
                FROM antrian_t a
                JOIN pendaftaran_t b ON a.pendaftaran_id = b.pendaftaran_id
                JOIN pasien_m c ON b.pasien_id = c.pasien_id
                JOIN jadwaldokter_m d ON a.ruangan_id = d.ruangan_id and a.pegawai_id = d.pegawai_id 
                JOIN jadwalbukapoli_m e ON d.jadwalbukapoli_id = e.jadwalbukapoli_id and e.hari = {$hariId}
                WHERE a.pendaftaran_id = {$pendaftranId} and a.is_deleted = false and a.jenisantrian_id = {$jenisAntrian} and a.tgl_antrian::DATE = '{$hari}' and d.is_active = true and d.is_deleted = false
            ")->queryOne();
        }

        if (!empty($data)) {
            $dataDisplay["panggil_antrian_poliklinik_with_dokter"] = [
                'no_antrian' => $data['no_antrian'],
                'nama_pasien' => $data['nama_pasien'],
                'jadwaldokter_id' => $data['jadwaldokter_id'],
                'text_panggil' => DocoHelpers::convertAntrian($data['no_antrian'])
            ];

            $this->publish($dataDisplay);
            return $dataDisplay;
        } else {
            return [
                'status' => 404,
                'title' => 'Proses Gagal!',
                'text' => 'Data tidak ditemukan'
            ];
        }
    }
}