<?php

use yii\db\Migration;

/**
 * Class m240212_084230_migrate_mcu_prima_resumemedis_t
 */
class m240212_084230_migrate_mcu_prima_resumemedis_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.resumemedis_t ADD IF NOT EXISTS status_kesehatan text NULL;");
        $this->execute("ALTER TABLE public.resumemedis_t ADD IF NOT EXISTS hasil_pemeriksaan_kesehatan text NULL;");
        $this->execute("ALTER TABLE public.resumemedis_t ADD IF NOT EXISTS resume_pemeriksaan text NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_084230_migrate_mcu_prima_resumemedis_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240212_084230_migrate_mcu_prima_resumemedis_t cannot be reverted.\n";

        return false;
    }
    */
}
