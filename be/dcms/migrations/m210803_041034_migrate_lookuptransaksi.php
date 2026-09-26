<?php

use yii\db\Migration;

/**
 * Class m210803_041034_migrate_lookuptransaksi
 */
class m210803_041034_migrate_lookuptransaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    $this->execute('DELETE FROM lookuptransaksi_m WHERE kode_transaksi=\'jenisobat_narkotika\';');

    $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('jenisobat_narkotika', 71, 'jenisobatalkes_id narkotika', NULL);");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210803_041034_migrate_lookuptransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210803_041034_migrate_lookuptransaksi cannot be reverted.\n";

        return false;
    }
    */
}
