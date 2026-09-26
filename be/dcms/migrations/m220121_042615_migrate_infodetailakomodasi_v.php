<?php

use yii\db\Migration;

/**
 * Class m220121_042615_migrate_infodetailakomodasi_v
 */
class m220121_042615_migrate_infodetailakomodasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infodetailakomodasi_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infodetailakomodasi_v\" AS  SELECT 'akomodasi_tagihan'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.penjamin_id,
    akomodasi.kamarruangan_id,
    akomodasi.kelaspelayanan_id,
    akomodasi.kamar,
    akomodasi.kelaspelayanan_nama AS kelas,
    akomodasi.tempat_tidur,
    tgl_masuk.tgl_masuk,
    tgl_keluar.tgl_keluar,
        CASE
            WHEN akomodasi.persen = 50 THEN 0.5
            ELSE akomodasi.qty::numeric
        END AS lama_rawat,
    akomodasi.tarif_satuan AS tarif_kamar,
    akomodasi.pembayaran_id
   FROM pendaftaran_t
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.kelaspelayanan_id,
            a.penjamin_id,
            replace(((a.additional_data::json ->> 'detail_akomodasi'::text)::json) ->> 'persentase'::text, '%'::text, ''::text)::integer AS persen,
            sum(a.qty_tindakan) AS qty,
            max(a.tarif_satuan) AS tarif_satuan,
            concat(kamarruangan_m.kamarruangan_nokamar, ' - ', ruangan_m.ruangan_nama) AS kamar,
            kamartempattidur_m.no_tempattidur AS tempat_tidur,
            kelaspelayanan_m.kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id
           FROM tindakanpelayanan_t a
             JOIN daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id AND daftartindakan_m.is_akomodasi = true
             LEFT JOIN kamarruangan_m ON a.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN kamartempattidur_m ON a.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             JOIN kelaspelayanan_m ON a.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN tindakansudahbayar_t ON tindakansudahbayar_t.tindakansudahbayar_id = a.tindakansudahbayar_id
             JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id
          WHERE a.is_deleted = false
          GROUP BY a.pendaftaran_id, a.kamarruangan_id, a.kamartempattidur_id, a.kelaspelayanan_id, a.penjamin_id, kamarruangan_m.kamarruangan_nokamar, ruangan_m.ruangan_nama, kamartempattidur_m.no_tempattidur, kelaspelayanan_m.kelaspelayanan_nama, pembayaranpelayanan_t.pembayaran_id, a.additional_data) akomodasi ON pendaftaran_t.pendaftaran_id = akomodasi.pendaftaran_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            min(a.tgl_tindakan) AS tgl_masuk
           FROM tindakanpelayanan_t a
             JOIN daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id AND daftartindakan_m.is_akomodasi = true
          WHERE a.is_deleted = false
          GROUP BY a.pendaftaran_id, a.kamarruangan_id, a.kamartempattidur_id) tgl_masuk ON akomodasi.pendaftaran_id = tgl_masuk.pendaftaran_id AND akomodasi.kamarruangan_id = tgl_masuk.kamarruangan_id AND akomodasi.kamartempattidur_id = tgl_masuk.kamartempattidur_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            max(a.tgl_tindakan) AS tgl_keluar
           FROM tindakanpelayanan_t a
             JOIN daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id AND daftartindakan_m.is_akomodasi = true
          WHERE a.is_deleted = false
          GROUP BY a.pendaftaran_id, a.kamarruangan_id, a.kamartempattidur_id) tgl_keluar ON akomodasi.pendaftaran_id = tgl_keluar.pendaftaran_id AND akomodasi.kamarruangan_id = tgl_keluar.kamarruangan_id AND akomodasi.kamartempattidur_id = tgl_keluar.kamartempattidur_id;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220121_042615_migrate_infodetailakomodasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220121_042615_migrate_infodetailakomodasi_v cannot be reverted.\n";

        return false;
    }
    */
}
