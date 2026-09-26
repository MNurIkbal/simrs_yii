<?php

use yii\db\Migration;

/**
 * Class m210516_051046_migrate_20210516_infotagihanpasien_r
 */
class m210516_051046_migrate_20210516_infotagihanpasien_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT exists "public"."infotagihanpasien_r" (
  "infotagihanpasien_id" serial8,
  "pendaftaran_id" int4,
  "pasienmasukpenunjang_id" int4,
  "kelompok" varchar(20) COLLATE "pg_catalog"."default",
  "status" text COLLATE "pg_catalog"."default",
  "tipe_pasien" varchar(50) COLLATE "pg_catalog"."default",
  "checkPenjamin" varchar(15) COLLATE "pg_catalog"."default",
  "cyto" varchar(255) COLLATE "pg_catalog"."default",
  "defaultPenjamin" text COLLATE "pg_catalog"."default",
  "dijamin" numeric(18,0),
  "harga" numeric(18,0),
  "instalasi" varchar(50) COLLATE "pg_catalog"."default",
  "isPenjamin" varchar(15) COLLATE "pg_catalog"."default",
  "is_obat" bool,
  "kelompoktindakan_nama" varchar(100) COLLATE "pg_catalog"."default",
  "keterangan" text COLLATE "pg_catalog"."default",
  "nominal_diskon" numeric(18,0),
  "penjamin" varchar(255) COLLATE "pg_catalog"."default",
  "persen_diskon" numeric(18,0),
  "qty" numeric(18,0),
  "subtotal" numeric(18,0),
  "subtotal_origin" numeric(18,0),
  "tanggal" timestamp(0),
  "tindakan" varchar(100) COLLATE "pg_catalog"."default",
  "tindakan_obat_id" int4,
  "totalDibayar" numeric(18,0),
  "value" text COLLATE "pg_catalog"."default",
  CONSTRAINT "infotagihanpasien_r_pkey" PRIMARY KEY ("infotagihanpasien_id")
)
;');

        $this->execute('ALTER TABLE "public"."infotagihanpasien_r" 
  OWNER TO "postgres";');

          $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ALTER COLUMN "dijamin" TYPE numeric(18,0) USING "dijamin"::numeric(18,0);');

          $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ALTER COLUMN "harga" TYPE numeric(18,0) USING "harga"::numeric(18,0);');

          $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ALTER COLUMN "nominal_diskon" TYPE numeric(18,0) USING "nominal_diskon"::numeric(18,0);');

          $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ALTER COLUMN "persen_diskon" TYPE numeric(18,0) USING "persen_diskon"::numeric(18,0);');

          $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ALTER COLUMN "qty" TYPE numeric(18,0) USING "qty"::numeric(18,0);');

          $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ALTER COLUMN "subtotal" TYPE numeric(18,0) USING "subtotal"::numeric(18,0);');

          $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ALTER COLUMN "subtotal_origin" TYPE numeric(18,0) USING "subtotal_origin"::numeric(18,0);');

          $this->execute('ALTER TABLE "public"."infotagihanpasien_r" ALTER COLUMN "totalDibayar" TYPE numeric(18,0) USING "totalDibayar"::numeric(18,0);');

         

        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210516_051046_migrate_20210516_infotagihanpasien_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210516_051046_migrate_20210516_infotagihanpasien_r cannot be reverted.\n";

        return false;
    }
    */
}
