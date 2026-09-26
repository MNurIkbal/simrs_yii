<?php 

namespace Integrasi\Service\Sirs\SinkronDataBpjs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\Services\AsuransiPenjaminService;
use Integrasi\Service\Sirs\Models\SyKunjunganPasien;
use Integrasi\Service\Sirs\Models\Cron;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\SyInfoKlaimInacbg;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\SyKlaimInacbg;
use Integrasi\Service\Sirs\Models\SyKunjunganTagihan;
use Mpdf\Tag\P;

class TriggerSinkron extends \Integrasi\Contracts\DocoImplement 
{
    protected $pendaftaran;
    
    public function execute()
    {
        date_default_timezone_set('Asia/Jakarta');
        $type = $this->type_sinkron;
        
        if(strtolower($type) == "sinkron") {
            $result = self::triggerSinkronisasi();
        }
        if(strtolower($type) == 'hapus'){
            $result = self::triggerHapusSinkronisasi(); 
        }

        return json_encode([
            'service' => 'Sirs-TriggerSinkronisasi',
            'payload' => $this->attributes,
            'response' => $result,
            'timestamp' => date('Y-m-d H:i:s'),
         ]);
    }


    /**
     * Function untuk melakukan sinkronisasi ketika pasien melakukan pembayaran 
     * Function ini tidak menggunakan pendaftaran_id ataupun no_pendaftaran
     * @return Json
     */
    protected function triggerSinkronisasi()
    {
        $result = null;
        $resultValue = self::searchPendafataran($this->pendaftaran_id); 
        $no_pendaftaran = ArrayHelper::getValue($resultValue, 'no_pendaftaran');
        if(! empty($resultValue)) {
           $response = (new AsuransiPenjaminService)->triggerSinkronisasi($this->instalasi, $no_pendaftaran);
           $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
        }
        return $result;
    }

    /**
     * Function untuk menghapus ketika kondisi batal bayar apabila sudah terintegrasi
     * @property pendaftaran_id string
     * @property no_pendaftaran string
     * @return Json
     */
    protected function triggerHapusSinkronisasi()
    {
        $resultValue = self::searchPendafataran($this->pendaftaran_id); 
        $no_pendaftaran = ArrayHelper::getValue($resultValue, 'no_pendaftaran');
        $resultData = null;

        if(! empty($resultValue)) {
            $prevData = SyKunjunganPasien::find()
            ->where(
                [
                    'no_pendaftaran' => $no_pendaftaran,
                    'status_kunjungan' => [549, 550]
                ]
            )->one();
            
            try {
                // Kondisi apabila data tidak kosong
                if(! empty($prevData)) {
                    $kunjungan_id = $prevData->kunjungan_id;
                    $jumlahPembayaran = self::countJumlahPembayaran($kunjungan_id);
                    if($jumlahPembayaran > 1) {
                        $resultData = self::deleteTransactionPembayaran($kunjungan_id);
                    }else{
                        // Kondisi ketika jumlah pembayaran hanya satu kali
                        $resultData = self::deleteTransactionPembayaran($kunjungan_id);
                        $prevData->is_deleted = true;
                        $prevData->save();   
                    }
                }else {
                    $resultData = "DATA TIDAK DITEMUKAN!";

                }

                return [
                    'status' => 200,
                    'message' => "Data berhasil dihapus!",
                    "processingData" => $resultData,
                    "prevData" => $prevData,
                    "jumlahPembayaran" => $jumlahPembayaran
                ];
            } catch (\Throwable $th) {
                return [
                    'status' => 500,
                    'message' => $th->getMessage()
                ];
    
            }
        }
        return $this->pendaftaran_id;
    }

    /**
     * Function untuk mendapatkan nomer pendaftaran.
     */
    protected function searchPendafataran($pendaftaran_id)
    {
        $pendaftaran_id = intval($pendaftaran_id);

        return Yii::$app->db->createCommand("
            SELECT no_pendaftaran
                FROM pendaftaran_t
            WHERE pendaftaran_id = {$pendaftaran_id}
        ")->queryOne();
    }

    /**
     * Function untuk mencari nomer pembayaran
     * 
     * @param $pembayaran_id
     * @return array
     */
    protected function searchPembayaran($pembayaran_id)
    {
        $pembayaran_id = intval($pembayaran_id);

        return Yii::$app->db->createCommand("
            SELECT no_pembayaran
                FROM pembayaran_t
            WHERE pembayaran_id = {$pembayaran_id}
        ")->queryOne();
    }

    /**
     * Function untuk menentukan jumlah pembayaran di sy_kunjungantagihan
     * @param $kunjungan_id string
     * @return $jumlahNoPembayaran int
     */
    protected function countJumlahPembayaran($kunjungan_id)
    {
        $jumlahNoPembayaran = SyKunjunganTagihan::find()
        ->select(['no_buktitrans'])
        ->where(['kunjungan_id' => $kunjungan_id, 'is_deleted' => false])
        ->groupBy(['no_buktitrans'])
        ->count();

        return $jumlahNoPembayaran;
    }

    /** 
     * Function untuk mengehapus data transaksi sy_kunjungantagihan
     */
    protected function deleteTransactionPembayaran($kunjungan_id)
    {
        $noPembayaran = self::searchPembayaran($this->pembayaran_id);
        if(isset($noPembayaran)) {
            $noPembayaran = ArrayHelper::getValue($noPembayaran, 'no_pembayaran');
            return SyKunjunganTagihan::updateAll([
                'is_deleted' => true
            ], ['no_buktitrans' => $noPembayaran , 'kunjungan_id' => $kunjungan_id]);
        }

        return "DATA PEMBAYARAN TIDAK DITEMUKAN!";
    }
}