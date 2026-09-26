<?php

use yii\db\Migration;

/**
 * Class m220306_053904_migrate_BTS137_updatetanggalpulang_v
 */
class m220306_053904_migrate_BTS137_updatetanggalpulang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.updatetanggalpulang_v;');
        $this->execute("
            CREATE VIEW \"public\".\"updatetanggalpulang_v\" AS
            SELECT pasienadmisi_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.pasienadmisi_id,
            pasienpulang_t.pasienpulang_id,
            bpjs_t.bpjs_id,
            bpjs_t.nosep,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            bpjs_t.nokartuasuransi,
            pasienadmisi_t.last_modified_date AS tglpasienpulang,
            pasienadmisi_t.status_ranap AS status_pulang_id,
            lookup_statusranap.lookup_name AS status_pulang_nama,
            pasienpulang_t.no_surat_kematian,
            pasienpulang_t.tgl_meninggal,
            NULL::text AS no_up_manual
            FROM (((((pendaftaran_t
            JOIN ( SELECT a.pasienadmisi_id,
            a.pasien_id,
            a.status_ranap,
            a.pasienpulang_id,
            a.pendaftaran_id,
            a.last_modified_date
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien
            FROM pasien_m a) pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.pendaftaran_id,
            a.bpjs_id,
            a.nokartuasuransi,
            a.nosep
            FROM (bpjs_t a
            JOIN ( SELECT max(bpjs_t_2.bpjs_id) AS maxbpjs_id,
            bpjs_t_2.pendaftaran_id
            FROM bpjs_t bpjs_t_2
            GROUP BY bpjs_t_2.pendaftaran_id) bpjs_t_1 ON (((a.bpjs_id = bpjs_t_1.maxbpjs_id) AND (a.pendaftaran_id = bpjs_t_1.pendaftaran_id))))) bpjs_t ON ((pasienadmisi_t.pendaftaran_id = bpjs_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasienpulang_id,
            a.pasienadmisi_id,
            a.tglpasienpulang,
            a.tgl_meninggal,
            a.no_surat_kematian
            FROM pasienpulang_t a) pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) lookup_statusranap ON ((pasienadmisi_t.status_ranap = lookup_statusranap.lookup_id)))
        ;");
        $this->execute('
            ALTER TABLE public.updatetanggalpulang_v OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220306_053904_migrate_BTS137_updatetanggalpulang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220306_053904_migrate_BTS137_updatetanggalpulang_v cannot be reverted.\n";

        return false;
    }
    */
}
