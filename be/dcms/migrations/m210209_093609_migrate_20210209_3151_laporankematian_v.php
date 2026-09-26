<?php

use yii\db\Migration;

/**
 * Class m210209_093609_migrate_20210209_3151_laporankematian_v
 */
class m210209_093609_migrate_20210209_3151_laporankematian_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporankematian_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporankematian_v\" AS
            SELECT 'RI'::text AS text,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.tgl_pendaftaran AS tgl_masukpasien,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            CASE
            WHEN (pasien_m.no_identitas_pasien IS NULL) THEN (pasien_m.additional_pasien)::character varying
            ELSE pasien_m.no_identitas_pasien
            END AS no_identitas_pasien,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
            pasien_m.tempat_lahir,
            pasien_m.tanggal_lahir,
            pendidikan_m.pendidikan_nama,
            pekerjaan_m.pekerjaan_nama,
            fgetnamalookup((pasien_m.warga_negara)::integer) AS status_kependudukan,
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
            WHEN (pasien_m.tanggal_lahir = (pasienadmisi_t.tgl_pulang)::date) THEN 'YA'::text
            ELSE 'TIDAK'::text
            END AS lahir_mati,
            NULL::text AS alm_dlm_keadaan,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_m.instalasi_nama
            ELSE instalasi_admisi.instalasi_nama
            END AS tempat_meninggal,
            kondisikeluar_m.kondisikeluar_nama,
            NULL::text AS sumber_data,
            NULL::text AS rencana_pemulasaran,
            NULL::text AS tempat_pemulasaran,
            pegawai_m.nama_pegawai AS dokter_menerangkan,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE ruang_admisi.ruangan_id
            END AS ruangan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_m.ruangan_nama
            ELSE ruang_admisi.ruangan_nama
            END AS ruangan_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.instalasi_id
            ELSE instalasi_admisi.instalasi_id
            END AS instalasi_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_m.instalasi_nama
            ELSE instalasi_admisi.instalasi_nama
            END AS instalasi_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_m.carabayar_nama
            ELSE cb_admisi.carabayar_nama
            END AS carabayar_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
            END AS penjamin_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_m.penjamin_nama
            ELSE pj_admisi.penjamin_nama
            END AS penjamin_nama,
            concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
            d_utama.diag_utama_id AS diagnosa_utama_id,
            concat(d_penyerta.diag_penyerta_kode, '. ', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
            d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id
            FROM (((((((((((((((((((((pendaftaran_t
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasienpulang_t ON (((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (pasienpulang_t.carakeluar_id = 4))))
            JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN ruangan_m ruang_admisi ON ((pasienadmisi_t.ruangan_id = ruang_admisi.ruangan_id)))
            LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN instalasi_m instalasi_admisi ON ((ruang_admisi.instalasi_id = instalasi_admisi.instalasi_id)))
            LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN carabayar_m cb_admisi ON ((pasienadmisi_t.carabayar_id = cb_admisi.carabayar_id)))
            LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN penjamin_m pj_admisi ON ((pasienadmisi_t.penjamin_id = pj_admisi.penjamin_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
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
            WHEN (pasien_m.no_identitas_pasien IS NULL) THEN (pasien_m.additional_pasien)::character varying
            ELSE pasien_m.no_identitas_pasien
            END AS no_identitas_pasien,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
            pasien_m.tempat_lahir,
            pasien_m.tanggal_lahir,
            pendidikan_m.pendidikan_nama,
            pekerjaan_m.pekerjaan_nama,
            fgetnamalookup((pasien_m.warga_negara)::integer) AS status_kependudukan,
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
            WHEN (pasien_m.tanggal_lahir = (pasienpulang_t.tglpasienpulang)::date) THEN 'YA'::text
            ELSE 'TIDAK'::text
            END AS lahir_mati,
            NULL::text AS alm_dlm_keadaan,
            instalasi_m.instalasi_nama AS tempat_meninggal,
            kondisikeluar_m.kondisikeluar_nama,
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
            concat(d_penyerta.diag_penyerta_kode, '. ', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
            d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id
            FROM ((((((((((((((((pendaftaran_t
            JOIN pasienpulang_t ON (((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (pasienpulang_t.carakeluar_id = 4))))
            LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
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
            ;");
            $this->execute('
                ALTER TABLE public.laporankematian_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210209_093609_migrate_20210209_3151_laporankematian_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210209_093609_migrate_20210209_3151_laporankematian_v cannot be reverted.\n";

        return false;
    }
    */
}
