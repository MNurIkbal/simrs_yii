<?php

use yii\db\Migration;

/**
 * Class m231030_053354_migrate_dsv776_sy_kunjungan
 */
class m231030_053354_migrate_dsv776_sy_kunjungan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE sy_kunjungan ADD COLUMN IF NOT EXISTS info_response_bpjs text NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231030_053354_migrate_dsv776_sy_kunjungan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231030_053354_migrate_dsv776_sy_kunjungan cannot be reverted.\n";

        return false;
    }
    */
}
