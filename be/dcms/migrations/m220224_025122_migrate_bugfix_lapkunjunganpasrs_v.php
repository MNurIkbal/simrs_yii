<?php

use yii\db\Migration;

/**
 * Class m220224_025122_migrate_bugfix_lapkunjunganpasrs_v
 */
class m220224_025122_migrate_bugfix_lapkunjunganpasrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."lapkunjunganpasrs_v";');
        $this->execute("CREATE VIEW \"public\".\"lapkunjunganpasrs_v\" AS  SELECT kunjungan.pendaftaran_id,
        kunjungan.pasienadmisi_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.tgl_pendaftaran
                ELSE pasienadmisi_t.tgl_pendaftaran
            END AS tgl_pendaftaran,
        kunjungan.no_pendaftaran,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        pasien_m.jenis_kelamin,
        pasien_m.tanggal_lahir,
        kunjungan.umur,
        pasien_m.alamat_pasien,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.carabayar_id
                ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN carabayar_m.carabayar_nama
                ELSE carabayar_ri.carabayar_nama
            END AS carabayar_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.penjamin_id
                ELSE pasienadmisi_t.penjamin_id
            END AS penjamin_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN penjamin_m.penjamin_nama
                ELSE penjamin_ri.penjamin_nama
            END AS penjamin_nama,
        kunjungan.jeniskasuspenyakit_id,
        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.instalasi_id
                ELSE ruangan_ri.instalasi_id
            END AS instalasi_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN instalasi_m.instalasi_nama
                ELSE instalasi_ri.instalasi_nama
            END AS instalasi_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.instalasi_id
                ELSE ruangan_ri.instalasi_id
            END AS instalasi_asal,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.ruangan_id
                ELSE pasienadmisi_t.ruangan_id
            END AS ruangan_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN ruangan_m.ruangan_nama
                ELSE ruangan_ri.ruangan_nama
            END AS ruangan_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.pegawai_id
                ELSE pasienadmisi_t.pegawai_id
            END AS dokterdpjp_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN pegawai_m.nama_pegawai
                ELSE pegawai_ri.nama_pegawai
            END AS dokterdpjp_nama,
        d_utama.diag_utama_id AS diagnosa_utama_id,
        concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
        joined_penyerta.diagnosa_penyerta_id,
        joined_penyerta.diagnosa_penyerta,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN look_status_periksa.lookup_name
                ELSE look_status_ranap.lookup_name
            END AS status_periksa,
        kunjungan.id_status_periksa,
        kunjungan.is_status_periksa,
        pasien_m.jeniskelamin,
        pasien_m.no_telepon_pasien,
        pasien_m.no_mobile_pasien,
        kunjungan.kunjungan_id,
        kunjungan.kunjungan_nama,
        pasienpulang_t.kondisikeluar_id,
        pasienpulang_t.kondisikeluar_nama,
        pasienpulang_t.carakeluar_id,
        pasienpulang_t.carakeluar_nama,
        kunjungan.ket
       FROM ( SELECT 'a'::text AS ket,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.pasien_id,
                pendaftaran_t.umur,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.status_periksa::integer
                        ELSE pasienadmisi.status_ranap
                    END AS id_status_periksa,
                    CASE
                        WHEN pendaftaran_t.status_periksa::text = 4::text THEN 't'::text
                        WHEN pendaftaran_t.status_periksa::text = 433::text THEN 't'::text
                        ELSE 'f'::text
                    END AS is_status_periksa,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE tt.pasien_id = pendaftaran_t.pasien_id AND tt.ruangan_id = pendaftaran_t.ruangan_id)) = 1 THEN 180
                        ELSE 181
                    END AS kunjungan_id,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE tt.pasien_id = pendaftaran_t.pasien_id AND tt.ruangan_id = pendaftaran_t.ruangan_id)) = 1 THEN 'Kunjungan Baru'::text
                        ELSE 'Kunjungan Lama'::text
                    END AS kunjungan_nama,
                pendaftaran_t.pasienpulang_id,
                pendaftaran_t.status_periksa
               FROM pendaftaran_t
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.status_ranap
                       FROM pasienadmisi_t a) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id) kunjungan
         LEFT JOIN ( SELECT a.pasienadmisi_id,
                a.carabayar_id,
                a.penjamin_id,
                a.ruangan_id,
                a.pegawai_id,
                a.tgl_pendaftaran,
                a.status_ranap,
                a.pasienpulang_id
               FROM pasienadmisi_t a) pasienadmisi_t ON kunjungan.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
         JOIN ( SELECT a.pasien_id,
                look_jenis_kelamin.lookup_name AS jenis_kelamin,
                a.jeniskelamin,
                a.no_rekam_medik,
                a.nama_pasien,
                a.tanggal_lahir,
                a.alamat_pasien,
                a.no_telepon_pasien,
                a.no_mobile_pasien
               FROM pasien_m a
                 JOIN ( SELECT b.lookup_id,
                        b.lookup_name
                       FROM lookup_m b) look_jenis_kelamin ON a.jeniskelamin::integer = look_jenis_kelamin.lookup_id) pasien_m ON kunjungan.pasien_id = pasien_m.pasien_id
         LEFT JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_status_periksa ON kunjungan.status_periksa::integer = look_status_periksa.lookup_id
         LEFT JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_status_ranap ON pasienadmisi_t.status_ranap = look_status_ranap.lookup_id
         LEFT JOIN ( SELECT a.carabayar_id,
                a.carabayar_nama
               FROM carabayar_m a) carabayar_m ON kunjungan.carabayar_id = carabayar_m.carabayar_id
         LEFT JOIN ( SELECT a.carabayar_id,
                a.carabayar_nama
               FROM carabayar_m a) carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
         LEFT JOIN ( SELECT a.penjamin_id,
                a.penjamin_nama
               FROM penjamin_m a) penjamin_m ON kunjungan.penjamin_id = penjamin_m.penjamin_id
         LEFT JOIN ( SELECT a.penjamin_id,
                a.penjamin_nama
               FROM penjamin_m a) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
         LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                a.jeniskasuspenyakit_nama
               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON kunjungan.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
         LEFT JOIN ( SELECT a.ruangan_id,
                a.ruangan_nama
               FROM ruangan_m a) ruangan_m ON kunjungan.ruangan_id = ruangan_m.ruangan_id
         LEFT JOIN ( SELECT a.ruangan_id,
                a.ruangan_nama,
                a.instalasi_id
               FROM ruangan_m a) ruangan_ri ON pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id
         LEFT JOIN ( SELECT a.instalasi_id,
                a.instalasi_nama
               FROM instalasi_m a) instalasi_m ON kunjungan.instalasi_id = instalasi_m.instalasi_id
         LEFT JOIN ( SELECT a.instalasi_id,
                a.instalasi_nama
               FROM instalasi_m a) instalasi_ri ON ruangan_ri.instalasi_id = instalasi_ri.instalasi_id
         LEFT JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) pegawai_m ON kunjungan.pegawai_id = pegawai_m.pegawai_id
         LEFT JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) pegawai_ri ON pasienadmisi_t.pegawai_id = pegawai_ri.pegawai_id
         LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
                diagnosa_m.diagnosa_id AS diag_utama_id,
                diagnosa_m.diagnosa_kode AS diag_utama_kode,
                diagnosa_m.diagnosa_nama AS diag_utama
               FROM koreksidiagnosa_t a
                 JOIN ( SELECT a_1.diagnosa_id,
                        a_1.diagnosa_kode,
                        a_1.diagnosa_nama
                       FROM diagnosa_m a_1) diagnosa_m ON a.diagnosa_id = diagnosa_m.diagnosa_id
              WHERE a.kelompokdiagnosa_id = 2 AND a.is_deleted = false) d_utama ON kunjungan.pendaftaran_id = d_utama.pendaftaran_id
         LEFT JOIN ( SELECT a.pendaftaran_id,
                a.pasienpulang_id,
                a.kondisikeluar_id,
                a.carakeluar_id,
                a.pasienadmisi_id,
                carakeluar_m.carakeluar_nama,
                kondisikeluar_m.kondisikeluar_nama
               FROM pasienpulang_t a
                 LEFT JOIN kondisikeluar_m ON a.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
                 LEFT JOIN carakeluar_m ON a.carakeluar_id = carakeluar_m.carakeluar_id) pasienpulang_t ON kunjungan.pendaftaran_id = pasienpulang_t.pendaftaran_id AND pasienpulang_t.pasienadmisi_id IS NULL
         JOIN ( SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                string_agg(d_penyerta.diag_penyerta_id::text, '$'::text) AS diagnosa_penyerta_id,
                string_agg(d_penyerta.diag_penyerta, '$'::text) AS diagnosa_penyerta
               FROM pendaftaran_t
                 LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
                        diagnosa_m.diagnosa_id AS diag_penyerta_id,
                        concat(diagnosa_m.diagnosa_kode, '. ', diagnosa_m.diagnosa_nama) AS diag_penyerta
                       FROM koreksidiagnosa_t
                         JOIN ( SELECT a.diagnosa_id,
                                a.diagnosa_kode,
                                a.diagnosa_nama
                               FROM diagnosa_m a) diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
                      WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 3 AND koreksidiagnosa_t.is_deleted = false
                      GROUP BY koreksidiagnosa_t.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id
              WHERE pendaftaran_t.status_periksa::text <> '402'::text
              GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id) joined_penyerta ON joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id AND joined_penyerta.pasienadmisi_id = kunjungan.pasienadmisi_id
      WHERE kunjungan.status_periksa::text <> '628'::text
      GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, pasienadmisi_t.tgl_pendaftaran, kunjungan.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.jenis_kelamin, pasien_m.tanggal_lahir, kunjungan.umur, pasien_m.alamat_pasien, kunjungan.carabayar_id, pasienadmisi_t.carabayar_id, carabayar_m.carabayar_nama, carabayar_ri.carabayar_nama, kunjungan.penjamin_id, pasienadmisi_t.penjamin_id, penjamin_m.penjamin_nama, penjamin_ri.penjamin_nama, kunjungan.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, kunjungan.instalasi_id, ruangan_ri.instalasi_id, instalasi_m.instalasi_nama, instalasi_ri.instalasi_nama, kunjungan.ruangan_id, pasienadmisi_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_ri.ruangan_nama, kunjungan.pegawai_id, pasienadmisi_t.pegawai_id, pegawai_m.nama_pegawai, pegawai_ri.nama_pegawai, d_utama.diag_utama_id, d_utama.diag_utama_kode, d_utama.diag_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, look_status_periksa.lookup_name, look_status_ranap.lookup_name, kunjungan.id_status_periksa, kunjungan.is_status_periksa, pasien_m.jeniskelamin, pasien_m.no_telepon_pasien, pasien_m.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, pasienpulang_t.kondisikeluar_id, pasienpulang_t.kondisikeluar_nama, pasienpulang_t.carakeluar_id, pasienpulang_t.carakeluar_nama, kunjungan.ket
    UNION ALL
     SELECT kunjungan.pendaftaran_id,
        kunjungan.pasienadmisi_id,
        kunjungan.tgl_pendaftaran,
        kunjungan.no_pendaftaran,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        pasien_m.jenis_kelamin,
        pasien_m.tanggal_lahir,
        kunjungan.umur,
        pasien_m.alamat_pasien,
        kunjungan.carabayar_id,
        carabayar_m.carabayar_nama,
        kunjungan.penjamin_id,
        penjamin_m.penjamin_nama,
        kunjungan.jeniskasuspenyakit_id,
        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
        kunjungan.instalasi_id,
        instalasi_m.instalasi_nama,
        kunjungan.instalasi_id AS instalasi_asal,
        kunjungan.ruangan_id,
        ruangan_m.ruangan_nama,
        kunjungan.pegawai_id AS dokterdpjp_id,
        pegawai_m.nama_pegawai AS dokterdpjp_nama,
        d_utama.diag_utama_id AS diagnosa_utama_id,
        concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
        joined_penyerta.diagnosa_penyerta_id,
        joined_penyerta.diagnosa_penyerta,
        look_status_periksa.lookup_name AS status_periksa,
        kunjungan.id_status_periksa,
        kunjungan.is_status_periksa,
        pasien_m.jeniskelamin,
        pasien_m.no_telepon_pasien,
        pasien_m.no_mobile_pasien,
        kunjungan.kunjungan_id,
        kunjungan.kunjungan_nama,
        pasienpulang_t.kondisikeluar_id,
        pasienpulang_t.kondisikeluar_nama,
        pasienpulang_t.carakeluar_id,
        pasienpulang_t.carakeluar_nama,
        kunjungan.ket
       FROM ( SELECT 'b'::text AS ket,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.pasien_id,
                pendaftaran_t.umur,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.status_periksa::integer
                        ELSE pasienadmisi.status_ranap
                    END AS id_status_periksa,
                    CASE
                        WHEN pendaftaran_t.status_periksa::text = 4::text THEN 't'::text
                        WHEN pendaftaran_t.status_periksa::text = 433::text THEN 't'::text
                        ELSE 'f'::text
                    END AS is_status_periksa,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE tt.pasien_id = pendaftaran_t.pasien_id AND tt.ruangan_id = pendaftaran_t.ruangan_id)) = 1 THEN 180
                        ELSE 181
                    END AS kunjungan_id,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE tt.pasien_id = pendaftaran_t.pasien_id AND tt.ruangan_id = pendaftaran_t.ruangan_id)) = 1 THEN 'Kunjungan Baru'::text
                        ELSE 'Kunjungan Lama'::text
                    END AS kunjungan_nama,
                pendaftaran_t.pasienpulang_id,
                pendaftaran_t.status_periksa
               FROM pendaftaran_t
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.status_ranap
                       FROM pasienadmisi_t a) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id) kunjungan
         LEFT JOIN ( SELECT a.pasienadmisi_id,
                a.carabayar_id,
                a.penjamin_id,
                a.ruangan_id,
                a.pegawai_id,
                a.tgl_pendaftaran,
                a.status_ranap,
                a.pasienpulang_id
               FROM pasienadmisi_t a) pasienadmisi_t ON kunjungan.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
         JOIN ( SELECT a.pasien_id,
                look_jenis_kelamin.lookup_name AS jenis_kelamin,
                a.jeniskelamin,
                a.no_rekam_medik,
                a.nama_pasien,
                a.tanggal_lahir,
                a.alamat_pasien,
                a.no_telepon_pasien,
                a.no_mobile_pasien
               FROM pasien_m a
                 JOIN ( SELECT b.lookup_id,
                        b.lookup_name
                       FROM lookup_m b) look_jenis_kelamin ON a.jeniskelamin::integer = look_jenis_kelamin.lookup_id) pasien_m ON kunjungan.pasien_id = pasien_m.pasien_id
         LEFT JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_status_periksa ON kunjungan.status_periksa::integer = look_status_periksa.lookup_id
         LEFT JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_status_ranap ON pasienadmisi_t.status_ranap = look_status_ranap.lookup_id
         LEFT JOIN ( SELECT a.carabayar_id,
                a.carabayar_nama
               FROM carabayar_m a) carabayar_m ON kunjungan.carabayar_id = carabayar_m.carabayar_id
         LEFT JOIN ( SELECT a.carabayar_id,
                a.carabayar_nama
               FROM carabayar_m a) carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
         LEFT JOIN ( SELECT a.penjamin_id,
                a.penjamin_nama
               FROM penjamin_m a) penjamin_m ON kunjungan.penjamin_id = penjamin_m.penjamin_id
         LEFT JOIN ( SELECT a.penjamin_id,
                a.penjamin_nama
               FROM penjamin_m a) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
         LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                a.jeniskasuspenyakit_nama
               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON kunjungan.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
         LEFT JOIN ( SELECT a.ruangan_id,
                a.ruangan_nama
               FROM ruangan_m a) ruangan_m ON kunjungan.ruangan_id = ruangan_m.ruangan_id
         LEFT JOIN ( SELECT a.ruangan_id,
                a.ruangan_nama,
                a.instalasi_id
               FROM ruangan_m a) ruangan_ri ON pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id
         LEFT JOIN ( SELECT a.instalasi_id,
                a.instalasi_nama
               FROM instalasi_m a) instalasi_m ON kunjungan.instalasi_id = instalasi_m.instalasi_id
         LEFT JOIN ( SELECT a.instalasi_id,
                a.instalasi_nama
               FROM instalasi_m a) instalasi_ri ON ruangan_ri.instalasi_id = instalasi_ri.instalasi_id
         LEFT JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) pegawai_m ON kunjungan.pegawai_id = pegawai_m.pegawai_id
         LEFT JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) pegawai_ri ON pasienadmisi_t.pegawai_id = pegawai_ri.pegawai_id
         LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
                diagnosa_m.diagnosa_id AS diag_utama_id,
                diagnosa_m.diagnosa_kode AS diag_utama_kode,
                diagnosa_m.diagnosa_nama AS diag_utama
               FROM koreksidiagnosa_t a
                 JOIN ( SELECT a_1.diagnosa_id,
                        a_1.diagnosa_kode,
                        a_1.diagnosa_nama
                       FROM diagnosa_m a_1) diagnosa_m ON a.diagnosa_id = diagnosa_m.diagnosa_id
              WHERE a.kelompokdiagnosa_id = 2 AND a.is_deleted = false) d_utama ON kunjungan.pendaftaran_id = d_utama.pendaftaran_id
         LEFT JOIN ( SELECT a.pendaftaran_id,
                a.pasienpulang_id,
                a.kondisikeluar_id,
                a.carakeluar_id,
                carakeluar_m.carakeluar_nama,
                kondisikeluar_m.kondisikeluar_nama
               FROM pasienpulang_t a
                 LEFT JOIN kondisikeluar_m ON a.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
                 LEFT JOIN carakeluar_m ON a.carakeluar_id = carakeluar_m.carakeluar_id) pasienpulang_t ON kunjungan.pendaftaran_id = pasienpulang_t.pendaftaran_id
         JOIN ( SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                string_agg(d_penyerta.diag_penyerta_id::text, '$'::text) AS diagnosa_penyerta_id,
                string_agg(d_penyerta.diag_penyerta, '$'::text) AS diagnosa_penyerta
               FROM pendaftaran_t
                 LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
                        diagnosa_m.diagnosa_id AS diag_penyerta_id,
                        concat(diagnosa_m.diagnosa_kode, '. ', diagnosa_m.diagnosa_nama) AS diag_penyerta
                       FROM koreksidiagnosa_t
                         JOIN ( SELECT a.diagnosa_id,
                                a.diagnosa_kode,
                                a.diagnosa_nama
                               FROM diagnosa_m a) diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
                      WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 3 AND koreksidiagnosa_t.is_deleted = false
                      GROUP BY koreksidiagnosa_t.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id
              WHERE pendaftaran_t.status_periksa::text <> '402'::text
              GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id) joined_penyerta ON joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id
      WHERE kunjungan.instalasi_id = 2 AND kunjungan.status_periksa::text <> '628'::text
      GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, pasienadmisi_t.tgl_pendaftaran, kunjungan.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.jenis_kelamin, pasien_m.tanggal_lahir, kunjungan.umur, pasien_m.alamat_pasien, kunjungan.carabayar_id, pasienadmisi_t.carabayar_id, carabayar_m.carabayar_nama, carabayar_ri.carabayar_nama, kunjungan.penjamin_id, pasienadmisi_t.penjamin_id, penjamin_m.penjamin_nama, penjamin_ri.penjamin_nama, kunjungan.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, kunjungan.instalasi_id, ruangan_ri.instalasi_id, instalasi_m.instalasi_nama, instalasi_ri.instalasi_nama, kunjungan.ruangan_id, pasienadmisi_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_ri.ruangan_nama, kunjungan.pegawai_id, pasienadmisi_t.pegawai_id, pegawai_m.nama_pegawai, pegawai_ri.nama_pegawai, d_utama.diag_utama_id, d_utama.diag_utama_kode, d_utama.diag_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, look_status_periksa.lookup_name, look_status_ranap.lookup_name, kunjungan.id_status_periksa, kunjungan.is_status_periksa, pasien_m.jeniskelamin, pasien_m.no_telepon_pasien, pasien_m.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, pasienpulang_t.kondisikeluar_id, pasienpulang_t.kondisikeluar_nama, pasienpulang_t.carakeluar_id, pasienpulang_t.carakeluar_nama, kunjungan.ket
    UNION ALL
     SELECT kunjungan.pendaftaran_id,
        kunjungan.pasienadmisi_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.tgl_pendaftaran
                ELSE pasienadmisi_t.tgl_pendaftaran
            END AS tgl_pendaftaran,
        kunjungan.no_pendaftaran,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        pasien_m.jenis_kelamin,
        pasien_m.tanggal_lahir,
        kunjungan.umur,
        pasien_m.alamat_pasien,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.carabayar_id
                ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN carabayar_m.carabayar_nama
                ELSE carabayar_ri.carabayar_nama
            END AS carabayar_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.penjamin_id
                ELSE pasienadmisi_t.penjamin_id
            END AS penjamin_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN penjamin_m.penjamin_nama
                ELSE penjamin_ri.penjamin_nama
            END AS penjamin_nama,
        kunjungan.jeniskasuspenyakit_id,
        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.instalasi_id
                ELSE ruangan_ri.instalasi_id
            END AS instalasi_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN instalasi_m.instalasi_nama
                ELSE instalasi_ri.instalasi_nama
            END AS instalasi_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.instalasi_id
                ELSE ruangan_ri.instalasi_id
            END AS instalasi_asal,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.ruangan_id
                ELSE pasienadmisi_t.ruangan_id
            END AS ruangan_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN ruangan_m.ruangan_nama
                ELSE ruangan_ri.ruangan_nama
            END AS ruangan_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.pegawai_id
                ELSE pasienadmisi_t.pegawai_id
            END AS dokterdpjp_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN pegawai_m.nama_pegawai
                ELSE pegawai_ri.nama_pegawai
            END AS dokterdpjp_nama,
        d_utama.diag_utama_id AS diagnosa_utama_id,
        concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
        joined_penyerta.diagnosa_penyerta_id,
        joined_penyerta.diagnosa_penyerta,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN look_status_periksa.lookup_name
                ELSE look_status_ranap.lookup_name
            END AS status_periksa,
        kunjungan.id_status_periksa,
        kunjungan.is_status_periksa,
        pasien_m.jeniskelamin,
        pasien_m.no_telepon_pasien,
        pasien_m.no_mobile_pasien,
        kunjungan.kunjungan_id,
        kunjungan.kunjungan_nama,
        pasienpulang_t.kondisikeluar_id,
        pasienpulang_t.kondisikeluar_nama,
        pasienpulang_t.carakeluar_id,
        pasienpulang_t.carakeluar_nama,
        kunjungan.ket
       FROM ( SELECT 'c'::text AS ket,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.pasien_id,
                pendaftaran_t.umur,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.status_periksa::integer
                        ELSE pasienadmisi.status_ranap
                    END AS id_status_periksa,
                    CASE
                        WHEN pendaftaran_t.status_periksa::text = 4::text THEN 't'::text
                        WHEN pendaftaran_t.status_periksa::text = 433::text THEN 't'::text
                        ELSE 'f'::text
                    END AS is_status_periksa,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE tt.pasien_id = pendaftaran_t.pasien_id AND tt.ruangan_id = pendaftaran_t.ruangan_id)) = 1 THEN 180
                        ELSE 181
                    END AS kunjungan_id,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE tt.pasien_id = pendaftaran_t.pasien_id AND tt.ruangan_id = pendaftaran_t.ruangan_id)) = 1 THEN 'Kunjungan Baru'::text
                        ELSE 'Kunjungan Lama'::text
                    END AS kunjungan_nama,
                pendaftaran_t.pasienpulang_id,
                pendaftaran_t.status_periksa
               FROM pendaftaran_t
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.status_ranap
                       FROM pasienadmisi_t a) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id) kunjungan
         LEFT JOIN ( SELECT a.pasienadmisi_id,
                a.carabayar_id,
                a.penjamin_id,
                a.ruangan_id,
                a.pegawai_id,
                a.tgl_pendaftaran,
                a.status_ranap,
                a.pasienpulang_id
               FROM pasienadmisi_t a) pasienadmisi_t ON kunjungan.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
         JOIN ( SELECT a.pasien_id,
                look_jenis_kelamin.lookup_name AS jenis_kelamin,
                a.jeniskelamin,
                a.no_rekam_medik,
                a.nama_pasien,
                a.tanggal_lahir,
                a.alamat_pasien,
                a.no_telepon_pasien,
                a.no_mobile_pasien
               FROM pasien_m a
                 JOIN ( SELECT b.lookup_id,
                        b.lookup_name
                       FROM lookup_m b) look_jenis_kelamin ON a.jeniskelamin::integer = look_jenis_kelamin.lookup_id) pasien_m ON kunjungan.pasien_id = pasien_m.pasien_id
         LEFT JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_status_periksa ON kunjungan.status_periksa::integer = look_status_periksa.lookup_id
         LEFT JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_status_ranap ON pasienadmisi_t.status_ranap = look_status_ranap.lookup_id
         LEFT JOIN ( SELECT a.carabayar_id,
                a.carabayar_nama
               FROM carabayar_m a) carabayar_m ON kunjungan.carabayar_id = carabayar_m.carabayar_id
         LEFT JOIN ( SELECT a.carabayar_id,
                a.carabayar_nama
               FROM carabayar_m a) carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
         LEFT JOIN ( SELECT a.penjamin_id,
                a.penjamin_nama
               FROM penjamin_m a) penjamin_m ON kunjungan.penjamin_id = penjamin_m.penjamin_id
         LEFT JOIN ( SELECT a.penjamin_id,
                a.penjamin_nama
               FROM penjamin_m a) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
         LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                a.jeniskasuspenyakit_nama
               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON kunjungan.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
         LEFT JOIN ( SELECT a.ruangan_id,
                a.ruangan_nama
               FROM ruangan_m a) ruangan_m ON kunjungan.ruangan_id = ruangan_m.ruangan_id
         LEFT JOIN ( SELECT a.ruangan_id,
                a.ruangan_nama,
                a.instalasi_id
               FROM ruangan_m a) ruangan_ri ON pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id
         LEFT JOIN ( SELECT a.instalasi_id,
                a.instalasi_nama
               FROM instalasi_m a) instalasi_m ON kunjungan.instalasi_id = instalasi_m.instalasi_id
         LEFT JOIN ( SELECT a.instalasi_id,
                a.instalasi_nama
               FROM instalasi_m a) instalasi_ri ON ruangan_ri.instalasi_id = instalasi_ri.instalasi_id
         LEFT JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) pegawai_m ON kunjungan.pegawai_id = pegawai_m.pegawai_id
         LEFT JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) pegawai_ri ON pasienadmisi_t.pegawai_id = pegawai_ri.pegawai_id
         LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
                diagnosa_m.diagnosa_id AS diag_utama_id,
                diagnosa_m.diagnosa_kode AS diag_utama_kode,
                diagnosa_m.diagnosa_nama AS diag_utama
               FROM koreksidiagnosa_t a
                 JOIN ( SELECT a_1.diagnosa_id,
                        a_1.diagnosa_kode,
                        a_1.diagnosa_nama
                       FROM diagnosa_m a_1) diagnosa_m ON a.diagnosa_id = diagnosa_m.diagnosa_id
              WHERE a.kelompokdiagnosa_id = 2 AND a.is_deleted = false) d_utama ON kunjungan.pendaftaran_id = d_utama.pendaftaran_id
         LEFT JOIN ( SELECT a.pendaftaran_id,
                a.pasienpulang_id,
                a.kondisikeluar_id,
                a.carakeluar_id,
                carakeluar_m.carakeluar_nama,
                kondisikeluar_m.kondisikeluar_nama
               FROM pasienpulang_t a
                 LEFT JOIN kondisikeluar_m ON a.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
                 LEFT JOIN carakeluar_m ON a.carakeluar_id = carakeluar_m.carakeluar_id) pasienpulang_t ON kunjungan.pendaftaran_id = pasienpulang_t.pendaftaran_id
         JOIN ( SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                string_agg(d_penyerta.diag_penyerta_id::text, '$'::text) AS diagnosa_penyerta_id,
                string_agg(d_penyerta.diag_penyerta, '$'::text) AS diagnosa_penyerta
               FROM pendaftaran_t
                 LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
                        diagnosa_m.diagnosa_id AS diag_penyerta_id,
                        concat(diagnosa_m.diagnosa_kode, '. ', diagnosa_m.diagnosa_nama) AS diag_penyerta
                       FROM koreksidiagnosa_t
                         JOIN ( SELECT a.diagnosa_id,
                                a.diagnosa_kode,
                                a.diagnosa_nama
                               FROM diagnosa_m a) diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
                      WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 3 AND koreksidiagnosa_t.is_deleted = false
                      GROUP BY koreksidiagnosa_t.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id
              WHERE pendaftaran_t.status_periksa::text <> '402'::text
              GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id) joined_penyerta ON joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id
      WHERE kunjungan.instalasi_id = 1 AND kunjungan.status_periksa::text <> '628'::text
      GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, pasienadmisi_t.tgl_pendaftaran, kunjungan.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.jenis_kelamin, pasien_m.tanggal_lahir, kunjungan.umur, pasien_m.alamat_pasien, kunjungan.carabayar_id, pasienadmisi_t.carabayar_id, carabayar_m.carabayar_nama, carabayar_ri.carabayar_nama, kunjungan.penjamin_id, pasienadmisi_t.penjamin_id, penjamin_m.penjamin_nama, penjamin_ri.penjamin_nama, kunjungan.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, kunjungan.instalasi_id, ruangan_ri.instalasi_id, instalasi_m.instalasi_nama, instalasi_ri.instalasi_nama, kunjungan.ruangan_id, pasienadmisi_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_ri.ruangan_nama, kunjungan.pegawai_id, pasienadmisi_t.pegawai_id, pegawai_m.nama_pegawai, pegawai_ri.nama_pegawai, d_utama.diag_utama_id, d_utama.diag_utama_kode, d_utama.diag_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, look_status_periksa.lookup_name, look_status_ranap.lookup_name, kunjungan.id_status_periksa, kunjungan.is_status_periksa, pasien_m.jeniskelamin, pasien_m.no_telepon_pasien, pasien_m.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, pasienpulang_t.kondisikeluar_id, pasienpulang_t.kondisikeluar_nama, pasienpulang_t.carakeluar_id, pasienpulang_t.carakeluar_nama, kunjungan.ket
    UNION ALL
     SELECT kunjungan.pendaftaran_id,
        kunjungan.pasienadmisi_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.tgl_pendaftaran
                ELSE pasienadmisi_t.tgl_pendaftaran
            END AS tgl_pendaftaran,
        kunjungan.no_pendaftaran,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        pasien_m.jenis_kelamin,
        pasien_m.tanggal_lahir,
        kunjungan.umur,
        pasien_m.alamat_pasien,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.carabayar_id
                ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN carabayar_m.carabayar_nama
                ELSE carabayar_ri.carabayar_nama
            END AS carabayar_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.penjamin_id
                ELSE pasienadmisi_t.penjamin_id
            END AS penjamin_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN penjamin_m.penjamin_nama
                ELSE penjamin_ri.penjamin_nama
            END AS penjamin_nama,
        kunjungan.jeniskasuspenyakit_id,
        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.instalasi_id
                ELSE ruangan_ri.instalasi_id
            END AS instalasi_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN instalasi_m.instalasi_nama
                ELSE instalasi_ri.instalasi_nama
            END AS instalasi_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.instalasi_id
                ELSE ruangan_ri.instalasi_id
            END AS instalasi_asal,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.ruangan_id
                ELSE pasienadmisi_t.ruangan_id
            END AS ruangan_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN ruangan_m.ruangan_nama
                ELSE ruangan_ri.ruangan_nama
            END AS ruangan_nama,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN kunjungan.pegawai_id
                ELSE pasienadmisi_t.pegawai_id
            END AS dokterdpjp_id,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN pegawai_m.nama_pegawai
                ELSE pegawai_ri.nama_pegawai
            END AS dokterdpjp_nama,
        d_utama.diag_utama_id AS diagnosa_utama_id,
        concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
        joined_penyerta.diagnosa_penyerta_id,
        joined_penyerta.diagnosa_penyerta,
            CASE
                WHEN kunjungan.pasienadmisi_id IS NULL THEN look_status_periksa.lookup_name
                ELSE look_status_ranap.lookup_name
            END AS status_periksa,
        kunjungan.id_status_periksa,
        kunjungan.is_status_periksa,
        pasien_m.jeniskelamin,
        pasien_m.no_telepon_pasien,
        pasien_m.no_mobile_pasien,
        kunjungan.kunjungan_id,
        kunjungan.kunjungan_nama,
        pasienpulang_t.kondisikeluar_id,
        pasienpulang_t.kondisikeluar_nama,
        pasienpulang_t.carakeluar_id,
        pasienpulang_t.carakeluar_nama,
        kunjungan.ket
       FROM ( SELECT 'd'::text AS ket,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.pasien_id,
                pendaftaran_t.umur,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.status_periksa::integer
                        ELSE pasienadmisi.status_ranap
                    END AS id_status_periksa,
                    CASE
                        WHEN pendaftaran_t.status_periksa::text = 4::text THEN 't'::text
                        WHEN pendaftaran_t.status_periksa::text = 433::text THEN 't'::text
                        ELSE 'f'::text
                    END AS is_status_periksa,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE tt.pasien_id = pendaftaran_t.pasien_id AND tt.ruangan_id = pendaftaran_t.ruangan_id)) = 1 THEN 180
                        ELSE 181
                    END AS kunjungan_id,
                    CASE
                        WHEN (( SELECT count(tt.pasien_id) AS jumlah_pasien
                           FROM pendaftaran_t tt
                          WHERE tt.pasien_id = pendaftaran_t.pasien_id AND tt.ruangan_id = pendaftaran_t.ruangan_id)) = 1 THEN 'Kunjungan Baru'::text
                        ELSE 'Kunjungan Lama'::text
                    END AS kunjungan_nama,
                pendaftaran_t.pasienpulang_id,
                pendaftaran_t.status_periksa
               FROM pendaftaran_t
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.status_ranap
                       FROM pasienadmisi_t a) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id) kunjungan
         LEFT JOIN ( SELECT a.pasienadmisi_id,
                a.carabayar_id,
                a.penjamin_id,
                a.ruangan_id,
                a.pegawai_id,
                a.tgl_pendaftaran,
                a.status_ranap,
                a.pasienpulang_id
               FROM pasienadmisi_t a) pasienadmisi_t ON kunjungan.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
         JOIN ( SELECT a.pasien_id,
                look_jenis_kelamin.lookup_name AS jenis_kelamin,
                a.jeniskelamin,
                a.no_rekam_medik,
                a.nama_pasien,
                a.tanggal_lahir,
                a.alamat_pasien,
                a.no_telepon_pasien,
                a.no_mobile_pasien
               FROM pasien_m a
                 JOIN ( SELECT b.lookup_id,
                        b.lookup_name
                       FROM lookup_m b) look_jenis_kelamin ON a.jeniskelamin::integer = look_jenis_kelamin.lookup_id) pasien_m ON kunjungan.pasien_id = pasien_m.pasien_id
         LEFT JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_status_periksa ON kunjungan.status_periksa::integer = look_status_periksa.lookup_id
         LEFT JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_status_ranap ON pasienadmisi_t.status_ranap = look_status_ranap.lookup_id
         LEFT JOIN ( SELECT a.carabayar_id,
                a.carabayar_nama
               FROM carabayar_m a) carabayar_m ON kunjungan.carabayar_id = carabayar_m.carabayar_id
         LEFT JOIN ( SELECT a.carabayar_id,
                a.carabayar_nama
               FROM carabayar_m a) carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
         LEFT JOIN ( SELECT a.penjamin_id,
                a.penjamin_nama
               FROM penjamin_m a) penjamin_m ON kunjungan.penjamin_id = penjamin_m.penjamin_id
         LEFT JOIN ( SELECT a.penjamin_id,
                a.penjamin_nama
               FROM penjamin_m a) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
         LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                a.jeniskasuspenyakit_nama
               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON kunjungan.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
         LEFT JOIN ( SELECT a.ruangan_id,
                a.ruangan_nama
               FROM ruangan_m a) ruangan_m ON kunjungan.ruangan_id = ruangan_m.ruangan_id
         LEFT JOIN ( SELECT a.ruangan_id,
                a.ruangan_nama,
                a.instalasi_id
               FROM ruangan_m a) ruangan_ri ON pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id
         LEFT JOIN ( SELECT a.instalasi_id,
                a.instalasi_nama
               FROM instalasi_m a) instalasi_m ON kunjungan.instalasi_id = instalasi_m.instalasi_id
         LEFT JOIN ( SELECT a.instalasi_id,
                a.instalasi_nama
               FROM instalasi_m a) instalasi_ri ON ruangan_ri.instalasi_id = instalasi_ri.instalasi_id
         LEFT JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) pegawai_m ON kunjungan.pegawai_id = pegawai_m.pegawai_id
         LEFT JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) pegawai_ri ON pasienadmisi_t.pegawai_id = pegawai_ri.pegawai_id
         LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
                diagnosa_m.diagnosa_id AS diag_utama_id,
                diagnosa_m.diagnosa_kode AS diag_utama_kode,
                diagnosa_m.diagnosa_nama AS diag_utama
               FROM koreksidiagnosa_t a
                 JOIN ( SELECT a_1.diagnosa_id,
                        a_1.diagnosa_kode,
                        a_1.diagnosa_nama
                       FROM diagnosa_m a_1) diagnosa_m ON a.diagnosa_id = diagnosa_m.diagnosa_id
              WHERE a.kelompokdiagnosa_id = 2 AND a.is_deleted = false) d_utama ON kunjungan.pendaftaran_id = d_utama.pendaftaran_id
         LEFT JOIN ( SELECT a.pendaftaran_id,
                a.pasienpulang_id,
                a.kondisikeluar_id,
                a.carakeluar_id,
                carakeluar_m.carakeluar_nama,
                kondisikeluar_m.kondisikeluar_nama
               FROM pasienpulang_t a
                 LEFT JOIN kondisikeluar_m ON a.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
                 LEFT JOIN carakeluar_m ON a.carakeluar_id = carakeluar_m.carakeluar_id) pasienpulang_t ON kunjungan.pendaftaran_id = pasienpulang_t.pendaftaran_id
         JOIN ( SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                string_agg(d_penyerta.diag_penyerta_id::text, '$'::text) AS diagnosa_penyerta_id,
                string_agg(d_penyerta.diag_penyerta, '$'::text) AS diagnosa_penyerta
               FROM pendaftaran_t
                 LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
                        diagnosa_m.diagnosa_id AS diag_penyerta_id,
                        concat(diagnosa_m.diagnosa_kode, '. ', diagnosa_m.diagnosa_nama) AS diag_penyerta
                       FROM koreksidiagnosa_t
                         JOIN ( SELECT a.diagnosa_id,
                                a.diagnosa_kode,
                                a.diagnosa_nama
                               FROM diagnosa_m a) diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
                      WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 3 AND koreksidiagnosa_t.is_deleted = false
                      GROUP BY koreksidiagnosa_t.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id
              WHERE pendaftaran_t.status_periksa::text <> '402'::text
              GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id) joined_penyerta ON joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id
      WHERE (kunjungan.instalasi_id <> ALL (ARRAY[1, 2, 3])) AND kunjungan.status_periksa::text <> '628'::text
      GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, pasienadmisi_t.tgl_pendaftaran, kunjungan.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.jenis_kelamin, pasien_m.tanggal_lahir, kunjungan.umur, pasien_m.alamat_pasien, kunjungan.carabayar_id, pasienadmisi_t.carabayar_id, carabayar_m.carabayar_nama, carabayar_ri.carabayar_nama, kunjungan.penjamin_id, pasienadmisi_t.penjamin_id, penjamin_m.penjamin_nama, penjamin_ri.penjamin_nama, kunjungan.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, kunjungan.instalasi_id, ruangan_ri.instalasi_id, instalasi_m.instalasi_nama, instalasi_ri.instalasi_nama, kunjungan.ruangan_id, pasienadmisi_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_ri.ruangan_nama, kunjungan.pegawai_id, pasienadmisi_t.pegawai_id, pegawai_m.nama_pegawai, pegawai_ri.nama_pegawai, d_utama.diag_utama_id, d_utama.diag_utama_kode, d_utama.diag_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, look_status_periksa.lookup_name, look_status_ranap.lookup_name, kunjungan.id_status_periksa, kunjungan.is_status_periksa, pasien_m.jeniskelamin, pasien_m.no_telepon_pasien, pasien_m.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, pasienpulang_t.kondisikeluar_id, pasienpulang_t.kondisikeluar_nama, pasienpulang_t.carakeluar_id, pasienpulang_t.carakeluar_nama, kunjungan.ket;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220224_025122_migrate_bugfix_lapkunjunganpasrs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220224_025122_migrate_bugfix_lapkunjunganpasrs_v cannot be reverted.\n";

        return false;
    }
    */
}
