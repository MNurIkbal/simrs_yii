<?php

use yii\db\Migration;

/**
 * Class m250722_072717_RPP2044_laporanpenerimaanobatalkesv_harganetto
 */
class m250722_072717_RPP2044_laporanpenerimaanobatalkesv_harganetto extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpenerimaanobatalkes_v");
        $laporanpenerimaanobatalkes_v = file_get_contents(__DIR__ . '/definitions/laporanpenerimaanobatalkes_v.sql');
        $this->execute($laporanpenerimaanobatalkes_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250722_072717_RPP2044_laporanpenerimaanobatalkesv_harganetto cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250722_072717_RPP2044_laporanpenerimaanobatalkesv_harganetto cannot be reverted.\n";

        return false;
    }
    */
}
