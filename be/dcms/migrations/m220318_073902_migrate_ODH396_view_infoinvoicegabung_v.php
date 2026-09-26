<?php

use yii\db\Migration;

/**
 * Class m220318_073902_migrate_ODH396_view_infoinvoicegabung_v
 */
class m220318_073902_migrate_ODH396_view_infoinvoicegabung_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infoinvoicegabung_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoinvoicegabung_v" AS  SELECT invoicegabung_t.invoicegabung_id,
                invoicegabung_t.no_invoicegabung,
                invoicegabung_t.pendaftaran_id_cetak,
                pendaftaran_t.no_pendaftaran,
                invoicegabungdetail_t.no_pendaftaran_ref, 
                invoicegabungdetail_t.nama_pasien,
                concat(invoicegabungdetail_t.nama_pasien, \' (\', invoicegabungdetail_t.jenis_kelamin, \') \', invoicegabungdetail_t.no_rekam_medik) AS pasien,
                (invoicegabung_t.tgl_invoicegabung)::date AS tgl_invoicegabung,
                invoicegabung_t.total_invoicegabung,
                    CASE
                        WHEN (invoicegabung_t.is_deleted = false) THEN \'INVOICE\'::text
                        ELSE \'BATAL\'::text
                    END AS status,
                    CASE
                        WHEN (invoicegabung_t.is_deleted = false) THEN false
                        ELSE true
                    END AS is_batal,
                invoicegabungdetail_t.ref_invoice,
                invoicegabung_t.is_deleted,
                invoicegabung_t.penjamin_id_cetak,
                penjamin_m.penjamin_nama AS penjamin,
                invoicegabung_t.created_date AS tgl_invoicegabung_ref,
                invoicegabung_t.tgl_invoicegabung_cetak,
                nama_login.nama_pegawai AS kasir,
                invoicegabungdetail_t.penjamin_id_ref,
                invoicegabungdetail_t.penjamin_nama_ref
               FROM (((((invoicegabung_t
                 LEFT JOIN ( SELECT a.invoicegabung_id,
                        pasien.nama_pasien,
                        lookup_m.lookup_name AS jenis_kelamin,
                        pasien.no_rekam_medik,
                        string_agg((pendaftaran_t_1.no_pendaftaran)::text, \',\'::text) AS no_pendaftaran_ref,
                        string_agg((a.no_pembayaran)::text, \',\'::text) AS ref_invoice,
                        string_agg(((COALESCE(pasienadmisi.penjamin_id, pendaftaran_t_1.penjamin_id))::character varying)::text, \',\'::text) AS penjamin_id_ref,
                        string_agg((penjamin_m_1.penjamin_nama)::text, \',\'::text) AS penjamin_nama_ref
                       FROM (((((invoicegabungdetail_t a
                         JOIN pendaftaran_t pendaftaran_t_1 ON ((a.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
                         LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                                pasienadmisi_t.penjamin_id
                               FROM pasienadmisi_t) pasienadmisi ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi.pasienadmisi_id)))
                         JOIN ( SELECT pasien_m.pasien_id,
                                pasien_m.no_rekam_medik,
                                pasien_m.nama_pasien,
                                pasien_m.jeniskelamin
                               FROM pasien_m) pasien ON ((pendaftaran_t_1.pasien_id = pasien.pasien_id)))
                         LEFT JOIN penjamin_m penjamin_m_1 ON ((COALESCE(pasienadmisi.penjamin_id, pendaftaran_t_1.penjamin_id) = penjamin_m_1.penjamin_id)))
                         LEFT JOIN lookup_m ON (((pasien.jeniskelamin)::integer = lookup_m.lookup_id)))
                      GROUP BY a.invoicegabung_id, pasien.nama_pasien, lookup_m.lookup_name, pasien.no_rekam_medik) invoicegabungdetail_t ON ((invoicegabung_t.invoicegabung_id = invoicegabungdetail_t.invoicegabung_id)))
                 LEFT JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran
                       FROM pendaftaran_t a) pendaftaran_t ON ((invoicegabung_t.pendaftaran_id_cetak = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                       FROM penjamin_m a) penjamin_m ON ((invoicegabung_t.penjamin_id_cetak = penjamin_m.penjamin_id)))
                 LEFT JOIN loginpemakai_k ON ((invoicegabung_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m nama_login ON ((loginpemakai_k.pegawai_id = nama_login.pegawai_id)));

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220318_073902_migrate_ODH396_view_infoinvoicegabung_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220318_073902_migrate_ODH396_view_infoinvoicegabung_v cannot be reverted.\n";

        return false;
    }
    */
}
