<?php

use yii\db\Migration;

/**
 * Class m210211_040344_migrate_20210211_konfigfarmasi_k
 */
class m210211_040344_migrate_20210211_konfigfarmasi_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN IF NOT exists "is_bypassworklist" bool DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210211_040344_migrate_20210211_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210211_040344_migrate_20210211_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }
    */
}
