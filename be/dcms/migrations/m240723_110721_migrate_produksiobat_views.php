<?php

use yii\db\Migration;

/**
 * Class m240723_110721_migrate_produksiobat_views
 */
class m240723_110721_migrate_produksiobat_views extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE obatalkes_m ADD IF NOT EXISTS is_produksi bool DEFAULT false;');
		
		$this->execute("DROP VIEW IF EXISTS infopemesananproduksiobat_v");
		$infopemesananproduksiobat_v = file_get_contents(__DIR__ . '/definitions/infopemesananproduksiobat_v.sql');
		$this->execute($infopemesananproduksiobat_v);
		
		$this->execute("DROP VIEW IF EXISTS infopemesananproduksiobatdetail_v");
		$infopemesananproduksiobatdetail_v = file_get_contents(__DIR__ . '/definitions/infopemesananproduksiobatdetail_v.sql');
		$this->execute($infopemesananproduksiobatdetail_v);
		
		$this->execute("DROP VIEW IF EXISTS obatalkes_v");
		$obatalkes_v = file_get_contents(__DIR__ . '/definitions/obatalkes_v.sql');
		$this->execute($obatalkes_v);
		
		$this->execute("DROP VIEW IF EXISTS infoproduksiobatalkes_v");
		$infoproduksiobatalkes_v = file_get_contents(__DIR__ . '/definitions/infoproduksiobatalkes_v.sql');
		$this->execute($infoproduksiobatalkes_v);
		
		$this->execute("DROP VIEW IF EXISTS infoproduksiobatalkesdetail_v");
		$infoproduksiobatalkesdetail_v = file_get_contents(__DIR__ . '/definitions/infoproduksiobatalkesdetail_v.sql');
		$this->execute($infoproduksiobatalkesdetail_v);
		
		$this->execute("DROP VIEW IF EXISTS infoproduksiobatalkesbahanbaku_v");
		$infoproduksiobatalkesbahanbaku_v = file_get_contents(__DIR__ . '/definitions/infoproduksiobatalkesbahanbaku_v.sql');
		$this->execute($infoproduksiobatalkesbahanbaku_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240723_110721_migrate_produksiobat_views cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240723_110721_migrate_produksiobat_views cannot be reverted.\n";

        return false;
    }
    */
}
