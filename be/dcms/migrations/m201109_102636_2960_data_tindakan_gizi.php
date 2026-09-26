<?php

use yii\db\Migration;

/**
 * Class m201109_102636_2960_data_tindakan_gizi
 */
class m201109_102636_2960_data_tindakan_gizi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'JasaAsuhanGizi\';
        '); 
        
        $this->execute('
            INSERT INTO lookuptransaksi_m(
                kode_transaksi, kode_id, kode_fungsi
            )VALUES 
            (\'JasaAsuhanGizi\', 0, \'kode tindakan jasa asuhan gizi, digunakan untuk pengambilan tarif jasa asuhan gizi\');
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201109_102636_2960_data_tindakan_gizi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201109_102636_2960_data_tindakan_gizi cannot be reverted.\n";

        return false;
    }
    */
}
