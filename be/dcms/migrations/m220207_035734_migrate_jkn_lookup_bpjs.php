<?php

use yii\db\Migration;

/**
 * Class m220207_035734_migrate_jkn_lookup_bpjs
 */
class m220207_035734_migrate_jkn_lookup_bpjs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id = 1140;
        ");

        $this->execute("
            DELETE from lookup_m where lookup_id = 1110;
        ");

        $this->execute("
            DELETE from lookup_m where lookup_id = 1109;
        ");

        $this->execute("
            DELETE from lookup_m where lookup_id = 1114;
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1140, 'bpjs', 'version_jkn', '2.0', NULL, NULL, NULL, '2022-02-02 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1110, 'bpjs', 'url_jkn', 'https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev/', NULL, NULL, NULL, '2021-11-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1109, 'bpjs', 'user_key', NULL, NULL, NULL, NULL, '2021-11-11 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1114, 'bpjs', 'user_key_vclaim', NULL, NULL, NULL, NULL, '2021-12-02 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");

        $this->execute('
            ALTER TABLE "public"."spesialisruangan_mp" 
            ADD COLUMN IF NOT EXISTS "subspesialis_id" int4;
        ');

        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."subspesialis_m" (
            "subspesialis_id" serial4,
            "subspesialis_kode" varchar(32) COLLATE "pg_catalog"."default",
            "subspesialis_nama" varchar(100) COLLATE "pg_catalog"."default",
            "subspesialis_namalainnya" varchar(100) COLLATE "pg_catalog"."default",
            "additional_data" text COLLATE "pg_catalog"."default",
            "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
            "created_by" int4,
            "modified_count" int4,
            "last_modified_date" timestamp(6),
            "last_modified_by" int4,
            "is_deleted" bool NOT NULL DEFAULT false,
            "is_active" bool NOT NULL DEFAULT true,
            "deleted_date" timestamp(6),
            "deleted_by" int4,
            "spesialis_id" int4,
            CONSTRAINT "subspesialis_m_pkey" PRIMARY KEY ("subspesialis_id")
            )
            ;
        ');

        $this->execute('
            ALTER TABLE "public"."subspesialis_m" 
            OWNER TO "postgres";
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220207_035734_migrate_jkn_lookup_bpjs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220207_035734_migrate_jkn_lookup_bpjs cannot be reverted.\n";

        return false;
    }
    */
}
