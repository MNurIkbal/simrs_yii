<?php

use yii\db\Migration;

/**
 * Class m220707_031212_migrate_mhg1815_table_pemberianinfus_t
 */
class m220707_031212_migrate_mhg1815_table_pemberianinfus_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."pemberianinfus_t" (
                "pemberianinfus_id" serial8 NOT NULL PRIMARY KEY,
                "pendaftaran_id" int4,
                "pasienadmisi_id" int4,
                "pegawai_id" int4,
                "tgl_pemasangan" timestamp(6),
                "instruksitindakan_id" int4,
                "instruksitindakanbmhp_id" varchar(100) COLLATE "pg_catalog"."default",
                "volume" varchar(30) COLLATE "pg_catalog"."default",
                "durasi" varchar(30) COLLATE "pg_catalog"."default",
                "jumlah_tetesan" varchar(30) COLLATE "pg_catalog"."default",
                "titik_pemasangan" varchar(100) COLLATE "pg_catalog"."default",
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220707_031212_migrate_mhg1815_table_pemberianinfus_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220707_031212_migrate_mhg1815_table_pemberianinfus_t cannot be reverted.\n";

        return false;
    }
    */
}
