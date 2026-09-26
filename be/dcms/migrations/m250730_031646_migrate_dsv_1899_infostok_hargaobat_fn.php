<?php

use yii\db\Migration;

/**
 * Class m250730_031646_migrate_dsv_1899_infostok_hargaobat_fn
 */
class m250730_031646_migrate_dsv_1899_infostok_hargaobat_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {		
		$this->execute("DROP function IF EXISTS infostokobatalkes_fnr_new");
		$infostokobatalkes_fnr_new = file_get_contents(__DIR__ . '/definitions/infostokobatalkes_fnr_new.sql');
		$this->execute($infostokobatalkes_fnr_new);
		
		$this->execute("DROP function IF EXISTS  hargaobatalkes_fn");
		$hargaobatalkes_fn = file_get_contents(__DIR__ . '/definitions/hargaobatalkes_fn.sql');
		$this->execute($hargaobatalkes_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250730_031646_migrate_dsv_1899_infostok_hargaobat_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250730_031646_migrate_dsv_1899_infostok_hargaobat_fn cannot be reverted.\n";

        return false;
    }
    */
}
