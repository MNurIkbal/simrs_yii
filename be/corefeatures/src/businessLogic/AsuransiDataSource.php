<?php

namespace SirsCore\businessLogic;

/**
 * @author: [Maulana Muhammad Rizky]
 * A product of PT. Sirs
 * Powered by Sirs
 */

use Doco\components\DocoConstants;
use Doco\models\IntegrasiTindakanObatAsuransi;
use Doco\models\kasir\InfoTagihanPasien;
use Doco\models\kasir\InfoTagihanPasienAsuransiV;
use Doco\models\Reseptur;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Data Source Integrasi untuk asuransie.
 *
 * @author Maulana Muhammad Rizky
 */
class AsuransiDataSource
{
    protected $pendaftaranId;

    public function __construct($pendaftaranId)
    {
        $this->pendaftaranId = $pendaftaranId;
    }
    public function collectionItemRequest($prefixItem = null)
    {
        $pendaftaranId = $this->pendaftaranId;
        $mappingInstalasi = [
            DocoConstants::INST_ID_RJ => 'rawatjalan',
            DocoConstants::INST_ID_RD => 'rawatjalan',
            DocoConstants::INST_ID_RI => 'rawatinap',
            DocoConstants::INST_ID_LAB => 'laboratorium',
            DocoConstants::INST_ID_RAD => 'radiologi',
        ];

        $gabungBilling = $this->gabungBilling($pendaftaranId);
		$gabungBillingIds = isset($gabungBilling['pendaftaran_id']) ? $gabungBilling['pendaftaran_id'] : null;

        $condition = isset($gabungBillingIds) ? 'ref_pendaftaran_id' : 'pendaftaran_id';
        $tagihan = InfoTagihanPasienAsuransiV::find()->where([
            $condition => $pendaftaranId
        ])->asArray()->all();
        
        $penjualanResepId = [];
        $payloadRequestObat = [];
        $payloadItemRequest = [];
        if (! empty($tagihan)) {
            foreach ($tagihan as $value) {
                $instalasi = isset($mappingInstalasi[$value['instalasi_id']]) ? $mappingInstalasi[$value['instalasi_id']] : 'rawatjalan';
                $itemPrefix = $prefixItem !== null ? $prefixItem . '-' . $value['daftartindakan_kode'] : $value['daftartindakan_kode'];
                if ($value['is_obat']) {
                    $penjualanResepId[] = $value['penjualanresep_id'];
                    $payloadRequestObat[] = [
                        'pelayanan_id' => $value['pelayanan_id'], // Tidak dikirim ke asuransi
                        "tindakanobat_id" => $value['tindakan_obat_id'], // Tidak dikirim ke asuransi
                        "category"  =>  "obat",
                        "obatrutin" => "N",
                        "item_code"  => $itemPrefix,
                        "qty"  =>  $value['qty'],
                        "price" => $value['tarif_satuan'],
                        "jumlah_hari"  =>  1, // Default Hari.
                        "keterangan"  =>  $value['tindakan_obat_nama'],
                        "priority"  =>  "",
                        "type"  =>  $instalasi
                    ];
                } else {
                    /** Cek apakah tindakan obat memiliki cyto */
                    $priceSatuan = $value['is_cyto'] ? $value['tarif_satuan'] + $value['tarif_cyto'] + $value['tarifpenyulit_tindakan'] : $value['tarif_satuan'];

                    if (isset($payloadItemRequest[$value['tindakan_obat_id']])) {
                        $payloadItemRequest[$value['tindakan_obat_id']]['qty'] = $payloadItemRequest[$value['tindakan_obat_id']]['qty'] + $value['qty'];
                    } else {
                        $payloadItemRequest[$value['tindakan_obat_id']] = [
                            'pelayanan_id' => $value['pelayanan_id'], // Tidak dikirim ke asuransi
                            "tindakanobat_id" => $value['tindakan_obat_id'], // Tidak dikirim ke asuransi
                            "category" => "tindakan",
                            "item_code" => $itemPrefix,
                            "qty" => $value['qty'],
                            "price" => $priceSatuan,
                            "keterangan" => $value['tindakan_obat_nama'],
                            "priority" => "",
                            "type" => $instalasi
                        ];
                    }
                }
            }
        }

        // Cek Jumlah Hari dan Override Tanggal.
        if (! empty($penjualanResepId)) {
            $dataReseptur = Reseptur::find()
                ->select([
                    'reseptur_t.penjualanresep_id',
                    'reseptur_t.reseptur_id',
                    'resepturdetail_t.hari',
                    'resepturdetail_t.obatalkes_id'
                ])
                ->join('JOIN', 'resepturdetail_t', 'resepturdetail_t.reseptur_id = reseptur_t.reseptur_id')
                ->where(['reseptur_t.penjualanresep_id' => $penjualanResepId])
                ->asArray()
                ->all();


            if (! empty($dataReseptur)) {
                foreach ($payloadRequestObat as $key => $obat) {
                    foreach ($dataReseptur as $reseptur) {
                        if ($obat['tindakanobat_id'] == $reseptur['obatalkes_id']) {
                            $payloadRequestObat[$key]['jumlah_hari'] = isset($reseptur['hari']) ? $reseptur['hari'] : 1;
                        }
                    }
                }

            }
        }
        
        $payloadItemRequest = array_merge($payloadItemRequest, $payloadRequestObat);

        $logAsuransi = $this->collectionLogRequest();

        // Grouping array $logAsuransi berdasarkan type
        $logAsuransiGroup = [];
        foreach ($logAsuransi as $data) {
            $logAsuransiGroup[$data['type']][$data['pelayanan_id']] = [
                'type' => $data['type'],
                'item_code' => $data['item_code'],
                'qty' => $data['qty'],
                'subtotal' => (float) $data['subtotal'],
                'is_sending' => $data['is_sending']
            ];
        }
        
        foreach ($payloadItemRequest as $key => $requestData) {
            if ($requestData['category'] == 'tindakan' && isset($logAsuransiGroup['tindakan']) && in_array($requestData['pelayanan_id'], array_keys($logAsuransiGroup['tindakan']))) {
                $tindakanData = $logAsuransiGroup['tindakan'][$requestData['pelayanan_id']];
                if ($requestData['item_code'] == $tindakanData['item_code']) {
                    if ($requestData['qty'] == $tindakanData['qty'] && $requestData['price'] == $tindakanData['subtotal'] && $tindakanData['is_sending'] == true) {
                        if (isset($payloadItemRequest[$key])){
                            unset($payloadItemRequest[$key]);
                        }
                    }
                }

                unset($logAsuransiGroup['tindakan'][$requestData['pelayanan_id']]);
            }

            if ($requestData['category'] == 'obat' && isset($logAsuransiGroup['obat']) && in_array($requestData['pelayanan_id'], array_keys($logAsuransiGroup['obat']))) {
                $obatData = $logAsuransiGroup['obat'][$requestData['pelayanan_id']];
                if ($requestData['item_code'] == $obatData['item_code']) {
                    if ($requestData['qty'] == $obatData['qty'] && $requestData['price'] == $obatData['subtotal'] && $obatData['is_sending'] == true) {
                        if (isset($payloadItemRequest[$key])){
                            unset($payloadItemRequest[$key]);
                        }
                    }
                }

                unset($logAsuransiGroup['obat'][$requestData['pelayanan_id']]);
            }
        }

        $deleteListObat = isset($logAsuransiGroup['obat']) ? array_values($logAsuransiGroup['obat']) : [];
        $deleteListTindakan = isset($logAsuransiGroup['tindakan']) ? array_values($logAsuransiGroup['tindakan']) : [];
        
        return [
            'payloadItemRequest' => $payloadItemRequest,
            'deleteItemRequest' => array_merge($deleteListObat, $deleteListTindakan)
        ];
    }

    private function collectionLogRequest()
    {
        return IntegrasiTindakanObatAsuransi::find()->where([
            'pendaftaran_id' => $this->pendaftaranId
        ])->asArray()->all();
    }

    private function gabungBilling($pendaftaranId)
    {
        if(empty($pendaftaranId)) {
            return [];
        }

        return Yii::$app->db->createCommand("
            SELECT ref_pendaftaran_id, pendaftaran_id FROM gabungpelayanandetail_t WHERE pendaftaran_id = {$pendaftaranId} OR ref_pendaftaran_id = {$pendaftaranId} AND is_deleted = FALSE
        ")->queryOne();
    }
}
