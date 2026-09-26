<?php

use yii\db\Migration;

/**
 * Class m230823_051850_rpp_599_lookup_instalasieklaim
 */
class m230823_051850_rpp_599_lookup_instalasieklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'instalasi_eklaim';");
        $this->execute("
        INSERT INTO lookup_m (lookup_id ,lookup_type,lookup_name,lookup_value,lookup_urutan,lookup_kode) VALUES
        (2030,'instalasi_eklaim','Rawat Darurat','2',1,'RD'),
        (2031,'instalasi_eklaim','Rawat Jalan','1',2,'RJ'),
        (2032,'instalasi_eklaim','Rawat Inap','3',NULL,'RI'),
        (2033,'instalasi_eklaim','Rehabilitasi Medik','4',NULL,'REHAB');
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230823_051850_rpp_599_lookup_instalasieklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230823_051850_rpp_599_lookup_instalasieklaim cannot be reverted.\n";

        return false;
    }
    */
}
