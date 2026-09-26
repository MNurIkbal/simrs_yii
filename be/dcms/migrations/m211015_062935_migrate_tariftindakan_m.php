<?php

use yii\db\Migration;

/**
 * Class m211015_062935_migrate_tariftindakan_m
 */
class m211015_062935_migrate_tariftindakan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tariftindakan_m" ADD COLUMN if not exists "ruangan_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211015_062935_migrate_tariftindakan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211015_062935_migrate_tariftindakan_m cannot be reverted.\n";

        return false;
    }
    */
}
