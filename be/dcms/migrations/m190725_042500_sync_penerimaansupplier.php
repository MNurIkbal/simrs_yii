<?php

use yii\db\Migration;

/**
 * Class m190725_042500_sync_penerimaansupplier
 */
class m190725_042500_sync_penerimaansupplier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           DROP VIEW IF exists public.sync_penerimaansupplier;
        ');

        $this->execute("
          CREATE OR REPLACE VIEW public.sync_penerimaansupplier AS 
 SELECT 'OBAT'::text AS tipe_transaksi,
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
    (penerimaanobat_t.no_penerimaan::text || '-'::text) || supplier_m.supplier_nama::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT concat(obatalkes_m.obatalkes_nama, ' (1', sat_besar.satuanunit_nama, ' = ', satuankonversi_m.nilai_konversi, ' ', sat_besar.satuanunit_nama, ')') AS \"desc\",
                    penerimaanobatdetail_t.obatalkes_id,
                    jenisobatalkes_m.jenisobatalkes_kode AS category_code,
                    penerimaanobatdetail_t.qty_diterima,
                    penerimaanobatdetail_t.harga,
                    penerimaanobatdetail_t.jumlah,
                    penerimaanobatdetail_t.discount,
                    penerimaanobatdetail_t.discount_rp AS discount_amount
                   FROM penerimaanobatdetail_t
                     JOIN obatalkes_m ON penerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     LEFT JOIN satuankonversi_m ON penerimaanobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id AND penerimaanobatdetail_t.is_deleted IS FALSE) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT jenisobatalkes_m.jenisobatalkes_kode,
                    sum(COALESCE(penerimaanobatdetail_t.qty_diterima, 0)) AS qty_diterima,
                    sum(COALESCE(penerimaanobatdetail_t.harga)) AS harga,
                    sum(COALESCE(penerimaanobatdetail_t.jumlah)) + sum(COALESCE(penerimaanobatdetail_t.discount_rp)) AS jumlah,
                    sum(COALESCE(penerimaanobatdetail_t.discount_rp)) AS discount_amount
                   FROM penerimaanobatdetail_t
                     JOIN obatalkes_m ON penerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     LEFT JOIN satuankonversi_m ON penerimaanobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id AND penerimaanobatdetail_t.is_deleted IS FALSE
                  GROUP BY jenisobatalkes_m.jenisobatalkes_kode) d2) AS detail_jenisobat
   FROM validasipoobat_t
     JOIN penerimaanobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     JOIN ruangan_m ON validasipoobat_t.ruangan_id = ruangan_m.ruangan_id
     JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN payterm_m ON validasipoobat_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
  WHERE NOT (penerimaanobat_t.penerimaanobat_id IN ( SELECT COALESCE(syncakuntansi_r.penerimaanobat_id, 0) AS penerimaanobat_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'BARANG'::text AS tipe_transaksi,
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
    (penerimaanbarang_t.no_penerimaan::text || '-'::text) || supplier_m.supplier_nama::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT concat(barang_m.barang_nama, ' (1', sat_besar.satuanunit_nama, ' = ', satuankonversibrg_m.nilai_konversi, ' ', sat_besar.satuanunit_nama, ')') AS \"desc\",
                    penerimaanbarangdetail_t.barang_id AS obatalkes_id,
                    kelompokbarang_m.kelompokbarang_kode AS category_code,
                    penerimaanbarangdetail_t.qty_diterima,
                    penerimaanbarangdetail_t.harga,
                    penerimaanbarangdetail_t.jumlah,
                    penerimaanbarangdetail_t.discount,
                    penerimaanbarangdetail_t.discount_rp AS discount_amount
                   FROM penerimaanbarangdetail_t
                     JOIN barang_m ON penerimaanbarangdetail_t.barang_id = barang_m.barang_id
                     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                     LEFT JOIN satuankonversibrg_m ON penerimaanbarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE penerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id AND penerimaanbarangdetail_t.is_deleted IS FALSE) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT kelompokbarang_m.kelompokbarang_kode AS jenisobatalkes_kode,
                    sum(COALESCE(penerimaanbarangdetail_t.qty_diterima, 0)) AS qty_diterima,
                    sum(COALESCE(penerimaanbarangdetail_t.harga)) AS harga,
                    sum(COALESCE(penerimaanbarangdetail_t.jumlah)) + sum(COALESCE(penerimaanbarangdetail_t.discount_rp)) AS jumlah,
                    sum(COALESCE(penerimaanbarangdetail_t.discount_rp)) AS discount_amount
                   FROM penerimaanbarangdetail_t
                     JOIN barang_m ON penerimaanbarangdetail_t.barang_id = barang_m.barang_id
                     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                     LEFT JOIN satuankonversibrg_m ON penerimaanbarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                     LEFT JOIN satuanunit_m sat_kecil ON satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE penerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id AND penerimaanbarangdetail_t.is_deleted IS FALSE
                  GROUP BY kelompokbarang_m.kelompokbarang_kode) d2) AS detail_jenisobat
   FROM validasipobarang_t
     JOIN penerimaanbarang_t ON validasipobarang_t.validasipobarang_id = penerimaanbarang_t.validasipobarang_id
     JOIN ruangan_m ON validasipobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN payterm_m ON validasipobarang_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
  WHERE NOT (penerimaanbarang_t.penerimaanbarang_id IN ( SELECT COALESCE(syncakuntansi_r.penerimaanbarang_id, 0) AS penerimaanbarang_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'OBAT'::text AS tipe_transaksi,
    penerimaansupp_t.penerimaansupp_id AS id,
    penerimaansupp_t.no_penerimaan,
    penerimaansupp_t.tgl_penerimaan,
    ruangan_m.instalasi_id,
    penerimaansupp_t.ruanganpenerima_id AS ruangan_id,
    ruangan_m.ruangan_nama,
    penerimaansupp_t.supplier_id,
    supplier_m.supplier_nama,
    payterm_m.payterm_kode,
    pajak_m.pajak_kode,
    (penerimaansupp_t.no_penerimaan::text || '-'::text) || supplier_m.supplier_nama::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT concat(obatalkes_m.obatalkes_nama, ' (1', sat_besar.satuanunit_nama, ' = ', penerimaansuppdetail_t.qty_besar / penerimaansuppdetail_t.qty_kecil, ' ', sat_besar.satuanunit_nama, ')') AS \"desc\",
                    penerimaansuppdetail_t.obatalkes_id,
                    jenisobatalkes_m.jenisobatalkes_kode AS category_code,
                    penerimaansuppdetail_t.qty_besar AS qty_diterima,
                    penerimaansuppdetail_t.harga_netto AS harga,
                    penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.harga_netto AS jumlah,
                    penerimaansuppdetail_t.diskon AS discount,
                    penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.harga_netto * penerimaansuppdetail_t.diskon::double precision / 100::double precision AS discount_amount
                   FROM penerimaansuppdetail_t
                     JOIN obatalkes_m ON penerimaansuppdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     LEFT JOIN satuanunit_m sat_kecil ON penerimaansuppdetail_t.satuankecil_id = sat_kecil.satuanunit_id
                     LEFT JOIN satuanunit_m sat_besar ON penerimaansuppdetail_t.satuanbesar_id = sat_besar.satuanunit_id
                  WHERE penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id AND penerimaansuppdetail_t.is_deleted IS FALSE) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT x.jenisobatalkes_kode,
                    sum(x.qty_diterima) AS qty_diterima,
                    sum(x.harga) AS harga,
                    sum(x.jumlah) + sum(x.discount_amount) AS jumlah,
                    sum(x.discount_amount) AS discount_amount
                   FROM ( SELECT penerimaansuppdetail_t.penerimaansupp_id,
                            jenisobatalkes_m.jenisobatalkes_kode,
                            COALESCE(penerimaansuppdetail_t.qty_besar, 0) AS qty_diterima,
                            COALESCE(penerimaansuppdetail_t.harga_netto) AS harga,
                            COALESCE(penerimaansuppdetail_t.qty_besar, 0)::double precision * COALESCE(penerimaansuppdetail_t.harga_netto) AS jumlah,
                            COALESCE(penerimaansuppdetail_t.qty_besar, 0)::double precision * COALESCE(penerimaansuppdetail_t.harga_netto) * penerimaansuppdetail_t.diskon::double precision / 100::double precision AS discount_amount
                           FROM penerimaansuppdetail_t
                             JOIN obatalkes_m ON penerimaansuppdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                             JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                          WHERE penerimaansuppdetail_t.is_deleted IS FALSE) x
                  WHERE x.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
                  GROUP BY x.penerimaansupp_id, x.jenisobatalkes_kode) d2) AS detail_jenisobat
   FROM penerimaansupp_t
     JOIN ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
     JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN payterm_m ON penerimaansupp_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
  WHERE NOT (penerimaansupp_t.penerimaansupp_id IN ( SELECT COALESCE(syncakuntansi_r.penerimaansupp_id, 0) AS penerimaansupp_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'ADJM.OBAT'::text AS tipe_transaksi,
    adjusmenobat_t.adjusmenobat_id AS id,
    adjusmenobat_t.no_adjusmen AS no_penerimaan,
    adjusmenobat_t.tgl_adjusmen AS tgl_penerimaan,
    ruangan_m.instalasi_id,
    adjusmenobat_t.ruangan_adjusmen_id AS ruangan_id,
    ruangan_m.ruangan_nama,
    NULL::integer AS supplier_id,
    NULL::character varying AS supplier_nama,
    NULL::character varying AS payterm_kode,
    NULL::character varying AS pajak_kode,
    (adjusmenobat_t.no_adjusmen::text || '-'::text) || 'Adjusmen Obat Masuk'::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT jenisobatalkes_m.jenisobatalkes_kode,
                    sum(COALESCE(adjusmenobatmasuk_t.qty, 0)) AS qty_diterima,
                    sum(COALESCE(adjusmenobatmasuk_t.harga_netto)) / sum(COALESCE(adjusmenobatmasuk_t.qty, 0))::double precision AS harga,
                    sum(COALESCE(adjusmenobatmasuk_t.harga_netto)) AS jumlah,
                    0 AS discount_amount
                   FROM adjusmenobatmasuk_t
                     JOIN obatalkes_m ON adjusmenobatmasuk_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                  WHERE adjusmenobatmasuk_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id AND adjusmenobatmasuk_t.is_deleted IS FALSE
                  GROUP BY jenisobatalkes_m.jenisobatalkes_kode) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT jenisobatalkes_m.jenisobatalkes_kode,
                    sum(COALESCE(adjusmenobatmasuk_t.qty, 0)) AS qty_diterima,
                    sum(COALESCE(adjusmenobatmasuk_t.harga_netto)) / sum(COALESCE(adjusmenobatmasuk_t.qty, 0))::double precision AS harga,
                    sum(COALESCE(adjusmenobatmasuk_t.harga_netto)) AS jumlah,
                    0 AS discount_amount
                   FROM adjusmenobatmasuk_t
                     JOIN obatalkes_m ON adjusmenobatmasuk_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                  WHERE adjusmenobatmasuk_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id AND adjusmenobatmasuk_t.is_deleted IS FALSE
                  GROUP BY jenisobatalkes_m.jenisobatalkes_kode) d2) AS detail_jenisobat
   FROM adjusmenobat_t
     JOIN ruangan_m ON adjusmenobat_t.ruangan_adjusmen_id = ruangan_m.ruangan_id
  WHERE NOT (adjusmenobat_t.adjusmenobat_id IN ( SELECT COALESCE(syncakuntansi_r.adjusmenobat_id, 0) AS adjusmenobat_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'ADJM.BARANG'::text AS tipe_transaksi,
    adjusmenbarang_t.adjusmenbarang_id AS id,
    adjusmenbarang_t.no_adjusmen AS no_penerimaan,
    adjusmenbarang_t.tgl_adjusmen AS tgl_penerimaan,
    ruangan_m.instalasi_id,
    adjusmenbarang_t.ruangan_adjusmen_id AS ruangan_id,
    ruangan_m.ruangan_nama,
    NULL::integer AS supplier_id,
    NULL::character varying AS supplier_nama,
    NULL::character varying AS payterm_kode,
    NULL::character varying AS pajak_kode,
    (adjusmenbarang_t.no_adjusmen::text || '-'::text) || 'Adjusmen barang masuk'::text AS keterangan,
    NULL::json AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT kelompokbarang_m.kelompokbarang_kode AS jenisobatalkes_kode,
                    sum(COALESCE(adjusmenbarangmasuk_t.qty, 0)) AS qty_diterima,
                    sum(COALESCE(adjusmenbarangmasuk_t.harga_netto)) / sum(COALESCE(adjusmenbarangmasuk_t.qty, 0))::double precision AS harga,
                    sum(COALESCE(adjusmenbarangmasuk_t.harga_netto)) AS jumlah,
                    0 AS discount_amount
                   FROM adjusmenbarangmasuk_t
                     JOIN barang_m ON adjusmenbarangmasuk_t.barang_id = barang_m.barang_id
                     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                  WHERE adjusmenbarangmasuk_t.adjusmenbarang_id = adjusmenbarang_t.adjusmenbarang_id AND adjusmenbarangmasuk_t.is_deleted IS FALSE
                  GROUP BY kelompokbarang_m.kelompokbarang_id) d2) AS detail_jenisobat
   FROM adjusmenbarang_t
     JOIN ruangan_m ON adjusmenbarang_t.ruangan_adjusmen_id = ruangan_m.ruangan_id
  WHERE NOT (adjusmenbarang_t.adjusmenbarang_id IN ( SELECT COALESCE(syncakuntansi_r.adjusmenbarang_id, 0) AS adjusmenbarang_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'ADJK.OBAT'::text AS tipe_transaksi,
    adjusmenobat_t.adjusmenobat_id AS id,
    adjusmenobat_t.no_adjusmen AS no_penerimaan,
    adjusmenobat_t.tgl_adjusmen AS tgl_penerimaan,
    ruangan_m.instalasi_id,
    adjusmenobat_t.ruangan_adjusmen_id AS ruangan_id,
    ruangan_m.ruangan_nama,
    NULL::integer AS supplier_id,
    NULL::character varying AS supplier_nama,
    NULL::character varying AS payterm_kode,
    NULL::character varying AS pajak_kode,
    (adjusmenobat_t.no_adjusmen::text || '-'::text) || 'Adjusmen Obat Keluar'::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT jenisobatalkes_m.jenisobatalkes_kode,
                    sum(COALESCE(adjusmenobatkeluar_t.qty, 0)) AS qty_diterima,
                    sum(COALESCE(stokobatalkes_t.harganetto)) / sum(COALESCE(adjusmenobatkeluar_t.qty, 0))::double precision AS harga,
                    sum(COALESCE(stokobatalkes_t.harganetto)) AS jumlah,
                    0 AS discount_amount
                   FROM adjusmenobatkeluar_t
                     JOIN obatalkes_m ON adjusmenobatkeluar_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     JOIN stokobatalkes_t ON adjusmenobatkeluar_t.adjusmenobatkeluar_id = stokobatalkes_t.adjusmenobatkeluar_id
                  WHERE adjusmenobatkeluar_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id AND adjusmenobatkeluar_t.is_deleted IS FALSE
                  GROUP BY jenisobatalkes_m.jenisobatalkes_kode) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT jenisobatalkes_m.jenisobatalkes_kode,
                    sum(COALESCE(adjusmenobatkeluar_t.qty, 0)) AS qty_diterima,
                    sum(COALESCE(stokobatalkes_t.harganetto)) / sum(COALESCE(adjusmenobatkeluar_t.qty, 0))::double precision AS harga,
                    sum(COALESCE(stokobatalkes_t.harganetto)) AS jumlah,
                    0 AS discount_amount
                   FROM adjusmenobatkeluar_t
                     JOIN obatalkes_m ON adjusmenobatkeluar_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     JOIN stokobatalkes_t ON adjusmenobatkeluar_t.adjusmenobatkeluar_id = stokobatalkes_t.adjusmenobatkeluar_id
                  WHERE adjusmenobatkeluar_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id AND adjusmenobatkeluar_t.is_deleted IS FALSE
                  GROUP BY jenisobatalkes_m.jenisobatalkes_kode) d2) AS detail_jenisobat
   FROM adjusmenobat_t
     JOIN ruangan_m ON adjusmenobat_t.ruangan_adjusmen_id = ruangan_m.ruangan_id
  WHERE NOT (adjusmenobat_t.adjusmenobat_id IN ( SELECT COALESCE(syncakuntansi_r.adjusmenobatkeluar_id, 0) AS adjusmenobatkeluar_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'ADJK.BARANG'::text AS tipe_transaksi,
    adjusmenbarang_t.adjusmenbarang_id AS id,
    adjusmenbarang_t.no_adjusmen AS no_penerimaan,
    adjusmenbarang_t.tgl_adjusmen AS tgl_penerimaan,
    ruangan_m.instalasi_id,
    adjusmenbarang_t.ruangan_adjusmen_id AS ruangan_id,
    ruangan_m.ruangan_nama,
    NULL::integer AS supplier_id,
    NULL::character varying AS supplier_nama,
    NULL::character varying AS payterm_kode,
    NULL::character varying AS pajak_kode,
    (adjusmenbarang_t.no_adjusmen::text || '-'::text) || 'Adjusmen barang Keluar'::text AS keterangan,
    NULL::json AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT kelompokbarang_m.kelompokbarang_kode AS jenisobatalkes_kode,
                    sum(COALESCE(adjusmenbarangkeluar_t.qty, 0)) AS qty_diterima,
                    sum(COALESCE(stokbarang_t.harganetto)) / sum(COALESCE(adjusmenbarangkeluar_t.qty, 0))::double precision AS harga,
                    sum(COALESCE(stokbarang_t.harganetto)) AS jumlah,
                    0 AS discount_amount
                   FROM adjusmenbarangkeluar_t
                     JOIN barang_m ON adjusmenbarangkeluar_t.barang_id = barang_m.barang_id
                     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                     JOIN stokbarang_t ON adjusmenbarangkeluar_t.adjusmenbarang_id = stokbarang_t.adjusmenbarangkeluar_id
                  WHERE adjusmenbarangkeluar_t.adjusmenbarang_id = adjusmenbarang_t.adjusmenbarang_id AND adjusmenbarangkeluar_t.is_deleted IS FALSE
                  GROUP BY kelompokbarang_m.kelompokbarang_id) d2) AS detail_jenisobat
   FROM adjusmenbarang_t
     JOIN ruangan_m ON adjusmenbarang_t.ruangan_adjusmen_id = ruangan_m.ruangan_id
  WHERE NOT (adjusmenbarang_t.adjusmenbarang_id IN ( SELECT COALESCE(syncakuntansi_r.adjusmenbarangkeluar_id, 0) AS adjusmenbarangkeluar_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE));
        ");

        $this->execute('
         ALTER TABLE public.sync_penerimaansupplier
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_042500_sync_penerimaansupplier cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_042500_sync_penerimaansupplier cannot be reverted.\n";

        return false;
    }
    */
}
