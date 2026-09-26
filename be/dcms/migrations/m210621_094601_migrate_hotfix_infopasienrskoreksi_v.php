<?php

use yii\db\Migration;

/**
 * Class m210621_094601_migrate_hotfix_infopasienrskoreksi_v
 */
class m210621_094601_migrate_hotfix_infopasienrskoreksi_v extends Migration
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
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
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
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verif,
            pendaftaran_t.pasienpulang_id,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
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
            NULL::json AS diagnosadokter_masuk,
            cppt_t.a_diag_utama AS diagnosadokter_utama,
            cppt_t.a_diag_penyerta AS diagnosadokter_penyerta,
            NULL::json AS diagnosadokter_terapi
            FROM ((((((((((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pegawai_m dokter_dpjp ON ((pendaftaran_t.pegawai_id = dokter_dpjp.pegawai_id)))
            JOIN kelaspelayanan_m ON ((kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN pengajuanklaimdetail_t ON (((pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            LEFT JOIN pengajuanklaim_t ON ((pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            koreksidiagnosa_t.pasienadmisi_id,
            diagnosa_m.diagnosa_id AS diag_masuk_id,
            diagnosa_m.diagnosa_kode AS diag_masuk_kode,
            diagnosa_m.diagnosa_nama AS diag_masuk
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 1) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 1)) d_masuk ON ((pendaftaran_t.pendaftaran_id = d_masuk.pendaftaran_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            koreksidiagnosa_t.pasienadmisi_id,
            diagnosa_m.diagnosa_id AS diag_utama_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 2) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            koreksidiagnosa_t.pasienadmisi_id,
            diagnosa_m.diagnosa_id AS diag_penyerta_id,
            diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
            diagnosa_m.diagnosa_nama AS diag_penyerta
            FROM (koreksidiagnosa_t
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 3) AND (koreksidiagnosa_t.is_deleted = false))
            GROUP BY koreksidiagnosa_t.pendaftaran_id, koreksidiagnosa_t.pasienadmisi_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            koreksidiagnosa_t.pasienadmisi_id,
            diagnosa_m.diagnosa_id AS diag_terapi_id,
            diagnosa_m.diagnosa_kode AS diag_terapi_kode,
            diagnosa_m.diagnosa_nama AS diag_terapi
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 6) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 6)) d_terapi ON ((pendaftaran_t.pendaftaran_id = d_terapi.pendaftaran_id)))
            LEFT JOIN ( SELECT cppt_t_1.cppt_id,
            cppt_t_1.pendaftaran_id,
            cppt_t_1.pasienadmisi_id,
            cppt_t_1.a_diag_utama,
            cppt_t_1.a_diag_penyerta
            FROM (cppt_t cppt_t_1
            JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
            cppt_last.pendaftaran_id,
            cppt_last.pasienadmisi_id
            FROM cppt_t cppt_last
            WHERE (cppt_last.is_deleted = false)
            GROUP BY cppt_last.pendaftaran_id, cppt_last.pasienadmisi_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id)))
            WHERE ((pendaftaran_t.instalasi_id = 2) AND (cppt_t.pasienadmisi_id IS NULL) AND (d_masuk.pasienadmisi_id IS NULL) AND (d_utama.pasienadmisi_id IS NULL) AND (d_penyerta.pasienadmisi_id IS NULL) AND (d_terapi.pasienadmisi_id IS NULL) AND (pendaftaran_t.status_verifikasi = ANY (ARRAY[549, 550])))
            UNION ALL
            SELECT 'RJ'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
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
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verif,
            pendaftaran_t.pasienpulang_id,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
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
            WHEN (soaprj_t.soaprj_id IS NOT NULL) THEN NULL::json
            ELSE NULL::json
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
            FROM ((((((((((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pegawai_m dokter_dpjp ON ((pendaftaran_t.pegawai_id = dokter_dpjp.pegawai_id)))
            JOIN kelaspelayanan_m ON ((kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN pengajuanklaimdetail_t ON (((pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            LEFT JOIN pengajuanklaim_t ON ((pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_masuk_id,
            diagnosa_m.diagnosa_kode AS diag_masuk_kode,
            diagnosa_m.diagnosa_nama AS diag_masuk
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 1) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 1)) d_masuk ON ((pendaftaran_t.pendaftaran_id = d_masuk.pendaftaran_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_utama_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 2) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_penyerta_id,
            diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
            diagnosa_m.diagnosa_nama AS diag_penyerta
            FROM (koreksidiagnosa_t
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 3) AND (koreksidiagnosa_t.is_deleted = false))
            GROUP BY koreksidiagnosa_t.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_terapi_id,
            diagnosa_m.diagnosa_kode AS diag_terapi_kode,
            diagnosa_m.diagnosa_nama AS diag_terapi
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 6) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 6)) d_terapi ON ((pendaftaran_t.pendaftaran_id = d_terapi.pendaftaran_id)))
            LEFT JOIN soaprj_t ON (((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id) AND (soaprj_t.is_deleted = false))))
            WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.status_verifikasi = ANY (ARRAY[549, 550])))
            UNION ALL
            SELECT 'RI'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
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
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS status_verif,
            pasienadmisi_t.pasienpulang_id,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
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
            resumemedisri_t.diag_masuk AS diagnosadokter_masuk,
            resumemedisri_t.diag_utama AS diagnosadokter_utama,
            resumemedisri_t.diag_penyerta AS diagnosadokter_penyerta,
            resumemedisri_t.prosedur_diag AS diagnosadokter_terapi
            FROM (((((((((((((((((pendaftaran_t
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pegawai_m dokter_dpjp ON ((pasienadmisi_t.pegawai_id = dokter_dpjp.pegawai_id)))
            JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN pengajuanklaimdetail_t ON (((pasienadmisi_t.pasienadmisi_id = pengajuanklaimdetail_t.pasienadmisi_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            LEFT JOIN pengajuanklaim_t ON ((pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_masuk_id,
            diagnosa_m.diagnosa_kode AS diag_masuk_kode,
            diagnosa_m.diagnosa_nama AS diag_masuk
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 1) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 1)) d_masuk ON ((pendaftaran_t.pendaftaran_id = d_masuk.pendaftaran_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pasienadmisi_id,
            diagnosa_m.diagnosa_id AS diag_utama_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 2) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pasienadmisi_id = d_utama.pasienadmisi_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pasienadmisi_id,
            diagnosa_m.diagnosa_id AS diag_penyerta_id,
            diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
            diagnosa_m.diagnosa_nama AS diag_penyerta
            FROM (koreksidiagnosa_t
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 3) AND (koreksidiagnosa_t.is_deleted = false))
            GROUP BY koreksidiagnosa_t.pasienadmisi_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pasienadmisi_id = d_penyerta.pasienadmisi_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_terapi_id,
            diagnosa_m.diagnosa_kode AS diag_terapi_kode,
            diagnosa_m.diagnosa_nama AS diag_terapi
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 6) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 6)) d_terapi ON ((pendaftaran_t.pendaftaran_id = d_terapi.pendaftaran_id)))
            LEFT JOIN resumemedisri_t ON (((pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
            WHERE ((pasienadmisi_t.status_verifikasi = ANY (ARRAY[549, 550])) AND (pendaftaran_t.instalasi_id <> 2))
            UNION ALL
            SELECT 'RDRI'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienadmisi_t.pegawai_id AS dokter_dpjp_id,
            dokter_dpjp.nama_pegawai AS dokter_dpjp,
            pasienadmisi_t.status_verifikasi,
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS status_verif,
            pasienadmisi_t.pasienpulang_id,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
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
            resumemedisri_t.diag_masuk AS diagnosadokter_masuk,
            resumemedisri_t.diag_utama AS diagnosadokter_utama,
            resumemedisri_t.diag_penyerta AS diagnosadokter_penyerta,
            resumemedisri_t.prosedur_diag AS diagnosadokter_terapi
            FROM (((((((((((((((((pendaftaran_t
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN pegawai_m dokter_dpjp ON ((pasienadmisi_t.pegawai_id = dokter_dpjp.pegawai_id)))
            LEFT JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id)))
            LEFT JOIN pengajuanklaimdetail_t ON (((pasienadmisi_t.pasienadmisi_id = pengajuanklaimdetail_t.pasienadmisi_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            LEFT JOIN pengajuanklaim_t ON ((pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_masuk_id,
            diagnosa_m.diagnosa_kode AS diag_masuk_kode,
            diagnosa_m.diagnosa_nama AS diag_masuk
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 1) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 1)) d_masuk ON ((pendaftaran_t.pendaftaran_id = d_masuk.pendaftaran_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pasienadmisi_id,
            diagnosa_m.diagnosa_id AS diag_utama_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 2) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pasienadmisi_id = d_utama.pasienadmisi_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pasienadmisi_id,
            diagnosa_m.diagnosa_id AS diag_penyerta_id,
            diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
            diagnosa_m.diagnosa_nama AS diag_penyerta
            FROM (koreksidiagnosa_t
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 3) AND (koreksidiagnosa_t.is_deleted = false))
            GROUP BY koreksidiagnosa_t.pasienadmisi_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pasienadmisi_id = d_penyerta.pasienadmisi_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_terapi_id,
            diagnosa_m.diagnosa_kode AS diag_terapi_kode,
            diagnosa_m.diagnosa_nama AS diag_terapi
            FROM ((koreksidiagnosa_t
            JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
            pk.pendaftaran_id,
            pk.diagnosa_id
            FROM koreksidiagnosa_t pk
            WHERE ((pk.kelompokdiagnosa_id = 6) AND (pk.is_deleted = false))
            GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 6)) d_terapi ON ((pendaftaran_t.pendaftaran_id = d_terapi.pendaftaran_id)))
            LEFT JOIN resumemedisri_t ON (((pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
            WHERE ((pasienadmisi_t.status_verifikasi = ANY (ARRAY[549, 550])) AND (pendaftaran_t.instalasi_id = 2) AND ((pendaftaran_t.status_periksa)::text <> '402'::text) AND ((pendaftaran_t.status_periksa)::text <> '628'::text) AND ((pendaftaran_t.status_periksa)::text <> '453'::text) AND ((pasienadmisi_t.status_ranap)::text <> '453'::text))
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
        echo "m210621_094601_migrate_hotfix_infopasienrskoreksi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210621_094601_migrate_hotfix_infopasienrskoreksi_v cannot be reverted.\n";

        return false;
    }
    */
}
