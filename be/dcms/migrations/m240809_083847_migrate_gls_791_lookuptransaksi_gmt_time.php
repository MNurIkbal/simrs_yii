<?php

use yii\db\Migration;

/**
 * Class m240809_083847_migrate_gls_791_lookuptransaksi_gmt_time
 */
class m240809_083847_migrate_gls_791_lookuptransaksi_gmt_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from lookuptransaksi_m WHERE kode_transaksi in (\'gmt_time\')');
        
        $this->execute("INSERT INTO public.lookuptransaksi_m
            (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
            VALUES('gmt_time', 4948, 'GMT Time untuk Satu Sehat', '+08:00', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240809_083847_migrate_gls_791_lookuptransaksi_gmt_time cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240809_083847_migrate_gls_791_lookuptransaksi_gmt_time cannot be reverted.\n";

        return false;
    }
    */
}
