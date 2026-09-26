<?php

use yii\db\Migration;

/**
 * Class m250417_075323_rpp_2102_infoorderanlab_v_no_telepon_pasien
 */
class m250417_075323_rpp_2102_infoorderanlab_v_no_telepon_pasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infoorderanlab_v");
        $infoorderanlab_v = file_get_contents(__DIR__ . '/definitions/infoorderanlab_v.sql');
        $this->execute($infoorderanlab_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250417_075323_rpp_2102_infoorderanlab_v_no_telepon_pasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250417_075323_rpp_2102_infoorderanlab_v_no_telepon_pasien cannot be reverted.\n";

        return false;
    }
    */
}
