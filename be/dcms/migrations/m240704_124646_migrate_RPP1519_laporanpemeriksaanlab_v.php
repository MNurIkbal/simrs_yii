<?php

use yii\db\Migration;

/**
 * Class m240704_124646_migrate_RPP1519_laporanpemeriksaanlab_v
 */
class m240704_124646_migrate_RPP1519_laporanpemeriksaanlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpemeriksaanlab_v");
        $laporanpemeriksaanlab_v = file_get_contents(__DIR__ . '/definitions/laporanpemeriksaanlab_v.sql');
        $this->execute($laporanpemeriksaanlab_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240704_124646_migrate_RPP1519_laporanpemeriksaanlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240704_124646_migrate_RPP1519_laporanpemeriksaanlab_v cannot be reverted.\n";

        return false;
    }
    */
}
