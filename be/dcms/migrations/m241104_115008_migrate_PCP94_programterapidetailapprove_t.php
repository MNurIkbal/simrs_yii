<?php

use yii\db\Migration;

/**
 * Class m241104_115008_migrate_PCP94_programterapidetailapprove_t
 */
class m241104_115008_migrate_PCP94_programterapidetailapprove_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.programterapidetailapprove_t (
	programterapidetailapprove_id serial4 NOT NULL,
	programterapiapprove_id int4 NULL,
	daftartindakan_id int4 NULL,
	frekuensi float4 NULL,
	catatan text NULL,
	dokterperujuk_id int4 NULL,
	terapis_id int4 NULL,
	created_date timestamp DEFAULT 'now'::text::date NOT NULL,
	created_by int4 NULL,
	modified_count int4 NULL,
	is_deleted bool DEFAULT false NOT NULL,
	is_active bool DEFAULT true NOT NULL,
	deleted_date timestamp NULL,
	deleted_by int4 NULL,
	tipepaket_id int4 NULL,
	tariftindakan_id int4 NULL,
	pemeriksaanfisio_id int4 NULL,
	is_paketfisio bool DEFAULT false NULL,
	qty_pemeriksaan int4 NULL,
	is_approve_edit bool DEFAULT false NOT NULL,
	programterapi_deleted bool DEFAULT false NOT NULL,
	CONSTRAINT programterapidetailapprove_t_pkey PRIMARY KEY (programterapidetailapprove_id)
);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241104_115008_migrate_PCP94_programterapidetailapprove_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241104_115008_migrate_PCP94_programterapidetailapprove_t cannot be reverted.\n";

        return false;
    }
    */
}
