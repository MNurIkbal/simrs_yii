<?php

use yii\db\Migration;

/**
 * Class m220318_073933_migrate_ODH396_view_invoicegabungdetail_v
 */
class m220318_073933_migrate_ODH396_view_invoicegabungdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."invoicegabungdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."invoicegabungdetail_v" AS  SELECT invoicegabungdetail_t.invoicegabung_id,
                invoicegabungdetail_t.invoicegabungdetail_id,
                pendaftaran.no_pendaftaran,
                invoicegabungdetail_t.no_pembayaran,
                pendaftaran.no_rekam_medik, 
                pendaftaran.nama_pasien,
                pendaftaran.penjamin_nama,
                invoicegabung_t.tgl_invoicegabung,
                invoicegabungdetail_t.total_invoice
               FROM ((invoicegabungdetail_t
                 JOIN invoicegabung_t ON ((invoicegabungdetail_t.invoicegabung_id = invoicegabung_t.invoicegabung_id)))
                 JOIN ( SELECT pendaftaran_t.pendaftaran_id,
                        penjamin_m.penjamin_nama,
                        pendaftaran_t.no_pendaftaran,
                        pasien.no_rekam_medik,
                        pasien.nama_pasien
                       FROM (((pendaftaran_t
                         LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                                pasienadmisi_t.penjamin_id
                               FROM pasienadmisi_t) pasienadmisi ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id)))
                         LEFT JOIN penjamin_m ON ((COALESCE(pasienadmisi.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id)))
                         JOIN ( SELECT pasien_m.pasien_id,
                                pasien_m.no_rekam_medik,
                                pasien_m.nama_pasien
                               FROM pasien_m) pasien ON ((pendaftaran_t.pasien_id = pasien.pasien_id)))) pendaftaran ON ((invoicegabungdetail_t.pendaftaran_id = pendaftaran.pendaftaran_id)));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220318_073933_migrate_ODH396_view_invoicegabungdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220318_073933_migrate_ODH396_view_invoicegabungdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
