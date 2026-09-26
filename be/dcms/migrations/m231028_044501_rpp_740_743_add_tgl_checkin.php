<?php

use yii\db\Migration;

/**
 * Class m231028_044501_rpp_740_743_add_tgl_checkin
 */
class m231028_044501_rpp_740_743_add_tgl_checkin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE pendaftaranol_t ADD COLUMN IF NOT EXISTS is_checkin bool NULL DEFAULT false");

        $this->execute("ALTER TABLE pendaftaranol_t ADD COLUMN IF NOT EXISTS tgl_checkin timestamp NULL");

        $this->execute("ALTER TABLE antrianjkn_r ADD COLUMN IF NOT EXISTS tgl_checkin timestamp NULL");

        $antrianjkn_r_ins = file_get_contents(__DIR__ . '/definitions/antrianjkn_r_ins.fn.sql');
        $this->execute($antrianjkn_r_ins);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231028_044501_rpp_740_743_add_tgl_checkin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231028_044501_rpp_740_743_add_tgl_checkin cannot be reverted.\n";

        return false;
    }
    */
}
