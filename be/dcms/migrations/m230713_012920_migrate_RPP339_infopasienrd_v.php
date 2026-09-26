<?php

use yii\db\Migration;

/**
 * Class m230713_012920_migrate_RPP339_infopasienrd_v
 */
class m230713_012920_migrate_RPP339_infopasienrd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienrd_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopasienrd_v
        AS SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            look_jeniskelamin.lookup_name AS jenis_kelamin,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.pegawai_id AS dokter_jaga_id,
            dok_jaga.nama_pegawai AS dokter_jaga,
            gantidokterpj_t.dokterbaru_id AS dokter_id,
            dokjp_nama.nama_pegawai AS dokter,
            gantidokterpj_t.jenis_dokter AS jenis_dokter_id,
            look_jenisdokter.lookup_name AS jenis_dokter,
            pendaftaran_t.status_periksa AS status_periksa_id,
            look_statusperiksa.lookup_name AS status_periksa,
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
            carabayar_m.carabayar_kode_warna,
            pendaftaran_t.pasienpulang_id,
            pekerjaan_m.pekerjaan_nama,
            pasien_m.catatanpenting_pasien,
            asesmenperawatrd_t.is_alergi AS alergi,
            pasien_m.alamat_pasien,
            pasien_m.no_telepon_pasien,
            pasien_m.no_identitas_pasien,
            look_jenis_identitas.lookup_name AS jenisidentitas_nama
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.photopasien,
                    a.catatanpenting_pasien,
                    a.alamat_pasien,
                    a.no_telepon_pasien,
                    a.pekerjaan_id,
                    a.no_identitas_pasien,
                    a.jenisidentitas
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dok_jaga ON pendaftaran_t.pegawai_id = dok_jaga.pegawai_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.dokterbaru_id,
                    a.jenis_dokter,
                    a.is_active,
                    a.is_deleted
                   FROM gantidokterpj_t a
                  WHERE a.jenis_dokter = 485 AND a.is_active = true AND a.is_deleted = false
                 LIMIT 1) gantidokterpj_t ON pendaftaran_t.pendaftaran_id = gantidokterpj_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a
                     JOIN ( SELECT b.pegawai_id
                           FROM ruanganpegawai_mp b) ruanganpegawai_mp ON a.pegawai_id = ruanganpegawai_mp.pegawai_id) dokjp_nama ON gantidokterpj_t.dokterbaru_id = dokjp_nama.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id
                   FROM pegawai_m a) dok_dpjp ON gantidokterpj_t.dokterbaru_id = dok_dpjp.pegawai_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_warna,
                    a.carabayar_kode_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.pekerjaan_id,
                    a.is_alergi
                   FROM asesmenperawatrd_t a) asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON COALESCE(asesmenperawatrd_t.pekerjaan_id, pasien_m.pekerjaan_id) = pekerjaan_m.pekerjaan_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusperiksa ON pendaftaran_t.status_periksa::integer = look_statusperiksa.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jenisdokter ON gantidokterpj_t.jenis_dokter = look_jenisdokter.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_type
                   FROM lookup_m a) look_jenis_identitas ON pasien_m.jenisidentitas::integer = look_jenis_identitas.lookup_id AND look_jenis_identitas.lookup_type::text = 'jenis_identitas'::text
          WHERE pendaftaran_t.instalasi_id = 2;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230713_012920_migrate_RPP339_infopasienrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230713_012920_migrate_RPP339_infopasienrd_v cannot be reverted.\n";

        return false;
    }
    */
}
