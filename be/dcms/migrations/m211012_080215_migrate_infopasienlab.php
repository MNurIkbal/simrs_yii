<?php

use yii\db\Migration;

/**
 * Class m211012_080215_migrate_infopasienlab
 */
class m211012_080215_migrate_infopasienlab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW if exists "public"."infopasienlab_v";');
    
    $this->execute("
        CREATE VIEW \"public\".\"infopasienlab_v\" AS  SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
        CASE
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text
            ELSE NULL::text
        END AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
    COALESCE(tindakanpelayanan.status, 'Batal'::text) AS status_bayar,
        CASE
            WHEN tindakanpelayanan.status = 'Sudah Bayar'::text THEN true
            ELSE false
        END AS is_status_bayar,
        CASE
            WHEN pasienmasukpenunjang_t.is_bayar = true THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar_detail,
    COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
    fgetkodelookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin_kode,
    pegawai_m.tanda_tangan
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pasienadmisi_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_integrasi_t.order_no
           FROM hasilpemeriksaanlab_integrasi_t
          GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Sudah Bayar'::text
                    ELSE 'Batal'::text
                END AS status
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id, (
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Sudah Bayar'::text
                    ELSE 'Batal'::text
                END)) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.cyto_tindakan = true AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.is_active = true) cyto_tindakan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pendaftaran_t.is_indolab = false
UNION ALL
 SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
        CASE
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text
            ELSE NULL::text
        END AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
    COALESCE(tindakanpelayanan.status, 'Batal'::text) AS status_bayar,
        CASE
            WHEN tindakanpelayanan.status = 'Sudah Bayar'::text THEN true
            ELSE false
        END AS is_status_bayar,
        CASE
            WHEN pasienmasukpenunjang_t.is_bayar = true THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar_detail,
    COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
    fgetkodelookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin_kode,
    pegawai_m.tanda_tangan
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_integrasi_t.order_no
           FROM hasilpemeriksaanlab_integrasi_t
          GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Sudah Bayar'::text
                    ELSE 'Batal'::text
                END AS status
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id, (
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Sudah Bayar'::text
                    ELSE 'Batal'::text
                END)) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.cyto_tindakan = true AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.is_active = true) cyto_tindakan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL AND pendaftaran_t.is_indolab = false
UNION ALL
 SELECT 'RUJUKAN RS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    rujukan_t.no_rujukan,
    rujukan_t.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    rujukan_t.rujukandari_id AS ruanganasal_id,
    perujuk_m.namaperujuk AS ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
        CASE
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text
            ELSE NULL::text
        END AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
    COALESCE(tindakanpelayanan.status, 'Batal'::text) AS status_bayar,
        CASE
            WHEN tindakanpelayanan.status = 'Sudah Bayar'::text THEN true
            ELSE false
        END AS is_status_bayar,
        CASE
            WHEN pasienmasukpenunjang_t.is_bayar = true THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar_detail,
    COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
    fgetkodelookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin_kode,
    pegawai_m.tanda_tangan
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_integrasi_t.order_no
           FROM hasilpemeriksaanlab_integrasi_t
          GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Sudah Bayar'::text
                    ELSE 'Batal'::text
                END AS status
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id, (
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Sudah Bayar'::text
                    ELSE 'Batal'::text
                END)) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.cyto_tindakan = true AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.is_active = true) cyto_tindakan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id
  WHERE pendaftaran_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pendaftaran_t.is_aps = false AND pendaftaran_t.is_indolab = false
UNION ALL
 SELECT 'APS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    NULL::character varying AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    'APS'::character varying AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
        CASE
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text
            ELSE NULL::text
        END AS received_flag,
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
        CASE
            WHEN hasil_manual.hasil > 0 THEN true
            ELSE false
        END AS is_hasil_manual,
    pasienmasukpenunjang_t.additional_data,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) > 0 THEN true
            ELSE false
        END AS is_hasil_bridging,
        CASE
            WHEN tindakanpelayanan.pendaftaran_id IS NULL THEN 'Belum Bayar'::text
            ELSE COALESCE(tindakanpelayanan.status, 'Batal'::text)
        END AS status_bayar,
        CASE
            WHEN tindakanpelayanan.status = 'Sudah Bayar'::text THEN true
            ELSE false
        END AS is_status_bayar,
        CASE
            WHEN pasienmasukpenunjang_t.is_bayar = true THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar_detail,
    COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
    fgetkodelookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin_kode,
    pegawai_m.tanda_tangan
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
     LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
           FROM hasilpemeriksaanlabdetail_t
             JOIN hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_integrasi_t.order_no
           FROM hasilpemeriksaanlab_integrasi_t
          GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Sudah Bayar'::text
                    ELSE 'Batal'::text
                END AS status
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pendaftaran_id, (
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Sudah Bayar'::text
                    ELSE 'Batal'::text
                END)) tindakanpelayanan ON pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.cyto_tindakan = true AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.is_active = true) cyto_tindakan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id
  WHERE pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL AND ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pendaftaran_t.is_indolab = false AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND
        CASE
            WHEN pendaftaran_t.carabayar_id <> 2 THEN pasienmasukpenunjang_t.is_bayar = true
            ELSE pasienmasukpenunjang_t.is_deleted IS FALSE
        END;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211012_080215_migrate_infopasienlab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_080215_migrate_infopasienlab cannot be reverted.\n";

        return false;
    }
    */
}
