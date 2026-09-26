<?php

use yii\db\Migration;

/**
 * Class m231207_042659_migrate_dsv_1019_infopasienrd_v
 */
class m231207_042659_migrate_dsv_1019_infopasienrd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienrd_v");
        $infopasienrd_v = file_get_contents(__DIR__ . '/definitions/infopasienrd_v.sql');
        $this->execute($infopasienrd_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231207_042659_migrate_dsv_1019_infopasienrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231207_042659_migrate_dsv_1019_infopasienrd_v cannot be reverted.\n";

        return false;
    }
    */
}
