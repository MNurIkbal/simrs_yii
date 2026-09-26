<?php

use yii\db\Migration;

/**
 * Class m231029_071818_rpp_742_antrian_perusahaan_create_poli
 */
class m231029_071818_rpp_742_antrian_perusahaan_create_poli extends Migration
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
        echo "m231029_071818_rpp_742_antrian_perusahaan_create_poli cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231029_071818_rpp_742_antrian_perusahaan_create_poli cannot be reverted.\n";

        return false;
    }
    */
}
