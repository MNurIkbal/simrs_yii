<?php

use yii\db\Migration;

/**
 * Class m240402_111716_migration_pcp23_kunjunganpasienfisioterapiranap_v
 */
class m240402_111716_migration_pcp23_kunjunganpasienfisioterapiranap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS kunjunganpasienfisioterapiranap_v");
        $kunjunganpasienfisioterapiranap_v = file_get_contents(__DIR__ . '/definitions/kunjunganpasienfisioterapiranap_v.sql');
        $this->execute($kunjunganpasienfisioterapiranap_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240402_111716_migration_pcp23_kunjunganpasienfisioterapiranap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240402_111716_migration_pcp23_kunjunganpasienfisioterapiranap_v cannot be reverted.\n";

        return false;
    }
    */
}
