<?php

use yii\db\Migration;

/**
 * Class m240405_085542_pcp_10_informasiresep_kelaspelayanan
 */
class m240405_085542_pcp_10_informasiresep_kelaspelayanan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS informasiresep_v");
        $informasiresep_v = file_get_contents(__DIR__ . '/definitions/informasiresep_v.view.sql');
        $this->execute($informasiresep_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240405_085542_pcp_10_informasiresep_kelaspelayanan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240405_085542_pcp_10_informasiresep_kelaspelayanan cannot be reverted.\n";

        return false;
    }
    */
}
