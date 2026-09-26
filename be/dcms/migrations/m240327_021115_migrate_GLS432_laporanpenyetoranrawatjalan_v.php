<?php

use yii\db\Migration;

/**
 * Class m240327_021115_migrate_GLS432_laporanpenyetoranrawatjalan_v
 */
class m240327_021115_migrate_GLS432_laporanpenyetoranrawatjalan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpenyetoranrawatjalan_v");
        $laporanpenyetoranrawatjalan_v = file_get_contents(__DIR__ . '/definitions/laporanpenyetoranrawatjalan_v.sql');
        $this->execute($laporanpenyetoranrawatjalan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240327_021115_migrate_GLS432_laporanpenyetoranrawatjalan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240327_021115_migrate_GLS432_laporanpenyetoranrawatjalan_v cannot be reverted.\n";

        return false;
    }
    */
}
