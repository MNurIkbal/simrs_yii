<?php

use yii\db\Migration;

/**
 * Class m250728_093014_migrate_DSV_1940_improve_function
 */
class m250728_093014_migrate_DSV_1940_improve_function extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute("ALTER TABLE public.konfigmargin_k ADD COLUMN IF NOT EXISTS diskon float8");
		
		$this->execute("DROP function IF EXISTS infostokobatalkes_fnr_new");
		$infostokobatalkes_fnr_new = file_get_contents(__DIR__ . '/definitions/infostokobatalkes_fnr_new.sql');
		$this->execute($infostokobatalkes_fnr_new);
		
		$this->execute("DROP function IF EXISTS  hargaobatalkes_fn");
		$hargaobatalkes_fn = file_get_contents(__DIR__ . '/definitions/hargaobatalkes_fn.sql');
		$this->execute($hargaobatalkes_fn);
		
		$this->execute("DROP function IF EXISTS  sp_recalculate_tagihan");
		$sp_recalculate_tagihan = file_get_contents(__DIR__ . '/definitions/sp_recalculate_tagihan.sql');
		$this->execute($sp_recalculate_tagihan);
		    
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250728_093014_migrate_DSV_1940_improve_function cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250728_093014_migrate_DSV_1940_improve_function cannot be reverted.\n";

        return false;
    }
    */
}
