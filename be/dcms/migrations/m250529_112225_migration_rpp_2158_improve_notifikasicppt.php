<?php

use yii\db\Migration;

/**
 * Class m250529_112225_migration_rpp_2158_improve_notifikasicppt
 */
class m250529_112225_migration_rpp_2158_improve_notifikasicppt extends Migration
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
            CREATE INDEX pendaftaran_t_is_deleted_idx ON public.pendaftaran_t USING btree (is_deleted) WHERE (is_deleted = false);
        ');
        
        $this->execute('
            CREATE INDEX pendaftaran_t_status_periksa_idx ON public.pendaftaran_t USING btree (status_periksa);
        ');

        $this->execute("
            CREATE INDEX idx_pendaftaran_instalasi_status_str ON public.pendaftaran_t USING btree (instalasi_id, status_periksa) WHERE ((instalasi_id = 2) AND ((status_periksa)::text = '2'::text));
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250529_112225_migration_rpp_2158_improve_notifikasicppt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250529_112225_migration_rpp_2158_improve_notifikasicppt cannot be reverted.\n";

        return false;
    }
    */
}
