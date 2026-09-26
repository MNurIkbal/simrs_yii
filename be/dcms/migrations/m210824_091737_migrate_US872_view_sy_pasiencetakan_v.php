<?php

use yii\db\Migration;

/**
 * Class m210824_091737_migrate_US872_view_sy_pasiencetakan_v
 */
class m210824_091737_migrate_US872_view_sy_pasiencetakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.sy_pasiencetakan_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_pasiencetakan_v\" AS
            SELECT pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.tgl_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            pasien_m.jenisidentitas,
            fgetnamalookup((pasien_m.jenisidentitas)::integer) AS identitas,
            pasien_m.no_identitas_pasien,
            pasien_m.namadepan,
            fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
            pasien_m.nama_bin,
            pasien_m.tempat_lahir,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pasien_m.statusperkawinan,
            fgetnamalookup((pasien_m.statusperkawinan)::integer) AS status_perkawinan,
            pasien_m.nama_ibu,
            pasien_m.alamat_sekarang,
            pasien_m.alamat_pasien,
            pasien_m.rt,
            pasien_m.rw,
            pasien_m.propinsi_id,
            fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
            pasien_m.kabupaten_id,
            fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
            pasien_m.kecamatan_id,
            fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
            pasien_m.kelurahan_id,
            fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
            pasien_m.no_mobile_pasien,
            pasien_m.no_telepon_pasien,
            pasien_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pasien_m.warga_negara,
            fgetnamalookup((pasien_m.warga_negara)::integer) AS warganegara,
            pasien_m.agama,
            fgetnamalookup((pasien_m.agama)::integer) AS agama_pasien,
            pasien_m.alamatemail,
            pasien_m.suku_id,
            suku.suku_nama,
            pasien_m.nama_ayah,
            pasien_m.anakke,
            pasien_m.jumlah_bersaudara,
            pasien_m.golongandarah,
            fgetnamalookup((pasien_m.golongandarah)::integer) AS golongan_darah,
            pasien_m.photopasien,
            pasien_m.is_aps,
            dokrekammedis_m.dokrekammedis_id,
            pasien_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            pasien_m.additional_pasien,
            pasien_m.catatanpenting_pasien,
            '-'::text AS alergi,
            CASE
            WHEN (penanggungjawab.penanggungjawab_nama IS NULL) THEN pasien_m.penanggungjawabtera_nama
            ELSE penanggungjawab.penanggungjawab_nama
            END AS penanggungjawab_nama,
            COALESCE(fgetnamalookup((penanggungjawab.hubungankeluarga)::integer), pasien_m.penanggungjawabtera_hubungan) AS hubungankeluarga,
            CASE
            WHEN (penanggungjawab.penanggungjawab_alamat IS NULL) THEN pasien_m.penanggungjawabtera_alamat
            ELSE penanggungjawab.penanggungjawab_alamat
            END AS penanggungjawab_alamat,
            kelurahan_pj.kelurahan_nama AS penanggungjawab_kelurahan,
            kecamatan_pj.kecamatan_nama AS penanggungjawab_kecamatan,
            CASE
            WHEN (penanggungjawab.penanggungjawab_notelp IS NULL) THEN ('-'::text)::character varying
            ELSE penanggungjawab.penanggungjawab_notelp
            END AS penanggungjawab_notelp,
            CASE
            WHEN (pasien_m.nopeserta_bpjs IS NULL) THEN ('-'::text)::character varying
            ELSE pasien_m.nopeserta_bpjs
            END AS nokartuasuransi,
            CASE
            WHEN (kelurahan_m.kode_pos IS NULL) THEN ('-'::text)::character varying
            ELSE kelurahan_m.kode_pos
            END AS kode_pos,
            pasien_m.last_modified_date AS tgl_update_terakhir,
            petugas_pemakai.nama_pegawai AS petugas_nama,
            pasien_m.created_date AS tgl_pembuatan,
            petugas_pembuat.nama_pegawai AS pembuat_nama,
            NULL::text AS pengirim,
            NULL::text AS instansi,
            pasienadmisi_t.diagnosa_awal AS diagnosa_masuk,
            NULL::text AS dipindahkanke,
            NULL::text AS tanggal,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_m.ruangan_nama
            ELSE ruangan_ri.ruangan_nama
            END AS ruangan,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelaspelayanan_m.kelaspelayanan_nama
            ELSE kelaspelayanan_ri.kelaspelayanan_nama
            END AS kelas,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelaspelayanan_m.kelaspelayanan_nama
            ELSE kelaspelayanan_ri.kelaspelayanan_nama
            END AS kelas_diminta,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
            ELSE pegawai_ri.nama_pegawai
            END AS dokter,
            NULL::text AS tgl_keluar,
            NULL::text AS jam_keluar,
            pasienadmisi_t.diagnosa_awal AS kodeicd_kodetindakan,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            keluargapasien_t.keluarga_nama,
            keluargapasien_t.keluarga_alamat AS penanggungbiaya_alamat,
            kelurahan_pb.kelurahan_nama AS penanggungbiaya_kelurahan,
            kecamatan_pb.kecamatan_nama AS penanggungbiaya_kecamatan,
            pekerjaan_pb.pekerjaan_nama AS penanggungbiaya_pekerjaan,
            keluargapasien_t.keluarga_no_telepon AS penanggungbiaya_telepon
            FROM (((((((((((((((((((((((((pasien_m
            LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN suku_m suku ON ((pasien_m.suku_id = suku.suku_id)))
            LEFT JOIN dokrekammedis_m ON ((pasien_m.pasien_id = dokrekammedis_m.pasien_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN loginpemakai_k petugas_1 ON ((pasien_m.last_modified_by = petugas_1.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pemakai ON ((petugas_1.pegawai_id = petugas_pemakai.pegawai_id)))
            LEFT JOIN loginpemakai_k pembuat ON ((pasien_m.created_by = pembuat.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pembuat ON ((pembuat.pegawai_id = petugas_pembuat.pegawai_id)))
            LEFT JOIN ( SELECT penanggungjawab_m.penanggungjawab_id,
            penanggungjawab_m.pasien_id,
            penanggungjawab_m.penanggungjawab_nama,
            penanggungjawab_m.hubungankeluarga,
            penanggungjawab_m.penanggungjawab_alamat,
            penanggungjawab_m.penanggungjawab_notelp,
            penanggungjawab_m.penanggungjawab_id,
            penanggungjawab_m.pj_kelurahan_id,
            penanggungjawab_m.pj_kecamatan_id
            FROM (penanggungjawab_m
            JOIN ( SELECT max(penanggungjawab_m_1.penanggungjawab_id) AS penanggungjawab_id,
            penanggungjawab_m_1.pasien_id
            FROM penanggungjawab_m penanggungjawab_m_1
            GROUP BY penanggungjawab_m_1.pasien_id) penanggungjawab_max ON (((penanggungjawab_m.penanggungjawab_id = penanggungjawab_max.penanggungjawab_id) AND (penanggungjawab_m.pasien_id = penanggungjawab_max.pasien_id))))) penanggungjawab(penanggungjawab_id, pasien_id, penanggungjawab_nama, hubungankeluarga, penanggungjawab_alamat, penanggungjawab_notelp, penanggungjawab_id_1, pj_kelurahan_id, pj_kecamatan_id) ON ((pasien_m.pasien_id = penanggungjawab.pasien_id)))
            LEFT JOIN pasienubahdata_t ON (((pasien_m.pasien_id = pasienubahdata_t.pasien_id) AND (pasienubahdata_t.is_active IS TRUE) AND (pasienubahdata_t.is_deleted IS FALSE))))
            LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN ( SELECT a.pasien_id,
            a.diagnosa_awal,
            a.ruangan_id,
            a.kelaspelayanan_id,
            a.pegawai_id,
            a.pasienadmisi_id
            FROM (pasienadmisi_t a
            JOIN ( SELECT max(pasienadmisi_t_1.pasienadmisi_id) AS pasienadmisi_id,
            pasienadmisi_t_1.pasien_id
            FROM pasienadmisi_t pasienadmisi_t_1
            GROUP BY pasienadmisi_t_1.pasien_id) admisi_max ON (((a.pasienadmisi_id = admisi_max.pasienadmisi_id) AND (a.pasien_id = admisi_max.pasien_id))))) pasienadmisi_t ON ((pasien_m.pasien_id = pasienadmisi_t.pasien_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasien_id,
            a.ruangan_id,
            a.kelaspelayanan_id,
            a.pegawai_id,
            a.pasienadmisi_id,
            a.no_pendaftaran
            FROM (pendaftaran_t a
            JOIN ( SELECT max(pendaftaran_t_1.pendaftaran_id) AS pendaftaran_id,
            pendaftaran_t_1.pasien_id
            FROM pendaftaran_t pendaftaran_t_1
            GROUP BY pendaftaran_t_1.pasien_id) pendaftaran_max ON (((a.pendaftaran_id = pendaftaran_max.pendaftaran_id) AND (a.pasien_id = pendaftaran_max.pasien_id))))) pendaftaran_t ON ((pasien_m.pasien_id = pendaftaran_t.pasien_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_ri ON ((pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id)))
            LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_ri ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_ri ON ((pasienadmisi_t.pegawai_id = pegawai_ri.pegawai_id)))
            LEFT JOIN ( SELECT a.kelurahan_id,
            a.kelurahan_nama
            FROM kelurahan_m a) kelurahan_pj ON ((penanggungjawab.pj_kelurahan_id = kelurahan_pj.kelurahan_id)))
            LEFT JOIN ( SELECT a.kecamatan_id,
            a.kecamatan_nama
            FROM kecamatan_m a) kecamatan_pj ON ((penanggungjawab.pj_kecamatan_id = kecamatan_pj.kecamatan_id)))
            LEFT JOIN ( SELECT a.pasien_id,
            a.keluarga_nama,
            a.keluarga_alamat,
            a.keluarga_kelurahan_id,
            a.keluarga_kecamatan_id,
            a.keluarga_pekerjaan_id,
            a.keluarga_no_telepon
            FROM keluargapasien_t a) keluargapasien_t ON ((pasien_m.pasien_id = keluargapasien_t.pasien_id)))
            LEFT JOIN ( SELECT a.kelurahan_id,
            a.kelurahan_nama
            FROM kelurahan_m a) kelurahan_pb ON ((keluargapasien_t.keluarga_kelurahan_id = kelurahan_pb.kelurahan_id)))
            LEFT JOIN ( SELECT a.kecamatan_id,
            a.kecamatan_nama
            FROM kecamatan_m a) kecamatan_pb ON ((keluargapasien_t.keluarga_kecamatan_id = kecamatan_pb.kecamatan_id)))
            LEFT JOIN ( SELECT a.pekerjaan_id,
            a.pekerjaan_nama
            FROM pekerjaan_m a) pekerjaan_pb ON ((keluargapasien_t.keluarga_pekerjaan_id = pekerjaan_pb.pekerjaan_id)))
            WHERE ((pasien_m.is_active = true) AND (pasien_m.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.sy_pasiencetakan_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210824_091737_migrate_US872_view_sy_pasiencetakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210824_091737_migrate_US872_view_sy_pasiencetakan_v cannot be reverted.\n";

        return false;
    }
    */
}
