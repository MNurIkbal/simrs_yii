<?php

use yii\db\Migration;

/**
 * Class m210121_083417_migrate_20200121_obatalkes_m
 */
class m210121_083417_migrate_20200121_obatalkes_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD IF NOT EXISTS "manufaktur_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210121_083417_migrate_20200121_obatalkes_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210121_083417_migrate_20200121_obatalkes_m cannot be reverted.\n";

        return false;
    }
    */
}
