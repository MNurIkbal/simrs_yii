<?php

use yii\db\Migration;

/**
 * Class m200610_030909_migrate_20200610
 */
class m200610_030909_migrate_20200610 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."antrian_t" ADD COLUMN "jenisantriandetail_id" int4;');

        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ALTER COLUMN "keadaan_umum" TYPE text COLLATE "pg_catalog"."default" USING "keadaan_umum"::text;');
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ALTER COLUMN "td_systolic" TYPE float4 USING "td_systolic"::float4;');
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ALTER COLUMN "td_diastolic" TYPE float4 USING "td_diastolic"::float4;');
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ALTER COLUMN "detak_nadi" TYPE float4 USING "detak_nadi"::float4;');
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ALTER COLUMN "pernapasan" TYPE float4 USING "pernapasan"::float4;');
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ALTER COLUMN "suhu_tubuh" TYPE float4 USING "suhu_tubuh"::float4;');
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ALTER COLUMN "spo2" TYPE float4 USING "spo2"::float4;');

        $this->execute('ALTER TABLE "public"."konfigantrian_m" ADD COLUMN "jenisantriandetail_id" int4;');

        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN "digit_rekammedik" int2;');
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN "is_jenisantriandetail" bool;');
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN "is_validasipendaftaran" bool;');

        $this->execute('ALTER TABLE "public"."konsulpoli_t" ADD COLUMN "pendaftaranbaru_id" int4;');

        $this->execute('ALTER TABLE "public"."penjualanresep_t" ADD COLUMN "status_worklist" int2 DEFAULT 674;');

        $this->execute('ALTER TABLE "public"."reseptur_t" ADD COLUMN "status_worklist" int2 DEFAULT 674;');
        $this->execute("COMMENT ON COLUMN \"public\".\"reseptur_t\".\"status_worklist\" IS 'lookup_type =''status_worklistresep''';");

        $this->execute('DROP VIEW if exists "public"."obatalkesdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"obatalkesdetail_v\" AS  SELECT obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.jenisobatalkes_nama,
    obatalkes_m.ven AS ven_id,
    fgetnamalookup(obatalkes_m.ven) AS ven,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.kekuatan_obat,
    obatalkes_m.satuankecil_id,
    satuankecil.satuanunit_nama AS satuan_kecil,
    obatalkes_m.satuansedang_id,
    satuan_1.satuanunit_nama AS satuan_1,
    obatalkes_m.satuanbesar_id,
    satuan_2.satuanunit_nama AS satuan_2,
    obatalkes_m.kemasan_sedang,
    obatalkes_m.kemasan_besar,
    obatalkes_m.is_generik,
    obatalkes_m.tglkadaluarsa,
    obatalkes_m.obatalkes_nobatch,
    obatalkes_m.obatalkes_kategori,
    fgetnamalookup((obatalkes_m.obatalkes_kategori)::integer) AS kategori,
    obatalkes_m.maksimalstok,
    obatalkes_m.supplier_id,
    supplier_m.supplier_nama,
    obatalkes_m.harga_beli,
    obatalkes_m.discount,
    obatalkes_m.ppn_persen,
    obatalkes_m.harganetto,
    obatalkes_m.hargamaksimum,
    obatalkes_m.hargaminimum,
    obatalkes_m.hargaratarata,
    obatalkes_m.hargaterakhir,
    obatalkes_m.indikasi,
    obatalkes_m.interaksi,
    obatalkes_m.kontradiksi,
    obatalkes_m.efek_samping,
    obatalkes_m.satuankekuatan,
    fgetnamalookup((obatalkes_m.satuankekuatan)::integer) AS lookup_name,
    obatalkes_m.groupinacbg_id,
    obatalkes_m.lead_time,
    obatalkes_m.minimalstok,
    obatalkes_m.avg_usage,
    obatalkes_m.min_order,
    obatalkes_m.max_order,
    obatalkes_m.nilai_ro,
    obatalkes_m.on_po,
    obatalkes_m.on_ro,
    obatalkes_m.is_oral
   FROM (((((obatalkes_m
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     LEFT JOIN satuanunit_m satuankecil ON ((obatalkes_m.satuankecil_id = satuankecil.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_1 ON ((obatalkes_m.satuansedang_id = satuan_1.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_2 ON ((obatalkes_m.satuanbesar_id = satuan_2.satuanunit_id)))
     LEFT JOIN supplier_m ON ((obatalkes_m.supplier_id = supplier_m.supplier_id)))
  WHERE ((obatalkes_m.is_active = true) AND (obatalkes_m.is_deleted = false));");

        $this->execute('DROP VIEW if exists "public"."inforesepdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inforesepdetail_v\" AS  SELECT 'reseptur'::text AS jenis,
    resepturdetail_t.resepturdetail_id,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS penjualanresep_id,
    resepturdetail_t.reseptur_id,
    reseptur_t.pendaftaran_id,
    reseptur_t.pasien_id,
    resepturdetail_t.obatalkes_id,
    resepturdetail_t.satuankecil_id,
    resepturdetail_t.racikan_id,
    resepturdetail_t.signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    reseptur_t.noresep,
    reseptur_t.tglreseptur,
    racikan_m.racikan_nama,
    resepturdetail_t.r,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama,
    resepturdetail_t.qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    resepturdetail_t.hargasatuan_reseptur AS hargajual_satuan,
    resepturdetail_t.hargajual_reseptur AS totalharga_jual,
    resepturdetail_t.etiket,
    resepturdetail_t.iter,
    signaobat_m.signa_nama,
    reseptur_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    rotd_t.interaksi,
    rotd_t.duplikasi,
    rotd_t.dosisi AS dosis,
    rotd_t.alergi,
    rotd_t.kontradiksi,
    rotd_t.review_note,
    rotd_t.wkt_review,
    pegawai_m.nama_pegawai,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
    (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
    (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
    ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
    ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
    (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
    resepturdetail_t.is_deleted,
    resepturdetail_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data AS additional_reseptur,
    (((resepturdetail_t.additional_data)::json ->> 'satuaninput_id'::text))::character varying AS satuaninput_id,
    (((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text))::character varying AS satuan_input,
    (((resepturdetail_t.additional_data)::json ->> 'satuankonversi_id'::text))::character varying AS satuankonversi_id,
    (((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text))::character varying AS satuan_konversi,
    (((resepturdetail_t.additional_data)::json ->> 'harga_konversi'::text))::character varying AS harga_konversi,
    (((resepturdetail_t.additional_data)::json ->> 'nilai_konversi'::text))::character varying AS nilai_konversi,
    (0)::double precision AS biayaadministrasiresep,
    (0)::double precision AS totalhargajualresep,
    (0)::double precision AS totaltagihanresep,
    NULL::character varying AS nama_pembeli,
    resepturdetail_t.qty_reseptur AS qty_oa,
    reseptur_t.ruanganreseptur_id AS ruanganasal_id,
    ruangan_asal.instalasi_id AS instalasiasal_id
   FROM (((((((((((((resepturdetail_t
     JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN satuanunit_m satuan_kecil ON ((resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
     JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN rotd_t ON ((resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id)))
     LEFT JOIN pegawai_m ON ((rotd_t.pegawairotd_id = rotd_t.pegawairotd_id)))
     LEFT JOIN obatalkespasien_t ON ((resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
  WHERE ((resepturdetail_t.is_deleted = false) AND (resepturdetail_t.is_active = true))
UNION ALL
 SELECT 'resep'::text AS jenis,
    NULL::integer AS resepturdetail_id,
    obatalkespasien_t.obatalkespasien_id,
    penjualanresep_t.penjualanresep_id,
    NULL::integer AS reseptur_id,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkespasien_t.satuankecil_id,
    obatalkespasien_t.racikan_id,
    NULL::integer AS signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    penjualanresep_t.noresep,
    penjualanresep_t.tglresep AS tglreseptur,
    racikan_m.racikan_nama,
    obatalkespasien_t.r,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama,
    (((obatalkespasien_t.additional_data)::json ->> 'qty_input'::text))::integer AS qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
    obatalkespasien_t.hargajual_oa AS totalharga_jual,
    obatalkespasien_t.etiket,
    NULL::integer AS iter,
    signaobat_m.signa_nama,
    penjualanresep_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    NULL::character varying AS interaksi,
    NULL::character varying AS duplikasi,
    NULL::character varying AS dosis,
    NULL::character varying AS alergi,
    NULL::character varying AS kontradiksi,
    NULL::character varying AS review_note,
    NULL::timestamp without time zone AS wkt_review,
    pegawai_m.nama_pegawai,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
    (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
    (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
    ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
    ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
    (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur,
    obatalkespasien_t.is_deleted,
    obatalkespasien_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data AS additional_reseptur,
    (((obatalkespasien_t.additional_data)::json ->> 'satuaninput_id'::text))::character varying AS satuaninput_id,
    (((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text))::character varying AS satuan_input,
    (((obatalkespasien_t.additional_data)::json ->> 'satuankonversi_id'::text))::character varying AS satuankonversi_id,
    (((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text))::character varying AS satuan_konversi,
    (((obatalkespasien_t.additional_data)::json ->> 'harga_konversi'::text))::character varying AS harga_konversi,
    (((obatalkespasien_t.additional_data)::json ->> 'nilai_konversi'::text))::character varying AS nilai_konversi,
    penjualanresep_t.biayaadministrasi AS biayaadministrasiresep,
    penjualanresep_t.totalhargajual AS totalhargajualresep,
    (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihanresep,
    penjualanresep_t.nama_pembeli,
    obatalkespasien_t.qty_oa,
    penjualanresep_t.ruangan_id AS ruanganasal_id,
    ruangan_tujuan.instalasi_id AS instalasiasal_id
   FROM ((((((((((obatalkespasien_t
     JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN ruangan_m ruangan_tujuan ON ((penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
  WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.is_active = true));");

        $this->execute('DROP VIEW if exists "public"."infokunjunganrj_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganrj_v\" AS  SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
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
    pendaftaran_t.status_periksa,
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
    ruangan_m.ruangan_singkatan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.tgl_renkontrol,
    pendaftaran_t.pembayaranpelayanan_id,
    pendaftaran_t.panggil_antrian,
    antrian_t.antrian_id,
    antrian_t.tgl_antrian,
    antrian_t.no_antrian,
    antrian_t.panggil_flag,
    loket_m.loket_id,
    loket_m.loket_nama,
    loket_m.loket_fungsi,
    loket_m.loket_singkatan,
    loket_m.loket_nourut,
    loket_m.loket_formatnomor,
    loket_m.loket_maxantrian,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pendaftaran_t.statusdok_rekammedik,
    pegawai_m.kelompokpegawai_id,
    NULL::integer AS konsulpoli_id,
    pasien_m.is_deleted,
    pasienpulang_t.tglpasienpulang,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa1,
    NULL::integer AS ruanganasal_id,
    NULL::character varying AS ruanganasal_nama,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    antrian_t.jenisantrian_id,
    ruangan_m.ruangan_nama AS poliklinik,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS stat_ranap,
    bpjs_t.nosep,
    ruangan_m.lantai_id
   FROM ((((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
  WHERE (pendaftaran_t.instalasi_id = 1)
UNION
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
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
    konsulpoli_t.tgl_konsulpoli AS tgl_pendaftaran,
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
    konsulpoli_t.status_periksa,
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
    ruangan_m.ruangan_singkatan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.tgl_renkontrol,
    pendaftaran_t.pembayaranpelayanan_id,
    pendaftaran_t.panggil_antrian,
    antrian_t.antrian_id,
    antrian_t.tgl_antrian,
    antrian_t.no_antrian,
    antrian_t.panggil_flag,
    loket_m.loket_id,
    loket_m.loket_nama,
    loket_m.loket_fungsi,
    loket_m.loket_singkatan,
    loket_m.loket_nourut,
    loket_m.loket_formatnomor,
    loket_m.loket_maxantrian,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pendaftaran_t.statusdok_rekammedik,
    pegawai_m.kelompokpegawai_id,
    konsulpoli_t.konsulpoli_id,
    pasien_m.is_deleted,
    pasienpulang_t.tglpasienpulang,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status_periksa1,
    konsulpoli_t.asalpoliklinikkonsul_id AS ruanganasal_id,
    ruanganasal_m.ruangan_nama AS ruanganasal_nama,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    antrian_t.jenisantrian_id,
    ruangan_m.ruangan_nama AS poliklinik,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS stat_ranap,
    bpjs_t.nosep,
    ruangan_m.lantai_id
   FROM ((((((((((((((((((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN ruangan_m ruanganasal_m ON ((konsulpoli_t.asalpoliklinikkonsul_id = ruanganasal_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
  WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (konsulpoli_t.status_approve = 565) AND (konsulpoli_t.pendaftaranbaru_id IS NULL));");
        
        $this->execute('DROP VIEW if exists "public"."infojanjipoli_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infojanjipoli_v\" AS  SELECT '673'::text AS transaksi_konsul,
    buatjanjipoli_t.buatjanjipoli_id,
    buatjanjipoli_t.tgl_buatjanji,
    buatjanjipoli_t.antrian_id,
    antrian_t.no_antrian,
    buatjanjipoli_t.pegawai_id,
    pegawai_m.nama_pegawai,
    buatjanjipoli_t.ruangan_id,
    ruangan_m.ruangan_nama,
    buatjanjipoli_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.alamatemail,
    fgetnamalookup((buatjanjipoli_t.hari_jadwal)::integer) AS hari,
    buatjanjipoli_t.tgl_jadwal,
    buatjanjipoli_t.is_rencanakontrol,
    fgetnamalookup((buatjanjipoli_t.status_janjipoli)::integer) AS status_janji,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    buatjanjipoli_t.carabayar_id,
    carabayar_m.carabayar_nama,
    buatjanjipoli_t.penjamin_id,
    penjamin_m.penjamin_nama,
    buatjanjipoli_t.keterangan_buatjanji,
    buatjanjipoli_t.by_phone,
    NULL::smallint AS status_approve,
    NULL::character varying AS status_approve_nama,
    NULL::bigint AS asalpoliklinikkonsul_id,
    NULL::character varying AS ruangan_asal,
    NULL::integer AS doktermengkonsul_id,
    NULL::character varying AS doktermengkonsul,
    pasien_m.tanggal_lahir
   FROM ((((((((buatjanjipoli_t
     JOIN pasien_m ON ((buatjanjipoli_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((buatjanjipoli_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((buatjanjipoli_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN antrian_t ON (((buatjanjipoli_t.antrian_id)::integer = antrian_t.antrian_id)))
     LEFT JOIN pendaftaran_t ON ((buatjanjipoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN carabayar_m ON ((buatjanjipoli_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((buatjanjipoli_t.penjamin_id = penjamin_m.penjamin_id)))
  WHERE ((buatjanjipoli_t.is_active = true) AND (buatjanjipoli_t.is_deleted = false))
UNION ALL
 SELECT '672'::text AS transaksi_konsul,
    konsulpoli_t.konsulpoli_id AS buatjanjipoli_id,
    konsulpoli_t.tgl_konsulpoli AS tgl_buatjanji,
    NULL::text AS antrian_id,
    NULL::text AS no_antrian,
    konsulpoli_t.pegawai_id,
    pegawai_m.nama_pegawai,
    konsulpoli_t.ruangan_id,
    ruangan_tujuan.ruangan_nama,
    konsulpoli_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.alamatemail,
    NULL::text AS hari,
    konsulpoli_t.tgl_konsulpoli AS tgl_jadwal,
    NULL::boolean AS is_rencanakontrol,
    NULL::text AS status_janji,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_tujuan.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    konsulpoli_t.catatan_dokter_konsul AS keterangan_buatjanji,
    NULL::boolean AS by_phone,
    konsulpoli_t.status_approve,
    fgetnamalookup((konsulpoli_t.status_approve)::integer) AS status_approve_nama,
    konsulpoli_t.asalpoliklinikkonsul_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    pendaftaran_t.pegawai_id AS doktermengkonsul_id,
    dok_mengkonsul.nama_pegawai AS doktermengkonsul,
    pasien_m.tanggal_lahir
   FROM (((((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN ruangan_m ruangan_tujuan ON ((konsulpoli_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN ruangan_m ruangan_asal ON ((konsulpoli_t.asalpoliklinikkonsul_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_tujuan.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((konsulpoli_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m dok_mengkonsul ON ((pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id)));");

        $this->execute('DROP VIEW if exists "public"."antrian_v";');

        $this->execute("
            CREATE VIEW \"public\".\"antrian_v\" AS  SELECT antrian_t.antrian_id,
    antrian_t.no_antrian,
    antrian_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.no_telepon_pasien,
    antrian_t.ruangan_id,
    ruangan_m.ruangan_nama,
    antrian_t.carabayar_id,
    carabayar_m.carabayar_nama,
    antrian_t.penjamin_id,
    penjamin_m.penjamin_nama,
    antrian_t.pendaftaran_id,
    antrian_t.layarantrian_id,
    layarantrian_m.layarantrian_nama,
    antrian_t.loket_id,
    loket_m.loket_nama,
    antrian_t.panggilan_ke,
    antrian_t.tgl_antrian,
    antrian_t.status_antrian,
        CASE
            WHEN (antrian_t.status_antrian = 0) THEN 'Belum Panggil'::text
            WHEN (antrian_t.status_antrian = 1) THEN 'Panggil'::text
            WHEN (antrian_t.status_antrian = 2) THEN 'Lewati'::text
            ELSE 'Batal'::text
        END AS stat_antrian,
    antrian_t.status_pasien,
    fgetnamalookup(antrian_t.status_pasien) AS stat_pasien,
    antrian_t.racikan_id,
    racikan_m.racikan_nama,
    pegawai_m.nama_pegawai,
    concat(fgetnamalookup((pegawai_m.gelardepan)::integer), ' ', pegawai_m.nama_pegawai, ' ', gelarbelakang.gelarbelakang_nama) AS nama_pegawai_lengkap,
    fgetnamalookup(antrian_t.groupcarabayar_id) AS namagroupcarabayar,
    antrian_t.jenisantrian_id,
    pegawai_m.dokter_id,
    ruangan_m.poliklinik_id,
    jadwalbukapoli_m.shift_id,
    antrian_t.is_online,
    antrian_t.fungsiantrian_id,
    fgetnamalookup(antrian_t.fungsiantrian_id) AS fungsi_nama,
    instalasi.instalasi_nama,
    antrian_t.antrian_farmasi,
        CASE
            WHEN (fgetnamalookup(antrian_t.antrian_farmasi) IS NULL) THEN ('Belum Proses'::text)::character varying
            ELSE fgetnamalookup(antrian_t.antrian_farmasi)
        END AS stat_antrian_farmasi,
        CASE
            WHEN (antrian_t.antrian_farmasi = 584) THEN 'Siap Ambil'::text
            WHEN (antrian_t.antrian_farmasi = 585) THEN 'Selesai'::text
            WHEN (antrian_t.antrian_farmasi = 586) THEN 'Selesai'::text
            ELSE 'Proses'::text
        END AS stat_proses_antrian_farmasi,
    antrian_t.is_appointment,
    pendaftaran_t.no_pendaftaran,
    pendaftaranol_t.tgl_pendaftaranol,
    antrian_t.panggil_flag,
    pegawai_m.pegawai_id,
    ruangan_m.ruangan_urutan,
    jenisantriandetail_m.nama AS lantai
   FROM (((((((((((((((antrian_t
     LEFT JOIN ruangan_m ON ((antrian_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN carabayar_m ON ((antrian_t.antrian_id = carabayar_m.carabayar_id)))
     LEFT JOIN layarantrian_m ON ((antrian_t.layarantrian_id = layarantrian_m.layarantrian_id)))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN pasien_m ON ((antrian_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN penjamin_m ON ((antrian_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN racikan_m ON ((antrian_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN pegawai_m ON ((antrian_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN gelarbelakang_m gelarbelakang ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang.gelarbelakang_id)))
     LEFT JOIN jadwaldokter_m ON (((antrian_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id) AND (jadwaldokter_m.is_deleted = false) AND (jadwaldokter_m.is_active = true))))
     LEFT JOIN jadwalbukapoli_m ON (((jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id) AND (jadwalbukapoli_m.is_deleted = false))))
     LEFT JOIN instalasi_m instalasi ON ((antrian_t.instalasi_id = instalasi.instalasi_id)))
     LEFT JOIN pendaftaran_t ON ((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pendaftaranol_t ON ((antrian_t.antrian_id = pendaftaranol_t.antrian_id)))
     LEFT JOIN jenisantriandetail_m ON ((antrian_t.jenisantriandetail_id = jenisantriandetail_m.jenisantriandetail_id)));");
        
        $this->execute("
            CREATE VIEW \"public\".\"worklistresep_v\" AS  SELECT reseptur_t.noresep AS no_reseptur,
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
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar
   FROM ((((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
  WHERE (ruangan_asal.instalasi_id = 1)
  GROUP BY reseptur_t.noresep, penjualanresep_t.noresep, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, pegawai_m.nama_pegawai, reseptur_t.status_worklist, ruangan_asal.instalasi_id, penjualanresep_t.tglpenjualan, penjualanresep_t.status_bayar
UNION ALL
 SELECT reseptur_t.noresep AS no_reseptur,
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
    NULL::character varying AS status_bayar
   FROM ((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
  WHERE (ruangan_asal.instalasi_id <> 1)
UNION ALL
 SELECT NULL::character varying AS no_reseptur,
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
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar
   FROM ((((penjualanresep_t
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
  WHERE ((penjualanresep_t.jenispenjualan)::text <> '344'::text);");
        
        $this->execute("
            CREATE VIEW \"public\".\"worklistresepdetail_v\" AS  SELECT reseptur_t.noresep AS no_reseptur,
    reseptur_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    signaobat_m.signa_nama AS signa,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket
   FROM ((((((reseptur_t
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
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
        
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200610_030909_migrate_20200610 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200610_030909_migrate_20200610 cannot be reverted.\n";

        return false;
    }
    */
}
