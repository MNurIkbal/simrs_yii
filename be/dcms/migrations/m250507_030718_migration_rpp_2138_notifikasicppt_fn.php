<?php

use yii\db\Migration;

/**
 * Class m250507_030718_migration_rpp_2138_notifikasicppt_fn
 */
class m250507_030718_migration_rpp_2138_notifikasicppt_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS public.notifikasicppt_fn;');
        $notifikasicppt_fn = file_get_contents(__DIR__ . '/definitions/notifikasicppt_fn.sql');
        $this->execute($notifikasicppt_fn);

        $this->execute('
            CREATE INDEX IF NOT EXISTS cppt_t_pegawai_id_idx ON public.cppt_t (pegawai_id);
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS soaprj_t_pegawai_id_idx ON public.soaprj_t (pegawai_id);
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS idx_cppt_t_pendaftaran_id_created_date ON cppt_t(pendaftaran_id, created_date DESC);
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS idx_soaprj_t_pendaftaran_id_created_date ON soaprj_t(pendaftaran_id, created_date DESC);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250507_030718_migration_rpp_2138_notifikasicppt_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250507_030718_migration_rpp_2138_notifikasicppt_fn cannot be reverted.\n";

        return false;
    }
    */
}
