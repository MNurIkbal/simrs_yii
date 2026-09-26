<?php

use yii\db\Migration;

/**
 * Class m210108_195052_hotfix_view_infopasienrd_v
 */
class m210108_195052_hotfix_view_infopasienrd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienrd_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienrd_v" AS  SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.no_rekam_medik,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                pendaftaran_t.penjamin_id,
                penjamin_m.penjamin_nama,
                pendaftaran_t.pegawai_id AS dokter_jaga_id,
                dok_jaga.nama_pegawai AS dokter_jaga,
                ( SELECT dokpj.dokterbaru_id
                       FROM gantidokterpj_t dokpj
                      WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
                     LIMIT 1) AS dokter_id,
                ( SELECT dokter.nama_pegawai
                       FROM (gantidokterpj_t dokpj
                         JOIN ( SELECT pegawai_m.pegawai_id,
                                pegawai_m.nama_pegawai
                               FROM (pegawai_m
                                 JOIN ruanganpegawai_mp ON ((pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id)))
                              WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_active = true) AND (pegawai_m.is_deleted = false))) dokter ON ((dokpj.dokterbaru_id = dokter.pegawai_id)))
                      WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
                     LIMIT 1) AS dokter,
                ( SELECT dokpj.jenis_dokter
                       FROM gantidokterpj_t dokpj
                      WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
                     LIMIT 1) AS jenis_dokter_id,
                ( SELECT fgetnamalookup(gantidokterpj_t.jenis_dokter) AS lookup_name
                       FROM gantidokterpj_t dokpj
                      WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
                     LIMIT 1) AS jenis_dokter,
                pendaftaran_t.status_periksa AS status_periksa_id,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.ruangan_id,
                ruangan_m.ruangan_nama,
                pasien_m.jeniskelamin, 
                pendaftaran_t.*::pendaftaran_t AS pendaftaran_t,
                pendaftaran_t.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                pendaftaran_t.jeniskasuspenyakit_id,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pendaftaran_t.carabayar_id,
                carabayar_m.carabayar_nama,
                pendaftaran_t.umur,
                pasien_m.tanggal_lahir,
                pasien_m.photopasien,
                bpjs_t.nosep,
                carabayar_m.carabayar_warna,
                carabayar_m.carabayar_kode_warna
               FROM ((((((((((pendaftaran_t
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN pegawai_m dok_jaga ON ((pendaftaran_t.pegawai_id = dok_jaga.pegawai_id)))
                 LEFT JOIN gantidokterpj_t ON ((pendaftaran_t.pendaftaran_id = gantidokterpj_t.pendaftaran_id)))
                 LEFT JOIN pegawai_m dok_dpjp ON ((gantidokterpj_t.dokterbaru_id = dok_dpjp.pegawai_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
              WHERE ((pendaftaran_t.instalasi_id = 2) AND (pendaftaran_t.pasienpulang_id IS NULL));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210108_195052_hotfix_view_infopasienrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210108_195052_hotfix_view_infopasienrd_v cannot be reverted.\n";

        return false;
    }
    */
}
