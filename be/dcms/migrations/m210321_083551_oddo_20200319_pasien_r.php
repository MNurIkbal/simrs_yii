<?php

use yii\db\Migration;

/**
 * Class m210321_083551_oddo_20200319_pasien_r
 */
class m210321_083551_oddo_20200319_pasien_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasien_r" ADD COLUMN IF NOT exists "additional_pasien" text COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210321_083551_oddo_20200319_pasien_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210321_083551_oddo_20200319_pasien_r cannot be reverted.\n";

        return false;
    }
    */
}
