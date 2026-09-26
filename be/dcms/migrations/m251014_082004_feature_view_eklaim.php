<?php

use yii\db\Migration;

/**
 * Class m251014_082004_feature_view_eklaim
 */
class m251014_082004_feature_view_eklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_infopasienbpjsklaimlist_v");
        $sy_infopasienbpjsklaimlist_v= file_get_contents(__DIR__ . '/definitions/sy_infopasienbpjsklaimlist_v.sql');
        $this->execute($sy_infopasienbpjsklaimlist_v);

        $this->execute("DROP VIEW IF EXISTS diagnosa_v");
        $diagnosa_v = file_get_contents(__DIR__ . '/definitions/diagnosa_v.sql');
        $this->execute($diagnosa_v);

        $this->execute("DROP VIEW IF EXISTS sy_infopasienbpjsdiagnosa_v");
        $sy_infopasienbpjsdiagnosa_v = file_get_contents(__DIR__ . '/definitions/sy_infopasienbpjsdiagnosa_v.sql');
        $this->execute($sy_infopasienbpjsdiagnosa_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251014_082004_feature_view_eklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251014_082004_feature_view_eklaim cannot be reverted.\n";

        return false;
    }
    */
}
