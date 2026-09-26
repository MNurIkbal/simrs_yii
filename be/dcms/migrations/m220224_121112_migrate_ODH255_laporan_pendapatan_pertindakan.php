<?php

use yii\db\Migration;

/**
 * Class m220224_121112_migrate_ODH255_laporan_pendapatan_pertindakan
 */
class m220224_121112_migrate_ODH255_laporan_pendapatan_pertindakan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanpendapatantindakan_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanpendapatantindakan_v" AS  SELECT pendaftaran.pelayanan,
                (pembayaranpelayanan.tgl_pembayaran)::date AS tgl_pembayaran,
                (pendaftaran.tglpasienpulang)::date AS tglpasienpulang,
                tindakan.kelaspelayanan_id,
                tindakan.kelaspelayanan_nama,
                tindakan.kelompoktindakan_id,
                tindakan.kelompoktindakan_nama,
                tindakan.daftartindakan_kode,
                tindakan.daftartindakan_nama,
                sum(tindakan.total_harga) AS total_harga,
                sum(tindakan.qty) AS qty
               FROM (((pembayaran_t
                 JOIN ( SELECT
                            CASE
                                WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN \'RAJAL\'::text
                                ELSE \'RANAP\'::text
                            END AS pelayanan,
                        pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.instalasi_id,
                        COALESCE(pasienpulang_t.tglpasienpulang, pendaftaran_t.tgl_stopakomodasi, pendaftaran_t.tgl_pendaftaran) AS tglpasienpulang
                       FROM ((pendaftaran_t
                         LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                                pasienadmisi_t.pasienpulang_id
                               FROM pasienadmisi_t) pasienadmisi ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id)))
                         LEFT JOIN pasienpulang_t ON ((COALESCE(pasienadmisi.pasienpulang_id, pendaftaran_t.pasienpulang_id) = pasienpulang_t.pasienpulang_id)))) pendaftaran ON ((pembayaran_t.pendaftaran_id = pendaftaran.pendaftaran_id)))
                 JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.tgl_pembayaran
                       FROM pembayaranpelayanan_t) pembayaranpelayanan ON ((pembayaran_t.pembayaran_id = pembayaranpelayanan.pembayaran_id)))
                 JOIN ( SELECT tindakansudahbayar_t.pembayaranpelayanan_id,
                        tindakanpelayanan.total_harga,
                        tindakanpelayanan.qty,
                        tindakanpelayanan.daftartindakan_id,
                        tindakanpelayanan.daftartindakan_nama,
                        tindakanpelayanan.daftartindakan_kode,
                        tindakanpelayanan.kelompoktindakan_id,
                        tindakanpelayanan.kelompoktindakan_nama,
                        tindakanpelayanan.kelaspelayanan_id,
                        tindakanpelayanan.kelaspelayanan_nama
                       FROM (tindakansudahbayar_t
                         JOIN ( SELECT tindakanpelayanan_t.tindakansudahbayar_id,
                                sum(tindakanpelayanan_t.tarif_tindakan) AS total_harga,
                                sum(tindakanpelayanan_t.qty_tindakan) AS qty,
                                daftartindakan_m.daftartindakan_id,
                                daftartindakan_m.daftartindakan_kode,
                                daftartindakan_m.daftartindakan_nama,
                                kelompoktindakan_m.kelompoktindakan_id,
                                kelompoktindakan_m.kelompoktindakan_nama,
                                kelaspelayanan_m.kelaspelayanan_id,
                                kelaspelayanan_m.kelaspelayanan_nama
                               FROM (((tindakanpelayanan_t
                                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                                 LEFT JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                                 JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                              WHERE ((tindakanpelayanan_t.is_deleted IS FALSE) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.tarif_tindakan > (0)::double precision))
                              GROUP BY daftartindakan_m.daftartindakan_id, daftartindakan_m.daftartindakan_nama, kelompoktindakan_m.kelompoktindakan_nama, tindakanpelayanan_t.tindakansudahbayar_id, kelaspelayanan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, daftartindakan_m.daftartindakan_kode, kelompoktindakan_m.kelompoktindakan_id) tindakanpelayanan ON ((tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan.tindakansudahbayar_id)))
                    UNION ALL
                     SELECT obatsudahbayar_t.pembayaranpelayanan_id,
                        obat.total_harga,
                        obat.qty,
                        obat.obatalkes_id AS daftartindakan_id,
                        obat.obatalkes_nama AS daftartindakan_nama,
                        obat.obatalkes_kode AS daftartindakan_kode,
                        obat.kelompoktindakan_id,
                        obat.kelompoktindakan_nama,
                        obat.kelaspelayanan_id,
                        obat.kelaspelayanan_nama 
                       FROM (obatsudahbayar_t
                         JOIN ( SELECT obatalkespasien_t.obatsudahbayar_id,
                                sum(obatalkespasien_t.hargajual_oa) AS total_harga,
                                sum(obatalkespasien_t.qty_oa) AS qty,
                                obatalkes.obatalkes_id,
                                obatalkes.obatalkes_kode,
                                obatalkes.obatalkes_nama,
                                0 AS kelompoktindakan_id,
                                \'Obat Alkes\'::text AS kelompoktindakan_nama,
                                kelaspelayanan_m.kelaspelayanan_id,
                                kelaspelayanan_m.kelaspelayanan_nama
                               FROM ((obatalkespasien_t
                                 LEFT JOIN ( SELECT obatalkes_m.obatalkes_id,
                                        obatalkes_m.obatalkes_kode,
                                        obatalkes_m.obatalkes_nama
                                       FROM obatalkes_m) obatalkes ON ((obatalkespasien_t.obatalkes_id = obatalkes.obatalkes_id)))
                                 JOIN kelaspelayanan_m ON ((obatalkespasien_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                              WHERE ((obatalkespasien_t.is_deleted IS FALSE) AND (obatalkespasien_t.obatsudahbayar_id IS NOT NULL) AND (obatalkespasien_t.hargajual_oa > (0)::double precision))
                              GROUP BY obatalkespasien_t.obatsudahbayar_id, obatalkes.obatalkes_id, obatalkes.obatalkes_nama, kelaspelayanan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, obatalkes.obatalkes_kode) obat ON ((obatsudahbayar_t.obatsudahbayar_id = obat.obatsudahbayar_id)))
                    UNION ALL
                     SELECT pembayaranpelayanan_t.pembayaranpelayanan_id,
                        sum(pembayaran_t_1.total_administrasi) AS total_harga,
                        count(pembayaran_t_1.pembayaran_id) AS qty,
                        NULL::integer AS daftartindakan_id,
                        daftartindakan_m.daftartindakan_nama,
                        daftartindakan_m.daftartindakan_kode,
                        kelompoktindakan_m.kelompoktindakan_id,
                        kelompoktindakan_m.kelompoktindakan_nama,
                        NULL::integer AS kelaspelayanan_id,
                        NULL::character varying AS kelaspelayanan_nama
                       FROM ((((pembayaran_t pembayaran_t_1
                         JOIN pembayaranpelayanan_t ON ((pembayaran_t_1.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
                         JOIN lookuptransaksi_m ON (((lookuptransaksi_m.kode_transaksi)::text = \'ADMINISTRASI\'::text)))
                         JOIN daftartindakan_m ON ((lookuptransaksi_m.kode_id = daftartindakan_m.daftartindakan_id)))
                         JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                      WHERE ((pembayaran_t_1.is_deleted IS FALSE) AND (pembayaran_t_1.total_administrasi > (0)::double precision))
                      GROUP BY pembayaranpelayanan_t.pembayaranpelayanan_id, kelompoktindakan_m.kelompoktindakan_id, kelompoktindakan_m.kelompoktindakan_nama, daftartindakan_m.daftartindakan_nama, daftartindakan_m.daftartindakan_kode) tindakan ON ((pembayaranpelayanan.pembayaranpelayanan_id = tindakan.pembayaranpelayanan_id)))
              WHERE (pembayaran_t.is_deleted IS FALSE)
              GROUP BY pendaftaran.pelayanan, ((pembayaranpelayanan.tgl_pembayaran)::date), ((pendaftaran.tglpasienpulang)::date), tindakan.kelaspelayanan_id, tindakan.kelaspelayanan_nama, tindakan.kelompoktindakan_nama, tindakan.daftartindakan_kode, tindakan.daftartindakan_nama, tindakan.kelompoktindakan_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220224_121112_migrate_ODH255_laporan_pendapatan_pertindakan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220224_121112_migrate_ODH255_laporan_pendapatan_pertindakan cannot be reverted.\n";

        return false;
    }
    */
}
