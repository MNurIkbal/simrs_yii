<?php

use yii\db\Migration;

/**
 * Class m220615_010239_migrate_skema_fisioterapi_MHG2385_soapfisioterapiranap_v
 */
class m220615_010239_migrate_skema_fisioterapi_MHG2385_soapfisioterapiranap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.soapfisioterapiranap_v;');
        $this->execute("
            CREATE VIEW \"public\".\"soapfisioterapiranap_v\" AS
            SELECT soapfisioterapi_t.soapfisioterapi_id,
            soapfisioterapi_t.pendaftaran_id,
            soapfisioterapi_t.pasien_id,
            soapfisioterapi_t.terapis_id,
            soapfisioterapi_t.tgl_soapfisioterapi,
            ruangan_m.ruangan_id,
            pendaftaran_t.instalasi_id,
            array_to_string(array_agg(soapfisioterapidetail_t.pasienmasukpenunjang_id), ','::text) AS pasienmasukpenunjang_id,
            array_to_string(array_agg(soapfisioterapidetail_t.programterapi_id), ','::text) AS programterapi_id,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin_nama,
            pegawai_m.nama_pegawai AS terapis_nama,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
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
            soapfisioterapi_t.is_edit,
            peg_edit.nama_pegawai AS last_modified_by_name
            FROM ((((((((((((soapfisioterapi_t
            LEFT JOIN ( SELECT a.soapfisioterapi_id,
            a.programterapi_id,
            a.pasienmasukpenunjang_id
            FROM soapfisioterapidetail_t a) soapfisioterapidetail_t ON ((soapfisioterapi_t.soapfisioterapi_id = soapfisioterapidetail_t.soapfisioterapi_id)))
            LEFT JOIN ( SELECT a.pasien_id,
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
            a.pasienadmisi_id
            FROM pendaftaran_t a) pendaftaran_t ON ((soapfisioterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kamartempattidur_id
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
            LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
            LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienmasukpenunjang_id,
            a.programterapi_id,
            a.ruangan_id
            FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON ((soapfisioterapi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN loginpemakai_k pegawai_edit ON ((soapfisioterapi_t.last_modified_by = pegawai_edit.loginpemakai_id)))
            LEFT JOIN pegawai_m peg_edit ON ((pegawai_edit.pegawai_id = peg_edit.pegawai_id)))
            WHERE ((soapfisioterapi_t.is_active = true) AND (soapfisioterapi_t.is_deleted = false))
            GROUP BY soapfisioterapi_t.soapfisioterapi_id, soapfisioterapi_t.pendaftaran_id, soapfisioterapi_t.pasien_id, soapfisioterapi_t.terapis_id, soapfisioterapi_t.tgl_soapfisioterapi, ruangan_m.ruangan_id, pendaftaran_t.instalasi_id, pasien_m.nama_pasien, pasien_m.tanggal_lahir, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kamarruangan_m.kamarruangan_nokamar, kamartempattidur_m.no_tempattidur, instalasi_m.instalasi_nama, soapfisioterapi_t.subject, soapfisioterapi_t.object, soapfisioterapi_t.assesment, soapfisioterapi_t.planning, (soapfisioterapi_t.a_diag_utama)::text, (soapfisioterapi_t.a_diag_penyerta)::text, soapfisioterapi_t.catatan_dokter, soapfisioterapi_t.instruksi, soapfisioterapi_t.last_modified_date, soapfisioterapi_t.last_modified_by, soapfisioterapi_t.is_edit, peg_edit.nama_pegawai
            ;");
        $this->execute('
            ALTER TABLE public.soapfisioterapiranap_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220615_010239_migrate_skema_fisioterapi_MHG2385_soapfisioterapiranap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220615_010239_migrate_skema_fisioterapi_MHG2385_soapfisioterapiranap_v cannot be reverted.\n";

        return false;
    }
    */
}
