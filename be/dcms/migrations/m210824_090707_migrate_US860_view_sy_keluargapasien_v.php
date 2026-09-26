<?php

use yii\db\Migration;

/**
 * Class m210824_090707_migrate_US860_view_sy_keluargapasien_v
 */
class m210824_090707_migrate_US860_view_sy_keluargapasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.sy_keluargapasien_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_keluargapasien_v\" AS
            SELECT keluargapasien_t.keluargapasien_id,
            keluargapasien_t.keluarga_nama,
            fgetnamalookup((keluargapasien_t.keluarga_jk)::integer) AS keluarga_jk,
            fgetnamalookup((keluargapasien_t.keluarga_hubungan)::integer) AS keluarga_hubungan,
            keluargapasien_t.keluarga_alamat,
            keluargapasien_t.keluarga_no_telepon,
            fgetnamalookup((keluargapasien_t.keluarga_namadepan)::integer) AS keluarga_namadepan,
            keluargapasien_t.keluarga_propinsi_id,
            propinsi_m.propinsi_nama,
            keluargapasien_t.keluarga_kabupaten_id,
            kabupaten_m.kabupaten_nama,
            kecamatan_m.kode_kecamatan AS keluarga_kecamatan_id,
            kecamatan_m.kecamatan_nama,
            kelurahan_m.kode_kelurahan AS keluarga_kelurahan_id,
            kelurahan_m.kelurahan_nama,
            keluargapasien_t.keluarga_pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pekerjaan_m.pekerjaan_kode,
            keluargapasien_t.keluarga_rt,
            keluargapasien_t.keluarga_rw,
            keluargapasien_t.pasien_id,
            pasien_m.nama_pasien,
            kode_keluarga.lookup_kode AS keluarga_hubungan_kode,
            look_alamatdpn.lookup_name AS alamat_depan,
            kelurahan_m.kode_pos,
            pasien_m.no_rekam_medik
            FROM ((((((((keluargapasien_t
            LEFT JOIN propinsi_m ON ((keluargapasien_t.keluarga_propinsi_id = propinsi_m.propinsi_id)))
            LEFT JOIN kabupaten_m ON ((keluargapasien_t.keluarga_kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN kecamatan_m ON ((keluargapasien_t.keluarga_kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN kelurahan_m ON ((keluargapasien_t.keluarga_kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN pekerjaan_m ON ((keluargapasien_t.keluarga_pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            JOIN pasien_m ON ((keluargapasien_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN lookup_m kode_keluarga ON (((keluargapasien_t.keluarga_hubungan)::integer = kode_keluarga.lookup_id)))
            LEFT JOIN lookup_m look_alamatdpn ON (((keluargapasien_t.alamatdepan)::text = ((look_alamatdpn.lookup_id)::character varying)::text)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_keluargapasien_v OWNER TO postgres;
            ');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210824_090707_migrate_US860_view_sy_keluargapasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210824_090707_migrate_US860_view_sy_keluargapasien_v cannot be reverted.\n";

        return false;
    }
    */
}
