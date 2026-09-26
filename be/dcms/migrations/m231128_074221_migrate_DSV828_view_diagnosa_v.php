<?php

use yii\db\Migration;

/**
 * Class m231128_074221_migrate_DSV828_view_diagnosa_v
 */
class m231128_074221_migrate_DSV828_view_diagnosa_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS diagnosa_v");
        $diagnosa_v = file_get_contents(__DIR__ . '/definitions/diagnosa_v.sql');
        $this->execute($diagnosa_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231128_074221_migrate_DSV828_view_diagnosa_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231128_074221_migrate_DSV828_view_diagnosa_v cannot be reverted.\n";

        return false;
    }
    */
}
