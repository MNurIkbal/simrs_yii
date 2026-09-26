<?php

use yii\db\Migration;

/**
 * Class m230807_092741_koreksidiagnosa_new_v
 */
class m230807_092741_koreksidiagnosa_new_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS koreksidiagnosa_new_v");
        $koreksidiagnosa_new_v = file_get_contents(__DIR__ . '/definitions/koreksidiagnosa_new_v.sql');
        $this->execute($koreksidiagnosa_new_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230807_092741_koreksidiagnosa_new_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230807_092741_koreksidiagnosa_new_v cannot be reverted.\n";

        return false;
    }
    */
}
