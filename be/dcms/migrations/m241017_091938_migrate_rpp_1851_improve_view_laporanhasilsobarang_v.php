<?php

use yii\db\Migration;

/**
 * Class m241017_091938_migrate_rpp_1851_improve_view_laporanhasilsobarang_v
 */
class m241017_091938_migrate_rpp_1851_improve_view_laporanhasilsobarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanhasilsobarang_v");
        $laporanhasilsobarang_v = file_get_contents(__DIR__ . '/definitions/laporanhasilsobarang_v.sql');
        $this->execute($laporanhasilsobarang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241017_091938_migrate_rpp_1851_improve_view_laporanhasilsobarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241017_091938_migrate_rpp_1851_improve_view_laporanhasilsobarang_v cannot be reverted.\n";

        return false;
    }
    */
}
