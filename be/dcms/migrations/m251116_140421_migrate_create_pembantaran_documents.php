<?php

use yii\db\Migration;

/**
 * Class m251116_140421_migrate_create_pembantaran_documents
 */
class m251116_140421_migrate_create_pembantaran_documents extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS public.pembantaran_documents (
                id bigserial NOT NULL,
                pembantaran_id int8 NOT NULL,
                pengajuan_id int8 NOT NULL,
                nama_dokumen varchar(200) NOT NULL,
                url_dokumen text NOT NULL,
                created_date timestamp(0) DEFAULT CURRENT_TIMESTAMP NOT NULL,
                created_by int8 NULL,
                last_modified_date timestamp(0) NULL,
                last_modified_by int8 NULL,
                count_modified int4 DEFAULT 0 NOT NULL,
                deleted_date timestamp(0) NULL,
                deleted_by int8 NULL,
                is_active bool DEFAULT true NOT NULL,
                is_deleted bool DEFAULT false NOT NULL,
                CONSTRAINT pembantaran_documents_pkey PRIMARY KEY (id)
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251116_140421_migrate_create_pembantaran_documents cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251116_140421_migrate_create_pembantaran_documents cannot be reverted.\n";

        return false;
    }
    */
}
