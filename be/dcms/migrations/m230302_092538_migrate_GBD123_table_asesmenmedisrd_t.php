<?php

use yii\db\Migration;

/**
 * Class m230302_092538_migrate_GBD123_table_asesmenmedisrd_t 
 */

class m230302_092538_migrate_GBD123_table_asesmenmedisrd_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS kepala int4,
                ADD IF NOT EXISTS kepala_lainnya TEXT,
                ADD IF NOT EXISTS mulut int4,
                ADD IF NOT EXISTS mulut_lainnya TEXT,
                ADD IF NOT EXISTS mata int4,
                ADD IF NOT EXISTS mata_lainnya TEXT,
                ADD IF NOT EXISTS tht int4,
                ADD IF NOT EXISTS tht_lainnya TEXT,
                ADD IF NOT EXISTS leher int4,
                ADD IF NOT EXISTS leher_lainnya TEXT,
                ADD IF NOT EXISTS toraks int4,
                ADD IF NOT EXISTS thoraks_lainnya TEXT,
                ADD IF NOT EXISTS pergerakan int4,
                ADD IF NOT EXISTS perkusi int4,
                ADD IF NOT EXISTS perkusi_lainnya TEXT,
                ADD IF NOT EXISTS nafas int4,
                ADD IF NOT EXISTS pernapasan_lainnya TEXT,
                ADD IF NOT EXISTS rochi int4,
                ADD IF NOT EXISTS wheezing int4,
                ADD IF NOT EXISTS irama int4,
                ADD IF NOT EXISTS bunyi_jantung int4,
                ADD IF NOT EXISTS bunyi_jantung_lainnya TEXT,
                ADD IF NOT EXISTS kelainan int4,
                ADD IF NOT EXISTS kelaianan_lainnya TEXT,
                ADD IF NOT EXISTS benjolan int4,
                ADD IF NOT EXISTS benjolan_lainnya TEXT,
                ADD IF NOT EXISTS nyeri_tekan int4,
                ADD IF NOT EXISTS nyeri_tekan_lainnya TEXT,
                ADD IF NOT EXISTS hernia int4,
                ADD IF NOT EXISTS hernia_lainnya TEXT,
                ADD IF NOT EXISTS bising_usus int4,
                ADD IF NOT EXISTS bising_usus_lainnya TEXT,
                ADD IF NOT EXISTS distensi int4,
                ADD IF NOT EXISTS distensi_lainnya TEXT,
                ADD IF NOT EXISTS tulang_belakang int4,
                ADD IF NOT EXISTS tulang_belakang_lainnya TEXT,
                ADD IF NOT EXISTS sistem_saraf int4,
                ADD IF NOT EXISTS genetalia int4,
                ADD IF NOT EXISTS edema int4,
                ADD IF NOT EXISTS crt int4,
                ADD IF NOT EXISTS sistem_saraf_lainnya TEXT,
                ADD IF NOT EXISTS genetalia_lainnya TEXT,
                ADD IF NOT EXISTS edema_lainnya TEXT,
                ADD IF NOT EXISTS crt_lainnya TEXT,
                ADD IF NOT EXISTS pemeriksaan_fisik_lainnya TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230302_092538_migrate_GBD123_table_asesmenmedisrd_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230302_092538_migrate_GBD123_table_asesmenmedisrd_t cannot be reverted.\n";

        return false;
    }
    */
}
