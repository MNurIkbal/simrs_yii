<?php

use yii\db\Migration;

/**
 * Class m220318_073916_migrate_ODH396_view_invoicegabung_v
 */
class m220318_073916_migrate_ODH396_view_invoicegabung_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."invoicegabung_v";
        ');

        $this->execute('
            CREATE VIEW "public"."invoicegabung_v" AS  SELECT a.pendaftaran_id, 
                a.pembayaran_id,
                concat(COALESCE(a.no_pembayaran, pembayaranpelayanan_t.no_pembayaran), \' - \', ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan)) AS total_tagihan,
                a.created_date AS tgl_pembayaran,
                COALESCE(a.no_pembayaran, pembayaranpelayanan_t.no_pembayaran) AS no_pembayaran,
                (((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) - a.total_discountpembayaran) AS tagihan,
                pendaftaran_t.no_pendaftaran,
                invoicegabung_t.tgl_invoicegabung_cetak,
                invoicegabung_t.penjamin_id_cetak,
                invoicegabung_t.pendaftaran_id_cetak,
                COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) AS penjamin_id,
                penjamin_m.penjamin_nama,
                pendaftaran_t.pasien_id,
                pendaftaran_t.tgl_pendaftaran,
                pasien_m.nama_pasien,
                pasien_m.no_rekam_medik
               FROM (((((((pembayaran_t a
                 JOIN pendaftaran_t ON ((a.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 LEFT JOIN penjamin_m ON ((COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id)))
                 JOIN ( SELECT DISTINCT ON (a1.pembayaranpelayanan_id) a1.pembayaranpelayanan_id,
                        a1.pembayaran_id,
                        a1.no_pembayaran
                       FROM pembayaranpelayanan_t a1
                      WHERE (a1.is_deleted = false)
                      ORDER BY a1.pembayaranpelayanan_id DESC) pembayaranpelayanan_t ON ((a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
                 LEFT JOIN invoicegabungdetail_t ON (((a.pembayaran_id = invoicegabungdetail_t.pembayaran_id) AND (invoicegabungdetail_t.is_deleted = false))))
                 LEFT JOIN invoicegabung_t ON ((invoicegabungdetail_t.invoicegabung_id = invoicegabung_t.invoicegabung_id)))
                 JOIN ( SELECT pas.pasien_id,
                        pas.nama_pasien,
                        pas.no_rekam_medik
                       FROM pasien_m pas) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
              WHERE ((a.is_deleted = false) AND (invoicegabungdetail_t.invoicegabungdetail_id IS NULL));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220318_073916_migrate_ODH396_view_invoicegabung_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220318_073916_migrate_ODH396_view_invoicegabung_v cannot be reverted.\n";

        return false;
    }
    */
}
