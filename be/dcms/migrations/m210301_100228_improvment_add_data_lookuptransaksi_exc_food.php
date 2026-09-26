<?php

use yii\db\Migration;

/**
 * Class m210301_100228_improvment_add_data_lookuptransaksi_exc_food
 */
class m210301_100228_improvment_add_data_lookuptransaksi_exc_food extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi LIKE \'not_in_kelompoktindakan_m\';
        ');

        $this->execute('
            INSERT INTO lookuptransaksi_m(
                kode_transaksi, kode_id, kode_fungsi
            )VALUES(
                \'not_in_kelompoktindakan_m\',33, \'tidak menampilkan kategori makanan di tindakan rajal\'
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210301_100228_improvment_add_data_lookuptransaksi_exc_food cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210301_100228_improvment_add_data_lookuptransaksi_exc_food cannot be reverted.\n";

        return false;
    }
    */
}
