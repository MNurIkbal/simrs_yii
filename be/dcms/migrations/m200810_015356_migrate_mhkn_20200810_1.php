<?php

use yii\db\Migration;

/**
 * Class m200810_015356_migrate_mhkn_20200810_1
 */
class m200810_015356_migrate_mhkn_20200810_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN "kelola_tagihan" int4 DEFAULT 0;');

        $this->execute('ALTER TABLE "public"."pasienadmisi_t" ADD COLUMN "is_stoptitipan" bool DEFAULT false;');
        
        $this->execute('ALTER TABLE "public"."pindahkamar_t" ADD COLUMN "is_stoptitipan" bool DEFAULT false;');
     

        $this->execute('DROP VIEW if exists "public"."infokunjunganri_v";');
        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganri_v\" AS  SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
    pasienadmisi_t.tgl_pulang,
    pasienadmisi_t.kunjungan,
    pasienadmisi_t.status_keluar,
    pasienadmisi_t.rawat_gabung,
    kamarruangan_m.kamarruangan_id,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pasienadmisi_t.pegawai_id,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pegawai_m.kelompokpegawai_id,
    pasien_m.is_deleted,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    golonganumur_m.golonganumur_nama,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasienadmisi_t.created_by,
    kamartempattidur_m.kamartempattidur_id,
    pasienadmisi_t.bpjs_id,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    bpjs_t.nosep,
    kelaspelayanan_m.urutankelas,
    kelaspelayanan_m.bpjs_kelas,
    bpjs_t.klsrawat,
    pasienadmisi_t.is_aps,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pasienadmisi_t.kamar_titipan_id,
    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
    pasienadmisi_t.ruangan_titipan_id,
    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
    pasienadmisi_t.is_stoptitipan
   FROM (((((((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN caramasuk_m ON ((pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
     LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
  WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false));");

        $this->execute('DROP VIEW if exists "public"."invoicesudahbayardetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"invoicesudahbayardetail_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.pelayanan_id,
    tagihan.pasien_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN (pasien_m.nama_pasien IS NULL) THEN (tagihan.nama_pembeli)::character varying
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    pasien_m.tanggal_lahir,
    tagihan.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    tagihan.tgl_pendaftaran,
    tagihan.no_pendaftaran,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    (tagihan.tarif_satuan)::integer AS tarif_satuan,
    tagihan.qty,
    (tagihan.sub_total)::integer AS sub_total,
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
    (tagihan.tarif_cyto)::integer AS tarif_cyto,
    tagihan.tandabuktibayar_id,
    tagihan.jeniskasuspenyakit_nama,
    tagihan.penjualanresep_id,
    tagihan.is_konsultasi,
    dok_tindakan.nama_pegawai AS dokter_tindakan,
    tagihan.pembayaran_id,
    tagihan.satuan_kecil AS uom
   FROM (((((((( SELECT pendaftaran_t.pendaftaran_id,
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
                    WHEN (daftartindakan_m.is_konsultasi = true) THEN 'Consultation'::character varying
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
            NULL::text AS satuan_kecil
           FROM (((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN tindakanpelayanan_t ON (((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
             JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
             JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
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
            NULL::text AS satuan_kecil
           FROM ((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
             JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
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
            satuan_kecil.satuanunit_nama AS satuan_kecil
           FROM (((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
             LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
          WHERE (obatalkespasien_t.is_deleted = false)
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
            satuan_kecil.satuanunit_nama AS satuan_kecil
           FROM (((((((obatalkespasien_t
             JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
             LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
          WHERE ((penjualanresep_t.jenispenjualan)::integer <> 344)) tagihan
     LEFT JOIN ruangan_m ON ((tagihan.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN carabayar_m ON ((tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m dok_tindakan ON ((tagihan.doktertindakan_id = dok_tindakan.pegawai_id)));");

        $this->execute('DROP VIEW if exists "public"."infopasienri_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienri_v\" AS  SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
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
    pasien_m.jeniskelamin AS jeniskelamin_id,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
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
    fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap,
    kamarruangan_m.jeniskasuspenyakit_id,
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
              WHERE ((x.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (x.is_instruksi_pulang = true))) > 0) THEN true
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
    COALESCE(tagihan.sub_total, (0)::double precision) AS tagihan_rs,
    COALESCE(monitorsetdiagnosa.total, (0)::double precision) AS tarif_inacbg,
    carabayar_m.groupcarabayar_id AS group_carabayar,
        CASE
            WHEN (monitorsetdiagnosa.diag_utama_id IS NULL) THEN 'BELUM DIMONITOR'::text
            ELSE 'SUDAH DIMONITOR'::text
        END AS status_monitor,
    bpjs_t.nosep,
    pasienadmisi_t.is_aps,
    kelaspelayanan_m.urutankelas,
    kelaspelayanan_m.bpjs_kelas,
    pendaftaran_t.keterangan_pendaftaran,
    pasienadmisi_t.asuransipasien_id,
    pendaftaran_t.is_stopakomodasi,
    pendaftaran_t.tgl_stopakomodasi,
        CASE
            WHEN (implementasi.sisa = 0) THEN true
            WHEN (implementasi.sisa <> 0) THEN false
            ELSE false
        END AS status_implementasi,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pasienadmisi_t.kamar_titipan_id,
    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
    pasienadmisi_t.ruangan_titipan_id,
    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
    pasienadmisi_t.is_stoptitipan,
        CASE
            WHEN (pasienadmisi_t.is_stoptitipan IS TRUE) THEN true
            ELSE pasienadmisi_t.is_pasientitipan
        END AS is_stoppasientitipan
   FROM ((((((((((((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN pegawai_m dokter_pendaftaran ON ((pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id)))
     JOIN pegawai_m dokter_admisi ON ((pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN bpjs_t ON ((pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (asesmenmedis_t.is_deleted = false))))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN asesmenawal_t ON ((pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id)))
     LEFT JOIN rencanapulang_t ON (((pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id) AND (rencanapulang_t.is_deleted = false))))
     LEFT JOIN ( SELECT x.pendaftaran_id,
            x.pasienadmisi_id,
            sum(x.sub_total) AS sub_total
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                   FROM (pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON (((pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sub_total
                   FROM (pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON (((pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
          GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON (((pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id))))
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
           FROM (monitorsetdiagnosa_t
             JOIN diagnosa_m ON ((monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id)))
          WHERE (monitorsetdiagnosa_t.is_deleted = false)) monitorsetdiagnosa ON ((pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id)))
     LEFT JOIN ( SELECT cppt_t.pendaftaran_id,
            (count(instruksitindakan_t.status_implementasi) + count(instruksitindakanbmhp_t.status_implementasi)) AS sisa
           FROM (((cppt_t
             LEFT JOIN instruksi_t ON (((cppt_t.cppt_id = instruksi_t.cppt_id) AND (instruksi_t.is_deleted = false))))
             LEFT JOIN instruksitindakan_t ON (((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id) AND (instruksitindakan_t.is_deleted = false) AND ((instruksitindakan_t.status_implementasi)::text <> '455'::text))))
             LEFT JOIN instruksitindakanbmhp_t ON (((instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id) AND (instruksitindakanbmhp_t.is_deleted = false) AND ((instruksitindakanbmhp_t.status_implementasi)::text <> '455'::text))))
          WHERE (cppt_t.is_deleted = false)
          GROUP BY cppt_t.pendaftaran_id) implementasi ON ((pendaftaran_t.pendaftaran_id = implementasi.pendaftaran_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
     LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
  WHERE ((pasienadmisi_t.is_active = true) AND (pasienadmisi_t.is_deleted = false));");

        $this->execute('DROP VIEW if exists "public"."infopasienpulangri_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienpulangri_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
        CASE
            WHEN (pasienadmisi_t.pasienpulang_id IS NULL) THEN pendaftaran_t.tgl_stopakomodasi
            ELSE pasienpulang_t.tglpasienpulang
        END AS tglpasienpulang,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.ruangan_id AS ruanganakhir_id,
    ruangan_m.ruangan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.lama_rawat,
    pasienpulang_t.pasienpulang_id,
    pendaftaran_t.umur,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jns_kelamin,
    pasienadmisi_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    pasienadmisi_t.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur,
    pendaftaran_t.is_stopakomodasi,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pasienadmisi_t.kamar_titipan_id,
    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
    pasienadmisi_t.ruangan_titipan_id,
    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
    pasienadmisi_t.is_stoptitipan
   FROM ((((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pasienpulang_t ON (((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (pasienadmisi_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id))))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
     LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
  WHERE (pendaftaran_t.is_stopakomodasi = true);");

        $this->execute('DROP VIEW if exists "public"."infopasienoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienoperasi_v\" AS  SELECT 'ORDER'::text AS jenis,
    pasienmasukpenunjang_t.pendaftaran_id,
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
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
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
    pasienmasukpenunjang_t.catatan AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    (cppt_t.a_diag_utama ->> 'text'::text) AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama
   FROM (((((((((((((((((pasienmasukpenunjang_t
     JOIN rencanaoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id)))
     LEFT JOIN cppt_t ON (((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id) AND (pasienadmisi_t.pegawai_id = cppt_t.pegawai_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true) AND (cppt_t.is_instruksi_pulang = false))))
     LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 12) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
UNION ALL
 SELECT 'APS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama
   FROM ((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
     LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pendaftaran_t.instalasi_id = 12) AND (pasienmasukpenunjang_t.is_bayar = true))
UNION ALL
 SELECT 'PASIEN RS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama
   FROM (((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ruangan_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_penunjang.ruangan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
     LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (ruangan_penunjang.instalasi_id = 12) AND (pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL) AND (pendaftaran_t.instalasi_id <> 12));");
        
        $this->execute('DROP VIEW if exists "public"."rincianpasien_v";');

        $this->execute("
            CREATE VIEW \"public\".\"rincianpasien_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    carabayar_admisi.carabayar_nama AS carabayar_admisi,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_admisi.penjamin_nama AS penjamin_admisi,
    pendaftaran_t.kelaspelayanan_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN kelas_admisi.kelaspelayanan_nama
            ELSE kelaspelayanan_m.kelaspelayanan_nama
        END AS kelaspelayanan_nama,
    kelas_admisi.kelaspelayanan_nama AS kelas_admisi,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_lunas,
    pendaftaran_t.is_stopakomodasi,
        CASE
            WHEN (pendaftaran_t.is_stopakomodasi <> true) THEN false
            WHEN (pendaftaran_t.is_stopakomodasi = true) THEN true
            ELSE NULL::boolean
        END AS is_pulang,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text)
            ELSE to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)
        END AS tgl_masuk,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pasienadmisi_t.tgl_admisi, 'HH24:MI:SS'::text)
            ELSE to_char(pendaftaran_t.tgl_pendaftaran, 'HH24:MI:SS'::text)
        END AS jam_masuk,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'YYYY-MM-DD'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'YYYY-MM-DD'::text)
        END AS tgl_keluar,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'HH24:MI:SS'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'HH24:MI:SS'::text)
        END AS jam_keluar,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pasienadmisi_t.kamar_titipan_id,
    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
    pasienadmisi_t.ruangan_titipan_id,
    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
    pasienadmisi_t.is_stoptitipan
   FROM (((((((((((((((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_admisi ON ((pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_admisi ON ((pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN kelaspelayanan_m kelas_admisi ON ((pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m r_admisi ON ((pasienadmisi_t.ruangan_id = r_admisi.ruangan_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     LEFT JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     LEFT JOIN bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
     LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
     LEFT JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id)))
     LEFT JOIN pasienpulang_t pulang_pendaftaran ON ((pendaftaran_t.pasienpulang_id = pulang_pendaftaran.pasienpulang_id)))
     LEFT JOIN pasienpulang_t pulang_admisi ON ((pasienadmisi_t.pasienpulang_id = pulang_admisi.pasienpulang_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
     LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasienadmisi_t.tgl_admisi, pendaftaran_t.instalasi_id, instalasi_m.instalasi_nama, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, pendaftaran_t.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pegawai_id, pasienadmisi_t.pegawai_id, dok_pendaftaran.nama_pegawai, dok_admisi.nama_pegawai, pendaftaran_t.ruangan_id, r_pendaftaran.ruangan_nama, pasienadmisi_t.ruangan_id, r_admisi.ruangan_nama, pasienadmisi_t.kamarruangan_id, kamarruangan_m.kamarruangan_nokamar, pasienadmisi_t.kamartempattidur_id, kamartempattidur_m.no_tempattidur, pendaftaran_t.status_bayar, (fgetnamalookup(pendaftaran_t.status_bayar)), penjamin_admisi.penjamin_nama, carabayar_admisi.carabayar_nama, kelas_admisi.kelaspelayanan_nama, pasien_m.namadepan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'YYYY-MM-DD'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'YYYY-MM-DD'::text)
        END,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'HH24:MI:SS'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'HH24:MI:SS'::text)
        END, pasienadmisi_t.is_pasientitipan, pasienadmisi_t.kelas_ditagihkan_id, kelas_ditagihkan.kelaspelayanan_nama, pasienadmisi_t.kamar_titipan_id, kamar_ditagihkan.kamarruangan_nokamar, pasienadmisi_t.ruangan_titipan_id, ruangan_ditagihkan.ruangan_nama, pasienadmisi_t.is_stoptitipan;");
       
        $this->execute('DROP VIEW if exists "public"."invoiceridetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"invoiceridetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    layanan.layanan_jenis,
    layanan.tgl_pelayanan,
    layanan.tindakan_obat,
    layanan.kelompok,
    layanan.qty,
    layanan.harga_satuan,
    layanan.tarif,
    layanan.uom,
    layanan.ruangan,
    layanan.dokter,
    layanan.is_akomodasi,
    layanan.is_konsultasi,
    layanan.additional_data,
    layanan.kamarruangan_nokamar AS kamar,
    layanan.no_tempattidur AS no_bed,
    layanan.kelaspelayanan_nama AS kelas,
    layanan.pembayaran_id
   FROM ((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN ( SELECT 'tindakan'::text AS layanan_jenis,
            tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat,
                CASE
                    WHEN (daftartindakan_m.is_konsultasi = true) THEN 'Consultation'::character varying
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
            NULL::character varying AS no_tempattidur,
            kelaspelayanan_m.kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id
           FROM (((((((((tindakanpelayanan_t
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
             LEFT JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN pegawai_m dok_dpjp ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = dok_dpjp.pegawai_id)))
             LEFT JOIN ( SELECT masukkamar_t.pasienadmisi_id,
                    masukkamar_t.ruangan_id,
                    masukkamar_t.kamarruangan_id,
                    masukkamar_t.kelaspelayanan_id
                   FROM masukkamar_t
                  GROUP BY masukkamar_t.pasienadmisi_id, masukkamar_t.ruangan_id, masukkamar_t.kamarruangan_id, masukkamar_t.kelaspelayanan_id) masuk_kamar ON (((tindakanpelayanan_t.pasienadmisi_id = masuk_kamar.pasienadmisi_id) AND (tindakanpelayanan_t.ruangan_id = masuk_kamar.ruangan_id))))
             LEFT JOIN kamarruangan_m ON ((masuk_kamar.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
             LEFT JOIN kelaspelayanan_m ON ((masuk_kamar.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
             JOIN tindakansudahbayar_t ON ((tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
        UNION ALL
         SELECT 'obat'::text AS layanan_jenis,
            obatalkespasien_t.pendaftaran_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
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
            pembayaranpelayanan_t.pembayaran_id
           FROM (((((((obatalkespasien_t
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
             LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
             LEFT JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN pegawai_m dok_dpjp ON ((obatalkespasien_t.pegawai_id = dok_dpjp.pegawai_id)))
             JOIN obatsudahbayar_t ON ((obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
          WHERE (obatalkespasien_t.is_deleted = false)) layanan ON ((pendaftaran_t.pendaftaran_id = layanan.pendaftaran_id)));");

        $this->execute('DROP VIEW if exists "public"."infokunjunganrs_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganrs_v\" AS  SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pendaftaran_t.pendaftaran_id,
    pekerjaan_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.kunjungan,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    golonganumur_m.golonganumur_id,
    golonganumur_m.golonganumur_nama,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    pendaftaran_t.rujukan_id,
    pendaftaran_t.pasienpulang_id,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.pembayaranpelayanan_id,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    carakeluar_m.carakeluar_id,
    carakeluar_m.carakeluar_nama AS carakeluar,
    kondisikeluar_m.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    NULL::integer AS konsulpoli_id,
    pasien_m.is_deleted,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pendaftaran_t.created_by,
    antrian_t.no_antrian,
    pendaftaran_t.is_karcis,
    (pendaftaran_t.status_periksa)::integer AS status_periksa_id,
    pendaftaran_t.pasienpulang_id AS pulang_rj_rd,
    NULL::integer AS pulang_ri,
    NULL::integer AS pasienadmisi_id,
    pendaftaran_t.is_ranap,
    pendaftaran_t.bpjs_id,
    fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan_nama,
    fgetnamalookup((pegawai_m.gelarbelakang)::integer) AS gelarbelakang_nama,
    pendaftaran_t.pendaftaranibu_id,
    pasienpulang_t.tglpasienpulang,
    carakeluar_m.carakeluar_nama,
    bpjs_t.nosep,
    pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
    fgetnamalookup((pendaftaran_t.status_konfirmasi)::integer) AS status_konfirmasirm,
    fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien_nama,
    NULL::character varying AS kamar,
    NULL::character varying AS no_tempattidur,
    NULL::boolean AS is_pasientitipan,
    NULL::integer AS kelas_ditagihkan_id,
    NULL::character varying AS kelas_ditagihkan_nama,
    NULL::integer AS kamar_titipan_id,
    NULL::character varying AS kamar_titipan_nama,
    NULL::integer AS ruangan_titipan_id,
    NULL::character varying AS ruangan_titipan_nama,
    false AS is_stoptitipan
   FROM ((((((((((((((((((((((pendaftaran_t
     LEFT JOIN antrian_t ON ((antrian_t.antrian_id = pendaftaran_t.antrian_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
     LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
  WHERE (pendaftaran_t.instalasi_id <> 3)
UNION ALL
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pendaftaran_t.pendaftaran_id,
    pekerjaan_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.kunjungan,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    golonganumur_m.golonganumur_id,
    golonganumur_m.golonganumur_nama,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    pendaftaran_t.rujukan_id,
    pasienadmisi_t.pasienpulang_id,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pasienadmisi_t.pegawai_id,
    pendaftaran_t.pembayaranpelayanan_id,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    carakeluar_m.carakeluar_id,
    carakeluar_m.carakeluar_nama AS carakeluar,
    kondisikeluar_m.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    NULL::integer AS konsulpoli_id,
    pasien_m.is_deleted,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pendaftaran_t.created_by,
    antrian_t.no_antrian,
    pendaftaran_t.is_karcis,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    NULL::integer AS pulang_rj_rd,
    pasienadmisi_t.pasienpulang_id AS pulang_ri,
    pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.is_ranap,
    pasienadmisi_t.bpjs_id,
    fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan_nama,
    fgetnamalookup((pegawai_m.gelarbelakang)::integer) AS gelarbelakang_nama,
    pendaftaran_t.pendaftaranibu_id,
    pasienpulang_t.tglpasienpulang,
    carakeluar_m.carakeluar_nama,
    bpjs_t.nosep,
    pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
    fgetnamalookup((pendaftaran_t.status_konfirmasi)::integer) AS status_konfirmasirm,
    fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien_nama,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pasienadmisi_t.kamar_titipan_id,
    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
    pasienadmisi_t.ruangan_titipan_id,
    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
    NULL::boolean AS is_stoptitipan
   FROM ((((((((((((((((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN antrian_t ON ((antrian_t.antrian_id = pendaftaran_t.antrian_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
     LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
     LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)));");

        $this->execute('DROP VIEW if exists "public"."infopasienpenunjang_v";');

        $this->execute("CREATE VIEW \"public\".\"infopasienpenunjang_v\" AS  SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_penunjang,
    ruangasal.ruangan_nama AS ruangan_asal,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    kelaspelayanan_m.kelaspelayanan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienmasukpenunjang_t.status_periksa,
    ruangan_m.instalasi_id,
    pendaftaran_t.created_by,
    ruangan_m.ruangan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pegawai_m.nama_pegawai,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pasienadmisi_t.kamar_titipan_id,
    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
    pasienadmisi_t.ruangan_titipan_id,
    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
    pasienadmisi_t.is_stoptitipan
   FROM (((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN ruangan_m ruangasal ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangasal.ruangan_id)))
     JOIN kelaspelayanan_m ON ((pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pasienmasukpenunjang_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
     LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
  WHERE ((pasienmasukpenunjang_t.is_active = true) AND (pasienmasukpenunjang_t.is_deleted = false));");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200810_015356_migrate_mhkn_20200810_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200810_015356_migrate_mhkn_20200810_1 cannot be reverted.\n";

        return false;
    }
    */
}
