<?php

use yii\db\Migration;

/**
 * Class m210307_030233_migrate_20210703_perubahaninvoice
 */
class m210307_030233_migrate_20210703_perubahaninvoice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    
    $this->execute('DROP VIEW if exists "public"."invoiceobatdetail_v";');

    $this->execute("
        CREATE VIEW \"public\".\"invoiceobatdetail_v\" AS  SELECT pembayaranpelayanan_t.pembayaran_id,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    penjualanresep_t.noresep AS no_resep,
    penjualanresep_t.tglresep AS tgl_resep,
    detail_obat.obatalkes_nama,
    detail_obat.qty,
    detail_obat.uom,
    detail_obat.harga_satuan,
    detail_obat.tarif,
    detail_obat.tarif_dijamin,
    detail_obat.tarif_dibayarkan,
    detail_obat.tarif_diskon
   FROM pembayaranpelayanan_t
     JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     JOIN obatsudahbayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = obatsudahbayar_t.pembayaranpelayanan_id
     JOIN ( SELECT obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.penjualanresep_id,
            obatalkespasien_t.obatalkespasien_id,
            obatalkes_m.obatalkes_nama,
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.det
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.hargasatuan_oa AS harga_satuan,
            obatalkespasien_t.hargajual_oa AS tarif,
            sat_kecil.satuanunit_nama AS uom,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            obatalkespasien_t.tarif_diskon
           FROM obatalkespasien_t
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN satuanunit_m sat_kecil ON obatalkespasien_t.satuankecil_id = sat_kecil.satuanunit_id
          WHERE obatalkespasien_t.is_deleted = false) detail_obat ON obatsudahbayar_t.obatsudahbayar_id = detail_obat.obatsudahbayar_id AND obatsudahbayar_t.obatalkespasien_id = detail_obat.obatalkespasien_id
     JOIN penjualanresep_t ON detail_obat.penjualanresep_id = penjualanresep_t.penjualanresep_id;");

    $this->execute('ALTER TABLE "public"."invoiceobatdetail_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."invoiceridetail_v";');

    $this->execute("
        CREATE VIEW \"public\".\"invoiceridetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    layanan.layanan_jenis,
    layanan.tgl_pelayanan,
    layanan.groupinacbg_id,
    layanan.groupinacbg_nama,
    layanan.tindakan_obat_id,
    layanan.tindakan_obat,
    layanan.kelompok,
    layanan.qty,
    layanan.harga_satuan::integer AS harga_satuan,
    layanan.tarif::integer AS tarif,
    layanan.uom,
    layanan.ruangan,
    layanan.dokter,
    layanan.is_akomodasi,
    layanan.is_konsultasi,
    layanan.additional_data,
    layanan.kamarruangan_nokamar AS kamar,
    layanan.no_tempattidur AS no_bed,
    layanan.kelaspelayanan_nama AS kelas,
    layanan.pembayaran_id,
    layanan.tarif_dijamin,
    layanan.tarif_dibayarkan,
    layanan.tarif_diskon,
    layanan.tarifcyto_tindakan,
    layanan.cyto_tindakan,
    layanan.is_visite,
    layanan.kelompoktindakan_id,
    layanan.tarifpenyulit_tindakan
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT 'tindakan'::text AS layanan_jenis,
            tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            daftartindakan_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            daftartindakan_m.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat,
                CASE
                    WHEN daftartindakan_m.is_konsultasi = true THEN 'Consultation'::character varying
                    ELSE kelompoktindakan_m.kelompoktindakan_nama
                END AS kelompok,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarif_satuan AS harga_satuan,
            tindakanpelayanan_t.tarif_tindakan AS tarif,
            NULL::character varying AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            daftartindakan_m.is_akomodasi,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.additional_data,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            kelaspelayanan_m.kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_diskon,
            tindakanpelayanan_t.cyto_tindakan,
                CASE
                    WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                    ELSE false
                END AS is_visite,
            daftartindakan_m.kelompoktindakan_id,
            tindakanpelayanan_t.tarifpenyulit_tindakan
           FROM tindakanpelayanan_t
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             LEFT JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pegawai_m dok_dpjp ON tindakanpelayanan_t.dokterpenanggungjawab_id = dok_dpjp.pegawai_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN tindakansudahbayar_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
             JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id
             LEFT JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT 'obat'::text AS layanan_jenis,
            obatalkespasien_t.pendaftaran_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            NULL::integer AS groupinacbg_id,
            NULL::character varying AS groupinacbg_nama,
            obatalkes_m.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat,
            'Drugs & Consumables'::character varying AS kelompok,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.hargasatuan_oa AS harga_satuan,
            obatalkespasien_t.hargajual_oa AS tarif,
            satuanunit_m.satuanunit_nama AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            false AS is_akomodasi,
            false AS is_konsultasi,
            obatalkespasien_t.additional_data,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::character varying AS no_tempattidur,
            NULL::character varying AS kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            0 AS tarifcyto_tindakan,
            obatalkespasien_t.tarif_diskon,
            false AS cyto_tindakan,
            false AS is_visite,
            NULL::integer AS kelompoktindakan_id,
            0 AS tarifpenyulit_tindakan
           FROM obatalkespasien_t
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             LEFT JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
             LEFT JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pegawai_m dok_dpjp ON obatalkespasien_t.pegawai_id = dok_dpjp.pegawai_id
             JOIN obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
          WHERE obatalkespasien_t.is_deleted = false) layanan ON pendaftaran_t.pendaftaran_id = layanan.pendaftaran_id;");

    $this->execute('ALTER TABLE "public"."invoiceridetail_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."invoicesudahbayardetail_v";');

    $this->execute("
        CREATE VIEW \"public\".\"invoicesudahbayardetail_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.pelayanan_id,
    tagihan.pasien_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN tagihan.nama_pembeli::character varying
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    pasien_m.tanggal_lahir,
    tagihan.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
    tagihan.tgl_pendaftaran,
    tagihan.no_pendaftaran,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan::integer AS tarif_satuan,
    tagihan.qty,
    tagihan.sub_total::integer AS sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.tgl_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_tinpelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_tinpelayanan,
    tagihan.penjamin_tinpelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_tinpelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.jeniskasuspenyakit_id,
    tagihan.pembayaranpelayanan_id,
    tagihan.biaya_administrasi,
    tagihan.e_collection,
    tagihan.nama_pemrekening,
    tagihan.no_rekening,
    tagihan.carabayar_pelayanan_id,
    tagihan.carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    tagihan.penjamin_pelayanan,
    tagihan.tarif_cyto::integer AS tarif_cyto,
    tagihan.tandabuktibayar_id,
    tagihan.jeniskasuspenyakit_nama,
    tagihan.penjualanresep_id,
    tagihan.is_konsultasi,
    dok_tindakan.nama_pegawai AS dokter_tindakan,
    tagihan.pembayaran_id,
    tagihan.satuan_kecil AS uom,
    tagihan.tarif_dijamin,
    tagihan.tarif_dibayarkan,
    tagihan.groupinacbg_nama,
    tagihan.tarif_diskon,
    tagihan.is_visite,
    tagihan.tarifpenyulit_tindakan
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
                CASE
                    WHEN daftartindakan_m.is_konsultasi = true THEN 'Consultation'::character varying
                    ELSE kelompoktindakan_m.kelompoktindakan_nama
                END AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_nama,
            tindakanpelayanan_t.tarif_diskon,
                CASE
                    WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                    ELSE false
                END AS is_visite,
            tindakanpelayanan_t.tarifpenyulit_tindakan
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_paket'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            NULL::character varying AS groupinacbg_nama,
            tindakanpelayanan_t.tarif_diskon,
            false AS is_visite,
            tindakanpelayanan_t.tarifpenyulit_tindakan
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'Drugs & Consumables'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            NULL::bigint AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_nama,
            obatalkespasien_t.tarif_diskon,
            false AS is_visite,
            0 AS tarifpenyulit_tindakan
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE obatalkespasien_t.is_deleted = false
        UNION ALL
         SELECT penjualanresep_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            penjualanresep_t.noresep AS no_pendaftaran,
            NULL::character varying AS umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            NULL::integer AS jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            NULL::character varying AS jeniskasuspenyakit_nama,
            obatalkespasien_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            NULL::boolean AS is_konsultasi,
            NULL::bigint AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_nama,
            obatalkespasien_t.tarif_diskon,
            false AS is_visite,
            0 AS tarifpenyulit_tindakan
           FROM obatalkespasien_t
             JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE penjualanresep_t.jenispenjualan::integer <> 344) tagihan
     LEFT JOIN ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN carabayar_m ON tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m dok_tindakan ON tagihan.doktertindakan_id = dok_tindakan.pegawai_id;");

    $this->execute('ALTER TABLE "public"."invoicesudahbayardetail_v" OWNER TO "postgres";');

    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210307_030233_migrate_20210703_perubahaninvoice cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210307_030233_migrate_20210703_perubahaninvoice cannot be reverted.\n";

        return false;
    }
    */
}
