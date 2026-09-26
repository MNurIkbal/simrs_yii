<?php

use yii\db\Migration;

/**
 * Class m240905_072454_migrate_rpp_1672_improve_view_laporanmutasibarang_v
 */
class m240905_072454_migrate_rpp_1672_improve_view_laporanmutasibarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->execute("DROP VIEW IF EXISTS laporanmutasibarang_v");
	           $laporanmutasibarang_v = file_get_contents(__DIR__ . '/definitions/laporanmutasibarang_v.sql');
	           $this->execute($laporanmutasibarang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240905_072454_migrate_rpp_1672_improve_view_laporanmutasibarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240905_072454_migrate_rpp_1672_improve_view_laporanmutasibarang_v cannot be reverted.\n";

        return false;
    }
    */
}
