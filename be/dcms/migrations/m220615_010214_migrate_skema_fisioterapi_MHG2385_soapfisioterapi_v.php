<?php

use yii\db\Migration;

/**
 * Class m220615_010214_migrate_skema_fisioterapi_MHG2385_soapfisioterapi_v
 */
class m220615_010214_migrate_skema_fisioterapi_MHG2385_soapfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.soapfisioterapi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"soapfisioterapi_v\" AS
            SELECT 'SOAP Fisio'::text AS tipe,
            soapfisioterapi_t.soapfisioterapi_id,
            soapfisioterapi_t.pendaftaran_id,
            soapfisioterapi_t.pasien_id,
            soapfisioterapi_t.terapis_id,
            soapfisioterapi_t.tgl_soapfisioterapi,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.instalasi_id,
            NULL::text AS pasienmasukpenunjang_id,
            NULL::text AS programterapi_id,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            jk.lookup_name AS jeniskelamin_nama,
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
            FROM (((((((((((soapfisioterapi_t
            LEFT JOIN ( SELECT a.soapfisioterapi_id
            FROM soapfisioterapidetail_t a
            GROUP BY a.soapfisioterapi_id) soapfisioterapidetail_t ON ((soapfisioterapi_t.soapfisioterapi_id = soapfisioterapidetail_t.soapfisioterapi_id)))
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
            LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
            FROM loginpemakai_k a) pegawai_edit ON ((soapfisioterapi_t.last_modified_by = pegawai_edit.loginpemakai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_edit ON ((pegawai_edit.pegawai_id = peg_edit.pegawai_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) jk ON (((pasien_m.jeniskelamin)::integer = jk.lookup_id)))
            WHERE ((soapfisioterapi_t.is_active = true) AND (soapfisioterapi_t.is_deleted = false))
            GROUP BY soapfisioterapi_t.soapfisioterapi_id, pendaftaran_t.ruangan_id, pendaftaran_t.instalasi_id, pasien_m.nama_pasien, pasien_m.tanggal_lahir, jk.lookup_name, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, instalasi_m.instalasi_nama, peg_edit.nama_pegawai, look_statuspasien.lookup_id, look_statuspasien.lookup_name
            UNION ALL
            SELECT 'SOAP RJ'::text AS tipe,
            soaprj_t.soaprj_id AS soapfisioterapi_id,
            soaprj_t.pendaftaran_id,
            soaprj_t.pasien_id,
            soaprj_t.pegawai_id AS terapis_id,
            soaprj_t.tgl_soaprj AS tgl_soapfisioterapi,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.instalasi_id,
            NULL::text AS pasienmasukpenunjang_id,
            NULL::text AS programterapi_id,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            jk.lookup_name AS jeniskelamin_nama,
            pegawai_m.nama_pegawai AS terapis_nama,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            soaprj_t.subject,
            soaprj_t.object,
            NULL::text AS assesment,
            soaprj_t.planning,
            soaprj_t.a_diag_utama,
            soaprj_t.a_diag_penyerta,
            soaprj_t.catatan_dokter,
            soaprj_t.instruksi,
            soaprj_t.last_modified_date,
            soaprj_t.last_modified_by,
            peg_edit.nama_pegawai AS last_modified_by_name,
            CASE
            WHEN (soaprj_t.last_modified_by IS NULL) THEN false
            ELSE true
            END AS is_edit,
            look_statuspasien.lookup_id AS status_periksa_id,
            look_statuspasien.lookup_name AS status_periksa_nama
            FROM (((((((((soaprj_t
            JOIN ( SELECT a.pendaftaran_id,
            a.ruangan_id,
            a.instalasi_id,
            a.pasien_id,
            a.status_periksa
            FROM pendaftaran_t a) pendaftaran_t ON ((soaprj_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.jeniskelamin,
            a.nama_pasien,
            a.tanggal_lahir
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_statuspasien ON (((pendaftaran_t.status_periksa)::integer = look_statuspasien.lookup_id)))
            LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
            FROM loginpemakai_k a) pegawai_edit ON ((soaprj_t.last_modified_by = pegawai_edit.loginpemakai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_edit ON ((pegawai_edit.pegawai_id = peg_edit.pegawai_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) jk ON (((pasien_m.jeniskelamin)::integer = jk.lookup_id)))
            WHERE ((soaprj_t.is_deleted IS FALSE) AND (soaprj_t.is_active IS TRUE))
            UNION ALL
            SELECT 'SOAP RD/RI'::text AS tipe,
            cppt_t.cppt_id AS soapfisioterapi_id,
            cppt_t.pendaftaran_id,
            cppt_t.pasien_id,
            cppt_t.pegawai_id AS terapis_id,
            cppt_t.tgl_cppt AS tgl_soapfisioterapi,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.instalasi_id,
            NULL::text AS pasienmasukpenunjang_id,
            NULL::text AS programterapi_id,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            jk.lookup_name AS jeniskelamin_nama,
            pegawai_m.nama_pegawai AS terapis_nama,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            cppt_t.subject,
            cppt_t.object,
            NULL::text AS assesment,
            cppt_t.planning,
            cppt_t.a_diag_utama,
            cppt_t.a_diag_penyerta,
            cppt_t.catatan_dokter,
            cppt_t.instruksi,
            cppt_t.last_modified_date,
            cppt_t.last_modified_by,
            peg_edit.nama_pegawai AS last_modified_by_name,
            CASE
            WHEN (cppt_t.last_modified_by IS NULL) THEN false
            ELSE true
            END AS is_edit,
            look_statuspasien.lookup_id AS status_periksa_id,
            look_statuspasien.lookup_name AS status_periksa_nama
            FROM ((((((((((cppt_t
            JOIN ( SELECT a.pendaftaran_id,
            a.ruangan_id,
            a.instalasi_id,
            a.pasien_id,
            a.status_periksa,
            a.pasienadmisi_id
            FROM pendaftaran_t a) pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.status_ranap
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.jeniskelamin,
            a.nama_pasien,
            a.tanggal_lahir
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((cppt_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((cppt_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_statuspasien ON ((COALESCE(pasienadmisi_t.status_ranap, (pendaftaran_t.status_periksa)::integer) = look_statuspasien.lookup_id)))
            LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
            FROM loginpemakai_k a) pegawai_edit ON ((cppt_t.last_modified_by = pegawai_edit.loginpemakai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_edit ON ((pegawai_edit.pegawai_id = peg_edit.pegawai_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) jk ON (((pasien_m.jeniskelamin)::integer = jk.lookup_id)))
            WHERE ((cppt_t.is_deleted IS FALSE) AND (cppt_t.is_active IS TRUE))
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
        echo "m220615_010214_migrate_skema_fisioterapi_MHG2385_soapfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220615_010214_migrate_skema_fisioterapi_MHG2385_soapfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
