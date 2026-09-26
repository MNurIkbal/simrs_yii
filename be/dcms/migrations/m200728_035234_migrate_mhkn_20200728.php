<?php

use yii\db\Migration;

/**
 * Class m200728_035234_migrate_mhkn_20200728
 */
class m200728_035234_migrate_mhkn_20200728 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id=67;');

        $this->execute("INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES (67, 'Purchase Request', 'PR', NULL, '0', '0');");

        $this->execute('DELETE FROM lookup_m WHERE lookup_id IN (713,712)');

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(713, 'status_purchaserequest', 'Sudah PO', 'Sudah PO', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(712, 'status_purchaserequest', 'Belum PO', 'Belum PO', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");

        $this->execute('DROP VIEW IF exists "public"."infotindakanpenatajasa_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infotindakanpenatajasa_v\" AS  SELECT 'tindakan'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.is_penatajasa,
        CASE COALESCE(tindakansudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS obatalkes_id,
    NULL::character varying AS obatalkes_nama,
    NULL::double precision AS qty_oa,
    NULL::double precision AS hargajual_oa,
    NULL::integer AS satuanobat_id,
    NULL::character varying AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang
   FROM ((((((((tindakanpelayanan_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienmasukpenunjang_t ON (((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (pasienmasukpenunjang_t.is_deleted = false))))
  WHERE (tindakanpelayanan_t.is_deleted = false)
UNION ALL
 SELECT 'paket'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    tipepaket_m.tipepaket_id AS daftartindakan_id,
    tipepaket_m.tipepaket_nama AS daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.is_penatajasa,
        CASE COALESCE(tindakansudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang
   FROM (((((((((((tindakanpelayanan_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id)))
     LEFT JOIN obatalkespasien_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = obatalkespasien_t.tindakanpelayanan_id) AND (obatalkespasien_t.is_deleted = false))))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienmasukpenunjang_t ON (((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (pasienmasukpenunjang_t.is_deleted = false))))
  WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
UNION ALL
 SELECT 'obat'::text AS jenis,
    NULL::integer AS tindakanpelayanan_id,
    obatalkespasien_t.pendaftaran_id,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    obatalkespasien_t.ruangan_id,
    ruangan_m.ruangan_nama,
    obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    obatalkespasien_t.qty_oa AS qty_tindakan,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarifcyto_tindakan,
    obatalkespasien_t.hargajual_oa AS tarif_tindakan,
    obatalkespasien_t.is_penatajasa,
        CASE COALESCE(obatsudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    obatalkespasien_t.is_deleted,
    NULL::text AS keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    NULL::integer AS kelaspelayanan_id,
    NULL::character varying AS kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_masukpenunjang
   FROM (((((((((obatalkespasien_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT obatsudahbayar_t.obatalkespasien_id,
            count(*) AS telahbayar
           FROM obatsudahbayar_t
          GROUP BY obatsudahbayar_t.obatsudahbayar_id) obatsudahbayar ON ((obatalkespasien_t.obatalkespasien_id = obatsudahbayar.obatalkespasien_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN tindakanpelayanan_t ON (((obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id) AND (tindakanpelayanan_t.is_deleted = false))))
     LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
  WHERE (obatalkespasien_t.is_deleted IS FALSE);");

        $this->execute('DROP VIEW if exists "public"."mastertariftindakan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"mastertariftindakan_v\" AS  SELECT 'TINDAKAN'::text AS jenis_tindakan_paket,
    tariftindakan_m.tariftindakan_id,
    tariftindakan_m.daftartindakan_id AS tindakan_paket_id,
    daftartindakan_m.daftartindakan_nama AS nama_tindakan_paket,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.is_active,
    tariftindakan_m.komponentarif_id,
    daftartindakan_m.daftartindakan_kode,
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    NULL::character varying AS kelompok_nama,
    NULL::text AS ruangan_nama,
    NULL::text AS instalasi_nama,
    tariftindakan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    komponentarif_m.komponentarif_kode
   FROM (((((((tariftindakan_m
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
     LEFT JOIN kamarruangan_m ON ((tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE (tariftindakan_m.is_deleted = false)
UNION ALL
 SELECT 'PAKET'::text AS jenis_tindakan_paket,
    tariftindakan_m.tariftindakan_id,
    tariftindakan_m.tipepaket_id AS tindakan_paket_id,
    tipepaket_m.tipepaket_nama AS nama_tindakan_paket,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.is_active,
    tariftindakan_m.komponentarif_id,
    tipepaket_m.tipepaket_kode AS daftartindakan_kode,
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    NULL::integer AS daftartindakan_id,
    NULL::character varying AS daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    NULL::character varying AS kelompok_nama,
    NULL::text AS ruangan_nama,
    NULL::text AS instalasi_nama,
    NULL::integer AS kamarruangan_id,
    NULL::character varying AS kamar,
    komponentarif_m.komponentarif_kode
   FROM ((((((tariftindakan_m
     JOIN tipepaket_m ON ((tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
  WHERE ((tariftindakan_m.is_deleted = false) AND (tariftindakan_m.tarifparent_id IS NULL))
UNION ALL
 SELECT 'PAKET'::text AS jenis_tindakan_paket,
    tariftindakan_m.tariftindakan_id,
    tariftindakan_m.daftartindakan_id AS tindakan_paket_id,
    daftartindakan_m.daftartindakan_nama AS nama_tindakan_paket,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.is_active,
    tariftindakan_m.komponentarif_id,
    daftartindakan_m.daftartindakan_kode,
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    kelompoktindakan_m.kelompoktindakan_nama AS kelompok_nama,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    NULL::integer AS kamarruangan_id,
    NULL::character varying AS kamar,
    komponentarif_m.komponentarif_kode
   FROM (((((((((((tariftindakan_m
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN kelompoktindakan_m ON ((kelompoktindakan_m.kelompoktindakan_id = daftartindakan_m.kelompoktindakan_id)))
     JOIN tipepaket_m ON ((tipepaket_m.tipepaket_id = tariftindakan_m.tipepaket_id)))
     JOIN paketpelayanan_mp ON (((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false) AND (daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id))))
     LEFT JOIN ruangan_m ON ((ruangan_m.ruangan_id = paketpelayanan_mp.ruangan_id)))
     LEFT JOIN instalasi_m ON ((instalasi_m.instalasi_id = ruangan_m.instalasi_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
  WHERE ((tariftindakan_m.is_deleted = false) AND (tariftindakan_m.tarifparent_id IS NOT NULL));");

        $this->execute('DROP VIEW if exists "public"."infodatapendaftaran_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infodatapendaftaran_v\" AS  SELECT data_info.pendaftaran_id,
    data_info.instalasi_id AS ins_id,
    data_info.ruangan_id AS rua_id,
    data_info.pasien_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS pen_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS car_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.kelaspelayanan_id
            ELSE data_info.kelaspelayananri_id
        END AS kelaspelayanan_id,
    data_info.pasienpulang_id,
    data_info.no_pendaftaran,
    data_info.tgl_pendaftaran,
    data_info.no_rekam_medik,
    data_info.nama_pasien,
    data_info.no_mobile_pasien,
    data_info.instalasi_nama AS ins_nama,
    data_info.ruangan_nama AS rua_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS car,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS pen,
    data_info.kelaspelayanan_nama,
    data_info.jumlah_uangmuka,
    data_info.pasienpulangri_id,
    data_info.pasienadmisi_id,
    data_info.status_pasien,
    data_info.pasienmasukpenunjang_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.tglpasienpulang
            WHEN ((data_info.pasienadmisi_id IS NOT NULL) AND (data_info.tglpasienpulang_ri IS NULL)) THEN data_info.tgl_stopakomodasi
            ELSE data_info.tglpasienpulang_ri
        END AS tglpasienpulang,
    data_info.dokterrj_id,
    data_info.nama_dok_rj_rd,
    data_info.dokterri_id,
    data_info.nama_dok_ri,
    data_info.jeniskasuspenyakit_nama,
    data_info.umur,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS carabayar_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS penjamin_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS carabayar_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS penjamin_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.instalasi_id
            ELSE data_info.instalasiri_id
        END AS instalasi_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.ruangan_id
            ELSE data_info.ruanganri_id
        END AS ruangan_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.instalasi_nama
            ELSE data_info.instalasi_nama_ri
        END AS instalasi_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.ruangan_nama
            ELSE data_info.ruangan_nama_ri
        END AS ruangan_nama,
    data_info.status_bayar,
    data_info.jeniskasuspenyakit_id,
    data_info.tanggal_lahir,
    data_info.penjualanresep_id,
    data_info.jasa,
    data_info.administrasi,
    data_info.obat,
    data_info.totalharga_jual,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.kelas_bpjspendaftaran
            ELSE data_info.kelas_bpjsadmisi
        END AS hak_kelas,
        CASE
            WHEN ((data_info.pasienadmisi_id IS NULL) AND (data_info.bpjs_idpendaftaran IS NOT NULL)) THEN data_info.no_bpjspendaftaran
            WHEN ((data_info.pasienadmisi_id IS NOT NULL) AND (data_info.bpjs_idadmisi IS NOT NULL)) THEN data_info.no_bpjsadmisi
            WHEN ((data_info.pasienadmisi_id IS NULL) AND (data_info.bpjs_idadmisi IS NULL)) THEN data_info.no_asuransipendaftaran
            WHEN ((data_info.pasienadmisi_id IS NOT NULL) AND (data_info.bpjs_idadmisi IS NULL)) THEN data_info.no_asuransiadmisi
            ELSE NULL::character varying
        END AS no_kartu,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.groupcarabayar_pendaftaran
            ELSE data_info.groupcarabayar_admisi
        END AS group_carabayar,
    data_info.total_piutang,
    data_info.keadaanmasuk_id,
    data_info.keadaan_masuk,
    data_info.transportasi_id,
    data_info.transportasi,
    data_info.keterangan_pendaftaran,
    (data_info.tagihan_belumbayar)::integer AS tagihan_belumbayar,
    (data_info.sisa_penunjang)::integer AS sisa_penunjang,
    (data_info.sisa_karcis)::integer AS sisa_karcis,
    (data_info.sisa_obat)::integer AS sisa_obat,
    data_info.status_periksa,
    data_info.tinggi_badan,
    data_info.berat_badan
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.kelaspelayanan_id,
            pasienadmisi_t.kelaspelayanan_id AS kelaspelayananri_id,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.no_mobile_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelaspelayanan_m.kelaspelayanan_nama
                    ELSE kelaspelayanan_ri.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            pasienpulang_t.tglpasienpulang,
            pulang_ri.tglpasienpulang AS tglpasienpulang_ri,
            ((COALESCE(bayaruangmuka_t.jumlah_uangmuka, (0)::double precision) - COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, (0)::double precision)) - COALESCE(pengembalianuangmuka_t.total_pengembalian, (0)::double precision)) AS jumlah_uangmuka,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t.status_pasien,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            dok_rj_rd.nama_pegawai AS nama_dok_rj_rd,
            dok_ri.nama_pegawai AS nama_dok_ri,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.umur,
            pasienadmisi_t.carabayar_id AS carabayarri_id,
            carabayar_ri.carabayar_nama AS carabayar_nama_ri,
            pasienadmisi_t.penjamin_id AS penjaminri_id,
            penjamin_ri.penjamin_nama AS penjamin_nama_ri,
            pasienadmisi_t.ruangan_id AS ruanganri_id,
            ruang_ri.instalasi_id AS instalasiri_id,
            ruang_ri.ruangan_nama AS ruangan_nama_ri,
            ins_ri.instalasi_nama AS instalasi_nama_ri,
            pendaftaran_t.status_bayar,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            NULL::integer AS penjualanresep_id,
            0 AS jasa,
                CASE
                    WHEN (penjualan_resep.biaya_adm IS NULL) THEN (0)::double precision
                    ELSE penjualan_resep.biaya_adm
                END AS administrasi,
            0 AS obat,
            0 AS totalharga_jual,
            bpjs_pendaftaran.klsrawat AS kelas_bpjspendaftaran,
            bpjs_admisi.klsrawat AS kelas_bpjsadmisi,
            bpjs_pendaftaran.bpjs_id AS bpjs_idpendaftaran,
            bpjs_admisi.bpjs_id AS bpjs_idadmisi,
            bpjs_pendaftaran.nokartuasuransi AS no_bpjspendaftaran,
            bpjs_admisi.nokartuasuransi AS no_bpjsadmisi,
            asuransi_pendaftaran.nokartuasuransi AS no_asuransipendaftaran,
            asuransi_admisi.nokartuasuransi AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_ri.groupcarabayar_id AS groupcarabayar_admisi,
            COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision) AS total_piutang,
            pendaftaran_t.pegawai_id AS dokterrj_id,
            pasienadmisi_t.pegawai_id AS dokterri_id,
            pendaftaran_t.keadaan_masuk AS keadaanmasuk_id,
            fgetnamalookup((pendaftaran_t.keadaan_masuk)::integer) AS keadaan_masuk,
            pendaftaran_t.transportasi AS transportasi_id,
            fgetnamalookup((pendaftaran_t.transportasi)::integer) AS transportasi,
            pendaftaran_t.keterangan_pendaftaran,
            COALESCE(belum_bayar.total_tagihan, (0)::double precision) AS tagihan_belumbayar,
            COALESCE(sisa_penunjang.total_tagihan, (0)::double precision) AS sisa_penunjang,
            COALESCE(sisa_karcis.total_tagihan, (0)::double precision) AS sisa_karcis,
            COALESCE(sisa_obat.total_tagihan, (0)::double precision) AS sisa_obat,
            pendaftaran_t.status_periksa,
                CASE
                    WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN periksa_fisik_rj.tinggi
                    WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (periksa_fisik_rd.tinggi)::double precision
                    WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (periksa_fisik_ri.tinggi)::double precision
                    ELSE NULL::double precision
                END AS tinggi_badan,
                CASE
                    WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN periksa_fisik_rj.berat
                    WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (periksa_fisik_rd.berat)::double precision
                    WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (periksa_fisik_ri.berat)::double precision
                    ELSE NULL::double precision
                END AS berat_badan,
            pendaftaran_t.tgl_stopakomodasi
           FROM ((((((((((((((((((((((((((((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
             JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN ruangan_m ruang_ri ON ((pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id)))
             LEFT JOIN instalasi_m ins_ri ON ((ruang_ri.instalasi_id = ins_ri.instalasi_id)))
             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
             LEFT JOIN carabayar_m carabayar_ri ON ((pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id)))
             LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
             LEFT JOIN kelaspelayanan_m kelaspelayanan_ri ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
             LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
             LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
                    sum(bayaruangmuka_t_1.jumlah_uangmuka) AS jumlah_uangmuka
                   FROM bayaruangmuka_t bayaruangmuka_t_1
                  WHERE (bayaruangmuka_t_1.is_deleted = false)
                  GROUP BY bayaruangmuka_t_1.pendaftaran_id) bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
             LEFT JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT pengembalianuangmuka_t_1.pendaftaran_id,
                    sum(pengembalianuangmuka_t_1.total_pengembalian) AS total_pengembalian
                   FROM pengembalianuangmuka_t pengembalianuangmuka_t_1
                  WHERE (pengembalianuangmuka_t_1.is_deleted = false)
                  GROUP BY pengembalianuangmuka_t_1.pendaftaran_id) pengembalianuangmuka_t ON ((pendaftaran_t.pendaftaran_id = pengembalianuangmuka_t.pendaftaran_id)))
             LEFT JOIN ( SELECT pemakaianuangmuka_t_1.pendaftaran_id,
                    sum(pemakaianuangmuka_t_1.pemakaian_uangmuka) AS pemakaian_uangmuka
                   FROM pemakaianuangmuka_t pemakaianuangmuka_t_1
                  WHERE (pemakaianuangmuka_t_1.is_deleted = false)
                  GROUP BY pemakaianuangmuka_t_1.pendaftaran_id) pemakaianuangmuka_t ON ((pendaftaran_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
             LEFT JOIN pegawai_m dok_rj_rd ON ((pendaftaran_t.pegawai_id = dok_rj_rd.pegawai_id)))
             LEFT JOIN pegawai_m dok_ri ON ((pasienadmisi_t.pegawai_id = dok_ri.pegawai_id)))
             LEFT JOIN bpjs_t bpjs_pendaftaran ON ((pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id)))
             LEFT JOIN bpjs_t bpjs_admisi ON ((pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id)))
             LEFT JOIN asuransipasien_m asuransi_pendaftaran ON ((pendaftaran_t.asuransipasien_id = asuransi_pendaftaran.asuransipasien_id)))
             LEFT JOIN asuransipasien_m asuransi_admisi ON ((pasienadmisi_t.asuransipasien_id = asuransi_admisi.asuransipasien_id)))
             LEFT JOIN pemberianpiutang_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT sum(pt.biayaadministrasi) AS biaya_adm,
                    pt.pendaftaran_id
                   FROM penjualanresep_t pt
                  WHERE ((pt.status_bayar = 349) AND (pt.is_deleted = false))
                  GROUP BY pt.pendaftaran_id) penjualan_resep ON ((penjualan_resep.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                           FROM tindakanpelayanan_t
                          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL))
                          GROUP BY tindakanpelayanan_t.pendaftaran_id
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            sum(obatalkespasien_t.hargajual_oa) AS tagihan
                           FROM obatalkespasien_t
                          WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                          GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) belum_bayar ON ((pendaftaran_t.pendaftaran_id = belum_bayar.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                           FROM (tindakanpelayanan_t
                             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL) AND (daftartindakan_m.kelompoktindakan_id <> 17))
                          GROUP BY tindakanpelayanan_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) sisa_penunjang ON ((pendaftaran_t.pendaftaran_id = sisa_penunjang.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                           FROM (tindakanpelayanan_t
                             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (daftartindakan_m.kelompoktindakan_id = 17))
                          GROUP BY tindakanpelayanan_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) sisa_karcis ON ((pendaftaran_t.pendaftaran_id = sisa_karcis.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT obatalkespasien_t.pendaftaran_id,
                            sum(obatalkespasien_t.hargajual_oa) AS tagihan
                           FROM obatalkespasien_t
                          WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                          GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) sisa_obat ON ((pendaftaran_t.pendaftaran_id = sisa_obat.pendaftaran_id)))
             LEFT JOIN ( SELECT pemeriksaanfisik_t.pendaftaran_id,
                    pemeriksaanfisik_t.tinggibadan_cm AS tinggi,
                    pemeriksaanfisik_t.beratbadan_kg AS berat
                   FROM pemeriksaanfisik_t
                  WHERE (pemeriksaanfisik_t.is_deleted = false)) periksa_fisik_rj ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id)))
             LEFT JOIN ( SELECT asesmenperawatrd_t.pendaftaran_id,
                    asesmenperawatrd_t.tinggi_badan AS tinggi,
                    asesmenperawatrd_t.berat_badan AS berat
                   FROM asesmenperawatrd_t
                  WHERE (asesmenperawatrd_t.is_deleted = false)) periksa_fisik_rd ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id)))
             LEFT JOIN ( SELECT asesmenmedis_t.pendaftaran_id,
                    asesmenmedis_t.tinggi_badan AS tinggi,
                    asesmenmedis_t.berat_badan AS berat
                   FROM asesmenmedis_t
                  WHERE (asesmenmedis_t.is_deleted = false)) periksa_fisik_ri ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id)))
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.instalasi_id, pendaftaran_t.ruangan_id, pendaftaran_t.pasien_id, pendaftaran_t.penjamin_id, pendaftaran_t.carabayar_id, pendaftaran_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.pasienpulang_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.no_mobile_pasien, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pasienpulang_t.tglpasienpulang, pasienadmisi_t.pasienpulang_id, pasienadmisi_t.pasienadmisi_id, pasienmasukpenunjang_t.pasienmasukpenunjang_id, pendaftaran_t.status_pasien, pulang_ri.tglpasienpulang, dok_rj_rd.nama_pegawai, dok_ri.nama_pegawai, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pasienadmisi_t.carabayar_id, carabayar_ri.carabayar_nama, pasienadmisi_t.penjamin_id, penjamin_ri.penjamin_nama, pasienadmisi_t.ruangan_id, ruang_ri.instalasi_id, ruang_ri.ruangan_nama, ins_ri.instalasi_nama, bayaruangmuka_t.jumlah_uangmuka, pemakaianuangmuka_t.pemakaian_uangmuka, pengembalianuangmuka_t.total_pengembalian, pendaftaran_t.status_bayar, jeniskasuspenyakit_m.jeniskasuspenyakit_id, pasien_m.tanggal_lahir, bpjs_pendaftaran.klsrawat, bpjs_admisi.klsrawat, bpjs_pendaftaran.bpjs_id, bpjs_admisi.bpjs_id, bpjs_pendaftaran.nokartuasuransi, bpjs_admisi.nokartuasuransi, asuransi_pendaftaran.nokartuasuransi, asuransi_admisi.nokartuasuransi, kelaspelayanan_ri.kelaspelayanan_nama, carabayar_m.groupcarabayar_id, carabayar_ri.groupcarabayar_id, pemberianpiutang_t.total_piutang, pendaftaran_t.keadaan_masuk, pendaftaran_t.transportasi, pendaftaran_t.keterangan_pendaftaran, penjualan_resep.biaya_adm, belum_bayar.total_tagihan, sisa_penunjang.total_tagihan, sisa_karcis.total_tagihan, COALESCE(sisa_obat.total_tagihan, (0)::double precision), pendaftaran_t.status_periksa, periksa_fisik_rj.tinggi, periksa_fisik_rj.berat, periksa_fisik_rd.tinggi, periksa_fisik_rd.berat, periksa_fisik_ri.tinggi, periksa_fisik_ri.berat
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            ruangan_m.instalasi_id,
            penjualanresep_t.ruangan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.penjamin_id,
            penjualanresep_t.carabayar_id,
            penjualanresep_t.kelaspelayanan_id,
            penjualanresep_t.kelaspelayanan_id AS kelaspelayananri_id,
            0 AS pasienpulang_id,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            penjualanresep_t.nama_pembeli AS nama_pasien,
            pasien_m.no_mobile_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            NULL::character varying AS kelaspelayanan_nama,
            penjualanresep_t.tglresep AS tglpasienpulang,
            penjualanresep_t.tglresep AS tglpasienpulang_ri,
            0 AS jumlah_uangmuka,
            0 AS pasienpulangri_id,
            0 AS pasienadmisi_id,
            NULL::character varying AS status_pasien,
            0 AS pasienmasukpenunjang_id,
            pegawai_m.nama_pegawai AS nama_dok_rj_rd,
            pegawai_m.nama_pegawai AS nama_dok_ri,
            NULL::character varying AS jeniskasuspenyakit_nama,
            NULL::character varying AS umur,
            penjualanresep_t.carabayar_id AS carabayarri_id,
            carabayar_m.carabayar_nama AS carabayar_nama_ri,
            penjualanresep_t.penjamin_id AS penjaminri_id,
            penjamin_m.penjamin_nama AS penjamin_nama_ri,
            penjualanresep_t.ruangan_id AS ruanganri_id,
            ruangan_m.instalasi_id AS instalasiri_id,
            ruangan_m.ruangan_nama AS ruangan_nama_ri,
            instalasi_m.instalasi_nama AS instalasi_nama_ri,
            penjualanresep_t.status_bayar,
            0 AS jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            penjualanresep_t.penjualanresep_id,
            COALESCE(penjualanresep_t.totaltarifservice, (0)::double precision) AS jasa,
            COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision) AS administrasi,
            COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) AS obat,
            ((COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.totaltarifservice, (0)::double precision)) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totalharga_jual,
            NULL::integer AS kelas_bpjspendaftaran,
            NULL::integer AS kelas_bpjsadmisi,
            NULL::integer AS bpjs_idpendaftaran,
            NULL::integer AS bpjs_idadmisi,
            NULL::character varying AS no_bpjspendaftaran,
            NULL::character varying AS no_bpjsadmisi,
            NULL::character varying AS no_asuransipendaftaran,
            NULL::character varying AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_m.groupcarabayar_id AS groupcarabayar_admisi,
            pemberianpiutang_t.total_piutang,
            NULL::integer AS dokterrj_id,
            NULL::integer AS dokterri_id,
            NULL::character varying AS keadaanmasuk_id,
            NULL::character varying AS keadaan_masuk,
            NULL::character varying AS transportasi_id,
            NULL::character varying AS transportasi,
            NULL::text AS keterangan_pendaftaran,
            (tagihan_resep.tagihan_obat)::integer AS tagihan_belumbayar,
            0 AS sisa_penunjang,
            0 AS sisa_karcis,
            (tagihan_resep.tagihan_obat)::integer AS sisa_obat,
            NULL::character varying AS status_periksa,
            NULL::double precision AS tinggi,
            NULL::double precision AS berat,
            NULL::timestamp without time zone AS tgl_stopakomodasi
           FROM ((((((((penjualanresep_t
             LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN ruangan_m ON ((penjualanresep_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             LEFT JOIN carabayar_m ON ((penjualanresep_t.carabayar_id = carabayar_m.carabayar_id)))
             LEFT JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
             LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
             LEFT JOIN pemberianpiutang_t ON ((penjualanresep_t.penjualanresep_id = pemberianpiutang_t.penjualanresep_id)))
             LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
                   FROM obatalkespasien_t
                  WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                  GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON ((penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id)))
          WHERE (((penjualanresep_t.jenispenjualan)::text = ANY (ARRAY['343'::text, '345'::text])) AND (penjualanresep_t.is_deleted = false))) data_info;");

 

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200728_035234_migrate_mhkn_20200728 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200728_035234_migrate_mhkn_20200728 cannot be reverted.\n";

        return false;
    }
    */
}
