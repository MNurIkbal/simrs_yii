<?php

use yii\db\Migration;

/**
 * Class m240421_082024_fix_depending_detailreferraltagihan_v
 */
class m240326_000000_fix_depending_detailreferraltagihan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS detailreferraltagihan_v");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240421_082024_fix_depending_detailreferraltagihan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240421_082024_fix_depending_detailreferraltagihan_v cannot be reverted.\n";

        return false;
    }
    */
}
