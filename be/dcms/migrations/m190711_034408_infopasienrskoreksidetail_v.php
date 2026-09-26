<?php

use yii\db\Migration;

/**
 * Class m190711_034408_infopasienrskoreksidetail_v
 */
class m190711_034408_infopasienrskoreksidetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('
         DROP VIEW public.infopasienmeninggal_v;
              ');

        $this->execute('
         DROP VIEW public.infopasienrskoreksidetail_v;
              ');

        $this->execute("
        CREATE OR REPLACE VIEW public.infopasienrskoreksidetail_v AS 
 SELECT 'RJ'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmorbiditas_t.pasienmorbiditas_id AS diagnosapasien_id,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::text
        END AS diagnosa_masuk,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::text
        END AS diagnosa_utama,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 3 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::text
        END AS diagnosa_penyerta,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 6 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::text
        END AS diagnosa_terapi
   FROM pendaftaran_t
     JOIN pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
UNION ALL
 SELECT 'RD'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    NULL::integer AS pasienadmisi_id,
    cppt_t.cppt_id AS diagnosapasien_id,
    NULL::text AS diagnosa_masuk,
    cppt_t.a_diag_utama AS diagnosa_utama,
    cppt_t.a_diag_penyerta AS diagnosa_penyerta,
    NULL::text AS diagnosa_terapi
   FROM pendaftaran_t
     JOIN gantidokterpj_t ON pendaftaran_t.pendaftaran_id = gantidokterpj_t.pendaftaran_id AND gantidokterpj_t.jenis_dokter = 485
     JOIN cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND gantidokterpj_t.dokterbaru_id = cppt_t.pegawai_id
UNION ALL
 SELECT 'RI'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    resumemedisri_t.resumemedisri_id AS diagnosapasien_id,
    resumemedisri_t.diag_masuk AS diagnosa_masuk,
    resumemedisri_t.diag_utama AS diagnosa_utama,
    resumemedisri_t.diag_penyerta AS diagnosa_penyerta,
    resumemedisri_t.prosedur_diag AS diagnosa_terapi
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id;

              ");

        $this->execute('
        ALTER TABLE public.infopasienrskoreksidetail_v
  OWNER TO postgres');

       $this->execute("
       CREATE OR REPLACE VIEW public.infopasienmeninggal_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pasienpulang_id,
    pasienpulang_t.tgl_meninggal,
    peg_ruangan.nama_pegawai AS pegawai_ruangan,
    jabatan_m.jabatan_nama,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.alamat_pasien,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    jk.lookup_name AS jenis_kelamin,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_nama,
    ins_asal.instalasi_nama AS instalasi_asal,
    pendaftaran_t.penanggungjawab_id,
    persetujuanjenazah_t.nama_pj AS penanggungjawab_nama,
    persetujuanjenazah_t.umur AS umur_pj,
    jk_pj.lookup_name AS jenis_kelamin_pj,
    persetujuanjenazah_t.alamat,
    persetujuanjenazah_t.no_kontak,
    hub.lookup_name AS hubungan_kel,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    peg_jenazah.nama_pegawai AS pegawai_jenazah,
    jab_jenazah.jabatan_nama AS jabatan_pegjenazah,
    pasienmasukpenunjang_t.status_periksa,
    status_periksa.lookup_name AS status_periksa_nama,
    infopasienrskoreksidetail_v.diagnosa_utama::json ->> 'text'::text AS diagnosa_nama,
    ambiljenazah_t.tgl_pengambilan,
    persetujuanjenazah_t.kondisi,
    ambiljenazah_t.tgl_lahir AS tgl_lahir_pj,
    ambiljenazah_t.tempat_lahir AS tempat_lahir_pj
   FROM pendaftaran_t
     JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id = 4 AND pasienpulang_t.pasienbatalpulang_id IS NULL
     JOIN loginpemakai_k ON pasienpulang_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m peg_ruangan ON loginpemakai_k.pegawai_id = peg_ruangan.pegawai_id
     JOIN jabatan_m ON peg_ruangan.jabatan_id = jabatan_m.jabatan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
     JOIN instalasi_m ins_asal ON ruangan_m.instalasi_id = ins_asal.instalasi_id
     LEFT JOIN persetujuanjenazah_t ON pendaftaran_t.pendaftaran_id = persetujuanjenazah_t.pendaftaran_id AND persetujuanjenazah_t.is_deleted = false
     LEFT JOIN lookup_m jk_pj ON persetujuanjenazah_t.jeniskelamin_id = jk_pj.lookup_id
     LEFT JOIN lookup_m hub ON persetujuanjenazah_t.hubungan_keluarga = hub.lookup_id
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38 AND pasienmasukpenunjang_t.is_deleted = false
     JOIN pegawai_m peg_jenazah ON pasienmasukpenunjang_t.pegawai_id = peg_jenazah.pegawai_id
     JOIN jabatan_m jab_jenazah ON peg_jenazah.jabatan_id = jab_jenazah.jabatan_id
     LEFT JOIN infopasienrskoreksidetail_v ON pendaftaran_t.pendaftaran_id = infopasienrskoreksidetail_v.pendaftaran_id
     LEFT JOIN lookup_m status_periksa ON pasienmasukpenunjang_t.status_periksa::integer = status_periksa.lookup_id
     LEFT JOIN ambiljenazah_t ON pendaftaran_t.pendaftaran_id = ambiljenazah_t.pendaftaran_id
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.pasienpulang_id,
    pasienpulang_t.tgl_meninggal,
    peg_ruangan.nama_pegawai AS pegawai_ruangan,
    jabatan_m.jabatan_nama,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.alamat_pasien,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    jk.lookup_name AS jenis_kelamin,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_nama,
    ins_asal.instalasi_nama AS instalasi_asal,
    pendaftaran_t.penanggungjawab_id,
    persetujuanjenazah_t.nama_pj AS penanggungjawab_nama,
    persetujuanjenazah_t.umur AS umur_pj,
    jk_pj.lookup_name AS jenis_kelamin_pj,
    persetujuanjenazah_t.alamat,
    persetujuanjenazah_t.no_kontak,
    hub.lookup_name AS hubungan_kel,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    peg_jenazah.nama_pegawai AS pegawai_jenazah,
    jab_jenazah.jabatan_nama AS jabatan_pegjenazah,
    pasienmasukpenunjang_t.status_periksa,
    status_periksa.lookup_name AS status_periksa_nama,
    infopasienrskoreksidetail_v.diagnosa_utama::json ->> 'text'::text AS diagnosa_nama,
    ambiljenazah_t.tgl_pengambilan,
    persetujuanjenazah_t.kondisi,
    ambiljenazah_t.tgl_lahir AS tgl_lahir_pj,
    ambiljenazah_t.tempat_lahir AS tempat_lahir_pj
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id = 4 AND pasienpulang_t.pasienbatalpulang_id IS NULL
     JOIN loginpemakai_k ON pasienpulang_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m peg_ruangan ON loginpemakai_k.pegawai_id = peg_ruangan.pegawai_id
     JOIN jabatan_m ON peg_ruangan.jabatan_id = jabatan_m.jabatan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
     JOIN instalasi_m ins_asal ON ruangan_m.instalasi_id = ins_asal.instalasi_id
     LEFT JOIN persetujuanjenazah_t ON pendaftaran_t.pendaftaran_id = persetujuanjenazah_t.pendaftaran_id AND persetujuanjenazah_t.is_deleted = false
     LEFT JOIN lookup_m jk_pj ON persetujuanjenazah_t.jeniskelamin_id = jk_pj.lookup_id
     LEFT JOIN lookup_m hub ON persetujuanjenazah_t.hubungan_keluarga = hub.lookup_id
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38 AND pasienmasukpenunjang_t.is_deleted = false
     JOIN pegawai_m peg_jenazah ON pasienmasukpenunjang_t.pegawai_id = peg_jenazah.pegawai_id
     JOIN jabatan_m jab_jenazah ON peg_jenazah.jabatan_id = jab_jenazah.jabatan_id
     LEFT JOIN infopasienrskoreksidetail_v ON pendaftaran_t.pasienadmisi_id = infopasienrskoreksidetail_v.pasienadmisi_id
     LEFT JOIN lookup_m status_periksa ON pasienmasukpenunjang_t.status_periksa::integer = status_periksa.lookup_id
     LEFT JOIN ambiljenazah_t ON pendaftaran_t.pasienadmisi_id = ambiljenazah_t.pasienadmisi_id;
");

       $this->execute('
        ALTER TABLE public.infopasienmeninggal_v
  OWNER TO postgres;');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190711_034408_infopasienrskoreksidetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190711_034408_infopasienrskoreksidetail_v cannot be reverted.\n";

        return false;
    }
    */
}
