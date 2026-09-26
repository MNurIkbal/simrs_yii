<?php

use yii\db\Migration;

/**
 * Class m220714_084933_migrate_MHG2695_table_kesimpuannrs_m
 */
class m220714_084933_migrate_MHG2695_table_kesimpuannrs_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE kesimpuannrs_m ADD IF NOT EXISTS is_anak BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220714_084933_migrate_MHG2695_table_kesimpuannrs_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220714_084933_migrate_MHG2695_table_kesimpuannrs_m cannot be reverted.\n";

        return false;
    }
    */
}
