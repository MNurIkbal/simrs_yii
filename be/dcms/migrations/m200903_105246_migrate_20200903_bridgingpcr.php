<?php

use yii\db\Migration;

/**
 * Class m200903_105246_migrate_20200903_bridgingpcr
 */
class m200903_105246_migrate_20200903_bridgingpcr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {       
        $this->execute('DROP VIEW if exists "public"."bridgingpcr_v";');

        $this->execute("
            CREATE VIEW \"public\".\"bridgingpcr_v\" AS  SELECT
        CASE
            WHEN (pasien_m.profilrs_id IS NULL) THEN '01'::text
            ELSE '01'::text
        END AS hospitalsitecode,
        CASE
            WHEN (pasien_m.profilrs_id IS NULL) THEN 'MHKN'::text
            ELSE 'MHKN'::text
        END AS hospitalname,
    pendaftaran_t.pendaftaran_id AS admissionid,
    pendaftaran_t.no_pendaftaran AS admissionno,
    pendaftaran_t.tgl_pendaftaran AS admissiondate,
        CASE
            WHEN (pendaftaran_t.instalasi_id = 1) THEN 'OP'::text
            WHEN (pendaftaran_t.instalasi_id = 2) THEN 'Emergency'::text
            WHEN (pendaftaran_t.instalasi_id = 3) THEN 'IP'::text
            WHEN (pendaftaran_t.instalasi_id = 21) THEN 'MCU'::text
            ELSE 'Tidak Terdefinisikan'::text
        END AS patient_type,
    pasien_m.no_rekam_medik AS mrid,
    nama_depan.lookup_name AS title,
    pasien_m.nama_pasien AS patientname,
    pasien_m.jeniskelamin AS genderid,
    jenis_kelamin.lookup_kode AS gendercode,
    jenis_kelamin.lookup_name AS gendername,
    (pasien_m.tanggal_lahir)::timestamp without time zone AS dateofbirth,
    pasien_m.no_identitas_pasien AS idcardno,
    pasien_m.alamat_pasien AS patientaddress,
    pasien_m.alamatemail AS emailaddress,
    pasien_m.no_mobile_pasien AS mobilephoneno,
        CASE
            WHEN (pendaftaran_t.carabayar_id = 5) THEN 1
            ELSE 2
        END AS payertypeid,
    asuransipasien_m.nokartuasuransi AS payertypecode,
    penjamin_m.penjamin_nama AS payername,
    tandabuktibayar_t.tglbuktibayar AS billdate,
    tandabuktibayar_t.nobuktibayar AS billno,
    tandabuktibayar_t.created_date AS createddate,
    tandabuktibayar_t.created_by AS createdby,
    tandabuktibayar_t.last_modified_date AS updateddate,
    tandabuktibayar_t.last_modified_by AS updatedby,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nomorindukpegawai
            ELSE dpjp_ranap.nomorindukpegawai
        END AS kodedokter_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
            ELSE dpjp_ranap.nama_pegawai
        END AS namadokter_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN gelarbelakang_m.gelarbelakang_id
            ELSE gelar_dpjp_ranap.gelarbelakang_id
        END AS specialisecode_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelaspelayanan_m.kelaspelayanan_nama
            ELSE kelas_ranap.kelaspelayanan_nama
        END AS billable_class,
    pembayaran_t.total_tagihan AS grandtotalgrossbillamount,
    pembayaran_t.total_pembulatan AS totalroundoffamount,
    pembayaran_t.total_administrasi AS totaladminchargeamount,
    pembayaran_t.penggunaan_uangmuka AS totaldepositamount,
    pengembalianuangmuka_t.total_pengembalian AS totaldepositrefundamount,
    pembayaran_t.total_ditagihkan AS grandtotalnetbillamount,
    pembayaran_t.total_ditagihkan AS totalnetpatientamount,
    pembayaran_t.total_dijamin AS totalnetcompanyamount,
    0 AS totalpayerdiscountamount,
    pembayaran_t.total_discount AS totalpatientdiscountamount,
    0 AS totalpatientcancelamount,
    0 AS totalpayercancelamount,
    pembayaran_t.total_dibayar AS totalpatientreceiptamount,
    pembayaran_t.total_dijamin AS totalcompanyreceiptamount,
    pembayaran_t.total_kembalian AS totalpatientrefundamount,
    0 AS totalcompanyrefundamount,
    pembayaran_t.catatan AS billremarks,
        CASE
            WHEN (pembayaran_t.is_deleted IS TRUE) THEN 1
            ELSE 0
        END AS cancelbillstatus,
    NULL::text AS cancelbillreason,
        CASE
            WHEN (pembayaran_t.is_deleted IS TRUE) THEN pembayaran_t.deleted_date
            ELSE NULL::timestamp without time zone
        END AS cancelbilldate,
    NULL::text AS discountreason,
        CASE
            WHEN (pembayaran_t.total_discount > (0)::double precision) THEN pembayaran_t.created_date
            ELSE NULL::timestamp without time zone
        END AS discountdate,
        CASE
            WHEN (pembayaran_t.deleted_date IS NOT NULL) THEN 3
            ELSE 2
        END AS recordtype,
    pembayaran_t.created_date AS logdate
   FROM (((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN gelarbelakang_m ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN lookup_m nama_depan ON (((pasien_m.namadepan)::integer = nama_depan.lookup_id)))
     JOIN lookup_m jenis_kelamin ON (((pasien_m.jeniskelamin)::integer = jenis_kelamin.lookup_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     LEFT JOIN tandabuktibayar_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pegawai_m dpjp_ranap ON ((pasienadmisi_t.pegawai_id = dpjp_ranap.pegawai_id)))
     LEFT JOIN gelarbelakang_m gelar_dpjp_ranap ON (((dpjp_ranap.gelarbelakang)::integer = gelar_dpjp_ranap.gelarbelakang_id)))
     LEFT JOIN kelaspelayanan_m kelas_ranap ON ((pasienadmisi_t.kelaspelayanan_id = kelas_ranap.kelaspelayanan_id)))
     LEFT JOIN returbayarpelayanan_t ON ((tandabuktibayar_t.returbayarpelayanan_id = returbayarpelayanan_t.returbayarpelayanan_id)))
     LEFT JOIN tandabuktikeluar_t ON ((tandabuktikeluar_t.returbayarpelayanan_id = returbayarpelayanan_t.returbayarpelayanan_id)))
     LEFT JOIN pengembalianuangmuka_t ON ((tandabuktikeluar_t.pengembalianuangmuka_id = pengembalianuangmuka_t.pengembalianuangmuka_id)));");

        $this->execute('ALTER TABLE "public"."bridgingpcr_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."bridgingpcrdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"bridgingpcrdetail_v\" AS  SELECT
        CASE
            WHEN (pasien_m.profilrs_id IS NULL) THEN '01'::text
            ELSE '01'::text
        END AS hospitalsitecode,
        CASE
            WHEN (pasien_m.profilrs_id IS NULL) THEN 'MHKN'::text
            ELSE 'MHKN'::text
        END AS hospitalname,
    pendaftaran_t.pendaftaran_id AS admissionid,
    pendaftaran_t.no_pendaftaran AS admissionno,
    pendaftaran_t.tgl_pendaftaran AS admissiondate,
        CASE
            WHEN (pendaftaran_t.instalasi_id = 1) THEN 'OP'::text
            WHEN (pendaftaran_t.instalasi_id = 2) THEN 'Emergency'::text
            WHEN (pendaftaran_t.instalasi_id = 3) THEN 'IP'::text
            WHEN (pendaftaran_t.instalasi_id = 21) THEN 'MCU'::text
            ELSE 'Tidak Terdefinisikan'::text
        END AS patient_type,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id AS productorderid,
    pasienmasukpenunjang_t.no_masukpenunjang AS productorderno,
    pasienmasukpenunjang_t.tglmasukpenunjang AS productorderdate,
    tindakanpelayanan_t.tindakanpelayanan_id AS productorderitemid,
    pasien_m.no_rekam_medik AS mrid,
    nama_depan.lookup_name AS title,
    pasien_m.nama_pasien AS patientname,
    pasien_m.jeniskelamin AS genderid,
    jenis_kelamin.lookup_kode AS gendercode,
    jenis_kelamin.lookup_name AS gendername,
    (pasien_m.tanggal_lahir)::timestamp without time zone AS dateofbirth,
    pasien_m.no_identitas_pasien AS idcardno,
    pasien_m.alamat_pasien AS patientaddress,
    pasien_m.alamatemail AS emailaddress,
    pasien_m.no_mobile_pasien AS mobilephoneno,
        CASE
            WHEN (pendaftaran_t.carabayar_id = 5) THEN 1
            ELSE 2
        END AS payertypeid,
    asuransipasien_m.nokartuasuransi AS payertypecode,
    penjamin_m.penjamin_nama AS payername,
    carabayar_m.carabayar_nama AS kelompokmargin,
    daftartindakan_m.daftartindakan_id AS id_layanan,
    daftartindakan_m.daftartindakan_kode AS kode_layanan,
    daftartindakan_m.daftartindakan_nama AS nama_layanan,
    kelompoktindakan_m.kelompoktindakan_kode AS kodekelompok_layanan,
    kelompoktindakan_m.kelompoktindakan_nama AS namakelompok_layanan,
    kategoritindakan_m.kategori_kode AS kodekategori_layanan,
    kategoritindakan_m.kategoritindakan_nama AS namakategori_layanan,
    groupinacbg_m.groupinacbg_kode AS kodegroupinacbgs,
    groupinacbg_m.groupinacbg_nama AS namagroupinacbgs,
    tindakanpelayanan_t.qty_tindakan AS qtyorder,
    ruangan_m.ruangan_nama AS uom,
    tindakanpelayanan_t.tarif_tindakan AS unitrate_patientamount,
    tindakanpelayanan_t.subsidiasuransi_tindakan AS unitrate_payeramount,
    NULL::character varying AS pharmacy_issuesalesno,
    NULL::timestamp without time zone AS pharmacy_issuesalesdate,
    NULL::integer AS pharmacy_issuestorecode,
    NULL::character varying AS pharmacy_issuestorename,
    NULL::character varying AS pharmacy_returnsalesno,
    NULL::timestamp without time zone AS pharmacy_returnsalesdate,
    NULL::character varying AS pharamcy_returnstorecode,
    NULL::character varying AS pharamcy_returnstorename,
    tandabuktibayar_t.tglbuktibayar AS billdate,
    tandabuktibayar_t.nobuktibayar AS billno,
    tandabuktibayar_t.created_date AS createddate,
    tandabuktibayar_t.created_by AS createdby,
    tandabuktibayar_t.last_modified_date AS updateddate,
    tandabuktibayar_t.last_modified_by AS updatedby,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nomorindukpegawai
            ELSE dpjp_ranap.nomorindukpegawai
        END AS kodedokter_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
            ELSE dpjp_ranap.nama_pegawai
        END AS namadokter_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN gelarbelakang_m.gelarbelakang_id
            ELSE gelar_dpjp_ranap.gelarbelakang_id
        END AS specialisecode_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN gelarbelakang_m.gelarbelakang_nama
            ELSE gelar_dpjp_ranap.gelarbelakang_nama
        END AS specialisename_dpjp,
    dokter_pengirim.nomorindukpegawai AS kodedokter_pengirim,
    dokter_pengirim.nama_pegawai AS namadokter_pengirim,
    gelar_dokter_pengirim.gelarbelakang_id AS specialisecode_pengirim,
    gelar_dokter_pengirim.gelarbelakang_nama AS specialisename_pengirim,
    ruangan_asal.ruangan_id AS kodelokasi_pengirim,
    ruangan_asal.ruangan_nama AS arealokasi_pengirim,
    dokter_perform.nomorindukpegawai AS kodedokter_perform,
    dokter_perform.nama_pegawai AS namadokter_perform,
    gelar_dokter_perform.gelarbelakang_id AS specialisecode_perform,
    gelar_dokter_perform.gelarbelakang_nama AS specialisename_perform,
    tipepaket_m.tipepaket_kode AS kodepaket,
    tipepaket_m.tipepaket_nama AS namapaket,
    NULL::text AS jenispaket,
        CASE
            WHEN (tindakanpelayanan_t.is_deleted IS TRUE) THEN 1
            ELSE 0
        END AS cancelitemstatus,
        CASE
            WHEN (tindakanpelayanan_t.is_deleted IS TRUE) THEN tindakanpelayanan_t.deleted_date
            ELSE NULL::timestamp without time zone
        END AS ascancelitemdate,
        CASE
            WHEN (pembayaran_t.deleted_date IS NOT NULL) THEN 3
            ELSE 2
        END AS recordtype,
    pembayaran_t.created_date AS logdate,
    NULL::character varying AS no_register_wynacom
   FROM ((((((((((((((((((((((((((((((((((tindakanpelayanan_t
     LEFT JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_asal ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_asal.ruangan_id)))
     JOIN pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN gelarbelakang_m ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN lookup_m nama_depan ON (((pasien_m.namadepan)::integer = nama_depan.lookup_id)))
     JOIN lookup_m jenis_kelamin ON (((pasien_m.jeniskelamin)::integer = jenis_kelamin.lookup_id)))
     LEFT JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN pegawai_m dokter_perform ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = dokter_perform.pegawai_id)))
     LEFT JOIN gelarbelakang_m gelar_dokter_perform ON (((dokter_perform.gelarbelakang)::integer = gelar_dokter_perform.gelarbelakang_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
     JOIN kategoritindakan_m ON ((daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id)))
     LEFT JOIN groupinacbg_m ON ((daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id)))
     LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     LEFT JOIN pegawai_m dokter_pengirim ON ((pasienkirimkeunitlain_t.pegawai_id = dokter_pengirim.pegawai_id)))
     LEFT JOIN gelarbelakang_m gelar_dokter_pengirim ON (((dokter_pengirim.gelarbelakang)::integer = gelar_dokter_pengirim.gelarbelakang_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     LEFT JOIN tandabuktibayar_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pegawai_m dpjp_ranap ON ((pasienadmisi_t.pegawai_id = dpjp_ranap.pegawai_id)))
     LEFT JOIN gelarbelakang_m gelar_dpjp_ranap ON (((dpjp_ranap.gelarbelakang)::integer = gelar_dpjp_ranap.gelarbelakang_id)))
     LEFT JOIN kelaspelayanan_m kelas_ranap ON ((pasienadmisi_t.kelaspelayanan_id = kelas_ranap.kelaspelayanan_id)))
     LEFT JOIN returbayarpelayanan_t ON ((tandabuktibayar_t.returbayarpelayanan_id = returbayarpelayanan_t.returbayarpelayanan_id)))
     LEFT JOIN tandabuktikeluar_t ON ((tandabuktikeluar_t.returbayarpelayanan_id = returbayarpelayanan_t.returbayarpelayanan_id)))
     LEFT JOIN pengembalianuangmuka_t ON ((tandabuktikeluar_t.pengembalianuangmuka_id = pengembalianuangmuka_t.pengembalianuangmuka_id)))
     LEFT JOIN ( SELECT DISTINCT hasilpemeriksaanlab_wynacom_t_1.his_reg_no AS no_masukpenunjang,
            hasilpemeriksaanlab_wynacom_t_1.lis_reg_no
           FROM hasilpemeriksaanlab_wynacom_t hasilpemeriksaanlab_wynacom_t_1) hasilpemeriksaanlab_wynacom_t ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasilpemeriksaanlab_wynacom_t.no_masukpenunjang)::text)))
UNION ALL
 SELECT
        CASE
            WHEN (pasien_m.profilrs_id IS NULL) THEN '01'::text
            ELSE '01'::text
        END AS hospitalsitecode,
        CASE
            WHEN (pasien_m.profilrs_id IS NULL) THEN 'MHKN'::text
            ELSE 'MHKN'::text
        END AS hospitalname,
    COALESCE(pendaftaran_t.pendaftaran_id, penjualanresep_t.penjualanresep_id) AS admissionid,
    COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS admissionno,
    COALESCE(pendaftaran_t.tgl_pendaftaran, penjualanresep_t.tglpenjualan) AS admissiondate,
        CASE
            WHEN (pendaftaran_t.instalasi_id = 1) THEN 'OP'::text
            WHEN (pendaftaran_t.instalasi_id = 2) THEN 'Emergency'::text
            WHEN (pendaftaran_t.instalasi_id = 3) THEN 'IP'::text
            WHEN (pendaftaran_t.instalasi_id = 21) THEN 'MCU'::text
            ELSE 'Tidak Terdefinisikan'::text
        END AS patient_type,
    penjualanresep_t.penjualanresep_id AS productorderid,
    penjualanresep_t.noresep AS productorderno,
    penjualanresep_t.tglpenjualan AS productorderdate,
    obatalkespasien_t.obatalkespasien_id AS productorderitemid,
    pasien_m.no_rekam_medik AS mrid,
    nama_depan.lookup_name AS title,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN pasien_m.nama_pasien
            WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN penjualanresep_t.nama_pembeli
            WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN pegawai_m.nama_pegawai
            ELSE NULL::character varying
        END AS patientname,
    pasien_m.jeniskelamin AS genderid,
    jenis_kelamin.lookup_kode AS gendercode,
    jenis_kelamin.lookup_name AS gendername,
    (pasien_m.tanggal_lahir)::timestamp without time zone AS dateofbirth,
    pasien_m.no_identitas_pasien AS idcardno,
    pasien_m.alamat_pasien AS patientaddress,
    pasien_m.alamatemail AS emailaddress,
    pasien_m.no_mobile_pasien AS mobilephoneno,
        CASE
            WHEN (pendaftaran_t.carabayar_id = 5) THEN 1
            ELSE 2
        END AS payertypeid,
    asuransipasien_m.nokartuasuransi AS payertypecode,
    penjamin_m.penjamin_nama AS payername,
    carabayar_m.carabayar_nama AS kelompokmargin,
    NULL::integer AS id_layanan,
    NULL::character varying AS kode_layanan,
    NULL::character varying AS nama_layanan,
    NULL::character varying AS kodekelompok_layanan,
    NULL::character varying AS namakelompok_layanan,
    NULL::character varying AS kodekategori_layanan,
    NULL::character varying AS namakategori_layanan,
    NULL::character varying AS kodegroupinacbgs,
    NULL::character varying AS namagroupinacbgs,
    obatalkespasien_t.qty_oa AS qtyorder,
    ruangan_m.ruangan_nama AS uom,
        CASE carabayar_m.carabayar_id
            WHEN 5 THEN (obatalkespasien_t.hargajual_oa * obatalkespasien_t.qty_oa)
            ELSE (0)::double precision
        END AS unitrate_patientamount,
        CASE carabayar_m.carabayar_id
            WHEN 5 THEN (0)::double precision
            ELSE (obatalkespasien_t.hargajual_oa * obatalkespasien_t.qty_oa)
        END AS unitrate_payeramount,
    penjualanresep_t.noresep AS pharmacy_issuesalesno,
    penjualanresep_t.tglpenjualan AS pharmacy_issuesalesdate,
    ruangan_m.ruangan_id AS pharmacy_issuestorecode,
    ruangan_m.ruangan_nama AS pharmacy_issuestorename,
    NULL::character varying AS pharmacy_returnsalesno,
    NULL::timestamp without time zone AS pharmacy_returnsalesdate,
    NULL::character varying AS pharamcy_returnstorecode,
    NULL::character varying AS pharamcy_returnstorename,
    tandabuktibayar_t.tglbuktibayar AS billdate,
    tandabuktibayar_t.nobuktibayar AS billno,
    tandabuktibayar_t.created_date AS createddate,
    tandabuktibayar_t.created_by AS createdby,
    tandabuktibayar_t.last_modified_date AS updateddate,
    tandabuktibayar_t.last_modified_by AS updatedby,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nomorindukpegawai
            ELSE dpjp_ranap.nomorindukpegawai
        END AS kodedokter_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
            ELSE dpjp_ranap.nama_pegawai
        END AS namadokter_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN gelarbelakang_m.gelarbelakang_id
            ELSE gelar_dpjp_ranap.gelarbelakang_id
        END AS specialisecode_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN gelarbelakang_m.gelarbelakang_nama
            ELSE gelar_dpjp_ranap.gelarbelakang_nama
        END AS specialisename_dpjp,
    NULL::character varying AS kodedokter_pengirim,
    NULL::character varying AS namadokter_pengirim,
    NULL::integer AS specialisecode_pengirim,
    NULL::character varying AS specialisename_pengirim,
    ruangan_m.ruangan_id AS kodelokasi_pengirim,
    ruangan_m.ruangan_nama AS arealokasi_pengirim,
    NULL::character varying AS kodedokter_perform,
    NULL::character varying AS namadokter_perform,
    NULL::integer AS specialisecode_perform,
    NULL::character varying AS specialisename_perform,
    NULL::character varying AS kodepaket,
    NULL::character varying AS namapaket,
    NULL::text AS jenispaket,
        CASE
            WHEN (obatalkespasien_t.is_deleted IS TRUE) THEN 1
            ELSE 0
        END AS cancelitemstatus,
        CASE
            WHEN (obatalkespasien_t.is_deleted IS TRUE) THEN obatalkespasien_t.deleted_date
            ELSE NULL::timestamp without time zone
        END AS ascancelitemdate,
        CASE
            WHEN (pembayaranpelayanan_t.deleted_date IS NOT NULL) THEN 3
            ELSE 2
        END AS recordtype,
    pembayaranpelayanan_t.created_date AS logdate,
    NULL::character varying AS no_register_wynacom
   FROM ((((((((((((((((((((obatalkespasien_t
     JOIN stokobatalkes_t ON ((obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id)))
     JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     LEFT JOIN pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.karyawan_id = pegawai_m.pegawai_id)))
     LEFT JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN lookup_m nama_depan ON (((pasien_m.namadepan)::integer = nama_depan.lookup_id)))
     LEFT JOIN lookup_m jenis_kelamin ON (((pasien_m.jeniskelamin)::integer = jenis_kelamin.lookup_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id)))
     LEFT JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pegawai_m dpjp_ranap ON ((pasienadmisi_t.pegawai_id = dpjp_ranap.pegawai_id)))
     LEFT JOIN gelarbelakang_m ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
     LEFT JOIN gelarbelakang_m gelar_dpjp_ranap ON (((dpjp_ranap.gelarbelakang)::integer = gelar_dpjp_ranap.gelarbelakang_id)))
UNION ALL
 SELECT
        CASE
            WHEN (pasien_m.profilrs_id IS NULL) THEN '01'::text
            ELSE '01'::text
        END AS hospitalsitecode,
        CASE
            WHEN (pasien_m.profilrs_id IS NULL) THEN 'MHKN'::text
            ELSE 'MHKN'::text
        END AS hospitalname,
    pendaftaran_t.pendaftaran_id AS admissionid,
    pendaftaran_t.no_pendaftaran AS admissionno,
    pendaftaran_t.tgl_pendaftaran AS admissiondate,
        CASE
            WHEN (pendaftaran_t.instalasi_id = 1) THEN 'OP'::text
            WHEN (pendaftaran_t.instalasi_id = 2) THEN 'Emergency'::text
            WHEN (pendaftaran_t.instalasi_id = 3) THEN 'IP'::text
            WHEN (pendaftaran_t.instalasi_id = 21) THEN 'MCU'::text
            ELSE 'Tidak Terdefinisikan'::text
        END AS patient_type,
    obatalkespasien_t.obatalkespasien_id AS productorderid,
    'BMHP'::character varying AS productorderno,
    obatalkespasien_t.tglpelayanan AS productorderdate,
    tindakanpelayanan_t.tindakanpelayanan_id AS productorderitemid,
    pasien_m.no_rekam_medik AS mrid,
    nama_depan.lookup_name AS title,
    pasien_m.nama_pasien AS patientname,
    pasien_m.jeniskelamin AS genderid,
    jenis_kelamin.lookup_kode AS gendercode,
    jenis_kelamin.lookup_name AS gendername,
    (pasien_m.tanggal_lahir)::timestamp without time zone AS dateofbirth,
    pasien_m.no_identitas_pasien AS idcardno,
    pasien_m.alamat_pasien AS patientaddress,
    pasien_m.alamatemail AS emailaddress,
    pasien_m.no_mobile_pasien AS mobilephoneno,
        CASE
            WHEN (pendaftaran_t.carabayar_id = 5) THEN 1
            ELSE 2
        END AS payertypeid,
    asuransipasien_m.nokartuasuransi AS payertypecode,
    penjamin_m.penjamin_nama AS payername,
    carabayar_m.carabayar_nama AS kelompokmargin,
    NULL::integer AS id_layanan,
    NULL::character varying AS kode_layanan,
    NULL::character varying AS nama_layanan,
    NULL::character varying AS kodekelompok_layanan,
    NULL::character varying AS namakelompok_layanan,
    NULL::character varying AS kodekategori_layanan,
    NULL::character varying AS namakategori_layanan,
    NULL::character varying AS kodegroupinacbgs,
    NULL::character varying AS namagroupinacbgs,
    obatalkespasien_t.qty_oa AS qtyorder,
    ruangan_m.ruangan_nama AS uom,
        CASE carabayar_m.carabayar_id
            WHEN 5 THEN (obatalkespasien_t.hargajual_oa * obatalkespasien_t.qty_oa)
            ELSE (0)::double precision
        END AS unitrate_patientamount,
        CASE carabayar_m.carabayar_id
            WHEN 5 THEN (0)::double precision
            ELSE (obatalkespasien_t.hargajual_oa * obatalkespasien_t.qty_oa)
        END AS unitrate_payeramount,
    'BMHP'::character varying AS pharmacy_issuesalesno,
    obatalkespasien_t.tglpelayanan AS pharmacy_issuesalesdate,
    ruangan_m.ruangan_id AS pharmacy_issuestorecode,
    ruangan_m.ruangan_nama AS pharmacy_issuestorename,
    NULL::character varying AS pharmacy_returnsalesno,
    NULL::timestamp without time zone AS pharmacy_returnsalesdate,
    NULL::character varying AS pharamcy_returnstorecode,
    NULL::character varying AS pharamcy_returnstorename,
    tandabuktibayar_t.tglbuktibayar AS billdate,
    tandabuktibayar_t.nobuktibayar AS billno,
    tandabuktibayar_t.created_date AS createddate,
    tandabuktibayar_t.created_by AS createdby,
    tandabuktibayar_t.last_modified_date AS updateddate,
    tandabuktibayar_t.last_modified_by AS updatedby,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nomorindukpegawai
            ELSE dpjp_ranap.nomorindukpegawai
        END AS kodedokter_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
            ELSE dpjp_ranap.nama_pegawai
        END AS namadokter_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN gelarbelakang_m.gelarbelakang_id
            ELSE gelar_dpjp_ranap.gelarbelakang_id
        END AS specialisecode_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN gelarbelakang_m.gelarbelakang_nama
            ELSE gelar_dpjp_ranap.gelarbelakang_nama
        END AS specialisename_dpjp,
    NULL::character varying AS kodedokter_pengirim,
    NULL::character varying AS namadokter_pengirim,
    NULL::integer AS specialisecode_pengirim,
    NULL::character varying AS specialisename_pengirim,
    ruangan_m.ruangan_id AS kodelokasi_pengirim,
    ruangan_m.ruangan_nama AS arealokasi_pengirim,
    NULL::character varying AS kodedokter_perform,
    NULL::character varying AS namadokter_perform,
    NULL::integer AS specialisecode_perform,
    NULL::character varying AS specialisename_perform,
    NULL::character varying AS kodepaket,
    NULL::character varying AS namapaket,
    NULL::text AS jenispaket,
        CASE
            WHEN (obatalkespasien_t.is_deleted IS TRUE) THEN 1
            ELSE 0
        END AS cancelitemstatus,
        CASE
            WHEN (obatalkespasien_t.is_deleted IS TRUE) THEN obatalkespasien_t.deleted_date
            ELSE NULL::timestamp without time zone
        END AS ascancelitemdate,
        CASE
            WHEN (pembayaranpelayanan_t.deleted_date IS NOT NULL) THEN 3
            ELSE 2
        END AS recordtype,
    pembayaranpelayanan_t.created_date AS logdate,
    NULL::character varying AS no_register_wynacom
   FROM ((((((((((((((((((((obatalkespasien_t
     JOIN tindakanpelayanan_t ON ((obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN stokobatalkes_t ON ((obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     JOIN pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN lookup_m nama_depan ON (((pasien_m.namadepan)::integer = nama_depan.lookup_id)))
     LEFT JOIN lookup_m jenis_kelamin ON (((pasien_m.jeniskelamin)::integer = jenis_kelamin.lookup_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN penjamin_m ON ((obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id)))
     LEFT JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pegawai_m dpjp_ranap ON ((pasienadmisi_t.pegawai_id = dpjp_ranap.pegawai_id)))
     LEFT JOIN pegawai_m ON ((obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN gelarbelakang_m ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
     LEFT JOIN gelarbelakang_m gelar_dpjp_ranap ON (((dpjp_ranap.gelarbelakang)::integer = gelar_dpjp_ranap.gelarbelakang_id)))
UNION ALL
 SELECT
        CASE
            WHEN (pasien_m.profilrs_id IS NULL) THEN '01'::text
            ELSE '01'::text
        END AS hospitalsitecode,
        CASE
            WHEN (pasien_m.profilrs_id IS NULL) THEN 'MHKN'::text
            ELSE 'MHKN'::text
        END AS hospitalname,
    pendaftaran_t.pendaftaran_id AS admissionid,
    pendaftaran_t.no_pendaftaran AS admissionno,
    pendaftaran_t.tgl_pendaftaran AS admissiondate,
        CASE
            WHEN (pendaftaran_t.instalasi_id = 1) THEN 'OP'::text
            WHEN (pendaftaran_t.instalasi_id = 2) THEN 'Emergency'::text
            WHEN (pendaftaran_t.instalasi_id = 3) THEN 'IP'::text
            WHEN (pendaftaran_t.instalasi_id = 21) THEN 'MCU'::text
            ELSE 'Tidak Terdefinisikan'::text
        END AS patient_type,
    obatalkespasien_t.obatalkespasien_id AS productorderid,
    'BMHP'::character varying AS productorderno,
    obatalkespasien_t.tglpelayanan AS productorderdate,
    stokobatalkes_t.stokobatalkes_id AS productorderitemid,
    pasien_m.no_rekam_medik AS mrid,
    nama_depan.lookup_name AS title,
    pasien_m.nama_pasien AS patientname,
    pasien_m.jeniskelamin AS genderid,
    jenis_kelamin.lookup_kode AS gendercode,
    jenis_kelamin.lookup_name AS gendername,
    (pasien_m.tanggal_lahir)::timestamp without time zone AS dateofbirth,
    pasien_m.no_identitas_pasien AS idcardno,
    pasien_m.alamat_pasien AS patientaddress,
    pasien_m.alamatemail AS emailaddress,
    pasien_m.no_mobile_pasien AS mobilephoneno,
        CASE
            WHEN (pendaftaran_t.carabayar_id = 5) THEN 1
            ELSE 2
        END AS payertypeid,
    asuransipasien_m.nokartuasuransi AS payertypecode,
    penjamin_m.penjamin_nama AS payername,
    carabayar_m.carabayar_nama AS kelompokmargin,
    NULL::integer AS id_layanan,
    NULL::character varying AS kode_layanan,
    NULL::character varying AS nama_layanan,
    NULL::character varying AS kodekelompok_layanan,
    NULL::character varying AS namakelompok_layanan,
    NULL::character varying AS kodekategori_layanan,
    NULL::character varying AS namakategori_layanan,
    NULL::character varying AS kodegroupinacbgs,
    NULL::character varying AS namagroupinacbgs,
    obatalkespasien_t.qty_oa AS qtyorder,
    ruangan_m.ruangan_nama AS uom,
        CASE carabayar_m.carabayar_id
            WHEN 5 THEN (obatalkespasien_t.hargajual_oa * obatalkespasien_t.qty_oa)
            ELSE (0)::double precision
        END AS unitrate_patientamount,
        CASE carabayar_m.carabayar_id
            WHEN 5 THEN (0)::double precision
            ELSE (obatalkespasien_t.hargajual_oa * obatalkespasien_t.qty_oa)
        END AS unitrate_payeramount,
    'BMHP'::character varying AS pharmacy_issuesalesno,
    obatalkespasien_t.tglpelayanan AS pharmacy_issuesalesdate,
    ruangan_m.ruangan_id AS pharmacy_issuestorecode,
    ruangan_m.ruangan_nama AS pharmacy_issuestorename,
    NULL::character varying AS pharmacy_returnsalesno,
    NULL::timestamp without time zone AS pharmacy_returnsalesdate,
    NULL::character varying AS pharamcy_returnstorecode,
    NULL::character varying AS pharamcy_returnstorename,
    tandabuktibayar_t.tglbuktibayar AS billdate,
    tandabuktibayar_t.nobuktibayar AS billno,
    tandabuktibayar_t.created_date AS createddate,
    tandabuktibayar_t.created_by AS createdby,
    tandabuktibayar_t.last_modified_date AS updateddate,
    tandabuktibayar_t.last_modified_by AS updatedby,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nomorindukpegawai
            ELSE dpjp_ranap.nomorindukpegawai
        END AS kodedokter_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
            ELSE dpjp_ranap.nama_pegawai
        END AS namadokter_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN gelarbelakang_m.gelarbelakang_id
            ELSE gelar_dpjp_ranap.gelarbelakang_id
        END AS specialisecode_dpjp,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN gelarbelakang_m.gelarbelakang_nama
            ELSE gelar_dpjp_ranap.gelarbelakang_nama
        END AS specialisename_dpjp,
    NULL::character varying AS kodedokter_pengirim,
    NULL::character varying AS namadokter_pengirim,
    NULL::integer AS specialisecode_pengirim,
    NULL::character varying AS specialisename_pengirim,
    ruangan_m.ruangan_id AS kodelokasi_pengirim,
    ruangan_m.ruangan_nama AS arealokasi_pengirim,
    NULL::character varying AS kodedokter_perform,
    NULL::character varying AS namadokter_perform,
    NULL::integer AS specialisecode_perform,
    NULL::character varying AS specialisename_perform,
    NULL::character varying AS kodepaket,
    NULL::character varying AS namapaket,
    NULL::text AS jenispaket,
        CASE
            WHEN (obatalkespasien_t.is_deleted IS TRUE) THEN 1
            ELSE 0
        END AS cancelitemstatus,
        CASE
            WHEN (obatalkespasien_t.is_deleted IS TRUE) THEN obatalkespasien_t.deleted_date
            ELSE NULL::timestamp without time zone
        END AS ascancelitemdate,
        CASE
            WHEN (pembayaranpelayanan_t.deleted_date IS NOT NULL) THEN 3
            ELSE 2
        END AS recordtype,
    pembayaranpelayanan_t.created_date AS logdate,
    NULL::character varying AS no_register_wynacom
   FROM (((((((((((((((((((obatalkespasien_t
     JOIN stokobatalkes_t ON ((obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     JOIN pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN lookup_m nama_depan ON (((pasien_m.namadepan)::integer = nama_depan.lookup_id)))
     LEFT JOIN lookup_m jenis_kelamin ON (((pasien_m.jeniskelamin)::integer = jenis_kelamin.lookup_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN penjamin_m ON ((obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id)))
     LEFT JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pegawai_m dpjp_ranap ON ((pasienadmisi_t.pegawai_id = dpjp_ranap.pegawai_id)))
     LEFT JOIN pegawai_m ON ((obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN gelarbelakang_m ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
     LEFT JOIN gelarbelakang_m gelar_dpjp_ranap ON (((dpjp_ranap.gelarbelakang)::integer = gelar_dpjp_ranap.gelarbelakang_id)))
  WHERE ((obatalkespasien_t.tindakanpelayanan_id IS NULL) AND (obatalkespasien_t.penjualanresep_id IS NULL))
UNION ALL
 SELECT '01'::text AS hospitalsitecode,
    'MHKN'::text AS hospitalname,
    NULL::integer AS admissionid,
    NULL::character varying AS admissionno,
    NULL::timestamp without time zone AS admissiondate,
    'Tidak Terdefinisikan'::text AS patient_type,
    pemakaianobat_t.pemakaianobat_id AS productorderid,
    pemakaianobat_t.nopemakaian_obat AS productorderno,
    pemakaianobat_t.tglpemakaianobat AS productorderdate,
    stokobatalkes_t.stokobatalkes_id AS productorderitemid,
    NULL::character varying AS mrid,
    NULL::character varying AS title,
    pegawai_m.nama_pegawai AS patientname,
    pegawai_m.jeniskelamin AS genderid,
    jenis_kelamin.lookup_kode AS gendercode,
    jenis_kelamin.lookup_name AS gendername,
    (pegawai_m.tgl_lahirpegawai)::timestamp without time zone AS dateofbirth,
    pegawai_m.noidentitas AS idcardno,
    pegawai_m.alamat_pegawai AS patientaddress,
    pegawai_m.alamatemail AS emailaddress,
    pegawai_m.notelp_pegawai AS mobilephoneno,
    1 AS payertypeid,
    NULL::character varying AS payertypecode,
    'Umum'::character varying AS payername,
    'Umum'::character varying AS kelompokmargin,
    NULL::integer AS id_layanan,
    NULL::character varying AS kode_layanan,
    NULL::character varying AS nama_layanan,
    NULL::character varying AS kodekelompok_layanan,
    NULL::character varying AS namakelompok_layanan,
    NULL::character varying AS kodekategori_layanan,
    NULL::character varying AS namakategori_layanan,
    NULL::character varying AS kodegroupinacbgs,
    NULL::character varying AS namagroupinacbgs,
    pemakaianobatdetail_t.qty_satuanpakai AS qtyorder,
    ruangan_m.ruangan_nama AS uom,
    ((pemakaianobatdetail_t.qty_satuanpakai)::double precision * stokobatalkes_t.harganetto) AS unitrate_patientamount,
    0 AS unitrate_payeramount,
    pemakaianobat_t.nopemakaian_obat AS pharmacy_issuesalesno,
    pemakaianobat_t.tglpemakaianobat AS pharmacy_issuesalesdate,
    ruangan_m.ruangan_id AS pharmacy_issuestorecode,
    ruangan_m.ruangan_nama AS pharmacy_issuestorename,
    NULL::character varying AS pharmacy_returnsalesno,
    NULL::timestamp without time zone AS pharmacy_returnsalesdate,
    NULL::character varying AS pharamcy_returnstorecode,
    NULL::character varying AS pharamcy_returnstorename,
    NULL::timestamp without time zone AS billdate,
    NULL::character varying AS billno,
    NULL::timestamp without time zone AS createddate,
    NULL::integer AS createdby,
    NULL::timestamp without time zone AS updateddate,
    NULL::integer AS updatedby,
    NULL::character varying AS kodedokter_dpjp,
    NULL::character varying AS namadokter_dpjp,
    NULL::integer AS specialisecode_dpjp,
    NULL::character varying AS specialisename_dpjp,
    NULL::character varying AS kodedokter_pengirim,
    NULL::character varying AS namadokter_pengirim,
    NULL::integer AS specialisecode_pengirim,
    NULL::character varying AS specialisename_pengirim,
    ruangan_m.ruangan_id AS kodelokasi_pengirim,
    ruangan_m.ruangan_nama AS arealokasi_pengirim,
    NULL::character varying AS kodedokter_perform,
    NULL::character varying AS namadokter_perform,
    NULL::integer AS specialisecode_perform,
    NULL::character varying AS specialisename_perform,
    NULL::character varying AS kodepaket,
    NULL::character varying AS namapaket,
    NULL::text AS jenispaket,
        CASE
            WHEN (pemakaianobat_t.is_deleted IS TRUE) THEN 1
            ELSE 0
        END AS cancelitemstatus,
        CASE
            WHEN (pemakaianobat_t.is_deleted IS TRUE) THEN pemakaianobat_t.deleted_date
            ELSE NULL::timestamp without time zone
        END AS ascancelitemdate,
        CASE
            WHEN (pemakaianobat_t.deleted_date IS NOT NULL) THEN 3
            ELSE 2
        END AS recordtype,
    pemakaianobat_t.created_date AS logdate,
    NULL::character varying AS no_register_wynacom
   FROM (((((((((pemakaianobat_t
     JOIN pemakaianobatdetail_t ON ((pemakaianobat_t.pemakaianobat_id = pemakaianobatdetail_t.pemakaianobat_id)))
     JOIN stokobatalkes_t ON ((pemakaianobatdetail_t.pemakaianobatdetail_id = stokobatalkes_t.pemakaianobatdetail_id)))
     JOIN obatalkes_m ON ((pemakaianobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     JOIN pegawai_m ON ((pemakaianobat_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN ruangan_m ON ((pemakaianobat_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN lookup_m jenis_kelamin ON (((pegawai_m.jeniskelamin)::integer = jenis_kelamin.lookup_id)))
     LEFT JOIN gelarbelakang_m ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
UNION ALL
 SELECT '01'::text AS hospitalsitecode,
    'MHKN'::text AS hospitalname,
    NULL::integer AS admissionid,
    NULL::character varying AS admissionno,
    NULL::timestamp without time zone AS admissiondate,
    'Tidak Terdefinisikan'::text AS patient_type,
    adjusmenobatkeluar_t.adjusmenobat_id AS productorderid,
    adjusmenobat_t.no_adjusmen AS productorderno,
    adjusmenobat_t.tgl_adjusmen AS productorderdate,
    adjusmenobat_t.adjusmenobat_id AS productorderitemid,
    NULL::character varying AS mrid,
    NULL::character varying AS title,
    pegawai_m.nama_pegawai AS patientname,
    pegawai_m.jeniskelamin AS genderid,
    jenis_kelamin.lookup_kode AS gendercode,
    jenis_kelamin.lookup_name AS gendername,
    (pegawai_m.tgl_lahirpegawai)::timestamp without time zone AS dateofbirth,
    pegawai_m.noidentitas AS idcardno,
    pegawai_m.alamat_pegawai AS patientaddress,
    pegawai_m.alamatemail AS emailaddress,
    pegawai_m.notelp_pegawai AS mobilephoneno,
    1 AS payertypeid,
    NULL::character varying AS payertypecode,
    'Umum'::character varying AS payername,
    'Umum'::character varying AS kelompokmargin,
    NULL::integer AS id_layanan,
    NULL::character varying AS kode_layanan,
    NULL::character varying AS nama_layanan,
    NULL::character varying AS kodekelompok_layanan,
    NULL::character varying AS namakelompok_layanan,
    NULL::character varying AS kodekategori_layanan,
    NULL::character varying AS namakategori_layanan,
    NULL::character varying AS kodegroupinacbgs,
    NULL::character varying AS namagroupinacbgs,
    adjusmenobatkeluar_t.qty_konversi AS qtyorder,
    ruangan_m.ruangan_nama AS uom,
    ((adjusmenobatkeluar_t.qty_konversi)::double precision * stokobatalkes_t.harganetto) AS unitrate_patientamount,
    0 AS unitrate_payeramount,
    adjusmenobat_t.no_adjusmen AS pharmacy_issuesalesno,
    adjusmenobat_t.tgl_adjusmen AS pharmacy_issuesalesdate,
    ruangan_m.ruangan_id AS pharmacy_issuestorecode,
    ruangan_m.ruangan_nama AS pharmacy_issuestorename,
    NULL::character varying AS pharmacy_returnsalesno,
    NULL::timestamp without time zone AS pharmacy_returnsalesdate,
    NULL::character varying AS pharamcy_returnstorecode,
    NULL::character varying AS pharamcy_returnstorename,
    NULL::timestamp without time zone AS billdate,
    NULL::character varying AS billno,
    NULL::timestamp without time zone AS createddate,
    NULL::integer AS createdby,
    NULL::timestamp without time zone AS updateddate,
    NULL::integer AS updatedby,
    NULL::character varying AS kodedokter_dpjp,
    NULL::character varying AS namadokter_dpjp,
    NULL::integer AS specialisecode_dpjp,
    NULL::character varying AS specialisename_dpjp,
    NULL::character varying AS kodedokter_pengirim,
    NULL::character varying AS namadokter_pengirim,
    NULL::integer AS specialisecode_pengirim,
    NULL::character varying AS specialisename_pengirim,
    ruangan_m.ruangan_id AS kodelokasi_pengirim,
    ruangan_m.ruangan_nama AS arealokasi_pengirim,
    NULL::character varying AS kodedokter_perform,
    NULL::character varying AS namadokter_perform,
    NULL::integer AS specialisecode_perform,
    NULL::character varying AS specialisename_perform,
    NULL::character varying AS kodepaket,
    NULL::character varying AS namapaket,
    NULL::text AS jenispaket,
        CASE
            WHEN (adjusmenobat_t.is_deleted IS TRUE) THEN 1
            ELSE 0
        END AS cancelitemstatus,
        CASE
            WHEN (adjusmenobat_t.is_deleted IS TRUE) THEN adjusmenobat_t.deleted_date
            ELSE NULL::timestamp without time zone
        END AS ascancelitemdate,
        CASE
            WHEN (adjusmenobat_t.deleted_date IS NOT NULL) THEN 3
            ELSE 2
        END AS recordtype,
    adjusmenobat_t.created_date AS logdate,
    NULL::character varying AS no_register_wynacom
   FROM (((((((((adjusmenobat_t
     JOIN adjusmenobatkeluar_t ON ((adjusmenobat_t.adjusmenobat_id = adjusmenobatkeluar_t.adjusmenobat_id)))
     JOIN stokobatalkes_t ON ((adjusmenobatkeluar_t.adjusmenobatkeluar_id = stokobatalkes_t.adjusmenobatkeluar_id)))
     JOIN ruangan_m ON ((stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN obatalkes_m ON ((adjusmenobatkeluar_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     JOIN pegawai_m ON ((adjusmenobat_t.peg_menyetujui_id = pegawai_m.pegawai_id)))
     LEFT JOIN lookup_m jenis_kelamin ON (((pegawai_m.jeniskelamin)::integer = jenis_kelamin.lookup_id)))
     LEFT JOIN gelarbelakang_m ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)));");

        $this->execute('ALTER TABLE "public"."bridgingpcrdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200903_105246_migrate_20200903_bridgingpcr cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200903_105246_migrate_20200903_bridgingpcr cannot be reverted.\n";

        return false;
    }
    */
}
