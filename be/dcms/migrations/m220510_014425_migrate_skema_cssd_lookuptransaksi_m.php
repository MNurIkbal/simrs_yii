<?php

use yii\db\Migration;

/**
 * Class m220510_014425_migrate_skema_cssd_lookuptransaksi_m
 */
class m220510_014425_migrate_skema_cssd_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'ruangan_cssd';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('ruangan_cssd', 167, 'Ruangan CSSD', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'instalasi_cssd';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('instalasi_cssd', 70, 'Instalasi CSSD', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'status_pengajuan_cssd';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('status_pengajuan_cssd', 1190, 'Status ID pengajuan sterilisasi CSSD', NULL, NULL, NULL);
            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220510_014425_migrate_skema_cssd_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220510_014425_migrate_skema_cssd_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
