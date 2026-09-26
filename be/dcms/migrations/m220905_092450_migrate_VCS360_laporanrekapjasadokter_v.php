<?php

use yii\db\Migration;

/**
 * Class m220905_092450_migrate_VCS360_laporanrekapjasadokter_v
 */
class m220905_092450_migrate_VCS360_laporanrekapjasadokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanrekapjasadokter_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporanrekapjasadokter_v
        AS SELECT gabung.pendaftaran_id,
            gabung.pasienadmisi_id,
            gabung.no_pendaftaran,
            gabung.tindakanpelayanan_id,
            gabung.tgl_tindakan,
            gabung.dokterpenanggungjawab_id,
            gabung.nama_pegawai,
            gabung.pasien_id,
            gabung.no_rekam_medik,
            gabung.nama_pasien,
            gabung.daftartindakan_id,
            gabung.daftartindakan_nama,
            gabung.tarif_tindakankomp,
            gabung.komponentarif_nama,
            gabung.status_bayar,
            gabung.jenis_transaksi,
            gabung.pelayananjasadokter_id,
                CASE
                    WHEN gabung.jenis_transaksi = ANY (ARRAY['pelayanan'::text]) THEN gabung.tarif_tindakankomp * 100::double precision / 90::double precision
                    ELSE 0::double precision
                END AS bruto,
                CASE
                    WHEN gabung.jenis_transaksi = ANY (ARRAY['pelayanan'::text]) THEN gabung.tarif_tindakankomp * 100::double precision / 90::double precision * 50::double precision / 100::double precision
                    ELSE 0::double precision
                END AS dpp,
            gabung.penjamin_id,
            gabung.penjamin_nama,
            gabung.carabayar_id,
            gabung.carabayar_nama,
            gabung.keterangan,
            gabung.instalasi_id,
            gabung.instalasi_nama,
            gabung.ruangan_id,
            gabung.ruangan_nama,
                CASE
                    WHEN gabung.pasienadmisi_id IS NULL AND gabung.instalasi_asal <> 2 THEN 'RAJAL'::text
                    WHEN gabung.pasienadmisi_id IS NULL AND gabung.instalasi_asal = 2 THEN 'IGD'::text
                    ELSE 'RANAP'::text
                END AS pelayanan,
            gabung.tglpasienpulang AS tgl_pasienpulang,
            gabung.status_bayar_id,
            gabung.tarif_tindakan,
            gabung.kelaspelayanan_id,
            gabung.kelaspelayanan_nama,
            concat(gabung.cyto, gabung.penyulit, gabung.overwrite) AS kondisi
           FROM ( SELECT 'PENERIMAAN'::text AS keterangan,
                    pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    pendaftaran_t.no_pendaftaran,
                    tindakanpelayanan_t.tindakanpelayanan_id,
                    tindakanpelayanan_t.tgl_tindakan,
                    COALESCE(permintaankepenunjang_t.dokter_id::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) AS dokterpenanggungjawab_id,
                    COALESCE(dokter_penunjang.nama_pegawai, dokter_tindakan.nama_pegawai) AS nama_pegawai,
                    pendaftaran_t.pasien_id,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    tindakanpelayanan_t.daftartindakan_id,
                    daftartindakan_m.daftartindakan_nama,
                    tindakankomponen_t.tarif_tindakankomp,
                    komponentarif_m.komponentarif_nama,
                    status_bayar.lookup_name AS status_bayar,
                    'pelayanan'::text AS jenis_transaksi,
                    NULL::integer AS pelayananjasadokter_id,
                    tindakanpelayanan_t.penjamin_id,
                    penjamin_m.penjamin_nama,
                    penjamin_m.carabayar_id,
                    carabayar_m.carabayar_nama,
                    tindakanpelayanan_t.instalasi_id,
                    instalasi_m.instalasi_nama,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    pendaftaran_t.instalasi_id AS instalasi_asal,
                    COALESCE(pendaftaran_t.tgl_stopakomodasi, pasienpulang_ri.tglpasienpulang, pasienpulang_rjrd.tglpasienpulang) AS tglpasienpulang,
                    pendaftaran_t.status_bayar AS status_bayar_id,
                        CASE
                            WHEN tindakanpelayanan_t.parent_id IS NULL THEN tindakanpelayanan_t.tarif_tindakan
                            ELSE tindakanpelayanan_t.harga_origin * tindakanpelayanan_t.qty_tindakan::double precision
                        END AS tarif_tindakan,
                    tindakanpelayanan_t.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                        CASE
                            WHEN tindakanpelayanan_t.cyto_tindakan IS TRUE THEN 'Cyto'::text
                            ELSE ''::text
                        END AS cyto,
                        CASE
                            WHEN tindakanpelayanan_t.penyulit_tindakan IS TRUE THEN
                            CASE
                                WHEN tindakanpelayanan_t.cyto_tindakan IS TRUE THEN ', Penyulit'::text
                                ELSE 'Penyulit'::text
                            END
                            ELSE ''::text
                        END AS penyulit,
                        CASE
                            WHEN COALESCE(tindakanpelayanan_t.is_overwrite, false) IS TRUE THEN
                            CASE
                                WHEN tindakanpelayanan_t.cyto_tindakan OR tindakanpelayanan_t.penyulit_tindakan IS TRUE THEN ', Edit Harga'::text
                                ELSE 'Edit Harga'::text
                            END
                            ELSE ''::text
                        END AS overwrite
                   FROM pendaftaran_t
                     LEFT JOIN ( SELECT a.pasienadmisi_id,
                            a.pasienpulang_id
                           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     JOIN ( SELECT a.tindakanpelayanan_id,
                            a.tgl_tindakan,
                            a.dokterpenanggungjawab_id,
                            a.daftartindakan_id,
                            a.penjamin_id,
                            a.instalasi_id,
                            a.parent_id,
                            a.tarif_tindakan,
                            a.harga_origin,
                            a.qty_tindakan,
                            a.cyto_tindakan,
                            a.penyulit_tindakan,
                            a.is_overwrite,
                            a.pendaftaran_id,
                            a.ruangan_id,
                            a.kelaspelayanan_id,
                            a.is_deleted,
                            a.pasienmasukpenunjang_id
                           FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                     JOIN ( SELECT a.tarif_tindakankomp,
                            a.tindakanpelayanan_id,
                            a.komponentarif_id,
                            a.is_deleted
                           FROM tindakankomponen_t a) tindakankomponen_t ON tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id
                     JOIN ( SELECT komponentarif.komponentarif_id,
                            komponentarif.komponentarif_nama
                           FROM komponentarif_m komponentarif
                             JOIN ( SELECT a.kode_id,
                                    a.kode_transaksi
                                   FROM lookuptransaksi_m a) lookuptransaksi_m ON komponentarif.komponentarif_id = lookuptransaksi_m.kode_id AND lookuptransaksi_m.kode_transaksi::text = 'komponen_jas_dok'::text) komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id
                     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                            a.pasienkirimkeunitlain_id
                           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                     LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id
                           FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                     LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                            a.dokter_id
                           FROM permintaankepenunjang_t a) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.kelompokpegawai_id
                           FROM pegawai_m a) dokter_penunjang ON permintaankepenunjang_t.dokter_id = dokter_penunjang.pegawai_id AND dokter_penunjang.kelompokpegawai_id = 1
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.kelompokpegawai_id
                           FROM pegawai_m a) dokter_tindakan ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter_tindakan.pegawai_id
                     JOIN ( SELECT a.pasien_id,
                            a.no_rekam_medik,
                            a.nama_pasien
                           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     JOIN ( SELECT a.daftartindakan_id,
                            a.daftartindakan_nama
                           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama,
                            a.carabayar_id
                           FROM penjamin_m a) penjamin_m ON tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id
                     JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a.tglpasienpulang,
                            a.pasienpulang_id
                           FROM pasienpulang_t a) pasienpulang_rjrd ON pendaftaran_t.pasienpulang_id = pasienpulang_rjrd.pasienpulang_id
                     LEFT JOIN ( SELECT a.tglpasienpulang,
                            a.pasienpulang_id
                           FROM pasienpulang_t a) pasienpulang_ri ON pasienadmisi_t.pasienpulang_id = pasienpulang_ri.pasienpulang_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) status_bayar ON pendaftaran_t.status_bayar = status_bayar.lookup_id
                     LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakankomponen_t.is_deleted = false AND
                        CASE
                            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pendaftaran_t.is_stopakomodasi IS TRUE
                            ELSE pendaftaran_t.pasienpulang_id IS NOT NULL
                        END
                UNION ALL
                 SELECT 'PENERIMAAN APS'::text AS keterangan,
                    pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    pendaftaran_t.no_pendaftaran,
                    tindakanpelayanan_t.tindakanpelayanan_id,
                    tindakanpelayanan_t.tgl_tindakan,
                    tindakanpelayanan_t.dokterpenanggungjawab_id,
                    pegawai_m.nama_pegawai,
                    pendaftaran_t.pasien_id,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    tindakanpelayanan_t.daftartindakan_id,
                    daftartindakan_m.daftartindakan_nama,
                    tindakankomponen_t.tarif_tindakankomp,
                    komponentarif_m.komponentarif_nama,
                    status_bayar.lookup_name AS status_bayar,
                    'pelayanan'::text AS jenis_transaksi,
                    NULL::integer AS pelayananjasadokter_id,
                    tindakanpelayanan_t.penjamin_id,
                    penjamin_m.penjamin_nama,
                    penjamin_m.carabayar_id,
                    carabayar_m.carabayar_nama,
                    tindakanpelayanan_t.instalasi_id,
                    instalasi_m.instalasi_nama,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    pendaftaran_t.instalasi_id AS instalasi_asal,
                    COALESCE(pendaftaran_t.tgl_stopakomodasi, pasienpulang_ri.tglpasienpulang, pasienpulang_rjrd.tglpasienpulang) AS tglpasienpulang,
                    pendaftaran_t.status_bayar AS status_bayar_id,
                        CASE
                            WHEN tindakanpelayanan_t.parent_id IS NULL THEN tindakanpelayanan_t.tarif_tindakan
                            ELSE tindakanpelayanan_t.harga_origin * tindakanpelayanan_t.qty_tindakan::double precision
                        END AS tarif_tindakan,
                    tindakanpelayanan_t.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                        CASE
                            WHEN tindakanpelayanan_t.cyto_tindakan IS TRUE THEN 'Cyto'::text
                            ELSE ''::text
                        END AS cyto,
                        CASE
                            WHEN tindakanpelayanan_t.penyulit_tindakan IS TRUE THEN
                            CASE
                                WHEN tindakanpelayanan_t.cyto_tindakan IS TRUE THEN ', Penyulit'::text
                                ELSE 'Penyulit'::text
                            END
                            ELSE ''::text
                        END AS penyulit,
                        CASE
                            WHEN COALESCE(tindakanpelayanan_t.is_overwrite, false) IS TRUE THEN
                            CASE
                                WHEN tindakanpelayanan_t.cyto_tindakan OR tindakanpelayanan_t.penyulit_tindakan IS TRUE THEN ', Edit Harga'::text
                                ELSE 'Edit Harga'::text
                            END
                            ELSE ''::text
                        END AS overwrite
                   FROM pendaftaran_t
                     LEFT JOIN ( SELECT a.pasienadmisi_id,
                            a.pasienpulang_id
                           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     JOIN ( SELECT a.tindakanpelayanan_id,
                            a.pendaftaran_id,
                            a.tgl_tindakan,
                            a.dokterpenanggungjawab_id,
                            a.daftartindakan_id,
                            a.penjamin_id,
                            a.instalasi_id,
                            a.parent_id,
                            a.tarif_tindakan,
                            a.harga_origin,
                            a.qty_tindakan,
                            a.kelaspelayanan_id,
                            a.cyto_tindakan,
                            a.penyulit_tindakan,
                            a.is_overwrite,
                            a.ruangan_id,
                            a.is_deleted
                           FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                     JOIN ( SELECT a.tarif_tindakankomp,
                            a.tindakanpelayanan_id,
                            a.komponentarif_id,
                            a.is_deleted
                           FROM tindakankomponen_t a) tindakankomponen_t ON tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id
                     JOIN ( SELECT komponentarif.komponentarif_id,
                            komponentarif.komponentarif_nama
                           FROM komponentarif_m komponentarif
                             JOIN ( SELECT a.kode_id,
                                    a.kode_transaksi
                                   FROM lookuptransaksi_m a) lookuptransaksi_m ON komponentarif.komponentarif_id = lookuptransaksi_m.kode_id AND lookuptransaksi_m.kode_transaksi::text = 'komponen_jas_dok'::text) komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
                     JOIN ( SELECT a.pasien_id,
                            a.no_rekam_medik,
                            a.nama_pasien
                           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     JOIN ( SELECT a.daftartindakan_id,
                            a.daftartindakan_nama
                           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama,
                            a.carabayar_id
                           FROM penjamin_m a) penjamin_m ON tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id
                     JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a.tglpasienpulang,
                            a.pasienpulang_id
                           FROM pasienpulang_t a) pasienpulang_rjrd ON pendaftaran_t.pasienpulang_id = pasienpulang_rjrd.pasienpulang_id
                     LEFT JOIN ( SELECT a.tglpasienpulang,
                            a.pasienpulang_id
                           FROM pasienpulang_t a) pasienpulang_ri ON pasienadmisi_t.pasienpulang_id = pasienpulang_ri.pasienpulang_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) status_bayar ON pendaftaran_t.status_bayar = status_bayar.lookup_id
                     LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakankomponen_t.is_deleted = false AND pendaftaran_t.is_aps IS TRUE
                UNION ALL
                 SELECT 'PENERIMAAN'::text AS keterangan,
                    pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    pendaftaran_t.no_pendaftaran,
                    tindakanpelayanan_t.tindakanpelayanan_id,
                    tindakanpelayanan_t.tgl_tindakan,
                    tindakanpelayanan_t.dokterpenanggungjawab_id,
                    pegawai_m.nama_pegawai,
                    pendaftaran_t.pasien_id,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    tindakanpelayanan_t.tipepaket_id,
                    tipepaket_m.tipepaket_nama,
                    tindakankomponen_t.tarif_tindakankomp,
                    komponentarif_m.komponentarif_nama,
                    status_bayar.lookup_name AS status_bayar,
                    'pelayanan'::text AS jenis_transaksi,
                    NULL::integer AS pelayananjasadokter_id,
                    tindakanpelayanan_t.penjamin_id,
                    penjamin_m.penjamin_nama,
                    penjamin_m.carabayar_id,
                    carabayar_m.carabayar_nama,
                    tindakanpelayanan_t.instalasi_id,
                    instalasi_m.instalasi_nama,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    pendaftaran_t.instalasi_id AS instalasi_asal,
                    COALESCE(pendaftaran_t.tgl_stopakomodasi, pasienpulang_ri.tglpasienpulang, pasienpulang_rjrd.tglpasienpulang) AS tglpasienpulang,
                    pendaftaran_t.status_bayar AS status_bayar_id,
                        CASE
                            WHEN tindakanpelayanan_t.parent_id IS NULL THEN tindakanpelayanan_t.tarif_tindakan
                            ELSE tindakanpelayanan_t.harga_origin * tindakanpelayanan_t.qty_tindakan::double precision
                        END AS tarif_tindakan,
                    tindakanpelayanan_t.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                        CASE
                            WHEN tindakanpelayanan_t.cyto_tindakan IS TRUE THEN 'Cyto'::text
                            ELSE ''::text
                        END AS cyto,
                        CASE
                            WHEN tindakanpelayanan_t.penyulit_tindakan IS TRUE THEN
                            CASE
                                WHEN tindakanpelayanan_t.cyto_tindakan IS TRUE THEN ', Penyulit'::text
                                ELSE 'Penyulit'::text
                            END
                            ELSE ''::text
                        END AS penyulit,
                        CASE
                            WHEN COALESCE(tindakanpelayanan_t.is_overwrite, false) IS TRUE THEN
                            CASE
                                WHEN tindakanpelayanan_t.cyto_tindakan OR tindakanpelayanan_t.penyulit_tindakan IS TRUE THEN ', Edit Harga'::text
                                ELSE 'Edit Harga'::text
                            END
                            ELSE ''::text
                        END AS overwrite
                   FROM pendaftaran_t
                     LEFT JOIN ( SELECT a.pasienadmisi_id,
                            a.pasienpulang_id
                           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     JOIN ( SELECT a.tindakanpelayanan_id,
                            a.pendaftaran_id,
                            a.tgl_tindakan,
                            a.dokterpenanggungjawab_id,
                            a.tipepaket_id,
                            a.penjamin_id,
                            a.instalasi_id,
                            a.parent_id,
                            a.tarif_tindakan,
                            a.harga_origin,
                            a.qty_tindakan,
                            a.kelaspelayanan_id,
                            a.cyto_tindakan,
                            a.penyulit_tindakan,
                            a.is_overwrite,
                            a.ruangan_id,
                            a.is_deleted
                           FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                     JOIN ( SELECT a.tindakanpelayanan_id,
                            a.tarif_tindakankomp,
                            a.komponentarif_id,
                            a.is_deleted
                           FROM tindakankomponen_t a) tindakankomponen_t ON tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id
                     JOIN ( SELECT komponentarif.komponentarif_id,
                            komponentarif.komponentarif_nama
                           FROM komponentarif_m komponentarif
                             JOIN ( SELECT a.kode_id,
                                    a.kode_transaksi
                                   FROM lookuptransaksi_m a) lookuptransaksi_m ON komponentarif.komponentarif_id = lookuptransaksi_m.kode_id AND lookuptransaksi_m.kode_transaksi::text = 'komponen_jas_dok'::text) komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
                     JOIN ( SELECT a.pasien_id,
                            a.no_rekam_medik,
                            a.nama_pasien
                           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            a.is_mcu
                           FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama,
                            a.carabayar_id
                           FROM penjamin_m a) penjamin_m ON tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id
                     JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a.tglpasienpulang,
                            a.pasienpulang_id
                           FROM pasienpulang_t a) pasienpulang_rjrd ON pendaftaran_t.pasienpulang_id = pasienpulang_rjrd.pasienpulang_id
                     LEFT JOIN ( SELECT a.tglpasienpulang,
                            a.pasienpulang_id
                           FROM pasienpulang_t a) pasienpulang_ri ON pasienadmisi_t.pasienpulang_id = pasienpulang_ri.pasienpulang_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) status_bayar ON pendaftaran_t.status_bayar = status_bayar.lookup_id
                     LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakankomponen_t.is_deleted = false AND tipepaket_m.is_mcu IS FALSE AND
                        CASE
                            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pendaftaran_t.is_stopakomodasi IS TRUE
                            ELSE pendaftaran_t.pasienpulang_id IS NOT NULL
                        END
                UNION ALL
                 SELECT
                        CASE
                            WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN 'PENERIMAAN'::text
                            WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN 'PENGURANGAN'::text
                            ELSE NULL::text
                        END AS keterangan,
                    NULL::integer AS pendaftaran_id,
                    NULL::integer AS pasienadmisi_id,
                    pembayarantransaksi_t.no_transaksi AS no_pendaftaran,
                    NULL::integer AS tindakanpelayanan_id,
                    pembayarantransaksi_t.tgl_transaksi AS tgl_tindakan,
                    pembayarantransaksi_t.pegawai_id AS dokterpenanggungjawab_id,
                    dokter.nama_pegawai,
                    NULL::integer AS pasien_id,
                    NULL::character varying AS no_rekam_medik,
                    NULL::character varying AS nama_pasien,
                    NULL::integer AS daftartindakan_id,
                    pembayarantransaksi_t.deskripsi AS daftartindakan_nama,
                        CASE
                            WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN pembayarantransaksi_t.jumlah
                            WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN - pembayarantransaksi_t.jumlah
                            ELSE NULL::double precision
                        END AS tarif_tindakankomp,
                        CASE
                            WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN 'Penerimaan'::text
                            WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN 'Pengeluaran'::text
                            ELSE NULL::text
                        END AS komponentarif_nama,
                    '-'::text AS status_bayar,
                    'Transaksi Kasir'::text AS jenis_transaksi,
                    NULL::integer AS pelayananjasadokter_id,
                    NULL::integer AS penjamin_id,
                    NULL::character varying AS penjamin_nama,
                    NULL::integer AS carabayar_id,
                    NULL::character varying AS carabayar_nama,
                    0 AS instalasi_id,
                    NULL::character varying AS instalasi_nama,
                    0 AS ruangan_id,
                    NULL::character varying AS ruangan_nama,
                    NULL::integer AS instalasi_asal,
                    pembayarantransaksi_t.tgl_transaksi AS tglpasienpulang,
                    9999 AS status_bayar_id,
                    NULL::double precision AS tarif_tindakan,
                    NULL::integer AS kelaspelayanan_id,
                    NULL::character varying AS kelaspelayanan_nama,
                    NULL::text AS cyto_tindakan,
                    NULL::text AS penyulit_tindakan,
                    NULL::text AS is_overwrite
                   FROM pembayarantransaksi_t
                     JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.kelompokpegawai_id
                           FROM pegawai_m a) dokter ON pembayarantransaksi_t.pegawai_id = dokter.pegawai_id AND dokter.kelompokpegawai_id = 1
                     JOIN ( SELECT a.kategoritransaksi_id
                           FROM kategoritransaksi_m a) kategoritransaksi_m ON pembayarantransaksi_t.kategoritransaksi_id = kategoritransaksi_m.kategoritransaksi_id
                  WHERE pembayarantransaksi_t.tipe_transaksi = 701 AND pembayarantransaksi_t.is_deleted = false
                UNION ALL
                 SELECT
                        CASE
                            WHEN pelayananjasadokter_t.total_jasa > 0::double precision THEN 'PENERIMAAN'::text
                            WHEN pelayananjasadokter_t.total_jasa < 0::double precision THEN 'PENGURANGAN'::text
                            ELSE NULL::text
                        END AS keterangan,
                    NULL::integer AS pendaftaran_id,
                    NULL::integer AS pasienadmisi_id,
                    pelayananjasadokter_t.no_transaksi AS no_pendaftaran,
                    NULL::integer AS tindakanpelayanan_id,
                    pelayananjasadokter_t.tgl_transaksi AS tgl_tindakan,
                    pelayananjasadokter_t.pegawai_id AS dokterpenanggungjawab_id,
                    dokter.nama_pegawai,
                    NULL::integer AS pasien_id,
                    NULL::character varying AS no_rekam_medik,
                    NULL::character varying AS nama_pasien,
                    NULL::integer AS daftartindakan_id,
                    pelayananjasadokter_t.deskripsi AS daftartindakan_nama,
                    pelayananjasadokter_t.total_jasa AS tarif_tindakankomp,
                    jasadokter_m.jasadokter_nama AS komponentarif_nama,
                    '-'::character varying AS status_bayar,
                    'Transaksi Jasa Dokter'::text AS jenis_transaksi,
                    pelayananjasadokter_t.pelayananjasadokter_id,
                    NULL::integer AS penjamin_id,
                    NULL::character varying AS penjamin_nama,
                    NULL::integer AS carabayar_id,
                    NULL::character varying AS carabayar_nama,
                    0 AS instalasi_id,
                    NULL::character varying AS instalasi_nama,
                    0 AS ruangan_id,
                    NULL::character varying AS ruangan_nama,
                    NULL::integer AS instalasi_asal,
                    pelayananjasadokter_t.tgl_transaksi AS tglpasienpulang,
                    9999 AS status_bayar_id,
                    NULL::double precision AS tarif_tindakan,
                    NULL::integer AS kelaspelayanan_id,
                    NULL::character varying AS kelaspelayanan_nama,
                    NULL::text AS cyto_tindakan,
                    NULL::text AS penyulit_tindakan,
                    NULL::text AS is_overwrite
                   FROM pelayananjasadokter_t
                     JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dokter ON pelayananjasadokter_t.pegawai_id = dokter.pegawai_id
                     JOIN ( SELECT a.jasadokter_id,
                            a.jasadokter_nama
                           FROM jasadokter_m a) jasadokter_m ON pelayananjasadokter_t.jasadokter_id = jasadokter_m.jasadokter_id
                  WHERE pelayananjasadokter_t.is_deleted = false
                UNION ALL
                 SELECT 'PENGURANGAN'::text AS keterangan,
                    pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    pendaftaran_t.no_pendaftaran,
                    NULL::integer AS tindakanpelayanan_id,
                    pembayaran_t.created_date AS tgl_tindakan,
                    pembayarandiskon_t.pegawai_id AS dokterpenanggungjawab_id,
                    dokter.nama_pegawai,
                    pendaftaran_t.pasien_id,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    NULL::integer AS daftartindakan_id,
                    'Diskon'::character varying AS daftartindakan_nama,
                    - pembayarandiskon_t.total_diskon AS tarif_tindakankomp,
                    komponentarif_m.komponentarif_nama,
                    '-'::character varying AS status_bayar,
                    'Diskon'::text AS jenis_transaksi,
                    NULL::bigint AS pelayananjasadokter_id,
                    NULL::integer AS penjamin_id,
                    NULL::character varying AS penjamin_nama,
                    NULL::integer AS carabayar_id,
                    NULL::character varying AS carabayar_nama,
                    0 AS instalasi_id,
                    'DISKON'::text AS instalasi_nama,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    NULL::integer AS instalasi_asal,
                    COALESCE(pendaftaran_t.tgl_stopakomodasi, pasienpulang_ri.tglpasienpulang, pasienpulang_rjrd.tglpasienpulang) AS tglpasienpulang,
                    9999 AS status_bayar_id,
                    NULL::double precision AS tarif_tindakan,
                    NULL::integer AS kelaspelayanan_id,
                    NULL::character varying AS kelaspelayanan_nama,
                    NULL::text AS cyto_tindakan,
                    NULL::text AS penyulit_tindakan,
                    NULL::text AS is_overwrite
                   FROM pembayarandiskon_t
                     JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dokter ON pembayarandiskon_t.pegawai_id = dokter.pegawai_id
                     JOIN ( SELECT a.pembayaran_id,
                            a.pendaftaran_id,
                            a.created_date
                           FROM pembayaran_t a) pembayaran_t ON pembayarandiskon_t.pembayaran_id = pembayaran_t.pembayaran_id
                     JOIN ( SELECT a.pembayaran_id,
                            a.is_deleted
                           FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON pembayaran_t.pembayaran_id = pembayaranpelayanan_t.pembayaran_id AND pembayaranpelayanan_t.is_deleted = false
                     JOIN ( SELECT pendaftaran.tgl_pendaftaran,
                            pendaftaran.pendaftaran_id,
                            pendaftaran.status_bayar,
                            pendaftaran.no_pendaftaran,
                            pendaftaran.pasienadmisi_id,
                            pendaftaran.pasien_id,
                            pendaftaran.pasienpulang_id AS rjrd_pasienpulang_id,
                            pasienadmisi_t.pasienpulang_id AS ri_pasienpulang_id,
                            pendaftaran.tgl_stopakomodasi,
                            pendaftaran.is_stopakomodasi,
                            pendaftaran.pasienpulang_id,
                            COALESCE(pasienadmisi_t.ruangan_id, pendaftaran.ruangan_id) AS ruangan_id
                           FROM pendaftaran_t pendaftaran
                             LEFT JOIN pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.pasien_id,
                            a.no_rekam_medik,
                            a.nama_pasien
                           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     LEFT JOIN ( SELECT a.komponentarif_id,
                            a.komponentarif_nama
                           FROM komponentarif_m a) komponentarif_m ON pembayarandiskon_t.komponentarif_id = komponentarif_m.komponentarif_id
                     LEFT JOIN ( SELECT a.tglpasienpulang,
                            a.pasienpulang_id
                           FROM pasienpulang_t a) pasienpulang_rjrd ON pendaftaran_t.rjrd_pasienpulang_id = pasienpulang_rjrd.pasienpulang_id
                     LEFT JOIN ( SELECT a.tglpasienpulang,
                            a.pasienpulang_id
                           FROM pasienpulang_t a) pasienpulang_ri ON pendaftaran_t.ri_pasienpulang_id = pasienpulang_ri.pasienpulang_id
                  WHERE pembayarandiskon_t.is_deleted = false AND
                        CASE
                            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pendaftaran_t.is_stopakomodasi IS TRUE
                            ELSE pendaftaran_t.pasienpulang_id IS NOT NULL
                        END
                UNION ALL
                 SELECT 'PENGURANGAN'::text AS keterangan,
                    pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    pendaftaran_t.no_pendaftaran,
                    NULL::integer AS tindakanpelayanan_id,
                    tindakanpelayanan_t.tgl_tindakan,
                    dokter.pegawai_id AS dokterpenanggungjawab_id,
                    dokter.nama_pegawai,
                    pendaftaran_t.pasien_id,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    daftartindakan_m.daftartindakan_id,
                    concat('Diskon - ', daftartindakan_m.daftartindakan_nama) AS daftartindakan_nama,
                    - tindakankomponen_t.discount_komponen AS tarif_tindakankomp,
                    komponentarif_m.komponentarif_nama,
                    '-'::character varying AS status_bayar,
                    'Diskon'::text AS jenis_transaksi,
                    NULL::bigint AS pelayananjasadokter_id,
                    NULL::integer AS penjamin_id,
                    NULL::character varying AS penjamin_nama,
                    NULL::integer AS carabayar_id,
                    NULL::character varying AS carabayar_nama,
                    0 AS instalasi_id,
                    'DISKON'::text AS instalasi_nama,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    pendaftaran_t.instalasi_id AS instalasi_asal,
                    COALESCE(pendaftaran_t.tgl_stopakomodasi, pasienpulang_ri.tglpasienpulang, pasienpulang_rjrd.tglpasienpulang) AS tglpasienpulang,
                    9999 AS status_bayar_id,
                    NULL::double precision AS tarif_tindakan,
                    NULL::integer AS kelaspelayanan_id,
                    NULL::character varying AS kelaspelayanan_nama,
                    NULL::text AS cyto_tindakan,
                    NULL::text AS penyulit_tindakan,
                    NULL::text AS is_overwrite
                   FROM tindakankomponen_t
                     JOIN ( SELECT a.tindakanpelayanan_id,
                            a.pendaftaran_id,
                            a.tgl_tindakan,
                            a.dokterpenanggungjawab_id,
                            a.daftartindakan_id,
                            a.ruangan_id,
                            a.tindakansudahbayar_id
                           FROM tindakanpelayanan_t a) tindakanpelayanan_t ON tindakankomponen_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
                     JOIN ( SELECT pegawai_m.pegawai_id,
                            pegawai_m.nama_pegawai
                           FROM pegawai_m) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
                     JOIN ( SELECT pendaftaran.pendaftaran_id,
                            pendaftaran.tgl_pendaftaran,
                            pendaftaran.pasienadmisi_id,
                            pendaftaran.no_pendaftaran,
                            pendaftaran.pasien_id,
                            pendaftaran.status_bayar,
                            pendaftaran.instalasi_id,
                            pendaftaran.pasienpulang_id,
                            pendaftaran.tgl_stopakomodasi,
                            pendaftaran.is_stopakomodasi
                           FROM pendaftaran_t pendaftaran) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.pasienadmisi_id,
                            a.pasienpulang_id
                           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     JOIN ( SELECT pasien.pasien_id,
                            pasien.nama_pasien,
                            pasien.no_rekam_medik
                           FROM pasien_m pasien) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     LEFT JOIN ( SELECT a.tglpasienpulang,
                            a.pasienpulang_id
                           FROM pasienpulang_t a) pasienpulang_rjrd ON pendaftaran_t.pasienpulang_id = pasienpulang_rjrd.pasienpulang_id
                     LEFT JOIN ( SELECT a.tglpasienpulang,
                            a.pasienpulang_id
                           FROM pasienpulang_t a) pasienpulang_ri ON pasienadmisi_t.pasienpulang_id = pasienpulang_ri.pasienpulang_id
                     JOIN ( SELECT tindakan.daftartindakan_id,
                            tindakan.daftartindakan_nama
                           FROM daftartindakan_m tindakan) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN ( SELECT komponentarif.komponentarif_id,
                            komponentarif.komponentarif_nama
                           FROM komponentarif_m komponentarif
                             JOIN ( SELECT a.kode_id,
                                    a.kode_transaksi
                                   FROM lookuptransaksi_m a) lookuptransaksi_m ON komponentarif.komponentarif_id = lookuptransaksi_m.kode_id AND lookuptransaksi_m.kode_transaksi::text = 'komponen_jas_dok'::text) komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
                  WHERE tindakankomponen_t.discount_komponen > 0::double precision AND tindakankomponen_t.is_deleted IS FALSE AND tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND
                        CASE
                            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pendaftaran_t.is_stopakomodasi IS TRUE
                            ELSE pendaftaran_t.pasienpulang_id IS NOT NULL
                        END) gabung
             LEFT JOIN ( SELECT a.tgl_flag,
                    a.flag_key
                   FROM pembayaranjasadokter_t a) pembayaranjasadokter_t ON pembayaranjasadokter_t.flag_key = concat(gabung.no_pendaftaran, gabung.dokterpenanggungjawab_id, gabung.tindakanpelayanan_id);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220905_092450_migrate_VCS360_laporanrekapjasadokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220905_092450_migrate_VCS360_laporanrekapjasadokter_v cannot be reverted.\n";

        return false;
    }
    */
}
