<?php

use yii\db\Migration;

/**
 * Class m221220_073126_migrate_VCS440_infopasienlab_v
 */
class m221220_073126_migrate_VCS440_infopasienlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.pasienmasukpenunjang_t ADD IF NOT EXISTS is_read bool NULL DEFAULT false;');
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienlab_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopasienlab_v
        AS SELECT 'ORDER'::text AS tipe_pasien,
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
            status.lookup_name AS status_periksa_nama,
            pasienmasukpenunjang_t.no_antrian,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_opd.carabayar_id
                    ELSE penjamin_ipd.carabayar_id
                END AS carabayar_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_opd.carabayar_nama
                    ELSE carabayar_ipd.carabayar_nama
                END AS carabayar_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_opd.penjamin_nama
                    ELSE penjamin_ipd.penjamin_nama
                END AS penjamin_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
                    ELSE pasienadmisi_t.kelaspelayanan_id
                END AS kelaspelayanan_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelas_opd.kelaspelayanan_nama
                    ELSE kelas_ipd.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            jk.lookup_name AS j_kelamin,
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
                CASE
                    WHEN pendaftaran_t.status_periksa::integer = 628 THEN '472'::character varying
                    WHEN pendaftaran_t.status_periksa::integer = 2 THEN '471'::character varying
                    WHEN pasienmasukpenunjang_t.status_periksa::integer = 474 THEN '471'::character varying
                    WHEN pendaftaran_t.status_periksa::integer = 474 THEN '471'::character varying
                    ELSE pasienkirimkeunitlain_t.status_penunjang
                END AS status_penunjang,
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
            concat(COALESCE(gelar_depan.lookup_name, ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
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
            jk.lookup_kode AS jenis_kelamin_kode,
            pegawai_m.tanda_tangan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                   FROM ( SELECT daftartindakan_m.daftartindakan_nama::text ||
                                CASE
                                    WHEN COALESCE(permintaankepenunjang_t_1.is_referred, false) IS TRUE THEN ' (Dirujuk)'::text
                                    ELSE ''::text
                                END AS pemeriksaanlab_nama
                           FROM tindakanpelayanan_t
                             JOIN ( SELECT a.daftartindakan_id,
                                    a.daftartindakan_nama
                                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                             LEFT JOIN ( SELECT a.tindakanpelayanan_id,
                                    a.is_referred
                                   FROM permintaankepenunjang_t a) permintaankepenunjang_t_1 ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t_1.tindakanpelayanan_id
                          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) = (tindakanpelayanan.status = 'Belum Bayar'::text) AND tindakanpelayanan_t.tindakanpelayananasal_id IS NULL) x) AS pemeriksaan,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_opd.carabayar_kode_warna
                    ELSE carabayar_ipd.carabayar_kode_warna
                END AS carabayar_kode_warna,
            pasienmasukpenunjang_t.image_link,
            pasienmasukpenunjang_t.is_complete,
            pasiendirujukkeluar_t.rujukankeluar_id,
            pasiendirujukkeluar_t.diagnosa_dirujuk,
                CASE
                    WHEN permintaankepenunjang_t.ct_refered > 0 THEN true
                    ELSE false
                END AS is_referred,
            pasienmasukpenunjang_t.is_exception,
            pasienmasukpenunjang_t.is_read,
            COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) AS dpjp_id,
                CASE
                    WHEN hasilpemeriksaanlab_wynacom_t.his_reg_no IS NOT NULL AND hasilpemeriksaanlab_wynacom_t.result IS NOT NULL THEN '2'::text
                    WHEN hasilpemeriksaanlab_wynacom_t.his_reg_no IS NOT NULL AND hasilpemeriksaanlab_wynacom_t.result IS NULL THEN '1'::text
                    WHEN hasilpemeriksaanlab_wynacom_t.his_reg_no IS NULL THEN '0'::text
                    ELSE NULL::text
                END AS integrasi_result
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.no_orderkeunitlain,
                    a.tgl_kirimpasien,
                    a.status_penunjang,
                    a.additional_data,
                    a.pegawai_id,
                    a.instalasi_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.pasienadmisi_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.umur,
                    a.label_gelang,
                    a.status_periksa,
                    a.instalasi_id,
                    a.pasien_id,
                    a.kelaspelayanan_id,
                    a.is_indolab,
                    a.pegawai_id
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.status_ranap,
                    a.kelaspelayanan_id,
                    a.pegawai_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.alamat_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.tanda_tangan
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_opd ON pendaftaran_t.penjamin_id = penjamin_opd.penjamin_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_ipd ON pasienadmisi_t.penjamin_id = penjamin_ipd.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_opd ON penjamin_opd.carabayar_id = carabayar_opd.carabayar_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_ipd ON penjamin_ipd.carabayar_id = carabayar_ipd.carabayar_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_opd ON pendaftaran_t.kelaspelayanan_id = kelas_opd.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_ipd ON pasienadmisi_t.kelaspelayanan_id = kelas_ipd.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) dokter_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                    count(*) AS hasil
                   FROM hasilpemeriksaanlabdetail_t
                     JOIN ( SELECT a.pasienmasukpenunjang_id,
                            a.hasilpemeriksaanlab_id
                           FROM hasilpemeriksaanlab_t a) hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
                  GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT count(*) AS jml_hasil,
                    hasilpemeriksaanlab_integrasi_t.order_no
                   FROM hasilpemeriksaanlab_integrasi_t
                  GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.order_no::text
             LEFT JOIN ( SELECT x.pasienmasukpenunjang_id,
                    x.status
                   FROM ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                                CASE
                                    WHEN tindakanbelumbayar.qty_belumbayar > 0 THEN 'Belum Bayar'::text
                                    ELSE
                                    CASE
                                        WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                                        WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Sudah Bayar'::text
                                        ELSE 'Batal'::text
                                    END
                                END AS status
                           FROM tindakanpelayanan_t
                             JOIN ( SELECT b.pasienmasukpenunjang_id,
                                    count(*) AS qty_belumbayar
                                   FROM tindakanpelayanan_t b
                                  WHERE b.tindakansudahbayar_id IS NULL
                                  GROUP BY b.pasienmasukpenunjang_id) tindakanbelumbayar ON tindakanpelayanan_t.pasienmasukpenunjang_id = tindakanbelumbayar.pasienmasukpenunjang_id
                          WHERE tindakanpelayanan_t.is_deleted = false) x
                  GROUP BY x.pasienmasukpenunjang_id, x.status) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
                    tindakanpelayanan_t.cyto_tindakan
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.cyto_tindakan = true AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.is_active = true) cyto_tindakan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    string_agg(pasiendirujukkeluar_t_1.rujukankeluar_id::text, ','::text) AS rujukankeluar_id,
                    ('['::text || string_agg(pasiendirujukkeluar_t_1.diagnosa_dirujuk, ','::text)) || ']'::text AS diagnosa_dirujuk
                   FROM permintaankepenunjang_t a
                     JOIN ( SELECT pasiendirujukkeluar_t_2.permintaankepenunjang_id,
                            pasiendirujukkeluar_t_2.diagnosa AS diagnosa_dirujuk,
                            pasiendirujukkeluar_t_2.rujukankeluar_id
                           FROM pasiendirujukkeluar_t pasiendirujukkeluar_t_2
                          GROUP BY pasiendirujukkeluar_t_2.rujukankeluar_id, pasiendirujukkeluar_t_2.diagnosa, pasiendirujukkeluar_t_2.permintaankepenunjang_id) pasiendirujukkeluar_t_1 ON a.permintaankepenunjang_id = pasiendirujukkeluar_t_1.permintaankepenunjang_id
                  GROUP BY a.pasienkirimkeunitlain_id) pasiendirujukkeluar_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasiendirujukkeluar_t.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    count(*) AS ct_refered
                   FROM permintaankepenunjang_t a
                  WHERE a.is_referred IS TRUE
                  GROUP BY a.pasienkirimkeunitlain_id) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_kode
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_kode
                   FROM lookup_m a) gelar_depan ON dokter_perujuk.gelardepan::integer = gelar_depan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
             LEFT JOIN ( SELECT a.his_reg_no,
                    string_agg(a.result, ' '::text) AS result
                   FROM hasilpemeriksaanlab_wynacom_t a
                  WHERE a.is_deleted = false
                  GROUP BY a.his_reg_no) hasilpemeriksaanlab_wynacom_t ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasilpemeriksaanlab_wynacom_t.his_reg_no::text
          WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pendaftaran_t.is_indolab = false
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
            status.lookup_name AS status_periksa_nama,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            jk.lookup_name AS j_kelamin,
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
            concat(COALESCE(gelar_depan.lookup_name, ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
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
            jk.lookup_kode AS jenis_kelamin_kode,
            pegawai_m.tanda_tangan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                   FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                           FROM tindakanpelayanan_t
                             JOIN ( SELECT a.daftartindakan_id,
                                    a.daftartindakan_nama
                                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) = (tindakanpelayanan.status = 'Belum Bayar'::text) AND tindakanpelayanan_t.tindakanpelayananasal_id IS NULL) x) AS pemeriksaan,
            carabayar_m.carabayar_kode_warna,
            pasienmasukpenunjang_t.image_link,
            pasienmasukpenunjang_t.is_complete,
            NULL::text AS rujukankeluar_id,
            NULL::text AS diagnosa_dirujuk,
            false AS is_referred,
            pasienmasukpenunjang_t.is_exception,
            pasienmasukpenunjang_t.is_read,
            pendaftaran_t.pegawai_id AS dpjp_id,
                CASE
                    WHEN hasilpemeriksaanlab_wynacom_t.his_reg_no IS NOT NULL AND hasilpemeriksaanlab_wynacom_t.result IS NOT NULL THEN '2'::text
                    WHEN hasilpemeriksaanlab_wynacom_t.his_reg_no IS NOT NULL AND hasilpemeriksaanlab_wynacom_t.result IS NULL THEN '1'::text
                    WHEN hasilpemeriksaanlab_wynacom_t.his_reg_no IS NULL THEN '0'::text
                    ELSE NULL::text
                END AS integrasi_result
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.pasienadmisi_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.umur,
                    a.label_gelang,
                    a.status_periksa,
                    a.instalasi_id,
                    a.pasien_id,
                    a.kelaspelayanan_id,
                    a.is_indolab,
                    a.rujukan_id,
                    a.pegawai_id,
                    a.tgl_pendaftaran,
                    a.is_aps
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.alamat_pasien
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.rujukandari_id,
                    a.no_rujukan
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.tanda_tangan
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.perujuk_id,
                    a.namaperujuk
                   FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.status_penunjang
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.tanda_tangan,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                    count(*) AS hasil
                   FROM hasilpemeriksaanlabdetail_t
                     JOIN ( SELECT a.hasilpemeriksaanlab_id,
                            a.pasienmasukpenunjang_id
                           FROM hasilpemeriksaanlab_t a) hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
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
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_kode
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_kode
                   FROM lookup_m a) gelar_depan ON dokter_perujuk.gelardepan::integer = gelar_depan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
             LEFT JOIN ( SELECT a.his_reg_no,
                    string_agg(a.result, ' '::text) AS result
                   FROM hasilpemeriksaanlab_wynacom_t a
                  WHERE a.is_deleted = false
                  GROUP BY a.his_reg_no) hasilpemeriksaanlab_wynacom_t ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasilpemeriksaanlab_wynacom_t.his_reg_no::text
          WHERE pendaftaran_t.instalasi_id = 4 AND pendaftaran_t.is_aps = false AND pendaftaran_t.is_indolab = false
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
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            NULL::character varying AS no_rujukan,
            pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
            'APS'::character varying AS asalrujukan_nama,
            pasienmasukpenunjang_t.ruanganasal_id,
            ruangan_m.ruangan_nama,
                CASE
                    WHEN pendaftaran_t.status_periksa::integer = 628 THEN '476'::character varying
                    ELSE pasienmasukpenunjang_t.status_periksa
                END AS status_periksa,
                CASE
                    WHEN pendaftaran_t.status_periksa::integer = 628 THEN 'BATAL'::character varying
                    ELSE status.lookup_name
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
            jk.lookup_name AS j_kelamin,
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
                CASE
                    WHEN pendaftaran_t.status_periksa::integer = 628 THEN '472'::character varying
                    WHEN pasienmasukpenunjang_t.status_periksa::integer = 477 THEN '471'::character varying
                    WHEN pendaftaran_t.status_periksa::integer = 2 THEN '471'::character varying
                    WHEN pasienmasukpenunjang_t.status_periksa::integer = 474 THEN '471'::character varying
                    WHEN pasienmasukpenunjang_t.status_periksa::integer = 475 THEN '471'::character varying
                    WHEN pasienmasukpenunjang_t.status_periksa::integer = 473 THEN '471'::character varying
                    ELSE pasienkirimkeunitlain_t.status_penunjang
                END AS status_penunjang,
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
            concat(COALESCE(gelar_depan.lookup_name, ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
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
            jk.lookup_kode AS jenis_kelamin_kode,
            pegawai_m.tanda_tangan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                   FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                           FROM tindakanpelayanan_t
                             JOIN ( SELECT a.daftartindakan_id,
                                    a.daftartindakan_nama
                                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) = (tindakanpelayanan.status = 'Belum Bayar'::text) AND tindakanpelayanan_t.tindakanpelayananasal_id IS NULL) x) AS pemeriksaan,
            carabayar_m.carabayar_kode_warna,
            pasienmasukpenunjang_t.image_link,
            pasienmasukpenunjang_t.is_complete,
            NULL::text AS rujukankeluar_id,
            NULL::text AS diagnosa_dirujuk,
            false AS is_referred,
            pasienmasukpenunjang_t.is_exception,
            pasienmasukpenunjang_t.is_read,
            pendaftaran_t.pegawai_id AS dpjp_id,
                CASE
                    WHEN hasilpemeriksaanlab_wynacom_t.his_reg_no IS NOT NULL AND hasilpemeriksaanlab_wynacom_t.result IS NOT NULL THEN '2'::text
                    WHEN hasilpemeriksaanlab_wynacom_t.his_reg_no IS NOT NULL AND hasilpemeriksaanlab_wynacom_t.result IS NULL THEN '1'::text
                    WHEN hasilpemeriksaanlab_wynacom_t.his_reg_no IS NULL THEN '0'::text
                    ELSE NULL::text
                END AS integrasi_result
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.pasienadmisi_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.umur,
                    a.label_gelang,
                    a.status_periksa,
                    a.instalasi_id,
                    a.pasien_id,
                    a.kelaspelayanan_id,
                    a.is_indolab,
                    a.pegawai_id,
                    a.tgl_pendaftaran,
                    a.is_aps
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.alamat_pasien
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.tanda_tangan
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.status_penunjang,
                    a.additional_data
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.tanda_tangan,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                    count(*) AS hasil
                   FROM hasilpemeriksaanlabdetail_t
                     JOIN ( SELECT a.hasilpemeriksaanlab_id,
                            a.pasienmasukpenunjang_id
                           FROM hasilpemeriksaanlab_t a) hasilpemeriksaanlab_t ON hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id
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
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_kode
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_kode
                   FROM lookup_m a) gelar_depan ON dokter_perujuk.gelardepan::integer = gelar_depan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
             LEFT JOIN ( SELECT a.his_reg_no,
                    string_agg(a.result, ' '::text) AS result
                   FROM hasilpemeriksaanlab_wynacom_t a
                  WHERE a.is_deleted = false
                  GROUP BY a.his_reg_no) hasilpemeriksaanlab_wynacom_t ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasilpemeriksaanlab_wynacom_t.his_reg_no::text
          WHERE pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL AND ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pendaftaran_t.is_indolab = false AND pasienmasukpenunjang_t.status_periksa IS NOT NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221220_073126_migrate_VCS440_infopasienlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221220_073126_migrate_VCS440_infopasienlab_v cannot be reverted.\n";

        return false;
    }
    */
}
