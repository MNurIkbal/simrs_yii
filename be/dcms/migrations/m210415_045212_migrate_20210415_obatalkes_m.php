<?php

use yii\db\Migration;

/**
 * Class m210415_045212_migrate_20210415_obatalkes_m
 */
class m210415_045212_migrate_20210415_obatalkes_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN IF NOT exists "is_narcotic" bool;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210415_045212_migrate_20210415_obatalkes_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210415_045212_migrate_20210415_obatalkes_m cannot be reverted.\n";

        return false;
    }
    */
}
