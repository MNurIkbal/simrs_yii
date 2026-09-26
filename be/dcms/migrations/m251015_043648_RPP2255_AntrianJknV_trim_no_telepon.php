<?php

use yii\db\Migration;

/**
 * Class m251015_043648_RPP2255_AntrianJknV_trim_no_telepon
 */
class m251015_043648_RPP2255_AntrianJknV_trim_no_telepon extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS antrianjkn_v');

        $antrianjkn_v = file_get_contents(__DIR__ . '/definitions/antrianjkn_v.sql');
        $this->execute($antrianjkn_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251015_043648_RPP2255_AntrianJknV_trim_no_telepon cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251015_043648_RPP2255_AntrianJknV_trim_no_telepon cannot be reverted.\n";

        return false;
    }
    */
}
