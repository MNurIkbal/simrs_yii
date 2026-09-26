<?php

use yii\db\Migration;

/**
 * Class m230614_031029_RPP_161_add_value_status_pulang_on_lookup_transaksi
 */
class m230614_031029_RPP_161_add_value_status_pulang_on_lookup_transaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            INSERT INTO lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES('cara_pulang_bpjs', '0', 'id cara pulang dh : key dari bpjs', '{\"1\":\"1\",\"2\":\"3\",\"4\":\"4\"}', '[NULL]','[NULL]')
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230614_031029_RPP_161_add_value_status_pulang_on_lookup_transaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230614_031029_RPP_161_add_value_status_pulang_on_lookup_transaksi cannot be reverted.\n";

        return false;
    }
    */
}
