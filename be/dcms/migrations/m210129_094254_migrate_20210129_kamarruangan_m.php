<?php

use yii\db\Migration;

/**
 * Class m210129_094254_migrate_20210129_kamarruangan_m
 */
class m210129_094254_migrate_20210129_kamarruangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."kamarruangan_m" ADD COLUMN IF NOT EXISTS "durasi" float8;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210129_094254_migrate_20210129_kamarruangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210129_094254_migrate_20210129_kamarruangan_m cannot be reverted.\n";

        return false;
    }
    */
}
