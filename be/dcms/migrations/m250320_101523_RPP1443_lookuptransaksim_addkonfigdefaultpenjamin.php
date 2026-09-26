<?php

use yii\db\Migration;

/**
 * Class m250320_101523_RPP1443_lookuptransaksim_addkonfigdefaultpenjamin
 */
class m250320_101523_RPP1443_lookuptransaksim_addkonfigdefaultpenjamin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookuptransaksi_m WHERE kode_transaksi='default_penjamin_by_carabayar';");

        $this->execute("INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi,additional_value,kode_nama,kode_singkatan) VALUES
	    ('default_penjamin_by_carabayar',1,'default penjamin by cara bayar ketika melakukan select2 carabayar, FORMAT: {\"carabayar_id\": \"penjamin_id\", \"carabayar_id\": \"penjamin_id\"}','{\"5\": \"1\", \"17\": \"1753\", \"2\": \"1550\"}',NULL,NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250320_101523_RPP1443_lookuptransaksim_addkonfigdefaultpenjamin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250320_101523_RPP1443_lookuptransaksim_addkonfigdefaultpenjamin cannot be reverted.\n";

        return false;
    }
    */
}
