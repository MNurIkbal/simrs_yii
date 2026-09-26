<?php

use yii\db\Migration;

/**
 * Class m220719_141044_migrate_MHG723_infopasienrskoreksi_v
 */
class m220719_141044_migrate_MHG723_infopasienrskoreksi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienrskoreksi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasienrskoreksi_v\" AS
            SELECT 'RD'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            look_jeniskelamin.jeniskelamin AS jenis_kelamin,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.pegawai_id AS dokter_dpjp_id,
            dokter_dpjp.nama_pegawai AS dokter_dpjp,
            pendaftaran_t.status_verifikasi,
            look_status_verifikasi.status_verifikasi AS status_verif,
            pendaftaran_t.pasienpulang_id,
            look_status_periksa.status_periksa,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasien_m.photopasien,
            pasienpulang_t.tglpasienpulang,
            pengajuanklaim_t.status_pengajuanklaim,
            pasien_m.no_identitas_pasien,
            d_masuk.diag_masuk_id AS diagnosa_masuk_id,
            concat(d_masuk.diag_masuk_kode, '. ', d_masuk.diag_masuk) AS diagnosa_masuk,
            d_utama.diag_utama_id AS diagnosa_utama_id,
            concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
            d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id,
            concat(d_penyerta.diag_penyerta_kode, '. ', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
            d_terapi.diag_terapi_id AS diagnosa_terapi_id,
            concat(d_terapi.diag_terapi_kode, '. ', d_terapi.diag_terapi) AS diagnosa_terapi,
            concat((asesmenmedisrd_t.diagnosa_id)::text, ',', diag_masukrd.diagnosa_nama, ',', diag_masukrd.diagnosa_kode) AS diagnosadokter_masuk,
            cppt_t.a_diag_utama AS diagnosadokter_utama,
            cppt_t.a_diag_penyerta AS diagnosadokter_penyerta,
            NULL::json AS diagnosadokter_terapi
            FROM (((((((((((((((((((((pendaftaran_t
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.jeniskelamin,
            a.tanggal_lahir,
            a.photopasien,
            a.no_identitas_pasien
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
            FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
            FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dokter_dpjp ON ((pendaftaran_t.pegawai_id = dokter_dpjp.pegawai_id)))
            JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id)))
            JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
            FROM pasienpulang_t a) pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pengajuanklaim_id
            FROM pengajuanklaimdetail_t a
            WHERE (a.is_deleted = false)) pengajuanklaimdetail_t ON ((pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pengajuanklaim_id,
            a.status_pengajuanklaim
            FROM pengajuanklaim_t a) pengajuanklaim_t ON ((pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_masuk_id,
            diagnosa_m.diagnosa_kode AS diag_masuk_kode,
            diagnosa_m.diagnosa_nama AS diag_masuk
            FROM (koreksidiagnosa_t a
            JOIN diagnosa_m ON ((a.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE ((a.kelompokdiagnosa_id = 1) AND (a.pasienadmisi_id IS NULL))) d_masuk ON ((pendaftaran_t.pendaftaran_id = d_masuk.pendaftaran_id)))
            LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_utama_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
            FROM (koreksidiagnosa_t a
            JOIN diagnosa_m ON ((a.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE ((a.kelompokdiagnosa_id = 2) AND (a.pasienadmisi_id IS NULL))) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_penyerta_id,
            diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
            diagnosa_m.diagnosa_nama AS diag_penyerta
            FROM (koreksidiagnosa_t a
            JOIN diagnosa_m ON ((a.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE ((a.kelompokdiagnosa_id = 3) AND (a.pasienadmisi_id IS NULL) AND (a.is_deleted = false))
            GROUP BY a.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id)))
            LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_terapi_id,
            diagnosa_m.diagnosa_kode AS diag_terapi_kode,
            diagnosa_m.diagnosa_nama AS diag_terapi
            FROM (koreksidiagnosa_t a
            JOIN diagnosa_m ON ((a.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE ((a.kelompokdiagnosa_id = 6) AND (a.pasienadmisi_id IS NULL))) d_terapi ON ((pendaftaran_t.pendaftaran_id = d_terapi.pendaftaran_id)))
            LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
            a.pasienadmisi_id,
            a.a_diag_utama,
            a.a_diag_penyerta
            FROM cppt_t a
            WHERE (a.pasienadmisi_id IS NULL)) cppt_t ON ((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.diagnosa_id
            FROM asesmenmedisrd_t a) asesmenmedisrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.diagnosa_id,
            a.diagnosa_nama,
            a.diagnosa_kode
            FROM diagnosa_m a) diag_masukrd ON ((asesmenmedisrd_t.diagnosa_id = diag_masukrd.diagnosa_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS jeniskelamin
            FROM lookup_m a) look_jeniskelamin ON (((pasien_m.jeniskelamin)::integer = look_jeniskelamin.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_verifikasi
            FROM lookup_m a) look_status_verifikasi ON ((pendaftaran_t.status_verifikasi = look_status_verifikasi.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_periksa
            FROM lookup_m a) look_status_periksa ON (((pendaftaran_t.status_periksa)::integer = look_status_periksa.lookup_id)))
            WHERE ((pendaftaran_t.instalasi_id = 2) AND (pendaftaran_t.status_verifikasi = ANY (ARRAY[549, 550])) AND ((pendaftaran_t.status_periksa)::text <> ALL (ARRAY['402'::text, '411'::text, '628'::text])))
            UNION ALL
            SELECT 'RJ'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            look_jeniskelamin.jeniskelamin AS jenis_kelamin,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.pegawai_id AS dokter_dpjp_id,
            dokter_dpjp.nama_pegawai AS dokter_dpjp,
            pendaftaran_t.status_verifikasi,
            look_status_verifikasi.status_verifikasi AS status_verif,
            pendaftaran_t.pasienpulang_id,
            look_status_periksa.status_periksa,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasien_m.photopasien,
            pasienpulang_t.tglpasienpulang,
            pengajuanklaim_t.status_pengajuanklaim,
            pasien_m.no_identitas_pasien,
            d_masuk.diag_masuk_id AS diagnosa_masuk_id,
            concat(d_masuk.diag_masuk_kode, '. ', d_masuk.diag_masuk) AS diagnosa_masuk,
            d_utama.diag_utama_id AS diagnosa_utama_id,
            concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
            d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id,
            concat(d_penyerta.diag_penyerta_kode, '. ', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
            d_terapi.diag_terapi_id AS diagnosa_terapi_id,
            concat(d_terapi.diag_terapi_kode, '. ', d_terapi.diag_terapi) AS diagnosa_terapi,
            CASE
            WHEN (soaprj_t.soaprj_id IS NOT NULL) THEN NULL::text
            ELSE NULL::text
            END AS diagnosadokter_masuk,
            CASE
            WHEN (soaprj_t.soaprj_id IS NOT NULL) THEN soaprj_t.a_diag_utama
            ELSE NULL::json
            END AS diagnosadokter_utama,
            CASE
            WHEN (soaprj_t.soaprj_id IS NOT NULL) THEN soaprj_t.a_diag_penyerta
            ELSE NULL::json
            END AS diagnosadokter_penyerta,
            CASE
            WHEN (soaprj_t.soaprj_id IS NOT NULL) THEN NULL::json
            ELSE NULL::json
            END AS diagnosadokter_terapi
            FROM (((((((((((((((((((pendaftaran_t
            JOIN ( SELECT b.pasien_id,
            b.nama_pasien,
            b.no_rekam_medik,
            b.jeniskelamin,
            b.tanggal_lahir,
            b.photopasien,
            b.no_identitas_pasien
            FROM pasien_m b) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT b.carabayar_id,
            b.carabayar_nama
            FROM carabayar_m b) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN ( SELECT b.penjamin_id,
            b.penjamin_nama
            FROM penjamin_m b) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN ( SELECT b.jeniskasuspenyakit_id,
            b.jeniskasuspenyakit_nama
            FROM jeniskasuspenyakit_m b) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN ( SELECT b.instalasi_id,
            b.instalasi_nama
            FROM instalasi_m b) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama
            FROM ruangan_m b) ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
            FROM pegawai_m b) dokter_dpjp ON ((pendaftaran_t.pegawai_id = dokter_dpjp.pegawai_id)))
            JOIN ( SELECT b.kelaspelayanan_id,
            b.kelaspelayanan_nama
            FROM kelaspelayanan_m b) kelaspelayanan_m ON ((kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id)))
            LEFT JOIN ( SELECT b.pasienpulang_id,
            b.tglpasienpulang
            FROM pasienpulang_t b) pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN ( SELECT b.pendaftaran_id,
            b.pengajuanklaim_id
            FROM pengajuanklaimdetail_t b
            WHERE (b.is_deleted = false)) pengajuanklaimdetail_t ON ((pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id)))
            LEFT JOIN ( SELECT b.pengajuanklaim_id,
            b.status_pengajuanklaim
            FROM pengajuanklaim_t b) pengajuanklaim_t ON ((pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            LEFT JOIN ( SELECT DISTINCT ON (b.pendaftaran_id) b.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_masuk_id,
            diagnosa_m.diagnosa_kode AS diag_masuk_kode,
            diagnosa_m.diagnosa_nama AS diag_masuk
            FROM (koreksidiagnosa_t b
            JOIN diagnosa_m ON ((b.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (b.kelompokdiagnosa_id = 1)) d_masuk ON ((pendaftaran_t.pendaftaran_id = d_masuk.pendaftaran_id)))
            LEFT JOIN ( SELECT DISTINCT ON (b.pendaftaran_id) b.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_utama_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
            FROM (koreksidiagnosa_t b
            JOIN diagnosa_m ON ((b.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (b.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
            LEFT JOIN ( SELECT b.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_penyerta_id,
            diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
            diagnosa_m.diagnosa_nama AS diag_penyerta
            FROM (koreksidiagnosa_t b
            JOIN diagnosa_m ON ((b.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE ((b.kelompokdiagnosa_id = 3) AND (b.is_deleted = false))
            GROUP BY b.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id)))
            LEFT JOIN ( SELECT DISTINCT ON (b.pendaftaran_id) b.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_terapi_id,
            diagnosa_m.diagnosa_kode AS diag_terapi_kode,
            diagnosa_m.diagnosa_nama AS diag_terapi
            FROM (koreksidiagnosa_t b
            JOIN diagnosa_m ON ((b.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (b.kelompokdiagnosa_id = 6)) d_terapi ON ((pendaftaran_t.pendaftaran_id = d_terapi.pendaftaran_id)))
            LEFT JOIN ( SELECT b.pendaftaran_id,
            b.soaprj_id,
            b.a_diag_utama,
            b.a_diag_penyerta
            FROM soaprj_t b
            WHERE (b.is_deleted = false)) soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS jeniskelamin
            FROM lookup_m a) look_jeniskelamin ON (((pasien_m.jeniskelamin)::integer = look_jeniskelamin.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_verifikasi
            FROM lookup_m a) look_status_verifikasi ON ((pendaftaran_t.status_verifikasi = look_status_verifikasi.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_periksa
            FROM lookup_m a) look_status_periksa ON (((pendaftaran_t.status_periksa)::integer = look_status_periksa.lookup_id)))
            WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.status_verifikasi = ANY (ARRAY[549, 550])) AND ((pendaftaran_t.status_periksa)::text <> ALL (ARRAY['402'::text, '411'::text, '628'::text])))
            UNION ALL
            SELECT 'RI'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            look_jeniskelamin.jeniskelamin AS jenis_kelamin,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienadmisi_t.pegawai_id AS dokter_dpjp_id,
            dokter_dpjp.nama_pegawai AS dokter_dpjp,
            pasienadmisi_t.status_verifikasi,
            look_status_verifikasi.status_verifikasi AS status_verif,
            pasienadmisi_t.pasienpulang_id,
            look_status_periksa.status_ranap AS status_periksa,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasien_m.photopasien,
            pasienpulang_t.tglpasienpulang,
            pengajuanklaim_t.status_pengajuanklaim,
            pasien_m.no_identitas_pasien,
            d_masuk.diag_masuk_id AS diagnosa_masuk_id,
            concat(d_masuk.diag_masuk_kode, '. ', d_masuk.diag_masuk) AS diagnosa_masuk,
            d_utama.diag_utama_id AS diagnosa_utama_id,
            concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
            d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id,
            concat(d_penyerta.diag_penyerta_kode, '. ', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
            d_terapi.diag_terapi_id AS diagnosa_terapi_id,
            concat(d_terapi.diag_terapi_kode, '. ', d_terapi.diag_terapi) AS diagnosa_terapi,
            (resumemedisri_t.diag_awal)::text AS diagnosadokter_masuk,
            resumemedisri_t.diag_utama AS diagnosadokter_utama,
            resumemedisri_t.diag_penyerta AS diagnosadokter_penyerta,
            resumemedisri_t.prosedur_diag AS diagnosadokter_terapi
            FROM ((((((((((((((((((((pendaftaran_t
            JOIN ( SELECT c.pasienadmisi_id,
            c.carabayar_id,
            c.penjamin_id,
            c.ruangan_id,
            c.pegawai_id,
            c.kelaspelayanan_id,
            c.pasienpulang_id,
            c.tgl_admisi,
            c.status_verifikasi,
            c.status_ranap
            FROM pasienadmisi_t c
            WHERE ((c.status_verifikasi = ANY (ARRAY[549, 550])) AND (c.status_ranap <> ALL (ARRAY[440, 453])))) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN ( SELECT c.pasien_id,
            c.nama_pasien,
            c.no_rekam_medik,
            c.jeniskelamin,
            c.tanggal_lahir,
            c.photopasien,
            c.no_identitas_pasien
            FROM pasien_m c) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT c.carabayar_id,
            c.carabayar_nama
            FROM carabayar_m c) carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN ( SELECT c.penjamin_id,
            c.penjamin_nama
            FROM penjamin_m c) penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN ( SELECT c.jeniskasuspenyakit_id,
            c.jeniskasuspenyakit_nama
            FROM jeniskasuspenyakit_m c) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN ( SELECT c.ruangan_id,
            c.ruangan_nama,
            c.instalasi_id
            FROM ruangan_m c) ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT c.instalasi_id,
            c.instalasi_nama
            FROM instalasi_m c) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
            FROM pegawai_m c) dokter_dpjp ON ((pasienadmisi_t.pegawai_id = dokter_dpjp.pegawai_id)))
            JOIN ( SELECT c.kelaspelayanan_id,
            c.kelaspelayanan_nama
            FROM kelaspelayanan_m c) kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT c.pasienpulang_id,
            c.tglpasienpulang
            FROM pasienpulang_t c) pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN ( SELECT c.pasienadmisi_id,
            c.pengajuanklaim_id
            FROM pengajuanklaimdetail_t c
            WHERE (c.is_deleted = false)) pengajuanklaimdetail_t ON ((pasienadmisi_t.pasienadmisi_id = pengajuanklaimdetail_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT c.pengajuanklaim_id,
            c.status_pengajuanklaim
            FROM pengajuanklaim_t c) pengajuanklaim_t ON ((pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            LEFT JOIN ( SELECT DISTINCT ON (c.pendaftaran_id) c.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_masuk_id,
            diagnosa_m.diagnosa_kode AS diag_masuk_kode,
            diagnosa_m.diagnosa_nama AS diag_masuk
            FROM (koreksidiagnosa_t c
            JOIN diagnosa_m ON ((c.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (c.kelompokdiagnosa_id = 1)) d_masuk ON ((pendaftaran_t.pendaftaran_id = d_masuk.pendaftaran_id)))
            LEFT JOIN ( SELECT DISTINCT ON (c.pasienadmisi_id) c.pasienadmisi_id,
            diagnosa_m.diagnosa_id AS diag_utama_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
            FROM (koreksidiagnosa_t c
            JOIN diagnosa_m ON ((c.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (c.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pasienadmisi_id = d_utama.pasienadmisi_id)))
            LEFT JOIN ( SELECT c.pasienadmisi_id,
            diagnosa_m.diagnosa_id AS diag_penyerta_id,
            diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
            diagnosa_m.diagnosa_nama AS diag_penyerta
            FROM (koreksidiagnosa_t c
            JOIN diagnosa_m ON ((c.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE ((c.kelompokdiagnosa_id = 3) AND (c.is_deleted = false))
            GROUP BY c.pasienadmisi_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pasienadmisi_id = d_penyerta.pasienadmisi_id)))
            LEFT JOIN ( SELECT DISTINCT ON (c.pendaftaran_id) c.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_terapi_id,
            diagnosa_m.diagnosa_kode AS diag_terapi_kode,
            diagnosa_m.diagnosa_nama AS diag_terapi
            FROM (koreksidiagnosa_t c
            JOIN diagnosa_m ON ((c.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (c.kelompokdiagnosa_id = 6)) d_terapi ON ((pendaftaran_t.pendaftaran_id = d_terapi.pendaftaran_id)))
            LEFT JOIN ( SELECT c.pendaftaran_id,
            c.pasienadmisi_id,
            c.diag_awal,
            c.diag_utama,
            c.diag_penyerta,
            c.prosedur_diag
            FROM resumemedisri_t c) resumemedisri_t ON (((pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS jeniskelamin
            FROM lookup_m a) look_jeniskelamin ON (((pasien_m.jeniskelamin)::integer = look_jeniskelamin.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_verifikasi
            FROM lookup_m a) look_status_verifikasi ON ((pasienadmisi_t.status_verifikasi = look_status_verifikasi.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_ranap
            FROM lookup_m a) look_status_periksa ON ((pasienadmisi_t.status_ranap = look_status_periksa.lookup_id)))
            ;");
        $this->execute('
            ALTER TABLE public.infopasienrskoreksi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220719_141044_migrate_MHG723_infopasienrskoreksi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220719_141044_migrate_MHG723_infopasienrskoreksi_v cannot be reverted.\n";

        return false;
    }
    */
}
