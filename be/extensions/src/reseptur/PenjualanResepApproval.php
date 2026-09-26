<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\reseptur;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\Services\FarmasiService;
use GuzzleHttp\Exception\RequestException;

use app\modules\v1\models\ObatAlkesPasien;

class PenjualanResepApproval extends \Doco\processes\PenjualanResepProcess {
    protected $payloadStok;
    
    protected function getPayloadStok() {
        $this->payloadStok = ObatAlkesPasien::find()
                                ->select(['*', 'qty_konversi AS qty_satuanpakai'])
                                ->where(['penjualanresep_id' => $this->resep->penjualanresep_id])
                                ->asArray()->all();
    }

    protected function potongStok() {
        return (new FarmasiService)->potongStok([
            'detail_obat' => $this->payloadStok
        ]);
    }

    protected function processFlow() {
        $this->validasiPayload();

        $this->startDBTransaction();
        $this->insertPenjualanResep();
        $this->insertBilling();
        $this->commitDBTransaction();
        $this->getPayloadStok();
        $potongStok = $this->potongStok();
        if(isset($potongStok['meta']['code']) && $potongStok['meta']['code'] != 200 ) {
            return $potongStok;
        }
        $this->afterSave();

        return [
            'message' => 'Data Berhasil di simpan',
            'id' => DocoHelpers::encrypt($this->resep->penjualanresep_id),
            'nomor' => $this->noresep
        ];
    }
}