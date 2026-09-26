<?php

use yii\db\Migration;

/**
 * Class m240701_161100_migrate_GLBJ516_update_view_pegawai_master_v
 */
class m240701_161100_migrate_GLBJ516_update_view_pegawai_master_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS pegawai_master_v");
        $pegawai_master_v = file_get_contents(__DIR__ . '/definitions/pegawai_master_v.sql');
        $this->execute($pegawai_master_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240701_161100_migrate_GLBJ516_update_view_pegawai_master_v cannot be reverted.\n";

        return false;
    }

}
