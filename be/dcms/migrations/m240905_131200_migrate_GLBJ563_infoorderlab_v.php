<?php

use yii\db\Migration;

/**
 * Class m240905_131200_migrate_GLBJ563_infoorderlab_v
 */
class m240905_131200_migrate_GLBJ563_infoorderlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infoorderanlab_v");
        $infoorderlab_v = file_get_contents(__DIR__ . '/definitions/infoorderanlab_v.sql');
        $this->execute($infoorderlab_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240905_131200_migrate_GLBJ563_infoorderlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240905_131200_migrate_GLBJ563_infoorderlab_v cannot be reverted.\n";

        return false;
    }
    */
}
