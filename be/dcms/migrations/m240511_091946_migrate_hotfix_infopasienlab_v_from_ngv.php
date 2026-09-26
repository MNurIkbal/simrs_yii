<?php

use yii\db\Migration;

/**
 * Class m240511_091946_migrate_hotfix_infopasienlab_v_from_ngv
 */
class m240511_091946_migrate_hotfix_infopasienlab_v_from_ngv extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute("DROP VIEW IF EXISTS laporanpasienlabkasir_v");
		$this->execute("DROP VIEW IF EXISTS infopasienlab_v");
		
		$infopasienlab_v = file_get_contents(__DIR__ . '/definitions/infopasienlab_v.sql');
		$this->execute($infopasienlab_v);
		
		$laporanpasienlabkasir_v = file_get_contents(__DIR__ . '/definitions/laporanpasienlabkasir_v.sql');
		$this->execute($laporanpasienlabkasir_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240511_091946_migrate_hotfix_infopasienlab_v_from_ngv cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240511_091946_migrate_hotfix_infopasienlab_v_from_ngv cannot be reverted.\n";

        return false;
    }
    */
}
