<?php

use yii\db\Migration;

/**
 * Class m230807_092917_sy_infopasienbpjsdiagnosa_v
 */
class m230807_092917_sy_infopasienbpjsdiagnosa_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_infopasienbpjsdiagnosa_v");
        $sy_infopasienbpjsdiagnosa_v = file_get_contents(__DIR__ . '/definitions/sy_infopasienbpjsdiagnosa_v.sql');
        $this->execute($sy_infopasienbpjsdiagnosa_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230807_092917_sy_infopasienbpjsdiagnosa_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230807_092917_sy_infopasienbpjsdiagnosa_v cannot be reverted.\n";

        return false;
    }
    */
}
