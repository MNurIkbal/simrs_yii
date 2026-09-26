<?php

use yii\db\Migration;

/**
 * Class m250918_021624_rpp_2240_laporansensusharianri_sedangrawatinap_v
 */
class m250918_021624_rpp_2240_laporansensusharianri_sedangrawatinap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporansensusharianri_sedangrawatinap_v");
        $laporansensusharianri_sedangrawatinap_v = file_get_contents(__DIR__ . '/definitions/laporansensusharianri_sedangrawatinap_v.sql');
        $this->execute($laporansensusharianri_sedangrawatinap_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250918_021624_rpp_2240_laporansensusharianri_sedangrawatinap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250918_021624_rpp_2240_laporansensusharianri_sedangrawatinap_v cannot be reverted.\n";

        return false;
    }
    */
}
