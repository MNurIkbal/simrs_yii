<?php

use yii\db\Migration;

/**
 * Class m221219_075115_migrate_table_pasien_m
 */
class m221219_075115_migrate_table_pasien_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pasien_m ADD IF NOT EXISTS no_rm_old VARCHAR(30);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221219_075115_migrate_table_pasien_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221219_075115_migrate_table_pasien_m cannot be reverted.\n";

        return false;
    }
    */
}
