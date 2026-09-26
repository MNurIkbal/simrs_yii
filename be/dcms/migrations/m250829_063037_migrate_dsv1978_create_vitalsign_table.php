<?php

use yii\db\Migration;

/**
 * Class m250829_063037_migrate_dsv1978_create_vitalsign_table
 */
class m250829_063037_migrate_dsv1978_create_vitalsign_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $vitalSign = file_get_contents(__DIR__ . '/definitions/vitalsign_t.sql');

        // create new vitalsign_t table
        $this->execute("DROP TABLE IF EXISTS public.vitalsign_t");
        $this->execute($vitalSign);

        // Alter table konfigsystem_k add new column (idempotent) and set default via upsert
        $this->execute("ALTER TABLE public.konfigsystem_k ADD COLUMN IF NOT EXISTS konfig_monitoring_ttv text NULL;");
        $this->execute("UPDATE public.konfigsystem_k SET konfig_monitoring_ttv='{\"RJ\":true,\"RI\":true,\"RD\":true}'
            WHERE konfigsystem_id = 1;
        ");

        // Insert new lookup data (ignore if IDs already exist)
        $this->execute("
            INSERT INTO lookup_m (lookup_id ,lookup_type,lookup_name,lookup_value) VALUES
            (2249,'tingkat_kesadaran','Coma','Coma'),
            (2250,'tingkat_kesadaran','Soporocoma','Soporocoma'),
            (2251,'jenis_ttv','non_hd','Non HD'),
            (2252,'jenis_ttv','hd','HD'),
            (2253,'sumber_ttv','monitoring_ttv','Monitoring TTV'),
            (2254,'sumber_ttv','asesmen_medis','Asesmen Medis'),
            (2255,'sumber_ttv','asesmen_keperawatan','Asesmen Keperawatan')
            ON CONFLICT (lookup_id) DO NOTHING;
        ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250829_063037_migrate_dsv1978_create_vitalsign_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250829_063037_migrate_dsv1978_create_vitalsign_table cannot be reverted.\n";

        return false;
    }
    */
}
