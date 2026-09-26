<?php

use yii\db\Migration;

/**
 * Class m250722_073040_RPP2044_laporanpenerimaanbarangv_barangharganetto
 */
class m250722_073040_RPP2044_laporanpenerimaanbarangv_barangharganetto extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpenerimaanbarang_v");
        $laporanpenerimaanbarang_v = file_get_contents(__DIR__ . '/definitions/laporanpenerimaanbarang_v.sql');
        $this->execute($laporanpenerimaanbarang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250722_073040_RPP2044_laporanpenerimaanbarangv_barangharganetto cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250722_073040_RPP2044_laporanpenerimaanbarangv_barangharganetto cannot be reverted.\n";

        return false;
    }
    */
}
