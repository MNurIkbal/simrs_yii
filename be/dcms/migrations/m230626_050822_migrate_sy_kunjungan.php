<?php

use yii\db\Migration;

/**
 * Class m230626_050822_migrate_sy_kunjungan
 */
class m230626_050822_migrate_sy_kunjungan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE sy_kunjungan 
            ADD IF NOT EXISTS jeniskasuspenyakit_id int4 NULL,
            ADD IF NOT EXISTS jeniskasuspenyakit_nama varchar(100) NULL,
            ADD IF NOT EXISTS instalasi_id int4 NULL,
            ADD IF NOT EXISTS ruangan_id int4 NULL;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230626_050822_migrate_sy_kunjungan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230626_050822_migrate_sy_kunjungan cannot be reverted.\n";

        return false;
    }
    */
}
