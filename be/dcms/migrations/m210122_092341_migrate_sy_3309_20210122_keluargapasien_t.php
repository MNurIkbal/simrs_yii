<?php

use yii\db\Migration;

/**
 * Class m210122_092341_migrate_sy_3309_20210122_keluargapasien_t
 */
class m210122_092341_migrate_sy_3309_20210122_keluargapasien_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."keluargapasien_t" (
            "keluargapasien_id" serial8,
            "keluarga_nama" varchar(100) COLLATE "pg_catalog"."default",
            "keluarga_jk" varchar(20) COLLATE "pg_catalog"."default",
            "keluarga_hubungan" varchar(50) COLLATE "pg_catalog"."default",
            "keluarga_alamat" text COLLATE "pg_catalog"."default",
            "keluarga_no_telepon" varchar(15) COLLATE "pg_catalog"."default",
            "keluarga_namadepan" varchar(10) COLLATE "pg_catalog"."default",
            "keluarga_propinsi_id" int4,
            "keluarga_kabupaten_id" int4,
            "keluarga_kecamatan_id" int4,
            "keluarga_kelurahan_id" int4,
            "keluarga_pekerjaan_id" int4,
            "keluarga_rt" varchar(10) COLLATE "pg_catalog"."default",
            "keluarga_rw" varchar(10) COLLATE "pg_catalog"."default",
            "pasien_id" int4,
            "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
            "created_by" int4,
            "modified_count" int4,
            "last_modified_date" timestamp(6),
            "last_modified_by" int4,
            "is_deleted" bool NOT NULL DEFAULT false,
            "is_active" bool NOT NULL DEFAULT true,
            "deleted_date" timestamp(6),
            "deleted_by" int4,
            CONSTRAINT "keluargapasien_t_pkey" PRIMARY KEY ("keluargapasien_id"));
        ');

        $this->execute('
            ALTER TABLE "public"."keluargapasien_t" OWNER TO "postgres";
        ');
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210122_092341_migrate_sy_3309_20210122_keluargapasien_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210122_092341_migrate_sy_3309_20210122_keluargapasien_t cannot be reverted.\n";

        return false;
    }
    */
}
