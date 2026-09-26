<?php

use yii\db\Migration;

/**
 * Class m241114_033955_migrate_DSV1498_lookuptransaksi_m
 */
class m241114_033955_migrate_DSV1498_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m
                        WHERE kode_transaksi IN (
                        'ruangan_spesialis_tht',
                        'ruangan_spesialis_mata',
                        'ruangan_spesialis_gigi',
                        'ruangan_spesialis_obgyn'
                        );");
        $this->execute("INSERT INTO \"public\".\"lookuptransaksi_m\" (\"kode_transaksi\", \"kode_id\", \"kode_fungsi\", \"additional_value\", \"kode_nama\", \"kode_singkatan\") VALUES ('ruangan_spesialis_tht', 321, 'ruangan spesialis tht', NULL, NULL, 'SP'),
('ruangan_spesialis_mata', 313, 'ruangan spesialis mata', NULL, NULL, 'SP'),
('ruangan_spesialis_gigi', 308, 'ruangan spesialis gigi', NULL, NULL, 'SP'),
('ruangan_spesialis_obgyn', 310, 'ruangan spesialis obgyn', NULL, NULL, 'SP');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241114_033955_migrate_DSV1498_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241114_033955_migrate_DSV1498_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
