<?php

use yii\db\Migration;

/**
 * Class m231107_114401_antrianpolikonsul_infopasienrs
 */
class m231107_114401_antrianpolikonsul_infopasienrs extends Migration
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
        echo "m231107_114401_antrianpolikonsul_infopasienrs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231107_114401_antrianpolikonsul_infopasienrs cannot be reverted.\n";

        return false;
    }
    */
}
