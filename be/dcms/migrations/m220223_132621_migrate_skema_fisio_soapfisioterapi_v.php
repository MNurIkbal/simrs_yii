<?php

use yii\db\Migration;

/**
 * Class m220223_132621_migrate_skema_fisio_soapfisioterapi_v
 */
class m220223_132621_migrate_skema_fisio_soapfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.soapfisioterapi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"soapfisioterapi_v\" AS
            SELECT soapfisioterapi_t.soapfisioterapi_id,
            soapfisioterapi_t.pendaftaran_id,
            soapfisioterapi_t.pasien_id,
            soapfisioterapi_t.terapis_id,
            pendaftaran_t.tgl_pendaftaran AS tgl_soapfisioterapi,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.instalasi_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.programterapi_id,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin_nama,
            pegawai_m.nama_pegawai AS terapis_nama,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            soapfisioterapi_t.subject,
            soapfisioterapi_t.object,
            soapfisioterapi_t.assesment,
            soapfisioterapi_t.planning,
            soapfisioterapi_t.a_diag_utama,
            soapfisioterapi_t.a_diag_penyerta,
            soapfisioterapi_t.catatan_dokter,
            soapfisioterapi_t.instruksi,
            soapfisioterapi_t.last_modified_date,
            soapfisioterapi_t.last_modified_by,
            peg_edit.nama_pegawai AS last_modified_by_name,
            soapfisioterapi_t.is_edit,
            look_statuspasien.lookup_id AS status_periksa_id,
            look_statuspasien.lookup_name AS status_periksa_nama
            FROM (((((((((soapfisioterapi_t
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.tanggal_lahir,
            a.jeniskelamin
            FROM pasien_m a) pasien_m ON ((soapfisioterapi_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((soapfisioterapi_t.terapis_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.ruangan_id,
            a.instalasi_id,
            a.tgl_pendaftaran,
            a.status_periksa
            FROM pendaftaran_t a) pendaftaran_t ON ((soapfisioterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_statuspasien ON (((pendaftaran_t.status_periksa)::integer = look_statuspasien.lookup_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienmasukpenunjang_id,
            a.programterapi_id
            FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
            LEFT JOIN loginpemakai_k pegawai_edit ON ((soapfisioterapi_t.last_modified_by = pegawai_edit.loginpemakai_id)))
            LEFT JOIN pegawai_m peg_edit ON ((pegawai_edit.pegawai_id = peg_edit.pegawai_id)))
            WHERE ((soapfisioterapi_t.is_active = true) AND (soapfisioterapi_t.is_deleted = false))
            ;");
            $this->execute('
                ALTER TABLE public.soapfisioterapi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_132621_migrate_skema_fisio_soapfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_132621_migrate_skema_fisio_soapfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
