<?php

use yii\db\Migration;

/**
 * Class m220520_025348_hotfix_invoice_table_konfigtarif_k
 */
class m220520_025348_hotfix_invoice_table_konfigtarif_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigtarif_k ADD IF NOT EXISTS is_invoice_diskon BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220520_025348_hotfix_invoice_table_konfigtarif_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220520_025348_hotfix_invoice_table_konfigtarif_k cannot be reverted.\n";

        return false;
    }
    */
}
