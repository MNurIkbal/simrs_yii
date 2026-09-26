<?php

use yii\db\Migration;

/**
 * Class m210928_124715_improvment_dokumen_pasien_US15044
 */
class m210928_124715_improvment_dokumen_pasien_US15044 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."dokumenupload_t" (
              "dokumenupload_id" serial8 NOT NULL PRIMARY KEY,
              pendaftaran_id int4,
              pasienadmisi_id int4,
              dokumen_id int4,
              "path" TEXT,
              "filename" TEXT,
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4
            );
        ');

        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."dokumen_m" (
              "dokumen_id" serial8 NOT NULL PRIMARY KEY,
              jenis_dokumen_id int4,
              nama_dokumen VARCHAR(200),
              nama_dokumen_lainnya VARCHAR(200),
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4
            );
        ');

        $this->execute('
            DROP VIEW IF EXISTS dokumen_v;
        ');

        $this->execute('
            CREATE VIEW dokumen_v AS 
            SELECT 
                dokumen_id, 
                jenis_dokumen_id , 
                lookup_m.lookup_name AS jenis_dokumen_nama, 
                nama_dokumen, 
                nama_dokumen_lainnya
            FROM dokumen_m
            JOIN lookup_m ON dokumen_m.jenis_dokumen_id = lookup_m.lookup_id
            ORDER BY dokumen_id ASC;
        ');

        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
                1076,
                1077,
                1078,
                1079
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m"("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1076, \'jenis_dokumen\', \'ADMINISTRASI\', \'ADMINISTRASI\', NULL, NULL, NULL, \'2021-09-28 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
             (1077, \'jenis_dokumen\', \'LEMBAR CATATAN DOKTER DAN KEPERAWATAN\', \'LEMBAR CATATAN DOKTER DAN KEPERAWATAN\', NULL, NULL, NULL, \'2021-09-28 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
             (1078, \'jenis_dokumen\', \'PENUNJANG MEDIS\', \'PENUNJANG MEDIS\', NULL, NULL, NULL, \'2021-09-28 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
             (1079, \'jenis_dokumen\', \'LAIN-LAIN\', \'LAIN-LAIN\', NULL, NULL, NULL, \'2021-09-28 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);

        ');

        $this->execute('
            TRUNCATE TABLE dokumen_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."dokumen_m"("dokumen_id", "jenis_dokumen_id", "nama_dokumen", "nama_dokumen_lainnya", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES
            (1, 1076, \'SURAT-SURAT PENGANTAR RAWAT INAP\', \'SURAT-SURAT PENGANTAR RAWAT INAP\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (2, 1076, \'PERSETUJUAN UMUM\', \'PERSETUJUAN UMUM\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (3, 1076, \'HAK & KEWAJIBAN PASIEN\', \'HAK & KEWAJIBAN PASIEN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (4, 1076, \'TATA TERTIB PASIEN RAWAT INAP\', \'TATA TERTIB PASIEN RAWAT INAP\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (5, 1076, \'SURAT PERSETUJUAN RAWAT INAP\', \'SURAT PERSETUJUAN RAWAT INAP\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (6, 1076, \'CHEKLIST PEMBERIAN INFORMASI PASIEN DAN KELUARGA\', \'CHEKLIST PEMBERIAN INFORMASI PASIEN DAN KELUARGA\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (7, 1076, \'PERNYATAAN PERTANGGUNGAN SELISIH BIAYA\', \'PERNYATAAN PERTANGGUNGAN SELISIH BIAYA\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (8, 1076, \'SURAT KETERANGAN PINDAH KELAS PERAWATAN\', \'SURAT KETERANGAN PINDAH KELAS PERAWATAN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (9, 1076, \'PERNYATAAN PERUBAHAN KELAS PERAWATAN\', \'PERNYATAAN PERUBAHAN KELAS PERAWATAN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (10, 1076, \'PENGAJUAN RENCANA OPERASI\', \'PENGAJUAN RENCANA OPERASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (11, 1076, \'SURAT PERNYATAAN\', \'SURAT PERNYATAAN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (12, 1077, \'RINGKASAN MASUK DAN KELUAR\', \'RINGKASAN MASUK DAN KELUAR\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (13, 1077, \'RESUME MEDIS / DICHARGE SUMMARY\', \'RESUME MEDIS / DICHARGE SUMMARY\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (14, 1077, \'TRANSFER PASIEN ANTAR RUANGAN\', \'TRANSFER PASIEN ANTAR RUANGAN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (15, 1077, \'CATATAN PERKEMBANGAN PASIEN TERINTEGRASI\', \'CATATAN PERKEMBANGAN PASIEN TERINTEGRASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (16, 1077, \'FORMULIR KONSULTASI\', \'FORMULIR KONSULTASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (17, 1077, \'STATUS MEDIS AWAL RAWAT INAP\', \'STATUS MEDIS AWAL RAWAT INAP\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (18, 1077, \'RENCANA PELAYANAN MEDIS PASIEN\', \'RENCANA PELAYANAN MEDIS PASIEN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (19, 1077, \'PERSETUUJUAN / PENOLAKAN TINDAKAN KEDOKTERAN\', \'PERSETUUJUAN / PENOLAKAN TINDAKAN KEDOKTERAN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (20, 1077, \'LAPORAN OPERASI\', \'LAPORAN OPERASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (21, 1077, \'INSTRUKSI POST OPERASI\', \'INSTRUKSI POST OPERASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (22, 1077, \'PEMANTAUAN LUKA OPERASI\', \'PEMANTAUAN LUKA OPERASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (23, 1077, \'SURGICAL SAFETY CHECKLIST\', \'SURGICAL SAFETY CHECKLIST\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (24, 1077, \'CHECK LIST PRE-POST OPERASI DAN ENDOSCOPY\', \'CHECK LIST PRE-POST OPERASI DAN ENDOSCOPY\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (25, 1077, \'PERSETUJUAN TINDAKAN ANESTHESI DAN SEDASI\', \'PERSETUJUAN TINDAKAN ANESTHESI DAN SEDASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (26, 1077, \'PRA ANESTESI DAN PRA SEDASI\', \'PRA ANESTESI DAN PRA SEDASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (27, 1077, \'PEMANTAUAN ANESTHESI DAN SEDASI\', \'PEMANTAUAN ANESTHESI DAN SEDASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (28, 1077, \'CATATAN POST ANESTESI / SEDASI\', \'CATATAN POST ANESTESI / SEDASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (29, 1077, \'KONDISI STERILISASI ALAT RAWAT INAP DAN RAWAT JALAN\', \'KONDISI STERILISASI ALAT RAWAT INAP DAN RAWAT JALAN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (30, 1077, \'PENGKAJIAN KEPERAWATAN RAWAT INAP\', \'PENGKAJIAN KEPERAWATAN RAWAT INAP\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (31, 1077, \'PENGKAJIAN KEPERWATAN OBSTETRI\', \'PENGKAJIAN KEPERWATAN OBSTETRI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (32, 1077, \'CATATAN PERWATAN / NURSING NOTES\', \'CATATAN PERWATAN / NURSING NOTES\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (33, 1077, \'ASUKAN KEPERAWATAN PERIOPERATIF\', \'ASUKAN KEPERAWATAN PERIOPERATIF\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (34, 1077, \'PENGKAJIAN NYERI\', \'PENGKAJIAN NYERI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (35, 1077, \'PENGKAJIAN RESIKO DEKUBITUS (BRANDEN SCALE)\', \'PENGKAJIAN RESIKO DEKUBITUS (BRANDEN SCALE)\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (36, 1077, \'PENGKAJIAN RESIKO PASIEN JATUH DEWASA (MORSE SCALE)\', \'PENGKAJIAN RESIKO PASIEN JATUH DEWASA (MORSE SCALE)\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (37, 1077, \'PENGKAJIAN RESIKO PASIEN JATUH ANAK (HUMPTY DYMPTY)\', \'PENGKAJIAN RESIKO PASIEN JATUH ANAK (HUMPTY DYMPTY)\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (38, 1077, \'MONITORING BALANCE CAIRAN\', \'MONITORING BALANCE CAIRAN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (39, 1077, \'CATATAN PENGOBATAN\', \'CATATAN PENGOBATAN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (40, 1077, \'INTERVENSI KEPERAWATAN UTAMA\', \'INTERVENSI KEPERAWATAN UTAMA\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (41, 1077, \'OBSERVASI TERINTEGRASI DENGAN MEWS\', \'OBSERVASI TERINTEGRASI DENGAN MEWS\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (42, 1077, \'KONDISI STERILISASI ALAT RAWAT INAP DAN RAWAT JALAN\', \'KONDISI STERILISASI ALAT RAWAT INAP DAN RAWAT JALAN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (43, 1077, \'PEMBERIAN INFORMASI DAN EDUKASI TERINEGRASI\', \'PEMBERIAN INFORMASI DAN EDUKASI TERINEGRASI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (44, 1077, \'PELAKSANAAN EDUKASI KEPADA PASIEN / KELUARGA\', \'PELAKSANAAN EDUKASI KEPADA PASIEN / KELUARGA\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (45, 1077, \'DATA SERVEILANS PEMAKAIAN ALAT INVASIVE IVL\', \'DATA SERVEILANS PEMAKAIAN ALAT INVASIVE IVL\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (46, 1077, \'CHEKLIST LIST PASIEN MASUK DAN PASIEN PULANG\', \'CHEKLIST LIST PASIEN MASUK DAN PASIEN PULANG\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (47, 1077, \'DAFTAR DPJP\', \'DAFTAR DPJP\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (48, 1077, \'PASIEN PULANG DARI RAWAT INAP\', \'PASIEN PULANG DARI RAWAT INAP\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (49, 1077, \'PESANAN PULANG\', \'PESANAN PULANG\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (50, 1078, \'LAPORAN HASIL LABORATORIUM\', \'LAPORAN HASIL LABORATORIUM\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (51, 1078, \'LAPORAN HASIL RADIOLOGI\', \'LAPORAN HASIL RADIOLOGI\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (52, 1078, \'LEMBAR HASIL CTG//USG/EKG\', \'LEMBAR HASIL CTG//USG/EKG\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (53, 1079, \'KONFIRMASI TINDAKAN MEDIS\', \'KONFIRMASI TINDAKAN MEDIS\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (54, 1079, \'SURAT TANDA KELUAR\', \'SURAT TANDA KELUAR\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (55, 1079, \'FORM PERSETUJUAN TINDAKAN\', \'FORM PERSETUJUAN TINDAKAN\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (56, 1079, \'JASA DOKTER PENUNJANG MEDIS\', \'JASA DOKTER PENUNJANG MEDIS\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (57, 1079, \'SURAT KETERANGAN SAKIT\', \'SURAT KETERANGAN SAKIT\', NULL, \'2021-09-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);

 
        ');

        $this->execute('
            DROP VIEW IF EXISTS public.inforiwayatpasien_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW public.inforiwayatpasien_v AS
            SELECT \'RJ/RD\'::text AS tes,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.pasienpulang_id,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.ruangan_id AS ruangan_pend_id,
            pend_ruangan.ruangan_nama AS ruangan_pend,
            NULL::integer AS ruangan_adm_id,
            NULL::character varying AS ruangan_adm,
            pendaftaran_t.pegawai_id AS dok_rjrd_id,
            dok_rjrd.nama_pegawai AS dok_rjrd,
            NULL::integer AS dok_ri_id,
            NULL::character varying AS dok_ri,
                CASE
                    WHEN anamnesa_t.r_anamesa IS NULL THEN 0
                    ELSE 1
                END AS r_anamesa,
                CASE
                    WHEN pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL THEN 0
                    ELSE 1
                END AS r_pemeriksaanfisik,
                CASE
                    WHEN pasienmorbiditas_t.r_diagnosa IS NULL THEN 0
                    ELSE 1
                END AS r_diagnosa,
                CASE
                    WHEN konsulpoli_t.r_konsulpoli IS NULL THEN 0
                    ELSE 1
                END AS r_konsulpoli,
                CASE
                    WHEN tindakanpelayanan_t.r_tindakan IS NULL THEN 0
                    ELSE 1
                END AS r_tindakan,
                CASE
                    WHEN obatalkespasien_t.r_bmhp IS NULL THEN 0
                    ELSE 1
                END AS r_bmhp,
                CASE
                    WHEN reseptur_t.r_reseptur IS NULL THEN 0
                    ELSE 1
                END AS r_reseptur,
                CASE
                    WHEN anamnesa_t.r_anamesa IS NULL AND pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL AND pasienmorbiditas_t.r_diagnosa IS NULL AND tindakanpelayanan_t.r_tindakan IS NULL AND obatalkespasien_t.r_bmhp IS NULL AND reseptur_t.r_reseptur IS NULL AND hasilpemeriksaanlab_t.p_laboratorium IS NULL AND hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
                    ELSE 1
                END AS r_resumemedis_rj_rd,
                CASE
                    WHEN hasilpemeriksaanlab_t.p_laboratorium IS NULL THEN 0
                    ELSE 1
                END AS p_laboratorium,
                CASE
                    WHEN hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
                    ELSE 1
                END AS p_radiologi,
                CASE
                    WHEN operasi.p_operasi IS NULL THEN 0
                    ELSE 1
                END AS p_operasi,
                CASE
                    WHEN asesmenawal_t.r_asesmenawal IS NULL THEN 0
                    ELSE 1
                END AS r_asesmenawal,
                CASE
                    WHEN rekonsiliasiobat_t.r_rekonsiliasiobat IS NULL THEN 0
                    ELSE 1
                END AS r_rekonsiliasiobat,
                CASE
                    WHEN asesmenmedis_t.r_asesmenmedis IS NULL THEN 0
                    ELSE 1
                END AS r_asesmenmedis,
                CASE
                    WHEN rencanapulang_t.r_dischargeplan IS NULL THEN 0
                    ELSE 1
                END AS r_dischargeplan,
                CASE
                    WHEN cppt_t.r_cppt IS NULL THEN 0
                    ELSE 1
                END AS r_cppt,
                CASE
                    WHEN instruksitindakan_t.r_instruktitindakan IS NULL THEN 0
                    ELSE 1
                END AS r_instruktitindakan,
                CASE
                    WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp IS NULL THEN 0
                    ELSE 1
                END AS r_instruktitindakanbmhp,
                CASE
                    WHEN pemberianobat_t.r_pemberianobat IS NULL THEN 0
                    ELSE 1
                END AS r_pemberianobat,
                CASE
                    WHEN permintaankonsul_t.r_permintaankonsul IS NULL THEN 0
                    ELSE 1
                END AS r_permintaankonsul,
                CASE
                    WHEN pindahkamar_t.r_pindahkamar IS NULL THEN 0
                    ELSE 1
                END AS r_pindahkamar,
                CASE
                    WHEN resumemedisri_t.r_resumemedis_ri IS NULL THEN 0
                    ELSE 1
                END AS r_resumemedis_ri,
                CASE
                    WHEN visitdokter.r_visitedokter IS NULL THEN 0
                    ELSE 1
                END AS r_visitedokter,
                CASE
                    WHEN kesimpulanrd_t.r_kesimpulan_rd IS NULL THEN 0
                    ELSE 1
                END AS r_kesimpulan_rd,
                CASE
                    WHEN asesmenmedisrd_t.r_asesmendokter IS NULL THEN 0
                    ELSE 1
                END AS r_asesmendokter,
                CASE
                    WHEN asesmenperawatrd_t.r_asesmenperawat_rd IS NULL THEN 0
                    ELSE 1
                END AS r_asesmenperawat_rd,
                CASE
                    WHEN asuhangizi_t.r_asuhangizi IS NULL THEN 0
                    ELSE 1
                END AS r_asuhangizi,
                CASE
                    WHEN soaprj_t.r_soaprj IS NULL THEN 0
                    ELSE 1
                END AS r_soaprj,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
                CASE
                    WHEN anamnesa_t.r_anamesa = 1 THEN \'SELESAI\'::text
                    WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN \'SELESAI\'::text
                    WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN \'SELESAI\'::text
                    WHEN konsulpoli_t.r_konsulpoli = 1 THEN \'SELESAI\'::text
                    WHEN obatalkespasien_t.r_bmhp = 1 THEN \'SELESAI\'::text
                    WHEN reseptur_t.r_reseptur = 1 THEN \'SELESAI\'::text
                    WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN \'SELESAI\'::text
                    WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN \'SELESAI\'::text
                    WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN \'SELESAI\'::text
                    WHEN operasi.p_operasi = 1 THEN \'SELESAI\'::text
                    ELSE \'BELUM SELESAI\'::text
                END AS status_rj,
                CASE
                    WHEN anamnesa_t.r_anamesa = 1 THEN \'SELESAI\'::text
                    WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN \'SELESAI\'::text
                    WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN \'SELESAI\'::text
                    WHEN konsulpoli_t.r_konsulpoli = 1 THEN \'SELESAI\'::text
                    WHEN obatalkespasien_t.r_bmhp = 1 THEN \'SELESAI\'::text
                    WHEN reseptur_t.r_reseptur = 1 THEN \'SELESAI\'::text
                    WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN \'SELESAI\'::text
                    WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN \'SELESAI\'::text
                    WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN \'SELESAI\'::text
                    WHEN operasi.p_operasi = 1 THEN \'SELESAI\'::text
                    WHEN asesmenawal_t.r_asesmenawal = 1 THEN \'SELESAI\'::text
                    WHEN rekonsiliasiobat_t.r_rekonsiliasiobat = 1 THEN \'SELESAI\'::text
                    WHEN asesmenmedis_t.r_asesmenmedis = 1 THEN \'SELESAI\'::text
                    WHEN rencanapulang_t.r_dischargeplan = 1 THEN \'SELESAI\'::text
                    WHEN cppt_t.r_cppt = 1 THEN \'SELESAI\'::text
                    WHEN instruksitindakan_t.r_instruktitindakan = 1 THEN \'SELESAI\'::text
                    WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp = 1 THEN \'SELESAI\'::text
                    WHEN pemberianobat_t.r_pemberianobat = 1 THEN \'SELESAI\'::text
                    WHEN pindahkamar_t.r_pindahkamar = 1 THEN \'SELESAI\'::text
                    WHEN permintaankonsul_t.r_permintaankonsul = 1 THEN \'SELESAI\'::text
                    WHEN resumemedisri_t.r_resumemedis_ri = 1 THEN \'SELESAI\'::text
                    WHEN visitdokter.r_visitedokter = 1 THEN \'SELESAI\'::text
                    WHEN kesimpulanrd_t.r_kesimpulan_rd = 1 THEN \'SELESAI\'::text
                    WHEN asesmenmedisrd_t.r_asesmendokter = 1 THEN \'SELESAI\'::text
                    WHEN asesmenperawatrd_t.r_asesmenperawat_rd = 1 THEN \'SELESAI\'::text
                    WHEN asuhangizi_t.r_asuhangizi = 1 THEN \'SELESAI\'::text
                    ELSE \'BELUM SELESAI\'::text
                END AS status_rd_ri,
            pasienpulang_t.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama,
            pendaftaran_t.instalasi_id AS instalasi_pend_id,
                CASE
                    WHEN dokumenupload.r_dokumenupload IS NULL THEN 0
                    ELSE 1
                END AS is_dokumen
            FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ruangan_m pend_ruangan ON pendaftaran_t.ruangan_id = pend_ruangan.ruangan_id
             JOIN pegawai_m dok_rjrd ON pendaftaran_t.pegawai_id = dok_rjrd.pegawai_id
             LEFT JOIN ( SELECT anamnesa_t_1.pendaftaran_id,
                    count(anamnesa_t_1.pendaftaran_id) AS r_anamesa
                   FROM anamnesa_t anamnesa_t_1
                  WHERE anamnesa_t_1.is_deleted = false
                  GROUP BY anamnesa_t_1.pendaftaran_id) anamnesa_t ON anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT pemeriksaanfisik_t_1.pendaftaran_id,
                    count(pemeriksaanfisik_t_1.pendaftaran_id) AS r_pemeriksaanfisik
                   FROM pemeriksaanfisik_t pemeriksaanfisik_t_1
                  WHERE pemeriksaanfisik_t_1.is_deleted = false
                  GROUP BY pemeriksaanfisik_t_1.pendaftaran_id) pemeriksaanfisik_t ON pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT pasienmorbiditas_t_1.pendaftaran_id,
                    count(pasienmorbiditas_t_1.pendaftaran_id) AS r_diagnosa
                   FROM pasienmorbiditas_t pasienmorbiditas_t_1
                  WHERE pasienmorbiditas_t_1.is_deleted = false
                  GROUP BY pasienmorbiditas_t_1.pendaftaran_id) pasienmorbiditas_t ON pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT konsulpoli_t_1.pendaftaran_id,
                    count(konsulpoli_t_1.pendaftaran_id) AS r_konsulpoli
                   FROM konsulpoli_t konsulpoli_t_1
                  WHERE konsulpoli_t_1.is_deleted = false
                  GROUP BY konsulpoli_t_1.pendaftaran_id) konsulpoli_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
                    count(tindakanpelayanan_t_1.pendaftaran_id) AS r_tindakan
                   FROM tindakanpelayanan_t tindakanpelayanan_t_1
                     LEFT JOIN ( SELECT daftartindakan_m.daftartindakan_id
                           FROM daftartindakan_m
                          WHERE daftartindakan_m.kelompoktindakan_id <> ALL (ARRAY[17, 19])) karcis ON karcis.daftartindakan_id = tindakanpelayanan_t_1.daftartindakan_id
                  GROUP BY tindakanpelayanan_t_1.pendaftaran_id) tindakanpelayanan_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT obatalkespasien_t_1.pendaftaran_id,
                    count(obatalkespasien_t_1.pendaftaran_id) AS r_bmhp
                   FROM obatalkespasien_t obatalkespasien_t_1
                  GROUP BY obatalkespasien_t_1.pendaftaran_id) obatalkespasien_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT reseptur_t_1.pendaftaran_id,
                    count(reseptur_t_1.pendaftaran_id) AS r_reseptur
                   FROM reseptur_t reseptur_t_1
                  WHERE reseptur_t_1.is_deleted = false
                  GROUP BY reseptur_t_1.pendaftaran_id) reseptur_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT resumemedis_t_1.pendaftaran_id,
                    count(resumemedis_t_1.pendaftaran_id) AS r_resumemedis_rj_rd
                   FROM resumemedis_t resumemedis_t_1
                  WHERE resumemedis_t_1.is_deleted = false
                  GROUP BY resumemedis_t_1.pendaftaran_id) resumemedis_t ON resumemedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT hasilpemeriksaanlab_t_1.pendaftaran_id,
                    count(hasilpemeriksaanlab_t_1.pendaftaran_id) AS p_laboratorium
                   FROM hasilpemeriksaanlab_t hasilpemeriksaanlab_t_1
                  WHERE hasilpemeriksaanlab_t_1.is_deleted = false
                  GROUP BY hasilpemeriksaanlab_t_1.pendaftaran_id) hasilpemeriksaanlab_t ON hasilpemeriksaanlab_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT hasilpemeriksaanrad_t_1.pendaftaran_id,
                    count(hasilpemeriksaanrad_t_1.pendaftaran_id) AS p_radiologi
                   FROM hasilpemeriksaanrad_t hasilpemeriksaanrad_t_1
                  WHERE hasilpemeriksaanrad_t_1.is_deleted = false AND hasilpemeriksaanrad_t_1.tgl_verifikasi IS NOT NULL
                  GROUP BY hasilpemeriksaanrad_t_1.pendaftaran_id) hasilpemeriksaanrad_t ON hasilpemeriksaanrad_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
                    count(*) AS p_operasi
                   FROM inpostoperasi_t
                     JOIN pasienmasukpenunjang_t ON inpostoperasi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                  GROUP BY pasienmasukpenunjang_t.pendaftaran_id) operasi ON pendaftaran_t.pendaftaran_id = operasi.pendaftaran_id
             LEFT JOIN ( SELECT asesmenawal_t_1.pendaftaran_id,
                    count(asesmenawal_t_1.pendaftaran_id) AS r_asesmenawal
                   FROM asesmenawal_t asesmenawal_t_1
                  WHERE asesmenawal_t_1.is_deleted = false
                  GROUP BY asesmenawal_t_1.pendaftaran_id) asesmenawal_t ON pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id
             LEFT JOIN ( SELECT rekonsiliasiobat_t_1.pendaftaran_id,
                    count(rekonsiliasiobat_t_1.pendaftaran_id) AS r_rekonsiliasiobat
                   FROM rekonsiliasiobat_t rekonsiliasiobat_t_1
                  WHERE rekonsiliasiobat_t_1.is_deleted = false
                  GROUP BY rekonsiliasiobat_t_1.pendaftaran_id) rekonsiliasiobat_t ON pendaftaran_t.pendaftaran_id = rekonsiliasiobat_t.pendaftaran_id
             LEFT JOIN ( SELECT asesmenmedis_t_1.pendaftaran_id,
                    count(asesmenmedis_t_1.pendaftaran_id) AS r_asesmenmedis
                   FROM asesmenmedis_t asesmenmedis_t_1
                  WHERE asesmenmedis_t_1.is_deleted = false
                  GROUP BY asesmenmedis_t_1.pendaftaran_id) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id
             LEFT JOIN ( SELECT rencanapulang_t_1.pendaftaran_id,
                    count(rencanapulang_t_1.pendaftaran_id) AS r_dischargeplan
                   FROM rencanapulang_t rencanapulang_t_1
                  WHERE rencanapulang_t_1.is_deleted = false
                  GROUP BY rencanapulang_t_1.pendaftaran_id) rencanapulang_t ON pendaftaran_t.pendaftaran_id = rencanapulang_t.pendaftaran_id
             LEFT JOIN ( SELECT cppt_t_1.pendaftaran_id,
                    count(cppt_t_1.pendaftaran_id) AS r_cppt
                   FROM cppt_t cppt_t_1
                  WHERE cppt_t_1.is_deleted = false
                  GROUP BY cppt_t_1.pendaftaran_id) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
             LEFT JOIN ( SELECT instruksitindakan_t_1.pendaftaran_id,
                    count(instruksitindakan_t_1.pendaftaran_id) AS r_instruktitindakan
                   FROM instruksitindakan_t instruksitindakan_t_1
                  WHERE instruksitindakan_t_1.is_deleted = false
                  GROUP BY instruksitindakan_t_1.pendaftaran_id) instruksitindakan_t ON pendaftaran_t.pendaftaran_id = instruksitindakan_t.pendaftaran_id
             LEFT JOIN ( SELECT instruksitindakanbmhp_t_1.pendaftaran_id,
                    count(instruksitindakanbmhp_t_1.pendaftaran_id) AS r_instruktitindakanbmhp
                   FROM instruksitindakanbmhp_t instruksitindakanbmhp_t_1
                  WHERE instruksitindakanbmhp_t_1.is_deleted = false
                  GROUP BY instruksitindakanbmhp_t_1.pendaftaran_id) instruksitindakanbmhp_t ON pendaftaran_t.pendaftaran_id = instruksitindakanbmhp_t.pendaftaran_id
             LEFT JOIN ( SELECT pemberianobat_t_1.pendaftaran_id,
                    count(pemberianobat_t_1.pendaftaran_id) AS r_pemberianobat
                   FROM pemberianobat_t pemberianobat_t_1
                  WHERE pemberianobat_t_1.is_deleted = false
                  GROUP BY pemberianobat_t_1.pendaftaran_id) pemberianobat_t ON pendaftaran_t.pendaftaran_id = pemberianobat_t.pendaftaran_id
             LEFT JOIN ( SELECT permintaankonsul_t_1.pendaftaran_id,
                    count(permintaankonsul_t_1.pendaftaran_id) AS r_permintaankonsul
                   FROM permintaankonsul_t permintaankonsul_t_1
                  WHERE permintaankonsul_t_1.is_deleted = false
                  GROUP BY permintaankonsul_t_1.pendaftaran_id) permintaankonsul_t ON pendaftaran_t.pendaftaran_id = permintaankonsul_t.pendaftaran_id
             LEFT JOIN ( SELECT pindahkamar_t_1.pendaftaran_id,
                    count(pindahkamar_t_1.pendaftaran_id) AS r_pindahkamar
                   FROM pindahkamar_t pindahkamar_t_1
                  WHERE pindahkamar_t_1.is_deleted = false
                  GROUP BY pindahkamar_t_1.pendaftaran_id) pindahkamar_t ON pendaftaran_t.pendaftaran_id = pindahkamar_t.pendaftaran_id
             LEFT JOIN ( SELECT resumemedisri_t_1.pendaftaran_id,
                    count(resumemedisri_t_1.pendaftaran_id) AS r_resumemedis_ri
                   FROM resumemedisri_t resumemedisri_t_1
                  WHERE resumemedisri_t_1.is_deleted = false
                  GROUP BY resumemedisri_t_1.pendaftaran_id) resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id
             LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
                    count(*) AS r_visitedokter
                   FROM tindakanpelayanan_t tindakanpelayanan_t_1
                     JOIN cppt_t cppt_t_1 ON tindakanpelayanan_t_1.tindakanpelayanan_id = cppt_t_1.tindakanvisite_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  WHERE daftartindakan_m.kelompoktindakan_id = 32 AND cppt_t_1.is_visitedokter IS TRUE
                  GROUP BY tindakanpelayanan_t_1.pendaftaran_id) visitdokter ON pendaftaran_t.pendaftaran_id = visitdokter.pendaftaran_id
             LEFT JOIN ( SELECT kesimpulanrd_t_1.pendaftaran_id,
                    count(kesimpulanrd_t_1.pendaftaran_id) AS r_kesimpulan_rd
                   FROM kesimpulanrd_t kesimpulanrd_t_1
                  WHERE kesimpulanrd_t_1.is_deleted = false
                  GROUP BY kesimpulanrd_t_1.pendaftaran_id) kesimpulanrd_t ON pendaftaran_t.pendaftaran_id = kesimpulanrd_t.pendaftaran_id
             LEFT JOIN ( SELECT asesmenmedisrd_t_1.pendaftaran_id,
                    count(asesmenmedisrd_t_1.pendaftaran_id) AS r_asesmendokter
                   FROM asesmenmedisrd_t asesmenmedisrd_t_1
                  WHERE asesmenmedisrd_t_1.is_deleted = false
                  GROUP BY asesmenmedisrd_t_1.pendaftaran_id) asesmenmedisrd_t ON pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id
             LEFT JOIN ( SELECT asesmenperawatrd_t_1.pendaftaran_id,
                    count(asesmenperawatrd_t_1.pendaftaran_id) AS r_asesmenperawat_rd
                   FROM asesmenperawatrd_t asesmenperawatrd_t_1
                  WHERE asesmenperawatrd_t_1.is_deleted = false
                  GROUP BY asesmenperawatrd_t_1.pendaftaran_id) asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
             LEFT JOIN ( SELECT asuhangizi_t_1.pendaftaran_id,
                    count(asuhangizi_t_1.pendaftaran_id) AS r_asuhangizi
                   FROM asuhangizi_t asuhangizi_t_1
                  WHERE asuhangizi_t_1.is_deleted = false
                  GROUP BY asuhangizi_t_1.pendaftaran_id) asuhangizi_t ON pendaftaran_t.pendaftaran_id = asuhangizi_t.pendaftaran_id
             LEFT JOIN ( SELECT soaprj_t_1.pendaftaran_id,
                    count(soaprj_t_1.pendaftaran_id) AS r_soaprj
                   FROM soaprj_t soaprj_t_1
                  WHERE soaprj_t_1.is_deleted = false
                  GROUP BY soaprj_t_1.pendaftaran_id) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
             LEFT JOIN ( SELECT dokumenupload_t.pendaftaran_id,
                    count(dokumenupload_t.pendaftaran_id) AS r_dokumenupload
                   FROM dokumenupload_t
                  WHERE dokumenupload_t.pasienadmisi_id IS NULL AND dokumenupload_t.is_deleted = false
                  GROUP BY dokumenupload_t.pendaftaran_id) dokumenupload ON pendaftaran_t.pendaftaran_id = dokumenupload.pendaftaran_id
             LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
            WHERE pendaftaran_t.instalasi_id <> 3
            UNION ALL
            SELECT \'RI\'::text AS tes,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.pasien_id,
            pasienadmisi_t.pasienpulang_id,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
                CASE pendaftaran_t.instalasi_id
                    WHEN 12 THEN adm_ruangan.ruangan_id
                    ELSE pendaftaran_t.ruangan_id
                END AS ruangan_pend_id,
                CASE pendaftaran_t.instalasi_id
                    WHEN 12 THEN adm_ruangan.ruangan_nama
                    ELSE ruangan_m.ruangan_nama
                END AS ruangan_pend,
            pasienadmisi_t.ruangan_id AS ruangan_adm_id,
            adm_ruangan.ruangan_nama AS ruangan_adm,
            pendaftaran_t.pegawai_id AS dok_rjrd_id,
            pegawai_m.nama_pegawai AS dok_rjrd,
            pasienadmisi_t.pegawai_id AS dok_ri_id,
            dok_ri.nama_pegawai AS dok_ri,
                CASE
                    WHEN anamnesa_t.r_anamesa IS NULL THEN 0
                    ELSE 1
                END AS r_anamesa,
                CASE
                    WHEN pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL THEN 0
                    ELSE 1
                END AS r_pemeriksaanfisik,
                CASE
                    WHEN pasienmorbiditas_t.r_diagnosa IS NULL THEN 0
                    ELSE 1
                END AS r_diagnosa,
                CASE
                    WHEN konsulpoli_t.r_konsulpoli IS NULL THEN 0
                    ELSE 1
                END AS r_konsulpoli,
                CASE
                    WHEN tindakanpelayanan_t.r_tindakan IS NULL THEN 0
                    ELSE 1
                END AS r_tindakan,
                CASE
                    WHEN obatalkespasien_t.r_bmhp IS NULL THEN 0
                    ELSE 1
                END AS r_bmhp,
                CASE
                    WHEN reseptur_t.r_reseptur IS NULL THEN 0
                    ELSE 1
                END AS r_reseptur,
                CASE
                    WHEN anamnesa_t.r_anamesa IS NULL AND pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL AND pasienmorbiditas_t.r_diagnosa IS NULL AND tindakanpelayanan_t.r_tindakan IS NULL AND obatalkespasien_t.r_bmhp IS NULL AND reseptur_t.r_reseptur IS NULL AND hasilpemeriksaanlab_t.p_laboratorium IS NULL AND hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
                    ELSE 1
                END AS r_resumemedis_rj_rd,
                CASE
                    WHEN hasilpemeriksaanlab_t.p_laboratorium IS NULL THEN 0
                    ELSE 1
                END AS p_laboratorium,
                CASE
                    WHEN hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
                    ELSE 1
                END AS p_radiologi,
                CASE
                    WHEN operasi.p_operasi IS NULL THEN 0
                    ELSE 1
                END AS p_operasi,
                CASE
                    WHEN asesmenawal_t.r_asesmenawal IS NULL THEN 0
                    ELSE 1
                END AS r_asesmenawal,
                CASE
                    WHEN rekonsiliasiobat_t.r_rekonsiliasiobat IS NULL THEN 0
                    ELSE 1
                END AS r_rekonsiliasiobat,
                CASE
                    WHEN asesmenmedis_t.r_asesmenmedis IS NULL THEN 0
                    ELSE 1
                END AS r_asesmenmedis,
                CASE
                    WHEN rencanapulang_t.r_dischargeplan IS NULL THEN 0
                    ELSE 1
                END AS r_dischargeplan,
                CASE
                    WHEN cppt_t.r_cppt IS NULL THEN 0
                    ELSE 1
                END AS r_cppt,
                CASE
                    WHEN instruksitindakan_t.r_instruktitindakan IS NULL THEN 0
                    ELSE 1
                END AS r_instruktitindakan,
                CASE
                    WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp IS NULL THEN 0
                    ELSE 1
                END AS r_instruktitindakanbmhp,
                CASE
                    WHEN pemberianobat_t.r_pemberianobat IS NULL THEN 0
                    ELSE 1
                END AS r_pemberianobat,
                CASE
                    WHEN permintaankonsul_t.r_permintaankonsul IS NULL THEN 0
                    ELSE 1
                END AS r_permintaankonsul,
                CASE
                    WHEN pindahkamar_t.r_pindahkamar IS NULL THEN 0
                    ELSE 1
                END AS r_pindahkamar,
                CASE
                    WHEN resumemedisri_t.r_resumemedis_ri IS NULL THEN 0
                    ELSE 1
                END AS r_resumemedis_ri,
                CASE
                    WHEN visitdokter.r_visitedokter IS NULL THEN 0
                    ELSE 1
                END AS r_visitedokter,
                CASE
                    WHEN kesimpulanrd_t.r_kesimpulan_rd IS NULL THEN 0
                    ELSE 1
                END AS r_kesimpulan_rd,
                CASE
                    WHEN asesmenmedisrd_t.r_asesmendokter IS NULL THEN 0
                    ELSE 1
                END AS r_asesmendokter,
                CASE
                    WHEN asesmenperawatrd_t.r_asesmenperawat_rd IS NULL THEN 0
                    ELSE 1
                END AS r_asesmenperawat_rd,
                CASE
                    WHEN asuhangizi_t.r_asuhangizi IS NULL THEN 0
                    ELSE 1
                END AS r_asuhangizi,
                CASE
                    WHEN soaprj_t.r_soaprj IS NULL THEN 0
                    ELSE 1
                END AS r_soaprj,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
                CASE
                    WHEN anamnesa_t.r_anamesa = 1 THEN \'SELESAI\'::text
                    WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN \'SELESAI\'::text
                    WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN \'SELESAI\'::text
                    WHEN konsulpoli_t.r_konsulpoli = 1 THEN \'SELESAI\'::text
                    WHEN obatalkespasien_t.r_bmhp = 1 THEN \'SELESAI\'::text
                    WHEN reseptur_t.r_reseptur = 1 THEN \'SELESAI\'::text
                    WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN \'SELESAI\'::text
                    WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN \'SELESAI\'::text
                    WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN \'SELESAI\'::text
                    WHEN operasi.p_operasi = 1 THEN \'SELESAI\'::text
                    ELSE \'BELUM SELESAI\'::text
                END AS status_rj,
                CASE
                    WHEN anamnesa_t.r_anamesa = 1 THEN \'SELESAI\'::text
                    WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN \'SELESAI\'::text
                    WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN \'SELESAI\'::text
                    WHEN konsulpoli_t.r_konsulpoli = 1 THEN \'SELESAI\'::text
                    WHEN obatalkespasien_t.r_bmhp = 1 THEN \'SELESAI\'::text
                    WHEN reseptur_t.r_reseptur = 1 THEN \'SELESAI\'::text
                    WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN \'SELESAI\'::text
                    WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN \'SELESAI\'::text
                    WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN \'SELESAI\'::text
                    WHEN operasi.p_operasi = 1 THEN \'SELESAI\'::text
                    WHEN asesmenawal_t.r_asesmenawal = 1 THEN \'SELESAI\'::text
                    WHEN rekonsiliasiobat_t.r_rekonsiliasiobat = 1 THEN \'SELESAI\'::text
                    WHEN asesmenmedis_t.r_asesmenmedis = 1 THEN \'SELESAI\'::text
                    WHEN rencanapulang_t.r_dischargeplan = 1 THEN \'SELESAI\'::text
                    WHEN cppt_t.r_cppt = 1 THEN \'SELESAI\'::text
                    WHEN instruksitindakan_t.r_instruktitindakan = 1 THEN \'SELESAI\'::text
                    WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp = 1 THEN \'SELESAI\'::text
                    WHEN pemberianobat_t.r_pemberianobat = 1 THEN \'SELESAI\'::text
                    WHEN pindahkamar_t.r_pindahkamar = 1 THEN \'SELESAI\'::text
                    WHEN permintaankonsul_t.r_permintaankonsul = 1 THEN \'SELESAI\'::text
                    WHEN resumemedisri_t.r_resumemedis_ri = 1 THEN \'SELESAI\'::text
                    WHEN visitdokter.r_visitedokter = 1 THEN \'SELESAI\'::text
                    WHEN kesimpulanrd_t.r_kesimpulan_rd = 1 THEN \'SELESAI\'::text
                    WHEN asesmenmedisrd_t.r_asesmendokter = 1 THEN \'SELESAI\'::text
                    WHEN asesmenperawatrd_t.r_asesmenperawat_rd = 1 THEN \'SELESAI\'::text
                    WHEN asuhangizi_t.r_asuhangizi = 1 THEN \'SELESAI\'::text
                    ELSE \'BELUM SELESAI\'::text
                END AS status_rd_ri,
            pasienpulang_t.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama,
                CASE pendaftaran_t.instalasi_id
                    WHEN 12 THEN adm_ruangan.instalasi_id
                    ELSE pendaftaran_t.instalasi_id
                END AS instalasi_pend_id,
                CASE
                    WHEN dokumenupload.r_dokumenupload IS NULL THEN 0
                    ELSE 1
                END AS is_dokumen
            FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ruangan_m adm_ruangan ON pasienadmisi_t.ruangan_id = adm_ruangan.ruangan_id
             LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN pegawai_m dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
             LEFT JOIN ( SELECT anamnesa_t_1.pendaftaran_id,
                    count(anamnesa_t_1.pendaftaran_id) AS r_anamesa
                   FROM anamnesa_t anamnesa_t_1
                  WHERE anamnesa_t_1.is_deleted = false
                  GROUP BY anamnesa_t_1.pendaftaran_id) anamnesa_t ON anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT pemeriksaanfisik_t_1.pendaftaran_id,
                    count(pemeriksaanfisik_t_1.pendaftaran_id) AS r_pemeriksaanfisik
                   FROM pemeriksaanfisik_t pemeriksaanfisik_t_1
                  WHERE pemeriksaanfisik_t_1.is_deleted = false
                  GROUP BY pemeriksaanfisik_t_1.pendaftaran_id) pemeriksaanfisik_t ON pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT pasienmorbiditas_t_1.pendaftaran_id,
                    count(pasienmorbiditas_t_1.pendaftaran_id) AS r_diagnosa
                   FROM pasienmorbiditas_t pasienmorbiditas_t_1
                  WHERE pasienmorbiditas_t_1.is_deleted = false
                  GROUP BY pasienmorbiditas_t_1.pendaftaran_id) pasienmorbiditas_t ON pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT konsulpoli_t_1.pendaftaran_id,
                    count(konsulpoli_t_1.pendaftaran_id) AS r_konsulpoli
                   FROM konsulpoli_t konsulpoli_t_1
                  WHERE konsulpoli_t_1.is_deleted = false
                  GROUP BY konsulpoli_t_1.pendaftaran_id) konsulpoli_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
                    count(tindakanpelayanan_t_1.pendaftaran_id) AS r_tindakan
                   FROM tindakanpelayanan_t tindakanpelayanan_t_1
                     LEFT JOIN ( SELECT daftartindakan_m.daftartindakan_id
                           FROM daftartindakan_m
                          WHERE daftartindakan_m.kelompoktindakan_id <> ALL (ARRAY[17, 19])) karcis ON karcis.daftartindakan_id = tindakanpelayanan_t_1.daftartindakan_id
                  GROUP BY tindakanpelayanan_t_1.pendaftaran_id) tindakanpelayanan_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT obatalkespasien_t_1.pendaftaran_id,
                    count(obatalkespasien_t_1.pendaftaran_id) AS r_bmhp
                   FROM obatalkespasien_t obatalkespasien_t_1
                  GROUP BY obatalkespasien_t_1.pendaftaran_id) obatalkespasien_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT reseptur_t_1.pendaftaran_id,
                    count(reseptur_t_1.pendaftaran_id) AS r_reseptur
                   FROM reseptur_t reseptur_t_1
                  WHERE reseptur_t_1.is_deleted = false
                  GROUP BY reseptur_t_1.pendaftaran_id) reseptur_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT resumemedis_t_1.pendaftaran_id,
                    count(resumemedis_t_1.pendaftaran_id) AS r_resumemedis_rj_rd
                   FROM resumemedis_t resumemedis_t_1
                  WHERE resumemedis_t_1.is_deleted = false
                  GROUP BY resumemedis_t_1.pendaftaran_id) resumemedis_t ON resumemedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT hasilpemeriksaanlab_t_1.pendaftaran_id,
                    hasilpemeriksaanlab_t_1.pasienadmisi_id,
                    count(hasilpemeriksaanlab_t_1.pendaftaran_id) AS p_laboratorium
                   FROM hasilpemeriksaanlab_t hasilpemeriksaanlab_t_1
                  WHERE hasilpemeriksaanlab_t_1.is_deleted = false
                  GROUP BY hasilpemeriksaanlab_t_1.pendaftaran_id, hasilpemeriksaanlab_t_1.pasienadmisi_id) hasilpemeriksaanlab_t ON hasilpemeriksaanlab_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = hasilpemeriksaanlab_t.pasienadmisi_id
             LEFT JOIN ( SELECT hasilpemeriksaanrad_t_1.pendaftaran_id,
                    hasilpemeriksaanrad_t_1.pasienadmisi_id,
                    count(hasilpemeriksaanrad_t_1.pendaftaran_id) AS p_radiologi
                   FROM hasilpemeriksaanrad_t hasilpemeriksaanrad_t_1
                  WHERE hasilpemeriksaanrad_t_1.is_deleted = false AND hasilpemeriksaanrad_t_1.tgl_verifikasi IS NOT NULL
                  GROUP BY hasilpemeriksaanrad_t_1.pendaftaran_id, hasilpemeriksaanrad_t_1.pasienadmisi_id) hasilpemeriksaanrad_t ON hasilpemeriksaanrad_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = hasilpemeriksaanrad_t.pasienadmisi_id
             LEFT JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
                    pasienmasukpenunjang_t.pasienadmisi_id,
                    count(*) AS p_operasi
                   FROM inpostoperasi_t
                     JOIN pasienmasukpenunjang_t ON inpostoperasi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                  GROUP BY pasienmasukpenunjang_t.pendaftaran_id, pasienmasukpenunjang_t.pasienadmisi_id) operasi ON pendaftaran_t.pendaftaran_id = operasi.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = operasi.pasienadmisi_id
             LEFT JOIN ( SELECT asesmenawal_t_1.pendaftaran_id,
                    count(asesmenawal_t_1.pendaftaran_id) AS r_asesmenawal
                   FROM asesmenawal_t asesmenawal_t_1
                  WHERE asesmenawal_t_1.is_deleted = false
                  GROUP BY asesmenawal_t_1.pendaftaran_id) asesmenawal_t ON pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id
             LEFT JOIN ( SELECT rekonsiliasiobat_t_1.pendaftaran_id,
                    count(rekonsiliasiobat_t_1.pendaftaran_id) AS r_rekonsiliasiobat
                   FROM rekonsiliasiobat_t rekonsiliasiobat_t_1
                  WHERE rekonsiliasiobat_t_1.is_deleted = false
                  GROUP BY rekonsiliasiobat_t_1.pendaftaran_id) rekonsiliasiobat_t ON pendaftaran_t.pendaftaran_id = rekonsiliasiobat_t.pendaftaran_id
             LEFT JOIN ( SELECT asesmenmedis_t_1.pendaftaran_id,
                    count(asesmenmedis_t_1.pendaftaran_id) AS r_asesmenmedis
                   FROM asesmenmedis_t asesmenmedis_t_1
                  WHERE asesmenmedis_t_1.is_deleted = false
                  GROUP BY asesmenmedis_t_1.pendaftaran_id) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id
             LEFT JOIN ( SELECT rencanapulang_t_1.pendaftaran_id,
                    count(rencanapulang_t_1.pendaftaran_id) AS r_dischargeplan
                   FROM rencanapulang_t rencanapulang_t_1
                  WHERE rencanapulang_t_1.is_deleted = false
                  GROUP BY rencanapulang_t_1.pendaftaran_id) rencanapulang_t ON pendaftaran_t.pendaftaran_id = rencanapulang_t.pendaftaran_id
             LEFT JOIN ( SELECT cppt_t_1.pendaftaran_id,
                    cppt_t_1.pasienadmisi_id,
                    count(cppt_t_1.pendaftaran_id) AS r_cppt
                   FROM cppt_t cppt_t_1
                  WHERE cppt_t_1.is_deleted = false
                  GROUP BY cppt_t_1.pendaftaran_id, cppt_t_1.pasienadmisi_id) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = cppt_t.pasienadmisi_id
             LEFT JOIN ( SELECT instruksitindakan_t_1.pendaftaran_id,
                    count(instruksitindakan_t_1.pendaftaran_id) AS r_instruktitindakan
                   FROM instruksitindakan_t instruksitindakan_t_1
                  WHERE instruksitindakan_t_1.is_deleted = false
                  GROUP BY instruksitindakan_t_1.pendaftaran_id) instruksitindakan_t ON pendaftaran_t.pendaftaran_id = instruksitindakan_t.pendaftaran_id
             LEFT JOIN ( SELECT instruksitindakanbmhp_t_1.pendaftaran_id,
                    count(instruksitindakanbmhp_t_1.pendaftaran_id) AS r_instruktitindakanbmhp
                   FROM instruksitindakanbmhp_t instruksitindakanbmhp_t_1
                  WHERE instruksitindakanbmhp_t_1.is_deleted = false
                  GROUP BY instruksitindakanbmhp_t_1.pendaftaran_id) instruksitindakanbmhp_t ON pendaftaran_t.pendaftaran_id = instruksitindakanbmhp_t.pendaftaran_id
             LEFT JOIN ( SELECT pemberianobat_t_1.pendaftaran_id,
                    count(pemberianobat_t_1.pendaftaran_id) AS r_pemberianobat
                   FROM pemberianobat_t pemberianobat_t_1
                  WHERE pemberianobat_t_1.is_deleted = false
                  GROUP BY pemberianobat_t_1.pendaftaran_id) pemberianobat_t ON pendaftaran_t.pendaftaran_id = pemberianobat_t.pendaftaran_id
             LEFT JOIN ( SELECT permintaankonsul_t_1.pendaftaran_id,
                    count(permintaankonsul_t_1.pendaftaran_id) AS r_permintaankonsul
                   FROM permintaankonsul_t permintaankonsul_t_1
                  WHERE permintaankonsul_t_1.is_deleted = false
                  GROUP BY permintaankonsul_t_1.pendaftaran_id) permintaankonsul_t ON pendaftaran_t.pendaftaran_id = permintaankonsul_t.pendaftaran_id
             LEFT JOIN ( SELECT pindahkamar_t_1.pendaftaran_id,
                    count(pindahkamar_t_1.pendaftaran_id) AS r_pindahkamar
                   FROM pindahkamar_t pindahkamar_t_1
                  WHERE pindahkamar_t_1.is_deleted = false
                  GROUP BY pindahkamar_t_1.pendaftaran_id) pindahkamar_t ON pendaftaran_t.pendaftaran_id = pindahkamar_t.pendaftaran_id
             LEFT JOIN ( SELECT resumemedisri_t_1.pendaftaran_id,
                    count(resumemedisri_t_1.pendaftaran_id) AS r_resumemedis_ri
                   FROM resumemedisri_t resumemedisri_t_1
                  WHERE resumemedisri_t_1.is_deleted = false
                  GROUP BY resumemedisri_t_1.pendaftaran_id) resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id
             LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
                    count(*) AS r_visitedokter
                   FROM tindakanpelayanan_t tindakanpelayanan_t_1
                     JOIN cppt_t cppt_t_1 ON tindakanpelayanan_t_1.tindakanpelayanan_id = cppt_t_1.tindakanvisite_id
                     JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  WHERE daftartindakan_m.kelompoktindakan_id = 32 AND cppt_t_1.is_visitedokter IS TRUE
                  GROUP BY tindakanpelayanan_t_1.pendaftaran_id) visitdokter ON pendaftaran_t.pendaftaran_id = visitdokter.pendaftaran_id
             LEFT JOIN ( SELECT kesimpulanrd_t_1.pendaftaran_id,
                    count(kesimpulanrd_t_1.pendaftaran_id) AS r_kesimpulan_rd
                   FROM kesimpulanrd_t kesimpulanrd_t_1
                  WHERE kesimpulanrd_t_1.is_deleted = false
                  GROUP BY kesimpulanrd_t_1.pendaftaran_id) kesimpulanrd_t ON pendaftaran_t.pendaftaran_id = kesimpulanrd_t.pendaftaran_id
             LEFT JOIN ( SELECT asesmenmedisrd_t_1.pendaftaran_id,
                    count(asesmenmedisrd_t_1.pendaftaran_id) AS r_asesmendokter
                   FROM asesmenmedisrd_t asesmenmedisrd_t_1
                  WHERE asesmenmedisrd_t_1.is_deleted = false
                  GROUP BY asesmenmedisrd_t_1.pendaftaran_id) asesmenmedisrd_t ON pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id
             LEFT JOIN ( SELECT asesmenperawatrd_t_1.pendaftaran_id,
                    count(asesmenperawatrd_t_1.pendaftaran_id) AS r_asesmenperawat_rd
                   FROM asesmenperawatrd_t asesmenperawatrd_t_1
                  WHERE asesmenperawatrd_t_1.is_deleted = false
                  GROUP BY asesmenperawatrd_t_1.pendaftaran_id) asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
             LEFT JOIN ( SELECT asuhangizi_t_1.pendaftaran_id,
                    count(asuhangizi_t_1.pendaftaran_id) AS r_asuhangizi
                   FROM asuhangizi_t asuhangizi_t_1
                  WHERE asuhangizi_t_1.is_deleted = false
                  GROUP BY asuhangizi_t_1.pendaftaran_id) asuhangizi_t ON pendaftaran_t.pendaftaran_id = asuhangizi_t.pendaftaran_id
             LEFT JOIN ( SELECT soaprj_t_1.pendaftaran_id,
                    count(soaprj_t_1.pendaftaran_id) AS r_soaprj
                   FROM soaprj_t soaprj_t_1
                  WHERE soaprj_t_1.is_deleted = false
                  GROUP BY soaprj_t_1.pendaftaran_id) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
             LEFT JOIN ( SELECT dokumenupload_t.pasienadmisi_id,
                    count(dokumenupload_t.pasienadmisi_id) AS r_dokumenupload
                   FROM dokumenupload_t
                  WHERE dokumenupload_t.is_deleted = false
                  GROUP BY dokumenupload_t.pasienadmisi_id) dokumenupload ON pasienadmisi_t.pasienadmisi_id = dokumenupload.pasienadmisi_id
             LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210928_124715_improvment_dokumen_pasien_US15044 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210928_124715_improvment_dokumen_pasien_US15044 cannot be reverted.\n";

        return false;
    }
    */
}
