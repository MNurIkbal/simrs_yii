<?php

use yii\db\Migration;

/**
 * Class m211028_071222_migrate_US1830_rm_laporanrl4a
 */
class m211028_071222_migrate_US1830_rm_laporanrl4a extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.rl4_a_morbiditasrawatinapexcel_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rl4_a_morbiditasrawatinapexcel_v\" AS
            SELECT 'vertikal'::text AS kolom,
            klasifikasidiagnosa_m.dtd_id,
            dtd_m.dtd_kode AS no_dtd,
            diagnosa_m.diagnosa_kode AS dtd_noterperinci,
            diagnosa_m.klasifikasidiagnosa_id,
            klasifikasidiagnosa_m.klasifikasidiagnosa_kode,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
            NULL::integer AS golonganumur_id,
            NULL::character varying AS golonganumur_nama,
            NULL::character varying AS golonganumur_namalainnya
            FROM (((diagnosa_m
            JOIN klasifikasidiagnosa_m ON ((diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id)))
            JOIN dtd_m ON ((klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id)))
            JOIN tabularlist_m ON ((dtd_m.tabularlist_id = tabularlist_m.tabularlist_id)))
            WHERE ((diagnosa_m.is_deleted = false) AND (diagnosa_m.is_active = true) AND (klasifikasidiagnosa_m.is_deleted = false) AND (klasifikasidiagnosa_m.is_active = true) AND (tabularlist_m.tabularlist_id <> 22) AND (dtd_m.is_deleted = false) AND (dtd_m.is_active = true) AND (dtd_m.tabularlist_id <> 22))
            UNION ALL
            SELECT 'horizontal'::text AS kolom,
            NULL::integer AS dtd_id,
            NULL::character(1) AS no_dtd,
            NULL::character varying AS dtd_noterperinci,
            NULL::integer AS klasifikasidiagnosa_id,
            NULL::character varying AS klasifikasidiagnosa_kode,
            NULL::integer AS diagnosa_id,
            NULL::character varying AS diagnosa_kode,
            NULL::character varying AS diagnosa_nama,
            golonganumur_m.golonganumur_id,
            golonganumur_m.golonganumur_nama,
            golonganumur_m.golonganumur_namalainnya
            FROM golonganumur_m
            WHERE (golonganumur_m.is_deleted = false)
            ;");
        $this->execute('
            ALTER TABLE public.rl4_a_morbiditasrawatinapexcel_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.rl4_a_morbiditasrawatinapdetailexcel_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rl4_a_morbiditasrawatinapdetailexcel_v\" AS
            SELECT 'RI'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.golonganumur_id,
            golonganumur_m.golonganumur_namalainnya,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            koreksidiagnosa_t.diagnosa_id,
            diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
            pasienadmisi_t.pasienpulang_id,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            koreksidiagnosa_t.tgl_koreksidiagnosa
            FROM (((((((pendaftaran_t
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            JOIN koreksidiagnosa_t ON ((pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id)))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 2) AND (koreksidiagnosa_t.deleted_date IS NULL))
            ;");
        $this->execute('
            ALTER TABLE public.rl4_a_morbiditasrawatinapdetailexcel_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211028_071222_migrate_US1830_rm_laporanrl4a cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211028_071222_migrate_US1830_rm_laporanrl4a cannot be reverted.\n";

        return false;
    }
    */
}
