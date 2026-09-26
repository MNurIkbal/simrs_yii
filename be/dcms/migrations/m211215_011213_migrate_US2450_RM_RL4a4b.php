<?php

use yii\db\Migration;

/**
 * Class m211215_011213_migrate_US2450_RM_RL4a4b
 */
class m211215_011213_migrate_US2450_RM_RL4a4b extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.diagnosa_v;');
        $this->execute('DROP VIEW if exists public.diagnosaruangan_v;');
        $this->execute('DROP VIEW if exists public.rl4_a_morbiditasrawatinap_v;');
        $this->execute('DROP VIEW if exists public.rl4_a_morbiditasrawatinapexcel_v;');
        $this->execute('DROP VIEW if exists public.rl4_b_morbiditasrawatjalan_v;');
        $this->execute('DROP VIEW if exists public.rl4_b_morbiditasrawatjalanexcel_v;');

        $this->execute('ALTER TABLE "public"."tabularlist_m" 
          ALTER COLUMN "tabularlist_chapter" TYPE varchar(255) COLLATE "pg_catalog"."default",
          ALTER COLUMN "tabularlist_block" TYPE varchar(255) COLLATE "pg_catalog"."default",
          ALTER COLUMN "tabularlist_revisi" TYPE varchar(255) COLLATE "pg_catalog"."default",
          ALTER COLUMN "tabularlist_versi" TYPE varchar(255) COLLATE "pg_catalog"."default";
          ');

        $this->execute('ALTER TABLE "public"."klasifikasidiagnosa_m" 
          ALTER COLUMN "klasifikasidiagnosa_kode" TYPE varchar(255) COLLATE "pg_catalog"."default";
          ');

        $this->execute('ALTER TABLE "public"."dtd_m" 
          ALTER COLUMN "dtd_namalainnya" TYPE varchar(255) COLLATE "pg_catalog"."default";
          ');

        $this->execute("
            CREATE VIEW \"public\".\"diagnosa_v\" AS
            SELECT diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama AS diagnosa_namalainnya,
            diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
            klasifikasidiagnosa_m.klasifikasidiagnosa_nama,
            dtd_m.dtd_nama,
            tabularlist_m.tabularlist_chapter,
            tabularlist_m.tabularlist_versi,
            diagnosa_m.is_active,
            diagnosa_m.is_deleted
            FROM (((diagnosa_m
            LEFT JOIN klasifikasidiagnosa_m ON ((diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id)))
            LEFT JOIN dtd_m ON ((klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id)))
            LEFT JOIN tabularlist_m ON ((dtd_m.tabularlist_id = tabularlist_m.tabularlist_id)))
            WHERE ((diagnosa_m.is_active = true) AND (diagnosa_m.is_deleted = false) AND (diagnosa_m.klasifikasidiagnosa_id IS NOT NULL))
            UNION ALL
            SELECT diagnosakep_m.diagnosakep_id AS diagnosa_id,
            diagnosakep_m.diagnosakep_kode AS diagnosa_kode,
            diagnosakep_m.diagnosakep_nama AS diagnosa_namalainnya,
            diagnosakep_m.diagnosakep_nama AS diagnosa_nama,
            NULL::character varying AS klasifikasidiagnosa_nama,
            NULL::character varying AS dtd_nama,
            NULL::character varying AS tabularlist_chapter,
            'ICD_KEP'::character varying AS tabularlist_versi,
            diagnosakep_m.is_active,
            diagnosakep_m.is_deleted
            FROM diagnosakep_m
            WHERE ((diagnosakep_m.is_active = true) AND (diagnosakep_m.is_deleted = false))
            ;");
            $this->execute('
                ALTER TABLE public.diagnosa_v OWNER TO postgres;
            ');

            $this->execute("
                CREATE VIEW \"public\".\"diagnosaruangan_v\" AS
                SELECT diagnosa_m.diagnosa_id,
                diagnosa_m.diagnosa_kode,
                diagnosa_m.diagnosa_nama,
                diagnosa_m.diagnosa_namalainnya,
                kasuspenyakitdiagnosa_mp.jeniskasuspenyakit_id,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                klasifikasidiagnosa_m.klasifikasidiagnosa_nama,
                dtd_m.dtd_nama,
                tabularlist_m.tabularlist_chapter,
                tabularlist_m.tabularlist_versi,
                kasuspenyakitruangan_mp.ruangan_id,
                ruangan_m.ruangan_nama
                FROM (((((((diagnosa_m
                JOIN kasuspenyakitdiagnosa_mp ON ((diagnosa_m.diagnosa_id = kasuspenyakitdiagnosa_mp.diagnosa_id)))
                JOIN jeniskasuspenyakit_m ON ((kasuspenyakitdiagnosa_mp.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                JOIN kasuspenyakitruangan_mp ON ((kasuspenyakitdiagnosa_mp.jeniskasuspenyakit_id = kasuspenyakitruangan_mp.jeniskasuspenyakit_id)))
                JOIN ruangan_m ON ((kasuspenyakitruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
                JOIN klasifikasidiagnosa_m ON ((diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id)))
                JOIN dtd_m ON ((klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id)))
                JOIN tabularlist_m ON ((dtd_m.tabularlist_id = tabularlist_m.tabularlist_id)))
                WHERE ((diagnosa_m.is_active = true) AND (diagnosa_m.is_deleted = false))
            ;");
            $this->execute('
                ALTER TABLE public.diagnosaruangan_v OWNER TO postgres;
            ');

            $this->execute("
            CREATE VIEW \"public\".\"rl4_a_morbiditasrawatinap_v\" AS
            SELECT 'vertikal'::text AS kolom,
            klasifikasidiagnosa_m.dtd_id,
            dtd_m.dtd_kode AS no_dtd,
            klasifikasidiagnosa_m.klasifikasidiagnosa_kode AS dtd_noterperinci,
            diagnosa_m.klasifikasidiagnosa_id,
            klasifikasidiagnosa_m.klasifikasidiagnosa_kode,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            NULL::integer AS golonganumur_id,
            NULL::character varying AS golonganumur_nama,
            NULL::character varying AS golonganumur_namalainnya
            FROM ((((diagnosa_m
            JOIN klasifikasidiagnosa_m ON ((diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id)))
            JOIN dtd_m ON ((klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id)))
            JOIN tabularlist_m ON ((dtd_m.tabularlist_id = tabularlist_m.tabularlist_id)))
            JOIN koreksidiagnosa_t ON ((diagnosa_m.diagnosa_id = koreksidiagnosa_t.diagnosa_id)))
            WHERE ((diagnosa_m.is_deleted = false) AND (diagnosa_m.is_active = true) AND (klasifikasidiagnosa_m.is_deleted = false) AND (klasifikasidiagnosa_m.is_active = true) AND (dtd_m.is_deleted = false) AND (dtd_m.is_active = true) AND (koreksidiagnosa_t.kelompokdiagnosa_id = 2))
            GROUP BY koreksidiagnosa_t.diagnosa_id, klasifikasidiagnosa_m.dtd_id, dtd_m.dtd_kode, dtd_m.dtd_noterperinci, diagnosa_m.klasifikasidiagnosa_id, klasifikasidiagnosa_m.klasifikasidiagnosa_kode, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, klasifikasidiagnosa_m.klasifikasidiagnosa_nama, NULL::integer, NULL::character varying
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
                ALTER TABLE public.rl4_a_morbiditasrawatinap_v OWNER TO postgres;
            ');

            $this->execute("
            CREATE VIEW \"public\".\"rl4_a_morbiditasrawatinapexcel_v\" AS
            SELECT 'vertikal'::text AS kolom,
            klasifikasidiagnosa_m.dtd_id,
            dtd_m.dtd_kode AS no_dtd,
            klasifikasidiagnosa_m.klasifikasidiagnosa_kode AS dtd_noterperinci,
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
            WHERE ((diagnosa_m.is_deleted = false) AND (diagnosa_m.is_active = true) AND (klasifikasidiagnosa_m.is_deleted = false) AND (klasifikasidiagnosa_m.is_active = true) AND (dtd_m.is_deleted = false) AND (dtd_m.is_active = true) AND (diagnosa_m.klasifikasidiagnosa_id <> ALL (ARRAY[538])))
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

            $this->execute("
            CREATE VIEW \"public\".\"rl4_b_morbiditasrawatjalan_v\" AS
            SELECT 'vertikal'::text AS kolom,
            klasifikasidiagnosa_m.dtd_id,
            dtd_m.dtd_kode AS no_dtd,
            klasifikasidiagnosa_m.klasifikasidiagnosa_kode AS dtd_noterperinci,
            diagnosa_m.klasifikasidiagnosa_id,
            klasifikasidiagnosa_m.klasifikasidiagnosa_kode,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            NULL::integer AS golonganumur_id,
            NULL::character varying AS golonganumur_nama,
            NULL::character varying AS golonganumur_namalainnya
            FROM ((((diagnosa_m
            JOIN klasifikasidiagnosa_m ON ((diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id)))
            JOIN dtd_m ON ((klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id)))
            JOIN koreksidiagnosa_t ON ((diagnosa_m.diagnosa_id = koreksidiagnosa_t.diagnosa_id)))
            JOIN tabularlist_m ON ((dtd_m.tabularlist_id = tabularlist_m.tabularlist_id)))
            WHERE ((diagnosa_m.is_deleted = false) AND (klasifikasidiagnosa_m.is_deleted = false) AND (dtd_m.is_active = true) AND (dtd_m.is_deleted = false) AND (tabularlist_m.tabularlist_id <> 1) AND (dtd_m.tabularlist_id <> 1) AND (koreksidiagnosa_t.kelompokdiagnosa_id = 2))
            GROUP BY koreksidiagnosa_t.diagnosa_id, klasifikasidiagnosa_m.dtd_id, dtd_m.dtd_kode, dtd_m.dtd_noterperinci, diagnosa_m.klasifikasidiagnosa_id, klasifikasidiagnosa_m.klasifikasidiagnosa_kode, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama, NULL::integer, NULL::character varying
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
                ALTER TABLE public.rl4_b_morbiditasrawatjalan_v OWNER TO postgres;
            ');

            $this->execute("
            CREATE VIEW \"public\".\"rl4_b_morbiditasrawatjalanexcel_v\" AS
            SELECT 'vertikal'::text AS kolom,
            klasifikasidiagnosa_m.dtd_id,
            dtd_m.dtd_kode AS no_dtd,
            klasifikasidiagnosa_m.klasifikasidiagnosa_kode AS dtd_noterperinci,
            diagnosa_m.klasifikasidiagnosa_id,
            klasifikasidiagnosa_m.klasifikasidiagnosa_kode,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            NULL::integer AS golonganumur_id,
            NULL::character varying AS golonganumur_nama,
            NULL::character varying AS golonganumur_namalainnya
            FROM (((diagnosa_m
            JOIN klasifikasidiagnosa_m ON ((diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id)))
            JOIN dtd_m ON ((klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id)))
            JOIN tabularlist_m ON ((dtd_m.tabularlist_id = tabularlist_m.tabularlist_id)))
            WHERE ((diagnosa_m.is_deleted = false) AND (diagnosa_m.is_active = true) AND (klasifikasidiagnosa_m.is_deleted = false) AND (klasifikasidiagnosa_m.is_active = true) AND (dtd_m.is_deleted = false) AND (dtd_m.is_active = true) AND (diagnosa_m.klasifikasidiagnosa_id <> ALL (ARRAY[538])))
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
                ALTER TABLE public.rl4_b_morbiditasrawatjalanexcel_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.rl4_a_morbiditasrawatinapdetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rl4_a_morbiditasrawatinapdetail_v\" AS
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
            ALTER TABLE public.rl4_a_morbiditasrawatinapdetail_v OWNER TO postgres;
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

        $this->execute('DROP VIEW if exists public.rl4_b_morbiditasrawatjalandetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rl4_b_morbiditasrawatjalandetail_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.golonganumur_id,
            golonganumur_m.golonganumur_namalainnya,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            koreksidiagnosa_t.diagnosa_id,
            diagnosa_m.diagnosa_nama,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            koreksidiagnosa_t.tgl_koreksidiagnosa,
            CASE
            WHEN (koreksidiagnosa_t.is_diagnosa_baru = true) THEN 'BARU'::text
            ELSE 'LAMA'::text
            END AS is_diagnosa_barulama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama
            FROM (((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            JOIN koreksidiagnosa_t ON ((pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id)))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            WHERE ((pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND (koreksidiagnosa_t.kelompokdiagnosa_id = 2))
        ;");
        $this->execute('
            ALTER TABLE public.rl4_b_morbiditasrawatjalandetail_v OWNER TO postgres;
        ');

        $this->execute('DROP VIEW if exists public.rl4_b_morbiditasrawatjalandetailexcel_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rl4_b_morbiditasrawatjalandetailexcel_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.golonganumur_id,
            golonganumur_m.golonganumur_namalainnya,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            koreksidiagnosa_t.diagnosa_id,
            diagnosa_m.diagnosa_nama,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            koreksidiagnosa_t.tgl_koreksidiagnosa,
            CASE
            WHEN (koreksidiagnosa_t.is_diagnosa_baru = true) THEN 'BARU'::text
            ELSE 'LAMA'::text
            END AS is_diagnosa_barulama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama
            FROM (((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            JOIN koreksidiagnosa_t ON ((pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id)))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            WHERE ((pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND (koreksidiagnosa_t.kelompokdiagnosa_id = 2))
        ;");
        $this->execute('
            ALTER TABLE public.rl4_b_morbiditasrawatjalandetailexcel_v OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211215_011213_migrate_US2450_RM_RL4a4b cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211215_011213_migrate_US2450_RM_RL4a4b cannot be reverted.\n";

        return false;
    }
    */
}
