<?php

namespace Integrasi\Service\Sirs\Remunerasi;

use Integrasi\Components\Services\RemunerasiService;
use Yii;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunPegawai;
use yii\helpers\ArrayHelper;

class SyncData extends \Integrasi\Contracts\DocoImplement
{
    protected $dataPegawai = [];

    public function execute()
    {
        // Ambil data other.
        $bulan = $this->bulan;
        $tahun = $this->tahun;
        $response = $this->getDataTindakan($bulan, $tahun);
        $tmpPegawai = [];
        foreach ($response as $key => $value) {

            $jabatanKode = ArrayHelper::getValue($value, 'jabatan_kode');
            $jabatanNama = ArrayHelper::getValue($value, 'jabatan_nama');
            $grading = ArrayHelper::getValue($value, 'grading');
            $posisi = ArrayHelper::getValue($value, 'posisi');
            $item = [
                'pegawai_kode' => ArrayHelper::getValue($value, 'pegawai_id'),
                'nama_pegawai' => ArrayHelper::getValue($value, 'nama_pegawai'),
                'jabatan_kode' => $jabatanKode != "" ? $jabatanKode : 'MS2',
                'jabatan_nama' => $jabatanNama != "" ? $jabatanNama : 'Ka KSM',
                'grading' => $grading != "" ? $grading : 16,  
                'posisi' => $posisi != "" ? $posisi : 2,
                'nik' =>  ArrayHelper::getValue($value, 'nik'),
                'detail_tindakan' => self::convertJson('detail_tindakan', $value['detail_tindakan'])
            ];

            $tmpPegawai[] = $item;
        }
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Melakukan proses pengambilan data..',
                'progress' => 60,
                'filename' => $this->unique_str
            ]),
        ]);

        // Ambil data absensi.
        $dataAbsensi = $this->getDataAbsensi();

        // Ambil data tindakan.
        $dataOthers = $this->getDataOthers();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Mengambil data berhasil dan memulai sinkronisasi..',
                'progress' => 70,
                'filename' => $this->unique_str
            ]),
        ]);

        foreach ($tmpPegawai as $key => $pegawaiValue) {
            if(isset($pegawaiValue['pegawai_kode'])) {

                // Proses memasukan data absensi kedalam array pegawaiValue
                foreach ($dataAbsensi as $keyAbsensi => $absensiValue) {
                    $pegawaiValue['detail_absen'] = self::convertJson('detail_absen', $absensiValue['detail_absen']);
                }
                
                foreach ($dataOthers as $key => $otherValue) {
        
                    // Proses memasukan data tindakan kedalam array pegawaiValue
                    $pegawaiValue['pegawai_kode'] = ArrayHelper::getValue($otherValue, 'pegawai_id');
                    $pegawaiValue['ronde_besar'] = ArrayHelper::getValue($otherValue,'ronde_besar');
                    $pegawaiValue['koord_lap_jaga_bangsal'] = ArrayHelper::getValue($otherValue,'koord_lap_jaga_bangsal');
                    $pegawaiValue['rapat_koord_pelayanan'] = ArrayHelper::getValue($otherValue,'rapat_koord_pelayanan');
                    $pegawaiValue['audit_medik'] = ArrayHelper::getValue($otherValue,'audit_medik');
                    $pegawaiValue['penelitian'] = ArrayHelper::getValue($otherValue,'penelitian');
                    $pegawaiValue['presentasi'] = ArrayHelper::getValue($otherValue,'presentasi');
                    $pegawaiValue['rapat_direksi'] = ArrayHelper::getValue($otherValue,'rapat_direksi');
                    $pegawaiValue["kwalitas"] = ArrayHelper::getValue($otherValue, 'kwalitas');
                    $pegawaiValue["kepatuhan"] = ArrayHelper::getValue($otherValue, 'kepatuhan');
                    $pegawaiValue["tidak_apel"] = ArrayHelper::getValue($otherValue, 'tidak_apel');
                    $pegawaiValue["created_date"] = date('Y-m-d');
                    $pegawaiValue["periode_bulan"] = $bulan;
                    $pegawaiValue["periode_tahun"] = $tahun;
                    $pegawaiValue["is_deleted"] = false;
                    $pegawaiValue["is_active"] = true;
                }
                
                if(isset($pegawaiValue['detail_tindakan']) && isset($pegawaiValue['detail_absen'])) {
                    $this->dataPegawai[] = $pegawaiValue;
                }
            }
        }
      
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Melakukan penyimpanan data..',
                'progress' => 90,
                'filename' => $this->unique_str
            ]),
        ]);

        if(! empty($bulan) && ! empty($tahun)) {
            self::deleteDataRemunerasi($bulan, $tahun);
        }

        if(! empty($this->dataPegawai)) {
            RemunPegawai::batchInsert($this->dataPegawai);
        }

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses berhasil',
                'progress' => 100,
                'filename' => $this->unique_str
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-Remunerasi-SyncData',
            'timestamp' => date('Y-m-d H:i:s'),
            'attributes' => $this->attributes,
            'response' => $this->dataPegawai
        ]);
    }

    /**
     * Function untuk mengambil data tindakan.
     */
    private function getDataTindakan($bulan, $tahun)
    {
        $options = [
            'month' => $bulan,
            'year' => $tahun
        ];

        $response = (new RemunerasiService)->getDataTindakan('/sirs/doctordo', $options);
        $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
        return $result;
    }

    /**
     * Function untuk mengambil data absensi
     */
    private function getDataAbsensi()
    {
        $response = (new RemunerasiService)->getDataAbsensi();
        $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
        return $result;
    }

    /**
     * Function untuk mengambil data others.
     */
    private function getDataOthers()
    {
        $response = (new RemunerasiService)->getDataOther();
        $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
        return $result;
    }

    private static function convertJson($key, $value = null)
    {   
        $result = json_encode([
            $key => $value
        ]);

        return $result;
    }

    /**
     * Function bundle ketika proses menghapus data remunerasi.
     */
    public static function deleteDataRemunerasi($bulan, $tahun)
    {
        $dataPeriode = self::getDataPerPeriode($bulan, $tahun);
        $tmpRemunPegawaiId = [];
        if(! empty($dataPeriode)) {
            foreach ($dataPeriode as $key => $value) {
                $tmpRemunPegawaiId[] = ArrayHelper::getValue($value, 'remunpegawai_id');
            }
        }

        if (! empty($tmpRemunPegawaiId)) {
            // Hapus data periode
            self::deleteDataPeriode($bulan, $tahun);
            
            // Hapus kalkukasi
            $tmpRemunPegawaiId = implode(",", $tmpRemunPegawaiId);
            self::deleteRemunKalkulasi($tmpRemunPegawaiId);
        }

        return $tmpRemunPegawaiId;
    }

    /**
     * Function ini digunakan untuk menghapus data periode yang sudah ada
     * dan juga untuk menghandle double data.
     */
    private static function deleteDataPeriode($bulan, $tahun)
    {
        $sql = "update remunpegawai_t set is_deleted = true, is_active = false where periode_bulan = '{$bulan}' and periode_tahun = '{$tahun}'";
        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Function untuk mengambil data per periode.
     */
    private function getDataPerPeriode($bulan, $tahun)
    {
        $sql = "select remunpegawai_id from remunpegawai_t where periode_bulan = '{$bulan}' and periode_tahun = '{$tahun}' and is_active = true and is_deleted = false";
        $getDataPeriode = Yii::$app->db->createCommand($sql)->queryAll();
        return $getDataPeriode;
    }

    /**
     * Function untuk update is_deleted dan is_active
     * @param remunpegawai_id @array
     */
    private static function deleteRemunKalkulasi($remunpegawai_id)
    {
        $sql = "update remunpegawaicalc_t set is_deleted = true, is_active = false where remunpegawai_id IN ($remunpegawai_id)";
        Yii::$app->db->createCommand($sql)->execute();
    }
}
