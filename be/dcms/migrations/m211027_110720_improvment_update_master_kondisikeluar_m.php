<?php

use yii\db\Migration;

/**
 * Class m211027_110720_improvment_update_master_kondisikeluar_m
 */
class m211027_110720_improvment_update_master_kondisikeluar_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            UPDATE kondisikeluar_m
            SET kondisikeluar_nama = REPLACE(kondisikeluar_nama, \'SEMBUH\', \'PERBAIKAN\'),
                kondisikeluar_namalain = REPLACE(kondisikeluar_namalain, \'SEMBUH\', 
                \'PERBAIKAN\')
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211027_110720_improvment_update_master_kondisikeluar_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211027_110720_improvment_update_master_kondisikeluar_m cannot be reverted.\n";

        return false;
    }
    */
}
