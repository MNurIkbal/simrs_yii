<?php

use yii\db\Migration;

/**
 * Class m231124_064111_add_fn_hargaobatalkesfn_infostokobatalkesfnnew
 */
class m231124_064111_add_fn_hargaobatalkesfn_infostokobatalkesfnnew extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $hargaobatalkes_fn = file_get_contents(__DIR__ . '/definitions/hargaobatalkes_fn.fn.sql');
        $this->execute($hargaobatalkes_fn);

        $infostokobatalkes_fnr_new = file_get_contents(__DIR__ . '/definitions/infostokobatalkes_fnr_new.fn.sql');
        $this->execute($infostokobatalkes_fnr_new);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231124_064111_add_fn_hargaobatalkesfn_infostokobatalkesfnnew cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231124_064111_add_fn_hargaobatalkesfn_infostokobatalkesfnnew cannot be reverted.\n";

        return false;
    }
    */
}
