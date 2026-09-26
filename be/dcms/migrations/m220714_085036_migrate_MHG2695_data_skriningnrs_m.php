<?php

use yii\db\Migration;

/**
 * Class m220714_085036_migrate_MHG2695_data_skriningnrs_m
 */
class m220714_085036_migrate_MHG2695_data_skriningnrs_m extends Migration
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
            INSERT INTO "skriningnrs_m" ("skriningnrs_nama", "skor", "jenisskrining_id") VALUES (\'Status Gizi Normal\', \'0\', 1211),
            (\'Kehilangan BB > 5% dalam 2 bulan atau IMT 18.5 - 20.5, atau asupan 25-50% dari kebutuhan\', \'2\', 1211),
            (\'Kehilangan BB > 5% dalam 1 bulan (15% dalam 3 bulan) atau IMT 15.5 atau asupan 0-25% dari kebutuhan\', \'3\', 1211),
            (\'Kebutuhan gizi normal\', \'0\', 1212),
            (\'Fraktur, pasien kronik (sirosisi hati, COPD, HD rutin, diabetes, kanker)\', \'1\', 1212),
            (\'Mayor bedah, stroke, pneumia berat, kanker darah\', \'2\', 1212),
            (\'Cidera kepala, transplantasi sumsum, pasien ICU\', \'3\', 1212),
            (\'Nafsu Makan\', \'0\', 1233),
            (\'Nafsu Makan\', \'2\', 1233),
            (\'Nafsu Makan \', \'3\', 1233),
            (\'Kemampuan untuk makan\', \'0\', 1234),
            (\'Kemampuan untuk makan\', \'1\', 1234),
            (\'Kemampuan untuk makan\', \'2\', 1234),
            (\'Kemampuan untuk makan\', \'3\', 1234),
            (\'Faktor stress\', \'0\', 1235),
            (\'Faktor stress\', \'1\', 1235),
            (\'Faktor stress\', \'2\', 1235),
            (\'Faktor stress\', \'3\', 1235),
            (\'Persentil berat badan\', \'0\', 1236),
            (\'Persentil berat badan\', \'1\', 1236),
            (\'Persentil berat badan\', \'2\', 1236),
            (\'Persentil berat badan\', \'3\', 1236);

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220714_085036_migrate_MHG2695_data_skriningnrs_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220714_085036_migrate_MHG2695_data_skriningnrs_m cannot be reverted.\n";

        return false;
    }
    */
}
