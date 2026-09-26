<?php

use yii\db\Migration;

/**
 * Class m210209_030612_migrate_20210209_sy_3352_keluargapasien_v
 */
class m210209_030612_migrate_20210209_sy_3352_keluargapasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.keluargapasien_v;');
        $this->execute("
            CREATE VIEW \"public\".\"keluargapasien_v\" AS
            SELECT keluargapasien_t.keluargapasien_id,
            keluargapasien_t.keluarga_nama,
            keluargapasien_t.keluarga_jk,
            keluargapasien_t.keluarga_hubungan,
            keluargapasien_t.keluarga_alamat,
            keluargapasien_t.keluarga_no_telepon,
            keluargapasien_t.keluarga_namadepan,
            keluargapasien_t.keluarga_propinsi_id,
            propinsi_m.propinsi_nama AS keluarga_propinsi_nama,
            keluargapasien_t.keluarga_kabupaten_id,
            kabupaten_m.kabupaten_nama AS keluarga_kabupaten_nama,
            keluargapasien_t.keluarga_kecamatan_id,
            kecamatan_m.kecamatan_nama AS keluarga_kecamatan_nama,
            keluargapasien_t.keluarga_kelurahan_id,
            kelurahan_m.kelurahan_nama AS keluarga_kelurahan_nama,
            keluargapasien_t.keluarga_pekerjaan_id,
            pekerjaan_m.pekerjaan_nama AS keluarga_pekerjaan_nama,
            keluargapasien_t.keluarga_rt,
            keluargapasien_t.keluarga_rw,
            keluargapasien_t.pasien_id,
            pasien_m.nama_pasien,
            fgetnamalookup((keluargapasien_t.keluarga_namadepan)::integer) AS keluarga_namadepan_nama
            FROM ((((((keluargapasien_t
            LEFT JOIN propinsi_m ON ((keluargapasien_t.keluarga_propinsi_id = propinsi_m.propinsi_id)))
            LEFT JOIN kabupaten_m ON ((keluargapasien_t.keluarga_kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN kecamatan_m ON ((keluargapasien_t.keluarga_kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN kelurahan_m ON ((keluargapasien_t.keluarga_kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN pekerjaan_m ON ((keluargapasien_t.keluarga_pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            JOIN pasien_m ON ((keluargapasien_t.pasien_id = pasien_m.pasien_id)))
            ;");
            $this->execute('
                ALTER TABLE public.keluargapasien_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210209_030612_migrate_20210209_sy_3352_keluargapasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210209_030612_migrate_20210209_sy_3352_keluargapasien_v cannot be reverted.\n";

        return false;
    }
    */
}
