<?php

use yii\db\Migration;

/**
 * Class m220114_111807_migrate_hotfix_asuransi_penjamin
 */
class m220114_111807_migrate_hotfix_asuransi_penjamin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienlab_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasienlab_v\" AS
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
            fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status_periksa_nama,
            pasienmasukpenunjang_t.no_antrian,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_opd.carabayar_id
            ELSE penjamin_ipd.carabayar_id
            END AS carabayar_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_opd.carabayar_nama
            ELSE carabayar_ipd.carabayar_nama
            END AS carabayar_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
            END AS penjamin_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_opd.penjamin_nama
            ELSE penjamin_ipd.penjamin_nama
            END AS penjamin_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
            END AS kelaspelayanan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelas_opd.kelaspelayanan_nama
            ELSE kelas_ipd.kelaspelayanan_nama
            END AS kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
            pasien_m.tanggal_lahir,
            ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
            ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
            ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
            ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            pasienadmisi_t.pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            CASE
            WHEN ((pendaftaran_t.status_periksa)::integer = 628) THEN '472'::character varying
            WHEN ((pendaftaran_t.status_periksa)::integer = 2) THEN '471'::character varying
            WHEN ((pasienmasukpenunjang_t.status_periksa)::integer = 474) THEN '471'::character varying
            ELSE pasienkirimkeunitlain_t.status_penunjang
            END AS status_penunjang,
            pasienmasukpenunjang_t.tanggal_verifikasi,
            pendaftaran_t.instalasi_id,
            CASE
            WHEN (pasienmasukpenunjang_t.additional_data IS NOT NULL) THEN (((pasienmasukpenunjang_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
            END AS received_flag,
            pasienmasukpenunjang_t.is_hasil,
            pasien_m.alamat_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
            CASE
            WHEN (hasil_manual.hasil > 0) THEN true
            ELSE false
            END AS is_hasil_manual,
            pasienmasukpenunjang_t.additional_data,
            CASE
            WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
            ELSE false
            END AS is_hasil_bridging,
            COALESCE(tindakanpelayanan.status, 'Batal'::text) AS status_bayar,
            CASE
            WHEN (tindakanpelayanan.status = 'Sudah Bayar'::text) THEN true
            ELSE false
            END AS is_status_bayar,
            CASE
            WHEN (pasienmasukpenunjang_t.is_bayar = true) THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
            END AS status_bayar_detail,
            COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
            fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
            pegawai_m.tanda_tangan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
            FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
            FROM (tindakanpelayanan_t
            JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) = (tindakanpelayanan.status = 'Belum Bayar'::text)) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))
            UNION ALL
            SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
            FROM (((tindakanpelayanan_t
            JOIN paketpelayanan_mp ON (((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
            JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 4))))
            JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id) AND ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) = (tindakanpelayanan.status = 'Belum Bayar'::text)) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))) x) AS pemeriksaan
            FROM (((((((((((((((((((pasienmasukpenunjang_t
            JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
            JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
            LEFT JOIN penjamin_m penjamin_opd ON ((pendaftaran_t.penjamin_id = penjamin_opd.penjamin_id)))
            LEFT JOIN penjamin_m penjamin_ipd ON ((pasienadmisi_t.penjamin_id = penjamin_ipd.penjamin_id)))
            LEFT JOIN carabayar_m carabayar_opd ON ((penjamin_opd.carabayar_id = carabayar_opd.carabayar_id)))
            LEFT JOIN carabayar_m carabayar_ipd ON ((penjamin_ipd.carabayar_id = carabayar_ipd.carabayar_id)))
            LEFT JOIN kelaspelayanan_m kelas_opd ON ((pendaftaran_t.kelaspelayanan_id = kelas_opd.kelaspelayanan_id)))
            LEFT JOIN kelaspelayanan_m kelas_ipd ON ((pasienadmisi_t.kelaspelayanan_id = kelas_ipd.kelaspelayanan_id)))
            LEFT JOIN pegawai_m dokter_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dokter_perujuk.pegawai_id)))
            LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
            LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
            FROM (hasilpemeriksaanlabdetail_t
            JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
            GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
            LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_integrasi_t.order_no
            FROM hasilpemeriksaanlab_integrasi_t
            GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
            LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END AS status
            FROM tindakanpelayanan_t
            WHERE (tindakanpelayanan_t.is_deleted = false)
            GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
            LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
            FROM tindakanpelayanan_t
            WHERE ((tindakanpelayanan_t.cyto_tindakan = true) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true))) cyto_tindakan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id)))
            WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pendaftaran_t.is_indolab = false))
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
            fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status_periksa_nama,
            pasienmasukpenunjang_t.no_antrian,
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
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            NULL::integer AS pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienmasukpenunjang_t.tanggal_verifikasi,
            pendaftaran_t.instalasi_id,
            CASE
            WHEN (pasienmasukpenunjang_t.additional_data IS NOT NULL) THEN (((pasienmasukpenunjang_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
            END AS received_flag,
            pasienmasukpenunjang_t.is_hasil,
            pasien_m.alamat_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
            CASE
            WHEN (hasil_manual.hasil > 0) THEN true
            ELSE false
            END AS is_hasil_manual,
            pasienmasukpenunjang_t.additional_data,
            CASE
            WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
            ELSE false
            END AS is_hasil_bridging,
            COALESCE(tindakanpelayanan.status, 'Batal'::text) AS status_bayar,
            CASE
            WHEN (tindakanpelayanan.status = 'Sudah Bayar'::text) THEN true
            ELSE false
            END AS is_status_bayar,
            CASE
            WHEN (pasienmasukpenunjang_t.is_bayar = true) THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
            END AS status_bayar_detail,
            COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
            fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
            pegawai_m.tanda_tangan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
            FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
            FROM (tindakanpelayanan_t
            JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))
            UNION ALL
            SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
            FROM (((tindakanpelayanan_t
            JOIN paketpelayanan_mp ON (((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
            JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 4))))
            JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))) x) AS pemeriksaan
            FROM ((((((((((((((((pasienmasukpenunjang_t
            JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
            JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
            LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
            LEFT JOIN perujuk_m ON ((rujukan_t.rujukandari_id = perujuk_m.perujuk_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
            LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
            LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
            LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
            FROM (hasilpemeriksaanlabdetail_t
            JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
            GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
            LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_integrasi_t.order_no
            FROM hasilpemeriksaanlab_integrasi_t
            GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
            LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END AS status
            FROM tindakanpelayanan_t
            WHERE (tindakanpelayanan_t.is_deleted = false)
            GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
            LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
            FROM tindakanpelayanan_t
            WHERE ((tindakanpelayanan_t.cyto_tindakan = true) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true))) cyto_tindakan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id)))
            WHERE ((pendaftaran_t.instalasi_id = 4) AND (pendaftaran_t.is_aps = false) AND (pendaftaran_t.is_indolab = false))
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
            CASE
            WHEN ((pendaftaran_t.status_periksa)::integer = 628) THEN '476'::character varying
            ELSE pasienmasukpenunjang_t.status_periksa
            END AS status_periksa,
            CASE
            WHEN ((pendaftaran_t.status_periksa)::integer = 628) THEN 'BATAL'::character varying
            ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
            END AS status_periksa_nama,
            pasienmasukpenunjang_t.no_antrian,
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
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            NULL::integer AS pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            CASE
            WHEN ((pendaftaran_t.status_periksa)::integer = 628) THEN '472'::character varying
            WHEN ((pasienmasukpenunjang_t.status_periksa)::integer = 477) THEN '471'::character varying
            WHEN ((pendaftaran_t.status_periksa)::integer = 2) THEN '471'::character varying
            WHEN ((pasienmasukpenunjang_t.status_periksa)::integer = 474) THEN '471'::character varying
            WHEN ((pasienmasukpenunjang_t.status_periksa)::integer = 475) THEN '471'::character varying
            ELSE pasienkirimkeunitlain_t.status_penunjang
            END AS status_penunjang,
            pasienmasukpenunjang_t.tanggal_verifikasi,
            pendaftaran_t.instalasi_id,
            CASE
            WHEN (pasienmasukpenunjang_t.additional_data IS NOT NULL) THEN (((pasienmasukpenunjang_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
            END AS received_flag,
            pasienmasukpenunjang_t.is_hasil,
            pasien_m.alamat_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
            CASE
            WHEN (hasil_manual.hasil > 0) THEN true
            ELSE false
            END AS is_hasil_manual,
            pasienmasukpenunjang_t.additional_data,
            CASE
            WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
            ELSE false
            END AS is_hasil_bridging,
            CASE
            WHEN (tindakanpelayanan.pendaftaran_id IS NULL) THEN 'Belum Bayar'::text
            ELSE COALESCE(tindakanpelayanan.status, 'Batal'::text)
            END AS status_bayar,
            CASE
            WHEN (tindakanpelayanan.status = 'Sudah Bayar'::text) THEN true
            ELSE false
            END AS is_status_bayar,
            CASE
            WHEN (pasienmasukpenunjang_t.is_bayar = true) THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
            END AS status_bayar_detail,
            COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
            fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
            pegawai_m.tanda_tangan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
            FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
            FROM (tindakanpelayanan_t
            JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))
            UNION ALL
            SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
            FROM (((tindakanpelayanan_t
            JOIN paketpelayanan_mp ON (((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
            JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 4))))
            JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))) x) AS pemeriksaan
            FROM ((((((((((((((((pasienmasukpenunjang_t
            JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
            JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ruang_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id)))
            LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
            LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
            LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
            count(*) AS hasil
            FROM (hasilpemeriksaanlabdetail_t
            JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
            GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
            LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_integrasi_t.order_no
            FROM hasilpemeriksaanlab_integrasi_t
            GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
            LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END AS status
            FROM tindakanpelayanan_t
            WHERE (tindakanpelayanan_t.is_deleted = false)
            GROUP BY tindakanpelayanan_t.pendaftaran_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END) tindakanpelayanan ON ((pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan.pendaftaran_id)))
            LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
            FROM tindakanpelayanan_t
            WHERE ((tindakanpelayanan_t.cyto_tindakan = true) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true))) cyto_tindakan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id)))
            WHERE ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL) AND (ruang_penunjang.instalasi_id = 4) AND (pendaftaran_t.is_aps = true) AND (pendaftaran_t.is_indolab = false) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
            ;");
        $this->execute('
            ALTER TABLE public.infopasienlab_v OWNER TO postgres;
            ');

            $this->execute('DROP VIEW if exists public.pengajuanklaim_v;');
        $this->execute("
            CREATE VIEW \"public\".\"pengajuanklaim_v\" AS
            SELECT
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN 'RJ'::text
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN 'RJ'::text
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN 'RD'::text
            ELSE 'OTHER'::text
            END AS tipe,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 4)) THEN pulang_lab.tgl_hasilpemeriksaanlab
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 21)) THEN pendaftaran_t.tgl_pendaftaran
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pulang_rjrd.tglpasienpulang
            ELSE pulang_ri.tglpasienpulang
            END AS tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
            END AS ruangan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_rjrd.ruangan_nama
            ELSE ruangan_ri.ruangan_nama
            END AS ruangan_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_rjrd.instalasi_nama
            ELSE instalasi_ri.instalasi_nama
            END AS instalasi_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_rjrd.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
            END AS carabayar_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
            END AS penjamin_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_rjrd.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
            END AS penjamin_nama,
            pembayaran_t.no_pembayaran,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_dibayar AS total_sdh_bayar,
            pembayaran_t.total_sisatagihan AS total_sisa_tagihan,
            (pembayaran_t.total_dijamin)::integer AS total_asuransi,
            pendaftaran_t.is_skd,
            CASE
            WHEN ((pendaftaran_t.is_skd IS FALSE) OR (pendaftaran_t.is_skd IS NULL)) THEN 'Belum Dibuat'::text
            WHEN ((pendaftaran_t.is_skd IS TRUE) OR (pendaftaran_t.is_skd IS NOT NULL)) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END AS status_skd,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.bpjs_id
            ELSE pasienadmisi_t.bpjs_id
            END AS bpjs_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN bpjs_rjrd.nosep
            ELSE bpjs_ri.nosep
            END AS nosep,
            (pembayaran_t.total_dijamin)::integer AS jumlah_inacbg,
            pendaftaran_t.status_verifikasi,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaimdetail_t.pengajuanklaim_id,
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN (('RJ'::text || '-'::text) || pendaftaran_t.pendaftaran_id)
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (('RD'::text || '-'::text) || pendaftaran_t.pendaftaran_id)
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (('RI'::text || '-'::text) || pendaftaran_t.pasienadmisi_id)
            ELSE NULL::text
            END AS verif_klaim_id,
            pembayaran_t.pembayaranpelayanan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_rjrd.instalasi_id
            ELSE instalasi_ri.instalasi_id
            END AS instalasi_id,
            pembayaran_t.total_discountpembayaran
            FROM (((((((((((((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN pasienpulang_t pulang_rjrd ON ((pendaftaran_t.pasienpulang_id = pulang_rjrd.pasienpulang_id)))
            LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
            LEFT JOIN hasilpemeriksaanlab_t pulang_lab ON ((pendaftaran_t.pendaftaran_id = pulang_lab.pendaftaran_id)))
            LEFT JOIN ruangan_m ruangan_rjrd ON ((pendaftaran_t.ruangan_id = ruangan_rjrd.ruangan_id)))
            LEFT JOIN ruangan_m ruangan_ri ON ((pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id)))
            LEFT JOIN instalasi_m instalasi_rjrd ON ((ruangan_rjrd.instalasi_id = instalasi_rjrd.instalasi_id)))
            LEFT JOIN instalasi_m instalasi_ri ON ((ruangan_ri.instalasi_id = instalasi_ri.instalasi_id)))
            LEFT JOIN penjamin_m penjamin_rjrd ON ((pendaftaran_t.penjamin_id = penjamin_rjrd.penjamin_id)))
            LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
            LEFT JOIN carabayar_m carabayar_rjrd ON ((penjamin_rjrd.carabayar_id = carabayar_rjrd.carabayar_id)))
            LEFT JOIN carabayar_m carabayar_ri ON ((penjamin_ri.carabayar_id = carabayar_ri.carabayar_id)))
            LEFT JOIN bpjs_t bpjs_rjrd ON (((pendaftaran_t.bpjs_id = bpjs_rjrd.bpjs_id) AND (bpjs_rjrd.is_deleted = false))))
            LEFT JOIN bpjs_t bpjs_ri ON (((pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id) AND (bpjs_ri.is_deleted = false))))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.klaiminacbg_id,
            a.klaimgroup_id,
            klaimgroup_t.total
            FROM (klaiminacbg_t a
            JOIN klaimgroup_t ON (((a.klaiminacbg_id = klaimgroup_t.klaiminacbg_id) AND (a.klaimgroup_id = klaimgroup_t.klaimgroup_id) AND (klaimgroup_t.is_deleted = false))))
            WHERE (a.is_deleted = false)) klaim_rjrd ON ((pendaftaran_t.pendaftaran_id = klaim_rjrd.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.klaiminacbg_id,
            a.klaimgroup_id,
            klaimgroup_t.total
            FROM (klaiminacbg_t a
            JOIN klaimgroup_t ON (((a.klaiminacbg_id = klaimgroup_t.klaiminacbg_id) AND (a.klaimgroup_id = klaimgroup_t.klaimgroup_id) AND (klaimgroup_t.is_deleted = false))))
            WHERE (a.is_deleted = false)) klaim_ri ON ((pasienadmisi_t.pasienadmisi_id = klaim_ri.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) AS total_tagihan,
            a.total_dibayar,
            a.total_dijamin,
            b.no_pembayaran,
            a.total_sisatagihan,
            b.pembayaranpelayanan_id,
            a.total_discountpembayaran
            FROM (pembayaran_t a
            JOIN pembayaranpelayanan_t b ON ((a.pembayaran_id = b.pembayaran_id)))
            WHERE (a.is_deleted = false)) pembayaran_t ON ((pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id)))
            LEFT JOIN pengajuanklaimdetail_t ON (((pembayaran_t.pembayaranpelayanan_id = pengajuanklaimdetail_t.pembayaranpelayanan_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            UNION ALL
            SELECT
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN 'RJ'::text
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN 'RJ'::text
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN 'RD'::text
            ELSE 'OTHER'::text
            END AS tipe,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 5)) THEN pulang_rad.tgl_hasilrad
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pulang_rjrd.tglpasienpulang
            ELSE pulang_ri.tglpasienpulang
            END AS tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
            END AS ruangan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_rjrd.ruangan_nama
            ELSE ruangan_ri.ruangan_nama
            END AS ruangan_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_rjrd.instalasi_nama
            ELSE instalasi_ri.instalasi_nama
            END AS instalasi_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_rjrd.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
            END AS carabayar_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
            END AS penjamin_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_rjrd.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
            END AS penjamin_nama,
            pembayaran_t.no_pembayaran,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_dibayar AS total_sdh_bayar,
            pembayaran_t.total_sisatagihan AS total_sisa_tagihan,
            (pembayaran_t.total_dijamin)::integer AS total_asuransi,
            pendaftaran_t.is_skd,
            CASE
            WHEN ((pendaftaran_t.is_skd IS FALSE) OR (pendaftaran_t.is_skd IS NULL)) THEN 'Belum Dibuat'::text
            WHEN ((pendaftaran_t.is_skd IS TRUE) OR (pendaftaran_t.is_skd IS NOT NULL)) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END AS status_skd,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.bpjs_id
            ELSE pasienadmisi_t.bpjs_id
            END AS bpjs_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN bpjs_rjrd.nosep
            ELSE bpjs_ri.nosep
            END AS nosep,
            (pembayaran_t.total_dijamin)::integer AS jumlah_inacbg,
            pendaftaran_t.status_verifikasi,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaimdetail_t.pengajuanklaim_id,
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN (('RJ'::text || '-'::text) || pendaftaran_t.pendaftaran_id)
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (('RD'::text || '-'::text) || pendaftaran_t.pendaftaran_id)
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (('RI'::text || '-'::text) || pendaftaran_t.pasienadmisi_id)
            ELSE NULL::text
            END AS verif_klaim_id,
            pembayaran_t.pembayaranpelayanan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_rjrd.instalasi_id
            ELSE instalasi_ri.instalasi_id
            END AS instalasi_id,
            pembayaran_t.total_discountpembayaran
            FROM (((((((((((((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN pasienpulang_t pulang_rjrd ON ((pendaftaran_t.pasienpulang_id = pulang_rjrd.pasienpulang_id)))
            LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
            LEFT JOIN hasilpemeriksaanrad_t pulang_rad ON ((pendaftaran_t.pendaftaran_id = pulang_rad.pendaftaran_id)))
            LEFT JOIN ruangan_m ruangan_rjrd ON ((pendaftaran_t.ruangan_id = ruangan_rjrd.ruangan_id)))
            LEFT JOIN ruangan_m ruangan_ri ON ((pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id)))
            LEFT JOIN instalasi_m instalasi_rjrd ON ((ruangan_rjrd.instalasi_id = instalasi_rjrd.instalasi_id)))
            LEFT JOIN instalasi_m instalasi_ri ON ((ruangan_ri.instalasi_id = instalasi_ri.instalasi_id)))
            LEFT JOIN penjamin_m penjamin_rjrd ON ((pendaftaran_t.penjamin_id = penjamin_rjrd.penjamin_id)))
            LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
            LEFT JOIN carabayar_m carabayar_rjrd ON ((penjamin_rjrd.carabayar_id = carabayar_rjrd.carabayar_id)))
            LEFT JOIN carabayar_m carabayar_ri ON ((penjamin_ri.carabayar_id = carabayar_ri.carabayar_id)))
            LEFT JOIN bpjs_t bpjs_rjrd ON (((pendaftaran_t.bpjs_id = bpjs_rjrd.bpjs_id) AND (bpjs_rjrd.is_deleted = false))))
            LEFT JOIN bpjs_t bpjs_ri ON (((pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id) AND (bpjs_ri.is_deleted = false))))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.klaiminacbg_id,
            a.klaimgroup_id,
            klaimgroup_t.total
            FROM (klaiminacbg_t a
            JOIN klaimgroup_t ON (((a.klaiminacbg_id = klaimgroup_t.klaiminacbg_id) AND (a.klaimgroup_id = klaimgroup_t.klaimgroup_id) AND (klaimgroup_t.is_deleted = false))))
            WHERE (a.is_deleted = false)) klaim_rjrd ON ((pendaftaran_t.pendaftaran_id = klaim_rjrd.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.klaiminacbg_id,
            a.klaimgroup_id,
            klaimgroup_t.total
            FROM (klaiminacbg_t a
            JOIN klaimgroup_t ON (((a.klaiminacbg_id = klaimgroup_t.klaiminacbg_id) AND (a.klaimgroup_id = klaimgroup_t.klaimgroup_id) AND (klaimgroup_t.is_deleted = false))))
            WHERE (a.is_deleted = false)) klaim_ri ON ((pasienadmisi_t.pasienadmisi_id = klaim_ri.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) AS total_tagihan,
            a.total_dibayar,
            a.total_dijamin,
            b.no_pembayaran,
            a.total_sisatagihan,
            b.pembayaranpelayanan_id,
            a.total_discountpembayaran
            FROM (pembayaran_t a
            JOIN pembayaranpelayanan_t b ON ((a.pembayaran_id = b.pembayaran_id)))
            WHERE (a.is_deleted = false)) pembayaran_t ON ((pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id)))
            LEFT JOIN pengajuanklaimdetail_t ON (((pembayaran_t.pembayaranpelayanan_id = pengajuanklaimdetail_t.pembayaranpelayanan_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            WHERE (pendaftaran_t.instalasi_id = 5)
            ;");
            $this->execute('
                ALTER TABLE public.pengajuanklaim_v OWNER TO postgres;
            ');

            $this->execute('DROP VIEW if exists public.infopasiennonbpjs_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasiennonbpjs_v\" AS
            SELECT rincian.tipe,
            rincian.pasien_id,
            rincian.no_rekam_medik,
            rincian.nama_pasien,
            rincian.pendaftaran_id,
            rincian.tgl_pendaftaran,
            rincian.tglpasienpulang,
            rincian.no_pendaftaran,
            rincian.ruangan_id,
            rincian.ruangan_nama,
            rincian.instalasi_id,
            rincian.instalasi_nama,
            rincian.carabayar_id,
            rincian.carabayar_nama,
            rincian.penjamin_id,
            rincian.penjamin_nama,
            rincian.kelaspelayanan_id,
            rincian.kelaspelayanan_nama,
            rincian.jeniskasuspenyakit_id,
            rincian.jeniskasuspenyakit_nama,
            rincian.pegawai_id,
            rincian.dokter,
            rincian.status_bayar,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_dibayar AS total_sdh_bayar,
            CASE
            WHEN (pengajuan_klaim.total_telahbayar IS NULL) THEN (pembayaran_t.total_dijamin)::integer
            ELSE ((pembayaran_t.total_dijamin)::integer - (pengajuan_klaim.total_telahbayar)::integer)
            END AS total_sisa_tagihan,
            (pembayaran_t.total_dijamin)::integer AS total_asuransi,
            rincian.is_skd,
            CASE
            WHEN (rincian.is_skd IS FALSE) THEN 'Belum Dibuat'::text
            WHEN (rincian.is_skd IS TRUE) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END AS status_skd,
            rincian.pasienadmisi_id,
            rincian.status_verifikasi,
            rincian.lookup_name AS status_verif,
            pembayaran_t.no_pembayaran AS no_invoice,
            pengajuan_klaim.total_telahbayar AS jumlah_pembayaran,
            pembayaran_t.pembayaran_id,
            CASE
            WHEN (pengajuan_klaim.statuspengajuan_id IS NULL) THEN 1120
            ELSE (pengajuan_klaim.statuspengajuan_id)::integer
            END AS statuspengajuan_id,
            CASE
            WHEN (pengajuan_klaim.statuspengajuan_nama IS NULL) THEN 'Belum Melakukan Pengajuan'::character varying
            ELSE pengajuan_klaim.statuspengajuan_nama
            END AS statuspengajuan_nama
            FROM ((( SELECT 'RJ'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.is_skd,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name,
            NULL::integer AS no_pembayaran
            FROM (((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id <> 2))))
            JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> 5) AND (pendaftaran_t.instalasi_id = 1))
            UNION ALL
            SELECT 'RI'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasienadmisi_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pasienadmisi_t.is_skd,
            pasienadmisi_t.pasienadmisi_id,
            pasienadmisi_t.status_verifikasi,
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS lookup_name,
            NULL::integer AS no_pembayaran
            FROM ((((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id <> 2))))
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN carabayar_m carabayar_m_1 ON ((pasienadmisi_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pasienadmisi_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((pasienadmisi_t.is_active = true) AND (pasienadmisi_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> 5))
            UNION ALL
            SELECT 'LAB'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienmasukpenunjang_t.last_modified_date AS tglpasienpulang,
            pendaftaran_t.is_skd,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name,
            NULL::integer AS no_pembayaran
            FROM ((((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id = 4))))
            JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
            LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
            WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> 5))
            UNION ALL
            SELECT 'RAD'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienmasukpenunjang_t.last_modified_date AS tglpasienpulang,
            pendaftaran_t.is_skd,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name,
            NULL::integer AS no_pembayaran
            FROM ((((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id = 5))))
            JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
            LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
            WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> 5))
            UNION ALL
            SELECT 'MCU'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.tgl_pendaftaran AS tglpasienpulang,
            pendaftaran_t.is_skd,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name,
            NULL::integer AS no_pembayaran
            FROM ((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id = 21))))
            JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> 5))
            UNION ALL
            SELECT 'RD-RI'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.is_skd,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name,
            pendaftaran_t.no_pembayaran
            FROM (((((((((pasien_m
            JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.pasienadmisi_id,
            pendaftaran_t_1.tgl_pendaftaran,
            pendaftaran_t_1.no_pendaftaran,
            CASE
            WHEN (pasienadmisi_t.carabayar_id IS NULL) THEN pendaftaran_t_1.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
            WHEN (pasienadmisi_t.penjamin_id IS NULL) THEN pendaftaran_t_1.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
            END AS penjamin_id,
            CASE
            WHEN (pasienadmisi_t.kelaspelayanan_id IS NULL) THEN pendaftaran_t_1.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
            END AS kelaspelayanan_id,
            pendaftaran_t_1.jeniskasuspenyakit_id,
            CASE
            WHEN (pasienadmisi_t.pegawai_id IS NULL) THEN pendaftaran_t_1.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
            END AS pegawai_id,
            CASE
            WHEN (pasienadmisi_t.ruangan_id IS NULL) THEN pendaftaran_t_1.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
            END AS ruangan_id,
            CASE
            WHEN (pasienadmisi_t.ruangan_id IS NULL) THEN pendaftaran_t_1.instalasi_id
            ELSE 3
            END AS instalasi_id,
            CASE
            WHEN (pasienadmisi_t.pasienpulang_id IS NULL) THEN pendaftaran_t_1.pasienpulang_id
            ELSE pasienadmisi_t.pasienpulang_id
            END AS pasienpulang_id,
            CASE
            WHEN (pasienadmisi_t.is_skd IS NULL) THEN pendaftaran_t_1.is_skd
            ELSE pasienadmisi_t.is_skd
            END AS is_skd,
            CASE
            WHEN (pasienadmisi_t.status_verifikasi IS NULL) THEN pendaftaran_t_1.status_verifikasi
            ELSE pasienadmisi_t.status_verifikasi
            END AS status_verifikasi,
            pendaftaran_t_1.status_bayar,
            pendaftaran_t_1.pasien_id,
            NULL::integer AS no_pembayaran
            FROM (pendaftaran_t pendaftaran_t_1
            LEFT JOIN pasienadmisi_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t_1.pasienadmisi_id)))
            WHERE (pendaftaran_t_1.instalasi_id = 2)) pendaftaran_t ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN carabayar_m carabayar_m_1 ON (((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id) AND (carabayar_m_1.carabayar_id <> 5))))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))) rincian
            LEFT JOIN ( SELECT ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) AS total_tagihan,
            a.total_dibayar,
            a.total_dijamin,
            pembayaranpelayanan_t.no_pembayaran,
            a.total_sisatagihan,
            a.pendaftaran_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            a.pembayaran_id
            FROM (pembayaran_t a
            JOIN ( SELECT a1.pembayaran_id,
            a1.no_pembayaran,
            a1.pembayaranpelayanan_id
            FROM pembayaranpelayanan_t a1
            WHERE (a1.is_deleted = false)) pembayaranpelayanan_t ON ((a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (a.is_deleted = false)) pembayaran_t ON ((rincian.pendaftaran_id = pembayaran_t.pendaftaran_id)))
            LEFT JOIN ( SELECT sum(pengajuanklaimdetail_t.jumlah_telahbayar) AS total_telahbayar,
            pengajuanklaimdetail_t.pembayaranpelayanan_id,
            pengajuanklaim_t.status_pengajuanklaim AS statuspengajuan_id,
            fgetnamalookup((pengajuanklaim_t.status_pengajuanklaim)::integer) AS statuspengajuan_nama
            FROM (pengajuanklaimdetail_t
            JOIN pengajuanklaim_t ON ((pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            GROUP BY pengajuanklaimdetail_t.pembayaranpelayanan_id, pengajuanklaim_t.status_pengajuanklaim) pengajuan_klaim ON ((pengajuan_klaim.pembayaranpelayanan_id = pembayaran_t.pembayaranpelayanan_id)))
            GROUP BY rincian.tipe, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.tglpasienpulang, rincian.no_pendaftaran, rincian.ruangan_id, rincian.ruangan_nama, rincian.instalasi_id, rincian.instalasi_nama, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter, rincian.status_bayar, rincian.is_skd,
            CASE
            WHEN (rincian.is_skd IS FALSE) THEN 'Belum Dibuat'::text
            WHEN (rincian.is_skd IS TRUE) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END, rincian.pasienadmisi_id, rincian.status_verifikasi, rincian.lookup_name, rincian.no_pembayaran, rincian.carabayar_id, rincian.carabayar_nama, rincian.penjamin_id, rincian.penjamin_nama, pembayaran_t.total_tagihan, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.no_pembayaran, pembayaran_t.total_dibayar, pengajuan_klaim.total_telahbayar, pembayaran_t.pembayaran_id, pengajuan_klaim.statuspengajuan_id, pengajuan_klaim.statuspengajuan_nama
            ;");
            $this->execute('
                ALTER TABLE public.infopasiennonbpjs_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220114_111807_migrate_hotfix_asuransi_penjamin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220114_111807_migrate_hotfix_asuransi_penjamin cannot be reverted.\n";

        return false;
    }
    */
}
