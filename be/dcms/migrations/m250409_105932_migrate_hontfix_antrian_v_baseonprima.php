<?php

use yii\db\Migration;

/**
 * Class m250409_105932_migrate_hontfix_antrian_v_baseonprima
 */
class m250409_105932_migrate_hontfix_antrian_v_baseonprima extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute("DROP VIEW IF EXISTS antrian_v");
		$antrian_v = file_get_contents(__DIR__ . '/definitions/antrian_v.sql');
		$this->execute($antrian_v);
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250409_105932_migrate_hontfix_antrian_v_baseonprima cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250409_105932_migrate_hontfix_antrian_v_baseonprima cannot be reverted.\n";

        return false;
    }
    */
}
