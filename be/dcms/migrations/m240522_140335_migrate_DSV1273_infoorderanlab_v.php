<?php

use yii\db\Migration;

/**
 * Class m240522_140335_migrate_DSV1273_infoorderanlab_v
 */
class m240522_140335_migrate_DSV1273_infoorderanlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infoorderanlab_v");
        $infoorderanlab_v = file_get_contents(__DIR__ . '/definitions/infoorderanlab_v.sql');
        $this->execute($infoorderanlab_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240522_140335_migrate_DSV1273_infoorderanlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240522_140335_migrate_DSV1273_infoorderanlab_v cannot be reverted.\n";

        return false;
    }
    */
}
