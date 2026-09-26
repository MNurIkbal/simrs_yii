<?php

use yii\db\Migration;

/**
 * Class m250822_092826_cron_unduh_dokumen_eklaim_dokumenresepkronis_r
 */
class m250822_092826_cron_unduh_dokumen_eklaim_dokumenresepkronis_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS public.dokumenresepkronis_r (
                id serial4 NOT NULL,
                penjualanresep_id int4 NULL,
                response text NULL,
                is_sent bool DEFAULT false NOT NULL,
                created_date timestamp(6) DEFAULT now() NULL,
                created_by int4 NULL,
                is_deleted bool DEFAULT false NOT NULL,
                is_active bool DEFAULT true NOT NULL,
                deleted_date timestamp(6) NULL,
                deleted_by int4 NULL,
                CONSTRAINT dokumenresepkronis_r_pkey PRIMARY KEY (id)
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250822_092826_cron_unduh_dokumen_eklaim_dokumenresepkronis_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250822_092826_cron_unduh_dokumen_eklaim_dokumenresepkronis_r cannot be reverted.\n";

        return false;
    }
    */
}
