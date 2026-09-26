<?php

use yii\db\Migration;

/**
 * Class m240707_060430_rpp1510_fgetketersediaanobat
 */
class m240707_060430_rpp1510_fgetketersediaanobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $pendaftaran_t = file_get_contents(__DIR__ . '/definitions/fgetketersediaanobat.fn.sql');
        $this->execute($pendaftaran_t);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240707_060430_rpp1510_fgetketersediaanobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240707_060430_rpp1510_fgetketersediaanobat cannot be reverted.\n";

        return false;
    }
    */
}
