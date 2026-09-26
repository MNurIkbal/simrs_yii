<?php

use yii\db\Migration;

/**
 * Class m250729_074215_GLBJ781_infopasienoperasi_v
 */
class m250729_074215_GLBJ781_infopasienoperasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienoperasi_v");
        $infopasienoperasi_v = file_get_contents(__DIR__ . '/definitions/infopasienoperasi_v.sql');
        $this->execute($infopasienoperasi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250729_074215_GLBJ781_infopasienoperasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250729_074215_GLBJ781_infopasienoperasi_v cannot be reverted.\n";

        return false;
    }
    */
}
