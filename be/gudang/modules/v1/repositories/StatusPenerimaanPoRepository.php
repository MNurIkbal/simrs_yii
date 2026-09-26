<?php

/**
 * @author : M. Rivaldi Irawan
 * Repositories untuk mendapatakan nilai status penerimaan
 * berdasarkan selisih yang didapat dari table detail transaksi
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\repositories;

use Yii;
use Doco\components\DocoConstants;

class StatusPenerimaanPoRepository
{
    /** 
     * itemId : nama kolom id barang_id atau obatalkes_id
     * detail : table detail transaksi (penerimaanobatdetail_t atau penerimaanbarangdetail_t)
     * detailId : nama kolom validasi po detail id (validasipoobatdetail_id atau validasipobarangdetail_id)
     * idObatAlkes : list obat alkes id
     * idDetail : list validasi po detail id (obat atau barang)
    */
    public static function getStatusPenerimaan($penerimaanDetail, $detailId, $idDetail, $validasiDetail, $attrValid, $id, $penerimaanDetailId, $tableReturDetail)
    {
        $listDetailId = "(" . implode(",", array_unique($idDetail)) . ")";

        $querySumPO = Yii::$app->db->createCommand("
            SELECT
                SUM(a.qty_input)
            FROM ( select {$detailId}, {$attrValid}, qty_input FROM {$validasiDetail} WHERE {$attrValid} = {$id}) as a
            WHERE {$detailId} IN {$listDetailId};
        ")->queryOne();

        $queryQtyTerima = Yii::$app->db->createCommand("
            SELECT
                SUM({$penerimaanDetail}.qty_diterima) - COALESCE(SUM({$tableReturDetail}.qty_retur), 0) as SUM
            FROM {$penerimaanDetail}
            LEFT JOIN {$tableReturDetail} ON {$penerimaanDetail}.{$penerimaanDetailId} = {$tableReturDetail}.{$penerimaanDetailId}
            WHERE {$penerimaanDetail}.{$detailId} IN {$listDetailId}
            AND {$penerimaanDetail}.is_batal is NULL;
        ")->queryOne();

        $totalSelisih = $querySumPO['sum'] - $queryQtyTerima['sum'];

        return $totalSelisih > 0 ? DocoConstants::BELUM_SELESAI_PO : DocoConstants::SUDAH_DITERIMA_PO;
    }
}
