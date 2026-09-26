<?php

use yii\db\Migration;

/**
 * Class m190614_070807_laporanpasienigd_v_del_update
 */
class m190614_070807_laporanpasienigd_v_del_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
     {
        $this->execute('
    DROP VIEW laporanpasienigd_v_del;
        ');

        $this->execute('
   CREATE OR REPLACE VIEW laporanpasienigd_v_del AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jk.lookup_name AS jenis_kelamin,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.pegawai_id AS dokter_jaga_id,
    dokter_jaga.nama_pegawai AS dokter_jaga,
        CASE
            WHEN (( SELECT count(dokpj.pendaftaran_id) AS count
               FROM gantidokterpj_t dokpj
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false)) > 1 THEN ( SELECT dokpj.dokterbaru_id
               FROM gantidokterpj_t dokpj
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false
             LIMIT 1)
            ELSE pendaftaran_t.pegawai_id
        END AS dokter_id,
        CASE
            WHEN (( SELECT count(dokpj.pendaftaran_id) AS count
               FROM gantidokterpj_t dokpj
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false)) > 1 THEN ( SELECT dokter.nama_pegawai
               FROM gantidokterpj_t dokpj
                 JOIN pegawai_m dokter ON dokpj.dokterbaru_id = dokter.pegawai_id AND dokter.kelompokpegawai_id = 1 AND dokter.is_active = true AND dokter.is_deleted = false
              WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false
             LIMIT 1)
            ELSE dokter_jaga.nama_pegawai
        END AS dokter,
    pendaftaran_t.pasienpulang_id,
    stat_periksa.lookup_name AS status_periksa
   FROM pendaftaran_t
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pegawai_m dokter_jaga ON pendaftaran_t.pegawai_id = dokter_jaga.pegawai_id
     LEFT JOIN lookup_m stat_verifikasi ON pendaftaran_t.status_verifikasi = stat_verifikasi.lookup_id
     LEFT JOIN lookup_m stat_periksa ON pendaftaran_t.status_periksa::integer = stat_periksa.lookup_id
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id
     LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienpulang_id = pendaftaran_t.pasienpulang_id
  WHERE pendaftaran_t.instalasi_id = 2;

        ');

        $this->execute('
ALTER TABLE laporanpasienigd_v_del
  OWNER TO postgres;

        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190614_070807_laporanpasienigd_v_del_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190614_070807_laporanpasienigd_v_del_update cannot be reverted.\n";

        return false;
    }
    */
}
