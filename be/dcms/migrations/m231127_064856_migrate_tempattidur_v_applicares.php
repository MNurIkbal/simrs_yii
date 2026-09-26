<?php

use yii\db\Migration;

/**
 * Class m231127_064856_migrate_tempattidur_v_applicares
 */
class m231127_064856_migrate_tempattidur_v_applicares extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS tempattidur_v");
        $tempattidur_v = file_get_contents(__DIR__ . '/definitions/tempattidur_v.sql');
        $this->execute($tempattidur_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231127_064856_migrate_tempattidur_v_applicares cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_064856_migrate_tempattidur_v_applicares cannot be reverted.\n";

        return false;
    }
    */
}
