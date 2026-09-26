<?php

use yii\db\Migration;

/**
 * Class m240405_085901_migrate_GLS517_laporanpenyetoranrawatdarurat_v
 */
class m240405_085901_migrate_GLS517_laporanpenyetoranrawatdarurat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpenyetoranrawatdarurat_v");
        $laporanpenyetoranrawatdarurat_v = file_get_contents(__DIR__ . '/definitions/laporanpenyetoranrawatdarurat_v.sql');
        $this->execute($laporanpenyetoranrawatdarurat_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240405_085901_migrate_GLS517_laporanpenyetoranrawatdarurat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240405_085901_migrate_GLS517_laporanpenyetoranrawatdarurat_v cannot be reverted.\n";

        return false;
    }
    */
}
