<?php

use yii\db\Migration;

/**
 * Class m211217_111327_migrate_US2450_hotfix_rl4a_rl4b
 */
class m211217_111327_migrate_US2450_hotfix_rl4a_rl4b extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.rl4_a_morbiditasrawatinap_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rl4_a_morbiditasrawatinap_v\" AS
            SELECT 'vertikal'::text AS kolom,
            klasifikasidiagnosa_m.dtd_id,
            dtd_m.dtd_kode AS no_dtd,
            diagnosa_m.diagnosa_kode AS dtd_noterperinci,
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

        $this->execute('DROP VIEW if exists public.rl4_b_morbiditasrawatjalan_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rl4_b_morbiditasrawatjalan_v\" AS
            SELECT 'vertikal'::text AS kolom,
            klasifikasidiagnosa_m.dtd_id,
            dtd_m.dtd_kode AS no_dtd,
            diagnosa_m.diagnosa_kode AS dtd_noterperinci,
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

            $this->execute('DROP VIEW if exists public.rl4_b_morbiditasrawatjalanexcel_v;');
            $this->execute("
                CREATE VIEW \"public\".\"rl4_b_morbiditasrawatjalanexcel_v\" AS
                SELECT 'vertikal'::text AS kolom,
                klasifikasidiagnosa_m.dtd_id,
                dtd_m.dtd_kode AS no_dtd,
                diagnosa_m.diagnosa_kode AS dtd_noterperinci,
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211217_111327_migrate_US2450_hotfix_rl4a_rl4b cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211217_111327_migrate_US2450_hotfix_rl4a_rl4b cannot be reverted.\n";

        return false;
    }
    */
}
