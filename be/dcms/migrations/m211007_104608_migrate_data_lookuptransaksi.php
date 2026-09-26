<?php

use yii\db\Migration;

/**
 * Class m211007_104608_migrate_data_lookuptransaksi
 */
class m211007_104608_migrate_data_lookuptransaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from lookuptransaksi_m WHERE kode_transaksi in (\'farmasi_utama\',\'gudang_farmasi\')');
        
        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES 
            ('farmasi_utama', 6, 'Ruangan utama farmasi', NULL),
            ('gudang_farmasi', 25, 'Ruangan gudang farmasi', NULL);");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211007_104608_migrate_data_lookuptransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211007_104608_migrate_data_lookuptransaksi cannot be reverted.\n";

        return false;
    }
    */
}
