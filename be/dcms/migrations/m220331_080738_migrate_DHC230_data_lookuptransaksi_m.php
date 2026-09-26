<?php

use yii\db\Migration;

/**
 * Class m220331_100738_migrate_DHC230_data_lookuptransaksi_m
 */
class m220331_080738_migrate_DHC230_data_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi IN (
                \'ruangan_mcu_rj\',
                \'ruangan_mcu\'
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES 
            (\'ruangan_mcu_rj\', 14, \'Ruangan POLI MCU RAJAL\', NULL, NULL, NULL),
            (\'ruangan_mcu\', 14, \'RUANGAN MCU\', NULL, NULL, NULL);
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220331_080738_migrate_DHC230_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220331_080738_migrate_DHC230_data_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
