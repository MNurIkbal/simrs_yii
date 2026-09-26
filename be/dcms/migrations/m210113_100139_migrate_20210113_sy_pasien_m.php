<?php

use yii\db\Migration;

/**
 * Class m210113_100139_migrate_20210113_sy_pasien_m
 */
class m210113_100139_migrate_20210113_sy_pasien_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasien_m"
ADD COLUMN IF NOT EXISTS "bahasa_sehari" varchar(20) COLLATE "pg_catalog"."default" DEFAULT 9999;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210113_100139_migrate_20210113_sy_pasien_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210113_100139_migrate_20210113_sy_pasien_m cannot be reverted.\n";

        return false;
    }
    */
}
