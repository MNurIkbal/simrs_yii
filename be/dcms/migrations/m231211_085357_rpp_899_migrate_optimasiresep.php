<?php

use yii\db\Migration;

/**
 * Class m231211_085357_rpp_899_migrate_optimasiresep
 */
class m231211_085357_rpp_899_migrate_optimasiresep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS prescribe_v");
        $prescribe_v = file_get_contents(__DIR__ . '/definitions/prescribe_v.view.sql');
        $this->execute($prescribe_v);

        $this->execute("DROP VIEW IF EXISTS headerresep_v");
        $headerresep_v = file_get_contents(__DIR__ . '/definitions/headerresep_v.view.sql');
        $this->execute($headerresep_v);

        $this->execute("DROP VIEW IF EXISTS headeretiket_v");
        $headeretiket_v = file_get_contents(__DIR__ . '/definitions/headeretiket_v.view.sql');
        $this->execute($headeretiket_v);

        $this->execute("DROP VIEW IF EXISTS informasiresep_v");
        $informasiresep_v = file_get_contents(__DIR__ . '/definitions/informasiresep_v.view.sql');
        $this->execute($informasiresep_v);

        $this->execute("DROP VIEW IF EXISTS informasiresepdetail_v");
        $informasiresepdetail_v = file_get_contents(__DIR__ . '/definitions/informasiresepdetail_v.view.sql');
        $this->execute($informasiresepdetail_v);

        $this->execute("DROP VIEW IF EXISTS detailcetakresep_v");
        $detailcetakresep_v = file_get_contents(__DIR__ . '/definitions/detailcetakresep_v.view.sql');
        $this->execute($detailcetakresep_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231211_085357_rpp_899_migrate_optimasiresep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231211_085357_rpp_899_migrate_optimasiresep cannot be reverted.\n";

        return false;
    }
    */
}
