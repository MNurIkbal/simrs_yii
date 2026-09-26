<?php

use yii\db\Migration;

/**
 * Class m241216_094401_integrasi_vendor_asuransi_r_db_sirs_integration
 */
class m241216_094401_integrasi_vendor_asuransi_r_db_sirs_integration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('CREATE TABLE IF NOT EXISTS "public"."integrasi_vendor_asuransi_r" (
  "id" serial4 NOT NULL ,
  "waktumulai" timestamp(6),
  "waktuselesai" timestamp(6),
  "payload" text COLLATE "pg_catalog"."default",
  "response" text COLLATE "pg_catalog"."default",
  "raw_response" text COLLATE "pg_catalog"."default",
  "url" varchar COLLATE "pg_catalog"."default",
  "status_code" int4,
  "created_at" timestamp(6),
  "state" varchar COLLATE "pg_catalog"."default",
  "provider" varchar COLLATE "pg_catalog"."default",
  CONSTRAINT "integrasi_vendor_asuransi_r_pk" PRIMARY KEY ("id")
)
;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241216_094401_integrasi_vendor_asuransi_r_db_sirs_integration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241216_094401_integrasi_vendor_asuransi_r_db_sirs_integration cannot be reverted.\n";

        return false;
    }
    */
}
