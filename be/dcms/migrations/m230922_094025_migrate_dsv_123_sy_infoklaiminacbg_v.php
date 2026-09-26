<?php

use yii\db\Migration;

/**
 * Class m230922_094025_migrate_dsv_123_sy_infoklaiminacbg_v
 */
class m230922_094025_migrate_dsv_123_sy_infoklaiminacbg_v extends Migration
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
        echo "m230922_094025_migrate_dsv_123_sy_infoklaiminacbg_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230922_094025_migrate_dsv_123_sy_infoklaiminacbg_v cannot be reverted.\n";

        return false;
    }
    */
}
