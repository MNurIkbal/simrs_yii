<?php

use yii\db\Migration;

/**
 * Class m230919_042037_rpp683_infopasienpenatajasa_v_rujukmuncul
 */
class m230919_042037_rpp683_infopasienpenatajasa_v_rujukmuncul extends Migration
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
        echo "m230919_042037_rpp683_infopasienpenatajasa_v_rujukmuncul cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230919_042037_rpp683_infopasienpenatajasa_v_rujukmuncul cannot be reverted.\n";

        return false;
    }
    */
}
