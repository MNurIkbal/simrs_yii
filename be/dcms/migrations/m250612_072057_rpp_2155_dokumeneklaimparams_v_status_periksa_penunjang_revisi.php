<?php

use yii\db\Migration;

/**
 * Class m250612_072057_rpp_2155_dokumeneklaimparams_v_status_periksa_penunjang_revisi
 */
class m250612_072057_rpp_2155_dokumeneklaimparams_v_status_periksa_penunjang_revisi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS dokumeneklaimparams_v");
        $dokumeneklaimparams_v = file_get_contents(__DIR__ . '/definitions/dokumeneklaimparams_v_improve_status_periksa_penunjang.sql');
        $this->execute($dokumeneklaimparams_v);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250612_072057_rpp_2155_dokumeneklaimparams_v_status_periksa_penunjang_revisi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250612_072057_rpp_2155_dokumeneklaimparams_v_status_periksa_penunjang_revisi cannot be reverted.\n";

        return false;
    }
    */
}
