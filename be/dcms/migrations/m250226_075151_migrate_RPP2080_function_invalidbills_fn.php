<?php

use yii\db\Migration;

/**
 * Class m250226_075151_migrate_RPP2080_function_invalidbills_fn
 */
class m250226_075151_migrate_RPP2080_function_invalidbills_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."invalidbills_fn"("var_stdate" date, "var_endate" date)
  RETURNS TABLE("pendaftaran_id" int4, "status_bayar" int4) AS $BODY$ 
BEGIN
    RETURN QUERY
    SELECT 
      pendaftaran_t.pendaftaran_id,
      pendaftaran_t.status_bayar
    FROM pendaftaran_t
    -- JOIN (
    --   SELECT
    --     pembayaran_t.pendaftaran_id
    --   FROM pembayaran_t 
    --   WHERE is_deleted IS FALSE
    -- ) pembayaran_t ON pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id
    WHERE pendaftaran_t.status_bayar = 349
      AND NOT EXISTS (
        SELECT DISTINCT
          x.pendaftaran_id
        FROM (
          SELECT DISTINCT
            tindakanpelayanan_t.pendaftaran_id,
            COUNT(1) AS ct
          FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted IS FALSE
            AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL 
            AND tindakanpelayanan_t.pendaftaran_id IS NOT NULL
          GROUP BY tindakanpelayanan_t.pendaftaran_id
          UNION
          SELECT DISTINCT
            obatalkespasien_t.pendaftaran_id,
            COUNT(1) AS ct
          FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted IS FALSE
            AND obatalkespasien_t.obatsudahbayar_id IS NULL
            AND obatalkespasien_t.pendaftaran_id IS NOT NULL
          GROUP BY obatalkespasien_t.pendaftaran_id
        ) AS x
        WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id
      )
      AND tgl_pendaftaran::DATE BETWEEN COALESCE(var_stdate, CURRENT_DATE - INTERVAL \'1 month\') AND COALESCE(var_endate, CURRENT_DATE - 1)
      AND status_periksa NOT IN (\'628\', \'402\')
    UNION 
    SELECT 
      pendaftaran_t.pendaftaran_id,
      pendaftaran_t.status_bayar
    FROM pendaftaran_t
    WHERE pendaftaran_t.status_bayar = 349
      AND NOT EXISTS (
        SELECT DISTINCT
          x.pendaftaran_id
        FROM (
          SELECT DISTINCT
            tindakanpelayanan_t.pendaftaran_id,
            COUNT(1) AS ct
          FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted IS FALSE
            AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL 
            AND tindakanpelayanan_t.pendaftaran_id IS NOT NULL
            AND tindakanpelayanan_t.tarif_tindakan > 0
          GROUP BY tindakanpelayanan_t.pendaftaran_id
          UNION
          SELECT DISTINCT
            obatalkespasien_t.pendaftaran_id,
            COUNT(1) AS ct
          FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted IS FALSE
            AND obatalkespasien_t.obatsudahbayar_id IS NULL
            AND obatalkespasien_t.pendaftaran_id IS NOT NULL
            AND obatalkespasien_t.hargajual_oa > 0
          GROUP BY obatalkespasien_t.pendaftaran_id
        ) AS x
        WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id
      )
      AND tgl_pendaftaran::DATE BETWEEN COALESCE(var_stdate, CURRENT_DATE - INTERVAL \'1 month\') AND COALESCE(var_endate, CURRENT_DATE - 1)
      AND status_periksa NOT IN (\'628\', \'402\');
END; 
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250226_075151_migrate_RPP2080_function_invalidbills_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250226_075151_migrate_RPP2080_function_invalidbills_fn cannot be reverted.\n";

        return false;
    }
    */
}
