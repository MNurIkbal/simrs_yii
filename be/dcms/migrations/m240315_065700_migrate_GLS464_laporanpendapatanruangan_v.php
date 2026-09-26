<?php

use yii\db\Migration;

/**
 * Class m240315_065700_migrate_GLS464_laporanpendapatanruangan_v
 */
class m240315_065700_migrate_GLS464_laporanpendapatanruangan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpendapatanruangan_v");
        $laporanpendapatanruangan_v = file_get_contents(__DIR__ . '/definitions/laporanpendapatanruangan_v.sql');
        $this->execute($laporanpendapatanruangan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240315_065700_migrate_GLS464_laporanpendapatanruangan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240315_065700_migrate_GLS464_laporanpendapatanruangan_v cannot be reverted.\n";

        return false;
    }
    */
}
