<?php

use yii\db\Migration;

/**
 * Class m220331_034744_migrate_cdh288_konfigfarmasi_k_update_ppn
 */
class m220331_034744_migrate_cdh288_konfigfarmasi_k_update_ppn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
			update konfigfarmasi_k set persenppn = 11 ;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220331_034744_migrate_cdh288_konfigfarmasi_k_update_ppn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220331_034744_migrate_cdh288_konfigfarmasi_k_update_ppn cannot be reverted.\n";

        return false;
    }
    */
}
