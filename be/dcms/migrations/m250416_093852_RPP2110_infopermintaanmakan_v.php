<?php

use yii\db\Migration;

/**
 * Class m250416_093852_RPP2110_infopermintaanmakan_v
 */
class m250416_093852_RPP2110_infopermintaanmakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopermintaanmakan_v");
        $infopermintaanmakan_v = file_get_contents(__DIR__ . '/definitions/infopermintaanmakan_v.sql');
        $this->execute($infopermintaanmakan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250416_093852_RPP2110_infopermintaanmakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250416_093852_RPP2110_infopermintaanmakan_v cannot be reverted.\n";

        return false;
    }
    */
}
