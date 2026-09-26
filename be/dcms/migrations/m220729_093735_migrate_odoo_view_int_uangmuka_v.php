<?php

use yii\db\Migration;

/**
 * Class m220729_093735_migrate_odoo_view_int_uangmuka_v
 */
class m220729_093735_migrate_odoo_view_int_uangmuka_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_uangmuka_v;
        ');

        $this->execute('
            CREATE VIEW "public"."int_uangmuka_v" AS  SELECT concat(\'UM\', bayaruangmuka_r.id) AS sync_id_api,
                pegawai_m.nama_pegawai AS user_name,
                bayaruangmuka_r.pendaftaran_id AS admission_id,
                bayaruangmuka_r.no_uangmuka AS trans_no,
                bayaruangmuka_r.tgl_uangmuka AS trans_date,
                \'Deposit Collect\'::text AS trans_type,
                bayaruangmuka_r.no_uangmuka AS reference_no,
                pendaftaran_t.no_pendaftaran AS admission_no,
                pasien_m.nama_pasien AS patient_name,
                    CASE
                        WHEN bayaruangmuka_r.metode_pembayaran = 27 THEN \'Cash\'::text
                        WHEN bayaruangmuka_r.metode_pembayaran = 28 THEN \'DebitCard\'::text
                        ELSE \'-\'::text
                    END AS payment_name,
                bayaruangmuka_r.tgl_uangmuka AS tglproses,
                concat(jenisnontunai_m.nama, \' - \', tandabuktibayar_t.no_rek) AS edc_machine,
                bayaruangmuka_r.jumlah_uangmuka AS amount,
                bayaruangmuka_r.keterangan_uangmuka AS note,
                \'draft\'::text AS state,
                6 AS sync_type,
                bayaruangmuka_r.id,
                bayaruangmuka_r.is_sent,
                bayaruangmuka_r.is_sending,
                \'UANG_MUKA\'::text AS tipe_rekap,
                    CASE
                        WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN bayaruangmuka_r.is_sending = false AND bayaruangmuka_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                NULL::text AS billing_id,
                NULL::boolean AS is_sent_billing,
                pendaftaran_t.pasien_id AS partner_id,
                    CASE
                        WHEN pendaftaran_t.instalasi_id = 1 THEN \'1\'::text
                        WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NOT NULL THEN \'2\'::text
                        WHEN pendaftaran_t.instalasi_id = 3 THEN \'2\'::text
                        WHEN pendaftaran_t.instalasi_id = 2 THEN \'3\'::text
                        ELSE \'1\'::text
                    END AS patient_type
               FROM bayaruangmuka_r
                 JOIN ( SELECT a.loginpemakai_id,
                        a.pegawai_id
                       FROM loginpemakai_k a) loginpemakai_k ON bayaruangmuka_r.created_by = loginpemakai_k.loginpemakai_id
                 JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
                 LEFT JOIN ( SELECT a.tandabuktibayar_id,
                        a.bayaruangmuka_id,
                        a.no_rek
                       FROM tandabuktibayar_t a) tandabuktibayar_t ON bayaruangmuka_r.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
                 JOIN ( SELECT a.pendaftaran_id,
                        a.pasien_id,
                        a.no_pendaftaran,
                        a.instalasi_id,
                        a.pasienadmisi_id
                       FROM pendaftaran_t a) pendaftaran_t ON bayaruangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.pasien_id,
                        a.nama_pasien
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN ( SELECT a.jenisnontunai_id,
                        a.nama
                       FROM jenisnontunai_m a) jenisnontunai_m ON bayaruangmuka_r.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
              WHERE bayaruangmuka_r.is_deleted = false
            UNION ALL
             SELECT concat(\'UM\', bayaruangmuka_r.id) AS sync_id_api,
                pegawai_m.nama_pegawai AS user_name,
                bayaruangmuka_r.pendaftaran_id AS admission_id,
                bayaruangmuka_r.no_uangmuka AS trans_no,
                bayaruangmuka_r.tgl_uangmuka AS trans_date,
                \'Deposit Refund\'::text AS trans_type,
                bayaruangmuka_r.no_uangmuka AS reference_no,
                pendaftaran_t.no_pendaftaran AS admission_no,
                pasien_m.nama_pasien AS patient_name,
                    CASE
                        WHEN bayaruangmuka_r.metode_pembayaran = 27 THEN \'Cash\'::text
                        WHEN bayaruangmuka_r.metode_pembayaran = 28 THEN \'DebitCard\'::text
                        ELSE \'-\'::text
                    END AS payment_name,
                bayaruangmuka_r.tgl_uangmuka AS tglproses,
                concat(jenisnontunai_m.nama, \' - \', tandabuktibayar_t.no_rek) AS edc_machine,
                bayaruangmuka_r.jumlah_uangmuka AS amount,
                bayaruangmuka_r.keterangan_uangmuka AS note,
                \'draft\'::text AS state,
                6 AS sync_type,
                bayaruangmuka_r.id,
                bayaruangmuka_r.is_sent,
                bayaruangmuka_r.is_sending,
                \'BATAL_UANG_MUKA\'::text AS tipe_rekap,
                    CASE
                        WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN bayaruangmuka_r.is_sending = false AND bayaruangmuka_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                NULL::text AS billing_id,
                NULL::boolean AS is_sent_billing,
                pendaftaran_t.pasien_id AS partner_id,
                    CASE
                        WHEN pendaftaran_t.instalasi_id = 1 THEN \'1\'::text
                        WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NOT NULL THEN \'2\'::text
                        WHEN pendaftaran_t.instalasi_id = 3 THEN \'2\'::text
                        WHEN pendaftaran_t.instalasi_id = 2 THEN \'3\'::text
                        ELSE \'1\'::text
                    END AS patient_type
               FROM bayaruangmuka_r
                 JOIN ( SELECT a.loginpemakai_id,
                        a.pegawai_id
                       FROM loginpemakai_k a) loginpemakai_k ON bayaruangmuka_r.last_modified_by = loginpemakai_k.loginpemakai_id
                 JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
                 LEFT JOIN ( SELECT a.tandabuktibayar_id,
                        a.bayaruangmuka_id,
                        a.no_rek
                       FROM tandabuktibayar_t a) tandabuktibayar_t ON bayaruangmuka_r.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
                 JOIN ( SELECT a.pendaftaran_id,
                        a.pasien_id,
                        a.no_pendaftaran,
                        a.instalasi_id,
                        a.pasienadmisi_id
                       FROM pendaftaran_t a) pendaftaran_t ON bayaruangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.pasien_id,
                        a.nama_pasien
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN ( SELECT a.jenisnontunai_id,
                        a.nama
                       FROM jenisnontunai_m a) jenisnontunai_m ON bayaruangmuka_r.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
              WHERE bayaruangmuka_r.is_deleted = true
            UNION ALL
             SELECT concat(\'PUM\', pengembalianuangmuka_r.id) AS sync_id_api,
                pegawai_m.nama_pegawai AS user_name,
                pengembalianuangmuka_r.pendaftaran_id AS admission_id,
                tandabuktikeluar_t.no_buktikeluar AS trans_no,
                pengembalianuangmuka_r.tgl_pengembalian AS trans_date,
                pengembalianuangmuka_r.keterangan AS trans_type,
                tandabuktikeluar_t.no_buktikeluar AS reference_no,
                pendaftaran_t.no_pendaftaran AS admission_no,
                pasien_m.nama_pasien AS patient_name,
                    CASE
                        WHEN tandabuktikeluar_t.is_tunai IS TRUE THEN \'Cash\'::text
                        WHEN tandabuktikeluar_t.is_tunai IS FALSE THEN \'DebitCard\'::text
                        ELSE \'-\'::text
                    END AS payment_name,
                pengembalianuangmuka_r.tgl_pengembalian AS tglproses,
                tandabuktikeluar_t.no_rek AS edc_machine,
                pengembalianuangmuka_r.total_pengembalian AS amount,
                \'-\'::text AS note,
                \'draft\'::text AS state,
                6 AS sync_type,
                pengembalianuangmuka_r.id,
                pengembalianuangmuka_r.is_sent,
                pengembalianuangmuka_r.is_sending,
                \'PENGEMBALIAN_UANGMUKA\'::text AS tipe_rekap,
                    CASE
                        WHEN pengembalianuangmuka_r.is_sending = true AND pengembalianuangmuka_r.is_sent = false AND pengembalianuangmuka_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN pengembalianuangmuka_r.is_sending = true AND pengembalianuangmuka_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN pengembalianuangmuka_r.is_sending = false AND pengembalianuangmuka_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN pengembalianuangmuka_r.is_sending = true AND pengembalianuangmuka_r.is_sent = false AND pengembalianuangmuka_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
                        WHEN pengembalianuangmuka_r.is_sending = false AND pengembalianuangmuka_r.is_sent = false AND pengembalianuangmuka_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        ELSE NULL::text
                    END AS status_proses,
                NULL::text AS billing_id,
                NULL::boolean AS is_sent_billing,
                pendaftaran_t.pasien_id AS partner_id,
                    CASE
                        WHEN pendaftaran_t.instalasi_id = 1 THEN \'1\'::text
                        WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NOT NULL THEN \'2\'::text
                        WHEN pendaftaran_t.instalasi_id = 3 THEN \'2\'::text
                        WHEN pendaftaran_t.instalasi_id = 2 THEN \'3\'::text
                        ELSE \'1\'::text
                    END AS patient_type
               FROM pengembalianuangmuka_r
                 JOIN ( SELECT b.loginpemakai_id,
                        b.pegawai_id
                       FROM loginpemakai_k b) loginpemakai_k ON pengembalianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id
                 JOIN ( SELECT b.pegawai_id,
                        b.nama_pegawai
                       FROM pegawai_m b) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
                 LEFT JOIN ( SELECT b.pengembalianuangmuka_id,
                        b.no_buktikeluar,
                        b.is_tunai,
                        b.no_rek
                       FROM tandabuktikeluar_t b) tandabuktikeluar_t ON pengembalianuangmuka_r.pengembalianuangmuka_id = tandabuktikeluar_t.pengembalianuangmuka_id
                 JOIN ( SELECT b.pendaftaran_id,
                        b.pasien_id,
                        b.no_pendaftaran,
                        b.instalasi_id,
                        b.pasienadmisi_id
                       FROM pendaftaran_t b) pendaftaran_t ON pengembalianuangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT b.pasien_id,
                        b.nama_pasien
                       FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            UNION ALL
             SELECT concat(\'PKUM\', pemakaianuangmuka_r.id) AS sync_id_api,
                pegawai_m.nama_pegawai AS user_name,
                pemakaianuangmuka_r.pendaftaran_id AS admission_id,
                pembayaran.no_pembayaran AS trans_no,
                pemakaianuangmuka_r.tgl_pemakaian AS trans_date,
                pemakaianuangmuka_r.keterangan AS trans_type,
                pembayaran.no_pembayaran AS reference_no,
                pendaftaran_t.no_pendaftaran AS admission_no,
                pasien_m.nama_pasien AS patient_name,
                \'Cash\'::text AS payment_name,
                pemakaianuangmuka_r.tgl_proses AS tglproses,
                \'-\'::character varying AS edc_machine,
                pemakaianuangmuka_r.pemakaian_uangmuka AS amount,
                \'-\'::text AS note,
                \'draft\'::text AS state,
                6 AS sync_type,
                pemakaianuangmuka_r.id,
                pemakaianuangmuka_r.is_sent,
                pemakaianuangmuka_r.is_sending,
                \'PEMAKAIAN_UANGMUKA\'::text AS tipe_rekap,
                    CASE
                        WHEN pemakaianuangmuka_r.is_sending = true AND pemakaianuangmuka_r.is_sent = false AND pemakaianuangmuka_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN pemakaianuangmuka_r.is_sending = true AND pemakaianuangmuka_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN pemakaianuangmuka_r.is_sending = false AND pemakaianuangmuka_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN pemakaianuangmuka_r.is_sending = true AND pemakaianuangmuka_r.is_sent = false AND pemakaianuangmuka_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
                        WHEN pemakaianuangmuka_r.is_sending = false AND pemakaianuangmuka_r.is_sent = false AND pemakaianuangmuka_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        ELSE NULL::text
                    END AS status_proses,
                int_billing_r.id::text AS billing_id,
                int_billing_r.is_sent AS is_sent_billing,
                pendaftaran_t.pasien_id AS partner_id,
                    CASE
                        WHEN pendaftaran_t.instalasi_id = 1 THEN \'1\'::text
                        WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NOT NULL THEN \'2\'::text
                        WHEN pendaftaran_t.instalasi_id = 3 THEN \'2\'::text
                        WHEN pendaftaran_t.instalasi_id = 2 THEN \'3\'::text
                        ELSE \'1\'::text
                    END AS patient_type
               FROM pemakaianuangmuka_r
                 JOIN ( SELECT c.loginpemakai_id,
                        c.pegawai_id
                       FROM loginpemakai_k c) loginpemakai_k ON pemakaianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id
                 JOIN ( SELECT c.pegawai_id,
                        c.nama_pegawai
                       FROM pegawai_m c) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
                 JOIN ( SELECT c.pendaftaran_id,
                        c.pembayaran_id,
                        c.no_pembayaran,
                        d.pemakaianuangmuka_id
                       FROM pembayaran_t c
                         JOIN pemakaianuangmuka_t d ON d.pembayaran_id = c.pembayaran_id) pembayaran ON pemakaianuangmuka_r.pendaftaran_id = pembayaran.pendaftaran_id AND pemakaianuangmuka_r.pemakaianuangmuka_id = pembayaran.pemakaianuangmuka_id
                 JOIN ( SELECT c.pendaftaran_id,
                        c.pasien_id,
                        c.no_pendaftaran,
                        c.instalasi_id,
                        c.pasienadmisi_id
                       FROM pendaftaran_t c) pendaftaran_t ON pemakaianuangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT c.pasien_id,
                        c.nama_pasien
                       FROM pasien_m c) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN ( SELECT c.pembayaran_id,
                        c.id,
                        c.is_sent
                       FROM int_billing_r c) int_billing_r ON pembayaran.pembayaran_id = int_billing_r.pembayaran_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_093735_migrate_odoo_view_int_uangmuka_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_093735_migrate_odoo_view_int_uangmuka_v cannot be reverted.\n";

        return false;
    }
    */
}
