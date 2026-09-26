<?php

use yii\db\Migration;

/**
 * Class m230829_155400_rpp_501_infopasienpenatajasa_v
 */
class m230829_155400_rpp_501_infopasienpenatajasa_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienpenatajasa_v");
        $infopasienpenatajasa_v = file_get_contents(__DIR__ . '/definitions/infopasienpenatajasa_v.sql');
        $this->execute($infopasienpenatajasa_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230829_155400_rpp_501_infopasienpenatajasa_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230829_155400_rpp_501_infopasienpenatajasa_v cannot be reverted.\n";

        return false;
    }
    */
}
