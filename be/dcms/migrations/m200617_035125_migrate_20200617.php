<?php

use yii\db\Migration;

/**
 * Class m200617_035125_migrate_20200617
 */
class m200617_035125_migrate_20200617 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."supplier_m" ADD COLUMN "parent_suppliercode" varchar(100) COLLATE "pg_catalog"."default";');
        
        $this->execute('DROP VIEW if exists "public"."worklistresep_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"worklistresep_v\" AS  SELECT penjualanresep_t.penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    array_agg(anamnesa_t.riwayat_alergiobat) AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup((reseptur_t.status_worklist)::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur
   FROM ((((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
  WHERE (ruangan_asal.instalasi_id = 1)
  GROUP BY reseptur_t.noresep, penjualanresep_t.noresep, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, pegawai_m.nama_pegawai, reseptur_t.status_worklist, ruangan_asal.instalasi_id, penjualanresep_t.tglpenjualan, penjualanresep_t.status_bayar, penjualanresep_t.penjualanresep_id, reseptur_t.reseptur_id, penjualanresep_t.status_reseptur
UNION ALL
 SELECT NULL::integer AS penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    NULL::character varying AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    NULL::text[] AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup((reseptur_t.status_worklist)::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    reseptur_t.tglreseptur AS tanggal,
    NULL::smallint AS status_bayar_id,
    NULL::character varying AS status_bayar,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur
   FROM ((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
  WHERE (ruangan_asal.instalasi_id <> 1)
UNION ALL
 SELECT penjualanresep_t.penjualanresep_id,
    NULL::integer AS reseptur_id,
    NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_rm,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN penjualanresep_t.nama_pembeli
            WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN karyawan.nama_pegawai
            ELSE NULL::character varying
        END AS nama_pasien,
    NULL::date AS tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    NULL::text[] AS alergi,
    penjualanresep_t.status_worklist AS status_worklist_id,
    fgetnamalookup((penjualanresep_t.status_worklist)::integer) AS status_worklist,
    NULL::integer AS instalasi_id,
    penjualanresep_t.jenispenjualan AS jenispenjualan_id,
    fgetnamalookup((penjualanresep_t.jenispenjualan)::integer) AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur
   FROM ((((penjualanresep_t
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
  WHERE ((penjualanresep_t.jenispenjualan)::text <> '344'::text);");

        $this->execute('DROP VIEW if exists "public"."riwayattindakan_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"riwayattindakan_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    tindakanpelayanan_t.tindakanpelayanan_id AS id,
    'TINDAKAN'::text AS tipe_pelayanan,
    tindakanpelayanan_t.tgl_tindakan,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat,
    NULL::character varying AS tindakan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan AS jumlah_tarif,
    NULL::double precision AS ditagihkan,
    tindakanpelayanan_t.ruangan_id AS ruangan_pelayanan_id,
    r_tindakan.ruangan_nama AS ruangan_pelayanan,
    peg_1.nama_pegawai AS dokter_pemeriksa,
    peg_2.nama_pegawai AS dokter_delegasi,
    peg_3.nama_pegawai AS perawat_1,
    peg_4.nama_pegawai AS perawat_2,
    daftartindakan_m.daftartindakan_id AS tindakan_obat_id,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    tindakanpelayanan_t.dokterdelegasi_id,
    tindakanpelayanan_t.perawat1_id,
    tindakanpelayanan_t.perawat2_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.kelaspelayanan_id,
    tindakanpelayanan_t.tipepaket_id,
    r_tindakan.instalasi_id AS ins_pelayanan_id,
    ins_tindakan.instalasi_nama AS ins_pelayanan_nama,
    NULL::integer AS daftartindakan_bhmp_id,
    NULL::integer AS pegawai_id,
    NULL::text AS tipepaket_nama,
    tindakanpelayanan_t.tindakansudahbayar_id,
    pendaftaran_t.pasienpulang_id,
    tindakanpelayanan_t.is_deleted AS tindakanbmhp_is_deleted,
    instruksitindakan_t.status_implementasi,
    fgetnamalookup((instruksitindakan_t.status_implementasi)::integer) AS stat_implementasi,
    instruksitindakan_t.instruksitindakan_id,
    paketpelayanan_mp.daftartindakan_namapaket
   FROM ((((((((((((((pendaftaran_t
     JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN ruangan_m r_tindakan ON ((tindakanpelayanan_t.ruangan_id = r_tindakan.ruangan_id)))
     JOIN instalasi_m ins_tindakan ON ((r_tindakan.instalasi_id = ins_tindakan.instalasi_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pegawai_m peg_1 ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = peg_1.pegawai_id)))
     LEFT JOIN pegawai_m peg_2 ON ((tindakanpelayanan_t.dokterdelegasi_id = peg_2.pegawai_id)))
     LEFT JOIN pegawai_m peg_3 ON ((tindakanpelayanan_t.perawat1_id = peg_3.pegawai_id)))
     LEFT JOIN pegawai_m peg_4 ON ((tindakanpelayanan_t.perawat2_id = peg_4.pegawai_id)))
     LEFT JOIN instruksitindakan_t ON ((tindakanpelayanan_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id)))
     LEFT JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
            string_agg((daftartindakan_m_1.daftartindakan_nama)::text, ', '::text) AS daftartindakan_namapaket
           FROM (paketpelayanan_mp paketpelayanan_mp_1
             LEFT JOIN daftartindakan_m daftartindakan_m_1 ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m_1.daftartindakan_id)))
          GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
  WHERE (r_tindakan.instalasi_id = ANY (ARRAY[1, 2, 3]))
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    obatalkespasien_t.obatalkespasien_id AS id,
    'BMHP'::text AS tipe_pelayanan,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    obatalkes_m.obatalkes_nama AS tindakan_obat,
    daftartindakan_m.daftartindakan_nama AS tindakan,
    obatalkespasien_t.qty_oa AS qty,
    NULL::double precision AS tarif_satuan,
    NULL::double precision AS tarifcyto_tindakan,
    obatalkespasien_t.hargajual_oa AS jumlah_tarif,
    obatalkespasien_t.hargajual_oa AS ditagihkan,
    obatalkespasien_t.ruangan_id AS ruangan_pelayanan_id,
    r_obat.ruangan_nama AS ruangan_pelayanan,
    dokter_pemeriksa.nama_pegawai AS dokter_pemeriksa,
    NULL::character varying AS dokter_delegasi,
    peg_3.nama_pegawai AS perawat_1,
    peg_4.nama_pegawai AS perawat_2,
    obatalkes_m.obatalkes_id AS tindakan_obat_id,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    tindakanpelayanan_t.dokterdelegasi_id,
    tindakanpelayanan_t.perawat1_id,
    tindakanpelayanan_t.perawat2_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.kelaspelayanan_id,
    NULL::integer AS tipepaket_id,
    r_obat.instalasi_id AS ins_pelayanan_id,
    instalasi_m.instalasi_nama AS ins_pelayanan_nama,
    obatalkespasien_t.daftartindakan_id AS daftartindakan_bhmp_id,
    obatalkespasien_t.pegawai_id,
    NULL::text AS tipepaket_nama,
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
    pendaftaran_t.pasienpulang_id,
    obatalkespasien_t.is_deleted AS tindakanbmhp_is_deleted,
    instruksitindakanbmhp_t.status_implementasi,
    fgetnamalookup((instruksitindakanbmhp_t.status_implementasi)::integer) AS stat_implementasi,
    instruksitindakanbmhp_t.instruksitindakanbmhp_id AS instruksitindakan_id,
    paketpelayanan_mp.daftartindakan_namapaket
   FROM (((((((((((((((pendaftaran_t
     JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN ruangan_m r_obat ON ((obatalkespasien_t.ruangan_id = r_obat.ruangan_id)))
     JOIN instalasi_m ON ((r_obat.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN tindakanpelayanan_t ON ((obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id)))
     LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pegawai_m peg_3 ON ((obatalkespasien_t.perawat1_id = peg_3.pegawai_id)))
     LEFT JOIN pegawai_m peg_4 ON ((obatalkespasien_t.perawat2_id = peg_4.pegawai_id)))
     LEFT JOIN pegawai_m dokter_pemeriksa ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = dokter_pemeriksa.pegawai_id)))
     LEFT JOIN instruksitindakanbmhp_t ON ((obatalkespasien_t.instruksitindakanbmhp_id = instruksitindakanbmhp_t.instruksitindakanbmhp_id)))
     LEFT JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
            string_agg((daftartindakan_m_1.daftartindakan_nama)::text, ', '::text) AS daftartindakan_namapaket
           FROM (paketpelayanan_mp paketpelayanan_mp_1
             LEFT JOIN daftartindakan_m daftartindakan_m_1 ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m_1.daftartindakan_id)))
          GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    tindakanpelayanan_t.tindakanpelayanan_id AS id,
    'PAKET'::text AS tipe_pelayanan,
    tindakanpelayanan_t.tgl_tindakan,
    tipepaket_m.tipepaket_nama AS tindakan_obat,
    NULL::character varying AS tindakan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan AS jumlah_tarif,
    NULL::double precision AS ditagihkan,
    tindakanpelayanan_t.ruangan_id AS ruangan_pelayanan_id,
    r_tindakan.ruangan_nama AS ruangan_pelayanan,
    peg_1.nama_pegawai AS dokter_pemeriksa,
    peg_2.nama_pegawai AS dokter_delegasi,
    peg_3.nama_pegawai AS perawat_1,
    peg_4.nama_pegawai AS perawat_2,
    tipepaket_m.tipepaket_id AS tindakan_obat_id,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    tindakanpelayanan_t.dokterdelegasi_id,
    tindakanpelayanan_t.perawat1_id,
    tindakanpelayanan_t.perawat2_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.kelaspelayanan_id,
    tindakanpelayanan_t.tipepaket_id,
    r_tindakan.instalasi_id AS ins_pelayanan_id,
    ins_tindakan.instalasi_nama AS ins_pelayanan_nama,
    NULL::integer AS daftartindakan_bhmp_id,
    NULL::integer AS pegawai_id,
    tipepaket_m.tipepaket_nama,
    tindakanpelayanan_t.tindakansudahbayar_id,
    pendaftaran_t.pasienpulang_id,
    tindakanpelayanan_t.is_deleted AS tindakanbmhp_is_deleted,
    instruksitindakan_t.status_implementasi,
    fgetnamalookup((instruksitindakan_t.status_implementasi)::integer) AS stat_implementasi,
    instruksitindakan_t.instruksitindakan_id,
    paketpelayanan_mp.daftartindakan_namapaket
   FROM ((((((((((((((pendaftaran_t
     JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN ruangan_m r_tindakan ON ((tindakanpelayanan_t.ruangan_id = r_tindakan.ruangan_id)))
     JOIN instalasi_m ins_tindakan ON ((r_tindakan.instalasi_id = ins_tindakan.instalasi_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pegawai_m peg_1 ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = peg_1.pegawai_id)))
     LEFT JOIN pegawai_m peg_2 ON ((tindakanpelayanan_t.dokterdelegasi_id = peg_2.pegawai_id)))
     LEFT JOIN pegawai_m peg_3 ON ((tindakanpelayanan_t.perawat1_id = peg_3.pegawai_id)))
     LEFT JOIN pegawai_m peg_4 ON ((tindakanpelayanan_t.perawat2_id = peg_4.pegawai_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     LEFT JOIN instruksitindakan_t ON ((tindakanpelayanan_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id)))
     LEFT JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
            string_agg((daftartindakan_m.daftartindakan_nama)::text, ', '::text) AS daftartindakan_namapaket
           FROM (paketpelayanan_mp paketpelayanan_mp_1
             LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
          GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
  WHERE (r_tindakan.instalasi_id = ANY (ARRAY[1, 2, 3]));");

        $this->execute('DROP VIEW if exists "public"."inforeseptur_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"inforeseptur_v\" AS  SELECT reseptur_t.reseptur_id,
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
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    ruangan_reseptur.ruangan_nama AS ruangan_reseptur,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
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
    string_agg((resepturdetail_t.racikan_id)::text, '-'::text) AS antrian_racikan,
    penjualanresep_t.catatan,
    resepturdetail_t.iter,
    penjualanresep_t.noresep AS noresep_penjualan,
    resepturdetail_t.iter AS iter_penjualan,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
            ELSE asesmenawal_t.nama_alergi
        END AS riwayat_alergi,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
            WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
            ELSE NULL::text
        END AS diagnosa_text,
    sum(resepturdetail_t.hargajual_reseptur) AS total_tagihan,
    pendaftaran_t.kelaspelayanan_id,
    reseptur_t.status_worklist
   FROM (((((((((((((((((((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN ruangan_m ruangan_reseptur ON ((reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m instalasi_reseptur ON ((ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id)))
     JOIN instalasi_m instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
     JOIN resepturdetail_t ON (((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id) AND (resepturdetail_t.is_deleted = false))))
     JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN antrian_t ON ((reseptur_t.antrian_id = antrian_t.antrian_id)))
     LEFT JOIN diagnosa_m ON ((reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN penjualanresep_t ON ((reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN ( SELECT resepturdetail_t_1.reseptur_id,
            resepturdetail_t_1.iter
           FROM resepturdetail_t resepturdetail_t_1
          WHERE (resepturdetail_t_1.is_deleted = false)
          GROUP BY resepturdetail_t_1.reseptur_id, resepturdetail_t_1.iter) iter ON ((iter.reseptur_id = reseptur_t.reseptur_id)))
     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
     LEFT JOIN asesmenperawatrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id)))
     LEFT JOIN asesmenawal_t ON ((pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id)))
     LEFT JOIN pasienmorbiditas_t ON (((pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false) AND (pasienmorbiditas_t.kelompokdiagnosa_id = 2))))
     LEFT JOIN ( SELECT instruksi_t.instruksi_id,
            cppt_t.cppt_id,
            cppt_t.pendaftaran_id,
            (cppt_t.a_diag_utama ->> 'text'::text) AS diagnosa_utama
           FROM (instruksi_t
             JOIN cppt_t ON (((instruksi_t.cppt_id = cppt_t.cppt_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true))))
          WHERE ((instruksi_t.is_deleted = false) AND (instruksi_t.is_active = true))) cppt_rd ON (((pendaftaran_t.pendaftaran_id = cppt_rd.pendaftaran_id) AND (reseptur_t.instruksi_id = cppt_rd.instruksi_id))))
  WHERE ((reseptur_t.is_deleted = false) AND (reseptur_t.is_active = true))
  GROUP BY reseptur_t.instruksi_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.umur, pasien_m.tanggal_lahir, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), diagnosa_m.diagnosa_namalainnya, reseptur_t.reseptur_id, reseptur_t.pasien_id, reseptur_t.pendaftaran_id, reseptur_t.pasienadmisi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, reseptur_t.ruangan_id, reseptur_t.ruanganreseptur_id, reseptur_t.tglreseptur, reseptur_t.noresep, reseptur_t.penjualanresep_id, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_tujuan.ruangan_nama, ruangan_reseptur.ruangan_nama, reseptur_t.status_reseptur, reseptur_t.pegawai_id, pegawai_m.nama_pegawai, ruangan_reseptur.instalasi_id, instalasi_reseptur.instalasi_nama, ruangan_tujuan.instalasi_id, instalasi_tujuan.instalasi_nama, antrian_t.no_antrian, reseptur_t.is_hamil, reseptur_t.berat_badan, reseptur_t.tinggi_badan, reseptur_t.luas_tubuh, reseptur_t.diagnosa_id, penjualanresep_t.catatan, resepturdetail_t.iter, penjualanresep_t.noresep,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
            ELSE asesmenawal_t.nama_alergi
        END,
        CASE
            WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
            WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
            WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
            ELSE NULL::text
        END, (concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama)), pendaftaran_t.kelaspelayanan_id, reseptur_t.status_worklist;");
        
        $this->execute('DROP VIEW if exists "public"."worklistresepdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"worklistresepdetail_v\" AS  SELECT reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    signaobat_m.signa_nama AS signa,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket
   FROM (((((((reseptur_t
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
     JOIN resepturdetail_t ON ((obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id)))
  WHERE (ruangan_asal.instalasi_id = 1)
UNION ALL
 SELECT reseptur_t.noresep AS no_reseptur,
    NULL::text AS no_resep,
    racikan_m.racikan_nama AS racikan,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    signaobat_m.signa_nama AS signa,
    resepturdetail_t.qty_reseptur AS qty_obat,
    resepturdetail_t.qty_konversi,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    resepturdetail_t.etiket
   FROM (((((reseptur_t
     JOIN resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
     JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
  WHERE (ruangan_asal.instalasi_id <> 1)
UNION ALL
 SELECT NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    signaobat_m.signa_nama AS signa,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket
   FROM ((((penjualanresep_t
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
  WHERE ((penjualanresep_t.jenispenjualan)::text <> '344'::text);");

        $this->execute('CREATE TABLE "public"."returtagihan_r" (
  "returtagihan_id" serial8,
  "pembayaran_id" int4,
  "no_tagihan" varchar(255) COLLATE "pg_catalog"."default",
  "pegawaipembayaran_id" int4,
  "pegawairetur_id" int4,
  "tgl_returtagihan" timestamp(6) DEFAULT (\'now\'::text)::date,
  "total_returtagihan" float8 DEFAULT 0,
  "keterangan" text COLLATE "pg_catalog"."default",
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "returtagihan_r_pkey" PRIMARY KEY ("returtagihan_id")
)
;');
        
        $this->execute('CREATE TABLE "public"."returtagihandetail_r" (
  "returtagihandetail_id" serial8,
  "returtagihan_id" int4,
  "tindakanpelayanan_id" int4,
  "obatalkespasien_id" int4,
  "nama_tagihan" varchar(255) COLLATE "pg_catalog"."default",
  "qty_tagihan" int4 DEFAULT 0,
  "tarif_tagihan" float8 DEFAULT 0,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "returtagihandetail_t_pkey" PRIMARY KEY ("returtagihandetail_id")
)
;');

        $this->execute("
            CREATE VIEW \"public\".\"infopermintaanbmhp_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    (to_char(obatalkespasien_t.tglpelayanan, 'YYYY-MM-DD'::text))::date AS tgl_permintaan,
    pasien_m.nama_pasien,
    obatalkespasien_t.ruangan_id AS ruangan_tujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
        END AS ruangan_asal_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_asal_1.ruangan_nama
            ELSE ruangan_asal_2.ruangan_nama
        END AS ruangan_asal,
    obatalkespasien_t.status_bmhp AS status_bmhp_id,
    fgetnamalookup((obatalkespasien_t.status_bmhp)::integer) AS status_bmhp
   FROM ((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ruangan_tujuan ON ((obatalkespasien_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_asal_1 ON ((pendaftaran_t.ruangan_id = ruangan_asal_1.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_asal_2 ON ((pasienadmisi_t.ruangan_id = ruangan_asal_2.ruangan_id)))
  WHERE ((obatalkespasien_t.penjualanresep_id IS NULL) AND (obatalkespasien_t.is_deleted = false) AND (ruangan_tujuan.instalasi_id = 6))
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, (to_char(obatalkespasien_t.tglpelayanan, 'YYYY-MM-DD'::text))::date, pasien_m.nama_pasien, obatalkespasien_t.ruangan_id, ruangan_tujuan.ruangan_nama, obatalkespasien_t.status_bmhp, pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id, ruangan_asal_1.ruangan_nama, ruangan_asal_2.ruangan_nama;");
        
        $this->execute("
            CREATE VIEW \"public\".\"infopermintaanbmhpdetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi
   FROM ((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN ruangan_m ruangan_tujuan ON ((obatalkespasien_t.ruangan_id = ruangan_tujuan.ruangan_id)))
  WHERE ((obatalkespasien_t.penjualanresep_id IS NULL) AND (obatalkespasien_t.is_deleted = false) AND (ruangan_tujuan.instalasi_id = 6));");
        
        $this->execute("
            CREATE VIEW \"public\".\"historireturtagihan_v\" AS  SELECT returtagihan_r.pembayaran_id,
    returtagihan_r.no_tagihan,
    pasien_m.no_rekam_medik,
    peg_pembayaran.nama_pegawai AS pegawai_pembayaran,
    peg_retur.nama_pegawai AS pegawai_retur,
    returtagihan_r.tgl_returtagihan,
    returtagihan_r.total_returtagihan,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT returtagihandetail_r.returtagihandetail_id,
                    returtagihandetail_r.returtagihan_id,
                    returtagihandetail_r.tindakanpelayanan_id,
                    returtagihandetail_r.obatalkespasien_id,
                    returtagihandetail_r.nama_tagihan,
                    returtagihandetail_r.qty_tagihan,
                    returtagihandetail_r.tarif_tagihan,
                    returtagihandetail_r.additional_data,
                    returtagihandetail_r.created_date,
                    returtagihandetail_r.created_by,
                    returtagihandetail_r.modified_count,
                    returtagihandetail_r.last_modified_date,
                    returtagihandetail_r.last_modified_by,
                    returtagihandetail_r.is_deleted,
                    returtagihandetail_r.is_active,
                    returtagihandetail_r.deleted_date,
                    returtagihandetail_r.deleted_by
                   FROM returtagihandetail_r
                  WHERE (returtagihandetail_r.returtagihan_id = returtagihan_r.returtagihan_id)) d2) AS detail_tagihan
   FROM (((((returtagihan_r
     JOIN pembayaran_t ON ((returtagihan_r.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN pendaftaran_t ON ((pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m peg_pembayaran ON ((returtagihan_r.pegawaipembayaran_id = peg_pembayaran.pegawai_id)))
     JOIN pegawai_m peg_retur ON ((returtagihan_r.pegawairetur_id = peg_retur.pegawai_id)));");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200617_035125_migrate_20200617 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200617_035125_migrate_20200617 cannot be reverted.\n";

        return false;
    }
    */
}
