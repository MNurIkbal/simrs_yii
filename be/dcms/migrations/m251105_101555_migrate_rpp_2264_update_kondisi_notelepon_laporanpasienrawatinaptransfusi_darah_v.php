<?php

use yii\db\Migration;

/**
 * Class m251105_101555_migrate_rpp_2264_update_kondisi_notelepon_laporanpasienrawatinaptransfusi_darah_v
 */
class m251105_101555_migrate_rpp_2264_update_kondisi_notelepon_laporanpasienrawatinaptransfusi_darah_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpasienrawatinaptransfusidarah_v");
        $laporanpasienrawatinaptransfusidarah_v = file_get_contents(__DIR__ . '/definitions/laporanpasienrawatinaptransfusidarah_v.sql');
        $this->execute($laporanpasienrawatinaptransfusidarah_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251105_101555_migrate_rpp_2264_update_kondisi_notelepon_laporanpasienrawatinaptransfusi_darah_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251105_101555_migrate_rpp_2264_update_kondisi_notelepon_laporanpasienrawatinaptransfusi_darah_v cannot be reverted.\n";

        return false;
    }
    */
}
