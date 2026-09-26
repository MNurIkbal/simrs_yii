<?php

use yii\db\Migration;

/**
 * Class m230815_095926_migrate_RPP513_skrining_covid_t
 */
class m230815_095926_migrate_RPP513_skrining_covid_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.skrining_covid_t (
            skrining_covid_id serial4 NOT NULL,
            pendaftaran_id int4 NULL,
            demam bool NULL,
            batuk_pilek_nyeri_tenggorokan bool NULL,
            sesak_napas bool NULL,
            riwayat_luar_negeri bool NULL,
            riwayat_dalam_negeri bool NULL,
            resiko_kontak_pasien_covid bool NULL,
            kontak_tatap_muka bool NULL,
            kontak_perawatan_tanpa_apd bool NULL,
            swab_positif bool NULL,
            tgl_swab_positif timestamp NULL,
            swab_negatif bool NULL,
            tgl_swab_negatif timestamptz NULL,
            suspek bool NULL,
            terkonfirmasi bool NULL,
            petugas_pemeriksa varchar(100) NULL,
            is_deleted bool NULL DEFAULT false,
            is_active bool NULL DEFAULT true,
            additional_data text NULL,
            created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            kontak_fisik bool NULL,
            CONSTRAINT skrining_covid_t_pkey PRIMARY KEY (skrining_covid_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230815_095926_migrate_RPP513_skrining_covid_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_095926_migrate_RPP513_skrining_covid_t cannot be reverted.\n";

        return false;
    }
    */
}
