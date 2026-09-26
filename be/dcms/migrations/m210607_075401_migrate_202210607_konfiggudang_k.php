<?php

use yii\db\Migration;

/**
 * Class m210607_075401_migrate_202210607_konfiggudang_k
 */
class m210607_075401_migrate_202210607_konfiggudang_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfiggudang_k" ADD COLUMN if not exists "is_autogeneratekodebarang" bool NOT NULL DEFAULT false;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210607_075401_migrate_202210607_konfiggudang_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210607_075401_migrate_202210607_konfiggudang_k cannot be reverted.\n";

        return false;
    }
    */
}
