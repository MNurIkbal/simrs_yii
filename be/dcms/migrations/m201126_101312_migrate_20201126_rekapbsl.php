<?php

use yii\db\Migration;

/**
 * Class m201126_101312_migrate_20201126_rekapbsl
 */
class m201126_101312_migrate_20201126_rekapbsl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."rekapanbsl_r" (
  "id" serial8,
  "pendaftaran_id" int4,
  "pasienmasukpenunjang_id" int4,
  "tindakanpelayanan_id" int4,
  "daftartindakan_id" int4,
  "tipepaket_id" int4,
  "no_pembayaran" varchar(100) COLLATE "pg_catalog"."default",
  "payload" text COLLATE "pg_catalog"."default",
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "is_deleted" bool,
  CONSTRAINT "rekapanbsl_r_pkey" PRIMARY KEY ("id")
)
;');
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201126_101312_migrate_20201126_rekapbsl cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201126_101312_migrate_20201126_rekapbsl cannot be reverted.\n";

        return false;
    }
    */
}
