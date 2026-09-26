<?php

use yii\db\Migration;

/**
 * Class m231120_101319_rpp_901_bugfix_pendaftaran_t_fn
 */
class m231120_101319_rpp_901_bugfix_pendaftaran_t_fn extends Migration
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
        echo "m231120_101319_rpp_901_bugfix_pendaftaran_t_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231120_101319_rpp_901_bugfix_pendaftaran_t_fn cannot be reverted.\n";

        return false;
    }
    */
}
