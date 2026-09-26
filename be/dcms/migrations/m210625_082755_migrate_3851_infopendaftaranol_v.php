<?php

use yii\db\Migration;

/**
 * Class m210625_082755_migrate_3851_infopendaftaranol_v
 */
class m210625_082755_migrate_3851_infopendaftaranol_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopendaftaranol_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopendaftaranol_v\" AS
            SELECT pendaftaranol_t.pendaftaranol_id,
            pendaftaranol_t.pendaftaran_id,
            pendaftaranol_t.no_pendaftaranol,
            pendaftaranol_t.status_pasien,
            pendaftaranol_t.pasien_id,
            pasien_m.no_rekam_medik,
            fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            pasien_m.alamat_pasien,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jk,
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
            fgetnamalookup(pendaftaranol_t.status_daftar_ol) AS status_daftar,
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
            fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi,
            fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten,
            fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan,
            fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan,
            pasien_m.rt,
            pasien_m.rw,
            pasien_m.no_telepon_pasien,
            pasien_m.agama AS agama_id,
            fgetnamalookup((pasien_m.agama)::integer) AS agama_nama,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            pasien_m.statusperkawinan AS statusperkawinan_id,
            fgetnamalookup((pasien_m.statusperkawinan)::integer) AS statusperkawinan_nama,
            pasien_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            pasien_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pendaftaranol_t.no_rujukan,
            pendaftaranol_t.jenis_reservasi,
            fgetnamalookup((pendaftaranol_t.jenis_reservasi)::integer) AS jenis_reservasinama,
            fgetnamalookup((pendaftaranol_t.jenisidentitas)::integer) AS jenis_identitas,
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
            b.no_antrian AS antrian_dokter
            FROM ((((((((((pendaftaranol_t
            LEFT JOIN pasien_m ON ((pendaftaranol_t.pasien_id = pasien_m.pasien_id)))
            JOIN ruangan_m ON ((pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN pegawai_m ON ((pendaftaranol_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN carabayar_m ON ((pendaftaranol_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN penjamin_m ON ((pendaftaranol_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN jenispasien_m ON ((pendaftaranol_t.klasifikasipasien_id = jenispasien_m.jenispasien_id)))
            LEFT JOIN antrian_t ON ((pendaftaranol_t.antrian_id = antrian_t.antrian_id)))
            LEFT JOIN antrian_t b ON ((b.antrianasal_id = antrian_t.antrian_id)))
            ;");
        $this->execute('
            ALTER TABLE public.infopendaftaranol_v OWNER TO postgres;
            ');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210625_082755_migrate_3851_infopendaftaranol_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210625_082755_migrate_3851_infopendaftaranol_v cannot be reverted.\n";

        return false;
    }
    */
}
