<?php

use yii\db\Migration;

/**
 * Class m220203_060636_migrate_cdh105_is_pesanstokobat_0_konfigfarmasi_k
 */
class m220203_060636_migrate_cdh105_is_pesanstokobat_0_konfigfarmasi_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN if not exists "is_pesanstokobat_0" bool DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220203_060636_migrate_cdh105_is_pesanstokobat_0_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220203_060636_migrate_cdh105_is_pesanstokobat_0_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }
    */
}
