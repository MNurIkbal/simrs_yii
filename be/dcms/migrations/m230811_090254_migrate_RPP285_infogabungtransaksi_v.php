<?php

use yii\db\Migration;

/**
 * Class m230811_090254_migrate_RPP285_infogabungtransaksi_v
 */
class m230811_090254_migrate_RPP285_infogabungtransaksi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infogabungtransaksi_v");
        $infogabungtransaksi_v = file_get_contents(__DIR__ . '/definitions/infogabungtransaksi_v.sql');
        $this->execute($infogabungtransaksi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230811_090254_migrate_RPP285_infogabungtransaksi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230811_090254_migrate_RPP285_infogabungtransaksi_v cannot be reverted.\n";

        return false;
    }
    */
}
