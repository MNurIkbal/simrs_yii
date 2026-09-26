<?php

namespace SirsCore\features;

use Yii;

use Doco\components\DocoConstants;

class FeaturePendaftaran
{
	/**
     * @param pendaftaran_id
     * @return string || false 
     * @desc Mengubah status bayar di pendaftaran menjadi tagihan
     */
	public static function updateTagihan($pendaftaran_id)
	{
		$statusbayar = DocoConstants::BELUM_LUNAS;
		$res = Yii::$app->db->createCommand("
            UPDATE pendaftaran_t SET status_bayar = {$statusbayar}
            WHERE pendaftaran_id = {$pendaftaran_id}
        ")->execute();
        return $res;
	}

    public static function getTagihan($pendaftaranId)
    {
        $checkTotal = Yii::$app->db->createCommand("
            SELECT detail.pendaftaran_id,SUM(total) as total FROM (
                SELECT pendaftaran_id,COUNT(*) AS total
                FROM tindakanpelayanan_t
                WHERE tindakansudahbayar_id IS NULL 
                AND is_deleted = false
                GROUP BY pendaftaran_id
                UNION ALL
                SELECT pendaftaran_id,COUNT(*) AS total
                FROM obatalkespasien_t
                WHERE obatsudahbayar_id IS NULL 
                AND is_deleted = false
                GROUP BY pendaftaran_id
             ) detail
            WHERE detail.pendaftaran_id = {$pendaftaranId}
            GROUP BY detail.pendaftaran_id
        ")->queryOne();

        return $checkTotal;
    }
}