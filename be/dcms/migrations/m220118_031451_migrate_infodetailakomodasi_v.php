<?php

use yii\db\Migration;

/**
 * Class m220118_031451_migrate_infodetailakomodasi_v
 */
class m220118_031451_migrate_infodetailakomodasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->execute('DROP VIEW IF EXISTS "public"."infodetailakomodasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infodetailakomodasi_v\" AS  SELECT 'akomodasi_sementara'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.penjamin_id,
    masukkamar_t.kamarruangan_id,
    kamarruangan_m.kelaspelayanan_id,
    concat(kamarruangan_m.kamarruangan_nokamar, '-', ruangan_m.ruangan_nama) AS kamar,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    masukkamar_t.tgl_masukkamar AS tgl_masuk,
    pendaftaran_t.tgl_stopakomodasi::date AS tgl_keluar,
        CASE
            WHEN (CURRENT_DATE - masukkamar_t.tgl_masukkamar::date) = 0 THEN 1
            WHEN (CURRENT_DATE - masukkamar_t.tgl_masukkamar::date) <> 0 THEN CURRENT_DATE - masukkamar_t.tgl_masukkamar::date
            ELSE NULL::integer
        END AS lama_rawat,
    COALESCE(tariftindakan_m.harga_tariftindakan, tarif_default.harga_tariftindakan) AS tarif_kamar
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
     JOIN kamarruangan_m ON masukkamar_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON masukkamar_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN tariftindakan_m ON masukkamar_t.kamarruangan_id = tariftindakan_m.kamarruangan_id AND pasienadmisi_t.kelaspelayanan_id = tariftindakan_m.kelaspelayanan_id AND pasienadmisi_t.penjamin_id = tariftindakan_m.penjamin_id AND tariftindakan_m.komponentarif_id = 6 AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
     LEFT JOIN ( SELECT a.harga_tariftindakan,
            a.penjamin_id,
            a.kelaspelayanan_id,
            a.kamarruangan_id
           FROM tariftindakan_m a
             JOIN konfigtarif_k ON a.penjamin_id = konfigtarif_k.default_penjamin
          WHERE a.is_deleted = false AND a.is_active = true AND a.komponentarif_id = 6) tarif_default ON masukkamar_t.kamarruangan_id = tarif_default.kamarruangan_id AND kamarruangan_m.kelaspelayanan_id = tarif_default.kelaspelayanan_id
  WHERE pendaftaran_t.tgl_stopakomodasi IS NULL
UNION ALL
 SELECT 'akomodasi_tagihan'::text AS tipe,
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
    akomodasi.qty AS lama_rawat,
    akomodasi.tarif_satuan AS tarif_kamar
   FROM pendaftaran_t
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.kelaspelayanan_id,
            a.penjamin_id,
            sum(a.qty_tindakan) AS qty,
            max(a.tarif_satuan) AS tarif_satuan,
            concat(kamarruangan_m.kamarruangan_nokamar, ' - ', ruangan_m.ruangan_nama) AS kamar,
            kamartempattidur_m.no_tempattidur AS tempat_tidur,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM tindakanpelayanan_t a
             JOIN daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id AND daftartindakan_m.is_akomodasi = true
             JOIN kamarruangan_m ON a.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
             JOIN kamartempattidur_m ON a.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             JOIN kelaspelayanan_m ON a.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
          WHERE a.is_deleted = false
          GROUP BY a.pendaftaran_id, a.kamarruangan_id, a.kamartempattidur_id, a.kelaspelayanan_id, a.penjamin_id, kamarruangan_m.kamarruangan_nokamar, ruangan_m.ruangan_nama, kamartempattidur_m.no_tempattidur, kelaspelayanan_m.kelaspelayanan_nama) akomodasi ON pendaftaran_t.pendaftaran_id = akomodasi.pendaftaran_id
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
          GROUP BY a.pendaftaran_id, a.kamarruangan_id, a.kamartempattidur_id) tgl_keluar ON akomodasi.pendaftaran_id = tgl_keluar.pendaftaran_id AND akomodasi.kamarruangan_id = tgl_keluar.kamarruangan_id AND akomodasi.kamartempattidur_id = tgl_keluar.kamartempattidur_id;");
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220118_031451_migrate_infodetailakomodasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220118_031451_migrate_infodetailakomodasi_v cannot be reverted.\n";

        return false;
    }
    */
}
