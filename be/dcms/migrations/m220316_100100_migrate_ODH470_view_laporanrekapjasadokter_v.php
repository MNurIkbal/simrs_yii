<?php

use yii\db\Migration;

/**
 * Class m220316_100100_migrate_ODH470_view_laporanrekapjasadokter_v
 */
class m220316_100100_migrate_ODH470_view_laporanrekapjasadokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanrekapjasadokter_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanrekapjasadokter_v" AS  SELECT gabung.pendaftaran_id,
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
                        WHEN (gabung.jenis_transaksi = ANY (ARRAY[\'pelayanan\'::text])) THEN ((gabung.tarif_tindakankomp * (100)::double precision) / (90)::double precision)
                        ELSE (0)::double precision
                    END AS bruto,
                    CASE
                        WHEN (gabung.jenis_transaksi = ANY (ARRAY[\'pelayanan\'::text])) THEN ((((gabung.tarif_tindakankomp * (100)::double precision) / (90)::double precision) * (50)::double precision) / (100)::double precision)
                        ELSE (0)::double precision
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
                        WHEN ((gabung.pasienadmisi_id IS NULL) AND (gabung.instalasi_asal <> 2)) THEN \'RAJAL\'::text
                        WHEN ((gabung.pasienadmisi_id IS NULL) AND (gabung.instalasi_asal = 2)) THEN \'IGD\'::text
                        ELSE \'RANAP\'::text
                    END AS pelayanan,
                gabung.tglpasienpulang AS tgl_pasienpulang,
                gabung.status_bayar_id,
                gabung.tarif_tindakan,
                gabung.kelaspelayanan_id,
                gabung.kelaspelayanan_nama,
                concat(gabung.cyto, gabung.penyulit, gabung.overwrite) AS kondisi
               FROM ( SELECT \'PENERIMAAN\'::text AS keterangan,
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
                        \'pelayanan\'::text AS jenis_transaksi,
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
                        pasienpulang_t.tglpasienpulang,
                        pendaftaran_t.status_bayar AS status_bayar_id,
                        tindakanpelayanan_t.tarif_tindakan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                            CASE
                                WHEN (tindakanpelayanan_t.cyto_tindakan IS TRUE) THEN \'Cyto\'::text
                                ELSE \'\'::text
                            END AS cyto,
                            CASE
                                WHEN (tindakanpelayanan_t.penyulit_tindakan IS TRUE) THEN
                                CASE
                                    WHEN (tindakanpelayanan_t.cyto_tindakan IS TRUE) THEN \', Penyulit\'::text
                                    ELSE \'Penyulit\'::text
                                END
                                ELSE \'\'::text
                            END AS penyulit,
                            CASE
                                WHEN (COALESCE(tindakanpelayanan_t.is_overwrite, false) IS TRUE) THEN
                                CASE
                                    WHEN (tindakanpelayanan_t.cyto_tindakan OR (tindakanpelayanan_t.penyulit_tindakan IS TRUE)) THEN \', Edit Harga\'::text
                                    ELSE \'Edit Harga\'::text
                                END
                                ELSE \'\'::text
                            END AS overwrite
                       FROM (((((((((((((pendaftaran_t
                         JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                         JOIN tindakankomponen_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id)))
                         JOIN ( SELECT komponentarif.komponentarif_id,
                                komponentarif.komponentarif_nama
                               FROM (komponentarif_m komponentarif
                                 JOIN lookuptransaksi_m ON (((komponentarif.komponentarif_id = lookuptransaksi_m.kode_id) AND ((lookuptransaksi_m.kode_transaksi)::text = \'komponen_jas_dok\'::text))))) komponentarif_m ON ((tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id)))
                         LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
                         JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                         JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         JOIN penjamin_m ON ((tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         JOIN ( SELECT max(a.tglpasienpulang) AS tglpasienpulang,
                                a.pendaftaran_id
                               FROM pasienpulang_t a
                              GROUP BY a.pendaftaran_id) pasienpulang_t ON ((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
                         LEFT JOIN lookup_m status_bayar ON ((pendaftaran_t.status_bayar = status_bayar.lookup_id)))
                         LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                      WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakankomponen_t.is_deleted = false))
                    UNION ALL
                     SELECT \'PENERIMAAN APS\'::text AS keterangan,
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
                        \'pelayanan\'::text AS jenis_transaksi,
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
                        COALESCE(pasienpulang_t.tglpasienpulang, pendaftaran_t.tgl_pendaftaran) AS tglpasienpulang,
                        pendaftaran_t.status_bayar AS status_bayar_id,
                        tindakanpelayanan_t.tarif_tindakan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                            CASE
                                WHEN (tindakanpelayanan_t.cyto_tindakan IS TRUE) THEN \'Cyto\'::text
                                ELSE \'\'::text
                            END AS cyto,
                            CASE
                                WHEN (tindakanpelayanan_t.penyulit_tindakan IS TRUE) THEN
                                CASE
                                    WHEN (tindakanpelayanan_t.cyto_tindakan IS TRUE) THEN \', Penyulit\'::text
                                    ELSE \'Penyulit\'::text
                                END
                                ELSE \'\'::text
                            END AS penyulit,
                            CASE
                                WHEN (COALESCE(tindakanpelayanan_t.is_overwrite, false) IS TRUE) THEN
                                CASE
                                    WHEN (tindakanpelayanan_t.cyto_tindakan OR (tindakanpelayanan_t.penyulit_tindakan IS TRUE)) THEN \', Edit Harga\'::text
                                    ELSE \'Edit Harga\'::text
                                END
                                ELSE \'\'::text
                            END AS overwrite
                       FROM (((((((((((((pendaftaran_t
                         JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                         JOIN tindakankomponen_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id)))
                         JOIN ( SELECT komponentarif.komponentarif_id,
                                komponentarif.komponentarif_nama
                               FROM (komponentarif_m komponentarif
                                 JOIN lookuptransaksi_m ON (((komponentarif.komponentarif_id = lookuptransaksi_m.kode_id) AND ((lookuptransaksi_m.kode_transaksi)::text = \'komponen_jas_dok\'::text))))) komponentarif_m ON ((tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id)))
                         LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
                         JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                         JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         JOIN penjamin_m ON ((tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN ( SELECT max(a.tglpasienpulang) AS tglpasienpulang,
                                a.pendaftaran_id
                               FROM pasienpulang_t a
                              GROUP BY a.pendaftaran_id) pasienpulang_t ON ((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
                         LEFT JOIN lookup_m status_bayar ON ((pendaftaran_t.status_bayar = status_bayar.lookup_id)))
                         LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                      WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakankomponen_t.is_deleted = false) AND (pendaftaran_t.is_aps IS TRUE))
                    UNION ALL
                     SELECT \'PENERIMAAN\'::text AS keterangan,
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
                        \'pelayanan\'::text AS jenis_transaksi,
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
                        pasienpulang_t.tglpasienpulang,
                        pendaftaran_t.status_bayar AS status_bayar_id,
                        tindakanpelayanan_t.tarif_tindakan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                            CASE
                                WHEN (tindakanpelayanan_t.cyto_tindakan IS TRUE) THEN \'Cyto\'::text
                                ELSE \'\'::text
                            END AS cyto,
                            CASE
                                WHEN (tindakanpelayanan_t.penyulit_tindakan IS TRUE) THEN
                                CASE
                                    WHEN (tindakanpelayanan_t.cyto_tindakan IS TRUE) THEN \', Penyulit\'::text
                                    ELSE \'Penyulit\'::text
                                END
                                ELSE \'\'::text
                            END AS penyulit,
                            CASE
                                WHEN (COALESCE(tindakanpelayanan_t.is_overwrite, false) IS TRUE) THEN
                                CASE
                                    WHEN (tindakanpelayanan_t.cyto_tindakan OR (tindakanpelayanan_t.penyulit_tindakan IS TRUE)) THEN \', Edit Harga\'::text
                                    ELSE \'Edit Harga\'::text
                                END
                                ELSE \'\'::text
                            END AS overwrite
                       FROM (((((((((((((pendaftaran_t
                         JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                         JOIN tindakankomponen_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id)))
                         JOIN ( SELECT komponentarif.komponentarif_id,
                                komponentarif.komponentarif_nama
                               FROM (komponentarif_m komponentarif
                                 JOIN lookuptransaksi_m ON (((komponentarif.komponentarif_id = lookuptransaksi_m.kode_id) AND ((lookuptransaksi_m.kode_transaksi)::text = \'komponen_jas_dok\'::text))))) komponentarif_m ON ((tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id)))
                         LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
                         JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                         JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                         JOIN penjamin_m ON ((tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         JOIN ( SELECT max(a.tglpasienpulang) AS tglpasienpulang,
                                a.pendaftaran_id
                               FROM pasienpulang_t a
                              GROUP BY a.pendaftaran_id) pasienpulang_t ON ((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
                         LEFT JOIN lookup_m status_bayar ON ((pendaftaran_t.status_bayar = status_bayar.lookup_id)))
                         LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                      WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakankomponen_t.is_deleted = false))
                    UNION ALL
                     SELECT
                            CASE
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN \'PENERIMAAN\'::text
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN \'PENGURANGAN\'::text
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
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN pembayarantransaksi_t.jumlah
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN (- pembayarantransaksi_t.jumlah)
                                ELSE NULL::double precision
                            END AS tarif_tindakankomp,
                            CASE
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN \'Penerimaan\'::text
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN \'Pengeluaran\'::text
                                ELSE NULL::text
                            END AS komponentarif_nama,
                        \'-\'::text AS status_bayar,
                        \'Transaksi Kasir\'::text AS jenis_transaksi,
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
                       FROM ((pembayarantransaksi_t
                         JOIN pegawai_m dokter ON (((pembayarantransaksi_t.pegawai_id = dokter.pegawai_id) AND (dokter.kelompokpegawai_id = 1))))
                         JOIN kategoritransaksi_m ON ((pembayarantransaksi_t.kategoritransaksi_id = kategoritransaksi_m.kategoritransaksi_id)))
                      WHERE (pembayarantransaksi_t.tipe_transaksi = 701)
                    UNION ALL
                     SELECT
                            CASE
                                WHEN (pelayananjasadokter_t.total_jasa > (0)::double precision) THEN \'PENERIMAAN\'::text
                                WHEN (pelayananjasadokter_t.total_jasa < (0)::double precision) THEN \'PENGURANGAN\'::text
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
                        \'-\'::character varying AS status_bayar,
                        \'Transaksi Jasa Dokter\'::text AS jenis_transaksi,
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
                       FROM ((pelayananjasadokter_t
                         JOIN pegawai_m dokter ON ((pelayananjasadokter_t.pegawai_id = dokter.pegawai_id)))
                         JOIN jasadokter_m ON ((pelayananjasadokter_t.jasadokter_id = jasadokter_m.jasadokter_id)))
                      WHERE (pelayananjasadokter_t.is_deleted = false)
                    UNION ALL
                     SELECT \'PENGURANGAN\'::text AS keterangan,
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
                        NULL::character varying AS daftartindakan_nama,
                        (- pembayarandiskon_t.total_diskon) AS tarif_tindakankomp,
                        komponentarif_m.komponentarif_nama,
                            CASE
                                WHEN (fgetnamalookup(pendaftaran_t.status_bayar) IS NULL) THEN fgetnamalookup(2001)
                                ELSE fgetnamalookup(pendaftaran_t.status_bayar)
                            END AS status_bayar,
                        \'Diskon\'::text AS jenis_transaksi,
                        NULL::bigint AS pelayananjasadokter_id,
                        NULL::integer AS penjamin_id,
                        NULL::character varying AS penjamin_nama,
                        NULL::integer AS carabayar_id,
                        NULL::character varying AS carabayar_nama,
                        0 AS instalasi_id,
                        \'DISKON\'::text AS instalasi_nama,
                        0 AS ruangan_id,
                        \'DISKON\'::text AS ruangan_nama,
                        NULL::integer AS instalasi_asal,
                        COALESCE(pasienpulang_t.tglpasienpulang, pendaftaran_t.tgl_pendaftaran) AS tglpasienpulang,
                        9999 AS status_bayar_id,
                        NULL::double precision AS tarif_tindakan,
                        NULL::integer AS kelaspelayanan_id,
                        NULL::character varying AS kelaspelayanan_nama,
                        NULL::text AS cyto_tindakan,
                        NULL::text AS penyulit_tindakan,
                        NULL::text AS is_overwrite
                       FROM (((((((pembayarandiskon_t
                         JOIN pegawai_m dokter ON ((pembayarandiskon_t.pegawai_id = dokter.pegawai_id)))
                         JOIN pembayaran_t ON ((pembayarandiskon_t.pembayaran_id = pembayaran_t.pembayaran_id)))
                         JOIN pembayaranpelayanan_t ON (((pembayaran_t.pembayaran_id = pembayaranpelayanan_t.pembayaran_id) AND (pembayaranpelayanan_t.is_deleted = false))))
                         JOIN pendaftaran_t ON ((pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                         LEFT JOIN komponentarif_m ON ((pembayarandiskon_t.komponentarif_id = komponentarif_m.komponentarif_id)))
                         LEFT JOIN ( SELECT max(a.tglpasienpulang) AS tglpasienpulang,
                                a.pendaftaran_id
                               FROM pasienpulang_t a
                              GROUP BY a.pendaftaran_id) pasienpulang_t ON ((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
                      WHERE (pembayarandiskon_t.is_deleted = false)) gabung ;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220316_100100_migrate_ODH470_view_laporanrekapjasadokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220316_100100_migrate_ODH470_view_laporanrekapjasadokter_v cannot be reverted.\n";

        return false;
    }
    */
}
