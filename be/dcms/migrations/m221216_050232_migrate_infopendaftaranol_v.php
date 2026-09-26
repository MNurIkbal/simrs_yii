<?php

use yii\db\Migration;

/**
 * Class m221216_050232_migrate_infopendaftaranol_v
 */
class m221216_050232_migrate_infopendaftaranol_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopendaftaranol_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopendaftaranol_v
        AS SELECT pendaftaranol_t.pendaftaranol_id,
            pendaftaranol_t.pendaftaran_id,
            pendaftaranol_t.no_pendaftaranol,
            pendaftaranol_t.status_pasien,
            pendaftaranol_t.pasien_id,
            pasien_m.no_rekam_medik,
            look_namadepan.lookup_name AS nama_depan,
            pasien_m.nama_pasien,
                CASE
                    WHEN pendaftaranol_t.pasien_id IS NULL THEN pendaftaranol_t.tanggal_lahir
                    ELSE pasien_m.tanggal_lahir
                END AS tanggal_lahir,
            pasien_m.alamat_pasien,
                CASE
                    WHEN pendaftaranol_t.pasien_id IS NULL THEN pendaftaranol_t.jeniskelamin
                    ELSE pasien_m.jeniskelamin
                END AS jeniskelamin,
                CASE
                    WHEN pendaftaranol_t.pasien_id IS NULL THEN look_jeniskelaminol.lookup_name
                    ELSE look_jeniskelamin.lookup_name
                END AS jk,
            pendaftaranol_t.no_asuransi,
            pendaftaranol_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaranol_t.pegawai_id,
            pegawai_m.nama_pegawai,
            pendaftaranol_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaranol_t.jam_kunjungan,
            pendaftaranol_t.created_date AS tgl_pendaftaran,
            pendaftaranol_t.tgl_pendaftaranol AS tgl_kunjungan,
            pendaftaranol_t.status_daftar_ol,
            look_statuspendaftaran.lookup_name AS status_daftar,
            pendaftaranol_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaranol_t.antrian_id,
            antrian_t.no_antrian,
            pendaftaranol_t.klasifikasipasien_id,
            jenispasien_m.jenispasien_id AS klasifikasipasien_kode,
            jenispasien_m.statuspasien_nama AS klasifikasipasien_nama,
            pendaftaranol_t.jadwaldokter_id,
            pendaftaranol_t.jam_mulai,
            pendaftaranol_t.jam_tutup,
            pendaftaranol_t.created_by,
            pasien_m.no_identitas_pasien,
            propinsi_m.propinsi_nama AS propinsi,
            kabupaten_m.kabupaten_nama AS kabupaten,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasien_m.rt,
            pasien_m.rw,
            pasien_m.no_telepon_pasien,
            pasien_m.agama AS agama_id,
            look_agama.lookup_name AS agama_nama,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            pasien_m.statusperkawinan AS statusperkawinan_id,
            look_statusperkawinan.lookup_name AS statusperkawinan_nama,
            pasien_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            pasien_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pendaftaranol_t.no_rujukan,
            pendaftaranol_t.jenis_reservasi,
            look_jenisreservasi.lookup_name AS jenis_reservasinama,
            look_jenisidentitas.lookup_name AS jenis_identitas,
            pendaftaranol_t.jenisidentitas AS jenisidentitas_id_ol,
            pendaftaranol_t.no_identitas_pasien AS no_identitas_pasien_ol,
            pendaftaranol_t.namadepan AS namadepan_ol,
            pendaftaranol_t.nama_pasien AS nama_pasien_ol,
            pendaftaranol_t.tempat_lahir AS tempat_lahir_ol,
            pendaftaranol_t.tanggal_lahir AS tanggal_lahir_ol,
            pendaftaranol_t.jeniskelamin AS jeniskelamin_ol,
            pendaftaranol_t.no_telepon_pasien AS no_telepon_pasien_ol,
            pendaftaranol_t.alamat_pasien AS alamat_pasien_ol,
            b.antrian_id AS antrian_dokter_id,
            b.no_antrian AS antrian_dokter,
            antrian_t.slot_sequence,
            antrian_t.status_antrian,
            pendaftaranol_t.keterangan,
            pendaftaranol_t.additional_data::json ->> 'bpjs'::text AS data_bpjs,
            pegawai_m.kode_dokter_bpjs,
            ruangan_m.kode_ruangan_bpjs,
            asalreservasi_m.asalreservasi_id,
            asalreservasi_m.asalreservasi_nama,
            dokterperujuk.pegawai_id AS dokterperujuk_id,
            dokterperujuk.nama_pegawai AS dokterperujuk_nama,
            pendaftaranol_t.is_postranap
           FROM pendaftaranol_t
             LEFT JOIN ( SELECT a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.alamat_pasien,
                    a.jeniskelamin,
                    a.no_identitas_pasien,
                    a.rt,
                    a.rw,
                    a.no_telepon_pasien,
                    a.agama,
                    a.nama_ibu,
                    a.nama_ayah,
                    a.pendidikan_id,
                    a.pekerjaan_id,
                    a.pasien_id,
                    a.namadepan,
                    a.propinsi_id,
                    a.kabupaten_id,
                    a.kecamatan_id,
                    a.kelurahan_id,
                    a.statusperkawinan
                   FROM pasien_m a) pasien_m ON pendaftaranol_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.kode_ruangan_bpjs
                   FROM ruangan_m a) ruangan_m ON pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.kode_dokter_bpjs
                   FROM pegawai_m a) pegawai_m ON pendaftaranol_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaranol_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaranol_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.pendidikan_id,
                    a.pendidikan_nama
                   FROM pendidikan_m a) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             LEFT JOIN ( SELECT a.jenispasien_id,
                    a.statuspasien_nama
                   FROM jenispasien_m a) jenispasien_m ON pendaftaranol_t.klasifikasipasien_id = jenispasien_m.jenispasien_id
             LEFT JOIN ( SELECT a.no_antrian,
                    a.slot_sequence,
                    a.status_antrian,
                    a.antrian_id
                   FROM antrian_t a) antrian_t ON pendaftaranol_t.antrian_id = antrian_t.antrian_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.no_antrian,
                    a.antrianasal_id
                   FROM antrian_t a) b ON b.antrianasal_id = antrian_t.antrian_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jeniskelaminol ON pendaftaranol_t.jeniskelamin::integer = look_jeniskelaminol.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statuspendaftaran ON pendaftaranol_t.status_daftar_ol = look_statuspendaftaran.lookup_id
             LEFT JOIN ( SELECT a.propinsi_id,
                    a.propinsi_nama
                   FROM propinsi_m a) propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
             LEFT JOIN ( SELECT a.kabupaten_id,
                    a.kabupaten_nama
                   FROM kabupaten_m a) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT a.kecamatan_id,
                    a.kecamatan_nama
                   FROM kecamatan_m a) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT a.kelurahan_id,
                    a.kelurahan_nama
                   FROM kelurahan_m a) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_agama ON pasien_m.agama::integer = look_agama.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusperkawinan ON pasien_m.statusperkawinan::integer = look_statusperkawinan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jenisreservasi ON pendaftaranol_t.jenis_reservasi::integer = look_jenisreservasi.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jenisidentitas ON pendaftaranol_t.jenisidentitas::integer = look_jenisidentitas.lookup_id
             LEFT JOIN ( SELECT a.asalreservasi_id,
                    a.asalreservasi_nama
                   FROM asalreservasi_m a) asalreservasi_m ON pendaftaranol_t.asalreservasi_id = asalreservasi_m.asalreservasi_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokterperujuk ON pendaftaranol_t.dokterperujuk_id = dokterperujuk.pegawai_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221216_050232_migrate_infopendaftaranol_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221216_050232_migrate_infopendaftaranol_v cannot be reverted.\n";

        return false;
    }
    */
}
