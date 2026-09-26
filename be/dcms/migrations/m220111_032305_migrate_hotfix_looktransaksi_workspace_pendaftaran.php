<?php

use yii\db\Migration;

/**
 * Class m220111_032305_migrate_hotfix_looktransaksi_workspace_pendaftaran
 */
class m220111_032305_migrate_hotfix_looktransaksi_workspace_pendaftaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'workspace_pendaftaran';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES 
            ('workspace_pendaftaran', 10, 'ruangan pendaftaran igd', 'igd', 'pendaftaran igd', 'IGD'),
            ('workspace_pendaftaran', 5, 'ruangan pendaftaran rajal', 'rajal', 'pendaftaran rajal', 'RJ'),
            ('workspace_pendaftaran', 11, 'ruangan pendaftaran ranap', 'ranap', 'pendaftaran ranap', 'RI'),
            ('workspace_pendaftaran', 74, 'ruangan pendaftaran mcu', 'mcu', 'pendaftaran mcu', 'MCU'),
            ('workspace_pendaftaran', 28, 'ruangan pendaftaran penunjang', 'penunjang', 'pendaftaran penunjang', 'RJ');

        ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220111_032305_migrate_hotfix_looktransaksi_workspace_pendaftaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220111_032305_migrate_hotfix_looktransaksi_workspace_pendaftaran cannot be reverted.\n";

        return false;
    }
    */
}
