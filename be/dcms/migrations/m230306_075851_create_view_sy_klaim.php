<?php

use yii\db\Migration;

/**
 * Class m230306_075851_create_view_sy_klaim
 */
class m230306_075851_create_view_sy_klaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        DROP VIEW IF EXISTS koreksidiagnosa_new_v;
        ");

        $this->execute("
        CREATE OR REPLACE VIEW public.koreksidiagnosa_new_v
        AS SELECT sy_koreksidiagnosa.sy_koreksidiagnosa_id,
            sy_koreksidiagnosa.kunjungan_id,
            sy_kunjungan.no_pendaftaran,
            sy_kunjungan.pasien_id,
            sy_kunjungan.no_rekammedik,
            sy_kunjungan.nama_pasien,
            sy_kunjungan.dokter_kode,
            sy_kunjungan.dokter_nama AS dokter_dpjp,
            sy_koreksidiagnosa.tgl_koreksidiagnosa,
            sy_koreksidiagnosa.kelompokdiagnosa_id,
            kelompokdiagnosa_m.kelompokdiagnosa_nama,
            sy_koreksidiagnosa.diagnosa_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
            sy_koreksidiagnosa.diagnosaasal_id,
            sy_koreksidiagnosa.diag_asal_masuk,
            sy_koreksidiagnosa.diag_asal_utama,
            sy_koreksidiagnosa.diag_asal_penyerta,
            sy_koreksidiagnosa.diag_asal_terapi,
            sy_koreksidiagnosa.is_inacbg,
            sy_koreksidiagnosa.is_icdprimer,
            sy_koreksidiagnosa.is_deleted,
            sy_koreksidiagnosa.is_active,
            kelompokdiagnosa_m.kelompokdiagnosa_namalainnya AS kelompok_diagnosa
        FROM sy_koreksidiagnosa
            JOIN sy_kunjungan ON sy_koreksidiagnosa.kunjungan_id = sy_kunjungan.kunjungan_id
            JOIN kelompokdiagnosa_m ON sy_koreksidiagnosa.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
            JOIN diagnosa_m ON sy_koreksidiagnosa.diagnosa_id = diagnosa_m.diagnosa_id
        WHERE sy_koreksidiagnosa.is_deleted = false;
        ");

        $this->execute("
        DROP VIEW IF EXISTS sy_koreksidiagnosa_v;
        ");

        $this->execute("
        CREATE OR REPLACE VIEW public.sy_koreksidiagnosa_v
        AS SELECT sy_koreksidiagnosa.sy_koreksidiagnosa_id AS koreksidiagnosa_id,
            sy_koreksidiagnosa.kunjungan_id,
            sy_kunjungan.no_pendaftaran,
            sy_kunjungan.no_rekammedik,
            sy_kunjungan.nama_pasien,
            sy_kunjungan.dokter_nama AS dokter_dpjp,
            sy_koreksidiagnosa.tgl_koreksidiagnosa,
            sy_koreksidiagnosa.kelompokdiagnosa_id,
            kelompokdiagnosa_m.kelompokdiagnosa_nama,
            sy_koreksidiagnosa.diagnosa_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
            sy_koreksidiagnosa.diagnosaasal_id,
            sy_koreksidiagnosa.diag_asal_masuk,
            sy_koreksidiagnosa.diag_asal_utama,
            sy_koreksidiagnosa.diag_asal_penyerta,
            sy_koreksidiagnosa.diag_asal_terapi,
            sy_koreksidiagnosa.is_inacbg,
            sy_koreksidiagnosa.is_icdprimer,
            sy_koreksidiagnosa.is_deleted,
            sy_koreksidiagnosa.is_active,
            kelompokdiagnosa_m.kelompokdiagnosa_namalainnya AS kelompok_diagnosa
        FROM sy_koreksidiagnosa
            JOIN sy_kunjungan ON sy_koreksidiagnosa.kunjungan_id = sy_kunjungan.kunjungan_id
            JOIN kelompokdiagnosa_m ON sy_koreksidiagnosa.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
            JOIN diagnosa_m ON sy_koreksidiagnosa.diagnosa_id = diagnosa_m.diagnosa_id
        WHERE sy_koreksidiagnosa.is_deleted = false;
        ");

        $this->execute("
        DROP VIEW IF EXISTS sy_infoklaimkirimol_v;
        ");

        $this->execute("
        
        CREATE OR REPLACE VIEW public.sy_infoklaimkirimol_v
        AS SELECT x.instalasi_kode AS tipe,
            x.instalasi_kode,
            x.tgl_keluar,
            x.tgl_group,
            sum(x.rj) AS rawat_jalan,
            sum(x.ri) AS rawat_inap,
            sum(x.rj) + sum(x.ri) AS total,
            sum(x.blm_kirim) AS belum_kirim,
            sum(x.sdh_kirim) AS sudah_kirim
        FROM ( SELECT kunjungan.instalasi_kode,
                    to_char(kunjungan.tgl_pulang, 'YYYY-MM-DD'::text)::date AS tgl_keluar,
                    to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date AS tgl_group,
                    count(kunjungan.instalasi_kode) AS rj,
                    0 AS ri,
                    0 AS blm_kirim,
                    0 AS sdh_kirim
                FROM sy_klaiminacbg
                    LEFT JOIN ( SELECT klaimgroup_t.klaimgroup_id,
                            klaimgroup_t.created_date
                        FROM klaimgroup_t) klaim_group ON sy_klaiminacbg.klaimgroup_id = klaim_group.klaimgroup_id
                    LEFT JOIN ( SELECT sy_kunjungan.kunjungan_id,
                            sy_kunjungan.instalasi_kode,
                            sy_kunjungan.tgl_pulang
                        FROM sy_kunjungan) kunjungan ON sy_klaiminacbg.kunjungan_id = kunjungan.kunjungan_id
                WHERE kunjungan.instalasi_kode::text = 'RJ'::text AND sy_klaiminacbg.is_deleted = false
                GROUP BY kunjungan.instalasi_kode, kunjungan.tgl_pulang, (to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date)
                UNION ALL
                SELECT kunjungan.instalasi_kode,
                    to_char(kunjungan.tgl_pulang, 'YYYY-MM-DD'::text)::date AS tgl_keluar,
                    to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date AS tgl_group,
                    0 AS rj,
                    count(kunjungan.instalasi_kode) AS ri,
                    0 AS blm_kirim,
                    0 AS sdh_kirim
                FROM sy_klaiminacbg
                    LEFT JOIN ( SELECT klaimgroup_t.klaimgroup_id,
                            klaimgroup_t.created_date
                        FROM klaimgroup_t) klaim_group ON sy_klaiminacbg.klaimgroup_id = klaim_group.klaimgroup_id
                    LEFT JOIN ( SELECT sy_kunjungan.kunjungan_id,
                            sy_kunjungan.instalasi_kode,
                            sy_kunjungan.tgl_pulang
                        FROM sy_kunjungan) kunjungan ON sy_klaiminacbg.kunjungan_id = kunjungan.kunjungan_id
                WHERE kunjungan.instalasi_kode::text = 'RI'::text AND sy_klaiminacbg.is_deleted = false
                GROUP BY kunjungan.instalasi_kode, kunjungan.tgl_pulang, (to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date)
                UNION ALL
                SELECT kunjungan.instalasi_kode,
                    to_char(kunjungan.tgl_pulang, 'YYYY-MM-DD'::text)::date AS tgl_keluar,
                    to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date AS tgl_group,
                    0 AS rj,
                    0 AS ri,
                    count(sy_klaiminacbg.is_terkirim) AS blm_kirim,
                    0 AS sdh_kirim
                FROM sy_klaiminacbg
                    LEFT JOIN ( SELECT klaimgroup_t.klaimgroup_id,
                            klaimgroup_t.created_date
                        FROM klaimgroup_t) klaim_group ON sy_klaiminacbg.klaimgroup_id = klaim_group.klaimgroup_id
                    LEFT JOIN ( SELECT sy_kunjungan.kunjungan_id,
                            sy_kunjungan.instalasi_kode,
                            sy_kunjungan.tgl_pulang
                        FROM sy_kunjungan) kunjungan ON sy_klaiminacbg.kunjungan_id = kunjungan.kunjungan_id
                WHERE sy_klaiminacbg.is_terkirim = false AND sy_klaiminacbg.is_deleted = false
                GROUP BY kunjungan.instalasi_kode, kunjungan.tgl_pulang, (to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date)
                UNION ALL
                SELECT kunjungan.instalasi_kode,
                    to_char(kunjungan.tgl_pulang, 'YYYY-MM-DD'::text)::date AS tgl_keluar,
                    to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date AS tgl_group,
                    0 AS rj,
                    0 AS ri,
                    0 AS blm_kirim,
                    count(sy_klaiminacbg.is_terkirim) AS sdh_kirim
                FROM sy_klaiminacbg
                    LEFT JOIN ( SELECT klaimgroup_t.klaimgroup_id,
                            klaimgroup_t.created_date
                        FROM klaimgroup_t) klaim_group ON sy_klaiminacbg.klaimgroup_id = klaim_group.klaimgroup_id
                    LEFT JOIN ( SELECT sy_kunjungan.kunjungan_id,
                            sy_kunjungan.instalasi_kode,
                            sy_kunjungan.tgl_pulang
                        FROM sy_kunjungan) kunjungan ON sy_klaiminacbg.kunjungan_id = kunjungan.kunjungan_id
                WHERE sy_klaiminacbg.is_terkirim = true AND sy_klaiminacbg.is_deleted = false
                GROUP BY kunjungan.instalasi_kode, kunjungan.tgl_pulang, (to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date)) x
        GROUP BY x.instalasi_kode, x.tgl_keluar, x.tgl_group
        ORDER BY x.tgl_keluar;
        ");

        $this->execute("
        DROP VIEW IF EXISTS sy_kunjungan_v;
        ");

        $this->execute("

            CREATE OR REPLACE VIEW public.sy_kunjungan_v
            AS SELECT 'RD'::text AS type,
                pendaftaran_t.pendaftaran_id AS kunjungan_id,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik AS no_rekammedik,
                pasien_m.nama_pasien,
                look_jeniskelamin.jeniskelamin AS jenis_kelamin,
                pasien_m.tanggal_lahir AS tgl_lahir,
                pendaftaran_t.umur,
                pendaftaran_t.tgl_pendaftaran,
                pasienpulang_t.tglpasienpulang AS tgl_pulang,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_singkatan AS instalasi_kode,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_singkatan AS ruangan_kode,
                ruangan_m.ruangan_nama,
                carabayar_m.carabayar_id,
                carabayar_m.carabayar_kode,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_id,
                penjamin_m.penjamin_kode,
                penjamin_m.penjamin_nama,
                kelaspelayanan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_kode AS kelas_kode,
                kelaspelayanan_m.kelaspelayanan_nama AS kelas_nama,
                pegawai_m.pegawai_id AS dokter_id,
                pegawai_m.nomorindukpegawai AS dokter_kode,
                pegawai_m.nama_pegawai AS dokter_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS kasus_penyakit,
                bpjs_t.nosep AS no_sep,
                look_verifikasi.lookup_id AS status_kunjungan_id,
                look_verifikasi.verifikasi AS status_kunjungan,
                pendaftaran_t.additional_data,
                pendaftaran_t.created_date,
                pendaftaran_t.created_by,
                pendaftaran_t.modified_count,
                pendaftaran_t.last_modified_date,
                pendaftaran_t.last_modified_by,
                pendaftaran_t.is_deleted,
                pendaftaran_t.is_active,
                pendaftaran_t.deleted_date,
                pendaftaran_t.deleted_by
            FROM pendaftaran_t
                JOIN ( SELECT a.pasien_id,
                        a.nama_pasien,
                        a.no_rekam_medik,
                        a.jeniskelamin,
                        a.tanggal_lahir
                    FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_kode AS jeniskelamin
                    FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
                JOIN ( SELECT a.pasienpulang_id,
                        a.tglpasienpulang
                    FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                JOIN ( SELECT a.instalasi_id,
                        a.instalasi_singkatan,
                        a.instalasi_nama
                    FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                JOIN ( SELECT a.ruangan_id,
                        a.ruangan_singkatan,
                        a.ruangan_nama
                    FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_kode,
                        a.carabayar_nama
                    FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_kode,
                        a.penjamin_nama
                    FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_kode,
                        a.kelaspelayanan_nama
                    FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nomorindukpegawai,
                        a.nama_pegawai
                    FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
                LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                        a.jeniskasuspenyakit_nama
                    FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                LEFT JOIN ( SELECT a.bpjs_id,
                        a.pendaftaran_id,
                        a.nosep
                    FROM bpjs_t a) bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name AS verifikasi
                    FROM lookup_m a) look_verifikasi ON pendaftaran_t.status_verifikasi = look_verifikasi.lookup_id
            WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND pendaftaran_t.instalasi_id = 2 AND (pendaftaran_t.status_verifikasi = ANY (ARRAY[549, 550])) AND (pendaftaran_t.status_periksa::text <> ALL (ARRAY['402'::text, '411'::text, '628'::text]))
            UNION ALL
            SELECT 'RI'::text AS type,
                pendaftaran_t.pendaftaran_id AS kunjungan_id,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik AS no_rekammedik,
                pasien_m.nama_pasien,
                look_jeniskelamin.jeniskelamin AS jenis_kelamin,
                pasien_m.tanggal_lahir AS tgl_lahir,
                pendaftaran_t.umur,
                pendaftaran_t.tgl_pendaftaran,
                pasienpulang_t.tglpasienpulang AS tgl_pulang,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_singkatan AS instalasi_kode,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_singkatan AS ruangan_kode,
                ruangan_m.ruangan_nama,
                carabayar_m.carabayar_id,
                carabayar_m.carabayar_kode,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_id,
                penjamin_m.penjamin_kode,
                penjamin_m.penjamin_nama,
                kelaspelayanan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_kode AS kelas_kode,
                kelaspelayanan_m.kelaspelayanan_nama AS kelas_nama,
                pegawai_m.pegawai_id AS dokter_id,
                pegawai_m.nomorindukpegawai AS dokter_kode,
                pegawai_m.nama_pegawai AS dokter_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS kasus_penyakit,
                bpjs_t.nosep AS no_sep,
                look_verifikasi.lookup_id AS status_kunjungan_id,
                look_verifikasi.verifikasi AS status_kunjungan,
                pendaftaran_t.additional_data,
                pendaftaran_t.created_date,
                pendaftaran_t.created_by,
                pendaftaran_t.modified_count,
                pendaftaran_t.last_modified_date,
                pendaftaran_t.last_modified_by,
                pendaftaran_t.is_deleted,
                pendaftaran_t.is_active,
                pendaftaran_t.deleted_date,
                pendaftaran_t.deleted_by
            FROM pendaftaran_t
                JOIN ( SELECT a.pasienadmisi_id,
                        a.carabayar_id,
                        a.penjamin_id,
                        a.ruangan_id,
                        a.kelaspelayanan_id,
                        a.pasienpulang_id,
                        a.status_verifikasi,
                        a.status_ranap
                    FROM pasienadmisi_t a
                    WHERE (a.status_verifikasi = ANY (ARRAY[549, 550])) AND (a.status_ranap <> ALL (ARRAY[440, 453]))) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                JOIN ( SELECT a.pasien_id,
                        a.nama_pasien,
                        a.no_rekam_medik,
                        a.jeniskelamin,
                        a.tanggal_lahir
                    FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_kode AS jeniskelamin
                    FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
                JOIN ( SELECT a.pasienpulang_id,
                        a.tglpasienpulang
                    FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                JOIN ( SELECT a.ruangan_id,
                        a.ruangan_singkatan,
                        a.ruangan_nama,
                        a.instalasi_id
                    FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
                JOIN ( SELECT a.instalasi_id,
                        a.instalasi_singkatan,
                        a.instalasi_nama
                    FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_kode,
                        a.carabayar_nama
                    FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
                LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_kode,
                        a.penjamin_nama
                    FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
                LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_kode,
                        a.kelaspelayanan_nama
                    FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nomorindukpegawai,
                        a.nama_pegawai
                    FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
                LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                        a.jeniskasuspenyakit_nama
                    FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                LEFT JOIN ( SELECT a.bpjs_id,
                        a.pendaftaran_id,
                        a.nosep
                    FROM bpjs_t a) bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name AS verifikasi
                    FROM lookup_m a) look_verifikasi ON pasienadmisi_t.status_verifikasi = look_verifikasi.lookup_id
            UNION ALL
            SELECT 'RJ'::text AS type,
                pendaftaran_t.pendaftaran_id AS kunjungan_id,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik AS no_rekammedik,
                pasien_m.nama_pasien,
                look_jeniskelamin.jeniskelamin AS jenis_kelamin,
                pasien_m.tanggal_lahir AS tgl_lahir,
                pendaftaran_t.umur,
                pendaftaran_t.tgl_pendaftaran,
                pasienpulang_t.tglpasienpulang AS tgl_pulang,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_singkatan AS instalasi_kode,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_singkatan AS ruangan_kode,
                ruangan_m.ruangan_nama,
                carabayar_m.carabayar_id,
                carabayar_m.carabayar_kode,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_id,
                penjamin_m.penjamin_kode,
                penjamin_m.penjamin_nama,
                kelaspelayanan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_kode AS kelas_kode,
                kelaspelayanan_m.kelaspelayanan_nama AS kelas_nama,
                pegawai_m.pegawai_id AS dokter_id,
                pegawai_m.nomorindukpegawai AS dokter_kode,
                pegawai_m.nama_pegawai AS dokter_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS kasus_penyakit,
                bpjs_t.nosep AS no_sep,
                look_verifikasi.lookup_id AS status_kunjungan_id,
                look_verifikasi.verifikasi AS status_kunjungan,
                pendaftaran_t.additional_data,
                pendaftaran_t.created_date,
                pendaftaran_t.created_by,
                pendaftaran_t.modified_count,
                pendaftaran_t.last_modified_date,
                pendaftaran_t.last_modified_by,
                pendaftaran_t.is_deleted,
                pendaftaran_t.is_active,
                pendaftaran_t.deleted_date,
                pendaftaran_t.deleted_by
            FROM pendaftaran_t
                JOIN ( SELECT a.pasien_id,
                        a.nama_pasien,
                        a.no_rekam_medik,
                        a.jeniskelamin,
                        a.tanggal_lahir
                    FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_kode AS jeniskelamin
                    FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
                JOIN ( SELECT a.pasienpulang_id,
                        a.tglpasienpulang
                    FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                JOIN ( SELECT a.instalasi_id,
                        a.instalasi_singkatan,
                        a.instalasi_nama
                    FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                JOIN ( SELECT a.ruangan_id,
                        a.ruangan_singkatan,
                        a.ruangan_nama
                    FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_kode,
                        a.carabayar_nama
                    FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_kode,
                        a.penjamin_nama
                    FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_kode,
                        a.kelaspelayanan_nama
                    FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nomorindukpegawai,
                        a.nama_pegawai
                    FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
                LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                        a.jeniskasuspenyakit_nama
                    FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                LEFT JOIN ( SELECT a.bpjs_id,
                        a.pendaftaran_id,
                        a.nosep
                    FROM bpjs_t a) bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name AS verifikasi
                    FROM lookup_m a) look_verifikasi ON pendaftaran_t.status_verifikasi = look_verifikasi.lookup_id
            WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND pendaftaran_t.instalasi_id = 1 AND (pendaftaran_t.status_verifikasi = ANY (ARRAY[549, 550])) AND (pendaftaran_t.status_periksa::text <> ALL (ARRAY['402'::text, '411'::text, '628'::text]));
        ");

        $this->execute("
        DROP VIEW IF EXISTS sy_kunjungandetail_v;
        ");

        $this->execute("
        CREATE OR REPLACE VIEW public.sy_kunjungandetail_v
        AS SELECT sy_kunjungandetail.kunjungandetail_id,
            sy_kunjungandetail.kunjungan_id,
            sy_kunjungandetail.no_pendaftaran,
            sy_kunjungandetail.no_rekammedik,
            sy_kunjungan.nama_pasien,
            sy_kunjungandetail.kelompok_diagnosa,
            sy_kunjungandetail.diagnosa_kode,
            sy_kunjungandetail.diagnosa_nama,
            sy_kunjungandetail.additional_data,
            sy_kunjungandetail.created_date,
            sy_kunjungandetail.created_by,
            sy_kunjungandetail.modified_count,
            sy_kunjungandetail.last_modified_date,
            sy_kunjungandetail.last_modified_by,
            sy_kunjungandetail.is_deleted,
            sy_kunjungandetail.is_active,
            sy_kunjungandetail.deleted_date,
            sy_kunjungandetail.deleted_by
        FROM sy_kunjungandetail
            JOIN sy_kunjungan ON sy_kunjungandetail.kunjungan_id = sy_kunjungan.kunjungan_id
        WHERE sy_kunjungandetail.is_deleted = false;
        ");

        $this->execute("
        DROP VIEW IF EXISTS sy_infopasienbpjsdiagnosa_v;
        ");

        $this->execute("
        CREATE OR REPLACE VIEW public.sy_infopasienbpjsdiagnosa_v
        AS SELECT sy_kunjungan.kunjungan_id,
            koreksi_diagnosa.kelompokdiagnosa_id,
            kelompok_diagnosa.kelompokdiagnosa_nama,
            koreksi_diagnosa.diagnosa_id,
            diagnosa.diagnosa_kode,
            diagnosa.diagnosa_nama,
            koreksi_diagnosa.diagnosaasal_id,
            koreksi_diagnosa.diag_asal_masuk,
            koreksi_diagnosa.diag_asal_utama,
            koreksi_diagnosa.diag_asal_terapi,
            koreksi_diagnosa.diag_asal_penyerta,
            koreksi_diagnosa.is_inacbg,
            koreksi_diagnosa.is_icdprimer,
            koreksi_diagnosa.is_deleted
        FROM sy_kunjungan
            JOIN ( SELECT a.kunjungan_id,
                    a.kelompokdiagnosa_id,
                    a.diagnosa_id,
                    a.diagnosaasal_id,
                    a.diag_asal_masuk,
                    a.diag_asal_utama,
                    a.diag_asal_terapi,
                    a.diag_asal_penyerta,
                    a.is_inacbg,
                    a.is_icdprimer,
                    a.is_deleted
                FROM sy_koreksidiagnosa a) koreksi_diagnosa ON sy_kunjungan.kunjungan_id = koreksi_diagnosa.kunjungan_id
            JOIN ( SELECT a.kelompokdiagnosa_id,
                    a.kelompokdiagnosa_nama
                FROM kelompokdiagnosa_m a) kelompok_diagnosa ON kelompok_diagnosa.kelompokdiagnosa_id = koreksi_diagnosa.kelompokdiagnosa_id
            JOIN ( SELECT a.diagnosa_id,
                    a.diagnosa_nama,
                    a.diagnosa_kode
                FROM diagnosa_m a) diagnosa ON diagnosa.diagnosa_id = koreksi_diagnosa.diagnosa_id
        WHERE sy_kunjungan.is_active = true AND sy_kunjungan.is_deleted = false;
        ");

        $this->execute("
        DROP VIEW IF EXISTS sy_infopasienbpjsklaim_v;
        ");

        $this->execute("
        CREATE OR REPLACE VIEW public.sy_infopasienbpjsklaim_v
        AS SELECT sy_kunjungantagihan.kunjungantagihan_id,
            sy_kunjungantagihan.kunjungan_id,
            sy_kunjungan.no_pendaftaran,
            sy_kunjungan.no_rekammedik,
            sy_kunjungan.instalasi_kode,
            sy_kunjungan.tgl_pendaftaran,
            sy_kunjungan.tgl_pulang,
            sy_kunjungantagihan.ruangan_kode,
            sy_kunjungantagihan.ruangan_nama,
            sy_kunjungantagihan.dokter_kode,
            sy_kunjungantagihan.dokter_nama,
            sy_kunjungantagihan.layanan_qty,
            sy_kunjungantagihan.layanan_kode,
            sy_kunjungantagihan.layanan_nama,
            sy_kunjungantagihan.layanan_tarif,
            sy_kunjungantagihan.jasa_rs,
            sy_kunjungantagihan.jasa_dokter,
            sy_kunjungantagihan.created_date,
            sy_kunjungantagihan.groupinacbg_nama
        FROM sy_kunjungantagihan
            JOIN ( SELECT a.kunjungan_id,
                    a.no_pendaftaran,
                    a.no_rekammedik,
                    a.instalasi_kode,
                    a.tgl_pendaftaran,
                    a.tgl_pulang
                FROM sy_kunjungan a) sy_kunjungan ON sy_kunjungan.kunjungan_id = sy_kunjungantagihan.kunjungan_id
        WHERE sy_kunjungantagihan.is_active = true AND sy_kunjungantagihan.is_deleted = false;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230306_075851_create_view_sy_klaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_075851_create_view_sy_klaim cannot be reverted.\n";

        return false;
    }
    */
}
