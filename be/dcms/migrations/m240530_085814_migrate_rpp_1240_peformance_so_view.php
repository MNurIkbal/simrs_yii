<?php

use yii\db\Migration;

/**
 * Class m240530_085814_migrate_rpp_1240_peformance_so_view
 */
class m240530_085814_migrate_rpp_1240_peformance_so_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute("DROP VIEW IF EXISTS infostokopname_v");
		$infostokopname_v = file_get_contents(__DIR__ . '/definitions/infostokopname_v.sql');
		$this->execute($infostokopname_v);
		
		
		$this->execute("DROP VIEW IF EXISTS infostokopnamedetail_v");
		$infostokopnamedetail_v = file_get_contents(__DIR__ . '/definitions/infostokopnamedetail_v.sql');
		$this->execute($infostokopnamedetail_v);
		
		
		$this->execute("DROP VIEW IF EXISTS laporanhasilso_v");
		$laporanhasilso_v = file_get_contents(__DIR__ . '/definitions/laporanhasilso_v.sql');
		$this->execute($laporanhasilso_v);
		
		
		$this->execute("DROP VIEW IF EXISTS infostokobatrakdetail_v");
		$infostokobatrakdetail_v = file_get_contents(__DIR__ . '/definitions/infostokobatrakdetail_v.sql');
		$this->execute($infostokobatrakdetail_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240530_085814_migrate_rpp_1240_peformance_so_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240530_085814_migrate_rpp_1240_peformance_so_view cannot be reverted.\n";

        return false;
    }
    */
}
