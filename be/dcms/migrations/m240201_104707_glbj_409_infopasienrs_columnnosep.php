<?php

use yii\db\Migration;

/**
 * Class m240201_104707_glbj_409_infopasienrs_columnnosep
 */
class m240201_104707_glbj_409_infopasienrs_columnnosep extends Migration
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
        echo "m240201_104707_glbj_409_infopasienrs_columnnosep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240201_104707_glbj_409_infopasienrs_columnnosep cannot be reverted.\n";

        return false;
    }
    */
}
