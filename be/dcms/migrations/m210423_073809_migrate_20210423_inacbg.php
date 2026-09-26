<?php

use yii\db\Migration;

/**
 * Class m210423_073809_migrate_20210423_inacbg
 */
class m210423_073809_migrate_20210423_inacbg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
 
    $this->execute('ALTER TABLE "public"."groupinacbg_m" ADD COLUMN IF NOT exists "inacbgs_field" varchar(255) COLLATE "pg_catalog"."default";');

    $this->execute('TRUNCATE TABLE groupinacbg_m RESTART IDENTITY;');

    $this->execute("
        INSERT INTO public.groupinacbg_m(groupinacbg_id, groupinacbg_nama, groupinacbg_namalainnya, groupinacbg_kode, catatan, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, is_obat, inacbgs_field) VALUES 
(1, 'Prosedur Non Bedah', 'Prosedur Non Bedah', 'PNB', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'prosedur_non_bedah'),
(2, 'Prosedur Bedah', 'Prosedur Bedah', 'PB', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'prosedur_bedah'),
(3, 'Konsultasi', 'Konsultasi', 'KON', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'konsultasi'),
(4, 'Tenaga Ahli', 'Tenaga Ahli', 'TA', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'tenaga_ahli'),
(5, 'Keperawatan', 'Keperawatan', 'KEP', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'keperawatan'),
(6, 'Penunjang', 'Penunjang', 'PEN', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'penunjang'),
(7, 'Radiologi', 'Radiologi', 'RAD', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'radiologi'),
(8, 'Laboratorium', 'Laboratorium', 'LAB', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'laboratorium'),
(9, 'Pelayanan Darah', 'Pelayanan Darah', 'PD', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'pelayanan_darah'),
(10, 'Rehabilitasi', 'Rehabilitasi', 'REHAB', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'rehabilitasi'),
(11, 'Kamar/Akomodasi', 'Kamar/Akomodasi', 'AKOM', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'kamar_akomodasi'),
(12, 'Rawat Intensif', 'Rawat Intensif', 'RINTEN', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'rawat_intensif'),
(13, 'Obat', 'Obat', 'OA', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 't', 'obat'),
(14, 'Alkes', 'Alkes', 'ALKES', NULL, NULL, '2019-07-12 00:00:00', NULL, 2, '2019-07-30 13:42:43', 1, 'f', 't', NULL, NULL, 't', 'alkes'),
(15, 'BMHP', 'BMHP', 'BMHP', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 't', 'bmhp'),
(16, 'Sewa Alat', 'Sewa Alat', 'SA', NULL, NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'f', 'sewa_alat'),
(17, 'Obat Kronis', 'Obat Kronis', 'OBKR', 'Tambahan grup di agustus 2019', NULL, '2019-08-15 09:52:38', 1, 0, NULL, NULL, 'f', 't', NULL, NULL, 't', 'obat_kronis'),
(18, 'Obat Kemoterapi', 'Obat Kemoterapi', 'OBKM', 'Grup tambahan agustus 2019', NULL, '2019-08-15 09:53:06', 1, 0, NULL, NULL, 'f', 't', NULL, NULL, 't', 'obat_kemoterapi');
");

    $this->execute('SELECT setval(\'"public"."groupinacbg_m_groupinacbg_id_seq"\', 18, true);');

    
    
    $this->execute('DROP VIEW if exists "public"."infopasienbpjsklaim_v";');

    $this->execute("
        CREATE VIEW \"public\".\"infopasienbpjsklaim_v\" AS  SELECT 'TINDAKAN'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
        CASE
            WHEN tindakanpelayanan_t.instalasi_id = 3 THEN pendaftaran_t.pasienadmisi_id
            ELSE NULL::integer
        END AS pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    tindakanpelayanan_t.instalasi_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    daftartindakan_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    tindakanpelayanan_t.tarif_tindakan,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat,
    dokter.nama_pegawai AS dokter,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
        CASE
            WHEN groupinacbg_m.groupinacbg_id = 3 THEN true
            ELSE false
        END AS is_konsultasi
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN pegawai_m dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
  WHERE tindakanpelayanan_t.carabayar_id = 6 AND tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
UNION ALL
 SELECT 'TINDAKAN'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
        CASE
            WHEN ruangan_m.instalasi_id = 3 THEN pendaftaran_t.pasienadmisi_id
            ELSE NULL::integer
        END AS pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    ruangan_m.instalasi_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    daftartindakan_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    tindakanpelayanan_t.tarif_tindakan,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat,
    dokter.nama_pegawai AS dokter,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
        CASE
            WHEN groupinacbg_m.groupinacbg_id = 3 THEN true
            ELSE false
        END AS is_konsultasi
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     LEFT JOIN pegawai_m dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
  WHERE tindakanpelayanan_t.carabayar_id = 6 AND tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL
UNION ALL
 SELECT 'PAKET'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
        CASE
            WHEN tindakanpelayanan_t.instalasi_id = 3 THEN pendaftaran_t.pasienadmisi_id
            ELSE NULL::integer
        END AS pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    tindakanpelayanan_t.instalasi_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    daftartindakan_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    tindakanpelayanan_t.tarif_tindakan,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat,
    dokter.nama_pegawai AS dokter,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
        CASE
            WHEN groupinacbg_m.groupinacbg_id = 3 THEN true
            ELSE false
        END AS is_konsultasi
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN pegawai_m dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
  WHERE tindakanpelayanan_t.carabayar_id = 6 AND tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
UNION ALL
 SELECT 'PAKET'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
        CASE
            WHEN tindakanpelayanan_t.instalasi_id = 3 THEN pendaftaran_t.pasienadmisi_id
            ELSE NULL::integer
        END AS pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    tindakanpelayanan_t.instalasi_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    daftartindakan_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    tindakanpelayanan_t.tarif_tindakan,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat,
    dokter.nama_pegawai AS dokter,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
        CASE
            WHEN groupinacbg_m.groupinacbg_id = 3 THEN true
            ELSE false
        END AS is_konsultasi
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     LEFT JOIN pegawai_m dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
  WHERE tindakanpelayanan_t.carabayar_id = 6 AND tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL
UNION ALL
 SELECT 'OBAT'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    ruangan_m.instalasi_id,
    obatalkespasien_t.obatalkespasien_id AS tindakanpelayanan_id,
    obatalkes_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    obatalkespasien_t.hargajual_oa AS tarif_tindakan,
    obatalkes_m.obatalkes_nama AS tindakan_obat,
    dokter.nama_pegawai AS dokter,
    obatalkespasien_t.hargajual_oa AS tarif_satuan,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
        CASE
            WHEN groupinacbg_m.groupinacbg_id = 3 THEN true
            ELSE false
        END AS is_konsultasi
   FROM pendaftaran_t
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m dokter ON obatalkespasien_t.pegawai_id = dokter.pegawai_id
  WHERE obatalkespasien_t.carabayar_id = 6 AND obatalkespasien_t.resepturdetail_id IS NULL
UNION ALL
 SELECT 'OBAT'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    ruangan_m.instalasi_id,
    obatalkespasien_t.obatalkespasien_id AS tindakanpelayanan_id,
    obatalkes_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    obatalkespasien_t.hargajual_oa AS tarif_tindakan,
    obatalkes_m.obatalkes_nama AS tindakan_obat,
    dokter.nama_pegawai AS dokter,
    obatalkespasien_t.hargajual_oa AS tarif_satuan,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
        CASE
            WHEN groupinacbg_m.groupinacbg_id = 3 THEN true
            ELSE false
        END AS is_konsultasi
   FROM pendaftaran_t
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
     LEFT JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
     LEFT JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m dokter ON obatalkespasien_t.pegawai_id = dokter.pegawai_id
  WHERE obatalkespasien_t.resepturdetail_id IS NOT NULL;");

    $this->execute('ALTER TABLE "public"."infopasienbpjsklaim_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."infopasienbelumbayar_v";');

    $this->execute("
        CREATE VIEW \"public\".\"infopasienbelumbayar_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN cb_1.carabayar_nama
            ELSE cb_2.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pj_1.penjamin_nama
            ELSE pj_2.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN dr_1.nama_pegawai
            ELSE dr_2.nama_pegawai
        END AS nama_dokter,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ins_1.instalasi_nama
            ELSE ins_1.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_1.ruangan_nama
            ELSE ruang_2.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN fgetnamalookup(pendaftaran_t.status_periksa::integer)
            ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
        END AS status_periksa,
    COALESCE(tindakan.total_tindakan::double precision, 0::double precision) + COALESCE(obat.total_obat::double precision, 0::double precision) AS total_tagihan,
    COALESCE(uang_masuk.total_uangmasuk, 0::double precision) AS uang_masuk,
    COALESCE(tindakan.total_tindakan::double precision, 0::double precision) + COALESCE(obat.total_obat::double precision, 0::double precision) - COALESCE(uang_masuk.total_uangmasuk, 0::double precision) AS sisa_tagihan,
    konfigsystem_k.kelola_tagihan,
        CASE
            WHEN (COALESCE(tindakan.total_tindakan::double precision, 0::double precision) + COALESCE(obat.total_obat::double precision, 0::double precision)) >= konfigsystem_k.kelola_tagihan::double precision THEN true
            ELSE false
        END AS is_kelola_tagihan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_1.instalasi_id
            ELSE ruang_2.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_1.ruangan_id
            ELSE ruang_2.ruangan_id
        END AS ruangan_id,
    COALESCE(pendaftaran_t.limit_tagihan, 0::double precision) AS limit_tagihan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_t.nosep
            ELSE bpjs_admisi.nosep
        END AS no_sep,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pasienpulang_id
            ELSE pasienadmisi_t.pasienpulang_id
        END AS pasienpulang_id,
    pendaftaran_t.is_stopakomodasi
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN carabayar_m cb_1 ON pendaftaran_t.carabayar_id = cb_1.carabayar_id
     LEFT JOIN carabayar_m cb_2 ON pasienadmisi_t.carabayar_id = cb_2.carabayar_id
     LEFT JOIN penjamin_m pj_1 ON pendaftaran_t.penjamin_id = pj_1.penjamin_id
     LEFT JOIN penjamin_m pj_2 ON pasienadmisi_t.penjamin_id = pj_2.penjamin_id
     LEFT JOIN pegawai_m dr_1 ON pendaftaran_t.pegawai_id = dr_1.pegawai_id
     LEFT JOIN pegawai_m dr_2 ON pasienadmisi_t.pegawai_id = dr_2.pegawai_id
     LEFT JOIN ruangan_m ruang_1 ON pendaftaran_t.ruangan_id = ruang_1.ruangan_id
     LEFT JOIN ruangan_m ruang_2 ON pasienadmisi_t.ruangan_id = ruang_2.ruangan_id
     LEFT JOIN instalasi_m ins_1 ON ruang_1.instalasi_id = ins_1.instalasi_id
     LEFT JOIN instalasi_m ins_2 ON ruang_2.instalasi_id = dr_2.pegawai_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            sum(tindakanpelayanan_t.tarif_tindakan::integer) AS total_tindakan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted IS FALSE
          GROUP BY tindakanpelayanan_t.pendaftaran_id) tindakan ON pendaftaran_t.pendaftaran_id = tindakan.pendaftaran_id
     LEFT JOIN ( SELECT obatalkespasien_t.pendaftaran_id,
            sum(obatalkespasien_t.hargajual_oa::integer) AS total_obat
           FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted = false
          GROUP BY obatalkespasien_t.pendaftaran_id) obat ON pendaftaran_t.pendaftaran_id = obat.pendaftaran_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar - pembayaran_t.total_kembalian + pembayaran_t.total_dijamin - pembayaran_t.total_administrasi - pembayaran_t.total_pembulatan) AS total_uangmasuk
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
             JOIN pembayaran_t ON pembayaranpelayanan_t_1.pembayaran_id = pembayaran_t.pembayaran_id
          WHERE pembayaranpelayanan_t_1.is_deleted = false AND pembayaran_t.is_deleted = false
          GROUP BY pembayaran_t.pendaftaran_id) uang_masuk ON pendaftaran_t.pendaftaran_id = uang_masuk.pendaftaran_id
     LEFT JOIN konfigsystem_k ON konfigsystem_k.is_deleted = false
     LEFT JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN bpjs_t bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
  WHERE pendaftaran_t.status_bayar = 349;");

    $this->execute('ALTER TABLE "public"."infopasienbelumbayar_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."infopasienbpjs_v";');

    $this->execute("
        CREATE VIEW \"public\".\"infopasienbpjs_v\" AS  SELECT rincian.jenis,
    rincian.pendaftaran_id,
    rincian.tgl_pendaftaran,
    rincian.no_pendaftaran,
    rincian.instalasi_id,
    rincian.instalasi_nama,
    rincian.pasien_id,
    rincian.no_rekam_medik,
    rincian.nama_pasien,
    rincian.carabayar_id,
    rincian.carabayar_nama,
    rincian.penjamin_id,
    rincian.penjamin_nama,
    rincian.ruangan_id,
    rincian.ruangan_nama,
    rincian.jeniskasuspenyakit_id,
    rincian.jeniskasuspenyakit_nama,
    rincian.pegawai_id,
    rincian.dokter_dpjp,
    rincian.status_verifikasi,
    rincian.status_verif,
    rincian.umur,
    rincian.kelaspelayanan_id,
    rincian.kelaspelayanan_nama,
    rincian.jeniskelas_nama,
    rincian.pasienpulang_id,
    rincian.tglpasienpulang,
    rincian.carakeluar_id,
    rincian.carakeluar_nama,
    rincian.nosep,
    rincian.kamarruangan_id,
    rincian.kamarruangan_nokamar,
    rincian.kamartempattidur_id,
    rincian.no_tempattidur,
    rincian.pasienadmisi_id,
    rincian.status_bayar,
    rincian.stat_bayar,
    rincian.total_tagihan,
    rincian.nokartuasuransi,
    rincian.jeniskelamin,
    rincian.tanggal_lahir,
    rincian.carakeluarinacbg_id,
    rincian.carakeluar_value,
    rincian.lama_rawat,
    rincian.urutankelas,
    rincian.pengajuanklaimdetail_id,
    rincian.bpjs_id,
    rincian.kelas_bpjs,
    masukkamar_t.kelaspelayanan_id AS naik_kelas,
    rincian.naik_kelas AS naik_kelas_klaim,
    masukkamar_t.lamadirawat_kamar,
    rincian.jeniskelas_id,
    profilrumahsakit_m.kodetarifbpjs_id,
    rincian.tarif_polieksekutif,
    rincian.is_naikkelas,
    rincian.is_rawatintensif,
    rincian.lama_kelasintensif,
    rincian.ventilator,
    rincian.status_klaim,
    rincian.klaiminacbg_id,
    rincian.jenis_kelasrawat,
    rincian.is_terkirim,
    rincian.alamat_pasien,
    rincian.tgl_stopakomodasi,
    rincian.is_stopakomodasi
   FROM ( SELECT 'RJ-RD'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_dpjp,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verif,
            pendaftaran_t.umur,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            jeniskelas_m.jeniskelas_nama,
            pendaftaran_t.pasienpulang_id,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            bpjs_t.nosep,
            NULL::integer AS kamarruangan_id,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::integer AS kamartempattidur_id,
            NULL::character varying AS no_tempattidur,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.status_bayar,
            fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
            cektagihaninstalasi.total AS total_tagihan,
            bpjs_t.nokartuasuransi,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
            pasien_m.tanggal_lahir,
            carakeluar_m.carakeluarinacbg_id,
            fgetvaluelookup(carakeluar_m.carakeluarinacbg_id) AS carakeluar_value,
            bpjs_t.klsrawat AS kelas_bpjs,
            1 AS lama_rawat,
            kelaspelayanan_m.urutankelas,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pendaftaran_t.bpjs_id,
            jeniskelas_m.jeniskelas_id,
            klaiminacbg_t.tarif_polieksekutif,
            klaiminacbg_t.is_naikkelas,
            klaiminacbg_t.is_rawatintensif,
            klaiminacbg_t.lama_kelasintensif,
            klaiminacbg_t.ventilator,
            klaiminacbg_t.naik_kelas,
            klaiminacbg_t.status_klaim,
            klaiminacbg_t.klaiminacbg_id,
            klaiminacbg_t.jenis_kelasrawat,
            klaiminacbg_t.is_terkirim,
            pasien_m.alamat_pasien,
            pendaftaran_t.tgl_stopakomodasi,
            pendaftaran_t.is_stopakomodasi
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN jeniskelas_m ON kelaspelayanan_m.jeniskelas_id = jeniskelas_m.jeniskelas_id
             JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             JOIN ( SELECT hitung.pendaftaran_id,
                        CASE
                            WHEN hitung.instalasi_id = 3 THEN pendaftaran_t_1.pasienadmisi_id
                            ELSE NULL::integer
                        END AS pasienadmisi_id,
                    hitung.instalasi_id,
                    hitung.instalasi_nama,
                    sum(hitung.tarif) AS total
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            tindakanpelayanan_t.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM tindakanpelayanan_t
                             LEFT JOIN instalasi_m instalasi_m_1 ON tindakanpelayanan_t.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT tindakanpelayanan_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM tindakanpelayanan_t
                             LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                             LEFT JOIN ruangan_m ruangan_m_1 ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM obatalkespasien_t
                             LEFT JOIN ruangan_m ruangan_m_1 ON obatalkespasien_t.ruangan_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE obatalkespasien_t.resepturdetail_id IS NULL
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM obatalkespasien_t
                             LEFT JOIN resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
                             LEFT JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
                             LEFT JOIN ruangan_m ruangan_m_1 ON reseptur_t.ruanganreseptur_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE obatalkespasien_t.resepturdetail_id IS NOT NULL
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama) hitung
                     JOIN pendaftaran_t pendaftaran_t_1 ON hitung.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t_1.pasienadmisi_id, pendaftaran_t_1.instalasi_id) cektagihaninstalasi ON pendaftaran_t.pendaftaran_id = cektagihaninstalasi.pendaftaran_id AND pendaftaran_t.instalasi_id = cektagihaninstalasi.instalasi_id
             LEFT JOIN pengajuanklaimdetail_t ON pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id AND pengajuanklaimdetail_t.is_deleted = false
             LEFT JOIN klaiminacbg_t ON pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id AND klaiminacbg_t.is_deleted = false
          WHERE pendaftaran_t.carabayar_id = 6 AND pendaftaran_t.instalasi_id <> 3
        UNION ALL
         SELECT 'RI'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasienadmisi_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_dpjp,
            pasienadmisi_t.status_verifikasi,
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS status_verif,
            pendaftaran_t.umur,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            jeniskelas_m.jeniskelas_nama,
            pasienadmisi_t.pasienpulang_id,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            bpjs_t.nosep,
            pasienadmisi_t.kamarruangan_id,
            kamarruangan_m.kamarruangan_nokamar,
            pasienadmisi_t.kamartempattidur_id,
            kamartempattidur_m.no_tempattidur,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.status_bayar,
            fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
            cektagihaninstalasi.total AS total_tagihan,
            bpjs_t.nokartuasuransi,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
            pasien_m.tanggal_lahir,
            carakeluar_m.carakeluarinacbg_id,
            fgetvaluelookup(carakeluar_m.carakeluarinacbg_id) AS carakeluar_value,
            bpjs_t.klsrawat AS kelas_bpjs,
            pasienpulang_t.lama_rawat,
            kelaspelayanan_m.urutankelas,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pasienadmisi_t.bpjs_id,
            jeniskelas_m.jeniskelas_id,
            klaiminacbg_t.tarif_polieksekutif,
            klaiminacbg_t.is_naikkelas,
            klaiminacbg_t.is_rawatintensif,
            klaiminacbg_t.lama_kelasintensif,
            klaiminacbg_t.ventilator,
            klaiminacbg_t.naik_kelas,
            klaiminacbg_t.status_klaim,
            klaiminacbg_t.klaiminacbg_id,
            klaiminacbg_t.jenis_kelasrawat,
            klaiminacbg_t.is_terkirim,
            pasien_m.alamat_pasien,
            pendaftaran_t.tgl_stopakomodasi,
            pendaftaran_t.is_stopakomodasi
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN jeniskelas_m ON kelaspelayanan_m.jeniskelas_id = jeniskelas_m.jeniskelas_id
             JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             JOIN ( SELECT hitung.pendaftaran_id,
                        CASE
                            WHEN hitung.instalasi_id = 3 THEN pendaftaran_t_1.pasienadmisi_id
                            ELSE NULL::integer
                        END AS pasienadmisi_id,
                    hitung.instalasi_id,
                    hitung.instalasi_nama,
                    sum(hitung.tarif) AS total
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            tindakanpelayanan_t.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM tindakanpelayanan_t
                             LEFT JOIN instalasi_m instalasi_m_1 ON tindakanpelayanan_t.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT tindakanpelayanan_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM tindakanpelayanan_t
                             LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                             LEFT JOIN ruangan_m ruangan_m_1 ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM obatalkespasien_t
                             LEFT JOIN ruangan_m ruangan_m_1 ON obatalkespasien_t.ruangan_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE obatalkespasien_t.resepturdetail_id IS NULL
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM obatalkespasien_t
                             LEFT JOIN resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
                             LEFT JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
                             LEFT JOIN ruangan_m ruangan_m_1 ON reseptur_t.ruanganreseptur_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE obatalkespasien_t.resepturdetail_id IS NOT NULL
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama) hitung
                     JOIN pendaftaran_t pendaftaran_t_1 ON hitung.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t_1.pasienadmisi_id, pendaftaran_t_1.instalasi_id) cektagihaninstalasi ON pendaftaran_t.pasienadmisi_id = cektagihaninstalasi.pasienadmisi_id AND pendaftaran_t.pendaftaran_id = cektagihaninstalasi.pendaftaran_id
             LEFT JOIN pengajuanklaimdetail_t ON pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id AND pengajuanklaimdetail_t.is_deleted = false
             LEFT JOIN klaiminacbg_t ON pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id AND klaiminacbg_t.is_deleted = false
          WHERE pasienadmisi_t.carabayar_id = 6 AND pendaftaran_t.is_stopakomodasi = true) rincian
     LEFT JOIN masukkamar_t ON rincian.pasienadmisi_id = masukkamar_t.pasienadmisi_id AND masukkamar_t.pindahkamar_id IS NULL
     LEFT JOIN profilrumahsakit_m ON profilrumahsakit_m.is_deleted = false AND profilrumahsakit_m.is_active = true
  GROUP BY rincian.jenis, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.no_pendaftaran, rincian.instalasi_id, rincian.instalasi_nama, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.carabayar_id, rincian.carabayar_nama, rincian.penjamin_id, rincian.penjamin_nama, rincian.ruangan_id, rincian.ruangan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter_dpjp, rincian.status_verifikasi, rincian.status_verif, rincian.umur, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskelas_nama, rincian.pasienpulang_id, rincian.tglpasienpulang, rincian.carakeluar_id, rincian.carakeluar_nama, rincian.nosep, rincian.kamarruangan_id, rincian.kamarruangan_nokamar, rincian.kamartempattidur_id, rincian.no_tempattidur, rincian.pasienadmisi_id, rincian.status_bayar, rincian.stat_bayar, rincian.total_tagihan, rincian.nokartuasuransi, rincian.jeniskelamin, rincian.tanggal_lahir, rincian.carakeluarinacbg_id, rincian.carakeluar_value, rincian.kelas_bpjs, rincian.lama_rawat, rincian.urutankelas, rincian.pengajuanklaimdetail_id, rincian.bpjs_id, masukkamar_t.kelaspelayanan_id, masukkamar_t.lamadirawat_kamar, rincian.jeniskelas_id, profilrumahsakit_m.kodetarifbpjs_id, rincian.tarif_polieksekutif, rincian.is_naikkelas, rincian.is_rawatintensif, rincian.lama_kelasintensif, rincian.ventilator, rincian.naik_kelas, rincian.status_klaim, rincian.klaiminacbg_id, rincian.jenis_kelasrawat, rincian.is_terkirim, rincian.alamat_pasien, rincian.tgl_stopakomodasi, rincian.is_stopakomodasi;");

    $this->execute('ALTER TABLE "public"."infopasienbpjs_v" OWNER TO "postgres";'); 

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210423_073809_migrate_20210423_inacbg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210423_073809_migrate_20210423_inacbg cannot be reverted.\n";

        return false;
    }
    */
}
