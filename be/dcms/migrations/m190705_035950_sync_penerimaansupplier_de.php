<?php

use yii\db\Migration;

/**
 * Class m190705_035950_sync_penerimaansupplier_de
 */
class m190705_035950_sync_penerimaansupplier_de extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           DROP VIEW IF exists public.sync_penerimaansupplier_de;
        ');

        $this->execute('
          CREATE OR REPLACE VIEW public.sync_penerimaansupplier_de AS 
 SELECT \'OBAT\'::text AS tipe_transaksi,
    penerimaanobat_t.penerimaanobat_id AS id,
    penerimaanobat_t.no_penerimaan,
    validasipoobat_t.tgl_validasi AS tgl_penerimaan,
    ruangan_m.instalasi_id,
    validasipoobat_t.ruangan_id,
    ruangan_m.ruangan_nama,
    validasipoobat_t.supplier_id,
    supplier_m.supplier_nama,
    payterm_m.payterm_kode,
    pajak_m.pajak_kode,
    (penerimaanobat_t.no_penerimaan::text || \'-\'::text) || supplier_m.supplier_nama::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT concat(obatalkes_m.obatalkes_nama, \' (1\', sat_besar.satuanunit_nama, \' = \', satuankonversi_m.nilai_konversi, \' \', sat_besar.satuanunit_nama, \')\') AS "desc",
                    penerimaanobatdetail_t.qty_diterima,
                    penerimaanobatdetail_t.harga,
                    penerimaanobatdetail_t.jumlah,
                    penerimaanobatdetail_t.discount,
                    penerimaanobatdetail_t.discount_rp AS discount_amount
                   FROM penerimaanobatdetail_t
                     JOIN obatalkes_m ON penerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                     LEFT JOIN satuankonversi_m ON penerimaanobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT jenisobatalkes_m.jenisobatalkes_kode,
                    sum(COALESCE(penerimaanobatdetail_t.qty_diterima, 0)) AS qty_diterima,
                    sum(COALESCE(penerimaanobatdetail_t.harga)) AS harga,
                    sum(COALESCE(penerimaanobatdetail_t.jumlah)) AS jumlah,
                    sum(COALESCE(penerimaanobatdetail_t.discount_rp)) AS discount_amount
                   FROM penerimaanobatdetail_t
                     JOIN obatalkes_m ON penerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     LEFT JOIN satuankonversi_m ON penerimaanobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
                  GROUP BY jenisobatalkes_m.jenisobatalkes_kode) d2) AS detail_jenisobat
   FROM validasipoobat_t
     JOIN penerimaanobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     JOIN ruangan_m ON validasipoobat_t.ruangan_id = ruangan_m.ruangan_id
     JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN payterm_m ON validasipoobat_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
  WHERE NOT (penerimaanobat_t.penerimaanobat_id IN ( SELECT COALESCE(syncakuntansi_r.penerimaanobat_id, 0) AS penerimaanobat_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS FALSE))
UNION ALL
 SELECT \'BARANG\'::text AS tipe_transaksi,
    penerimaanbarang_t.penerimaanbarang_id AS id,
    penerimaanbarang_t.no_penerimaan,
    validasipobarang_t.tgl_validasi AS tgl_penerimaan,
    ruangan_m.instalasi_id,
    validasipobarang_t.ruangan_id,
    ruangan_m.ruangan_nama,
    validasipobarang_t.supplier_id,
    supplier_m.supplier_nama,
    payterm_m.payterm_kode,
    pajak_m.pajak_kode,
    (penerimaanbarang_t.no_penerimaan::text || \'-\'::text) || supplier_m.supplier_nama::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT concat(barang_m.barang_nama, \' (1\', sat_besar.satuanunit_nama, \' = \', satuankonversibrg_m.nilai_konversi, \' \', sat_besar.satuanunit_nama, \')\') AS "desc",
                    penerimaanbarangdetail_t.qty_diterima,
                    penerimaanbarangdetail_t.harga,
                    penerimaanbarangdetail_t.jumlah,
                    penerimaanbarangdetail_t.discount,
                    penerimaanbarangdetail_t.discount_rp AS discount_amount
                   FROM penerimaanbarangdetail_t
                     JOIN barang_m ON penerimaanbarangdetail_t.barang_id = barang_m.barang_id
                     LEFT JOIN satuankonversibrg_m ON penerimaanbarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE penerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT kelompokbarang_m.kelompokbarang_kode AS jenisobatalkes_kode,
                    sum(COALESCE(penerimaanbarangdetail_t.qty_diterima, 0)) AS qty_diterima,
                    sum(COALESCE(penerimaanbarangdetail_t.harga)) AS harga,
                    sum(COALESCE(penerimaanbarangdetail_t.jumlah)) AS jumlah,
                    sum(COALESCE(penerimaanbarangdetail_t.discount_rp)) AS discount_amount
                   FROM penerimaanbarangdetail_t
                     JOIN barang_m ON penerimaanbarangdetail_t.barang_id = barang_m.barang_id
                     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                     LEFT JOIN satuankonversibrg_m ON penerimaanbarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE penerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id
                  GROUP BY kelompokbarang_m.kelompokbarang_kode) d2) AS detail_jenisobat
   FROM validasipobarang_t
     JOIN penerimaanbarang_t ON validasipobarang_t.validasipobarang_id = penerimaanbarang_t.validasipobarang_id
     JOIN ruangan_m ON validasipobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN payterm_m ON validasipobarang_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
  WHERE NOT (penerimaanbarang_t.penerimaanbarang_id IN ( SELECT COALESCE(syncakuntansi_r.penerimaanbarang_id, 0) AS penerimaanbarang_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS FALSE));
        ');

        $this->execute('
           ALTER TABLE public.sync_penerimaansupplier_de
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190705_035950_sync_penerimaansupplier_de cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190705_035950_sync_penerimaansupplier_de cannot be reverted.\n";

        return false;
    }
    */
}
