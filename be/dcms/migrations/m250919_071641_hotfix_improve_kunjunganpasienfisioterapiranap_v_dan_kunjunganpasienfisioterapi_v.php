<?php

use yii\db\Migration;

/**
 * Class m250919_071641_hotfix_improve_kunjunganpasienfisioterapiranap_v_dan_kunjunganpasienfisioterapi_v
 */
class m250919_071641_hotfix_improve_kunjunganpasienfisioterapiranap_v_dan_kunjunganpasienfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS kunjunganpasienfisioterapiranap_v");
        $kunjunganpasienfisioterapiranap_v = file_get_contents(__DIR__ . '/definitions/kunjunganpasienfisioterapiranap_v_190925.sql');
        $this->execute($kunjunganpasienfisioterapiranap_v);
        $this->execute('ALTER TABLE "public"."kunjunganpasienfisioterapiranap_v" OWNER TO "postgres";');
        
        $this->execute("DROP VIEW IF EXISTS kunjunganpasienfisioterapi_v");
        $kunjunganpasienfisioterapi_v = file_get_contents(__DIR__ . '/definitions/kunjunganpasienfisioterapi_v_190925.sql');
        $this->execute($kunjunganpasienfisioterapi_v);
        $this->execute('ALTER TABLE "public"."kunjunganpasienfisioterapi_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250919_071641_hotfix_improve_kunjunganpasienfisioterapiranap_v_dan_kunjunganpasienfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250919_071641_hotfix_improve_kunjunganpasienfisioterapiranap_v_dan_kunjunganpasienfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
