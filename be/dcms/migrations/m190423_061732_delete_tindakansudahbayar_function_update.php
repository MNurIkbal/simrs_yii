<?php

use yii\db\Migration;

/**
 * Class m190423_061732_delete_tindakansudahbayar_function_update
 */
class m190423_061732_delete_tindakansudahbayar_function_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION public.delete_tindakansudahbayar()
              RETURNS pg_catalog.trigger AS $BODY$-- author: Rizqi Febian

            DECLARE

            varPembayaranPelayananId INT;
            varPendaftaranId INT;
            isDeleted BOOLEAN;
            dateDeleted DATE;
            deletedBy INT;

            BEGIN
            varPembayaranPelayananId := NEW.pembayaranpelayanan_id;
            varPendaftaranId := NEW.pendaftaran_id;
            isDeleted := NEW.is_deleted;
            dateDeleted := NEW.deleted_date;
            deletedBy := NEW.deleted_by;

            IF isDeleted = TRUE THEN
                        UPDATE tandabuktibayar_t set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                        UPDATE piutangasuransi_t set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                        UPDATE tindakanpelayanan_t SET tindakansudahbayar_id  = NULL FROM (SELECT tindakanpelayanan_id, tindakansudahbayar_id FROM tindakansudahbayar_t where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = FALSE) as subquery WHERE tindakanpelayanan_t.tindakanpelayanan_id = subquery.tindakanpelayanan_id;
                        UPDATE obatalkespasien_t SET obatsudahbayar_id  = NULL FROM (SELECT obatalkespasien_id, obatsudahbayar_id FROM obatsudahbayar_t where pembayaranpelayanan_id = varPembayaranPelayananId) as subquery WHERE obatalkespasien_t.obatalkespasien_id = subquery.obatalkespasien_id;
                        UPDATE obatsudahbayar_t  set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                        UPDATE tindakansudahbayar_t  set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                        UPDATE bayaruangmuka_t set pemakaianuangmuka_id = NULL, jumlah_uangmuka = subquery.jumlahuangmuka FROM (SELECT pemakaianuangmuka_id,SUM(pemakaian_uangmuka) as jumlahuangmuka FROM pemakaianuangmuka_t WHERE pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = FALSE GROUP BY pemakaianuangmuka_id, pemakaian_uangmuka) as subquery WHERE bayaruangmuka_t.pemakaianuangmuka_id = subquery.pemakaianuangmuka_id;
                        UPDATE pemakaianuangmuka_t set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                        UPDATE pendaftaran_t set status_bayar = 349  WHERE pendaftaran_id = varPendaftaranId;
                        -- Add Ikbal 2019-03-15 update status bayar table penjualanresep_t
                        UPDATE penjualanresep_t x
                        SET status_bayar = 349
                        FROM obatalkespasien_t AS y,
                        obatsudahbayar_t z
                        WHERE x.penjualanresep_id = y.penjualanresep_id
                        AND y.obatalkespasien_id = z.obatalkespasien_id
                        AND z.pembayaranpelayanan_id = varPembayaranPelayananId;
            END IF;

            RETURN NEW;

            END$BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190423_061732_delete_tindakansudahbayar_function_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190423_061732_delete_tindakansudahbayar_function_update cannot be reverted.\n";

        return false;
    }
    */
}
