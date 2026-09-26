<?php

use yii\db\Migration;

/**
 * Class m250829_023533_migrate_dsv1976_formulir_rajal_t
 */
class m250829_023533_migrate_dsv1976_formulir_rajal_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TABLE IF EXISTS formulir_rajal_t");
        $this->execute(" CREATE TABLE public.formulir_rajal_t (
            formulir_rajal_id serial4 NOT NULL,
            pendaftaran_id int4 NOT NULL,
            programterapi_id int4 NOT NULL,
            pegawai_id int4 NULL,
            anamnesa text NULL,
            pemeriksaanfisik_dan_ujifungsi text NULL,
            diag_medis json NULL,
            diag_fungsi json NULL,
            diag_kfr json NULL,
            hasil_pemeriksaan_penunjang text NULL,
            anjuran text NULL,
            goal text NULL,
            evaluasi varchar(255) NULL,
            is_suspek bool DEFAULT false NOT NULL,
            suspek_penyakit varchar(255) NULL,
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
            CONSTRAINT formulir_rajal_t_pkey PRIMARY KEY (formulir_rajal_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250829_023533_migrate_dsv1976_formulir_rajal_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250829_023533_migrate_dsv1976_formulir_rajal_t cannot be reverted.\n";

        return false;
    }
    */
}
