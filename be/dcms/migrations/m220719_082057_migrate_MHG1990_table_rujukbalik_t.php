<?php

use yii\db\Migration;

/**
 * Class m220719_082057_migrate_MHG1990_table_rujukbalik_t
 */
class m220719_082057_migrate_MHG1990_table_rujukbalik_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."rujukbalik_t" (
                "rujukbalik_id" serial8 NOT NULL PRIMARY KEY,
                "pendaftaran_id" int4 NOT NULL,
                "pasienadmisi_id" int4,
                "tgl_rujukbalik" timestamp(6),
                "alamat" text COLLATE "pg_catalog"."default",
                "email" varchar(50) COLLATE "pg_catalog"."default",
                "kode_dpjp" varchar(100) COLLATE "pg_catalog"."default",
                "nama_dokter" varchar(100) COLLATE "pg_catalog"."default",
                "saran" text COLLATE "pg_catalog"."default",
                "diagnosa" text COLLATE "pg_catalog"."default",
                "parent_id" int4,
                "data_reseptur" text COLLATE "pg_catalog"."default",
                "no_srb" varchar(50) COLLATE "pg_catalog"."default" NOT NULL,
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

        $this->execute('
            ALTER TABLE "public"."rujukbalik_t" OWNER TO "postgres";
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220719_082057_migrate_MHG1990_table_rujukbalik_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220719_082057_migrate_MHG1990_table_rujukbalik_t cannot be reverted.\n";

        return false;
    }
    */
}
