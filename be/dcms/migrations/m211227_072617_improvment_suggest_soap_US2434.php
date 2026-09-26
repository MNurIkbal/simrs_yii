<?php

use yii\db\Migration;

/**
 * Class m211227_072617_improvment_suggest_soap_US2434
 */
class m211227_072617_improvment_suggest_soap_US2434 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'reset_suggestion_soap\';
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'reset_suggestion_soap\', 0, \'untuk mengatur waktu untuk mereset local storage suggest soap format waktu berdasarkan menit additional value format penulisan
                [ RJ , RD, RI] 1 hari => 1440, 7 hari => 10080, 30 hari => 43200 \', \'[5,5,5]\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211227_072617_improvment_suggest_soap_US2434 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211227_072617_improvment_suggest_soap_US2434 cannot be reverted.\n";

        return false;
    }
    */
}
