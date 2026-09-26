<?php

use yii\db\Migration;

/**
 * Class m230919_091455_migrate_dsv467_sy_infoklaiminacbg_v
 */
class m230919_091455_migrate_dsv467_sy_infoklaiminacbg_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_infoklaiminacbg_v");
        $sy_infoklaiminacbg_v = file_get_contents(__DIR__ . '/definitions/sy_infoklaiminacbg_v.sql');
        $this->execute($sy_infoklaiminacbg_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230919_091455_migrate_dsv467_sy_infoklaiminacbg_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230919_091455_migrate_dsv467_sy_infoklaiminacbg_v cannot be reverted.\n";

        return false;
    }
    */
}
