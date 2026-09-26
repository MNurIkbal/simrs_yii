<?php

use yii\db\Migration;

/**
 * Class m220531_121503_migrate_VCS170_infopasienlabdetail_v
 */
class m220531_121503_migrate_VCS170_infopasienlabdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienlabdetail_v";');
        $this->execute("CREATE VIEW \"public\".\"infopasienlabdetail_v\" AS  SELECT 'NON_PAKET'::text AS jenis,
        tindakanpelayanan_t.tindakanpelayanan_id,
        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
        tindakanpelayanan_t.tgl_tindakan,
        jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
        tindakanpelayanan_t.tipepaket_id,
        ''::character varying AS tipepaket_nama,
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
                WHEN tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN false
                WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN false
                ELSE true
            END AS is_bayar,
            CASE
                WHEN tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'Belum Bayar'::text
                WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'Batal Bayar'::text
                ELSE 'Sudah Bayar'::text
            END AS status_bayar,
            CASE
                WHEN tindakanpelayanan_t.is_deleted = true THEN 'BATAL'::text
                WHEN ambilsample_t.ambilsample_id IS NULL AND hasil.tindakanpelayanan_id IS NULL THEN 'BELUM PERIKSA'::text
                WHEN ambilsample_t.ambilsample_id IS NOT NULL AND hasil.tindakanpelayanan_id IS NULL THEN 'PERIKSA'::text
                WHEN ambilsample_t.ambilsample_id IS NOT NULL AND hasil.tindakanpelayanan_id IS NOT NULL THEN 'SELESAI'::text
                WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'BATAL'::text
                ELSE NULL::text
            END AS status_periksa,
            CASE
                WHEN tindakanpelayanan_t.is_deleted = true THEN 476
                WHEN ambilsample_t.ambilsample_id IS NULL AND hasil.tindakanpelayanan_id IS NULL THEN 477
                WHEN ambilsample_t.ambilsample_id IS NOT NULL AND hasil.tindakanpelayanan_id IS NULL THEN 473
                WHEN ambilsample_t.ambilsample_id IS NOT NULL AND hasil.tindakanpelayanan_id IS NOT NULL THEN 475
                WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 476
                ELSE NULL::integer
            END AS status_periksa_id,
        tindakanpelayanan_t.ruangan_id,
        tindakanpelayanan_t.kelaspelayanan_id,
        tindakanpelayanan_t.penjamin_id,
        tindakanpelayanan_t.carabayar_id,
        tindakanpelayanan_t.pasienadmisi_id,
        pendaftaran_t.no_pendaftaran,
        tindakanpelayanan_t.dokterpenanggungjawab_id AS pegawai_id,
        tindakanpelayanan_t.tindakanpelayananasal_id,
        COALESCE((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text, (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) AS received_flag,
        pasienmasukpenunjang_t.is_hasil
       FROM pendaftaran_t
         JOIN ( SELECT a.tindakanpelayanan_id,
                a.tgl_tindakan,
                a.tipepaket_id,
                a.daftartindakan_id,
                a.tarif_satuan,
                a.cyto_tindakan,
                a.tarifcyto_tindakan,
                a.tarif_tindakan,
                a.qty_tindakan,
                a.is_deleted,
                a.tindakansudahbayar_id,
                a.ruangan_id,
                a.kelaspelayanan_id,
                a.penjamin_id,
                a.carabayar_id,
                a.pasienadmisi_id,
                a.dokterpenanggungjawab_id,
                a.tindakanpelayananasal_id,
                a.pendaftaran_id,
                a.pasienmasukpenunjang_id
               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
         LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                a.additional_data,
                a.is_hasil
               FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
         LEFT JOIN ( SELECT a.additional_data,
                a.pasienmasukpenunjang_id
               FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id
         JOIN ( SELECT a.daftartindakan_nama,
                a.daftartindakan_id,
                a.kelompoktindakan_id
               FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
         LEFT JOIN ( SELECT a.tindakanpelayanan_id,
                a.pemeriksaanlab_id
               FROM permintaankepenunjang_t a) permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
         LEFT JOIN ( SELECT a.pemeriksaanlab_id,
                a.jenispemeriksaanlab_id
               FROM pemeriksaanlab_m a) pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
         LEFT JOIN ( SELECT a.jenispemeriksaanlab_nama,
                a.jenispemeriksaanlab_id
               FROM jenispemeriksaanlab_m a) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
         LEFT JOIN ( SELECT a.ambilsample_id,
                a.tindakanpelayanan_id,
                a.is_deleted
               FROM ambilsample_t a) ambilsample_t ON tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id AND ambilsample_t.is_deleted = false
         LEFT JOIN ( SELECT hasilpemeriksaanlabdetail_t.tindakanpelayanan_id
               FROM hasilpemeriksaanlabdetail_t
              WHERE hasilpemeriksaanlabdetail_t.is_deleted = false
              GROUP BY hasilpemeriksaanlabdetail_t.tindakanpelayanan_id) hasil ON tindakanpelayanan_t.tindakanpelayanan_id = hasil.tindakanpelayanan_id
      WHERE daftartindakan_m.kelompoktindakan_id = 26
    UNION ALL
     SELECT 'PAKET'::text AS jenis,
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
                WHEN tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN false
                WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN false
                ELSE true
            END AS is_bayar,
            CASE
                WHEN tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'Belum Bayar'::text
                WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'Batal Bayar'::text
                ELSE 'Sudah Bayar'::text
            END AS status_bayar,
            CASE
                WHEN tindakanpelayanan_t.is_deleted = true THEN 'BATAL'::text
                WHEN ambilsample_t.ambilsample_id IS NULL AND hasil.tindakanpelayanan_id IS NULL THEN 'BELUM PERIKSA'::text
                WHEN ambilsample_t.ambilsample_id IS NOT NULL AND hasil.tindakanpelayanan_id IS NULL THEN 'PERIKSA'::text
                WHEN ambilsample_t.ambilsample_id IS NOT NULL AND hasil.tindakanpelayanan_id IS NOT NULL THEN 'SELESAI'::text
                WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'BATAL'::text
                ELSE NULL::text
            END AS status_periksa,
            CASE
                WHEN tindakanpelayanan_t.is_deleted = true THEN 476
                WHEN ambilsample_t.ambilsample_id IS NULL AND hasil.tindakanpelayanan_id IS NULL THEN 477
                WHEN ambilsample_t.ambilsample_id IS NOT NULL AND hasil.tindakanpelayanan_id IS NULL THEN 473
                WHEN ambilsample_t.ambilsample_id IS NOT NULL AND hasil.tindakanpelayanan_id IS NOT NULL THEN 475
                WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 476
                ELSE NULL::integer
            END AS status_periksa_id,
        tindakanpelayanan_t.ruangan_id,
        tindakanpelayanan_t.kelaspelayanan_id,
        tindakanpelayanan_t.penjamin_id,
        tindakanpelayanan_t.carabayar_id,
        tindakanpelayanan_t.pasienadmisi_id,
        pendaftaran_t.no_pendaftaran,
        tindakanpelayanan_t.dokterpenanggungjawab_id AS pegawai_id,
        tindakanpelayanan_t.tindakanpelayananasal_id,
        COALESCE((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text, (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) AS received_flag,
        pasienmasukpenunjang_t.is_hasil
       FROM pendaftaran_t
         JOIN ( SELECT a.tindakanpelayanan_id,
                a.tgl_tindakan,
                a.tipepaket_id,
                a.tarif_satuan,
                a.cyto_tindakan,
                a.tarifcyto_tindakan,
                a.tarif_tindakan,
                a.qty_tindakan,
                a.is_deleted,
                a.tindakansudahbayar_id,
                a.ruangan_id,
                a.kelaspelayanan_id,
                a.penjamin_id,
                a.carabayar_id,
                a.pasienadmisi_id,
                a.dokterpenanggungjawab_id,
                a.tindakanpelayananasal_id,
                a.pendaftaran_id,
                a.parent_id
               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.parent_id IS NOT NULL
         LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                a.additional_data,
                a.is_hasil,
                a.pendaftaran_id,
                a.instalasiasal_id
               FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.instalasiasal_id = 4
         LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                a.additional_data
               FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id
         LEFT JOIN ( SELECT a.tipepaket_nama,
                a.tipepaket_id,
                a.is_mcu
               FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id AND tipepaket_m.is_mcu = false
         JOIN ( SELECT a.daftartindakan_id,
                a.tipepaket_id
               FROM paketpelayanan_mp a) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
         JOIN ( SELECT a.daftartindakan_nama,
                a.daftartindakan_id,
                a.kelompoktindakan_id
               FROM daftartindakan_m a) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
         LEFT JOIN ( SELECT a.tindakanpelayanan_id,
                a.pemeriksaanlab_id
               FROM permintaankepenunjang_t a) permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
         LEFT JOIN ( SELECT a.pemeriksaanlab_id,
                a.jenispemeriksaanlab_id
               FROM pemeriksaanlab_m a) pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
         LEFT JOIN ( SELECT a.jenispemeriksaanlab_nama,
                a.jenispemeriksaanlab_id
               FROM jenispemeriksaanlab_m a) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
         LEFT JOIN ( SELECT a.ambilsample_id,
                a.tindakanpelayanan_id,
                a.is_deleted
               FROM ambilsample_t a) ambilsample_t ON tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id AND ambilsample_t.is_deleted = false
         LEFT JOIN ( SELECT hasilpemeriksaanlabdetail_t.tindakanpelayanan_id
               FROM hasilpemeriksaanlabdetail_t
              WHERE hasilpemeriksaanlabdetail_t.is_deleted = false
              GROUP BY hasilpemeriksaanlabdetail_t.tindakanpelayanan_id) hasil ON tindakanpelayanan_t.tindakanpelayanan_id = hasil.tindakanpelayanan_id
      WHERE daftartindakan_m.kelompoktindakan_id = 26;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220531_121503_migrate_VCS170_infopasienlabdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220531_121503_migrate_VCS170_infopasienlabdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
