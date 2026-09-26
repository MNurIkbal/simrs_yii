<?php

use yii\db\Migration;

/**
 * Class m220921_093938_view_gateway_pembayaranlayanan
 */
class m220921_093938_view_gateway_pembayaranlayanan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_pembayaranpelayanan_v";
        ');

        $this->execute("
CREATE OR REPLACE VIEW \"public\".\"gt_pembayaranpelayanan_v\"
AS SELECT pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    tindakanpelayanan_t.data_transaksi,
    pembayaran_t.data_pembayaran
   FROM ( SELECT a.pasien_id,
            a.no_pendaftaran,
            a.tgl_pendaftaran,
            a.pendaftaran_id,
            COALESCE(pasienadmisi_t.carabayar_id, a.carabayar_id) AS carabayar_id,
            COALESCE(pasienadmisi_t.penjamin_id, a.penjamin_id) AS penjamin_id
           FROM pendaftaran_t a
             LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                    a_1.carabayar_id,
                    a_1.penjamin_id
                   FROM pasienadmisi_t a_1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id) pendaftaran_t
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.no_identitas_pasien
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT pembayaran.pendaftaran_id,
            ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                   FROM ( SELECT x.pendaftaran_id,
                            x.tgl_layanan,
                            x.instalasi_nama,
                            x.ruangan_nama,
                            x.jenis_transaksi,
                            x.tindakan_obat_id,
                            x.tindakan_obat_nama,
                            x.qty,
                            x.harga_satuan,
                            x.harga_cyto,
                            x.sub_total
                           FROM ( SELECT a.pendaftaran_id,
                                    a.tgl_tindakan::text AS tgl_layanan,
                                    instalasi_m.instalasi_nama,
                                    ruangan_m.ruangan_nama,
                                    'Tindakan'::text AS jenis_transaksi,
                                    a.daftartindakan_id AS tindakan_obat_id,
                                    daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
                                    a.qty_tindakan AS qty,
                                    a.tarif_satuan AS harga_satuan,
                                    a.tarifcyto_tindakan AS harga_cyto,
                                    a.tarif_tindakan AS sub_total
                                   FROM tindakanpelayanan_t a
                                     JOIN ( SELECT a_1.daftartindakan_id,
    a_1.daftartindakan_nama
   FROM daftartindakan_m a_1) daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                     LEFT JOIN ( SELECT a_1.instalasi_id,
    a_1.instalasi_nama
   FROM instalasi_m a_1) instalasi_m ON a.instalasi_id = instalasi_m.instalasi_id
                                     LEFT JOIN ( SELECT a_1.ruangan_id,
    a_1.ruangan_nama
   FROM ruangan_m a_1) ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id
                                  WHERE a.is_deleted = false AND a.is_active = true AND a.tindakansudahbayar_id IS NOT NULL AND a.parent_id IS NULL
                                UNION ALL
                                 SELECT a.pendaftaran_id,
                                    a.tgl_tindakan::text AS tgl_layanan,
                                    instalasi_m.instalasi_nama,
                                    ruangan_m.ruangan_nama,
                                    'Paket'::text AS jenis_transaksi,
                                    a.tipepaket_id AS tindakan_obat_id,
                                    tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
                                    a.qty_tindakan AS qty,
                                    a.tarif_satuan AS harga_satuan,
                                    a.tarifcyto_tindakan AS harga_cyto,
                                    a.tarif_tindakan AS sub_total
                                   FROM tindakanpelayanan_t a
                                     JOIN ( SELECT a_1.tipepaket_id,
    a_1.tipepaket_nama
   FROM tipepaket_m a_1) tipepaket_m ON a.tipepaket_id = tipepaket_m.tipepaket_id
                                     LEFT JOIN ( SELECT a_1.instalasi_id,
    a_1.instalasi_nama
   FROM instalasi_m a_1) instalasi_m ON a.instalasi_id = instalasi_m.instalasi_id
                                     LEFT JOIN ( SELECT a_1.ruangan_id,
    a_1.ruangan_nama
   FROM ruangan_m a_1) ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id
                                  WHERE a.is_deleted = false AND a.is_active = true AND a.tindakansudahbayar_id IS NOT NULL AND a.parent_id IS NULL
                                UNION ALL
                                 SELECT a.pendaftaran_id,
                                    a.tglpelayanan::text AS tgl_layanan,
                                    instalasi_m.instalasi_nama,
                                    ruangan_m.ruangan_nama,
                                    'Obat'::text AS jenis_transaksi,
                                    a.obatalkes_id AS tindakan_obat_id,
                                    obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
                                    a.qty_oa AS qty,
                                    a.hargasatuan_oa AS harga_satuan,
                                    COALESCE(a.tarifcyto, 0::double precision) AS harga_cyto,
                                    a.hargajual_oa AS sub_total
                                   FROM obatalkespasien_t a
                                     JOIN ( SELECT a_1.obatalkes_id,
    a_1.obatalkes_nama
   FROM obatalkes_m a_1) obatalkes_m ON a.obatalkes_id = obatalkes_m.obatalkes_id
                                     LEFT JOIN ( SELECT a_1.instalasi_id,
    a_1.ruangan_id,
    a_1.ruangan_nama
   FROM ruangan_m a_1) ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id
                                     LEFT JOIN ( SELECT a_1.instalasi_id,
    a_1.instalasi_nama
   FROM instalasi_m a_1) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                                  WHERE a.is_deleted = false AND a.is_active = true AND a.obatsudahbayar_id IS NOT NULL AND a.hargajual_oa > 0::double precision) x
                          WHERE pembayaran.pendaftaran_id = x.pendaftaran_id) d) AS data_transaksi
           FROM pembayaran_t pembayaran
          WHERE pembayaran.is_deleted IS FALSE
          GROUP BY pembayaran.pendaftaran_id) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN ( SELECT pembayaran.pendaftaran_id,
            ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                   FROM ( SELECT x.pendaftaran_id,
                            x.total_tagihan,
                            x.total_penjamin,
                            x.total_nontunai,
                            x.total_tunai,
                                CASE
                                    WHEN x.total_nontunai > 0::double precision AND x.total_tunai > 0::double precision THEN 'TUNAI/NON TUNAI'::text
                                    WHEN x.total_tunai > 0::double precision THEN 'TUNAI'::text
                                    WHEN x.total_nontunai > 0::double precision THEN 'NON TUNAI'::text
                                    ELSE 'NON TUNAI'::text
                                END AS jenis_pembayaran
                           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                                    COALESCE(pembayaran_t_1.total_tagihan, 0::double precision) AS total_tagihan,
CASE
 WHEN pendaftaran_t_1.carabayar_id = 5 THEN COALESCE(pembayaran_t_1.total_dijamin, 0::double precision) + COALESCE(pembayaran_t_1.total_pembulatan, 0::double precision) + COALESCE(pembayaran_t_1.total_piutang, 0::double precision)
 WHEN pendaftaran_t_1.carabayar_id = 2 THEN COALESCE(pembayaran_t_1.total_penjamin, 0::double precision) - COALESCE(pembayaran_t_1.total_discount, 0::double precision)
 ELSE COALESCE(pembayaran_t_1.total_penjamin, 0::double precision)
END AS total_penjamin,
                                    COALESCE(pembayaran_t_1.total_nontunai, 0::double precision) AS total_nontunai,
CASE
 WHEN pendaftaran_t_1.carabayar_id = 2 THEN COALESCE(pembayaran_t_1.total_tunai, 0::double precision) + COALESCE(pembayaran_t_1.total_kembalian, 0::double precision)
 ELSE COALESCE(pembayaran_t_1.total_tunai, 0::double precision)
END AS total_tunai
                                   FROM ( SELECT a.pendaftaran_id,
    a.pembayaran_id,
    a.total_nontunai,
    a.total_ditagihkan,
    a.total_dijamin,
    a.total_pembulatan,
    pemberianpiutang_t.total_piutang,
    a.total_discount,
    a.total_kembalian,
    COALESCE(a.total_dijamin, 0::double precision) + a.total_pembulatan + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
    a.total_tunai - a.total_kembalian AS total_tunai,
    a.total_tagihan + a.total_administrasi + a.total_pembulatan + a.pembulatan - (a.total_discount + a.total_discountpembayaran) AS total_tagihan
   FROM pembayaran_t a
     LEFT JOIN ( SELECT a1.pemberianpiutang_id,
      a1.total_piutang
     FROM pemberianpiutang_t a1) pemberianpiutang_t ON a.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
  WHERE a.is_deleted IS FALSE) pembayaran_t_1
                                     JOIN ( SELECT a.pasien_id,
    a.no_pendaftaran,
    a.tgl_pendaftaran,
    a.pendaftaran_id,
    COALESCE(pasienadmisi_t.carabayar_id, a.carabayar_id) AS carabayar_id,
    COALESCE(pasienadmisi_t.penjamin_id, a.penjamin_id) AS penjamin_id
   FROM pendaftaran_t a
     LEFT JOIN ( SELECT a_1.pasienadmisi_id,
      a_1.carabayar_id,
      a_1.penjamin_id
     FROM pasienadmisi_t a_1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id) pendaftaran_t_1 ON pembayaran_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id) x
                          WHERE pembayaran.pendaftaran_id = x.pendaftaran_id) d) AS data_pembayaran
           FROM pembayaran_t pembayaran
          WHERE pembayaran.is_deleted IS FALSE
          GROUP BY pembayaran.pendaftaran_id) pembayaran_t ON pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_093938_view_gateway_pembayaranlayanan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_093938_view_gateway_pembayaranlayanan cannot be reverted.\n";

        return false;
    }
    */
}
