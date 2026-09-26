<?php

use yii\db\Migration;

/**
 * Class m230830_042016_migrate_GLBJ209_table_konfigantrian_m
 */
class m230830_042016_migrate_GLBJ209_table_konfigantrian_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigantrian_m ADD IF NOT EXISTS pegawai_id int4;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230830_042016_migrate_GLBJ209_table_konfigantrian_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230830_042016_migrate_GLBJ209_table_konfigantrian_m cannot be reverted.\n";

        return false;
    }
    */
}
