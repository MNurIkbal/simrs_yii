<?php

use yii\db\Migration;

/**
 * Class m220620_102336_migrate_MHG2441_table_asesmenmedis_t
 */
class m220620_102336_migrate_MHG2441_table_asesmenmedis_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS kategori_asmed TEXT,
                ADD IF NOT EXISTS is_merokok_pasif BOOLEAN DEFAULT FALSE,
                ADD IF NOT EXISTS jumlah_rokok varchar(50),
                ADD IF NOT EXISTS kategori_denyut_nadi TEXT,
                ADD IF NOT EXISTS kesadaran_umum int4,
                ADD IF NOT EXISTS keadaan_umum int4,
                ADD IF NOT EXISTS pergerakan int4,
                ADD IF NOT EXISTS perkusi int4,
                ADD IF NOT EXISTS nafas int4,
                ADD IF NOT EXISTS rochi int4,
                ADD IF NOT EXISTS wheezing int4,
                ADD IF NOT EXISTS irama int4,
                ADD IF NOT EXISTS bunyi_jantung int4,
                ADD IF NOT EXISTS kelainan int4,
                ADD IF NOT EXISTS benjolan int4,
                ADD IF NOT EXISTS nyeri_tekan int4,
                ADD IF NOT EXISTS hernia int4,
                ADD IF NOT EXISTS bising_usus int4,
                ADD IF NOT EXISTS distensi int4,
                ADD IF NOT EXISTS tulang_belakang int4,
                ADD IF NOT EXISTS kepala_lainnya TEXT,
                ADD IF NOT EXISTS mata_lainnya TEXT,
                ADD IF NOT EXISTS tht_lainnya TEXT,
                ADD IF NOT EXISTS leher_lainnya TEXT,
                ADD IF NOT EXISTS mulut_lainnya TEXT,
                ADD IF NOT EXISTS thoraks_lainnya TEXT,
                ADD IF NOT EXISTS perkusi_lainnya TEXT,
                ADD IF NOT EXISTS pernapasan_lainnya TEXT,
                ADD IF NOT EXISTS bunyi_jantung_lainnya TEXT,
                ADD IF NOT EXISTS kelaianan_lainnya TEXT,
                ADD IF NOT EXISTS benjolan_lainnya TEXT,
                ADD IF NOT EXISTS nyeri_tekan_lainnya TEXT,
                ADD IF NOT EXISTS hernia_lainnya TEXT,
                ADD IF NOT EXISTS bising_usus_lainnya TEXT,
                ADD IF NOT EXISTS distensi_lainnya TEXT,
                ADD IF NOT EXISTS tulang_belakang_lainnya TEXT,
                ADD IF NOT EXISTS terintubasi_lainnya TEXT,
                ADD IF NOT EXISTS luka_bakar float4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220620_102336_migrate_MHG2441_table_asesmenmedis_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220620_102336_migrate_MHG2441_table_asesmenmedis_t cannot be reverted.\n";

        return false;
    }
    */
}
