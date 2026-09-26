<?php

use yii\db\Migration;

/**
 * Class m220311_063857_migrate_ODH241_view_infopasienlabdetail_v
 */
class m220311_063857_migrate_ODH241_view_infopasienlabdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienlabdetail_v";
        ');

         $this->execute('
            CREATE VIEW "public"."infopasienlabdetail_v" AS  SELECT \'NON_PAKET\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tgl_tindakan,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                tindakanpelayanan_t.tipepaket_id,
                \'\'::character varying AS tipepaket_nama,
                NULL::text AS detail_2,
                tindakanpelayanan_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.cyto_tindakan,
                tindakanpelayanan_t.tarifcyto_tindakan, 
                tindakanpelayanan_t.tarif_tindakan,
                ambilsample_t.ambilsample_id,
                tindakanpelayanan_t.qty_tindakan,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasien_id,
                tindakanpelayanan_t.is_deleted,
                    CASE
                        WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
                        ELSE true
                    END AS is_bayar,
                    CASE
                        WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'Belum Bayar\'::text
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'Batal Bayar\'::text
                        ELSE \'Sudah Bayar\'::text
                    END AS status_bayar,
                    CASE
                        WHEN (tindakanpelayanan_t.is_deleted = true) THEN \'BATAL\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN \'BELUM PERIKSA\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN \'PERIKSA\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN \'SELESAI\'::text
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
                        ELSE NULL::text
                    END AS status_periksa,
                    CASE
                        WHEN (tindakanpelayanan_t.is_deleted = true) THEN 476
                        WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 477
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 473
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN 475
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 476
                        ELSE NULL::integer
                    END AS status_periksa_id,
                tindakanpelayanan_t.ruangan_id,
                tindakanpelayanan_t.kelaspelayanan_id,
                tindakanpelayanan_t.penjamin_id,
                tindakanpelayanan_t.carabayar_id,
                tindakanpelayanan_t.pasienadmisi_id,
                pendaftaran_t.no_pendaftaran,
                tindakanpelayanan_t.dokterpenanggungjawab_id AS pegawai_id,
                tindakanpelayanan_t.tindakanpelayananasal_id
               FROM ((((((((pendaftaran_t
                 JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                 LEFT JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                 LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                 LEFT JOIN ambilsample_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id) AND (ambilsample_t.is_deleted = false))))
                 LEFT JOIN ( SELECT hasilpemeriksaanlabdetail_t.tindakanpelayanan_id
                       FROM hasilpemeriksaanlabdetail_t
                      WHERE (hasilpemeriksaanlabdetail_t.is_deleted = false)
                      GROUP BY hasilpemeriksaanlabdetail_t.tindakanpelayanan_id) hasil ON ((tindakanpelayanan_t.tindakanpelayanan_id = hasil.tindakanpelayanan_id)))
              WHERE (daftartindakan_m.kelompoktindakan_id = 26)
            UNION ALL
             SELECT \'PAKET\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tgl_tindakan,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                tindakanpelayanan_t.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                NULL::text AS detail_2,
                paketpelayanan_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.cyto_tindakan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                ambilsample_t.ambilsample_id,
                tindakanpelayanan_t.qty_tindakan,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasien_id,
                tindakanpelayanan_t.is_deleted,
                    CASE
                        WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
                        ELSE true
                    END AS is_bayar,
                    CASE
                        WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'Belum Bayar\'::text
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'Batal Bayar\'::text
                        ELSE \'Sudah Bayar\'::text
                    END AS status_bayar,
                    CASE
                        WHEN (tindakanpelayanan_t.is_deleted = true) THEN \'BATAL\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN \'BELUM PERIKSA\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN \'PERIKSA\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN \'SELESAI\'::text
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
                        ELSE NULL::text
                    END AS status_periksa,
                    CASE
                        WHEN (tindakanpelayanan_t.is_deleted = true) THEN 476
                        WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 477
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 473
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN 475
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 476
                        ELSE NULL::integer
                    END AS status_periksa_id,
                tindakanpelayanan_t.ruangan_id,
                tindakanpelayanan_t.kelaspelayanan_id,
                tindakanpelayanan_t.penjamin_id,
                tindakanpelayanan_t.carabayar_id,
                tindakanpelayanan_t.pasienadmisi_id,
                pendaftaran_t.no_pendaftaran,
                tindakanpelayanan_t.dokterpenanggungjawab_id AS pegawai_id,
                tindakanpelayanan_t.tindakanpelayananasal_id
               FROM ((((((((((pendaftaran_t
                 JOIN tindakanpelayanan_t ON (((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.parent_id IS NOT NULL))))
                 LEFT JOIN pasienmasukpenunjang_t ON (((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id) AND (pasienmasukpenunjang_t.instalasiasal_id = 4))))
                 LEFT JOIN tipepaket_m ON (((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id) AND (tipepaket_m.is_mcu = false))))
                 JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                 LEFT JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                 LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                 LEFT JOIN ambilsample_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id) AND (ambilsample_t.is_deleted = false))))
                 LEFT JOIN ( SELECT hasilpemeriksaanlabdetail_t.tindakanpelayanan_id
                       FROM hasilpemeriksaanlabdetail_t
                      WHERE (hasilpemeriksaanlabdetail_t.is_deleted = false)
                      GROUP BY hasilpemeriksaanlabdetail_t.tindakanpelayanan_id) hasil ON ((tindakanpelayanan_t.tindakanpelayanan_id = hasil.tindakanpelayanan_id)))
              WHERE (daftartindakan_m.kelompoktindakan_id = 26);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220311_063857_migrate_ODH241_view_infopasienlabdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220311_063857_migrate_ODH241_view_infopasienlabdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
