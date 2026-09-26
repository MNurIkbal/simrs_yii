<?php

use yii\db\Migration;

/**
 * Class m231023_110118_glbj_294_pendaftaran_t_function
 */
class m231023_110118_glbj_294_pendaftaran_t_function extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $pendaftaran_t = file_get_contents(__DIR__ . '/definitions/pendaftaran_t.fn.sql');
        $this->execute($pendaftaran_t);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231023_110118_glbj_294_pendaftaran_t_function cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231023_110118_glbj_294_pendaftaran_t_function cannot be reverted.\n";

        return false;
    }
    */
}
