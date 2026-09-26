<?php

use yii\db\Migration;

/**
 * Class m230304_033729_create_view_dh_kunjungan_new
 */
class m230304_033729_create_view_dh_kunjungan_new extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP VIEW IF EXISTS public.dh_kunjungan_new_v;
        ");
        $this->execute("
        CREATE OR REPLACE VIEW public.dh_kunjungan_new_v
        AS SELECT sy_kunjungan.kunjungan_id,
            sy_kunjungan.no_pendaftaran,
            sy_kunjungan.no_rekammedik,
            sy_kunjungan.nama_pasien,
            sy_kunjungan.jenis_kelamin,
            sy_kunjungan.tgl_lahir,
            sy_kunjungan.umur,
            sy_kunjungan.tgl_pendaftaran,
            sy_kunjungan.tgl_pulang,
            sy_kunjungan.instalasi_kode,
            sy_kunjungan.instalasi_nama,
            sy_kunjungan.ruangan_kode,
            sy_kunjungan.ruangan_nama,
            sy_kunjungan.carabayar_kode,
            sy_kunjungan.carabayar_nama,
            sy_kunjungan.penjamin_kode,
            sy_kunjungan.penjamin_nama,
            sy_kunjungan.kelas_kode,
            sy_kunjungan.kelas_nama,
            sy_kunjungan.dokter_kode,
            sy_kunjungan.dokter_nama,
            sy_kunjungan.no_sep,
            sy_kunjungan.status_kunjungan,
            sy_kunjungan.no_kamar,
            sy_kunjungan.no_tempattidur,
            sy_kunjungan.hak_kelasbpjs,
            sy_kunjungan.carakeluar_kode,
            sy_kunjungan.lama_rawat,
            sy_kunjungan.no_asuransi,
            sy_kunjungan.is_verifikasi,
            sy_kunjungan.total_verifikasi,
            sy_kunjungan.tgl_verifikasi,
            sy_kunjungan.identitas_id,
            sy_kunjungan.identitas_nama,
            sy_kunjungan.identitas_value,
            sy_kunjungan.no_klaimcovid,
            sy_kunjungan.pasien_id,
            sy_kunjungan.additional_data,
            sy_kunjungan.created_date,
            sy_kunjungan.created_by,
            sy_kunjungan.modified_count,
            sy_kunjungan.last_modified_date,
            sy_kunjungan.last_modified_by,
            sy_kunjungan.is_deleted,
            sy_kunjungan.is_active,
            sy_kunjungan.deleted_date,
            sy_kunjungan.deleted_by,
            sy_kunjungan.nosep,
            look_verifikasi.lookup_id,
            look_verifikasi.verifikasi
        FROM sy_kunjungan
            JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS verifikasi
                FROM lookup_m a) look_verifikasi ON sy_kunjungan.status_kunjungan = look_verifikasi.lookup_id
        WHERE sy_kunjungan.is_active = true AND sy_kunjungan.is_deleted = false AND (sy_kunjungan.status_kunjungan = ANY (ARRAY[549, 550, 551, 556]));
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230304_033729_create_view_dh_kunjungan_new cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230304_033729_create_view_dh_kunjungan_new cannot be reverted.\n";

        return false;
    }
    */
}
