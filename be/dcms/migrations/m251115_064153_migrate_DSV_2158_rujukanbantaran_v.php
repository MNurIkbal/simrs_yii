<?php

use yii\db\Migration;

/**
 * Class m251115_064153_migrate_DSV_2158_rujukanbantaran_v
 */
class m251115_064153_migrate_DSV_2158_rujukanbantaran_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS rujukanbantaran_v;");
        $rujukanbantaran_v = file_get_contents(__DIR__ . '/definitions/rujukanbantaran_v.sql');
        $this->execute($rujukanbantaran_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251115_064153_migrate_DSV_2158_rujukanbantaran_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251115_064153_migrate_DSV_2158_rujukanbantaran_v cannot be reverted.\n";

        return false;
    }
    */
}
