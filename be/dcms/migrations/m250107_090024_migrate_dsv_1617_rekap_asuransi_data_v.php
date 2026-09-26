<?php

use yii\db\Migration;

/**
 * Class m250107_090024_migrate_dsv_1617_rekap_asuransi_data_v
 */
class m250107_090024_migrate_dsv_1617_rekap_asuransi_data_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS rekap_asuransi_data_v");
        $rekap_asuransi_data_v = file_get_contents(__DIR__ . '/definitions/rekap_asuransi_data_v.sql');
        $this->execute($rekap_asuransi_data_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250107_090024_migrate_dsv_1617_rekap_asuransi_data_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250107_090024_migrate_dsv_1617_rekap_asuransi_data_v cannot be reverted.\n";

        return false;
    }
    */
}
