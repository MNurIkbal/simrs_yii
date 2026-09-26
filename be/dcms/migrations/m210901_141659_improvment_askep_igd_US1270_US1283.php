<?php

use yii\db\Migration;

/**
 * Class m210901_141659_improvment_askep_igd_US1270_US1283
 */
class m210901_141659_improvment_askep_igd_US1270_US1283 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_kepala TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_kepala_lacerasi TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_kepala_battle_sign TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_kepala_lainnya TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_mata TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_mulut TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_mulut_luka_dalam TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_mulut_lainnya TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_telinga TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_leher TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_leher_lainnya TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_extremitas TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_extremitas_pulsasi TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_dada TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_dada_simetris_asimetris TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_dada_pneumo_hamatotoraks TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_dada_nyeri_lokasi TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_dada_nyeri_kapan TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_dada_nyeri_durasi TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_dada_nyeri_kegiatan TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_dada_bunyi_jantung TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_abdomen TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_abdomen_memas TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_abdomen_nyeri TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_abdomen_lainnya TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_abdomen_bising_usus TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_pelvis TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_pelvis_lainnya TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_medulla_spinalis TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_kolumna_vertebralis TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS survey_kepala_utuh TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS extremitas_utuh TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS extremitas_nyeri TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS extremitas_deformitas TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS extremitas_defisit_neurologis TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS extremitas_jejas TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS extremitas_pulsasi TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedisrd_t ADD IF NOT EXISTS extremitas_fraktur TEXT;
        ');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210901_141659_improvment_askep_igd_US1270_US1283 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210901_141659_improvment_askep_igd_US1270_US1283 cannot be reverted.\n";

        return false;
    }
    */
}
