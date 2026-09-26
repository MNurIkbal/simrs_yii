<?php

use yii\db\Migration;

/**
 * Class m240911_105437_migrate_rpp1665_laporanpasienlabkasir_v
 */
class m240911_105437_migrate_rpp1665_laporanpasienlabkasir_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpasienlabkasir_v");
        $laporanpasienlabkasir_v = file_get_contents(__DIR__ . '/definitions/laporanpasienlabkasir_v.sql');
        $this->execute($laporanpasienlabkasir_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240911_105437_migrate_rpp1665_laporanpasienlabkasir_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240911_105437_migrate_rpp1665_laporanpasienlabkasir_v cannot be reverted.\n";

        return false;
    }
    */
}
