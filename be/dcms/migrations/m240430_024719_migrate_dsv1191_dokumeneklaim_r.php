<?php

use yii\db\Migration;

/**
 * Class m240430_024719_migrate_dsv1191_dokumeneklaim_r
 */
class m240430_024719_migrate_dsv1191_dokumeneklaim_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS dokumeneklaim_r (
                id_log serial4 NOT NULL,
                dokumen_id int4 NOT NULL,
                pendaftaran_id int4 NOT NULL,
                type_dokumen varchar(50) NULL,
                status BOOLEAN NULL,
               	created_date timestamp(6) DEFAULT 'now'::text::date NULL,
                created_by int4 NOT NULL,
                modified_count int4 NULL,
                last_modified_date timestamp(6) NULL,
                last_modified_by int4 NULL,
                is_deleted bool NOT NULL DEFAULT false,
                is_active bool NOT NULL DEFAULT true,
                deleted_date timestamp(6) NULL,
                deleted_by int4 null,
                CONSTRAINT dokumeneklaim_r_pkey PRIMARY KEY (id_log)
            )
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240430_024719_migrate_dsv1191_dokumeneklaim_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240430_024719_migrate_dsv1191_dokumeneklaim_r cannot be reverted.\n";

        return false;
    }
    */
}
