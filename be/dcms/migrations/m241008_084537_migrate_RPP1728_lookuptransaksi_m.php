<?php

use yii\db\Migration;

/**
 * Class m241008_084537_migrate_RPP1728_lookuptransaksi_m
 */
class m241008_084537_migrate_RPP1728_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m where kode_transaksi = 'batal_hadir_reservasi'");
        $this->execute("
           INSERT INTO public.lookuptransaksi_m
            (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
            VALUES('batal_hadir_reservasi', 0, 'kode id  0 = batal hadir terdapat validasi (default sistem) hanya dapat batal dari hari ini sampai hari kebelakangnya. 1 = validasi batal dilepas dan dapat batal dari hari ini sampai hari berikutnya atau backdate', NULL, NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241008_084537_migrate_RPP1728_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241008_084537_migrate_RPP1728_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
