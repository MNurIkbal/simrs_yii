<?php

use yii\db\Migration;

/**
 * Class m240426_143249_pcp48_alter_infopasiengizi_v
 */
class m240426_143249_pcp48_alter_infopasiengizi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasiengizi_v");
        $sql = file_get_contents(__DIR__ . '/definitions/infopasiengizi_v.view.sql');
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240426_143249_pcp48_alter_infopasiengizi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240426_143249_pcp48_alter_infopasiengizi_v cannot be reverted.\n";

        return false;
    }
    */
}
