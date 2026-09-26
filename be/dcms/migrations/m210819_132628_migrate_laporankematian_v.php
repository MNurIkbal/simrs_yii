<?php

use yii\db\Migration;

/**
 * Class m210819_132628_migrate_laporankematian_v
 */
class m210819_132628_migrate_laporankematian_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporankematian_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporankematian_v\" AS  SELECT 'RI'::text AS text,
    pasienpulang_t.tglpasienpulang,
    pendaftaran_t.tgl_pendaftaran AS tgl_masukpasien,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
        CASE
            WHEN pasien_m.no_identitas_pasien IS NULL THEN pasien_m.additional_pasien::character varying
            ELSE pasien_m.no_identitas_pasien
        END AS no_identitas_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendidikan_m.pendidikan_nama,
    pekerjaan_m.pekerjaan_nama,
    fgetnamalookup(pasien_m.warga_negara::integer) AS status_kependudukan,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    kelurahan_m.kelurahan_nama,
    kecamatan_m.kecamatan_nama,
    kabupaten_m.kabupaten_nama,
    penanggungjawab_m.penanggungjawab_notelp,
    pasienpulang_t.tgl_meninggal,
    NULL::text AS umur_meninggal,
        CASE
            WHEN pasien_m.tanggal_lahir = pasienadmisi_t.tgl_pulang::date THEN 'YA'::text
            ELSE 'TIDAK'::text
        END AS lahir_mati,
    NULL::text AS alm_dlm_keadaan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN instalasi_m.instalasi_nama
            ELSE instalasi_admisi.instalasi_nama
        END AS tempat_meninggal,
        CASE
            WHEN pasienpulang_t.lama_rawat <= 2 THEN 'MENINGGAL < 48 JAM'::text
            ELSE 'MENINGGAL > 48 JAM'::text
        END AS kondisikeluar_nama,
    NULL::text AS sumber_data,
    NULL::text AS rencana_pemulasaran,
    NULL::text AS tempat_pemulasaran,
    pegawai_m.nama_pegawai AS dokter_menerangkan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.ruangan_id
            ELSE ruang_admisi.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruangan_m.ruangan_nama
            ELSE ruang_admisi.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
            ELSE instalasi_admisi.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN instalasi_m.instalasi_nama
            ELSE instalasi_admisi.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_m.carabayar_nama
            ELSE cb_admisi.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_m.penjamin_nama
            ELSE pj_admisi.penjamin_nama
        END AS penjamin_nama,
    concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
    d_utama.diag_utama_id AS diagnosa_utama_id,
    d_penyerta.diag_penyerta AS diagnosa_penyerta,
    d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id
   FROM pendaftaran_t
     JOIN ( SELECT a.pasienadmisi_id,
            a.pasienpulang_id,
            a.pegawai_id,
            a.ruangan_id,
            a.carabayar_id,
            a.penjamin_id,
            a.tgl_pulang
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT a.pasienpulang_id,
            a.carakeluar_id,
            a.kondisikeluar_id,
            a.tglpasienpulang,
            a.tgl_meninggal,
            a.lama_rawat
           FROM pasienpulang_t a
          WHERE a.carakeluar_id = 4) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT a.kondisikeluar_id,
            a.kondisikeluar_nama
           FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.pendidikan_id,
            a.pekerjaan_id,
            a.kelurahan_id,
            a.kecamatan_id,
            a.kabupaten_id,
            a.no_identitas_pasien,
            a.additional_pasien,
            a.jeniskelamin,
            a.tempat_lahir,
            a.tanggal_lahir,
            a.warga_negara,
            a.alamat_pasien,
            a.rt,
            a.rw
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.pendidikan_id,
            a.pendidikan_nama
           FROM pendidikan_m a) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN ( SELECT a.pekerjaan_id,
            a.pekerjaan_nama
           FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN ( SELECT a.kelurahan_id,
            a.kelurahan_nama
           FROM kelurahan_m a) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
     LEFT JOIN ( SELECT a.kecamatan_id,
            a.kecamatan_nama
           FROM kecamatan_m a) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN ( SELECT a.kabupaten_id,
            a.kabupaten_nama
           FROM kabupaten_m a) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruang_admisi ON pasienadmisi_t.ruangan_id = ruang_admisi.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_admisi ON ruang_admisi.instalasi_id = instalasi_admisi.instalasi_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) cb_admisi ON pasienadmisi_t.carabayar_id = cb_admisi.carabayar_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) pj_admisi ON pasienadmisi_t.penjamin_id = pj_admisi.penjamin_id
     LEFT JOIN ( SELECT a.penanggungjawab_id,
            a.penanggungjawab_notelp
           FROM penanggungjawab_m a) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     LEFT JOIN ( SELECT DISTINCT ON (koreksidiagnosa_t.pendaftaran_id) koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_utama_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
           FROM koreksidiagnosa_t
             JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
          WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2) d_utama ON pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id
     LEFT JOIN ( SELECT penyerta.pendaftaran_id,
            string_agg(penyerta.diag_penyerta_id::text, ' - '::text) AS diag_penyerta_id,
            string_agg(penyerta.diagnosa, ' - '::text) AS diag_penyerta
           FROM ( SELECT koreksidiagnosa_t.pendaftaran_id,
                    diagnosa_m.diagnosa_id AS diag_penyerta_id,
                    diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
                    diagnosa_m.diagnosa_nama AS diag_penyerta,
                    concat(diagnosa_m.diagnosa_kode, '. ', diagnosa_m.diagnosa_nama) AS diagnosa
                   FROM koreksidiagnosa_t
                     JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
                  WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 3 AND koreksidiagnosa_t.is_deleted = false) penyerta
          GROUP BY penyerta.pendaftaran_id) d_penyerta ON pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id
UNION ALL
 SELECT 'RJ'::text AS text,
    pasienpulang_t.tglpasienpulang,
    pendaftaran_t.tgl_pendaftaran AS tgl_masukpasien,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
        CASE
            WHEN pasien_m.no_identitas_pasien IS NULL THEN pasien_m.additional_pasien::character varying
            ELSE pasien_m.no_identitas_pasien
        END AS no_identitas_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendidikan_m.pendidikan_nama,
    pekerjaan_m.pekerjaan_nama,
    fgetnamalookup(pasien_m.warga_negara::integer) AS status_kependudukan,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    kelurahan_m.kelurahan_nama,
    kecamatan_m.kecamatan_nama,
    kabupaten_m.kabupaten_nama,
    penanggungjawab_m.penanggungjawab_notelp,
    pasienpulang_t.tgl_meninggal,
    NULL::text AS umur_meninggal,
        CASE
            WHEN pasien_m.tanggal_lahir = pasienpulang_t.tglpasienpulang::date THEN 'YA'::text
            ELSE 'TIDAK'::text
        END AS lahir_mati,
    NULL::text AS alm_dlm_keadaan,
    instalasi_m.instalasi_nama AS tempat_meninggal,
        CASE
            WHEN pasienpulang_t.lama_rawat <= 2 THEN 'MENINGGAL < 48 JAM'::text
            ELSE 'MENINGGAL > 48 JAM'::text
        END AS kondisikeluar_nama,
    NULL::text AS sumber_data,
    NULL::text AS rencana_pemulasaran,
    NULL::text AS tempat_pemulasaran,
    pegawai_m.nama_pegawai AS dokter_menerangkan,
    pendaftaran_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
    d_utama.diag_utama_id AS diagnosa_utama_id,
    d_penyerta.diag_penyerta AS diagnosa_penyerta,
    d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id
   FROM pendaftaran_t
     JOIN ( SELECT b.pasienpulang_id,
            b.carakeluar_id,
            b.kondisikeluar_id,
            b.tglpasienpulang,
            b.tgl_meninggal,
            b.lama_rawat
           FROM pasienpulang_t b
          WHERE b.carakeluar_id = 4) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT b.kondisikeluar_id,
            b.kondisikeluar_nama
           FROM kondisikeluar_m b) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     JOIN ( SELECT b.pasien_id,
            b.nama_pasien,
            b.pendidikan_id,
            b.pekerjaan_id,
            b.kelurahan_id,
            b.kecamatan_id,
            b.kabupaten_id,
            b.no_identitas_pasien,
            b.additional_pasien,
            b.jeniskelamin,
            b.tempat_lahir,
            b.tanggal_lahir,
            b.warga_negara,
            b.alamat_pasien,
            b.rt,
            b.rw
           FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT b.pendidikan_id,
            b.pendidikan_nama
           FROM pendidikan_m b) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN ( SELECT b.pekerjaan_id,
            b.pekerjaan_nama
           FROM pekerjaan_m b) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN ( SELECT b.kelurahan_id,
            b.kelurahan_nama
           FROM kelurahan_m b) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
     LEFT JOIN ( SELECT b.kecamatan_id,
            b.kecamatan_nama
           FROM kecamatan_m b) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN ( SELECT b.kabupaten_id,
            b.kabupaten_nama
           FROM kabupaten_m b) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama,
            b.instalasi_id
           FROM ruangan_m b) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT b.instalasi_id,
            b.instalasi_nama
           FROM instalasi_m b) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT b.carabayar_id,
            b.carabayar_nama
           FROM carabayar_m b) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT b.penjamin_id,
            b.penjamin_nama
           FROM penjamin_m b) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT b.penanggungjawab_id,
            b.penanggungjawab_notelp
           FROM penanggungjawab_m b) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     LEFT JOIN ( SELECT DISTINCT ON (koreksidiagnosa_t.pendaftaran_id) koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_utama_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
           FROM koreksidiagnosa_t
             JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
          WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2) d_utama ON pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id
     LEFT JOIN ( SELECT penyerta.pendaftaran_id,
            string_agg(penyerta.diag_penyerta_id::text, ' - '::text) AS diag_penyerta_id,
            string_agg(penyerta.diagnosa, ' - '::text) AS diag_penyerta
           FROM ( SELECT koreksidiagnosa_t.pendaftaran_id,
                    diagnosa_m.diagnosa_id AS diag_penyerta_id,
                    diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
                    diagnosa_m.diagnosa_nama AS diag_penyerta,
                    concat(diagnosa_m.diagnosa_kode, '. ', diagnosa_m.diagnosa_nama) AS diagnosa
                   FROM koreksidiagnosa_t
                     JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
                  WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 3 AND koreksidiagnosa_t.is_deleted = false) penyerta
          GROUP BY penyerta.pendaftaran_id) d_penyerta ON pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id;");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210819_132628_migrate_laporankematian_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210819_132628_migrate_laporankematian_v cannot be reverted.\n";

        return false;
    }
    */
}
