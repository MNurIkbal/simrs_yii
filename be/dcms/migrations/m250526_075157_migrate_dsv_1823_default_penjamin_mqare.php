<?php

use yii\db\Migration;

/**
 * Class m250526_075157_migrate_dsv_1823_default_penjamin_mqare
 */
class m250526_075157_migrate_dsv_1823_default_penjamin_mqare extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute("DELETE FROM public.lookuptransaksi_m WHERE kode_transaksi='default_penjamin_mqare';");

        $this->execute("INSERT INTO public.lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES('default_penjamin_mqare', 599, 'Default Penjamin Mqare', NULL, NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250526_075157_migrate_dsv_1823_default_penjamin_mqare cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250526_075157_migrate_dsv_1823_default_penjamin_mqare cannot be reverted.\n";

        return false;
    }
    */
}
