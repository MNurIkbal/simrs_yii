<?php

use yii\db\Migration;

/**
 * Class m241010_030534_migrate_impove_reseptempdetail_v
 */
class m241010_030534_migrate_impove_reseptempdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS reseptempdetail_v");
        $reseptempdetail_v = file_get_contents(__DIR__ . '/definitions/reseptempdetail_v.sql');
        $this->execute($reseptempdetail_v);
		
		$this->execute("ALTER TABLE public.reseptempdetail_m DROP COLUMN IF EXISTS satuankecil_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241010_030534_migrate_impove_reseptempdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241010_030534_migrate_impove_reseptempdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
