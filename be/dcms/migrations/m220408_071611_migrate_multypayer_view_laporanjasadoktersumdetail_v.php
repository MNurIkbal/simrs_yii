<?php

use yii\db\Migration;

/**
 * Class m220408_071611_migrate_multypayer_view_laporanjasadoktersumdetail_v
 */
class m220408_071611_migrate_multypayer_view_laporanjasadoktersumdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanjasadoktersumdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanjasadoktersumdetail_v" AS  SELECT rekap_jasdok.pegawai_id,
                rekap_jasdok.nama_pegawai,
                rekap_jasdok.tgl_tindakan,
                rekap_jasdok.pasien_id,
                rekap_jasdok.no_pendaftaran,
                rekap_jasdok.nama_pasien, 
                rekap_jasdok.daftartindakan_id,
                rekap_jasdok.daftartindakan_nama,
                rekap_jasdok.keterangan,
                rekap_jasdok.instalasi_id,
                rekap_jasdok.instalasi_nama AS instalasi,
                rekap_jasdok.groupcarabayar_id,
                rekap_jasdok.tarif_jasadokter AS total_jasanetto,
                rekap_jasdok.total_bruto,
                rekap_jasdok.total_dpp,
                rekap_jasdok.cara_bayar,
                rekap_jasdok.penjamin_id,
                rekap_jasdok.carabayar_id,
                rekap_jasdok.penjamin_nama,
                rekap_jasdok.carabayar_nama,
                rekap_jasdok.tgl_pasienpulang
               FROM ( SELECT \'PENERIMAAN\'::text AS keterangan,
                        (to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY-MM-DD\'::text))::date AS tgl_tindakan,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.no_pendaftaran,
                        pasien_m.nama_pasien,
                        daftartindakan_m.daftartindakan_id,
                        daftartindakan_m.daftartindakan_nama,
                        tindakanpelayanan_t.dokterpenanggungjawab_id AS pegawai_id,
                        pegawai_m.nama_pegawai,
                        tindakanpelayanan_t.instalasi_id,
                        instalasi_m.instalasi_nama,
                        carabayar_m.groupcarabayar_id,
                        fgetnamalookup(carabayar_m.groupcarabayar_id) AS cara_bayar,
                        sum(tindakankomponen_t.tarif_tindakankomp) AS tarif_jasadokter,
                        ((sum(tindakankomponen_t.tarif_tindakankomp) * (100)::double precision) / (90)::double precision) AS total_bruto,
                        ((((sum(tindakankomponen_t.tarif_tindakankomp) * (100)::double precision) / (90)::double precision) * (50)::double precision) / (100)::double precision) AS total_dpp,
                        penjamin_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        penjamin_m.penjamin_nama,
                        carabayar_m.carabayar_nama,
                        pasienpulang_t.tglpasienpulang AS tgl_pasienpulang
                       FROM (((((((((((tindakanpelayanan_t
                         JOIN tindakankomponen_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id)))
                         JOIN komponentarif_m ON (((tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id) AND (komponentarif_m.is_dokter = true))))
                         JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
                         LEFT JOIN pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                         JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         JOIN penjamin_m ON ((tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         JOIN pasienpulang_t ON ((tindakanpelayanan_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
                      WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakankomponen_t.is_deleted = false))
                      GROUP BY \'PENERIMAAN\'::text, (to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY-MM-DD\'::text))::date, tindakanpelayanan_t.dokterpenanggungjawab_id, pendaftaran_t.pasien_id, pendaftaran_t.no_pendaftaran, pasien_m.nama_pasien, daftartindakan_m.daftartindakan_id, pegawai_m.nama_pegawai, tindakanpelayanan_t.instalasi_id, instalasi_m.instalasi_nama, carabayar_m.groupcarabayar_id, penjamin_m.penjamin_id, penjamin_m.carabayar_id, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pasienpulang_t.tglpasienpulang
                    UNION ALL
                     SELECT \'PENERIMAAN\'::text AS keterangan,
                        (to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY-MM-DD\'::text))::date AS tgl_tindakan,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.no_pendaftaran,
                        pasien_m.nama_pasien,
                        tipepaket_m.tipepaket_id,
                        tipepaket_m.tipepaket_nama,
                        tindakanpelayanan_t.dokterpenanggungjawab_id AS pegawai_id,
                        pegawai_m.nama_pegawai,
                        tindakanpelayanan_t.instalasi_id,
                        instalasi_m.instalasi_nama,
                        carabayar_m.groupcarabayar_id,
                        fgetnamalookup(carabayar_m.groupcarabayar_id) AS cara_bayar,
                        sum(tindakankomponen_t.tarif_tindakankomp) AS tarif_jasadokter,
                        (((sum(tindakankomponen_t.tarif_tindakankomp) * (100)::double precision) / (90)::double precision))::integer AS total_bruto,
                        (((((sum(tindakankomponen_t.tarif_tindakankomp) * (100)::double precision) / (90)::double precision) * (50)::double precision) / (100)::double precision))::integer AS total_dpp,
                        penjamin_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        penjamin_m.penjamin_nama,
                        carabayar_m.carabayar_nama,
                        pasienpulang_t.tglpasienpulang AS tgl_pasienpulang
                       FROM (((((((((((tindakanpelayanan_t
                         JOIN tindakankomponen_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id)))
                         JOIN komponentarif_m ON (((tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id) AND (komponentarif_m.is_dokter = true))))
                         JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
                         LEFT JOIN pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                         JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                         JOIN penjamin_m ON ((tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         JOIN pasienpulang_t ON ((tindakanpelayanan_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
                      WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakankomponen_t.is_deleted = false))
                      GROUP BY \'PENERIMAAN\'::text, (to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY-MM-DD\'::text))::date, tindakanpelayanan_t.dokterpenanggungjawab_id, pendaftaran_t.pasien_id, pendaftaran_t.no_pendaftaran, pasien_m.nama_pasien, tipepaket_m.tipepaket_id, pegawai_m.nama_pegawai, tindakanpelayanan_t.instalasi_id, instalasi_m.instalasi_nama, carabayar_m.groupcarabayar_id, penjamin_m.penjamin_id, penjamin_m.carabayar_id, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pasienpulang_t.tglpasienpulang
                    UNION ALL
                     SELECT
                            CASE
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN \'PENERIMAAN\'::text
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN \'PENGURANGAN\'::text
                                ELSE NULL::text
                            END AS keterangan,
                        (to_char((pembayarantransaksi_t.tgl_transaksi)::timestamp with time zone, \'YYYY-MM-DD\'::text))::date AS tgl_tindakan,
                        pendaftaran_t.pasien_id,
                        NULL::character varying AS no_pendaftaran,
                        pasien_m.nama_pasien,
                        0 AS daftartindakan_id,
                        kategoritransaksi_m.kategoritransaksi_nama AS daftartindakan_nama,
                        pembayarantransaksi_t.pegawai_id,
                        dokter.nama_pegawai,
                        0 AS instalasi_id,
                        kategoritransaksi_m.kategoritransaksi_nama AS instalasi_nama,
                        0 AS groupcarabayar_id,
                        NULL::text AS cara_bayar,
                            CASE
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN sum(pembayarantransaksi_t.jumlah)
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN sum(pembayarantransaksi_t.jumlah)
                                ELSE NULL::double precision
                            END AS tarif_jasadokter,
                        0 AS total_bruto,
                        0 AS total_dpp,
                        NULL::integer AS penjamin_id,
                        NULL::integer AS carabayar_id,
                        NULL::character varying AS penjamin_nama,
                        NULL::character varying AS carabayar_nama,
                        NULL::date AS tgl_pasienpulang
                       FROM ((((pembayarantransaksi_t
                         JOIN pegawai_m dokter ON (((pembayarantransaksi_t.pegawai_id = dokter.pegawai_id) AND (dokter.kelompokpegawai_id = 1))))
                         LEFT JOIN pendaftaran_t ON ((pembayarantransaksi_t.pasien_id = pendaftaran_t.pasien_id)))
                         LEFT JOIN pasien_m ON ((pembayarantransaksi_t.pasien_id = pasien_m.pasien_id)))
                         JOIN kategoritransaksi_m ON ((pembayarantransaksi_t.kategoritransaksi_id = kategoritransaksi_m.kategoritransaksi_id)))
                      WHERE (pembayarantransaksi_t.tipe_transaksi = 701)
                      GROUP BY
                            CASE
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN \'PENERIMAAN\'::text
                                WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN \'PENGURANGAN\'::text
                                ELSE NULL::text
                            END, (to_char((pembayarantransaksi_t.tgl_transaksi)::timestamp with time zone, \'YYYY-MM-DD\'::text))::date, pendaftaran_t.pasien_id, pendaftaran_t.no_pendaftaran, pasien_m.nama_pasien, pembayarantransaksi_t.pegawai_id, dokter.nama_pegawai, 0::integer, kategoritransaksi_m.kategoritransaksi_nama, \'TRANSAKSI KASIR\'::text, NULL::text, pembayarantransaksi_t.jenis_transaksi, pendaftaran_t.penjamin_id, pendaftaran_t.carabayar_id
                    UNION ALL
                     SELECT
                            CASE
                                WHEN (pelayananjasadokter_t.total_jasa > (0)::double precision) THEN \'PENERIMAAN\'::text
                                WHEN (pelayananjasadokter_t.total_jasa < (0)::double precision) THEN \'PENGURANGAN\'::text
                                ELSE NULL::text
                            END AS keterangan,
                        (to_char(pelayananjasadokter_t.tgl_transaksi, \'YYYY-MM-DD\'::text))::date AS tgl_tindakan,
                        0 AS pasien_id,
                        NULL::text AS no_pendaftaran,
                        NULL::text AS nama_pasien,
                        0 AS daftartindakan_id,
                        jasadokter_m.jasadokter_nama AS daftartindakan_nama,
                        pelayananjasadokter_t.pegawai_id,
                        dokter.nama_pegawai,
                        0 AS instalasi_id,
                        jasadokter_m.jasadokter_nama AS instalasi_nama,
                        0 AS groupcarabayar_id,
                        NULL::text AS cara_bayar,
                            CASE
                                WHEN (pelayananjasadokter_t.total_jasa > (0)::double precision) THEN sum(pelayananjasadokter_t.total_jasa)
                                WHEN (pelayananjasadokter_t.total_jasa < (0)::double precision) THEN sum((- pelayananjasadokter_t.total_jasa))
                                ELSE (0)::double precision
                            END AS tarif_jasadokter,
                        0 AS total_bruto,
                        0 AS total_dpp,
                        NULL::integer AS penjamin_id,
                        NULL::integer AS carabayar_id,
                        NULL::character varying AS penjamin_nama,
                        NULL::character varying AS carabayar_nama,
                        NULL::date AS tgl_pasienpulang
                       FROM ((pelayananjasadokter_t
                         JOIN pegawai_m dokter ON ((pelayananjasadokter_t.pegawai_id = dokter.pegawai_id)))
                         JOIN jasadokter_m ON ((pelayananjasadokter_t.jasadokter_id = jasadokter_m.jasadokter_id)))
                      WHERE (pelayananjasadokter_t.is_deleted = false)
                      GROUP BY \'PENGURANGAN\'::text, (to_char(pelayananjasadokter_t.tgl_transaksi, \'YYYY-MM-DD\'::text))::date, pelayananjasadokter_t.pegawai_id, dokter.nama_pegawai, jasadokter_m.jasadokter_nama, pelayananjasadokter_t.total_jasa, 0::integer, \'TRANSAKSI JASA DOKTER\'::text, NULL::text, NULL::integer
                    UNION ALL
                     SELECT \'PENGURANGAN\'::text AS keterangan,
                        (to_char(pembayaran_t.created_date, \'YYYY-MM-DD\'::text))::date AS tgl_tindakan,
                        0 AS pasien_id,
                        pendaftaran_t.no_pendaftaran,
                        pasien_m.nama_pasien,
                        0 AS daftartindakan_id,
                        \'DISKON\'::character varying AS daftartindakan_nama,
                        pembayarandiskon_t.pegawai_id,
                        dokter.nama_pegawai,
                        0 AS instalasi_id,
                        NULL::character varying AS instalasi_nama,
                        0 AS groupcarabayar_id,
                        NULL::text AS cara_bayar,
                        sum(pembayarandiskon_t.total_diskon) AS tarif_jasadokter,
                        (((sum(pembayarandiskon_t.total_diskon) * (100)::double precision) / (90)::double precision))::integer AS total_bruto,
                        (((((sum(pembayarandiskon_t.total_diskon) * (100)::double precision) / (90)::double precision) * (50)::double precision) / (100)::double precision))::integer AS total_dpp,
                        NULL::integer AS penjamin_id,
                        NULL::integer AS carabayar_id,
                        NULL::character varying AS penjamin_nama,
                        NULL::character varying AS carabayar_nama,
                        NULL::date AS tgl_pasienpulang
                       FROM (((((pembayarandiskon_t
                         JOIN pegawai_m dokter ON ((pembayarandiskon_t.pegawai_id = dokter.pegawai_id)))
                         JOIN pembayaran_t ON ((pembayarandiskon_t.pembayaran_id = pembayaran_t.pembayaran_id)))
                         JOIN pendaftaran_t ON ((pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                         JOIN pembayaranpelayanan_t ON (((pembayaran_t.pembayaran_id = pembayaranpelayanan_t.pembayaran_id) AND (pembayaranpelayanan_t.is_deleted = false))))
                      WHERE (pembayarandiskon_t.is_deleted = false)
                      GROUP BY \'PENGURANGAN\'::text, (to_char(pembayaran_t.created_date, \'YYYY-MM-DD\'::text))::date, pembayarandiskon_t.pegawai_id, dokter.nama_pegawai, 0::integer, pendaftaran_t.no_pendaftaran, NULL::text, pasien_m.nama_pasien) rekap_jasdok
              ORDER BY rekap_jasdok.keterangan;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_071611_migrate_multypayer_view_laporanjasadoktersumdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_071611_migrate_multypayer_view_laporanjasadoktersumdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
