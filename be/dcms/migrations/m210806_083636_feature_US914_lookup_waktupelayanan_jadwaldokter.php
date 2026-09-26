<?php

use yii\db\Migration;

/**
 * Class m210806_083636_feature_US914_lookup_waktupelayanan_jadwaldokter
 */
class m210806_083636_feature_US914_lookup_waktupelayanan_jadwaldokter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from lookup_m WHERE lookup_type =\'waktu_pelayanan\';');

        $this->execute("
            INSERT INTO \"public\".\"lookup_m\"(\"lookup_id\", \"lookup_type\", \"lookup_name\", \"lookup_value\", \"lookup_urutan\", \"lookup_kode\", \"additional_data\", \"created_date\", \"created_by\", \"modified_count\", \"last_modified_date\", \"last_modified_by\", \"is_deleted\", \"is_active\", \"deleted_date\", \"deleted_by\") VALUES 
            (1055, 'waktu_pelayanan', '60', '60', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1054, 'waktu_pelayanan', '55', '55', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1053, 'waktu_pelayanan', '50', '50', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1052, 'waktu_pelayanan', '45', '45', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1051, 'waktu_pelayanan', '40', '40', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1050, 'waktu_pelayanan', '35', '35', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1049, 'waktu_pelayanan', '30', '30', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1048, 'waktu_pelayanan', '25', '25', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1047, 'waktu_pelayanan', '20', '20', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1046, 'waktu_pelayanan', '15', '15', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1045, 'waktu_pelayanan', '10', '10', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1044, 'waktu_pelayanan', '5', '5', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1043, 'waktu_pelayanan', '1', '1', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210806_083636_feature_US914_lookup_waktupelayanan_jadwaldokter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210806_083636_feature_US914_lookup_waktupelayanan_jadwaldokter cannot be reverted.\n";

        return false;
    }
    */
}
