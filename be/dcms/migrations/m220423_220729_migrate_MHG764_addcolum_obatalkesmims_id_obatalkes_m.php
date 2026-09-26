<?php

use yii\db\Migration;

/**
 * Class m220423_220729_migrate_MHG764_addcolum_obatalkesmims_id_obatalkes_m
 */
class m220423_220729_migrate_MHG764_addcolum_obatalkesmims_id_obatalkes_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE obatalkes_m ADD IF NOT EXISTS obatalkesmims_id int4;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220423_220729_migrate_MHG764_addcolum_obatalkesmims_id_obatalkes_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220423_220729_migrate_MHG764_addcolum_obatalkesmims_id_obatalkes_m cannot be reverted.\n";

        return false;
    }
    */
}
