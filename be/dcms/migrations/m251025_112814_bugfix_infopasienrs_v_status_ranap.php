<?php

use yii\db\Migration;

/**
 * Class m251025_112814_bugfix_infopasienrs_v_status_ranap
 */
class m251025_112814_bugfix_infopasienrs_v_status_ranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienrs_v");
        $infopasienrs_v = file_get_contents(__DIR__ . '/definitions/infopasienrs_v.sql');
        $this->execute($infopasienrs_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251025_112814_bugfix_infopasienrs_v_status_ranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251025_112814_bugfix_infopasienrs_v_status_ranap cannot be reverted.\n";

        return false;
    }
    */
}
