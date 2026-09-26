<?php

use yii\db\Migration;

/**
 * Class m250911_055704_rpp_2235_laporanpemeriksaanradiologi_v
 */
class m250911_055704_rpp_2235_laporanpemeriksaanradiologi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpemeriksaanradiologi_v");
        $laporanpemeriksaanradiologi_v = file_get_contents(__DIR__ . '/definitions/laporanpemeriksaanradiologi_v.sql');
        $this->execute($laporanpemeriksaanradiologi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250911_055704_rpp_2235_laporanpemeriksaanradiologi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250911_055704_rpp_2235_laporanpemeriksaanradiologi_v cannot be reverted.\n";

        return false;
    }
    */
}
