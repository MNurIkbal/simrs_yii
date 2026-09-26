<?php

use yii\db\Migration;

/**
 * Class m241218_065525_rpp_1980_infopasienri_v_penambahan_kondisi_sep_ranap
 */
class m241218_065525_rpp_1980_infopasienri_v_penambahan_kondisi_sep_ranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienri_v");
        $infopasienri_v = file_get_contents(__DIR__ . '/definitions/infopasienri_v.sql');
        $this->execute($infopasienri_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241218_065525_rpp_1980_infopasienri_v_penambahan_kondisi_sep_ranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241218_065525_rpp_1980_infopasienri_v_penambahan_kondisi_sep_ranap cannot be reverted.\n";

        return false;
    }
    */
}
