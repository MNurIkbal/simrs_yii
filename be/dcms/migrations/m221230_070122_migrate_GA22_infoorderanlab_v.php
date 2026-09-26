<?php

use yii\db\Migration;

/**
 * Class m221230_070122_migrate_GA22_infoorderanlab_v
 */
class m221230_070122_migrate_GA22_infoorderanlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infoorderanlab_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infoorderanlab_v
        AS SELECT 'RJRD'::text AS tipe,
            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.pendaftaran_id,
            pasienkirimkeunitlain_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.umur,
            jk.lookup_name AS jenis_kelamin,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::character varying AS no_tempattidur,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_perujuk,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienkirimkeunitlain_t.status_penunjang,
            status.lookup_name AS stat_penunjang,
            pendaftaran_t.kelaspelayanan_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.kunjungan,
            pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
            pasien_m.tanggal_lahir,
            pendaftaran_t.status_pasien,
            carabayar_m.groupcarabayar_id,
            pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
                CASE
                    WHEN tindakan_bayar.jumlah_bayar <> 0::double precision AND tindakan_bayar.jumlah_bayar IS NOT NULL THEN true
                    ELSE false
                END AS is_bayar,
            pasienmasukpenunjang_t.status_periksa,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
                CASE
                    WHEN tindakan_bayar.jumlah_bayar <> 0::double precision AND tindakan_bayar.jumlah_bayar IS NOT NULL THEN 'Sudah Bayar'::text
                    ELSE 'Belum Bayar'::text
                END AS status_bayar,
            pasienkirimkeunitlain_t.is_rujukan,
            COALESCE(pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
            COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, 0::bigint) AS jml_pemeriksaan_approve,
            COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision) AS jumlah_tagihan,
            COALESCE(tindakan_bayar.jumlah_bayar, 0::double precision) AS jumlah_bayar,
            jk.lookup_name AS jenis_kelamin_kode,
            COALESCE(cyto_tindakan.is_cyto, false) AS is_cyto,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan_batal,
            detail_pemeriksaan.pemeriksaan,
            detail_pemeriksaanbatal.pemeriksaan_dibatalkan,
                CASE
                    WHEN pemeriksaan.jml_pemeriksaan = pemeriksaan_approve.jml_pemeriksaan_approve THEN true
                    ELSE false
                END AS is_approved_all,
            carabayar_m.carabayar_kode_warna,
                CASE
                    WHEN pemeriksaan_dirujuk.qty_dirujuk > 0 THEN true
                    ELSE false
                END AS is_referred,
            pendaftaran_t.is_aps,
            bpjs_t.nosep AS no_sep
           FROM pasienkirimkeunitlain_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.pasien_id,
                    a.umur,
                    a.instalasi_id,
                    a.pegawai_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.jeniskasuspenyakit_id,
                    a.ruangan_id,
                    a.tgl_pendaftaran,
                    a.kunjungan,
                    a.status_pasien,
                    a.is_aps
                   FROM pendaftaran_t a) pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.tanda_tangan
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                    a.status_periksa
                   FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                    count(*) AS jml_pemeriksaan
                   FROM permintaankepenunjang_t
                  WHERE permintaankepenunjang_t.is_deleted IS FALSE
                  GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                    count(*) AS jml_pemeriksaan_approve
                   FROM permintaankepenunjang_t
                  WHERE permintaankepenunjang_t.is_deleted IS FALSE AND permintaankepenunjang_t.is_approve IS TRUE
                  GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan_approve ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_approve.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                    sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                    sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_bayar
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t.pasienkirimkeunitlain_id, permintaankepenunjang_t.is_cyto) permintaankepenunjang_t.pasienkirimkeunitlain_id,
                    permintaankepenunjang_t.is_cyto
                   FROM permintaankepenunjang_t
                  WHERE permintaankepenunjang_t.is_cyto = true) cyto_tindakan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    count(*) AS qty_dirujuk
                   FROM permintaankepenunjang_t a
                  WHERE a.is_referred IS TRUE
                  GROUP BY a.pasienkirimkeunitlain_id) pemeriksaan_dirujuk ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_dirujuk.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_kode
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    string_agg(a.nosep::text, '##'::text) AS nosep,
                    string_agg(a.norujukan::text, '##'::text) AS norujukan
                   FROM bpjs_t a
                  WHERE a.pendaftaran_id IS NOT NULL AND a.is_deleted = false
                  GROUP BY a.pendaftaran_id) bpjs_t ON bpjs_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                           FROM ( SELECT daftartindakan_m.daftartindakan_nama::text ||
                                        CASE
                                            WHEN permintaankepenunjang_t.is_referred IS TRUE THEN ' (Dirujuk)'::text
                                            ELSE ''::text
                                        END AS pemeriksaanlab_nama
                                   FROM permintaankepenunjang_t
                                     JOIN ( SELECT a_1.pemeriksaanlab_id,
                                            a_1.pemeriksaanlab_nama,
                                            a_1.daftartindakan_id
                                           FROM pemeriksaanlab_m a_1) pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) daftartindakan_m ON pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                  WHERE permintaankepenunjang_t.is_approve = false AND permintaankepenunjang_t.is_deleted = false AND permintaankepenunjang_t.pasienkirimkeunitlain_id = a.pasienkirimkeunitlain_id
                                UNION ALL
                                 SELECT daftartindakan_m.daftartindakan_nama::text ||
                                        CASE
                                            WHEN permintaankepenunjang_t.is_referred IS TRUE THEN ' (Dirujuk)'::text
                                            ELSE ''::text
                                        END AS pemeriksaanlab_nama
                                   FROM permintaankepenunjang_t
                                     JOIN ( SELECT a_1.pemeriksaanlab_id,
                                            a_1.pemeriksaanlab_nama,
                                            a_1.daftartindakan_id
                                           FROM pemeriksaanlab_m a_1) pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) daftartindakan_m ON pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                     JOIN ( SELECT a_1.permintaankepenunjang_id
                                           FROM pasiendirujukkeluar_t a_1
                                          WHERE a_1.is_deleted IS FALSE) pasiendirujukkeluar_t ON permintaankepenunjang_t.permintaankepenunjang_id = pasiendirujukkeluar_t.permintaankepenunjang_id
                                  WHERE permintaankepenunjang_t.is_approve = true AND permintaankepenunjang_t.is_deleted = false AND permintaankepenunjang_t.pasienkirimkeunitlain_id = a.pasienkirimkeunitlain_id) x) AS pemeriksaan
                   FROM pasienkirimkeunitlain_t a) detail_pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = detail_pemeriksaan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                           FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                                   FROM permintaankepenunjang_t
                                     JOIN ( SELECT a_1.pemeriksaanlab_id,
                                            a_1.pemeriksaanlab_nama,
                                            a_1.daftartindakan_id
                                           FROM pemeriksaanlab_m a_1) pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) daftartindakan_m ON pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                  WHERE permintaankepenunjang_t.is_approve = false AND permintaankepenunjang_t.is_deleted = true AND permintaankepenunjang_t.pasienkirimkeunitlain_id = a.pasienkirimkeunitlain_id
                                UNION ALL
                                 SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                                   FROM tindakanpelayanan_t
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                     JOIN ( SELECT a_1.pemeriksaanlab_id,
                                            a_1.pemeriksaanlab_nama,
                                            a_1.daftartindakan_id
                                           FROM pemeriksaanlab_m a_1) pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                                     JOIN ( SELECT a_1.pasienkirimkeunitlain_id,
                                            a_1.pasienmasukpenunjang_id
                                           FROM pasienmasukpenunjang_t a_1) pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                                  WHERE tindakanpelayanan_t.is_deleted = true AND pasienmasukpenunjang_t_1.pasienkirimkeunitlain_id = a.pasienkirimkeunitlain_id AND tindakanpelayanan_t.tindakanpelayananasal_id IS NULL) x) AS pemeriksaan_dibatalkan
                   FROM pasienkirimkeunitlain_t a) detail_pemeriksaanbatal ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = detail_pemeriksaanbatal.pasienkirimkeunitlain_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 4
        UNION ALL
         SELECT 'RI'::text AS tipe,
            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
            pendaftaran_t.pendaftaran_id,
            pasienkirimkeunitlain_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.umur,
            jk.lookup_name AS jenis_kelamin,
            kelaspelayanan_m.kelaspelayanan_nama,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            pasienadmisi_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_perujuk,
            penjamin_m.carabayar_id,
            carabayar_m.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienkirimkeunitlain_t.status_penunjang,
            status.lookup_name AS stat_penunjang,
            pasienadmisi_t.kelaspelayanan_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pasienadmisi_t.ruangan_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.kunjungan,
            pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
            pasien_m.tanggal_lahir,
            pendaftaran_t.status_pasien,
            carabayar_m.groupcarabayar_id,
            pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
                CASE
                    WHEN tindakan_bayar.jumlah_bayar <> 0::double precision AND tindakan_bayar.jumlah_bayar IS NOT NULL THEN true
                    ELSE false
                END AS is_bayar,
            pasienmasukpenunjang_t.status_periksa,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
                CASE
                    WHEN tindakan_bayar.jumlah_bayar <> 0::double precision AND tindakan_bayar.jumlah_bayar IS NOT NULL THEN 'Sudah Bayar'::text
                    ELSE 'Belum Bayar'::text
                END AS status_bayar,
            pasienkirimkeunitlain_t.is_rujukan,
            COALESCE(pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
            COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, 0::bigint) AS jml_pemeriksaan_approve,
            COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision) AS jumlah_tagihan,
            COALESCE(tindakan_bayar.jumlah_bayar, 0::double precision) AS jumlah_bayar,
            jk.lookup_kode AS jenis_kelamin_kode,
            COALESCE(cyto_tindakan.is_cyto, false) AS is_cyto,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan_batal,
            detail_pemeriksaan.pemeriksaan,
            detail_pemeriksaanbatal.pemeriksaan_dibatalkan,
                CASE
                    WHEN pemeriksaan.jml_pemeriksaan = pemeriksaan_approve.jml_pemeriksaan_approve THEN true
                    ELSE false
                END AS is_approved_all,
            carabayar_m.carabayar_kode_warna,
                CASE
                    WHEN pemeriksaan_dirujuk.qty_dirujuk > 0 THEN true
                    ELSE false
                END AS is_referred,
            pendaftaran_t.is_aps,
            bpjs_t.nosep AS no_sep
           FROM pasienkirimkeunitlain_t
             JOIN ( SELECT a.pasienadmisi_id,
                    a.pegawai_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.pasien_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.ruangan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.pasien_id,
                    a.umur,
                    a.instalasi_id,
                    a.pegawai_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.jeniskasuspenyakit_id,
                    a.ruangan_id,
                    a.tgl_pendaftaran,
                    a.kunjungan,
                    a.status_pasien,
                    a.is_aps,
                    a.pasienadmisi_id
                   FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.tanda_tangan
                   FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                    a.status_periksa
                   FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                    count(*) AS jml_pemeriksaan
                   FROM permintaankepenunjang_t
                  WHERE permintaankepenunjang_t.is_deleted IS FALSE
                  GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                    count(*) AS jml_pemeriksaan_approve
                   FROM permintaankepenunjang_t
                  WHERE permintaankepenunjang_t.is_deleted IS FALSE AND permintaankepenunjang_t.is_approve IS TRUE
                  GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan_approve ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_approve.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                    sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                    sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_bayar
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t.pasienkirimkeunitlain_id, permintaankepenunjang_t.is_cyto) permintaankepenunjang_t.pasienkirimkeunitlain_id,
                    permintaankepenunjang_t.is_cyto
                   FROM permintaankepenunjang_t
                  WHERE permintaankepenunjang_t.is_cyto = true) cyto_tindakan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    count(*) AS qty_dirujuk
                   FROM permintaankepenunjang_t a
                  WHERE a.is_referred IS TRUE
                  GROUP BY a.pasienkirimkeunitlain_id) pemeriksaan_dirujuk ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_dirujuk.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_kode
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    string_agg(a.nosep::text, '##'::text) AS nosep,
                    string_agg(a.norujukan::text, '##'::text) AS norujukan
                   FROM bpjs_t a
                  WHERE a.pendaftaran_id IS NOT NULL
                  GROUP BY a.pendaftaran_id) bpjs_t ON bpjs_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                           FROM ( SELECT daftartindakan_m.daftartindakan_nama::text ||
                                        CASE
                                            WHEN permintaankepenunjang_t.is_referred IS TRUE THEN ' (Dirujuk)'::text
                                            ELSE ''::text
                                        END AS pemeriksaanlab_nama
                                   FROM permintaankepenunjang_t
                                     JOIN ( SELECT a_1.pemeriksaanlab_id,
                                            a_1.pemeriksaanlab_nama,
                                            a_1.daftartindakan_id
                                           FROM pemeriksaanlab_m a_1) pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) daftartindakan_m ON pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                  WHERE permintaankepenunjang_t.is_approve = false AND permintaankepenunjang_t.is_deleted = false AND permintaankepenunjang_t.pasienkirimkeunitlain_id = a.pasienkirimkeunitlain_id
                                UNION ALL
                                 SELECT daftartindakan_m.daftartindakan_nama::text ||
                                        CASE
                                            WHEN permintaankepenunjang_t.is_referred IS TRUE THEN ' (Dirujuk)'::text
                                            ELSE ''::text
                                        END AS pemeriksaanlab_nama
                                   FROM permintaankepenunjang_t
                                     JOIN ( SELECT a_1.pemeriksaanlab_id,
                                            a_1.pemeriksaanlab_nama,
                                            a_1.daftartindakan_id
                                           FROM pemeriksaanlab_m a_1) pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) daftartindakan_m ON pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                     JOIN ( SELECT a_1.permintaankepenunjang_id
                                           FROM pasiendirujukkeluar_t a_1
                                          WHERE a_1.is_deleted IS FALSE) pasiendirujukkeluar_t ON permintaankepenunjang_t.permintaankepenunjang_id = pasiendirujukkeluar_t.permintaankepenunjang_id
                                  WHERE permintaankepenunjang_t.is_approve = true AND permintaankepenunjang_t.is_deleted = false AND permintaankepenunjang_t.pasienkirimkeunitlain_id = a.pasienkirimkeunitlain_id) x) AS pemeriksaan
                   FROM pasienkirimkeunitlain_t a) detail_pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = detail_pemeriksaan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                           FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                                   FROM permintaankepenunjang_t
                                     JOIN ( SELECT a_1.pemeriksaanlab_id,
                                            a_1.pemeriksaanlab_nama,
                                            a_1.daftartindakan_id
                                           FROM pemeriksaanlab_m a_1) pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) daftartindakan_m ON pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                  WHERE permintaankepenunjang_t.is_approve = false AND permintaankepenunjang_t.is_deleted = true AND permintaankepenunjang_t.pasienkirimkeunitlain_id = a.pasienkirimkeunitlain_id
                                UNION ALL
                                 SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                                   FROM tindakanpelayanan_t
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                     JOIN ( SELECT a_1.pemeriksaanlab_id,
                                            a_1.pemeriksaanlab_nama,
                                            a_1.daftartindakan_id
                                           FROM pemeriksaanlab_m a_1) pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                                     JOIN ( SELECT a_1.pasienkirimkeunitlain_id,
                                            a_1.pasienmasukpenunjang_id
                                           FROM pasienmasukpenunjang_t a_1) pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                                  WHERE tindakanpelayanan_t.is_deleted = true AND pasienmasukpenunjang_t_1.pasienkirimkeunitlain_id = a.pasienkirimkeunitlain_id AND tindakanpelayanan_t.tindakanpelayananasal_id IS NULL) x) AS pemeriksaan_dibatalkan
                   FROM pasienkirimkeunitlain_t a) detail_pemeriksaanbatal ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = detail_pemeriksaanbatal.pasienkirimkeunitlain_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 4
        UNION ALL
         SELECT 'Penunjang'::text AS tipe,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id AS pasienkirimkeunitlain_id,
            pendaftaran_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
            pasienmasukpenunjang_t.no_masukpenunjang AS no_rujukan,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.umur,
            jk.lookup_name AS jenis_kelamin,
            kelaspelayanan_m.kelaspelayanan_nama,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::character varying AS no_tempattidur,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_perujuk,
            penjamin_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
                CASE
                    WHEN (( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM tindakanpelayanan_t
                                 JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                              WHERE tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayananasal_id IS NULL) x)) IS NULL THEN '471'::character varying
                    ELSE '472'::character varying
                END AS status_penunjang,
                CASE
                    WHEN (( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM tindakanpelayanan_t
                                 JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                              WHERE tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayananasal_id IS NULL) x)) IS NULL THEN 'DISETUJUI'::character varying
                    ELSE 'BATAL'::character varying
                END AS stat_penunjang,
            pendaftaran_t.kelaspelayanan_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.kunjungan,
            pasienmasukpenunjang_t.ruangan_id AS ruanganpenunjang_id,
            pasien_m.tanggal_lahir,
            pendaftaran_t.status_pasien,
            carabayar_m.groupcarabayar_id,
            pasienmasukpenunjang_t.instalasiasal_id AS instalasipen_id,
                CASE
                    WHEN tindakan_bayar.jumlah_bayar <> 0::double precision AND tindakan_bayar.jumlah_bayar IS NOT NULL THEN true
                    ELSE false
                END AS is_bayar,
            pasienmasukpenunjang_t.status_periksa,
            pasienmasukpenunjang_t.catatan AS catatan_dokterpengirim,
                CASE
                    WHEN tindakan_bayar.jumlah_bayar <> 0::double precision AND tindakan_bayar.jumlah_bayar IS NOT NULL THEN 'Sudah Bayar'::text
                    ELSE 'Belum Bayar'::text
                END AS status_bayar,
            pasienmasukpenunjang_t.is_bayar AS is_rujukan,
            NULL::bigint AS jml_pemeriksaan,
            NULL::bigint AS jml_pemeriksaan_approve,
            NULL::double precision AS jumlah_tagihan,
            NULL::double precision AS jumlah_bayar,
            jk.lookup_kode AS jenis_kelamin_kode,
            NULL::boolean AS is_cyto,
            pendaftaran_t.last_modified_date AS tgl_rujukan_batal,
            detail_pemeriksaan.pemeriksaan,
            detail_pemeriksaanbatal.pemeriksaan_dibatalkan,
            NULL::boolean AS is_approved_all,
            carabayar_m.carabayar_kode_warna,
            false AS is_referred,
            pendaftaran_t.is_aps,
            bpjs_t.nosep AS no_sep
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.pasien_id,
                    a.umur,
                    a.instalasi_id,
                    a.pegawai_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.jeniskasuspenyakit_id,
                    a.ruangan_id,
                    a.tgl_pendaftaran,
                    a.kunjungan,
                    a.status_pasien,
                    a.is_aps,
                    a.pasienadmisi_id,
                    a.last_modified_date
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.tanda_tangan
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        CASE
                            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Belum Bayar'::text
                            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false THEN 'Sudah Bayar'::text
                            ELSE 'Batal'::text
                        END AS status,
                    sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.tindakansudahbayar_id, tindakanpelayanan_t.is_deleted) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                    sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_bayar
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_kode
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    string_agg(a.nosep::text, '##'::text) AS nosep,
                    string_agg(a.norujukan::text, '##'::text) AS norujukan
                   FROM bpjs_t a
                  WHERE a.pendaftaran_id IS NOT NULL
                  GROUP BY a.pendaftaran_id) bpjs_t ON bpjs_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                    ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                           FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                                   FROM tindakanpelayanan_t
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.pasienmasukpenunjang_id = a.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.tindakanpelayananasal_id IS NULL
                                UNION ALL
                                 SELECT paket_tindakan.daftartindakan_nama AS pemeriksaanlab_nama
                                   FROM tindakanpelayanan_t
                                     JOIN ( SELECT a_1.tipepaket_id,
                                            a_1.tipepaket_nama
                                           FROM tipepaket_m a_1) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                                     JOIN ( SELECT a_1.tipepaket_id,
                                            a_1.daftartindakan_id
                                           FROM paketpelayanan_mp a_1) paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
                                     JOIN ( SELECT a_1.daftartindakan_id,
                                            a_1.daftartindakan_nama
                                           FROM daftartindakan_m a_1) paket_tindakan ON paketpelayanan_mp.daftartindakan_id = paket_tindakan.daftartindakan_id
                                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.pendaftaran_id = a.pendaftaran_id) x) AS pemeriksaan
                   FROM pasienmasukpenunjang_t a) detail_pemeriksaan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = detail_pemeriksaan.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                    ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                           FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                                   FROM tindakanpelayanan_t
                                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                  WHERE tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.pasienmasukpenunjang_id = a.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayananasal_id IS NULL
                                UNION ALL
                                 SELECT paket_tindakan.daftartindakan_nama AS pemeriksaanlab_nama
                                   FROM tindakanpelayanan_t
                                     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                                     JOIN paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
                                     JOIN daftartindakan_m paket_tindakan ON paketpelayanan_mp.daftartindakan_id = paket_tindakan.daftartindakan_id
                                  WHERE tindakanpelayanan_t.pendaftaran_id = a.pendaftaran_id AND tindakanpelayanan_t.is_deleted = true) x) AS pemeriksaan_dibatalkan
                   FROM pasienmasukpenunjang_t a) detail_pemeriksaanbatal ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = detail_pemeriksaanbatal.pasienmasukpenunjang_id
          WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[4, 5, 7, 21])) AND pasienmasukpenunjang_t.no_masukpenunjang::text ~~* '%LAB%'::text;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221230_070122_migrate_GA22_infoorderanlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221230_070122_migrate_GA22_infoorderanlab_v cannot be reverted.\n";

        return false;
    }
    */
}
