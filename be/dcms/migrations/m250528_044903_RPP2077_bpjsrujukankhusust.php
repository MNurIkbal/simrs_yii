<?php

use yii\db\Migration;

/**
 * Class m250528_044903_RPP2077_bpjsrujukankhusust
 */
class m250528_044903_RPP2077_bpjsrujukankhusust extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE SEQUENCE IF NOT EXISTS bpjs_rujukankhusus_t_id_seq AS bigint;
        ');
        
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."bpjs_rujukankhusus_t" (
                "id" bigint NOT NULL DEFAULT nextval(\'bpjs_rujukankhusus_t_id_seq\'), 
                "idrujukan" varchar(30) COLLATE "pg_catalog"."default",
                "norujukan" varchar(30) COLLATE "pg_catalog"."default",
                "nokapst" varchar(30) COLLATE "pg_catalog"."default",
                "nmpst" varchar(255) COLLATE "pg_catalog"."default",
                "diagppk" varchar(255) COLLATE "pg_catalog"."default",
                "tglrujukan_awal" date,
                "tglrujukan_berakhir" date,
                "created_date" timestamp(6) NOT NULL DEFAULT now(),
                "is_deleted" bool NOT NULL DEFAULT false,
                "last_sync" timestamp(6),
                CONSTRAINT "bpjs_rujukankhusus_t_pkey" PRIMARY KEY ("id")
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250528_044903_RPP2077_bpjsrujukankhusust cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250528_044903_RPP2077_bpjsrujukankhusust cannot be reverted.\n";

        return false;
    }
    */
}
