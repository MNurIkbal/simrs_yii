<?php

use yii\db\Migration;

/**
 * Class m200903_103305_migrate_20200903_notifikasi
 */
class m200903_103305_migrate_20200903_notifikasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
           $this->execute('DROP TABLE if exists "public"."notifikasi_r";');

           $this->execute('DROP SEQUENCE if exists notifikasi_r_notifikasi_id_seq;');
           
           $this->execute('CREATE TABLE "public"."notifikasi_r" (
  "notifikasi_id" serial8,
  "instalasi_id" int4 NOT NULL,
  "modul_id" int4,
  "tglnotifikasi" timestamp(6),
  "judulnotifikasi" varchar(250) COLLATE "pg_catalog"."default",
  "isi_notifikasi" text COLLATE "pg_catalog"."default" NOT NULL,
  "is_read" bool DEFAULT false,
  "lama_harinotif" int4 DEFAULT 2,
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
  "user_id" int4,
  "type" text COLLATE "pg_catalog"."default" NOT NULL DEFAULT \'other\'::text,
  "data" json,
  CONSTRAINT "pk_nofitikasi" PRIMARY KEY ("notifikasi_id")
)
;');
           $this->execute('ALTER TABLE "public"."notifikasi_r" 
  OWNER TO "postgres";');

    $this->execute('COMMENT ON COLUMN "public"."notifikasi_r"."isi_notifikasi" IS \'message\';');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200903_103305_migrate_20200903_notifikasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200903_103305_migrate_20200903_notifikasi cannot be reverted.\n";

        return false;
    }
    */
}
