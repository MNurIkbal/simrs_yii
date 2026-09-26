<?php

use yii\db\Migration;

/**
 * Class m240618_143256_pcp_90_usg_hasilusg
 */
class m240618_143256_pcp_90_usg_hasilusg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS public.hasilusg_t (
                hasilusg_id serial4 NOT NULL,
                pasien_id int4 NULL,
                pasienadmisi_id int4 NULL,
                pendaftaran_id int4 NULL,
                tgl_pemeriksaan timestamp NULL,
                dokterpemeriksa_id int4 NULL,
                hasilusg text NULL,
                additional_data text NULL,
                created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
                created_by int4 NULL,
                modified_count int4 NULL,
                last_modified_date timestamp(6) NULL,
                last_modified_by int4 NULL,
                is_deleted bool NOT NULL DEFAULT false,
                is_active bool NOT NULL DEFAULT true,
                deleted_date timestamp(6) NULL,
                deleted_by int4 NULL,
                CONSTRAINT hasilusg_t_pkey PRIMARY KEY (hasilusg_id)
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240618_143256_pcp_90_usg_hasilusg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240618_143256_pcp_90_usg_hasilusg cannot be reverted.\n";

        return false;
    }
    */
}
