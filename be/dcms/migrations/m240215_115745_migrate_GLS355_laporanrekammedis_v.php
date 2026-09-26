<?php

use yii\db\Migration;

/**
 * Class m240215_115745_migrate_GLS355_laporanrekammedis_v
 */
class m240215_115745_migrate_GLS355_laporanrekammedis_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanrekammedis_v");
        $laporanrekammedis_v = file_get_contents(__DIR__ . '/definitions/laporanrekammedis_v.sql');
        $this->execute($laporanrekammedis_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240215_115745_migrate_GLS355_laporanrekammedis_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240215_115745_migrate_GLS355_laporanrekammedis_v cannot be reverted.\n";

        return false;
    }
    */
}
