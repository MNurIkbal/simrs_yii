<?php

use yii\db\Migration;

/**
 * Class m240605_060431_DSV_1307_update_view_sy_kunjungan_v
 */
class m240605_060431_DSV_1307_update_view_sy_kunjungan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_kunjungan_v");
        $sy_kunjungan_v = file_get_contents(__DIR__ . '/definitions/sy_kunjungan_v.sql');
        $this->execute($sy_kunjungan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240605_060431_DSV_1307_update_view_sy_kunjungan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240605_060431_DSV_1307_update_view_sy_kunjungan_v cannot be reverted.\n";

        return false;
    }
    */
}
