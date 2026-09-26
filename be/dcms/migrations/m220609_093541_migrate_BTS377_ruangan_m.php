<?php

use yii\db\Migration;

/**
 * Class m220609_093541_migrate_BTS377_ruangan_m
 */
class m220609_093541_migrate_BTS377_ruangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."ruangan_m"
            ADD COLUMN IF NOT EXISTS "is_online" bool DEFAULT false;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220609_093541_migrate_BTS377_ruangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220609_093541_migrate_BTS377_ruangan_m cannot be reverted.\n";

        return false;
    }
    */
}
