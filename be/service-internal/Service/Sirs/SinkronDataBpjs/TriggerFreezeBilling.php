<?php 

namespace Integrasi\Service\Sirs\SinkronDataBpjs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\Services\AsuransiPenjaminSerconService;
use Integrasi\Components\Services\AsuransiPenjaminService;
use Integrasi\Service\Sirs\Models\SyKunjunganPasien;
use Integrasi\Service\Sirs\Models\Cron;
use Integrasi\Service\Sirs\Models\IntegrasiAsuransi;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\SyInfoKlaimInacbg;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\SyKlaimInacbg;
use Integrasi\Service\Sirs\Models\SyKunjunganTagihan;
use Mpdf\Tag\P;

class TriggerFreezeBilling extends \Integrasi\Contracts\DocoImplement 
{
    /**
     * Penggunaan dua paramater data 
     * 1. Dari Sy Kunjungantagihan
     * 2. Dari Pembayaran_t
     */
    public function execute()
    {
        $kunjunganId = $this->kunjungan_id;

        $tagihanData = SyKunjunganTagihan::find()
        ->select([
            'sy_kunjungan.kunjungan_id',
            'sy_kunjungan.no_pendaftaran',
            'sy_kunjungan.tgl_pendaftaran',
            'sy_kunjungan.penjamin_nama',
            'sy_kunjungan.penjamin_kode',
            'sy_kunjungan.instalasi_kode',
            'sy_kunjungan.nama_pasien',
            'sy_kunjungan.no_rekammedik',
            'sy_kunjungantagihan.no_buktitrans',
        ])
        ->where(['sy_kunjungantagihan.kunjungan_id' => $kunjunganId])
        ->leftJoin('sy_kunjungan', 'sy_kunjungan.kunjungan_id = sy_kunjungantagihan.kunjungan_id')
        ->asArray()
        ->one();

        if(! empty($tagihanData))
        {
            $noPembayaran = ArrayHelper::getValue($tagihanData, 'no_buktitrans');
            $dataPembayaran = Yii::$app->db->createCommand("SELECT 
                pembayaran_id, 
                pendaftaran_id,
                total_tagihan,
                total_dibayar,
                total_dijamin,
                no_invoicepasien,
                created_date,
                created_by
            FROM pembayaran_t WHERE no_pembayaran = '{$noPembayaran}' AND is_deleted = false")
            ->queryOne();            

            $detailData = [
                [
                    "order_id" => ArrayHelper::getValue($dataPembayaran, 'pendaftaran_id'), 
                    "order_sync_id_api" => ArrayHelper::getValue($dataPembayaran, 'pendaftaran_id'), 
                    "order_date" => ArrayHelper::getValue($tagihanData, 'tgl_pendaftaran'),
                    "order_no" => ArrayHelper::getValue($tagihanData, 'no_pendaftaran'), 
                    "cob_id" => ArrayHelper::getValue($dataPembayaran, 'pembayaran_id'),
                    "cob_sync_id_api" => ArrayHelper::getValue($dataPembayaran, 'pembayaran_id'),
                    "cob_date" => ArrayHelper::getValue($dataPembayaran, 'created_date'),
                    "cob_no" => ArrayHelper::getValue($tagihanData, 'no_buktitrans'),
                    "bill_id" => ArrayHelper::getValue($dataPembayaran, 'pembayaran_id'),
                    "bill_sync_id_api" => ArrayHelper::getValue($dataPembayaran, 'pembayaran_id'),
                    "bill_date" => ArrayHelper::getValue($dataPembayaran, 'created_date'), 
                    "bill_no" => ArrayHelper::getValue($tagihanData, 'no_buktitrans'), 
                    "payer_id" => ArrayHelper::getValue($tagihanData, 'penjamin_kode'), 
                    "payer_code" => ArrayHelper::getValue($tagihanData, 'penjamin_kode'), 
                    "payer_name" => ArrayHelper::getValue($tagihanData, 'penjamin_nama'), 
                    "payer_sync_id_api" => ArrayHelper::getValue($tagihanData, 'penjamin_kode'),
                    "patient_type" => ArrayHelper::getValue($tagihanData, 'instalasi_kode'), 
                    "patient_regcode" => ArrayHelper::getValue($tagihanData, 'no_rekammedik'), 
                    "patient_name" => ArrayHelper::getValue($tagihanData, 'nama_pasien'), 
                    "total_amount" => ArrayHelper::getValue($dataPembayaran, 'total_tagihan'), 
                    "payer_amount" => ArrayHelper::getValue($dataPembayaran, 'total_dijamin'), 
                    "personal_amount" => ArrayHelper::getValue($dataPembayaran, 'total_dibayar') 
                ]
            ];

            $payloadData = [
                "invoice_id" => ArrayHelper::getValue($dataPembayaran, 'pendaftaran_id'),
                "invoice_date" => date("Y-m-d"), 
                "invoie_due_date" => date("Y-m-d"), 
                "invoice_sent_date" => date("Y-m-d"), 
                "invoice_number" => ArrayHelper::getValue($tagihanData, 'no_buktitrans'), 
                "invoice_total" =>  ArrayHelper::getValue($dataPembayaran, 'total_tagihan'), 
                "invoice_status" =>  $this->type_sinkron, 
                "payer_id" =>  ArrayHelper::getValue($tagihanData, 'penjamin_kode'), 
                "payer_code" =>  ArrayHelper::getValue($tagihanData, 'penjamin_kode'), 
                "payer_name" =>  ArrayHelper::getValue($tagihanData, 'penjamin_nama'), 
                "payer_sync_id_api" => "INH01", 
                "create_uid" => ArrayHelper::getValue($dataPembayaran, 'created_by'), 
                "create_by" => ArrayHelper::getValue($dataPembayaran, 'created_by'),
                "create_date" => date("Y-m-d"), 
                "write_uid" => ArrayHelper::getValue($dataPembayaran, 'created_by'),
                "write_by" => ArrayHelper::getValue($dataPembayaran, 'created_by'),
                "write_date" => date("Y-m-d"), 
                "details" => $detailData 
            ];

            $response = (new AsuransiPenjaminSerconService)->sendPenjamin("/freezebilling/create", $payloadData, function($data){
                return $data;
            });

            $insertLog[] = [
                'pendaftaran_id' => ArrayHelper::getValue($dataPembayaran, 'pendaftaran_id'),
                'kunjungan_id' => $kunjunganId,
                'payload' => json_encode($payloadData),
                'sync_respon' => json_encode($response),
            ];

            
            if (!empty($insertLog)) {
                IntegrasiAsuransi::batchInsert($insertLog);
            }

            return json_encode([
                "service" => "Sirs-PenjaminFreezebilling",
                "attributes" => $this->attributes,
                "response" => $response,
                "payload" => $payloadData,
                "timestamp" => date('Y-m-d H:i:s'), 
            ]);
        }
        
        return json_encode([
            "service" => "Sirs-PenjaminFreezebilling",
            "attributes" => $this->attributes,
            "response" => [],
            "timestamp" => date('Y-m-d H:i:s'), 
        ]);
    }
}