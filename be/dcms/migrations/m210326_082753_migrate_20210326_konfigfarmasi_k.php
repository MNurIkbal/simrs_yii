<?php

use yii\db\Migration;

/**
 * Class m210326_082753_migrate_20210326_konfigfarmasi_k
 */
class m210326_082753_migrate_20210326_konfigfarmasi_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN IF NOT exists "max_dataso" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210326_082753_migrate_20210326_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210326_082753_migrate_20210326_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }
    */
}
