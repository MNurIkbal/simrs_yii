<?php

use yii\db\Migration;

/**
 * Class m190823_040626_perubahan_typedata_json
 */
class m190823_040626_perubahan_typedata_json extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      
// view terkait
      $this->execute('DROP VIEW if exists public.infopasienmeninggal_v;');
      $this->execute('DROP VIEW if exists public.infopasienrskoreksidetail_v;');
      $this->execute('DROP VIEW if exists public.inforeseptur_v;');
      $this->execute('DROP VIEW if exists public.cppt_v;');
      $this->execute('DROP VIEW if exists public.pasienrsambulan_v;');
      $this->execute('DROP VIEW if exists public.infopemesanandarahhd_v;');
      $this->execute('DROP VIEW if exists public.infopemesanandarah_v;');
      $this->execute('DROP VIEW if exists public.infopasienoperasi_v;');
      $this->execute('DROP VIEW if exists public.infopasienmasihdirawat_v;');
      $this->execute('DROP VIEW if exists public.infomonitoringbpjs_v;');
      $this->execute('DROP VIEW if exists public.infopasienbpjsdiagnosa_v;');

      $this->execute('DROP VIEW if exists public.resumemedisri_v;');
      $this->execute('DROP VIEW if exists public.infomorbiditas_v;');
      $this->execute('DROP VIEW if exists public.laporandiagnosapasien_v;');

      $this->execute('DROP VIEW if exists public.riwayatasesmenmedis_v;');
      $this->execute('DROP VIEW if exists public.infopasiengizi_v;');
      $this->execute('DROP VIEW if exists public.infopasiengizi_v_old;');
      $this->execute('DROP VIEW if exists public.asuhangizi_v;');
      $this->execute('DROP VIEW if exists public.infopasienri_v;');

      $this->execute('DROP VIEW if exists public.pemberianobat_v;');

// asesmenawal_t
      $this->execute('TRUNCATE TABLE asesmenawal_t RESTART IDENTITY;');
      $this->execute('ALTER TABLE "public"."asesmenawal_t" 
                      ALTER COLUMN "diagnosa_masuk" TYPE json USING "diagnosa_masuk"::json;');

// cppt_t
      $this->execute('TRUNCATE TABLE cppt_t RESTART IDENTITY;');
      $this->execute('ALTER TABLE "public"."cppt_t" 
                      ALTER COLUMN "a_diag_utama" TYPE json USING "a_diag_utama"::json,
                      ALTER COLUMN "a_diag_penyerta" TYPE json USING "a_diag_penyerta"::json;');

// resumemedisri_t
      $this->execute('TRUNCATE TABLE resumemedisri_t RESTART IDENTITY;');
      $this->execute('ALTER TABLE "public"."resumemedisri_t" 
                      ALTER COLUMN "diag_masuk" TYPE json USING "diag_masuk"::json,
                      ALTER COLUMN "diag_utama" TYPE json USING "diag_utama"::json,
                      ALTER COLUMN "diag_penyerta" TYPE json USING "diag_penyerta"::json,
                      ALTER COLUMN "prosedur_diag" TYPE json USING "prosedur_diag"::json;');

// pasienmorbiditas_t    
      $this->execute('TRUNCATE TABLE pasienmorbiditas_t RESTART IDENTITY;');
      $this->execute('DROP INDEX if exists "public"."pasienmorbiditas_diagnosa_pasien_idx";');
      $this->execute('DROP INDEX if exists "public"."pasienmorbiditas_kelompokdiagnosa_idx";');
      $this->execute('DROP INDEX if exists "public"."pasienmorbiditas_pendaftaran_id_idx";');
      $this->execute('ALTER TABLE "public"."pasienmorbiditas_t" 
                      ALTER COLUMN "diagnosa_pasien" TYPE json USING "diagnosa_pasien"::json;');

            

// asesmenmedis_t
      $this->execute('TRUNCATE TABLE asesmenmedis_t RESTART IDENTITY;');
      $this->execute('ALTER TABLE "public"."asesmenmedis_t" 
                      ALTER COLUMN "r_penyakitdahulu" TYPE text USING "r_penyakitdahulu"::text,
                      ALTER COLUMN "r_penyakitkeluarga" TYPE json USING "r_penyakitkeluarga"::json,
                      ALTER COLUMN "r_imunisasi" TYPE text USING "r_imunisasi"::text,
                      ALTER COLUMN "diagnosa_id" TYPE json USING "diagnosa_id"::json;');

// pemberianobat_t
      $this->execute('TRUNCATE TABLE pemberianobat_t RESTART IDENTITY;');
      $this->execute('ALTER TABLE "public"."pemberianobat_t" 
                      ALTER COLUMN "diagnosa_id" TYPE json USING "diagnosa_id"::json;');

// asesmenmedisrd_t
      $this->execute('TRUNCATE TABLE asesmenmedisrd_t RESTART IDENTITY;');
      $this->execute('ALTER TABLE "public"."asesmenmedisrd_t" 
                      ALTER COLUMN "diagnosakerja_id" TYPE json USING "diagnosakerja_id"::json,
                      ALTER COLUMN "riwayat_dahulu" TYPE json USING "riwayat_dahulu"::json;');


// create ulang view infopasienmeninggal_v
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
    diagnosa.diagnosa_utama ->> 'text'::text AS diagnosa_nama,
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
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
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
    diagnosa.diagnosa_utama ->> 'text'::text AS diagnosa_nama,
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
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.pasienadmisi_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id) diagnosa ON pendaftaran_t.pasienadmisi_id = diagnosa.pasienadmisi_id
     LEFT JOIN lookup_m status_periksa ON pasienmasukpenunjang_t.status_periksa::integer = status_periksa.lookup_id
     LEFT JOIN ambiljenazah_t ON pendaftaran_t.pasienadmisi_id = ambiljenazah_t.pasienadmisi_id;
");
      $this->execute('ALTER TABLE public.infopasienmeninggal_v
                        OWNER TO postgres;');

// create ulang view infopasienrskoreksidetail_v
      $this->execute("
        CREATE OR REPLACE VIEW public.infopasienrskoreksidetail_v AS 
 SELECT 'RJ'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmorbiditas_t.pasienmorbiditas_id AS diagnosapasien_id,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_masuk,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_utama,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 3 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_penyerta,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 6 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_terapi
   FROM pendaftaran_t
     JOIN pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
UNION ALL
 SELECT 'RD'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    NULL::integer AS pasienadmisi_id,
    cppt_t.cppt_id AS diagnosapasien_id,
    NULL::json AS diagnosa_masuk,
    cppt_t.a_diag_utama AS diagnosa_utama,
    cppt_t.a_diag_penyerta AS diagnosa_penyerta,
    NULL::json AS diagnosa_terapi
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
      $this->execute('ALTER TABLE public.infopasienrskoreksidetail_v
                    OWNER TO postgres;');

// create ulang view inforeseptur_v
      $this->execute("
        CREATE OR REPLACE VIEW public.inforeseptur_v AS 
 SELECT reseptur_t.reseptur_id,
    reseptur_t.pasien_id,
    reseptur_t.pendaftaran_id,
    reseptur_t.pasienadmisi_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.umur,
    kelaspelayanan_m.kelaspelayanan_nama,
    reseptur_t.ruangan_id,
    reseptur_t.ruanganreseptur_id,
    reseptur_t.tglreseptur,
    reseptur_t.noresep,
    reseptur_t.penjualanresep_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    jk.lookup_name AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    ruangan_reseptur.ruangan_nama AS ruangan_reseptur,
    status_reseptur.lookup_name AS status_reseptur,
    reseptur_t.pegawai_id,
    pegawai_m.nama_pegawai,
    ruangan_reseptur.instalasi_id AS instalasi_reseptur_id,
    instalasi_reseptur.instalasi_nama AS instalasi_reseptur,
    ruangan_tujuan.instalasi_id AS instalasi_tujuan_id,
    instalasi_tujuan.instalasi_nama AS instalasi_tujuan,
    sum(obatalkes_m.harganetto) AS total_harganetto,
    antrian_t.no_antrian,
    reseptur_t.status_reseptur AS status_reseptur_id,
    reseptur_t.is_hamil,
    reseptur_t.berat_badan,
    reseptur_t.tinggi_badan,
    reseptur_t.luas_tubuh,
    reseptur_t.diagnosa_id,
    concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama) AS diagnosa_nama,
    reseptur_t.instruksi_id,
    reseptur_t.antrian_id,
    string_agg(resepturdetail_t.racikan_id::text, '-'::text) AS antrian_racikan,
    penjualanresep_t.catatan,
    iter.iter,
    penjualanresep_t.noresep AS noresep_penjualan,
    penjualanresep_t.iter AS iter_penjualan,
        CASE
            WHEN ruangan_reseptur.instalasi_id = 1 THEN anamnesa_t.riwayat_alergiobat::character varying
            WHEN ruangan_reseptur.instalasi_id = 2 THEN asesmenperawatrd_t.alergi_obat::character varying
            ELSE asesmenawal_t.nama_alergi
        END AS riwayat_alergi,
        CASE
            WHEN ruangan_reseptur.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text
            WHEN ruangan_reseptur.instalasi_id = 2 THEN cppt_t.a_diag_utama ->> 'text'::text
            WHEN ruangan_reseptur.instalasi_id = 3 THEN diag_ri.diagnosa_utama
            ELSE NULL::text
        END AS diagnosa_text
   FROM reseptur_t
     JOIN pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
     JOIN ruangan_m ruangan_reseptur ON reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN lookup_m status_reseptur ON reseptur_t.status_reseptur = status_reseptur.lookup_id
     JOIN pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m instalasi_reseptur ON ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id
     JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     JOIN resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
     JOIN obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN antrian_t ON reseptur_t.antrian_id = antrian_t.antrian_id
     LEFT JOIN diagnosa_m ON reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN penjualanresep_t ON reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     JOIN ( SELECT resepturdetail_t_1.reseptur_id,
            resepturdetail_t_1.iter
           FROM resepturdetail_t resepturdetail_t_1
          GROUP BY resepturdetail_t_1.reseptur_id, resepturdetail_t_1.iter) iter ON iter.reseptur_id = reseptur_t.reseptur_id
     LEFT JOIN anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
     LEFT JOIN asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
     LEFT JOIN asesmenawal_t ON pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id
     LEFT JOIN pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false AND pasienmorbiditas_t.kelompokdiagnosa_id = 2
     LEFT JOIN cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND cppt_t.is_deleted = false
     LEFT JOIN ( SELECT cppt_t_1.cppt_id,
            cppt_t_1.pendaftaran_id,
            cppt_t_1.pasienadmisi_id,
            cppt_t_1.a_diag_utama ->> 'text'::text AS diagnosa_utama
           FROM cppt_t cppt_t_1
             JOIN ( SELECT max(cppt_t_2.cppt_id) AS cppt_id,
                    cppt_t_2.pendaftaran_id,
                    cppt_t_2.pasienadmisi_id
                   FROM cppt_t cppt_t_2
                  WHERE cppt_t_2.is_deleted = false
                  GROUP BY cppt_t_2.pendaftaran_id, cppt_t_2.pasienadmisi_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.pasienadmisi_id = cppt_max.pasienadmisi_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) diag_ri ON pendaftaran_t.pendaftaran_id = diag_ri.pendaftaran_id AND pendaftaran_t.pasienadmisi_id = diag_ri.pasienadmisi_id
  WHERE reseptur_t.is_deleted = false AND reseptur_t.is_active = true
  GROUP BY reseptur_t.instruksi_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.umur, pasien_m.tanggal_lahir, jk.lookup_name, diagnosa_m.diagnosa_namalainnya, reseptur_t.reseptur_id, reseptur_t.pasien_id, reseptur_t.pendaftaran_id, reseptur_t.pasienadmisi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, reseptur_t.ruangan_id, reseptur_t.ruanganreseptur_id, reseptur_t.tglreseptur, reseptur_t.noresep, reseptur_t.penjualanresep_id, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_tujuan.ruangan_nama, ruangan_reseptur.ruangan_nama, status_reseptur.lookup_name, reseptur_t.pegawai_id, pegawai_m.nama_pegawai, ruangan_reseptur.instalasi_id, instalasi_reseptur.instalasi_nama, ruangan_tujuan.instalasi_id, instalasi_tujuan.instalasi_nama, antrian_t.no_antrian, reseptur_t.status_reseptur, reseptur_t.is_hamil, reseptur_t.berat_badan, reseptur_t.tinggi_badan, reseptur_t.luas_tubuh, reseptur_t.diagnosa_id, penjualanresep_t.catatan, iter.iter, penjualanresep_t.noresep, penjualanresep_t.iter, (
        CASE
            WHEN ruangan_reseptur.instalasi_id = 1 THEN anamnesa_t.riwayat_alergiobat::character varying
            WHEN ruangan_reseptur.instalasi_id = 2 THEN asesmenperawatrd_t.alergi_obat::character varying
            ELSE asesmenawal_t.nama_alergi
        END), (
        CASE
            WHEN ruangan_reseptur.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text
            WHEN ruangan_reseptur.instalasi_id = 2 THEN cppt_t.a_diag_utama ->> 'text'::text
            WHEN ruangan_reseptur.instalasi_id = 3 THEN diag_ri.diagnosa_utama
            ELSE NULL::text
        END), (concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama));
");
      $this->execute('ALTER TABLE public.inforeseptur_v
                    OWNER TO postgres;');

// create ulang view cppt_v
      $this->execute("
        CREATE OR REPLACE VIEW public.cppt_v AS 
 SELECT cppt_t.cppt_id,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    cppt_t.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    cppt_t.tgl_cppt,
    cppt_t.pegawai_id,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_nama,
    cppt_t.subject,
    cppt_t.object,
    cppt_t.a_diag_utama,
    cppt_t.a_diag_penyerta,
    cppt_t.planning,
    cppt_t.instruksi,
    cppt_t.is_verifikasi,
    pegawai_verif.nama_pegawai AS pegawai_verifikasi,
    cppt_t.tgl_verifikasi,
    pegawai_m.nama_pegawai AS pegawai_cppt,
    pemberi_instruksi.nama_pegawai AS pegawai_instruksi,
    cppt_t.pemberi_instruksi_id,
    cppt_t.is_verifikasi_verbal,
    cppt_t.tgl_verif_verbal,
    cppt_t.pegawai_verbal_id,
    pegawai_verif2.nama_pegawai AS pegawai_verifikasi_verbal,
    pasienadmisi_t.pegawai_id AS dokteradmisi_id,
    dokteradmisi.nama_pegawai AS dokteradmisi_nama,
    cppt_t.is_instruksi_pulang,
    cppt_t.catatan_dokter,
    cppt_t.catatan_perawat
   FROM cppt_t
     JOIN pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON cppt_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON cppt_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON cppt_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN kamarruangan_m ON cppt_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON cppt_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN pegawai_m ON cppt_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN pegawai_m pegawai_verif ON cppt_t.pegawai_verifikasi_id = pegawai_verif.pegawai_id
     LEFT JOIN pegawai_m pemberi_instruksi ON cppt_t.pemberi_instruksi_id = pemberi_instruksi.pegawai_id
     LEFT JOIN pegawai_m pegawai_verif2 ON cppt_t.pegawai_verbal_id = pegawai_verif2.pegawai_id
     LEFT JOIN pegawai_m dokteradmisi ON pasienadmisi_t.pegawai_id = dokteradmisi.pegawai_id;
");
      $this->execute('ALTER TABLE public.cppt_v
                    OWNER TO postgres;');

// create ulang view pasienrsambulan_v
      $this->execute("
CREATE OR REPLACE VIEW public.pasienrsambulan_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    jk.lookup_name AS jenis_kelamin,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN r_pendaftaran.ruangan_id
            ELSE r_admisi.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN r_pendaftaran.ruangan_nama
            ELSE r_admisi.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN i_pendaftaran.instalasi_id
            ELSE i_admisi.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN i_pendaftaran.instalasi_nama
            ELSE i_admisi.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text
            WHEN pendaftaran_t.instalasi_id = 2 THEN cppt_t.a_diag_utama ->> 'text'::text
            ELSE
            CASE COALESCE(pasienadmisi_t.pasienpulang_id, 0)
                WHEN 0 THEN cppt_t.a_diag_utama ->> 'text'::text
                ELSE resumemedisri_t.diag_utama ->> 'text'::text
            END
        END AS diagnosa,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelaspelayanan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN cb_pendaftaran.carabayar_id
            ELSE cb_admisi.carabayar_id
        END AS carabayar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN cb_pendaftaran.carabayar_nama
            ELSE cb_admisi.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pj_pendaftaran.penjamin_nama
            ELSE pj_admisi.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kls_pendaftaran.kelaspelayanan_nama
            ELSE kls_pendaftaran.kelaspelayanan_nama
        END AS kelaspelayanan_nama
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     LEFT JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN instalasi_m i_pendaftaran ON r_pendaftaran.instalasi_id = i_pendaftaran.instalasi_id
     LEFT JOIN instalasi_m i_admisi ON r_admisi.instalasi_id = i_admisi.instalasi_id
     LEFT JOIN pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false AND pasienmorbiditas_t.kelompokdiagnosa_id = 2
     LEFT JOIN cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
     LEFT JOIN resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pasien_id,
            max(pendaftaran_t_1.tgl_pendaftaran) AS tgl_pendaftaran
           FROM pendaftaran_t pendaftaran_t_1
          GROUP BY pendaftaran_t_1.pasien_id) pendaftaran_t1 ON pasien_m.pasien_id = pendaftaran_t1.pasien_id
     LEFT JOIN carabayar_m cb_pendaftaran ON pendaftaran_t.carabayar_id = cb_pendaftaran.carabayar_id
     LEFT JOIN carabayar_m cb_admisi ON pasienadmisi_t.carabayar_id = cb_admisi.carabayar_id
     LEFT JOIN penjamin_m pj_pendaftaran ON pendaftaran_t.penjamin_id = pj_pendaftaran.penjamin_id
     LEFT JOIN penjamin_m pj_admisi ON pasienadmisi_t.penjamin_id = pj_admisi.penjamin_id
     LEFT JOIN kelaspelayanan_m kls_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kls_pendaftaran.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_admisi ON pasienadmisi_t.kelaspelayanan_id = kls_admisi.kelaspelayanan_id;
");
      $this->execute('ALTER TABLE public.pasienrsambulan_v
                        OWNER TO postgres;');

// create ulang view infopemesanandarahhd_v
      $this->execute("
CREATE OR REPLACE VIEW public.infopemesanandarahhd_v AS 
 SELECT pesandarah_t.pesandarah_id,
    pesandarah_t.no_pesandarah,
    pesandarah_t.tgl_pesandarah,
    pesandarah_t.ruanganpemesan_id,
    ruangan_m.ruangan_nama,
    pesandarah_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin::integer AS jenis_kelaminid,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.golongandarah AS golongandarah_id,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah_nama,
    COALESCE(hasillab.kadar_hb, '-'::text) AS kadar_hb,
        CASE
            WHEN pendaftaran_t.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien
            WHEN pendaftaran_t.instalasi_id = 2 THEN cppt_t.a_diag_utama
            ELSE resumemedisri_t.diag_utama
        END AS diagnosa,
    pesandarah_t.pasienadmisi_id,
    pesandarahdetail_t.pesandarahdetail_id,
    pesandarahdetail_t.jenisdarah_id,
    jenisdarah_m.jenisdarah_nama,
    jenisdarah_m.lama_penyimpanan,
    jenisdarah_m.suhu_penyimpanan,
    jenisdarah_m.harga,
    pesandarahdetail_t.tgl_mintakirim,
    pesandarahdetail_t.wkt_mintakirim,
    pesandarahdetail_t.jumlah,
    pesandarahdetail_t.harga_satuan,
    pesandarah_t.indikasi_transfusi,
    pesandarah_t.metode_pengambilan,
    fgetnamalookup(pesandarah_t.metode_pengambilan) AS metode_pengambilan_nama,
    COALESCE(pesandarah_t.total_harga, 0::double precision) AS total_harga,
    pesandarah_t.riwayat_transfusi,
    pesandarah_t.riwayat_kehamilan,
    pesandarah_t.keterangan,
    pesandarahdetail_t.additional_data,
    pesandarahdetail_t.is_deleted,
    pesandarahdetail_t.is_active,
    pesandarahdetail_t.status_pesan AS status_pesan_id,
    fgetnamalookup(pesandarahdetail_t.status_pesan::integer) AS status_pesan
   FROM pesandarah_t
     JOIN pesandarahdetail_t ON pesandarah_t.pesandarah_id = pesandarahdetail_t.pesandarah_id
     JOIN jenisdarah_m ON pesandarahdetail_t.jenisdarah_id = jenisdarah_m.jenisdarah_id
     JOIN ruangan_m ON pesandarah_t.ruanganpemesan_id = ruangan_m.ruangan_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.nama_pasien,
            pasien_m_1.tempat_lahir,
            pasien_m_1.tanggal_lahir,
            pasien_m_1.jeniskelamin,
            pasien_m_1.golongandarah
           FROM pasien_m pasien_m_1) pasien_m ON pesandarah_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.umur,
            pendaftaran_t_1.instalasi_id
           FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON pesandarah_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pasienmorbiditas_t_1.pendaftaran_id,
            pasienmorbiditas_t_1.diagnosa_pasien
           FROM pasienmorbiditas_t pasienmorbiditas_t_1
          WHERE pasienmorbiditas_t_1.is_deleted IS FALSE AND pasienmorbiditas_t_1.kelompokdiagnosa_id = 2) pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id
     LEFT JOIN ( SELECT gantidokterpj_t_1.pendaftaran_id,
            gantidokterpj_t_1.dokterbaru_id
           FROM gantidokterpj_t gantidokterpj_t_1
          WHERE gantidokterpj_t_1.jenis_dokter = 485) gantidokterpj_t ON pendaftaran_t.pendaftaran_id = gantidokterpj_t.pendaftaran_id
     LEFT JOIN ( SELECT cppt_t_1.pendaftaran_id,
            cppt_t_1.pegawai_id,
            cppt_t_1.a_diag_utama
           FROM cppt_t cppt_t_1) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND gantidokterpj_t.dokterbaru_id = cppt_t.pegawai_id
     LEFT JOIN ( SELECT resumemedisri_t_1.pendaftaran_id,
            resumemedisri_t_1.pasienadmisi_id,
            resumemedisri_t_1.diag_utama
           FROM resumemedisri_t resumemedisri_t_1) resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pesandarah_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
     LEFT JOIN ( SELECT DISTINCT tindakanpelayanan_t.pendaftaran_id,
            concat(hasilpemeriksaanlabdetail_t.hasil, ' ', hasilpemeriksaanlabdetail_t.satuan_hasil) AS kadar_hb
           FROM hasilpemeriksaanlabdetail_t
             JOIN tindakanpelayanan_t ON hasilpemeriksaanlabdetail_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
             JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
                    max(hasilpemeriksaanlabdetail_t_1.created_date) AS created_date
                   FROM hasilpemeriksaanlabdetail_t hasilpemeriksaanlabdetail_t_1
                     JOIN tindakanpelayanan_t tindakanpelayanan_t_1 ON hasilpemeriksaanlabdetail_t_1.tindakanpelayanan_id = tindakanpelayanan_t_1.tindakanpelayanan_id
                  GROUP BY tindakanpelayanan_t_1.pendaftaran_id) hasilpemeriksaanlabdetail_max ON hasilpemeriksaanlabdetail_t.created_date = hasilpemeriksaanlabdetail_max.created_date AND tindakanpelayanan_t.pendaftaran_id = hasilpemeriksaanlabdetail_max.pendaftaran_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN pemeriksaanlab_m ON hasilpemeriksaanlabdetail_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
             JOIN nilairujukan_m ON hasilpemeriksaanlabdetail_t.nilairujukan_id = nilairujukan_m.nilairujukan_id) hasillab ON pendaftaran_t.pendaftaran_id = hasillab.pendaftaran_id
  WHERE pesandarahdetail_t.is_deleted IS FALSE;
");
      $this->execute('ALTER TABLE public.infopemesanandarahhd_v
                    OWNER TO postgres;');

// create ulang view infopemesanandarah_v
      $this->execute("
CREATE OR REPLACE VIEW public.infopemesanandarah_v AS 
 SELECT pesandarah_t.pesandarah_id,
    pesandarah_t.pendaftaran_id,
    pesandarah_t.no_pesandarah,
    pesandarah_t.tgl_pesandarah,
    pesandarah_t.ruanganpemesan_id,
    ruangan_m.ruangan_nama,
    pesandarah_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin::integer AS jenis_kelaminid,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pesandarah_t.golongandarah_id,
    fgetnamalookup(pesandarah_t.golongandarah_id) AS golongandarah_nama,
    COALESCE(hasillab.kadar_hb, '-'::text) AS kadar_hb,
        CASE
            WHEN pendaftaran_t.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien
            WHEN pendaftaran_t.instalasi_id = 2 THEN cppt_t.a_diag_utama
            ELSE resumemedisri_t.diag_utama
        END AS diagnosa,
    pesandarah_t.pasienadmisi_id,
    pesandarah_t.indikasi_transfusi,
    pesandarah_t.metode_pengambilan,
    fgetnamalookup(pesandarah_t.metode_pengambilan) AS metode_pengambilan_nama,
    COALESCE(pesandarah_t.total_harga, 0::double precision) AS total_harga,
    pesandarah_t.riwayat_transfusi,
    pesandarah_t.riwayat_kehamilan,
    pesandarah_t.keterangan
   FROM pesandarah_t
     JOIN ruangan_m ON pesandarah_t.ruanganpemesan_id = ruangan_m.ruangan_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.nama_pasien,
            pasien_m_1.tempat_lahir,
            pasien_m_1.tanggal_lahir,
            pasien_m_1.jeniskelamin,
            pasien_m_1.golongandarah
           FROM pasien_m pasien_m_1) pasien_m ON pesandarah_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.umur,
            pendaftaran_t_1.instalasi_id
           FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON pesandarah_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pasienmorbiditas_t_1.pendaftaran_id,
            pasienmorbiditas_t_1.diagnosa_pasien
           FROM pasienmorbiditas_t pasienmorbiditas_t_1
          WHERE pasienmorbiditas_t_1.is_deleted IS FALSE AND pasienmorbiditas_t_1.kelompokdiagnosa_id = 2) pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id
     LEFT JOIN ( SELECT gantidokterpj_t_1.pendaftaran_id,
            gantidokterpj_t_1.dokterbaru_id
           FROM gantidokterpj_t gantidokterpj_t_1
          WHERE gantidokterpj_t_1.jenis_dokter = 485) gantidokterpj_t ON pendaftaran_t.pendaftaran_id = gantidokterpj_t.pendaftaran_id
     LEFT JOIN ( SELECT cppt_t_1.pendaftaran_id,
            cppt_t_1.pegawai_id,
            cppt_t_1.a_diag_utama
           FROM cppt_t cppt_t_1) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND gantidokterpj_t.dokterbaru_id = cppt_t.pegawai_id
     LEFT JOIN ( SELECT resumemedisri_t_1.pendaftaran_id,
            resumemedisri_t_1.pasienadmisi_id,
            resumemedisri_t_1.diag_utama
           FROM resumemedisri_t resumemedisri_t_1) resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pesandarah_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
     LEFT JOIN ( SELECT DISTINCT tindakanpelayanan_t.pendaftaran_id,
            concat(hasilpemeriksaanlabdetail_t.hasil, ' ', hasilpemeriksaanlabdetail_t.satuan_hasil) AS kadar_hb
           FROM hasilpemeriksaanlabdetail_t
             JOIN tindakanpelayanan_t ON hasilpemeriksaanlabdetail_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
             JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
                    max(hasilpemeriksaanlabdetail_t_1.created_date) AS created_date
                   FROM hasilpemeriksaanlabdetail_t hasilpemeriksaanlabdetail_t_1
                     JOIN tindakanpelayanan_t tindakanpelayanan_t_1 ON hasilpemeriksaanlabdetail_t_1.tindakanpelayanan_id = tindakanpelayanan_t_1.tindakanpelayanan_id
                  GROUP BY tindakanpelayanan_t_1.pendaftaran_id) hasilpemeriksaanlabdetail_max ON hasilpemeriksaanlabdetail_t.created_date = hasilpemeriksaanlabdetail_max.created_date AND tindakanpelayanan_t.pendaftaran_id = hasilpemeriksaanlabdetail_max.pendaftaran_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN pemeriksaanlab_m ON hasilpemeriksaanlabdetail_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
             JOIN nilairujukan_m ON hasilpemeriksaanlabdetail_t.nilairujukan_id = nilairujukan_m.nilairujukan_id) hasillab ON pendaftaran_t.pendaftaran_id = hasillab.pendaftaran_id;
");
      $this->execute('ALTER TABLE public.infopemesanandarah_v
                        OWNER TO postgres;');

// create ulang view infopasienoperasi_v
      $this->execute("
CREATE OR REPLACE VIEW public.infopasienoperasi_v AS 
 SELECT pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    status.lookup_name AS status,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    jk.lookup_name AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    cppt_t.a_diag_utama ->> 'text'::text AS a_diag_utama
   FROM pasienmasukpenunjang_t
     JOIN rencanaoperasi_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     JOIN lookup_m status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
     LEFT JOIN cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND pasienadmisi_t.pegawai_id = cppt_t.pegawai_id AND cppt_t.is_deleted = false AND cppt_t.is_active = true AND cppt_t.is_instruksi_pulang = false
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL;
");
      $this->execute('ALTER TABLE public.infopasienoperasi_v
                    OWNER TO postgres;');

// create ulang view infopasienmasihdirawat_v
      $this->execute("
CREATE OR REPLACE VIEW public.infopasienmasihdirawat_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.nama_bin AS nama_panggilan,
    pasien_m.nama_ibu,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin::integer AS jenis_kelaminid,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.golongandarah AS golongandarah_id,
    fgetnamalookup(pasien_m.golongandarah) AS golongandarah_nama,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS pegawai_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN r_pendaftaran.ruangan_id
            ELSE r_admisi.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN r_pendaftaran.ruangan_nama
            ELSE r_admisi.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN i_pendaftaran.instalasi_nama
            ELSE i_admisi.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien
            WHEN pendaftaran_t.instalasi_id = 2 THEN cppt_t.a_diag_utama
            ELSE resumemedisri_t.diag_utama
        END AS diagnosa,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelaspelayanan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
    COALESCE(hasillab.kadar_hb, '-'::text) AS kadar_hb,
    pendaftaran_terakhir.tgl_pendaftaran,
    pesandarah.riwayat_transfusi
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.nama_pasien,
            pasien_m_1.nama_bin,
            pasien_m_1.nama_ibu,
            pasien_m_1.tempat_lahir,
            pasien_m_1.tanggal_lahir,
            pasien_m_1.jeniskelamin,
            pasien_m_1.golongandarah::integer AS golongandarah,
            pasien_m_1.alamat_pasien,
            pasien_m_1.rt,
            pasien_m_1.rw
           FROM pasien_m pasien_m_1) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN instalasi_m i_pendaftaran ON r_pendaftaran.instalasi_id = i_pendaftaran.instalasi_id
     LEFT JOIN instalasi_m i_admisi ON r_admisi.instalasi_id = i_admisi.instalasi_id
     LEFT JOIN pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false AND pasienmorbiditas_t.kelompokdiagnosa_id = 2
     LEFT JOIN gantidokterpj_t ON pendaftaran_t.pendaftaran_id = gantidokterpj_t.pendaftaran_id AND gantidokterpj_t.jenis_dokter = 485
     LEFT JOIN cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND gantidokterpj_t.dokterbaru_id = cppt_t.pegawai_id
     LEFT JOIN resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
     LEFT JOIN ( SELECT DISTINCT tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.pasien_id,
            concat(hasilpemeriksaanlabdetail_t.hasil, ' ', hasilpemeriksaanlabdetail_t.satuan_hasil) AS kadar_hb
           FROM tindakanpelayanan_t
             JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
             JOIN hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id
             JOIN hasilpemeriksaanlabdetail_t ON hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id
             JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
                    max(hasilpemeriksaanlabdetail_t_1.created_date) AS created_date
                   FROM tindakanpelayanan_t tindakanpelayanan_t_1
                     JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON tindakanpelayanan_t_1.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                     JOIN hasilpemeriksaanlab_t hasilpemeriksaanlab_t_1 ON pasienmasukpenunjang_t_1.pasienmasukpenunjang_id = hasilpemeriksaanlab_t_1.pasienmasukpenunjang_id
                     JOIN hasilpemeriksaanlabdetail_t hasilpemeriksaanlabdetail_t_1 ON hasilpemeriksaanlab_t_1.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t_1.hasilpemeriksaanlab_id
                  GROUP BY tindakanpelayanan_t_1.pendaftaran_id) hasilpemeriksaanlabdetail_max ON hasilpemeriksaanlabdetail_t.created_date = hasilpemeriksaanlabdetail_max.created_date AND tindakanpelayanan_t.pendaftaran_id = hasilpemeriksaanlabdetail_max.pendaftaran_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN pemeriksaanlab_m ON hasilpemeriksaanlabdetail_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id AND pemeriksaanlab_m.jenispemeriksaanlab_id = 3
             JOIN nilairujukan_m ON hasilpemeriksaanlabdetail_t.nilairujukan_id = nilairujukan_m.nilairujukan_id) hasillab ON pendaftaran_t.pendaftaran_id = hasillab.pendaftaran_id
     JOIN ( SELECT pendaftaran_t_1.pasien_id,
            max(pendaftaran_t_1.tgl_pendaftaran) AS tgl_pendaftaran
           FROM pendaftaran_t pendaftaran_t_1
          GROUP BY pendaftaran_t_1.pasien_id) pendaftaran_terakhir ON pendaftaran_t.tgl_pendaftaran = pendaftaran_terakhir.tgl_pendaftaran AND pendaftaran_t.pasien_id = pendaftaran_terakhir.pasien_id
     LEFT JOIN ( SELECT pesandarah_t.pasien_id,
            string_agg(pesandarah_t.riwayat_transfusi, ' ,'::text) AS riwayat_transfusi
           FROM pesandarah_t
          GROUP BY pesandarah_t.pasien_id) pesandarah ON pendaftaran_t.pasien_id = pesandarah.pasien_id
  WHERE pendaftaran_t.pasienpulang_id IS NULL AND pasienadmisi_t.pasienpulang_id IS NULL;
");
      $this->execute('ALTER TABLE public.infopasienmasihdirawat_v
                     OWNER TO postgres;');

// create ulang view infomonitoringbpjs_v
      $this->execute("
CREATE OR REPLACE VIEW public.infomonitoringbpjs_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.tgl_pendaftaran,
    pasienpulang_t.tglpasienpulang,
    pasienadmisi_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kelaspelayanan_m.urutankelas,
    pasien_m.no_rekam_medik,
    bpjs_t.nosep,
    pasien_m.nama_pasien,
    pasien_m.jeniskelamin,
    pasien_m.tanggal_lahir,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    bpjs_t.klsrawat AS hak_kelas,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    NULL::text AS default_diagnosa,
    diag_ri.diagnosa_utama,
    (monitorsetdiagnosa.diagnosa_kode::text || ' - '::text) || monitorsetdiagnosa.diagnosa_nama::text AS set_diagnosautama,
    diag_ri.diagnosa_penyerta,
    monitorsetdiagnosa.diag_penyerta AS set_diagnosapenyerta,
    resumemedisri_t.prosedur_diag AS diagnosa_tindakan,
    monitorsetdiagnosa.diag_tindakan AS set_diagnosatindakan,
        CASE
            WHEN monitorsetdiagnosa.diag_utama_id IS NULL THEN 0
            ELSE 1
        END AS status_monitor_id,
        CASE
            WHEN monitorsetdiagnosa.diag_utama_id IS NULL THEN 'BELUM DIMONITOR'::text
            ELSE 'SUDAH DIMONITOR'::text
        END AS status_monitor,
        CASE
            WHEN bpjs_t.klsrawat > kelaspelayanan_m.urutankelas THEN 'NAIK KELAS'::text
            WHEN bpjs_t.klsrawat = kelaspelayanan_m.urutankelas THEN 'KELAS SAMA'::text
            ELSE 'TURUN KELAS'::text
        END AS keterangan_kelas,
    COALESCE(tagihan.sub_total, 0::double precision) AS tagihan_rs,
    COALESCE(monitorsetdiagnosa.total, 0::double precision) AS tarif_inacbg,
        CASE
            WHEN ((diag_ri.diagnosa_utama ->> 'id'::text)::integer) <> monitorsetdiagnosa.diag_utama_id THEN 'BEDA'::text
            ELSE 'SAMA'::text
        END AS cek_diagnosa,
    monitorsetdiagnosa.is_dokter
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id AND carabayar_m.carabayar_id = 6
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT diagnosa.cppt_id,
            diagnosa.pasienadmisi_id,
            diagnosa.a_diag_utama AS diagnosa_utama,
            diagnosa.a_diag_penyerta AS diagnosa_penyerta
           FROM cppt_t diagnosa
             JOIN ( SELECT max(diagnosa_max.cppt_id) AS cppt_id,
                    diagnosa_max.pasienadmisi_id
                   FROM cppt_t diagnosa_max
                  WHERE diagnosa_max.is_deleted = false
                  GROUP BY diagnosa_max.pasienadmisi_id) cppt_max ON diagnosa.pasienadmisi_id = cppt_max.pasienadmisi_id AND diagnosa.cppt_id = cppt_max.cppt_id) diag_ri ON pendaftaran_t.pasienadmisi_id = diag_ri.pasienadmisi_id
     LEFT JOIN ( SELECT monitorsetdiagnosa_t.monitorsetdiagnosa_id,
            monitorsetdiagnosa_t.pendaftaran_id,
            monitorsetdiagnosa_t.pasienadmisi_id,
            monitorsetdiagnosa_t.diag_utama_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            monitorsetdiagnosa_t.diag_penyerta,
            monitorsetdiagnosa_t.diag_tindakan,
            monitorsetdiagnosa_t.total,
            monitorsetdiagnosa_t.is_dokter
           FROM monitorsetdiagnosa_t
             JOIN diagnosa_m ON monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id
          WHERE monitorsetdiagnosa_t.is_deleted = false) monitorsetdiagnosa ON pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN resumemedisri_t ON pendaftaran_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id AND resumemedisri_t.is_deleted = false
     LEFT JOIN ( SELECT x.pendaftaran_id,
            x.pasienadmisi_id,
            sum(x.sub_total) AS sub_total
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
          GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id;
");
     $this->execute('ALTER TABLE public.infomonitoringbpjs_v
                    OWNER TO postgres;');


// create ulang view infopasienbpjsdiagnosa_v
      $this->execute("
CREATE OR REPLACE VIEW public.infopasienbpjsdiagnosa_v AS 
 SELECT diagnosa.jenis,
    diagnosa.pendaftaran_id,
    diagnosa.pasienadmisi_id,
    diagnosa.koreksidiagnosa_id,
    diagnosa.kelompokdiagnosa_id,
    diagnosa.kelompokdiagnosa_nama,
    diagnosa.diagnosa_id,
    diagnosa.diagnosa_nama,
    diagnosa.diagnosa_kode,
    diagnosa.diagnosaasal_id,
    diagnosa.diagnosa_masuk,
    diagnosa.diagnosa_utama,
    diagnosa.diagnosa_penyerta,
    diagnosa.diagnosa_terapi,
    diagnosa.is_inacbg,
    diagnosa.is_icdprimer,
    diagnosa.is_deleted
   FROM ( SELECT 'RJ-RD'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            koreksidiagnosa_t.koreksidiagnosa_id,
            koreksidiagnosa_t.kelompokdiagnosa_id,
            kelompokdiagnosa_m.kelompokdiagnosa_nama,
            koreksidiagnosa_t.diagnosa_id,
            diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
            diagnosa_m.diagnosa_kode,
            koreksidiagnosa_t.diagnosaasal_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_masuk,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 3 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_penyerta,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 6 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_terapi,
            koreksidiagnosa_t.is_inacbg,
            koreksidiagnosa_t.is_icdprimer,
            koreksidiagnosa_t.is_deleted
           FROM pendaftaran_t
             JOIN koreksidiagnosa_t ON pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id
             JOIN kelompokdiagnosa_m ON koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
             JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
             LEFT JOIN pasienmorbiditas_t ON koreksidiagnosa_t.diagnosaasal_id = pasienmorbiditas_t.pasienmorbiditas_id
          WHERE pendaftaran_t.carabayar_id = 6 AND koreksidiagnosa_t.is_inacbg = true AND pendaftaran_t.instalasi_id = 1 AND koreksidiagnosa_t.is_deleted = false
        UNION ALL
         SELECT 'RJ-RD'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            koreksidiagnosa_t.koreksidiagnosa_id,
            koreksidiagnosa_t.kelompokdiagnosa_id,
            kelompokdiagnosa_m.kelompokdiagnosa_nama,
            koreksidiagnosa_t.diagnosa_id,
            diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
            diagnosa_m.diagnosa_kode,
            koreksidiagnosa_t.diagnosaasal_id,
            NULL::json AS diagnosa_masuk,
            cppt_t.a_diag_utama AS diagnosa_utama,
            cppt_t.a_diag_penyerta AS diagnosa_penyerta,
            NULL::json AS diagnosa_terapi,
            koreksidiagnosa_t.is_inacbg,
            koreksidiagnosa_t.is_icdprimer,
            koreksidiagnosa_t.is_deleted
           FROM pendaftaran_t
             JOIN koreksidiagnosa_t ON pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id AND koreksidiagnosa_t.pasienadmisi_id IS NULL
             JOIN kelompokdiagnosa_m ON koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
             JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
             JOIN cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND cppt_t.is_deleted = false AND cppt_t.pasienadmisi_id IS NULL
          WHERE pendaftaran_t.carabayar_id = 6 AND koreksidiagnosa_t.is_inacbg = true AND pendaftaran_t.instalasi_id = 2 AND koreksidiagnosa_t.is_deleted = false
        UNION ALL
         SELECT 'RI'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            koreksidiagnosa_t.koreksidiagnosa_id,
            koreksidiagnosa_t.kelompokdiagnosa_id,
            kelompokdiagnosa_m.kelompokdiagnosa_nama,
            koreksidiagnosa_t.diagnosa_id,
            diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
            diagnosa_m.diagnosa_kode,
            koreksidiagnosa_t.diagnosaasal_id,
            resumemedisri_t.diag_masuk AS diagnosa_masuk,
            resumemedisri_t.diag_utama AS diagnosa_utama,
            resumemedisri_t.diag_penyerta AS diagnosa_penyerta,
            resumemedisri_t.prosedur_diag AS diagnosa_terapi,
            koreksidiagnosa_t.is_inacbg,
            koreksidiagnosa_t.is_icdprimer,
            koreksidiagnosa_t.is_deleted
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN koreksidiagnosa_t ON pendaftaran_t.pasienadmisi_id = koreksidiagnosa_t.pasienadmisi_id
             JOIN kelompokdiagnosa_m ON koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
             JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
             LEFT JOIN resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE pasienadmisi_t.carabayar_id = 6 AND koreksidiagnosa_t.is_inacbg = true AND koreksidiagnosa_t.is_deleted = false) diagnosa;
");
     $this->execute('ALTER TABLE public.infopasienbpjsdiagnosa_v
                    OWNER TO postgres;');

// create ulang view resumemedisri_v
      $this->execute("
CREATE OR REPLACE VIEW public.resumemedisri_v AS 
 SELECT resumemedisri_t.resumemedisri_id,
    resumemedisri_t.pendaftaran_id,
    resumemedisri_t.pasienadmisi_id,
    resumemedisri_t.tgl_masuk,
    resumemedisri_t.tgl_keluar,
    resumemedisri_t.diag_masuk,
    resumemedisri_t.diag_utama,
    resumemedisri_t.diag_penyerta,
    resumemedisri_t.a_f_bermakna,
    resumemedisri_t.prosedur_diag,
    resumemedisri_t.tatalaksana_obat,
    resumemedisri_t.is_rotd,
    resumemedisri_t.obat_rotd,
    resumemedisri_t.kondisipulang_id,
    kondisikeluar_m.kondisikeluar_nama,
    resumemedisri_t.kondisi_lain,
    resumemedisri_t.obat_pulang,
    resumemedisri_t.kontrol_ke,
    resumemedisri_t.tgl_kontrol,
    resumemedisri_t.rencana_tindaklanjut,
    resumemedisri_t.is_print
   FROM resumemedisri_t
     JOIN kondisikeluar_m ON resumemedisri_t.kondisipulang_id = kondisikeluar_m.kondisikeluar_id;
");
    $this->execute('ALTER TABLE public.resumemedisri_v
                    OWNER TO postgres;');


// create ulang view infomorbiditas_v
      $this->execute("
CREATE OR REPLACE VIEW public.infomorbiditas_v AS 
 SELECT pasienmorbiditas_t.pasienmorbiditas_id,
    pasienmorbiditas_t.pendaftaran_id,
    pasienmorbiditas_t.kelompokdiagnosa_id,
    kelompokdiagnosa_m.kelompokdiagnosa_nama,
    pasienmorbiditas_t.diagnosa_pasien
   FROM pasienmorbiditas_t
     JOIN kelompokdiagnosa_m ON pasienmorbiditas_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
  WHERE pasienmorbiditas_t.is_deleted = false;
");
    $this->execute('ALTER TABLE public.infomorbiditas_v
                 OWNER TO postgres;');

// create ulang view laporandiagnosapasien_v
      $this->execute("
CREATE OR REPLACE VIEW public.laporandiagnosapasien_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.jeniskelamin,
    jk.lookup_name AS jenis_kelamin,
    pasienmorbiditas_t.tglmorbiditas AS tgl_diagnosa,
    pasienmorbiditas_t.pasienmorbiditas_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienmorbiditas_t.is_deleted,
    pasienmorbiditas_t.kelompokdiagnosa_id,
    kelompokdiagnosa_m.kelompokdiagnosa_nama,
    pasienmorbiditas_t.diagnosa_pasien,
    pasienmorbiditas_t.diagnosa_pasien ->> 'id'::text AS diagnosa_id,
    pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text AS diagnosa_nama,
    pasienmorbiditas_t.diagnosa_pasien ->> 'kode'::text AS diagnosa_kode,
    klasifikasidiagnosa_m.klasifikasidiagnosa_nama
   FROM pasienmorbiditas_t
     JOIN pendaftaran_t ON pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelompokdiagnosa_m ON pasienmorbiditas_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
     JOIN ruangan_m ON pasienmorbiditas_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     LEFT JOIN diagnosa_m ON ((pasienmorbiditas_t.diagnosa_pasien ->> 'id'::text)::integer) = diagnosa_m.diagnosa_id
     LEFT JOIN klasifikasidiagnosa_m ON diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id
  WHERE pasienmorbiditas_t.is_active = true AND pasienmorbiditas_t.is_deleted = false;
");
    $this->execute('ALTER TABLE public.laporandiagnosapasien_v
                    OWNER TO postgres;');

// create ulang view riwayatasesmenmedis_v
      $this->execute("
CREATE OR REPLACE VIEW public.riwayatasesmenmedis_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.pasien_id,
    asesmenmedis_t.asesmenmedis_id,
    asesmenmedis_t.keluhan_tambahan,
    asesmenmedis_t.r_penyakitsekarang,
    asesmenmedis_t.lama_sakit,
    asesmenmedis_t.r_penyakitdahulu,
    asesmenmedis_t.r_penyakitkeluarga,
    asesmenmedis_t.r_imunisasi,
    asesmenmedis_t.r_peskk,
    asesmenmedis_t.tgl_asesmenmedis,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    asesmenmedis_t.obat_diberikan,
    asesmenmedis_t.r_makanan,
    asesmenmedis_t.r_kelahiran,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.keterangan,
    asesmenmedis_t.td_systolic,
    asesmenmedis_t.td_diastolic,
    asesmenmedis_t.tekanan_darah,
    asesmenmedis_t.hasil_td,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    asesmenmedis_t.bb_ideal,
    asesmenmedis_t.imt,
    asesmenmedis_t.ket_imt,
    asesmenmedis_t.detak_nadi,
    asesmenmedis_t.pernapasan,
    asesmenmedis_t.denyut_jantung,
    asesmenmedis_t.suhu_tubuh,
    asesmenmedis_t.gcs_eye,
    asesmenmedis_t.gcs_verbal,
    asesmenmedis_t.gcs_motorik,
    asesmenmedis_t.hasil_gcs,
    asesmenmedis_t.kontak,
    asesmenmedis_t.metod_asmennyeri
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id
  WHERE asesmenmedis_t.is_deleted = false;
");
    $this->execute('ALTER TABLE public.riwayatasesmenmedis_v
                 OWNER TO postgres;');

// create ulang view infopasiengizi_v
      $this->execute("
CREATE OR REPLACE VIEW public.infopasiengizi_v AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    jenis_kelamin.lookup_name AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kls_bpjs.kelaspelayanan_nama AS hak_kelas_nama,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.tgl_pulang,
    rencanapulang_t.rencana_pulang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap,
    status_ranap.lookup_name AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    pasienadmisi_t.tgl_pindahkamar,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.is_hamil,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.luas_permukaantubuh,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    asesmenmedis_t.r_penyakitkeluarga,
    asesmenmedis_t.r_imunisasi,
    asesmenmedis_t.diagnosa_id,
    asesmenmedis_t.diagnosa_id AS diagnosa_nama,
    pasienadmisi_t.kamarruangan_id,
    pasienadmisi_t.kamartempattidur_id,
    pasien_m.photopasien,
    pendaftaran_t.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    asesmenmedis_t.discharge_plan,
    asesmenawal_t.obatan_rumah,
    asesmenawal_t.obat_darirumah,
        CASE
            WHEN (( SELECT count(*) AS count
               FROM cppt_t x
              WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    skrininggizi_t.skrininggizi_id,
    skrininggizi_t.skor,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 80
            ELSE skrininggizi_t.status_asesmen
        END AS status_asesmen,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 'Tidak Asesmen'::character varying
            ELSE asmen_gizi.lookup_name
        END AS stat_asesmen_gizi,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    pekerjaan_m.pekerjaan_nama,
    pendidikan_m.pendidikan_nama,
    asesmenawal_t.asmen_riwayat,
    asesmenmedis_t.r_peskk
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
     JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_bpjs ON bpjs_t.klsrawat = kls_bpjs.bpjs_kelas
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN lookup_m status_ranap ON pasienadmisi_t.status_ranap = status_ranap.lookup_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN skrininggizi_t ON pendaftaran_t.pendaftaran_id = skrininggizi_t.pendaftaran_id AND skrininggizi_t.is_active = true
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN lookupkeperawatan_m asmen_gizi ON skrininggizi_t.status_asesmen = asmen_gizi.lookupkeperawatan_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453;
");
     $this->execute('ALTER TABLE public.infopasiengizi_v
  OWNER TO postgres;');

// create ulang view infopasiengizi_v_old
      $this->execute("
CREATE OR REPLACE VIEW public.infopasiengizi_v_old AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    jenis_kelamin.lookup_name AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kls_bpjs.kelaspelayanan_nama AS hak_kelas_nama,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.tgl_pulang,
    rencanapulang_t.rencana_pulang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap,
    status_ranap.lookup_name AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    pasienadmisi_t.tgl_pindahkamar,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.is_hamil,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.luas_permukaantubuh,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    asesmenmedis_t.r_penyakitkeluarga,
    asesmenmedis_t.r_imunisasi,
    asesmenmedis_t.diagnosa_id,
    pasienadmisi_t.kamarruangan_id,
    pasienadmisi_t.kamartempattidur_id,
    pasien_m.photopasien,
    pendaftaran_t.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    asesmenmedis_t.discharge_plan,
    asesmenawal_t.obatan_rumah,
    asesmenawal_t.obat_darirumah,
        CASE
            WHEN (( SELECT count(*) AS count
               FROM cppt_t x
              WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    skrininggizi_t.skrininggizi_id,
    skrininggizi_t.skor,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 80
            ELSE skrininggizi_t.status_asesmen
        END AS status_asesmen,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 'Tidak Asesmen'::character varying
            ELSE asmen_gizi.lookup_name
        END AS stat_asesmen_gizi,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    ( SELECT count(*) AS count
           FROM asuhangizi_t
          WHERE asuhangizi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AS jml_data_asuhan,
    asesmenawalgizi_t.asesmenawalgizi_id
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
     JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_bpjs ON bpjs_t.klsrawat = kls_bpjs.bpjs_kelas
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN lookup_m status_ranap ON pasienadmisi_t.status_ranap = status_ranap.lookup_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN skrininggizi_t ON pendaftaran_t.pendaftaran_id = skrininggizi_t.pendaftaran_id AND skrininggizi_t.is_active = true
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN lookupkeperawatan_m asmen_gizi ON skrininggizi_t.status_asesmen = asmen_gizi.lookupkeperawatan_id
     LEFT JOIN asesmenawalgizi_t ON asesmenawalgizi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453;
");
     $this->execute('ALTER TABLE public.infopasiengizi_v_old
                OWNER TO postgres;');

// create ulang view asuhangizi_v
      $this->execute("
CREATE OR REPLACE VIEW public.asuhangizi_v AS 
 SELECT pasien.no_rekam_medik,
    pasien.pendaftaran_id,
    pasien.tgl_pendaftaran,
    pasien.no_pendaftaran,
    pasien.nama_pasien,
    pasien.jenis_kelamin,
    pasien.jeniskasuspenyakit_nama,
    pasien.tanggal_lahir,
    pasien.umur,
    pasien.dokter_admisi,
    pasien.kelaspelayanan_nama,
    pasien.carabayar_nama,
    pasien.penjamin_nama,
    pasien.r_penyakitkeluarga,
    pasien.is_merokok,
    pasien.jml_rokok,
    t.gizi_makanan,
    t.antropometri,
    t.biokimia,
    t.fisikklinis_gizi,
    t.diagnosa_gizi,
    t.intervensi_gizi,
    t.rencana_gizi,
    t.asuhangizi_id,
    t.created_date,
    pasien.ruangan_nama,
    pasien.kamarruangan_nokamar,
    pasien.no_tempattidur,
    t.peg_gizi_id AS dietisen_id,
    dietisen.nama_pegawai AS dietisen_nama,
    asesmenmedis_t.r_peskk,
    asesmenmedis_t.diagnosa_id AS diagnosa_nama
   FROM asuhangizi_t t
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            dokter_admisi.nama_pegawai AS dokter_admisi,
            kelaspelayanan_m.kelaspelayanan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            asesmenmedis_t_1.r_penyakitkeluarga,
            asesmenmedis_t_1.is_merokok,
            asesmenmedis_t_1.jml_rokok,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN asesmenmedis_t asesmenmedis_t_1 ON pendaftaran_t.pendaftaran_id = asesmenmedis_t_1.pendaftaran_id AND asesmenmedis_t_1.is_deleted = false
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id) pasien ON t.pendaftaran_id = pasien.pendaftaran_id
     LEFT JOIN asesmenmedis_t ON t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     JOIN pegawai_m dietisen ON t.peg_gizi_id = dietisen.pegawai_id;
");
    $this->execute('ALTER TABLE public.asuhangizi_v
                    OWNER TO postgres;');

// create ulang view infopasienri_v
      $this->execute("
CREATE OR REPLACE VIEW public.infopasienri_v AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    jenis_kelamin.lookup_name AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.tgl_pulang,
    rencanapulang_t.rencana_pulang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap,
    status_ranap.lookup_name AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    pasienadmisi_t.tgl_pindahkamar,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.is_hamil,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.luas_permukaantubuh,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    asesmenmedis_t.r_penyakitkeluarga,
    asesmenmedis_t.r_imunisasi,
    asesmenmedis_t.diagnosa_id,
    asesmenmedis_t.diagnosa_id AS diagnosa_nama,
    pasienadmisi_t.kamarruangan_id,
    pasienadmisi_t.kamartempattidur_id,
    pasien_m.photopasien,
    pendaftaran_t.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    asesmenmedis_t.discharge_plan,
    asesmenawal_t.obatan_rumah,
    asesmenawal_t.obat_darirumah,
        CASE
            WHEN (( SELECT count(*) AS count
               FROM cppt_t x
              WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    pekerjaan_m.pekerjaan_nama,
    pendidikan_m.pendidikan_nama,
    asesmenmedis_t.r_peskk,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    COALESCE(tagihan.sub_total, 0::double precision) AS tagihan_rs,
    COALESCE(monitorsetdiagnosa.total, 0::double precision) AS tarif_inacbg,
    carabayar_m.groupcarabayar_id AS group_carabayar,
        CASE
            WHEN monitorsetdiagnosa.diag_utama_id IS NULL THEN 'BELUM DIMONITOR'::text
            ELSE 'SUDAH DIMONITOR'::text
        END AS status_monitor
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     JOIN lookup_m jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
     LEFT JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN lookup_m status_ranap ON pasienadmisi_t.status_ranap = status_ranap.lookup_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN ( SELECT x.pendaftaran_id,
            x.pasienadmisi_id,
            sum(x.sub_total) AS sub_total
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
          GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id
     LEFT JOIN ( SELECT monitorsetdiagnosa_t.monitorsetdiagnosa_id,
            monitorsetdiagnosa_t.pendaftaran_id,
            monitorsetdiagnosa_t.pasienadmisi_id,
            monitorsetdiagnosa_t.diag_utama_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            monitorsetdiagnosa_t.diag_penyerta,
            monitorsetdiagnosa_t.diag_tindakan,
            monitorsetdiagnosa_t.total,
            monitorsetdiagnosa_t.is_dokter
           FROM monitorsetdiagnosa_t
             JOIN diagnosa_m ON monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id
          WHERE monitorsetdiagnosa_t.is_deleted = false) monitorsetdiagnosa ON pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false;
");
    $this->execute('ALTER TABLE public.infopasienri_v
                    OWNER TO postgres;');

// create ulang view pemberianobat_v
      $this->execute("
CREATE OR REPLACE VIEW public.pemberianobat_v AS 
 SELECT pemberianobat_t.pemberianobat_id,
    pemberianobat_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    jk.lookup_name AS jenis_kelamin,
    pendaftaran_t.umur,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    kelaspelayanan_m.kelaspelayanan_nama,
    dok_dpjp.nama_pegawai AS dokter_dpjp,
    penjamin_m.penjamin_nama,
    pemberianobat_t.berat_badan,
    pemberianobat_t.tinggi_badan,
    pemberianobat_t.luas_tubuh,
    pemberianobat_t.is_hamil,
    pemberianobat_t.is_alergi,
    pemberianobat_t.diagnosa_id AS diagnosa_namalainnya,
    pemberianobat_t.dokterdpjp_id,
    pemberianobat_t.diagnosa_id,
    pemberianobat_t.diagnosa_id AS diagnosa_nama
   FROM pemberianobat_t
     JOIN pendaftaran_t ON pemberianobat_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN pegawai_m dok_dpjp ON pasienadmisi_t.pegawai_id = dok_dpjp.pegawai_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id;
");
    $this->execute('ALTER TABLE public.pemberianobat_v
                    OWNER TO postgres;');

     $this->execute('CREATE INDEX "pasienmorbiditas_kelompokdiagnosa_idx" ON "public"."pasienmorbiditas_t" USING btree (
  "kelompokdiagnosa_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

      $this->execute('CREATE INDEX "pasienmorbiditas_pendaftaran_id_idx" ON "public"."pasienmorbiditas_t" USING btree (
  "pendaftaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');
     

      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190823_040626_perubahan_typedata_json cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190823_040626_perubahan_typedata_json cannot be reverted.\n";

        return false;
    }
    */
}
