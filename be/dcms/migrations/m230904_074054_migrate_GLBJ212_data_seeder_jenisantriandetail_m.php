<?php

use yii\db\Migration;

/**
 * Class m230904_074054_migrate_GLBJ212_data_seeder_jenisantriandetail_m
 */
class m230904_074054_migrate_GLBJ212_data_seeder_jenisantriandetail_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM jenisantriandetail_m
            WHERE jenisantrian_id = 176;
        '); 

        $this->execute("
            INSERT INTO jenisantriandetail_m(jenisantrian_id, jenisantrian_nama, nama)
            VALUES(
                176, 'Farmasi','Farmasi'
            );    
        "); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230904_074054_migrate_GLBJ212_data_seeder_jenisantriandetail_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230904_074054_migrate_GLBJ212_data_seeder_jenisantriandetail_m cannot be reverted.\n";

        return false;
    }
    */
}
