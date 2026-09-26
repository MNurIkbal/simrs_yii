<?php

use yii\db\Migration;

/**
 * Class m231120_090132_rpp_901_estimasi_pendaftaran_fn
 */
class m231120_090132_rpp_901_estimasi_pendaftaran_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $pendaftaran_t = file_get_contents(__DIR__ . '/definitions/pendaftaran_t.fn.sql');
        $this->execute($pendaftaran_t);

        $pendaftaranol_t = file_get_contents(__DIR__ . '/definitions/pendaftaranol_t.fn.sql');
        $this->execute($pendaftaranol_t);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231120_090132_rpp_901_estimasi_pendaftaran_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231120_090132_rpp_901_estimasi_pendaftaran_fn cannot be reverted.\n";

        return false;
    }
    */
}
