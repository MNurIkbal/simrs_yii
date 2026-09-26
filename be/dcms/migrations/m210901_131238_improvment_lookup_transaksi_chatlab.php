<?php

use yii\db\Migration;

/**
 * Class m210901_131238_improvment_lookup_transaksi_chatlab
 */
class m210901_131238_improvment_lookup_transaksi_chatlab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'kdtindakan_cathlab\';    
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value") VALUES (\'kdtindakan_cathlab\', 34, \'kode kelompok tindakan cathlab\', NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210901_131238_improvment_lookup_transaksi_chatlab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210901_131238_improvment_lookup_transaksi_chatlab cannot be reverted.\n";

        return false;
    }
    */
}
