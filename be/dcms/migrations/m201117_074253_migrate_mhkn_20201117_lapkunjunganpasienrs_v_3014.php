<?php

use yii\db\Migration;

/**
 * Class m201117_074253_migrate_mhkn_20201117_lapkunjunganpasienrs_v_3014
 */
class m201117_074253_migrate_mhkn_20201117_lapkunjunganpasienrs_v_3014 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.lapkunjunganpasrs_v;');

        $this->execute('DROP VIEW if exists public.lapkunjunganpasienrs_v;');

        $this->execute('
            CREATE VIEW "public"."lapkunjunganpasienrs_v" AS  SELECT \'a\'::text AS ket,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                pasien_m.jeniskelamin,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
                        ELSE pasienadmisi_t.carabayar_id
                    END AS carabayar_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
                        ELSE pasienadmisi_t.penjamin_id
                    END AS penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.instalasi_id
                        ELSE ruang_admisi.instalasi_id
                    END AS instalasi_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
                        ELSE pasienadmisi_t.ruangan_id
                    END AS ruangan_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pegawai_id
                        ELSE pasienadmisi_t.pegawai_id
                    END AS dokterdpjp_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.tgl_pendaftaran
                        ELSE pasienadmisi_t.tgl_pendaftaran
                    END AS tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.instalasi_id AS instalasi_asal,
                pasien_m.no_rekam_medik,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                pasien_m.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                pasien_m.alamat_pasien,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_m.carabayar_nama
                        ELSE cb_admisi.carabayar_nama
                    END AS carabayar_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_m.penjamin_nama
                        ELSE pj_admisi.penjamin_nama
                    END AS penjamin_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_m.instalasi_nama
                        ELSE instalasi_admisi.instalasi_nama
                    END AS instalasi_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_m.ruangan_nama
                        ELSE ruang_admisi.ruangan_nama
                    END AS ruangan_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
                        ELSE peg_admisi.nama_pegawai
                    END AS dokterdpjp_nama,
                concat(d_utama.diag_utama_kode, \'. \', d_utama.diag_utama) AS diagnosa_utama,
                d_utama.diag_utama_id AS diagnosa_utama_id,
                concat(d_penyerta.diag_penyerta_kode, \'. \', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
                d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id,
                pendaftaran_t.status_periksa AS id_status_periksa,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN fgetnamalookup((pendaftaran_t.status_periksa)::integer)
                        ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
                    END AS status_periksa,
                    CASE
                        WHEN ((pendaftaran_t.status_periksa)::text = (4)::text) THEN \'t\'::text
                        WHEN ((pendaftaran_t.status_periksa)::text = (433)::text) THEN \'t\'::text
                        ELSE \'f\'::text
                    END AS is_status_periksa,
                pasien_m.no_telepon_pasien,
                pasien_m.no_mobile_pasien,
                ( SELECT count(tt.pasien_id) AS jumlah_pasien
                       FROM pendaftaran_t tt
                      WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) AS jumlah,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) = 1) THEN 180
                        ELSE 181
                    END AS kunjungan_id,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) = 1) THEN \'Kunjungan Baru\'::text
                        ELSE \'Kunjungan Lama\'::text
                    END AS kunjungan_nama,
                kondisikeluar_m.kondisikeluar_id,
                kondisikeluar_m.kondisikeluar_nama,
                carakeluar_m.carakeluar_id,
                carakeluar_m.carakeluar_nama
               FROM ((((((((((((((((((pendaftaran_t
                 LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN carabayar_m cb_admisi ON ((pasienadmisi_t.carabayar_id = cb_admisi.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN penjamin_m pj_admisi ON ((pasienadmisi_t.penjamin_id = pj_admisi.penjamin_id)))
                 LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id))) 
                 LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN ruangan_m ruang_admisi ON ((pasienadmisi_t.ruangan_id = ruang_admisi.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN instalasi_m instalasi_admisi ON ((ruang_admisi.instalasi_id = instalasi_admisi.instalasi_id)))
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
                 LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN pegawai_m peg_admisi ON ((pasienadmisi_t.pegawai_id = peg_admisi.pegawai_id)))
                 LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
                 LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
                 LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
              WHERE (((pendaftaran_t.status_periksa)::text <> \'402\'::text) AND (pendaftaran_t.instalasi_id <> 2) AND ((pendaftaran_t.status_periksa)::text <> \'628\'::text) AND ((pendaftaran_t.status_periksa)::text <> \'453\'::text) AND ((pasienadmisi_t.status_ranap)::text <> \'453\'::text))
            UNION ALL
             SELECT \'mcu\'::text AS ket,
                pendaftaran_t.pendaftaran_id,
                NULL::integer AS pasienadmisi_id,
                pasien_m.jeniskelamin,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.pegawai_id AS dokterdpjp_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.instalasi_id AS instalasi_asal,
                pasien_m.no_rekam_medik,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                pasien_m.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                pasien_m.alamat_pasien,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama,
                pegawai_m.nama_pegawai AS dokterdpjp_nama,
                concat(d_utama.diag_utama_kode, \'. \', d_utama.diag_utama) AS diagnosa_utama,
                d_utama.diag_utama_id AS diagnosa_utama_id,
                concat(d_penyerta.diag_penyerta_kode, \'. \', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
                d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id,
                pendaftaran_t.status_periksa AS id_status_periksa,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
                    CASE
                        WHEN ((pendaftaran_t.status_periksa)::text = (4)::text) THEN \'t\'::text
                        WHEN ((pendaftaran_t.status_periksa)::text = (433)::text) THEN \'t\'::text
                        ELSE \'f\'::text
                    END AS is_status_periksa,
                pasien_m.no_telepon_pasien,
                pasien_m.no_mobile_pasien,
                ( SELECT count(tt.pasien_id) AS jumlah_pasien
                       FROM pendaftaran_t tt
                      WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) AS jumlah,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) = 1) THEN 180
                        ELSE 181
                    END AS kunjungan_id,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) = 1) THEN \'Kunjungan Baru\'::text
                        ELSE \'Kunjungan Lama\'::text
                    END AS kunjungan_nama,
                kondisikeluar_m.kondisikeluar_id,
                kondisikeluar_m.kondisikeluar_nama,
                carakeluar_m.carakeluar_id,
                carakeluar_m.carakeluar_nama
               FROM ((((((((((((pendaftaran_t
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
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
                      WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 3) AND (koreksidiagnosa_t.is_deleted = false) AND (koreksidiagnosa_t.pasienadmisi_id IS NULL))
                      GROUP BY koreksidiagnosa_t.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id)))
                 LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN pasienpulang_t ON (((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id) AND (pasienpulang_t.pasienadmisi_id IS NULL))))
                 LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
                 LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
              WHERE (((pendaftaran_t.status_periksa)::text <> \'402\'::text) AND (pendaftaran_t.instalasi_id <> 2) AND ((pendaftaran_t.status_periksa)::text <> \'628\'::text) AND ((pendaftaran_t.status_periksa)::text <> \'453\'::text) AND (pendaftaran_t.pasienadmisi_id IS NOT NULL) AND (pendaftaran_t.pegawai_id IS NOT NULL))
            UNION ALL
             SELECT \'b\'::text AS ket,
                pendaftaran_t.pendaftaran_id,
                NULL::integer AS pasienadmisi_id,
                pasien_m.jeniskelamin,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.pegawai_id AS dokterdpjp_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.instalasi_id AS instalasi_asal,
                pasien_m.no_rekam_medik,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                pasien_m.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                pasien_m.alamat_pasien,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama,
                pegawai_m.nama_pegawai AS dokterdpjp_nama,
                concat(d_utama.diag_utama_kode, \'. \', d_utama.diag_utama) AS diagnosa_utama,
                d_utama.diag_utama_id AS diagnosa_utama_id,
                concat(d_penyerta.diag_penyerta_kode, \'. \', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
                d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id,
                pendaftaran_t.status_periksa AS id_status_periksa,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
                    CASE
                        WHEN ((pendaftaran_t.status_periksa)::text = (4)::text) THEN \'t\'::text
                        WHEN ((pendaftaran_t.status_periksa)::text = (433)::text) THEN \'t\'::text
                        ELSE \'f\'::text
                    END AS is_status_periksa,
                pasien_m.no_telepon_pasien,
                pasien_m.no_mobile_pasien,
                ( SELECT count(tt.pasien_id) AS jumlah_pasien
                       FROM pendaftaran_t tt
                      WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) AS jumlah,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) = 1) THEN 180
                        ELSE 181
                    END AS kunjungan_id,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) = 1) THEN \'Kunjungan Baru\'::text
                        ELSE \'Kunjungan Lama\'::text
                    END AS kunjungan_nama,
                kondisikeluar_m.kondisikeluar_id,
                kondisikeluar_m.kondisikeluar_nama,
                carakeluar_m.carakeluar_id,
                carakeluar_m.carakeluar_nama
               FROM ((((((((((((pendaftaran_t
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
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
                      WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 3) AND (koreksidiagnosa_t.is_deleted = false) AND (koreksidiagnosa_t.pasienadmisi_id IS NULL))
                      GROUP BY koreksidiagnosa_t.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id)))
                 LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN pasienpulang_t ON (((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id) AND (pasienpulang_t.pasienadmisi_id IS NULL))))
                 LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
                 LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
              WHERE (((pendaftaran_t.status_periksa)::text <> \'402\'::text) AND (pendaftaran_t.instalasi_id <> 2) AND ((pendaftaran_t.status_periksa)::text <> \'628\'::text) AND ((pendaftaran_t.status_periksa)::text <> \'453\'::text) AND (pendaftaran_t.pegawai_id IS NOT NULL))
            UNION ALL
             SELECT \'mcuRD\'::text AS ket,
                pendaftaran_t.pendaftaran_id,
                NULL::integer AS pasienadmisi_id,
                pasien_m.jeniskelamin,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.pegawai_id AS dokterdpjp_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.instalasi_id AS instalasi_asal,
                pasien_m.no_rekam_medik,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                pasien_m.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                pasien_m.alamat_pasien,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama,
                pegawai_m.nama_pegawai AS dokterdpjp_nama,
                concat(d_utama.diag_utama_kode, \'. \', d_utama.diag_utama) AS diagnosa_utama,
                d_utama.diag_utama_id AS diagnosa_utama_id,
                concat(d_penyerta.diag_penyerta_kode, \'. \', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
                d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id,
                pendaftaran_t.status_periksa AS id_status_periksa,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
                    CASE
                        WHEN ((pendaftaran_t.status_periksa)::text = (4)::text) THEN \'t\'::text
                        WHEN ((pendaftaran_t.status_periksa)::text = (433)::text) THEN \'t\'::text
                        ELSE \'f\'::text
                    END AS is_status_periksa,
                pasien_m.no_telepon_pasien,
                pasien_m.no_mobile_pasien,
                ( SELECT count(tt.pasien_id) AS jumlah_pasien
                       FROM pendaftaran_t tt
                      WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) AS jumlah,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) = 1) THEN 180
                        ELSE 181
                    END AS kunjungan_id,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pendaftaran_t.ruangan_id))) = 1) THEN \'Kunjungan Baru\'::text
                        ELSE \'Kunjungan Lama\'::text
                    END AS kunjungan_nama,
                kondisikeluar_m.kondisikeluar_id,
                kondisikeluar_m.kondisikeluar_nama,
                carakeluar_m.carakeluar_id,
                carakeluar_m.carakeluar_nama
               FROM ((((((((((((pendaftaran_t
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
                        diagnosa_m.diagnosa_id AS diag_utama_id,
                        diagnosa_m.diagnosa_kode AS diag_utama_kode,
                        diagnosa_m.diagnosa_nama AS diag_utama
                       FROM ((koreksidiagnosa_t
                         JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
                                pk.pendaftaran_id,
                                pk.diagnosa_id
                               FROM koreksidiagnosa_t pk
                              WHERE ((pk.kelompokdiagnosa_id = 2) AND (pk.is_deleted = false) AND (pk.pasienadmisi_id IS NULL))
                              GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
                         JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
                      WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 2) AND (koreksidiagnosa_t.is_deleted = false))) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
                 LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
                        diagnosa_m.diagnosa_id AS diag_penyerta_id,
                        diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
                        diagnosa_m.diagnosa_nama AS diag_penyerta
                       FROM (koreksidiagnosa_t
                         JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
                      WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 3) AND (koreksidiagnosa_t.is_deleted = false) AND (koreksidiagnosa_t.pasienadmisi_id IS NULL))
                      GROUP BY koreksidiagnosa_t.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id)))
                 LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN pasienpulang_t ON (((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id) AND (pasienpulang_t.pasienadmisi_id IS NULL))))
                 LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
                 LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
              WHERE (((pendaftaran_t.status_periksa)::text <> \'402\'::text) AND (pendaftaran_t.instalasi_id = 2) AND ((pendaftaran_t.status_periksa)::text <> \'628\'::text) AND ((pendaftaran_t.status_periksa)::text <> \'453\'::text) AND (pendaftaran_t.pegawai_id IS NOT NULL))
            UNION ALL
             SELECT \'RDRI\'::text AS ket,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                pasien_m.jeniskelamin,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
                        ELSE pasienadmisi_t.carabayar_id
                    END AS carabayar_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
                        ELSE pasienadmisi_t.penjamin_id
                    END AS penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.instalasi_id
                        ELSE ruang_admisi.instalasi_id
                    END AS instalasi_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
                        ELSE pasienadmisi_t.ruangan_id
                    END AS ruangan_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pegawai_id
                        ELSE pasienadmisi_t.pegawai_id
                    END AS dokterdpjp_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.tgl_pendaftaran
                        ELSE pasienadmisi_t.tgl_pendaftaran
                    END AS tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.instalasi_id AS instalasi_asal,
                pasien_m.no_rekam_medik,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                pasien_m.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                pasien_m.alamat_pasien,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_m.carabayar_nama
                        ELSE cb_admisi.carabayar_nama
                    END AS carabayar_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_m.penjamin_nama
                        ELSE pj_admisi.penjamin_nama
                    END AS penjamin_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_m.instalasi_nama
                        ELSE instalasi_admisi.instalasi_nama
                    END AS instalasi_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_m.ruangan_nama
                        ELSE ruang_admisi.ruangan_nama
                    END AS ruangan_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
                        ELSE peg_admisi.nama_pegawai
                    END AS dokterdpjp_nama,
                concat(d_utama.diag_utama_kode, \'. \', d_utama.diag_utama) AS diagnosa_utama,
                d_utama.diag_utama_id AS diagnosa_utama_id,
                concat(d_penyerta.diag_penyerta_kode, \'. \', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
                d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id,
                pendaftaran_t.status_periksa AS id_status_periksa,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN fgetnamalookup((pendaftaran_t.status_periksa)::integer)
                        ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
                    END AS status_periksa,
                    CASE
                        WHEN ((pendaftaran_t.status_periksa)::text = (4)::text) THEN \'t\'::text
                        WHEN ((pendaftaran_t.status_periksa)::text = (433)::text) THEN \'t\'::text
                        ELSE \'f\'::text
                    END AS is_status_periksa,
                pasien_m.no_telepon_pasien,
                pasien_m.no_mobile_pasien,
                ( SELECT count(tt.pasien_id) AS jumlah_pasien
                       FROM pasienadmisi_t tt
                      WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pasienadmisi_t.ruangan_id))) AS jumlah,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pasienadmisi_t tt
                          WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pasienadmisi_t.ruangan_id))) = 1) THEN 180
                        ELSE 181
                    END AS kunjungan_id,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pasienadmisi_t tt
                          WHERE ((tt.pasien_id = pasien_m.pasien_id) AND (tt.ruangan_id = pasienadmisi_t.ruangan_id))) = 1) THEN \'Kunjungan Baru\'::text
                        ELSE \'Kunjungan Lama\'::text
                    END AS kunjungan_nama,
                kondisikeluar_m.kondisikeluar_id,
                kondisikeluar_m.kondisikeluar_nama,
                carakeluar_m.carakeluar_id,
                carakeluar_m.carakeluar_nama
               FROM ((((((((((((((((((pendaftaran_t
                 LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN carabayar_m cb_admisi ON ((pasienadmisi_t.carabayar_id = cb_admisi.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN penjamin_m pj_admisi ON ((pasienadmisi_t.penjamin_id = pj_admisi.penjamin_id)))
                 LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN ruangan_m ruang_admisi ON ((pasienadmisi_t.ruangan_id = ruang_admisi.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN instalasi_m instalasi_admisi ON ((ruang_admisi.instalasi_id = instalasi_admisi.instalasi_id)))
                 LEFT JOIN ( SELECT koreksidiagnosa_t.pasienadmisi_id,
                        diagnosa_m.diagnosa_id AS diag_utama_id,
                        diagnosa_m.diagnosa_kode AS diag_utama_kode,
                        diagnosa_m.diagnosa_nama AS diag_utama
                       FROM ((koreksidiagnosa_t
                         JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
                                pk.pasienadmisi_id,
                                pk.diagnosa_id
                               FROM koreksidiagnosa_t pk
                              WHERE ((pk.kelompokdiagnosa_id = 2) AND (pk.is_deleted = false))
                              GROUP BY pk.pasienadmisi_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
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
                 LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN pegawai_m peg_admisi ON ((pasienadmisi_t.pegawai_id = peg_admisi.pegawai_id)))
                 LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id)))
                 LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
                 LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
              WHERE (((pendaftaran_t.status_periksa)::text <> \'402\'::text) AND (pendaftaran_t.instalasi_id = 2) AND ((pendaftaran_t.status_periksa)::text <> \'628\'::text) AND ((pendaftaran_t.status_periksa)::text <> \'453\'::text) AND ((pasienadmisi_t.status_ranap)::text <> \'453\'::text));    
        ;');

        $this->execute('ALTER TABLE public.lapkunjunganpasienrs_v
    OWNER TO postgres;');

        $this->execute('
            CREATE VIEW "public"."lapkunjunganpasrs_v" AS  
            SELECT kunjungan.pendaftaran_id,
                kunjungan.pasienadmisi_id,
                kunjungan.tgl_pendaftaran, 
                kunjungan.no_pendaftaran,
                kunjungan.no_rekam_medik,
                kunjungan.nama_pasien,
                kunjungan.jenis_kelamin,
                kunjungan.tanggal_lahir,
                kunjungan.umur,
                kunjungan.alamat_pasien,
                kunjungan.carabayar_id,
                kunjungan.carabayar_nama,
                kunjungan.penjamin_id,
                kunjungan.penjamin_nama,
                kunjungan.jeniskasuspenyakit_id,
                kunjungan.jeniskasuspenyakit_nama,
                kunjungan.instalasi_id,
                kunjungan.instalasi_nama,
                kunjungan.instalasi_asal,
                kunjungan.ruangan_id,
                kunjungan.ruangan_nama,
                kunjungan.dokterdpjp_id,
                kunjungan.dokterdpjp_nama,
                kunjungan.diagnosa_utama_id,
                kunjungan.diagnosa_utama,
                joined_penyerta.diagnosa_penyerta_id,
                joined_penyerta.diagnosa_penyerta,
                kunjungan.status_periksa,
                kunjungan.id_status_periksa,
                kunjungan.is_status_periksa,
                kunjungan.jeniskelamin,
                kunjungan.no_telepon_pasien,
                kunjungan.no_mobile_pasien,
                kunjungan.kunjungan_id,
                kunjungan.kunjungan_nama,
                kunjungan.kondisikeluar_id,
                kunjungan.kondisikeluar_nama,
                kunjungan.carakeluar_id,
                kunjungan.carakeluar_nama
               FROM (lapkunjunganpasienrs_v kunjungan
                 JOIN ( SELECT penyerta.pendaftaran_id,
                        penyerta.pasienadmisi_id,
                        string_agg((penyerta.diagnosa_penyerta_id)::text, \'$\'::text) AS diagnosa_penyerta_id,
                        string_agg(penyerta.diagnosa_penyerta, \'$\'::text) AS diagnosa_penyerta
                       FROM lapkunjunganpasienrs_v penyerta
                      GROUP BY penyerta.pendaftaran_id, penyerta.pasienadmisi_id) joined_penyerta ON (((joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id) AND (joined_penyerta.pasienadmisi_id = kunjungan.pasienadmisi_id))))
              GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, kunjungan.no_pendaftaran, kunjungan.no_rekam_medik, kunjungan.nama_pasien, kunjungan.jenis_kelamin, kunjungan.tanggal_lahir, kunjungan.umur, kunjungan.alamat_pasien, kunjungan.carabayar_id, kunjungan.carabayar_nama, kunjungan.penjamin_id, kunjungan.penjamin_nama, kunjungan.jeniskasuspenyakit_id, kunjungan.jeniskasuspenyakit_nama, kunjungan.instalasi_id, kunjungan.instalasi_nama, kunjungan.instalasi_asal, kunjungan.ruangan_id, kunjungan.ruangan_nama, kunjungan.dokterdpjp_id, kunjungan.dokterdpjp_nama, kunjungan.diagnosa_utama_id, kunjungan.diagnosa_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, kunjungan.status_periksa, kunjungan.id_status_periksa, kunjungan.is_status_periksa, kunjungan.jeniskelamin, kunjungan.no_telepon_pasien, kunjungan.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, kunjungan.kondisikeluar_id, kunjungan.kondisikeluar_nama, kunjungan.carakeluar_id, kunjungan.carakeluar_nama
            UNION ALL
             SELECT kunjungan.pendaftaran_id,
                kunjungan.pasienadmisi_id,
                kunjungan.tgl_pendaftaran,
                kunjungan.no_pendaftaran,
                kunjungan.no_rekam_medik,
                kunjungan.nama_pasien,
                kunjungan.jenis_kelamin,
                kunjungan.tanggal_lahir,
                kunjungan.umur,
                kunjungan.alamat_pasien,
                kunjungan.carabayar_id,
                kunjungan.carabayar_nama,
                kunjungan.penjamin_id,
                kunjungan.penjamin_nama,
                kunjungan.jeniskasuspenyakit_id,
                kunjungan.jeniskasuspenyakit_nama,
                kunjungan.instalasi_id,
                kunjungan.instalasi_nama,
                kunjungan.instalasi_asal,
                kunjungan.ruangan_id,
                kunjungan.ruangan_nama,
                kunjungan.dokterdpjp_id,
                kunjungan.dokterdpjp_nama,
                kunjungan.diagnosa_utama_id,
                kunjungan.diagnosa_utama,
                joined_penyerta.diagnosa_penyerta_id,
                joined_penyerta.diagnosa_penyerta,
                kunjungan.status_periksa,
                kunjungan.id_status_periksa,
                kunjungan.is_status_periksa,
                kunjungan.jeniskelamin,
                kunjungan.no_telepon_pasien,
                kunjungan.no_mobile_pasien,
                kunjungan.kunjungan_id,
                kunjungan.kunjungan_nama,
                kunjungan.kondisikeluar_id,
                kunjungan.kondisikeluar_nama,
                kunjungan.carakeluar_id,
                kunjungan.carakeluar_nama
               FROM (lapkunjunganpasienrs_v kunjungan
                 JOIN ( SELECT penyerta.pendaftaran_id,
                        penyerta.pasienadmisi_id,
                        string_agg((penyerta.diagnosa_penyerta_id)::text, \'$\'::text) AS diagnosa_penyerta_id,
                        string_agg(penyerta.diagnosa_penyerta, \'$\'::text) AS diagnosa_penyerta
                       FROM lapkunjunganpasienrs_v penyerta
                      WHERE (penyerta.pasienadmisi_id IS NULL)
                      GROUP BY penyerta.pendaftaran_id, penyerta.pasienadmisi_id) joined_penyerta ON ((joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id)))
              WHERE (kunjungan.instalasi_id = 2)
              GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, kunjungan.no_pendaftaran, kunjungan.no_rekam_medik, kunjungan.nama_pasien, kunjungan.jenis_kelamin, kunjungan.tanggal_lahir, kunjungan.umur, kunjungan.alamat_pasien, kunjungan.carabayar_id, kunjungan.carabayar_nama, kunjungan.penjamin_id, kunjungan.penjamin_nama, kunjungan.jeniskasuspenyakit_id, kunjungan.jeniskasuspenyakit_nama, kunjungan.instalasi_id, kunjungan.instalasi_nama, kunjungan.instalasi_asal, kunjungan.ruangan_id, kunjungan.ruangan_nama, kunjungan.dokterdpjp_id, kunjungan.dokterdpjp_nama, kunjungan.diagnosa_utama_id, kunjungan.diagnosa_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, kunjungan.status_periksa, kunjungan.id_status_periksa, kunjungan.is_status_periksa, kunjungan.jeniskelamin, kunjungan.no_telepon_pasien, kunjungan.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, kunjungan.kondisikeluar_id, kunjungan.kondisikeluar_nama, kunjungan.carakeluar_id, kunjungan.carakeluar_nama
            UNION ALL
             SELECT kunjungan.pendaftaran_id,
                kunjungan.pasienadmisi_id,
                kunjungan.tgl_pendaftaran,
                kunjungan.no_pendaftaran,
                kunjungan.no_rekam_medik,
                kunjungan.nama_pasien,
                kunjungan.jenis_kelamin,
                kunjungan.tanggal_lahir,
                kunjungan.umur,
                kunjungan.alamat_pasien,
                kunjungan.carabayar_id,
                kunjungan.carabayar_nama,
                kunjungan.penjamin_id,
                kunjungan.penjamin_nama,
                kunjungan.jeniskasuspenyakit_id,
                kunjungan.jeniskasuspenyakit_nama,
                kunjungan.instalasi_id,
                kunjungan.instalasi_nama,
                kunjungan.instalasi_asal,
                kunjungan.ruangan_id,
                kunjungan.ruangan_nama,
                kunjungan.dokterdpjp_id,
                kunjungan.dokterdpjp_nama,
                kunjungan.diagnosa_utama_id,
                kunjungan.diagnosa_utama,
                joined_penyerta.diagnosa_penyerta_id,
                joined_penyerta.diagnosa_penyerta,
                kunjungan.status_periksa,
                kunjungan.id_status_periksa,
                kunjungan.is_status_periksa,
                kunjungan.jeniskelamin,
                kunjungan.no_telepon_pasien,
                kunjungan.no_mobile_pasien,
                kunjungan.kunjungan_id,
                kunjungan.kunjungan_nama,
                kunjungan.kondisikeluar_id,
                kunjungan.kondisikeluar_nama,
                kunjungan.carakeluar_id,
                kunjungan.carakeluar_nama
               FROM (lapkunjunganpasienrs_v kunjungan
                 JOIN ( SELECT penyerta.pendaftaran_id,
                        penyerta.pasienadmisi_id,
                        string_agg((penyerta.diagnosa_penyerta_id)::text, \'$\'::text) AS diagnosa_penyerta_id,
                        string_agg(penyerta.diagnosa_penyerta, \'$\'::text) AS diagnosa_penyerta
                       FROM lapkunjunganpasienrs_v penyerta
                      WHERE (penyerta.pasienadmisi_id IS NULL)
                      GROUP BY penyerta.pendaftaran_id, penyerta.pasienadmisi_id) joined_penyerta ON ((joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id)))
              WHERE (kunjungan.instalasi_id = 1)
              GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, kunjungan.no_pendaftaran, kunjungan.no_rekam_medik, kunjungan.nama_pasien, kunjungan.jenis_kelamin, kunjungan.tanggal_lahir, kunjungan.umur, kunjungan.alamat_pasien, kunjungan.carabayar_id, kunjungan.carabayar_nama, kunjungan.penjamin_id, kunjungan.penjamin_nama, kunjungan.jeniskasuspenyakit_id, kunjungan.jeniskasuspenyakit_nama, kunjungan.instalasi_id, kunjungan.instalasi_nama, kunjungan.instalasi_asal, kunjungan.ruangan_id, kunjungan.ruangan_nama, kunjungan.dokterdpjp_id, kunjungan.dokterdpjp_nama, kunjungan.diagnosa_utama_id, kunjungan.diagnosa_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, kunjungan.status_periksa, kunjungan.id_status_periksa, kunjungan.is_status_periksa, kunjungan.jeniskelamin, kunjungan.no_telepon_pasien, kunjungan.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, kunjungan.kondisikeluar_id, kunjungan.kondisikeluar_nama, kunjungan.carakeluar_id, kunjungan.carakeluar_nama
            UNION ALL
             SELECT kunjungan.pendaftaran_id,
                kunjungan.pasienadmisi_id,
                kunjungan.tgl_pendaftaran,
                kunjungan.no_pendaftaran,
                kunjungan.no_rekam_medik,
                kunjungan.nama_pasien,
                kunjungan.jenis_kelamin,
                kunjungan.tanggal_lahir,
                kunjungan.umur,
                kunjungan.alamat_pasien,
                kunjungan.carabayar_id,
                kunjungan.carabayar_nama,
                kunjungan.penjamin_id,
                kunjungan.penjamin_nama,
                kunjungan.jeniskasuspenyakit_id,
                kunjungan.jeniskasuspenyakit_nama,
                kunjungan.instalasi_id,
                kunjungan.instalasi_nama,
                kunjungan.instalasi_asal,
                kunjungan.ruangan_id,
                kunjungan.ruangan_nama,
                kunjungan.dokterdpjp_id,
                kunjungan.dokterdpjp_nama,
                kunjungan.diagnosa_utama_id,
                kunjungan.diagnosa_utama,
                joined_penyerta.diagnosa_penyerta_id,
                joined_penyerta.diagnosa_penyerta,
                kunjungan.status_periksa,
                kunjungan.id_status_periksa,
                kunjungan.is_status_periksa,
                kunjungan.jeniskelamin,
                kunjungan.no_telepon_pasien,
                kunjungan.no_mobile_pasien,
                kunjungan.kunjungan_id,
                kunjungan.kunjungan_nama,
                kunjungan.kondisikeluar_id,
                kunjungan.kondisikeluar_nama,
                kunjungan.carakeluar_id,
                kunjungan.carakeluar_nama
               FROM (lapkunjunganpasienrs_v kunjungan
                 JOIN ( SELECT penyerta.pendaftaran_id,
                        penyerta.pasienadmisi_id,
                        string_agg((penyerta.diagnosa_penyerta_id)::text, \'$\'::text) AS diagnosa_penyerta_id,
                        string_agg(penyerta.diagnosa_penyerta, \'$\'::text) AS diagnosa_penyerta
                       FROM lapkunjunganpasienrs_v penyerta
                      WHERE (penyerta.pasienadmisi_id IS NULL)
                      GROUP BY penyerta.pendaftaran_id, penyerta.pasienadmisi_id) joined_penyerta ON ((joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id)))
              WHERE (kunjungan.instalasi_id <> ALL (ARRAY[1, 2, 3]))
              GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, kunjungan.no_pendaftaran, kunjungan.no_rekam_medik, kunjungan.nama_pasien, kunjungan.jenis_kelamin, kunjungan.tanggal_lahir, kunjungan.umur, kunjungan.alamat_pasien, kunjungan.carabayar_id, kunjungan.carabayar_nama, kunjungan.penjamin_id, kunjungan.penjamin_nama, kunjungan.jeniskasuspenyakit_id, kunjungan.jeniskasuspenyakit_nama, kunjungan.instalasi_id, kunjungan.instalasi_nama, kunjungan.instalasi_asal, kunjungan.ruangan_id, kunjungan.ruangan_nama, kunjungan.dokterdpjp_id, kunjungan.dokterdpjp_nama, kunjungan.diagnosa_utama_id, kunjungan.diagnosa_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, kunjungan.status_periksa, kunjungan.id_status_periksa, kunjungan.is_status_periksa, kunjungan.jeniskelamin, kunjungan.no_telepon_pasien, kunjungan.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, kunjungan.kondisikeluar_id, kunjungan.kondisikeluar_nama, kunjungan.carakeluar_id, kunjungan.carakeluar_nama;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201117_074253_migrate_mhkn_20201117_lapkunjunganpasienrs_v_3014 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201117_074253_migrate_mhkn_20201117_lapkunjunganpasienrs_v_3014 cannot be reverted.\n";

        return false;
    }
    */
}
