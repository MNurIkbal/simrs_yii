<?php

use yii\db\Migration;

/**
 * Class m230815_095913_migrate_RPP513_skrining_asesmen_rj_t
 */
class m230815_095913_migrate_RPP513_skrining_asesmen_rj_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.skrining_asesmen_rj_t (
            skrining_asesmen_rj_id serial4 NOT NULL,
            alamat_pasien text NULL,
            kelurahan_desa varchar(100) NULL,
            kecamatan varchar(100) NULL,
            kabupaten varchar(100) NULL,
            no_hp varchar(100) NULL,
            tempat_lahir varchar(100) NULL,
            tgl_lahir date NULL,
            jenis_kelamin varchar(10) NULL,
            status_pernikahan varchar(30) NULL,
            agama varchar(10) NULL,
            pendidikan varchar(20) NULL,
            pekerjaan varchar(100) NULL,
            nama_ayah_atau_ibu varchar(100) NULL,
            pekerjaan_ayah_atau_ibu varchar(100) NULL,
            nama_suami_atau_istri varchar(100) NULL,
            pekerjaan_suami_atau_istri varchar(100) NULL,
            cara_berkunjung varchar(100) NULL,
            cara_bayar varchar(100) NULL,
            no_peserta_bpjs_kis varchar(100) NULL,
            status_kepesertaan int4 NULL,
            tgl_kunjungan timestamp NULL,
            cara_masuk varchar(100) NULL,
            petugas_loket_pendaftaran varchar(100) NULL,
            pasien_keluarga_pasien varchar(100) NULL,
            created_date timestamp NULL,
            is_active bool NULL DEFAULT true,
            is_deleted bool NULL DEFAULT false,
            pendaftaran_id int4 NULL,
            CONSTRAINT skrining_asesmen_rj_t_pkey PRIMARY KEY (skrining_asesmen_rj_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230815_095913_migrate_RPP513_skrining_asesmen_rj_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_095913_migrate_RPP513_skrining_asesmen_rj_t cannot be reverted.\n";

        return false;
    }
    */
}
