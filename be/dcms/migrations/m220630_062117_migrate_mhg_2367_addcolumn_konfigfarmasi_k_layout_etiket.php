<?php

use yii\db\Migration;

/**
 * Class m220630_062117_migrate_mhg_2367_addcolumn_konfigfarmasi_k_layout_etiket
 */
class m220630_062117_migrate_mhg_2367_addcolumn_konfigfarmasi_k_layout_etiket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."konfigfarmasi_k" ADD IF NOT EXISTS "layout_etiket" json;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220630_062117_migrate_mhg_2367_addcolumn_konfigfarmasi_k_layout_etiket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220630_062117_migrate_mhg_2367_addcolumn_konfigfarmasi_k_layout_etiket cannot be reverted.\n";

        return false;
    }
    */
}
