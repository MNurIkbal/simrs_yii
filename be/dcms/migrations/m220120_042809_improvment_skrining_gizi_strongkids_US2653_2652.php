<?php

use yii\db\Migration;

/**
 * Class m220120_042809_improvment_skrining_gizi_strongkids_US2653_2652
 */
class m220120_042809_improvment_skrining_gizi_strongkids_US2653_2652 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenawal_t ADD IF NOT EXISTS is_verifikasigizi   BOOLEAN DEFAULT FALSE;
        ');

        $this->execute('
            ALTER TABLE asesmenawal_t ADD IF NOT EXISTS pegawaiverifikasigizi_id    int4;
        ');

        $this->execute('
            ALTER TABLE asesmenawal_t ADD IF NOT EXISTS tgl_verifikasigizi  timestamp(6);
        ');

        $this->execute('
            ALTER TABLE anamnesa_t ADD IF NOT EXISTS is_verifikasigizi  BOOLEAN DEFAULT FALSE;
        ');

        $this->execute('
            ALTER TABLE anamnesa_t ADD IF NOT EXISTS pegawaiverifikasigizi_id   int4;
        ');

        $this->execute('
            ALTER TABLE anamnesa_t ADD IF NOT EXISTS tgl_verifikasigizi timestamp(6);
        ');

        $this->execute('
            ALTER TABLE anamnesa_t ADD IF NOT EXISTS strongkids_kurus   TEXT;
        ');

        $this->execute('
            ALTER TABLE anamnesa_t ADD IF NOT EXISTS strongkids_turunbb TEXT;
        ');

        $this->execute('
            ALTER TABLE anamnesa_t ADD IF NOT EXISTS strongkids_kondisikhusus   TEXT;
        ');

        $this->execute('
            ALTER TABLE anamnesa_t ADD IF NOT EXISTS strongkids_keadaan_beresiko    TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS is_verifikasigizi  BOOLEAN DEFAULT FALSE;
        ');

        $this->execute('
            ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS pegawaiverifikasigizi_id   int4;
        ');

        $this->execute('
            ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS tgl_verifikasigizi timestamp(6);
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220120_042809_improvment_skrining_gizi_strongkids_US2653_2652 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220120_042809_improvment_skrining_gizi_strongkids_US2653_2652 cannot be reverted.\n";

        return false;
    }
    */
}
