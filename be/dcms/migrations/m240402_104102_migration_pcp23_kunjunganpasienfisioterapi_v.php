<?php

use yii\db\Migration;

/**
 * Class m240402_104102_migration_pcp23_kunjunganpasienfisioterapi_v
 */
class m240402_104102_migration_pcp23_kunjunganpasienfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS kunjunganpasienfisioterapi_v");
        $kunjunganpasienfisioterapi_v = file_get_contents(__DIR__ . '/definitions/kunjunganpasienfisioterapi_v.sql');
        $this->execute($kunjunganpasienfisioterapi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240402_104102_migration_pcp23_kunjunganpasienfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240402_104102_migration_pcp23_kunjunganpasienfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
