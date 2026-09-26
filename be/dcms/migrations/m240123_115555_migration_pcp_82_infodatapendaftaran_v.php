<?php

use yii\db\Migration;

/**
 * Class m240123_115555_migration_pcp_82_infodatapendaftaran_v
 */
class m240123_115555_migration_pcp_82_infodatapendaftaran_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infodatapendaftaran_v");
        $infodatapendaftaran_v = file_get_contents(__DIR__ . '/definitions/infodatapendaftaran_v.sql');
        $this->execute($infodatapendaftaran_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_115555_migration_pcp_82_infodatapendaftaran_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_115555_migration_pcp_82_infodatapendaftaran_v cannot be reverted.\n";

        return false;
    }
    */
}
