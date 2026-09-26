<?php

use yii\db\Migration;

/**
 * Class m210128_023356_improvment_add_data_lookup_transaksi
 */
class m210128_023356_improvment_add_data_lookup_transaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m WHERE kode_transaksi = \'VisitDokterSpesialis\';
        ');

        $this->execute('
            INSERT INTO lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi)
            VALUES(\'VisitDokterSpesialis\',\'0\',\'Tindakan Visit Dokter Spesialis\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210128_023356_improvment_add_data_lookup_transaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210128_023356_improvment_add_data_lookup_transaksi cannot be reverted.\n";

        return false;
    }
    */
}
