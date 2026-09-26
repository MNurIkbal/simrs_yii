<?php

use yii\db\Migration;

/**
 * Class m220729_075635_migrate_odoo_view_sale_order_cob
 */
class m220729_075635_migrate_odoo_view_sale_order_cob extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS sale_order_cob;
        ');

        $this->execute('
            CREATE VIEW "public"."sale_order_cob" AS  
            SELECT pembayaranpelayanan_r.id AS sync_id_api,
                pembayaranpelayanan_r.no_pembayaran AS cob_no,
                pembayaranpelayanan_r.deleted_date AS cancel_date,
                pembayaranpelayanan_r.tgl_pembayaran AS cob_date,
                pembayaranpelayanan_r.pendaftaran_id AS admission_id,
                int_billing_r.id AS billing_id,
                concat(\'PEN\', pembayaranpelayanan_r.penjamin_id) AS payer_id,
                6 AS sync_type,
                    CASE
                        WHEN pembayaranpelayanan_r.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_r.is_deleted = true THEN \'draft\'::text
                        WHEN pembayaranpelayanan_r.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_r.is_deleted = false THEN \'done\'::text
                        ELSE \'draft\'::text
                    END AS state,
                pembayaranpelayanan_r.total_subsidiasuransi AS payer_amount,
                pembayaranpelayanan_r.keterangan,
                pembayaranpelayanan_r.is_sending,
                pembayaranpelayanan_r.is_sent,
                    CASE
                        WHEN pembayaranpelayanan_r.is_sending = true AND pembayaranpelayanan_r.is_sent = false AND pembayaranpelayanan_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN pembayaranpelayanan_r.is_sending = true AND pembayaranpelayanan_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN pembayaranpelayanan_r.is_sending = false AND pembayaranpelayanan_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN pembayaranpelayanan_r.is_sending = true AND pembayaranpelayanan_r.is_sent = false AND pembayaranpelayanan_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
                        WHEN pembayaranpelayanan_r.is_sending = false AND pembayaranpelayanan_r.is_sent = false AND pembayaranpelayanan_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        ELSE NULL::text
                    END AS status_proses,
                int_billing_r.is_sent_billing
               FROM pembayaranpelayanan_r
                 JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.id,
                        a.pendaftaran_id
                       FROM pendaftaran_r a
                      WHERE a.is_sent = true AND a.keterangan::text = \'INSERT\'::text) pendaftaran_r ON pembayaranpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
                 LEFT JOIN ( SELECT a.id,
                        a.pembayaran_id,
                        a.is_sent AS is_sent_billing
                       FROM int_billing_r a) int_billing_r ON pembayaranpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_075635_migrate_odoo_view_sale_order_cob cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_075635_migrate_odoo_view_sale_order_cob cannot be reverted.\n";

        return false;
    }
    */
}
