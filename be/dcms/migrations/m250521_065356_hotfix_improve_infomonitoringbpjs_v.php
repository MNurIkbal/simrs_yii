<?php

use yii\db\Migration;

/**
 * Class m250521_065356_hotfix_improve_infomonitoringbpjs_v
 */
class m250521_065356_hotfix_improve_infomonitoringbpjs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infomonitoringbpjs_v");
        $infomonitoringbpjs_v = file_get_contents(__DIR__ . '/definitions/improve_infomonitoringbpjs_v.sql');
        $this->execute($infomonitoringbpjs_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250521_065356_hotfix_improve_infomonitoringbpjs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250521_065356_hotfix_improve_infomonitoringbpjs_v cannot be reverted.\n";

        return false;
    }
    */
}
