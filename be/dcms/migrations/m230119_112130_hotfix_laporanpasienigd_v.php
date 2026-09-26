<?php

use yii\db\Migration;

/**
 * Class m230119_112130_hotfix_laporanpasienigd_v
 */
class m230119_112130_hotfix_laporanpasienigd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanpasienigd_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporanpasienigd_v
        AS SELECT pendaftaran_t.pendaftaran_id,
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
                         JOIN ( SELECT a.pegawai_id,
                                a.nama_pegawai,
                                a.kelompokpegawai_id,
                                a.is_active,
                                a.is_deleted
                               FROM pegawai_m a) dokter ON dokpj.dokterbaru_id = dokter.pegawai_id AND dokter.kelompokpegawai_id = 1 AND dokter.is_active = true AND dokter.is_deleted = false
                      WHERE dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id AND dokpj.jenis_dokter = 485 AND dokpj.is_active = true AND dokpj.is_deleted = false
                     LIMIT 1)
                    ELSE dokter_jaga.nama_pegawai
                END AS dokter,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.status_periksa,
            status.lookup_name AS status_periksa_nama,
            pasien_m.jeniskelamin,
            triase_t.is_emergency,
                CASE
                    WHEN triase_t.is_doa IS TRUE THEN 'Emergency Death on Arrival'::text
                    ELSE
                    CASE
                        WHEN triase_t.is_emergency IS TRUE THEN 'False Emergency'::text
                        ELSE 'True Emergency'::text
                    END
                END AS status_emergency,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_jaga ON pendaftaran_t.pegawai_id = dokter_jaga.pegawai_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.pendaftaran_id
                   FROM pasienpulang_t a) pasienpulang_t ON pasienpulang_t.pasienpulang_id = pendaftaran_t.pasienpulang_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.is_emergency,
                    a.is_doa
                   FROM triase_t a) triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) status ON pendaftaran_t.status_periksa::integer = status.lookup_id
          WHERE pendaftaran_t.instalasi_id = 2;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230119_112130_hotfix_laporanpasienigd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230119_112130_hotfix_laporanpasienigd_v cannot be reverted.\n";

        return false;
    }
    */
}
