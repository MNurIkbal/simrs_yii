<?php

use yii\db\Migration;

/**
 * Class m250829_023211_migrate_dsv1950_ujifungsifisioterapi_t
 */
class m250829_023211_migrate_dsv1950_ujifungsifisioterapi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TABLE IF EXISTS ujifungsifisioterapi_t");
        $this->execute("CREATE TABLE public.ujifungsifisioterapi_t (
            ujifungsi_id serial4 NOT NULL,
            pendaftaran_id int4 NOT NULL,
            programterapi_id int4 NOT NULL,
            pegawai_id int4 NULL,
            hasil_yang_didapat text NULL,
            kesimpulan varchar(255) NULL,
            diagnosa_id int4 NULL,
            daftartindakan_id int4 NULL,
            additional_data text NULL,
            created_date timestamp(6) DEFAULT 'now'::text::date NOT NULL,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool DEFAULT false NOT NULL,
            is_active bool DEFAULT true NOT NULL,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            CONSTRAINT ujifungsi_t_pkey PRIMARY KEY (ujifungsi_id)
        );"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250829_023211_migrate_dsv1950_ujifungsifisioterapi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250829_023211_migrate_dsv1950_ujifungsifisioterapi_t cannot be reverted.\n";

        return false;
    }
    */
}
