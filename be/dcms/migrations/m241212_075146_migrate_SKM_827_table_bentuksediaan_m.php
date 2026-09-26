<?php

use yii\db\Migration;

/**
 * Class m241212_075146_migrate_SKM_827_table_bentuksediaan_m
 */
class m241212_075146_migrate_SKM_827_table_bentuksediaan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $bentuksediaan_m = file_get_contents(__DIR__ . '/definitions/bentuksediaan_m.sql');
        $this->execute($bentuksediaan_m);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_075146_migrate_SKM_827_table_bentuksediaan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_075146_migrate_SKM_827_table_bentuksediaan_m cannot be reverted.\n";

        return false;
    }
    */
}
