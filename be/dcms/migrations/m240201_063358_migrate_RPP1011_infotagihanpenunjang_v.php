<?php

use yii\db\Migration;

/**
 * Class m240201_063358_migrate_RPP1011_infotagihanpenunjang_v
 */
class m240201_063358_migrate_RPP1011_infotagihanpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infotagihanpenunjang_v");
        $infotagihanpenunjang_v = file_get_contents(__DIR__ . '/definitions/infotagihanpenunjang_v.sql');
        $this->execute($infotagihanpenunjang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240201_063358_migrate_RPP1011_infotagihanpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240201_063358_migrate_RPP1011_infotagihanpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
