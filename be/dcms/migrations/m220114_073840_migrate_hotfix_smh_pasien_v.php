<?php

use yii\db\Migration;

/**
 * Class m220114_073840_migrate_hotfix_smh_pasien_v
 */
class m220114_073840_migrate_hotfix_smh_pasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.pasien_v;');
        $this->execute("
            CREATE VIEW \"public\".\"pasien_v\" AS
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
            propinsi_m.kode_propinsi,
            propinsi_m.propinsi_nama,
            pasien_m.kabupaten_id,
            kabupaten_m.kode_kabupaten,
            kabupaten_m.kabupaten_nama,
            pasien_m.kecamatan_id,
            kecamatan_m.kode_kecamatan,
            kecamatan_m.kecamatan_nama,
            pasien_m.kelurahan_id,
            kelurahan_m.kode_kelurahan,
            kelurahan_m.kelurahan_nama,
            pasien_m.no_mobile_pasien,
            pasien_m.no_telepon_pasien,
            pasien_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pasien_m.warga_negara,
            negara.lookup_name AS warganegara,
            negara.lookup_kode AS kode_negara,
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
            COALESCE(piutang.total_sisapiutang, (0)::double precision) AS total_sisapiutang,
            pasien_m.additional_pasien,
            pasien_m.catatanpenting_pasien,
            '-'::text AS alergi,
            pasien_m.last_modified_date AS tgl_update_terakhir,
            petugas_pemakai.nama_pegawai AS petugas_nama,
            pasien_m.created_date AS tgl_pembuatan,
            petugas_pembuat.nama_pegawai AS pembuat_nama,
            pasien_m.penanggungjawabtera_nama,
            pasien_m.penanggungjawabtera_hubungan,
            pasien_m.penanggungjawabtera_alamat,
            pasienubahdata_t.alasan_ubahdata,
            pasien_m.nopeserta_bpjs,
            pasien_m.kode_pos,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan, kelurahan_m.kode_kelurahan) AS kode_kelurahan_kemendagri,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan) AS kode_kecamatan_kemendagri,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kabupaten_kemendagri,
            propinsi_m.kode_propinsi AS kode_propinsi_kemendagri,
            'ID'::text AS kode_negara_kemendagri
            FROM (((((((((((((((pasien_m
            LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN suku_m suku ON ((pasien_m.suku_id = suku.suku_id)))
            LEFT JOIN dokrekammedis_m ON ((pasien_m.pasien_id = dokrekammedis_m.pasien_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN loginpemakai_k petugas_1 ON ((pasien_m.last_modified_by = petugas_1.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pemakai ON ((petugas_1.pegawai_id = petugas_pemakai.pegawai_id)))
            LEFT JOIN loginpemakai_k pembuat ON ((pasien_m.created_by = pembuat.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pembuat ON ((pembuat.pegawai_id = petugas_pembuat.pegawai_id)))
            LEFT JOIN ( SELECT pendaftaran_t.pasien_id,
            sum(pemberianpiutang_t.total_sisapiutang) AS total_sisapiutang
            FROM (pemberianpiutang_t
            JOIN pendaftaran_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            GROUP BY pendaftaran_t.pasien_id) piutang ON ((pasien_m.pasien_id = piutang.pasien_id)))
            LEFT JOIN pasienubahdata_t ON (((pasien_m.pasien_id = pasienubahdata_t.pasien_id) AND (pasienubahdata_t.is_active IS TRUE) AND (pasienubahdata_t.is_deleted IS FALSE))))
            LEFT JOIN ( SELECT a.propinsi_id,
            a.propinsi_nama,
            a.kode_propinsi
            FROM propinsi_m a) propinsi_m ON ((pasien_m.propinsi_id = propinsi_m.propinsi_id)))
            LEFT JOIN ( SELECT a.kabupaten_id,
            a.kabupaten_nama,
            a.kode_kabupaten
            FROM kabupaten_m a) kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN ( SELECT a.kecamatan_id,
            a.kecamatan_nama,
            a.kode_kecamatan
            FROM kecamatan_m a) kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN ( SELECT a.kelurahan_id,
            a.kelurahan_nama,
            a.kode_kelurahan
            FROM kelurahan_m a) kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_kode,
            a.lookup_name
            FROM lookup_m a) negara ON (((pasien_m.warga_negara)::integer = negara.lookup_id)))
            WHERE ((pasien_m.is_active = true) AND (pasien_m.is_deleted = false))
            ;");
            $this->execute('
                ALTER TABLE public.pasien_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220114_073840_migrate_hotfix_smh_pasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220114_073840_migrate_hotfix_smh_pasien_v cannot be reverted.\n";

        return false;
    }
    */
}
