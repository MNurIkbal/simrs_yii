<?php

use yii\db\Migration;

/**
 * Class m231003_151249_rpp_605_tarif
 */
class m231003_151249_rpp_605_tarif extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tariftotalrs_fn = file_get_contents(__DIR__ . '/definitions/tariftotalrs_fn.function.sql');
        $this->execute($tariftotalrs_fn);

        $tarifkomponenrs_fn = file_get_contents(__DIR__ . '/definitions/tarifkomponenrs_fn.function.sql');
        $this->execute($tarifkomponenrs_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231003_151249_rpp_605_tarif cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231003_151249_rpp_605_tarif cannot be reverted.\n";

        return false;
    }
    */
}
