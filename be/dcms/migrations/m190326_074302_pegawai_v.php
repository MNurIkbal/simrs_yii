<?php

use yii\db\Migration;

/**
 * Class m190326_074302_pegawai_v
 */
class m190326_074302_pegawai_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP VIEW pegawai_v;
        ");
        $this->execute("
            CREATE OR REPLACE VIEW pegawai_v AS 
             SELECT ruangan_m.ruangan_id,
                ruangan_m.instalasi_id,
                ruangan_m.ruangan_nama,
                pegawai_m.pegawai_id,
                pegawai_m.gelardepan,
                pegawai_m.nama_pegawai,
                pegawai_m.gelarbelakang,
                pegawai_m.jeniskelamin,
                pegawai_m.tempatlahir_pegawai,
                pegawai_m.tgl_lahirpegawai,
                pegawai_m.alamat_pegawai,
                pegawai_m.agama,
                pegawai_m.alamatemail,
                pegawai_m.notelp_pegawai,
                pegawai_m.nomobile_pegawai,
                pegawai_m.photopegawai,
                pendidikan_m.pendidikan_id,
                pendidikan_m.pendidikan_nama,
                pendidikankualifikasi_m.pendkualifikasi_id,
                pendidikankualifikasi_m.pendkualifikasi_nama,
                pegawai_m.nomorindukpegawai,
                pegawai_m.pangkat_id,
                pegawai_m.kelompokpegawai_id,
                pegawai_m.jabatan_id,
                jabatan_m.jabatan_nama,
                ruanganpegawai_mp.is_deleted,
                instalasi_m.instalasi_nama,
                kelompokpegawai_m.kelompokpegawai_nama,
                kelompokpegawai_m.kelompokpegawai_namalainnya,
                kelompokpegawai_m.kelompokpegawai_fungsi,
                concat(gelar_depan.lookup_name, ' ', pegawai_m.nama_pegawai, ' ', gelarbelakang_m.gelarbelakang_nama) AS nama,
                ruanganpegawai_mp.is_active
               FROM ruanganpegawai_mp
                 JOIN ruangan_m ON ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 JOIN pegawai_m ON ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id
                 LEFT JOIN pendidikan_m ON pegawai_m.pendidikan_id = pendidikan_m.pendidikan_id
                 LEFT JOIN pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
                 LEFT JOIN jabatan_m ON pegawai_m.jabatan_id = jabatan_m.jabatan_id
                 LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
                 LEFT JOIN lookup_m gelar_depan ON pegawai_m.gelardepan::integer = gelar_depan.lookup_id
                 LEFT JOIN gelarbelakang_m ON pegawai_m.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
              WHERE pegawai_m.is_active = true AND pegawai_m.is_deleted = false AND ruanganpegawai_mp.is_active = true AND ruanganpegawai_mp.is_deleted = false;
        ");
        $this->execute("
            ALTER TABLE pegawai_v
              OWNER TO postgres;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190326_074302_pegawai_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190326_074302_pegawai_v cannot be reverted.\n";

        return false;
    }
    */
}
