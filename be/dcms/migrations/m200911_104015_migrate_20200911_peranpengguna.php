<?php

use yii\db\Migration;

/**
 * Class m200911_104015_migrate_20200911_peranpengguna
 */
class m200911_104015_migrate_20200911_peranpengguna extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."peranpengguna_k" ADD IF NOT EXISTS "is_exception" bool DEFAULT false;'); 

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200911_104015_migrate_20200911_peranpengguna cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200911_104015_migrate_20200911_peranpengguna cannot be reverted.\n";

        return false;
    }
    */
}
