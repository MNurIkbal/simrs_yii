<?php

use yii\db\Migration;

/**
 * Class m240123_114837_migration_pcp_82_add_table_logclosebill_r
 */
class m240123_114837_migration_pcp_82_add_table_logclosebill_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.logclosebill_r (
            logclosebill_id serial8 NOT NULL,
            tgl_close_bill timestamp(0) NULL,
            tipe varchar(100) NULL,
            additional_data text NULL,
            created_date timestamp(0) NOT NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            pendaftaran_id int4 NULL,
            keterangan text NULL,
            alasan text NULL,
            CONSTRAINT logclosebill_r_pkey PRIMARY KEY (logclosebill_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_114837_migration_pcp_82_add_table_logclosebill_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_114837_migration_pcp_82_add_table_logclosebill_r cannot be reverted.\n";

        return false;
    }
    */
}
