<?php

use yii\db\Migration;

/**
 * Class m240808_100958_antrianjkn_v
 */
class m240808_100958_antrianjkn_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS antrianjkn_v");
        $antrianjkn_v = file_get_contents(__DIR__ . '/definitions/antrianjkn_v.sql');
        $this->execute($antrianjkn_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240808_100958_antrianjkn_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240808_100958_antrianjkn_v cannot be reverted.\n";

        return false;
    }
    */
}
