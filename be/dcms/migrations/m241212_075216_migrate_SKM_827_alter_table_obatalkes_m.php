<?php

use yii\db\Migration;

/**
 * Class m241212_075216_migrate_SKM_827_alter_table_obatalkes_m
 */
class m241212_075216_migrate_SKM_827_alter_table_obatalkes_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.obatalkes_m ADD IF NOT EXISTS bentuksediaan_id int4;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_075216_migrate_SKM_827_alter_table_obatalkes_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_075216_migrate_SKM_827_alter_table_obatalkes_m cannot be reverted.\n";

        return false;
    }
    */
}
