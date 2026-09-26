<?php

use yii\db\Migration;

/**
 * Class m220610_073117_migrate_MHG1808_data_skriningnrs_m
 */
class m220610_073117_migrate_MHG1808_data_skriningnrs_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE skriningnrs_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO skriningnrs_m ("skriningnrs_nama", "skor", "jenisskrining_id") VALUES 
            (\'Status Gizi Normal\', \'0\', 1211),
            (\'Kehilangan BB > 5% dalam 2 bulan atau IMT 18.5 - 20.5, atau asupan 25-50% dari kebutuhan\', \'2\', 1211),
            (\'Kehilangan BB > 5% dalam 1 bulan (15% dalam 3 bulan) atau IMT 15.5 atau asupan 0-25% dari kebutuhan\', \'3\', 1211),
            (\'Kebutuhan gizi normal\', \'0\', 1212),
            (\'Fraktur, pasien kronik (sirosisi hati, COPD, HD rutin, diabetes, kanker)\', \'1\', 1212),
            (\'Mayor bedah, stroke, pneumia berat, kanker darah\', \'2\', 1212),
            (\'Cidera kepala, transplantasi sumsum, pasien ICU\', \'3\', 1212);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220610_073117_migrate_MHG1808_data_skriningnrs_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220610_073117_migrate_MHG1808_data_skriningnrs_m cannot be reverted.\n";

        return false;
    }
    */
}
