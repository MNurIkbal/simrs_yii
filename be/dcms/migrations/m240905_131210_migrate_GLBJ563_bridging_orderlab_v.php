<?php

use yii\db\Migration;

/**
 * Class m240905_131210_migrate_GLBJ563_bridging_orderlab_v
 */
class m240905_131210_migrate_GLBJ563_bridging_orderlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS bridging_orderlab_v");
        $bridging_orderlab_v = file_get_contents(__DIR__ . '/definitions/bridging_orderlab_v.sql');
        $this->execute($bridging_orderlab_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240905_131210_migrate_GLBJ563_bridging_orderlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240905_131210_migrate_GLBJ563_bridging_orderlab_v cannot be reverted.\n";

        return false;
    }
    */
}
