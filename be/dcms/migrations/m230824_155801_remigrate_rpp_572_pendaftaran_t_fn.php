<?php

use yii\db\Migration;

/**
 * Class m230824_155801_remigrate_rpp_572_pendaftaran_t_fn
 */
class m230824_155801_remigrate_rpp_572_pendaftaran_t_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $get_sequence_pendaftaran_t = file_get_contents(__DIR__ . '/definitions/get_sequence_pendaftaran_t.fn.sql');
        $this->execute($get_sequence_pendaftaran_t);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230824_155801_remigrate_rpp_572_pendaftaran_t_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230824_155801_remigrate_rpp_572_pendaftaran_t_fn cannot be reverted.\n";

        return false;
    }
    */
}
