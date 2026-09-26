<?php

use yii\db\Migration;

/**
 * Class m201112_061004_migrate_20201112_rekapanris
 */
class m201112_061004_migrate_20201112_rekapanris extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('CREATE TABLE IF NOT EXISTS "public"."rekapanris_r" (
  "id" serial8,
  "pendaftaran_id" int4,
  "pasienmasukpenunjang_id" int4,
  "tindakanpelayanan_id" int4,
  "daftartindakan_id" int4,
  "no_pembayaran" varchar(100) COLLATE "pg_catalog"."default",
  "payload" text COLLATE "pg_catalog"."default",
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  "is_update" bool DEFAULT false,
  "id_sync_sercon_update" text COLLATE "pg_catalog"."default",
  "sync_respon_update" text COLLATE "pg_catalog"."default",
  CONSTRAINT "rekapanris_r_pkey" PRIMARY KEY ("id")
)
;');
         $this->execute('ALTER TABLE "public"."rekapanris_r" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201112_061004_migrate_20201112_rekapanris cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_061004_migrate_20201112_rekapanris cannot be reverted.\n";

        return false;
    }
    */
}
