<?php

use yii\db\Migration;

/**
 * Class m220627_022027_migrate_MHG2146_laporanptm_v
 */
class m220627_022027_migrate_MHG2146_laporanptm_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanptm_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanptm_v\" AS
            SELECT 'RJ'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            CASE
            WHEN (pasien_m.no_identitas_pasien IS NULL) THEN (pasien_m.additional_pasien)::character varying
            ELSE pasien_m.no_identitas_pasien
            END AS no_identitas_pasien,
            pasien_m.nopeserta_bpjs,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.tanggal_lahir,
            pasien_m.no_telepon_pasien,
            pasien_m.alamatemail,
            pasien_m.alamat_pasien,
            pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            d_utama.diagnosa_id,
            d_utama.diag_utama_kode,
            d_utama.diag_utama,
            soaprj_t.subject,
            soaprj_t.object,
            soaprj_t.a_diag_utama,
            soaprj_t.a_diag_penyerta,
            soaprj_t.planning,
            pendaftaran_t.umur,
            1 AS jumlah_kunjungan,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai AS nama_dokter,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            look_golongandarah.golongandarah AS golongan_darah,
            pasienpulang_t.tglpasienpulang AS tgl_pulang,
            look_status_periksa.status_periksa AS keadaan_sekarang,
            instalasi_m.instalasi_id,
            pasien_m.pasien_id,
            NULL::text AS pemeriksaan_ekg
            FROM ((((((((pendaftaran_t
            LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
            FROM pasienpulang_t a) pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN ( SELECT a.pasien_id,
            a.no_identitas_pasien,
            a.additional_pasien,
            a.nopeserta_bpjs,
            a.nama_pasien,
            a.no_rekam_medik,
            a.tanggal_lahir,
            a.no_telepon_pasien,
            a.alamatemail,
            a.alamat_pasien,
            a.nama_ibu,
            a.nama_ayah,
            a.golongandarah
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.is_deleted,
            a.subject,
            a.object,
            a.a_diag_utama,
            a.a_diag_penyerta,
            a.planning
            FROM soaprj_t a) soaprj_t ON (((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id) AND (soaprj_t.is_deleted = false))))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_namalainnya AS diag_utama
            FROM (koreksidiagnosa_t
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS golongandarah
            FROM lookup_m a) look_golongandarah ON (((pasien_m.golongandarah)::integer = look_golongandarah.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_periksa
            FROM lookup_m a) look_status_periksa ON (((pendaftaran_t.status_periksa)::integer = look_golongandarah.lookup_id)))
            WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL) AND ((pendaftaran_t.status_periksa)::integer = ANY (ARRAY[1, 2, 4, 430, 433, 486, 540, 3, 339])))
            UNION ALL
            SELECT 'RI'::text AS jenis,
            pasienadmisi_t.pendaftaran_id,
            CASE
            WHEN (pasien_m.no_identitas_pasien IS NULL) THEN (pasien_m.additional_pasien)::character varying
            ELSE pasien_m.no_identitas_pasien
            END AS no_identitas_pasien,
            pasien_m.nopeserta_bpjs,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.tanggal_lahir,
            pasien_m.no_telepon_pasien,
            pasien_m.alamatemail,
            pasien_m.alamat_pasien,
            pasienadmisi_t.tgl_pendaftaran AS tgl_registrasi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            d_utama.diagnosa_id,
            d_utama.diag_utama_kode,
            d_utama.diag_utama,
            cppt.subject,
            cppt.object,
            cppt.a_diag_utama,
            cppt.a_diag_penyerta,
            cppt.planning,
            pendaftaran_t.umur,
            1 AS jumlah_kunjungan,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_rd.pegawai_id
            ELSE dok_ri.pegawai_id
            END AS pegawai_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_rd.nama_pegawai
            ELSE dok_ri.nama_pegawai
            END AS nama_dokter,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            look_golongandarah.golongandarah AS golongan_darah,
            pulang_ri.tglpasienpulang AS tgl_pulang,
            look_status_ranap.status_ranap AS keadaan_sekarang,
            ruangan_m.instalasi_id,
            pasien_m.pasien_id,
            NULL::text AS pemeriksaan_ekg
            FROM ((((((((((((pendaftaran_t
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pasienpulang_id,
            a.ruangan_id,
            a.pegawai_id,
            a.pendaftaran_id,
            a.tgl_pendaftaran,
            a.status_ranap
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.pasienpulang_id
            FROM pasienpulang_t a) pulang_rd ON ((pendaftaran_t.pasienpulang_id = pulang_rd.pasienpulang_id)))
            LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
            FROM pasienpulang_t a) pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_identitas_pasien,
            a.additional_pasien,
            a.nopeserta_bpjs,
            a.no_rekam_medik,
            a.tanggal_lahir,
            a.no_telepon_pasien,
            a.alamatemail,
            a.alamat_pasien,
            a.nama_ibu,
            a.nama_ayah,
            a.golongandarah
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
            FROM ruangan_m a) ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dok_rd ON ((pendaftaran_t.pegawai_id = dok_rd.pegawai_id)))
            LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
            FROM pegawai_m) dok_ri ON ((pasienadmisi_t.pegawai_id = dok_ri.pegawai_id)))
            LEFT JOIN ( SELECT cppt_t.pendaftaran_id,
            cppt_t.pasienadmisi_id,
            cppt_t.subject,
            cppt_t.object,
            cppt_t.a_diag_utama,
            cppt_t.a_diag_penyerta,
            cppt_t.planning
            FROM (cppt_t
            JOIN ( SELECT max(pk.cppt_id) AS cppt_id,
            pk.pasienadmisi_id
            FROM cppt_t pk
            WHERE (pk.is_deleted = false)
            GROUP BY pk.pasienadmisi_id) max_pk ON (((cppt_t.cppt_id = max_pk.cppt_id) AND (cppt_t.pasienadmisi_id = max_pk.pasienadmisi_id))))) cppt ON ((pasienadmisi_t.pasienadmisi_id = cppt.pasienadmisi_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            koreksidiagnosa_t.pasienadmisi_id,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_namalainnya AS diag_utama
            FROM (koreksidiagnosa_t
            JOIN ( SELECT a.diagnosa_id,
            a.diagnosa_kode,
            a.diagnosa_namalainnya
            FROM diagnosa_m a) diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)) d_utama ON ((pasienadmisi_t.pasienadmisi_id = d_utama.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS golongandarah
            FROM lookup_m a) look_golongandarah ON (((pasien_m.golongandarah)::integer = look_golongandarah.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_ranap
            FROM lookup_m a) look_status_ranap ON ((pasienadmisi_t.status_ranap = look_golongandarah.lookup_id)))
            WHERE ((ruangan_m.instalasi_id = 3) AND ((pendaftaran_t.status_periksa)::integer = ANY (ARRAY[1, 2, 4, 430, 433, 486, 540, 3, 339])) AND (pasienadmisi_t.status_ranap = ANY (ARRAY[440, 441, 487])) AND ((dok_rd.pegawai_id IS NOT NULL) OR (dok_ri.pegawai_id IS NOT NULL)))
            UNION ALL
            SELECT 'RD'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            CASE
            WHEN (pasien_m.no_identitas_pasien IS NULL) THEN (pasien_m.additional_pasien)::character varying
            ELSE pasien_m.no_identitas_pasien
            END AS no_identitas_pasien,
            pasien_m.nopeserta_bpjs,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.tanggal_lahir,
            pasien_m.no_telepon_pasien,
            pasien_m.alamatemail,
            pasien_m.alamat_pasien,
            pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            d_utama.diagnosa_id,
            d_utama.diag_utama_kode,
            d_utama.diag_utama,
            cppt.subject,
            cppt.object,
            cppt.a_diag_utama,
            cppt.a_diag_penyerta,
            cppt.planning,
            pendaftaran_t.umur,
            1 AS jumlah_kunjungan,
            dok_rd.pegawai_id,
            dok_rd.nama_pegawai AS nama_dokter,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            look_golongandarah.golongandarah AS golongan_darah,
            pulang_rd.tglpasienpulang AS tgl_pulang,
            look_status_periksa.status_periksa AS keadaan_sekarang,
            pendaftaran_t.instalasi_id,
            pasien_m.pasien_id,
            NULL::text AS pemeriksaan_ekg
            FROM ((((((((((((pendaftaran_t
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pasienpulang_id,
            a.ruangan_id,
            a.pegawai_id
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
            FROM pasienpulang_t a) pulang_rd ON ((pendaftaran_t.pasienpulang_id = pulang_rd.pasienpulang_id)))
            LEFT JOIN ( SELECT a.pasienpulang_id
            FROM pasienpulang_t a) pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
            JOIN ( SELECT a.pasien_id,
            a.no_identitas_pasien,
            a.additional_pasien,
            a.nopeserta_bpjs,
            a.nama_pasien,
            a.no_rekam_medik,
            a.tanggal_lahir,
            a.no_telepon_pasien,
            a.alamatemail,
            a.alamat_pasien,
            a.nama_ibu,
            a.nama_ayah,
            a.golongandarah
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
            FROM ruangan_m a) ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dok_rd ON ((pendaftaran_t.pegawai_id = dok_rd.pegawai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dok_ri ON ((pasienadmisi_t.pegawai_id = dok_ri.pegawai_id)))
            LEFT JOIN ( SELECT cppt_t.pendaftaran_id,
            cppt_t.pasienadmisi_id,
            cppt_t.subject,
            cppt_t.object,
            cppt_t.a_diag_utama,
            cppt_t.a_diag_penyerta,
            cppt_t.planning
            FROM (cppt_t
            JOIN ( SELECT min(pk.cppt_id) AS cppt_id,
            pk.pendaftaran_id
            FROM cppt_t pk
            WHERE (pk.is_deleted = false)
            GROUP BY pk.pendaftaran_id) max_pk ON (((cppt_t.cppt_id = max_pk.cppt_id) AND (cppt_t.pendaftaran_id = max_pk.pendaftaran_id))))) cppt ON ((pendaftaran_t.pendaftaran_id = cppt.pendaftaran_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_namalainnya AS diag_utama
            FROM (koreksidiagnosa_t
            JOIN ( SELECT a.diagnosa_id,
            a.diagnosa_kode,
            a.diagnosa_namalainnya
            FROM diagnosa_m a) diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS golongandarah
            FROM lookup_m a) look_golongandarah ON (((pasien_m.golongandarah)::integer = look_golongandarah.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_periksa
            FROM lookup_m a) look_status_periksa ON (((pendaftaran_t.status_periksa)::integer = look_golongandarah.lookup_id)))
            WHERE ((pendaftaran_t.instalasi_id = 2) AND ((pendaftaran_t.status_periksa)::integer = ANY (ARRAY[1, 2, 4, 430, 433, 486, 540, 3, 339])) AND ((dok_rd.pegawai_id IS NOT NULL) OR (dok_ri.pegawai_id IS NOT NULL)))
            ;");
        $this->execute('
            ALTER TABLE public.laporanptm_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220627_022027_migrate_MHG2146_laporanptm_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220627_022027_migrate_MHG2146_laporanptm_v cannot be reverted.\n";

        return false;
    }
    */
}
