<?php

use yii\db\Migration;

/**
 * Class m250718_102813_rpp_2202_infopasienlab_v_kondisi_pasien_mcu
 */
class m250718_102813_rpp_2202_infopasienlab_v_kondisi_pasien_mcu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienlab_v");
        $infopasienlab_v = file_get_contents(__DIR__ . '/definitions/infopasienlab_v.sql');
        $this->execute($infopasienlab_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250718_102813_rpp_2202_infopasienlab_v_kondisi_pasien_mcu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250718_102813_rpp_2202_infopasienlab_v_kondisi_pasien_mcu cannot be reverted.\n";

        return false;
    }
    */
}
