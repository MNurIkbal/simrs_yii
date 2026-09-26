<?php

use yii\db\Migration;

/**
 * Class m190806_032401_sync_retursupplier_de
 */
class m190806_032401_sync_retursupplier_de extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      DROP VIEW if exists public.sync_retursupplier_de;
        ');

        $this->execute("
      CREATE OR REPLACE VIEW public.sync_retursupplier_de AS 
 SELECT 'OBAT'::text AS tipe_transaksi,
    returpenerimaanobat_t.returpenerimaanobat_id AS id,
    returpenerimaanobat_t.no_returpenerimaanobat,
    returpenerimaanobat_t.tgl_retur,
    ruangan_m.instalasi_id,
    returpenerimaanobat_t.ruanganretur_id AS ruangan_id,
    ruangan_m.ruangan_nama,
    supplier_m.supplier_id,
    supplier_m.supplier_nama,
    payterm_m.payterm_kode,
    pajak_m.pajak_kode,
    (returpenerimaanobat_t.no_returpenerimaanobat::text || '-'::text) || supplier_m.supplier_nama::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT penerimaansupp_t_1.no_penerimaan,
                    concat(obatalkes_m.obatalkes_nama, ' (1', sat_besar.satuanunit_nama, ' = ', penerimaansuppdetail_t_1.qty_besar / penerimaansuppdetail_t_1.qty_kecil, ' ', sat_besar.satuanunit_nama, ')') AS \"desc\",
                    penerimaansuppdetail_t_1.obatalkes_id,
                    jenisobatalkes_m.jenisobatalkes_kode AS category_code,
                    returpenerimaanobatdetail_t_1.qty_retur,
                    penerimaansuppdetail_t_1.harga_netto AS harga,
                    returpenerimaanobatdetail_t_1.qty_retur::double precision * penerimaansuppdetail_t_1.harga_netto AS jumlah,
                    penerimaansuppdetail_t_1.diskon AS discount,
                    penerimaansuppdetail_t_1.harga_netto * returpenerimaanobatdetail_t_1.qty_retur::double precision * penerimaansuppdetail_t_1.diskon::double precision / 100::double precision AS discount_amount
                   FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1
                     JOIN penerimaansuppdetail_t penerimaansuppdetail_t_1 ON returpenerimaanobatdetail_t_1.penerimaansuppdetail_id = penerimaansuppdetail_t_1.penerimaansuppdetail_id
                     JOIN penerimaansupp_t penerimaansupp_t_1 ON penerimaansuppdetail_t_1.penerimaansupp_id = penerimaansupp_t_1.penerimaansupp_id
                     JOIN obatalkes_m ON returpenerimaanobatdetail_t_1.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     LEFT JOIN satuanunit_m sat_kecil ON penerimaansuppdetail_t_1.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON penerimaansuppdetail_t_1.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE returpenerimaanobatdetail_t_1.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id AND returpenerimaanobatdetail_t_1.is_deleted IS FALSE) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT jenisobatalkes_m.jenisobatalkes_kode,
                    sum(returpenerimaanobatdetail_t_1.qty_retur) AS qty_retur,
                    penerimaansuppdetail_t_1.harga_netto AS harga,
                    sum(penerimaansuppdetail_t_1.harga_netto * returpenerimaanobatdetail_t_1.qty_retur::double precision) AS jumlah,
                    sum(penerimaansuppdetail_t_1.harga_netto * returpenerimaanobatdetail_t_1.qty_retur::double precision * penerimaansuppdetail_t_1.diskon::double precision / 100::double precision) AS discount_amount
                   FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1
                     JOIN penerimaansuppdetail_t penerimaansuppdetail_t_1 ON returpenerimaanobatdetail_t_1.penerimaansuppdetail_id = penerimaansuppdetail_t_1.penerimaansuppdetail_id
                     JOIN penerimaansupp_t penerimaansupp_t_1 ON penerimaansuppdetail_t_1.penerimaansupp_id = penerimaansupp_t_1.penerimaansupp_id
                     JOIN obatalkes_m ON returpenerimaanobatdetail_t_1.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     LEFT JOIN satuanunit_m sat_kecil ON penerimaansuppdetail_t_1.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON penerimaansuppdetail_t_1.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE returpenerimaanobatdetail_t_1.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id AND returpenerimaanobatdetail_t_1.is_deleted IS FALSE
                  GROUP BY jenisobatalkes_m.jenisobatalkes_kode, penerimaansuppdetail_t_1.harga_netto) d2) AS detail_jenisobat
   FROM returpenerimaanobat_t
     JOIN returpenerimaanobatdetail_t ON returpenerimaanobat_t.returpenerimaanobat_id = returpenerimaanobatdetail_t.returpenerimaanobat_id
     JOIN penerimaansuppdetail_t ON returpenerimaanobatdetail_t.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
     LEFT JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
     LEFT JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ruangan_m ON returpenerimaanobat_t.ruanganretur_id = ruangan_m.ruangan_id
     LEFT JOIN payterm_m ON penerimaansupp_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
  WHERE NOT (returpenerimaanobat_t.returpenerimaanobat_id IN ( SELECT COALESCE(syncakuntansi_r.returpenerimaanobat_id, 0) AS returpenerimaanobat_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS FALSE))
  GROUP BY returpenerimaanobat_t.returpenerimaanobat_id, returpenerimaanobat_t.no_returpenerimaanobat, returpenerimaanobat_t.tgl_retur, ruangan_m.instalasi_id, returpenerimaanobat_t.ruanganretur_id, ruangan_m.ruangan_nama, supplier_m.supplier_id, supplier_m.supplier_nama, payterm_m.payterm_kode, pajak_m.pajak_kode
UNION ALL
 SELECT 'OBAT'::text AS tipe_transaksi,
    returpenerimaanobat_t.returpenerimaanobat_id AS id,
    returpenerimaanobat_t.no_returpenerimaanobat,
    returpenerimaanobat_t.tgl_retur,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    supplier_m.supplier_id,
    supplier_m.supplier_nama,
    payterm_m.payterm_kode,
    pajak_m.pajak_kode,
    (returpenerimaanobat_t.no_returpenerimaanobat::text || '-'::text) || supplier_m.supplier_nama::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT penerimaanobat_t_1.no_penerimaan,
                    concat(obatalkes_m.obatalkes_nama, ' (1', sat_besar.satuanunit_nama, ' = ', satuankonversi_m.nilai_konversi, ' ', sat_besar.satuanunit_nama, ')') AS \"desc\",
                    penerimaanobatdetail_t_1.obatalkes_id,
                    jenisobatalkes_m.jenisobatalkes_kode AS category_code,
                    returpenerimaanobatdetail_t_1.qty_retur,
                    penerimaanobatdetail_t_1.harga,
                    returpenerimaanobatdetail_t_1.qty_retur::double precision * penerimaanobatdetail_t_1.harga AS jumlah,
                    penerimaanobatdetail_t_1.discount,
                    penerimaanobatdetail_t_1.harga * returpenerimaanobatdetail_t_1.qty_retur::double precision * penerimaanobatdetail_t_1.discount / 100::double precision AS discount_amount
                   FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1
                     JOIN penerimaanobatdetail_t penerimaanobatdetail_t_1 ON returpenerimaanobatdetail_t_1.penerimaanobatdetail_id = penerimaanobatdetail_t_1.penerimaanobatdetail_id
                     JOIN penerimaanobat_t penerimaanobat_t_1 ON penerimaanobatdetail_t_1.penerimaanobat_id = penerimaanobat_t_1.penerimaanobat_id
                     JOIN obatalkes_m ON returpenerimaanobatdetail_t_1.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     LEFT JOIN satuankonversi_m ON penerimaanobatdetail_t_1.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE returpenerimaanobatdetail_t_1.is_deleted IS FALSE AND returpenerimaanobatdetail_t_1.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT jenisobatalkes_m.jenisobatalkes_kode,
                    sum(returpenerimaanobatdetail_t_1.qty_retur) AS qty_retur,
                    penerimaanobatdetail_t_1.harga,
                    sum(returpenerimaanobatdetail_t_1.qty_retur::double precision * penerimaanobatdetail_t_1.harga) AS jumlah,
                    sum(penerimaanobatdetail_t_1.harga * returpenerimaanobatdetail_t_1.qty_retur::double precision * penerimaanobatdetail_t_1.discount / 100::double precision) AS discount_amount
                   FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1
                     JOIN penerimaanobatdetail_t penerimaanobatdetail_t_1 ON returpenerimaanobatdetail_t_1.penerimaanobatdetail_id = penerimaanobatdetail_t_1.penerimaanobatdetail_id
                     JOIN penerimaanobat_t penerimaanobat_t_1 ON penerimaanobatdetail_t_1.penerimaanobat_id = penerimaanobat_t_1.penerimaanobat_id
                     JOIN obatalkes_m ON returpenerimaanobatdetail_t_1.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     LEFT JOIN satuankonversi_m ON penerimaanobatdetail_t_1.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE returpenerimaanobatdetail_t_1.is_deleted IS FALSE AND returpenerimaanobatdetail_t_1.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id
                  GROUP BY jenisobatalkes_m.jenisobatalkes_kode, penerimaanobatdetail_t_1.harga) d2) AS detail_jenisobat
   FROM returpenerimaanobat_t
     JOIN returpenerimaanobatdetail_t ON returpenerimaanobat_t.returpenerimaanobat_id = returpenerimaanobatdetail_t.returpenerimaanobat_id
     JOIN penerimaanobatdetail_t ON returpenerimaanobatdetail_t.penerimaanobatdetail_id = penerimaanobatdetail_t.penerimaanobatdetail_id
     LEFT JOIN penerimaanobat_t ON penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
     LEFT JOIN validasipoobat_t ON penerimaanobat_t.validasipoobat_id = validasipoobat_t.validasipoobat_id
     LEFT JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ruangan_m ON validasipoobat_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN payterm_m ON validasipoobat_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
  WHERE NOT (returpenerimaanobat_t.returpenerimaanobat_id IN ( SELECT COALESCE(syncakuntansi_r.returpenerimaanobat_id, 0) AS returpenerimaanobat_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS FALSE))
  GROUP BY returpenerimaanobat_t.returpenerimaanobat_id, returpenerimaanobat_t.no_returpenerimaanobat, returpenerimaanobat_t.tgl_retur, ruangan_m.instalasi_id, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, supplier_m.supplier_id, supplier_m.supplier_nama, payterm_m.payterm_kode, pajak_m.pajak_kode
UNION ALL
 SELECT 'BARANG'::text AS tipe_transaksi,
    returpenerimaanbarang_t.returpenerimaanbarang_id AS id,
    returpenerimaanbarang_t.no_returpenerimaanbarang AS no_returpenerimaanobat,
    returpenerimaanbarang_t.tgl_retur,
    ruangan_m.instalasi_id,
    penerimaanbarang_t.ruanganpenerima_id AS ruangan_id,
    ruangan_m.ruangan_nama,
    supplier_m.supplier_id,
    supplier_m.supplier_nama,
    payterm_m.payterm_kode,
    pajak_m.pajak_kode,
    (returpenerimaanbarang_t.no_returpenerimaanbarang::text || '-'::text) || supplier_m.supplier_nama::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT penerimaanbarang_t_1.no_penerimaan,
                    concat(barang_m.barang_nama, ' (1', sat_besar.satuanunit_nama, ' = ', satuankonversibrg_m.nilai_konversi, ' ', sat_besar.satuanunit_nama, ')') AS \"desc\",
                    penerimaanbarangdetail_t_1.barang_id AS obatalkes_id,
                    kelompokbarang_m.kelompokbarang_kode AS category_code,
                    returpenerimaanbarangdetail_t_1.qty_retur,
                    penerimaanbarangdetail_t_1.harga,
                    returpenerimaanbarangdetail_t_1.qty_retur::double precision * penerimaanbarangdetail_t_1.harga AS jumlah,
                    penerimaanbarangdetail_t_1.discount,
                    penerimaanbarangdetail_t_1.harga * returpenerimaanbarangdetail_t_1.qty_retur::double precision * penerimaanbarangdetail_t_1.discount / 100::double precision AS discount_amount
                   FROM returpenerimaanbarangdetail_t returpenerimaanbarangdetail_t_1
                     JOIN penerimaanbarangdetail_t penerimaanbarangdetail_t_1 ON returpenerimaanbarangdetail_t_1.penerimaanbarangdetail_id = penerimaanbarangdetail_t_1.penerimaanbarangdetail_id
                     JOIN penerimaanbarang_t penerimaanbarang_t_1 ON penerimaanbarangdetail_t_1.penerimaanbarang_id = penerimaanbarang_t_1.penerimaanbarang_id
                     JOIN barang_m ON returpenerimaanbarangdetail_t_1.barang_id = barang_m.barang_id
                     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                     LEFT JOIN satuankonversibrg_m ON penerimaanbarangdetail_t_1.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE returpenerimaanbarangdetail_t_1.is_deleted IS FALSE AND returpenerimaanbarangdetail_t_1.returpenerimaanbarang_id = returpenerimaanbarang_t.returpenerimaanbarang_id) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT kelompokbarang_m.kelompokbarang_kode AS jenisobatalkes_kode,
                    sum(returpenerimaanbarangdetail_t_1.qty_retur) AS qty_retur,
                    penerimaanbarangdetail_t_1.harga,
                    sum(returpenerimaanbarangdetail_t_1.qty_retur::double precision * penerimaanbarangdetail_t_1.harga) AS jumlah,
                    sum(penerimaanbarangdetail_t_1.harga * returpenerimaanbarangdetail_t_1.qty_retur::double precision * penerimaanbarangdetail_t_1.discount / 100::double precision) AS discount_amount
                   FROM returpenerimaanbarangdetail_t returpenerimaanbarangdetail_t_1
                     JOIN penerimaanbarangdetail_t penerimaanbarangdetail_t_1 ON returpenerimaanbarangdetail_t_1.penerimaanbarangdetail_id = penerimaanbarangdetail_t_1.penerimaanbarangdetail_id
                     JOIN penerimaanbarang_t penerimaanbarang_t_1 ON penerimaanbarangdetail_t_1.penerimaanbarang_id = penerimaanbarang_t_1.penerimaanbarang_id
                     JOIN barang_m ON returpenerimaanbarangdetail_t_1.barang_id = barang_m.barang_id
                     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                     LEFT JOIN satuankonversibrg_m ON penerimaanbarangdetail_t_1.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE returpenerimaanbarangdetail_t_1.is_deleted IS FALSE AND returpenerimaanbarangdetail_t_1.returpenerimaanbarang_id = returpenerimaanbarang_t.returpenerimaanbarang_id
                  GROUP BY kelompokbarang_m.kelompokbarang_kode, penerimaanbarangdetail_t_1.harga) d2) AS detail_jenisobat
   FROM returpenerimaanbarang_t
     JOIN returpenerimaanbarangdetail_t ON returpenerimaanbarang_t.returpenerimaanbarang_id = returpenerimaanbarangdetail_t.returpenerimaanbarang_id
     JOIN penerimaanbarangdetail_t ON returpenerimaanbarangdetail_t.penerimaanbarangdetail_id = penerimaanbarangdetail_t.penerimaanbarangdetail_id
     LEFT JOIN penerimaanbarang_t ON penerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id
     LEFT JOIN validasipobarang_t ON penerimaanbarang_t.validasipobarang_id = validasipobarang_t.validasipobarang_id
     LEFT JOIN supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ruangan_m ON validasipobarang_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN payterm_m ON validasipobarang_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
  WHERE NOT (returpenerimaanbarang_t.returpenerimaanbarang_id IN ( SELECT COALESCE(syncakuntansi_r.returpenerimaanbarang_id, 0) AS returpenerimaanbarang_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS FALSE))
  GROUP BY returpenerimaanbarang_t.returpenerimaanbarang_id, returpenerimaanbarang_t.no_returpenerimaanbarang, returpenerimaanbarang_t.tgl_retur, ruangan_m.instalasi_id, penerimaanbarang_t.ruanganpenerima_id, ruangan_m.ruangan_nama, supplier_m.supplier_id, supplier_m.supplier_nama, payterm_m.payterm_kode, pajak_m.pajak_kode;
       ");

        $this->execute('
      ALTER TABLE public.sync_retursupplier_de
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190806_032401_sync_retursupplier_de cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190806_032401_sync_retursupplier_de cannot be reverted.\n";

        return false;
    }
    */
}
