<?php

use yii\db\Migration;

/**
 * Class m220711_143630_integrasi_roche_r
 */
class m220711_143630_integrasi_roche_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."integrasi_roche_r" (
                "id" serial4 NOT NULL,
                "pendaftaran_id" int4 NULL,
                "pasienmasukpenunjang_id" int4 NULL,
                "payload" text COLLATE "pg_catalog"."default",
                "is_sent" bool,
                "is_sending" bool,
                "id_sync_sercon" text COLLATE "pg_catalog"."default",
                "sync_respon" text COLLATE "pg_catalog"."default",
                "pasienkirimkeunitlain_id" int4,
                CONSTRAINT "integrasi_roche_r_pkey" PRIMARY KEY ("id")
            )
        ');
        $this->execute('
            ALTER TABLE "public"."integrasi_roche_r" 
              OWNER TO "postgres"
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220711_143630_integrasi_roche_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220711_143630_integrasi_roche_r cannot be reverted.\n";

        return false;
    }
    */
}
