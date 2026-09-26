<?php

use yii\db\Migration;

/**
 * Class m221014_112023_hotfix_view_infodetailakomodasi_v
 */
class m221014_112023_hotfix_view_infodetailakomodasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infodetailakomodasi_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infodetailakomodasi_v" AS  SELECT \'akomodasi_tagihan\'::text AS tipe,
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
                sum(akomodasi.lama_rawat) AS lama_rawat,
                akomodasi.tarif_satuan AS tarif_kamar,
                akomodasi.pembayaran_id,
                akomodasi.penjamin_pelayanan_id,
                akomodasi.groupcarabayar_id,
                    CASE
                        WHEN akomodasi.tarif_dijamin = 0::double precision AND akomodasi.tarif_dibayarkan = 0::double precision AND akomodasi.groupcarabayar_id <> 417 THEN akomodasi.tarif_satuan
                        ELSE akomodasi.tarif_dijamin
                    END AS tarif_dijamin,
                    CASE
                        WHEN akomodasi.tarif_dijamin = 0::double precision AND akomodasi.tarif_dibayarkan = 0::double precision AND akomodasi.groupcarabayar_id = 417 THEN akomodasi.tarif_satuan
                        ELSE akomodasi.tarif_dibayarkan
                    END AS tarif_dibayarkan
               FROM pendaftaran_t
                 LEFT JOIN ( SELECT a.pendaftaran_id,
                        a.kamarruangan_id,
                        a.kamartempattidur_id,
                        a.kelaspelayanan_id,
                        a.penjamin_id,
                        a.qty_tindakan AS lama_rawat,
                        a.tarif_satuan,
                        concat(kamarruangan_m.kamarruangan_nokamar, \' - \', ruangan_m.ruangan_nama) AS kamar,
                        kamartempattidur_m.no_tempattidur AS tempat_tidur,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        pembayaranpelayanan_t.pembayaran_id,
                        a.penjamin_id AS penjamin_pelayanan_id,
                        carabayar_m.groupcarabayar_id,
                        a.tarif_dibayarkan,
                        a.tarif_dijamin
                       FROM tindakanpelayanan_t a
                         JOIN ( SELECT a1.daftartindakan_id,
                                a1.is_akomodasi
                               FROM daftartindakan_m a1) daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id AND daftartindakan_m.is_akomodasi = true
                         LEFT JOIN ( SELECT a1.kamarruangan_id,
                                a1.ruangan_id,
                                a1.kamarruangan_nokamar
                               FROM kamarruangan_m a1) kamarruangan_m ON a.kamarruangan_id = kamarruangan_m.kamarruangan_id
                         LEFT JOIN ( SELECT a1.ruangan_id,
                                a1.ruangan_nama
                               FROM ruangan_m a1) ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                         LEFT JOIN ( SELECT a1.kamartempattidur_id,
                                a1.no_tempattidur
                               FROM kamartempattidur_m a1) kamartempattidur_m ON a.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
                         LEFT JOIN ( SELECT a1.kelaspelayanan_id,
                                a1.kelaspelayanan_nama
                               FROM kelaspelayanan_m a1) kelaspelayanan_m ON a.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                         JOIN ( SELECT a1.tindakansudahbayar_id,
                                a1.pembayaranpelayanan_id
                               FROM tindakansudahbayar_t a1) tindakansudahbayar_t ON tindakansudahbayar_t.tindakansudahbayar_id = a.tindakansudahbayar_id
                         JOIN ( SELECT a1.pembayaranpelayanan_id,
                                a1.pembayaran_id
                               FROM pembayaranpelayanan_t a1) pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id
                         LEFT JOIN ( SELECT a1.penjamin_id,
                                a1.carabayar_id
                               FROM penjamin_m a1) penjamin_m ON a.penjamin_id = penjamin_m.penjamin_id
                         LEFT JOIN ( SELECT a1.carabayar_id,
                                a1.groupcarabayar_id
                               FROM carabayar_m a1) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                      WHERE a.is_deleted = false) akomodasi ON pendaftaran_t.pendaftaran_id = akomodasi.pendaftaran_id
                 LEFT JOIN ( SELECT a.pendaftaran_id,
                        a.kamarruangan_id,
                        a.kamartempattidur_id,
                        min(a.tgl_tindakan) AS tgl_masuk
                       FROM tindakanpelayanan_t a
                         JOIN ( SELECT a1.daftartindakan_id,
                                a1.is_akomodasi
                               FROM daftartindakan_m a1
                              WHERE a1.is_akomodasi = true) daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id
                      WHERE a.is_deleted = false
                      GROUP BY a.pendaftaran_id, a.kamarruangan_id, a.kamartempattidur_id) tgl_masuk ON akomodasi.pendaftaran_id = tgl_masuk.pendaftaran_id AND akomodasi.kamarruangan_id = tgl_masuk.kamarruangan_id AND akomodasi.kamartempattidur_id = tgl_masuk.kamartempattidur_id
                 LEFT JOIN ( SELECT a.pendaftaran_id,
                        a.kamarruangan_id,
                        a.kamartempattidur_id,
                        max(a.tgl_tindakan) AS tgl_keluar
                       FROM tindakanpelayanan_t a
                         JOIN ( SELECT a1.daftartindakan_id
                               FROM daftartindakan_m a1
                              WHERE a1.is_akomodasi = true) daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id
                      WHERE a.is_deleted = false
                      GROUP BY a.pendaftaran_id, a.kamarruangan_id, a.kamartempattidur_id) tgl_keluar ON akomodasi.pendaftaran_id = tgl_keluar.pendaftaran_id AND akomodasi.kamarruangan_id = tgl_keluar.kamarruangan_id AND akomodasi.kamartempattidur_id = tgl_keluar.kamartempattidur_id
              GROUP BY \'akomodasi_tagihan\'::text, pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.penjamin_id, akomodasi.kamarruangan_id, akomodasi.kelaspelayanan_id, akomodasi.kamar, akomodasi.kelaspelayanan_nama, akomodasi.tempat_tidur, tgl_masuk.tgl_masuk, tgl_keluar.tgl_keluar, akomodasi.tarif_satuan, akomodasi.pembayaran_id, akomodasi.penjamin_pelayanan_id, akomodasi.groupcarabayar_id, akomodasi.tarif_dibayarkan, akomodasi.tarif_dijamin;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221014_112023_hotfix_view_infodetailakomodasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221014_112023_hotfix_view_infodetailakomodasi_v cannot be reverted.\n";

        return false;
    }
    */
}
