<?php

use yii\db\Migration;

/**
 * Class m210825_090008_migrate_konfigpr
 */
class m210825_090008_migrate_konfigpr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN if not exists "is_large_unit_pr" bool DEFAULT false;');
 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210825_090008_migrate_konfigpr cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210825_090008_migrate_konfigpr cannot be reverted.\n";

        return false;
    }
    */
}
