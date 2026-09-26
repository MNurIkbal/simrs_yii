<?php

use yii\db\Migration;

/**
 * Class m240124_031312_gls_322_data_lookup_transaksi_eklaim_dokter_multiple
 */
class m240124_031312_gls_322_data_lookup_transaksi_eklaim_dokter_multiple extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from lookuptransaksi_m WHERE kode_transaksi in (\'eklaim_dokter_multiple\')');
        
        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES 
            ('eklaim_dokter_multiple', 0, 'true = dokter bisa freetext dan lebih dari 1, false = dokter hanya bisa 1', false);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240124_031312_gls_322_data_lookup_transaksi_eklaim_dokter_multiple cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240124_031312_gls_322_data_lookup_transaksi_eklaim_dokter_multiple cannot be reverted.\n";

        return false;
    }
    */
}
