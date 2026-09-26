<?php

use yii\db\Migration;

/**
 * Class m241218_065552_rpp_1980_infopasienrs_v_penambahan_kondisi_rencanakontrol_id_ranap
 */
class m241218_065552_rpp_1980_infopasienrs_v_penambahan_kondisi_rencanakontrol_id_ranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienrs_v");
        $infopasienrs_v = file_get_contents(__DIR__ . '/definitions/infopasienrs_v.sql');
        $this->execute($infopasienrs_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241218_065552_rpp_1980_infopasienrs_v_penambahan_kondisi_rencanakontrol_id_ranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241218_065552_rpp_1980_infopasienrs_v_penambahan_kondisi_rencanakontrol_id_ranap cannot be reverted.\n";

        return false;
    }
    */
}
