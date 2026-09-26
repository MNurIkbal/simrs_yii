<?php

use yii\db\Migration;

/**
 * Class m210423_014319_oddo_20210423_penyesuaiatable
 */
class m210423_014319_oddo_20210423_penyesuaiatable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    $this->execute('CREATE TABLE "public"."int_billing_r" (
  "id" serial8,
  "pembayaran_id" int4,
  "pendaftaran_id" int4,
  "pasienadmisi_id" int4,
  "total_tagihan" float8 DEFAULT 0,
  "total_dibayar" float8 DEFAULT 0,
  "total_dijamin" float8 DEFAULT 0,
  "total_sisatagihan" float8 DEFAULT 0,
  "total_kembalian" float8 DEFAULT 0,
  "total_administrasi" float8 DEFAULT 0,
  "total_pembulatan" float8 DEFAULT 0,
  "total_pembebasan" float8 DEFAULT 0,
  "penggunaan_uangmuka" float8 DEFAULT 0,
  "pemberianpiutang_id" int4,
  "total_ditagihkan" float4 DEFAULT 0,
  "total_tunai" float8 DEFAULT 0,
  "total_nontunai" float8 DEFAULT 0,
  "total_discount" float8 DEFAULT 0,
  "total_discountpembayaran" float8 DEFAULT 0,
  "catatan" text COLLATE "pg_catalog"."default",
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool DEFAULT false,
  "is_active" bool DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  "keterangan" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  "is_update" bool DEFAULT false,
  "is_update_sent" bool DEFAULT false,
  "is_update_sending" bool DEFAULT false,
  CONSTRAINT "int_billing_r_pkey" PRIMARY KEY ("id")
)
;');

    $this->execute('ALTER TABLE "public"."int_billing_r" OWNER TO "postgres";');

    $this->execute('ALTER TABLE "public"."tindakansudahbayar_t" ADD COLUMN IF NOT exists "pembayaran_id" int4;');

    $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN IF NOT exists "pembayaran_id" int4;');

    $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN IF NOT exists "alasan_batal" text COLLATE "pg_catalog"."default";');

    $this->execute('ALTER TABLE "public"."obatsudahbayar_t" ADD COLUMN IF NOT exists "pembayaran_id" int4;');

    $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN IF NOT exists "pembayaran_id" int4;');

    $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN IF NOT exists "alasan_batal" text COLLATE "pg_catalog"."default";');

    $this->execute('ALTER TABLE "public"."obatalkespasien_r" ADD COLUMN IF NOT exists "pembayaran_id" int4;');
   

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210423_014319_oddo_20210423_penyesuaiatable cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210423_014319_oddo_20210423_penyesuaiatable cannot be reverted.\n";

        return false;
    }
    */
}
