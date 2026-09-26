<?php

use yii\db\Migration;

/**
 * Class m191007_064347_retur_penerimaan_barang
 */
class m191007_064347_retur_penerimaan_barang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/* truncate transaksi retur */
        $this->execute('TRUNCATE TABLE returpenerimaanbarang_t RESTART IDENTITY;');
        $this->execute('TRUNCATE TABLE returpenerimaanbarangdetail_t RESTART IDENTITY;');
        $this->execute('DELETE FROM stokbarang_t WHERE returbarangdetail_id is not null;');

 /*drop view terkait*/
        $this->execute('DROP VIEW if exists public.infokartustokbarang_salah_v;');

        $this->execute('DROP VIEW if exists public.inforeturpenerimaanbarangdetail_v;');

        $this->execute('DROP VIEW if exists public.infopenerimaanbarangdetail_v;');

        $this->execute('DROP VIEW if exists public.infokartustokbarang_v;');

        $this->execute('DROP VIEW if exists public.sync_retursupplier;');

        $this->execute('DROP VIEW if exists public.sync_retursupplier_de;');

 /*drop kolom returpenerimaanbarangdetail_t*/
        $this->execute('ALTER TABLE "public"."returpenerimaanbarangdetail_t" 
                      DROP COLUMN "returpenerimaanbarang_id",
                      DROP COLUMN "penerimaanbarangdetail_id",
                      DROP COLUMN "barang_id",
                      DROP COLUMN "satuanbesar_id",
                      DROP COLUMN "additional_data",
                      DROP COLUMN "created_date",
                      DROP COLUMN "created_by",
                      DROP COLUMN "modified_count",
                      DROP COLUMN "last_modified_date",
                      DROP COLUMN "last_modified_by",
                      DROP COLUMN "is_deleted",
                      DROP COLUMN "is_active",
                      DROP COLUMN "deleted_date",
                      DROP COLUMN "deleted_by",
                      DROP COLUMN "penerimaanbarang_id",
                      DROP COLUMN "tgl_kadaluarsa",
                      DROP COLUMN "qty_retur",
                      DROP COLUMN "qty_input";');

        $this->execute('ALTER TABLE "public"."returpenerimaanbarangdetail_t" 
                      ADD COLUMN "returpenerimaanbarang_id" int4 NOT NULL,
                      ADD COLUMN "penerimaanbarang_id" int4,
                      ADD COLUMN "penerimaanbarangdetail_id" int4,
                      ADD COLUMN "penerimaansuppbrgdetail_id" int4,
                      ADD COLUMN "barang_id" int4 NOT NULL,
                      ADD COLUMN "satuanbesar_id" int4,
                      ADD COLUMN "tgl_kadaluarsa" timestamp(0),
                      ADD COLUMN "qty_retur" int4,
                      ADD COLUMN "qty_input" int4,
                      ADD COLUMN "additional_data" text COLLATE "pg_catalog"."default",
                      ADD COLUMN "created_date" timestamp(6) NOT NULL DEFAULT now(),
                      ADD COLUMN "created_by" int4,
                      ADD COLUMN "modified_count" int4,
                      ADD COLUMN "last_modified_date" timestamp(6),
                      ADD COLUMN "last_modified_by" int4,
                      ADD COLUMN "is_deleted" bool NOT NULL DEFAULT false,
                      ADD COLUMN "is_active" bool NOT NULL DEFAULT true,
                      ADD COLUMN "deleted_date" timestamp(6),
                      ADD COLUMN "deleted_by" int4;');

 /*create view*/
        $this->execute("
            CREATE OR REPLACE VIEW public.inforeturpenerimaanbarangdetail_v AS 
 SELECT returpenerimaanbarang_t.returpenerimaanbarang_id,
    returpenerimaanbarang_t.tgl_retur,
    returpenerimaanbarang_t.no_returpenerimaanbarang,
    penerimaanbarang_t.no_penerimaan,
    penerimaanbarang_t.no_faktur,
    supplier_m.supplier_nama,
    barang_m.barang_nama,
    satuanunit_m.satuanunit_nama,
    COALESCE(returpenerimaanbarangdetail_t.qty_retur) AS qty_retur,
    COALESCE(penerimaanbarangdetail_t.qty_diterima) AS qty_diterima,
    COALESCE(returdetailjumlah.on_retur, 0::bigint) AS on_retur,
    returpenerimaanbarangdetail_t.tgl_kadaluarsa,
    returpenerimaanbarang_t.alasan_retur,
    penerimaanbarangdetail_t.no_batch,
    validasipobarang_t.no_pobarang,
    returpenerimaanbarangdetail_t.returpenerimaanbarangdetail_id,
    barang_m.barang_id,
    penerimaanbarangdetail_t.penerimaanbarangdetail_id,
    returpenerimaanbarangdetail_t.qty_input
   FROM returpenerimaanbarang_t
     JOIN returpenerimaanbarangdetail_t ON returpenerimaanbarang_t.returpenerimaanbarang_id = returpenerimaanbarangdetail_t.returpenerimaanbarang_id
     LEFT JOIN penerimaanbarang_t ON returpenerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id
     LEFT JOIN penerimaanbarangdetail_t ON returpenerimaanbarangdetail_t.penerimaanbarangdetail_id = penerimaanbarangdetail_t.penerimaanbarangdetail_id
     LEFT JOIN validasipobarang_t ON penerimaanbarang_t.validasipobarang_id = validasipobarang_t.validasipobarang_id
     JOIN barang_m ON barang_m.barang_id = returpenerimaanbarangdetail_t.barang_id
     JOIN satuanunit_m ON satuanunit_m.satuanunit_id = returpenerimaanbarangdetail_t.satuanbesar_id
     LEFT JOIN supplier_m ON penerimaanbarang_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT returpenerimaanbarangdetail_t_1.penerimaanbarangdetail_id,
            sum(returpenerimaanbarangdetail_t_1.qty_retur) AS on_retur
           FROM returpenerimaanbarangdetail_t returpenerimaanbarangdetail_t_1
          GROUP BY returpenerimaanbarangdetail_t_1.penerimaanbarangdetail_id) returdetailjumlah ON penerimaanbarangdetail_t.penerimaanbarangdetail_id = returdetailjumlah.penerimaanbarangdetail_id;
");

        $this->execute('ALTER TABLE public.inforeturpenerimaanbarangdetail_v
  OWNER TO postgres;
');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopenerimaanbarangdetail_v AS 
 SELECT terima.penerimaanbarang_id,
    terima.tgl_penerimaan,
    terima.no_penerimaan,
    terima.nomor_po,
    terima.supplier_id,
    supplier_m.supplier_nama,
    terima.barang_id,
    barang_m.barang_nama,
    terima.qty_po,
    terima.qty_diterima,
    terima.po_balance,
        CASE
            WHEN barang_m.is_kadaluarsa = true THEN terima.tgl_kadaluarsa
            ELSE NULL::date
        END AS tgl_kadaluarsa,
    terima.no_batch,
    terima.s_konversibrg_id,
    satuankonversibrg_m.satuanbesar_id,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversibrg_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS satuanunit_nama,
    terima.no_suratjalan,
    terima.tgl_suratjalan,
    terima.no_faktur,
    terima.diterima_oleh,
    terima.keterangan,
    terima.upload_berkas,
    terima.catatan_berkas,
    terima.catatan,
    COALESCE(terima.is_verifikasi::integer, 0) AS status_invoice,
    besar.satuanunit_nama AS satuan_besar,
    kecil.satuanunit_nama AS satuan_kecil,
    barang_m.is_kadaluarsa,
    terima.penerimaanbarangdetail_id,
    terima.harga,
    terima.discount_rp,
    terima.discount,
    terima.jumlah,
    terima.validasipobarangdetail_id,
    terima.pajak_id,
    satuankonversibrg_m.nilai_konversi,
    COALESCE(returdetailjumlah.on_retur, 0::bigint) AS on_retur
   FROM ( SELECT penerimaanbarang_t.penerimaanbarang_id,
            penerimaanbarang_t.tgl_penerimaan,
            penerimaanbarang_t.no_penerimaan,
            validasipobarang_t.no_pobarang AS nomor_po,
            penerimaanbarang_t.supplier_id,
            penerimaanbarang_t.no_suratjalan,
            penerimaanbarang_t.tgl_suratjalan,
            penerimaanbarang_t.no_faktur,
            penerimaanbarang_t.diterima_oleh,
            penerimaanbarang_t.upload_berkas,
            penerimaanbarang_t.catatan_berkas,
            penerimaanbarang_t.catatan,
            penerimaanbarang_t.peg_mengetahui,
            penerimaanbarang_t.peg_menyetujui,
            penerimaanbarangdetail_t.penerimaanbarangdetail_id,
            penerimaanbarangdetail_t.barang_id,
            penerimaanbarangdetail_t.qty_po,
            penerimaanbarangdetail_t.qty_diterima,
            penerimaanbarangdetail_t.po_balance,
            penerimaanbarangdetail_t.tgl_kadaluarsa,
            penerimaanbarangdetail_t.no_batch,
            penerimaanbarangdetail_t.s_konversibrg_id,
            penerimaanbarangdetail_t.keterangan,
            penerimaanbarangdetail_t.harga,
            penerimaanbarangdetail_t.discount_rp,
            penerimaanbarangdetail_t.discount,
            penerimaanbarangdetail_t.jumlah,
            penerimaanbarangdetail_t.validasipobarangdetail_id,
            penerimaanbarang_t.is_verifikasi,
            validasipobarang_t.pajak_id
           FROM penerimaanbarang_t
             JOIN penerimaanbarangdetail_t ON penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id
             JOIN validasipobarang_t ON penerimaanbarang_t.validasipobarang_id = validasipobarang_t.validasipobarang_id
          WHERE penerimaanbarangdetail_t.is_deleted = false) terima
     JOIN supplier_m ON terima.supplier_id = supplier_m.supplier_id
     JOIN barang_m ON terima.barang_id = barang_m.barang_id
     JOIN satuankonversibrg_m ON terima.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
     JOIN satuanunit_m besar ON satuankonversibrg_m.satuanbesar_id = besar.satuanunit_id
     JOIN satuanunit_m kecil ON satuankonversibrg_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN pegawai_m peg_mengetahui ON terima.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON terima.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN ( SELECT returpenerimaanbarangdetail_t.penerimaanbarangdetail_id,
            sum(returpenerimaanbarangdetail_t.qty_retur) AS on_retur
           FROM returpenerimaanbarangdetail_t returpenerimaanbarangdetail_t
          GROUP BY returpenerimaanbarangdetail_t.penerimaanbarangdetail_id) returdetailjumlah ON terima.penerimaanbarangdetail_id = returdetailjumlah.penerimaanbarangdetail_id;
");

        $this->execute('ALTER TABLE public.infopenerimaanbarangdetail_v
  OWNER TO postgres;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infokartustokbarang_v AS 
 SELECT stok_brg.stokbarang_id,
    stok_brg.tanggal_transaksi,
    stok_brg.barang_id,
    barang_m.barang_nama,
    stok_brg.no_transakasi,
    stok_brg.qtystok_in,
    stok_brg.qtystok_out,
    stok_brg.stok,
    satuanunit_m.satuanunit_nama,
    stok_brg.tglkadaluarsa,
    stok_brg.keterangan,
    stok_brg.ruangan_asal_id,
    stok_brg.ruangan_tujuan_id,
    ruangan_asal.ruangan_nama AS ruangan_asal_nama,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan_nama,
    stok_brg.ruangan_id
   FROM ( SELECT stokbarang_t.stokbarang_id,
            stokbarang_t.barang_id,
            stokbarang_t.tglstok_in,
            stokbarang_t.tglstok_out,
                CASE
                    WHEN stokbarang_t.is_deleted = true THEN 0::double precision
                    ELSE stokbarang_t.qtystok_in
                END AS qtystok_in,
                CASE
                    WHEN stokbarang_t.is_deleted = true THEN 0::double precision
                    ELSE stokbarang_t.qtystok_out
                END AS qtystok_out,
            stokbarang_t.stok,
            stokbarang_t.tglkadaluarsa,
            stokbarang_t.ruangan_id,
                CASE
                    WHEN stokbarang_t.tglstok_in IS NOT NULL THEN stokbarang_t.tglstok_in::text
                    WHEN stokbarang_t.tglstok_out IS NOT NULL THEN stokbarang_t.tglstok_out::text
                    ELSE ''::text
                END AS tanggal_transaksi,
                CASE
                    WHEN penerimaan_barang.no_penerimaan IS NOT NULL THEN penerimaan_barang.no_penerimaan::text
                    WHEN mutasi_barang.nomutasi_barang IS NOT NULL THEN mutasi_barang.nomutasi_barang::text
                    WHEN terima_mutasi.noterimamutasi IS NOT NULL THEN terima_mutasi.noterimamutasi::text
                    WHEN stokopname_barang.nostokopname IS NOT NULL THEN stokopname_barang.nostokopname::text
                    WHEN adjusmen_masuk.no_adjusmen IS NOT NULL THEN adjusmen_masuk.no_adjusmen::text
                    WHEN adjusmen_keluar.no_adjusmen IS NOT NULL THEN adjusmen_keluar.no_adjusmen::text
                    WHEN retur_barang.no_returpenerimaanbarang IS NOT NULL THEN retur_barang.no_returpenerimaanbarang::text
                    WHEN pemakaian_barang.no_pemakaianbarang IS NOT NULL THEN pemakaian_barang.no_pemakaianbarang::text
                    ELSE ''::text
                END AS no_transakasi,
                CASE
                    WHEN penerimaan_barang.penerimaanbarangdetail_id IS NOT NULL THEN 'Penerimaan Supplier'::text
                    WHEN mutasi_barang.mutasibarangdetail_id IS NOT NULL THEN 'Mutasi Barang'::text
                    WHEN terima_mutasi.terimamutasibarangdetail_id IS NOT NULL THEN 'Terima Mutasi Barang'::text
                    WHEN stokopname_barang.stokopnamebarangdetail_id IS NOT NULL THEN 'Stok Opname Barang'::text
                    WHEN adjusmen_masuk.adjusmenbarangmasuk_id IS NOT NULL THEN 'Adjusmen Barang Masuk'::text
                    WHEN adjusmen_keluar.adjusmenbarangkeluar_id IS NOT NULL THEN 'Adjusmen Barang Keluar'::text
                    WHEN retur_barang.returpenerimaanbarangdetail_id IS NOT NULL THEN 'Retur Penerimaan Barang'::text
                    WHEN pemakaian_barang.pemakaianbarangdetail_id IS NOT NULL THEN 'Pemakaian Barang'::text
                    ELSE '-'::text
                END AS keterangan,
                CASE
                    WHEN mutasi_barang.ruanganasal_id IS NOT NULL THEN COALESCE(mutasi_barang.ruanganasal_id, 0)
                    WHEN terima_mutasi.ruanganasalmutasi_id IS NOT NULL THEN COALESCE(terima_mutasi.ruanganasalmutasi_id, 0)
                    ELSE COALESCE(stokbarang_t.ruangan_id, 0)
                END AS ruangan_asal_id,
                CASE
                    WHEN mutasi_barang.ruangantujuan_id IS NOT NULL THEN COALESCE(mutasi_barang.ruangantujuan_id, 0)
                    WHEN terima_mutasi.ruanganpenerima_id IS NOT NULL THEN COALESCE(terima_mutasi.ruanganpenerima_id, 0)
                    ELSE COALESCE(stokbarang_t.ruangan_id, 0)
                END AS ruangan_tujuan_id
           FROM stokbarang_t
             LEFT JOIN ( SELECT penerimaanbarangdetail_t.penerimaanbarangdetail_id,
                    penerimaanbarang_t.no_penerimaan
                   FROM penerimaanbarang_t
                     JOIN penerimaanbarangdetail_t ON penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id) penerimaan_barang ON stokbarang_t.penerimaandetail_id = penerimaan_barang.penerimaanbarangdetail_id
             LEFT JOIN ( SELECT mutasibarang_t.nomutasi_barang,
                    mutasibarangdetail_t.mutasibarangdetail_id,
                    mutasibarang_t.ruanganasal_id,
                    mutasibarang_t.ruangantujuan_id
                   FROM mutasibarang_t
                     JOIN mutasibarangdetail_t ON mutasibarang_t.mutasibarang_id = mutasibarangdetail_t.mutasibarang_id) mutasi_barang ON mutasi_barang.mutasibarangdetail_id = stokbarang_t.mutasibarangdetail_id
             LEFT JOIN ( SELECT terimamutasibarang_t.noterimamutasi,
                    terimamutasibarangdetail_t.terimamutasibarangdetail_id,
                    terimamutasibarang_t.ruanganasalmutasi_id,
                    terimamutasibarang_t.ruanganpenerima_id
                   FROM terimamutasibarang_t
                     JOIN terimamutasibarangdetail_t ON terimamutasibarang_t.terimamutasibarang_id = terimamutasibarangdetail_t.terimamutasibarangdetail_id) terima_mutasi ON terima_mutasi.terimamutasibarangdetail_id = stokbarang_t.terimamutasibarangdetail_id
             LEFT JOIN ( SELECT returpenerimaanbarang_t.no_returpenerimaanbarang,
                    returpenerimaanbarangdetail_t.returpenerimaanbarangdetail_id
                   FROM returpenerimaanbarang_t
                     JOIN returpenerimaanbarangdetail_t ON returpenerimaanbarang_t.returpenerimaanbarang_id = returpenerimaanbarangdetail_t.returpenerimaanbarang_id) retur_barang ON retur_barang.returpenerimaanbarangdetail_id = stokbarang_t.returbarangdetail_id
             LEFT JOIN ( SELECT adjusmenbarang_t.no_adjusmen,
                    adjusmenbarangmasuk_t.adjusmenbarangmasuk_id
                   FROM adjusmenbarang_t
                     JOIN adjusmenbarangmasuk_t ON adjusmenbarang_t.adjusmenbarang_id = adjusmenbarangmasuk_t.adjusmenbarang_id) adjusmen_masuk ON stokbarang_t.adjusmenbarangmasuk_id = adjusmen_masuk.adjusmenbarangmasuk_id
             LEFT JOIN ( SELECT adjusmenbarang_t.no_adjusmen,
                    adjusmenbarangkeluar_t.adjusmenbarangkeluar_id
                   FROM adjusmenbarang_t
                     JOIN adjusmenbarangkeluar_t ON adjusmenbarang_t.adjusmenbarang_id = adjusmenbarangkeluar_t.adjusmenbarang_id) adjusmen_keluar ON stokbarang_t.adjusmenbarangkeluar_id = adjusmen_keluar.adjusmenbarangkeluar_id
             LEFT JOIN ( SELECT stokopnamebarang_t.nostokopname,
                    stokopnamebarangdetail_t.stokopnamebarangdetail_id
                   FROM stokopnamebarang_t
                     JOIN stokopnamebarangdetail_t ON stokopnamebarang_t.stokopnamebarang_id = stokopnamebarangdetail_t.stokopnamebarang_id) stokopname_barang ON stokbarang_t.stokopnamebarangdetail_id = stokopname_barang.stokopnamebarangdetail_id
             LEFT JOIN ( SELECT pemakaianbarang_t.no_pemakaianbarang,
                    pemakaianbarangdetail_t.pemakaianbarangdetail_id
                   FROM pemakaianbarang_t
                     JOIN pemakaianbarangdetail_t ON pemakaianbarang_t.pemakaianbarang_id = pemakaianbarangdetail_t.pemakaianbarang_id) pemakaian_barang ON stokbarang_t.pemakaianbarangdetail_id = pemakaian_barang.pemakaianbarangdetail_id) stok_brg
     JOIN barang_m ON barang_m.barang_id = stok_brg.barang_id
     JOIN satuanunit_m ON barang_m.satuankecil_id = satuanunit_m.satuanunit_id
     JOIN ruangan_m ruangan_asal ON ruangan_asal.ruangan_id = stok_brg.ruangan_asal_id
     JOIN ruangan_m ruangan_tujuan ON ruangan_tujuan.ruangan_id = stok_brg.ruangan_tujuan_id;");

        $this->execute('ALTER TABLE public.infokartustokbarang_v
  OWNER TO postgres;');

        $this->execute("
            CREATE OR REPLACE VIEW public.sync_retursupplier AS 
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
                    returpenerimaanobatdetail_t_1.qty_input AS qty_retur,
                    penerimaansuppdetail_t_1.harga_netto AS harga,
                    returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaansuppdetail_t_1.harga_netto AS jumlah,
                    penerimaansuppdetail_t_1.diskon AS discount,
                    penerimaansuppdetail_t_1.harga_netto * returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaansuppdetail_t_1.diskon::double precision / 100::double precision AS discount_amount,
                    penerimaansupp_t.no_faktur
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
                    sum(returpenerimaanobatdetail_t_1.qty_input) AS qty_retur,
                    penerimaansuppdetail_t_1.harga_netto AS harga,
                    sum(penerimaansuppdetail_t_1.harga_netto * returpenerimaanobatdetail_t_1.qty_input::double precision) AS jumlah,
                    sum(penerimaansuppdetail_t_1.harga_netto * returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaansuppdetail_t_1.diskon::double precision / 100::double precision) AS discount_amount
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
          WHERE syncakuntansi_r.is_sync IS TRUE))
  GROUP BY returpenerimaanobat_t.returpenerimaanobat_id, returpenerimaanobat_t.no_returpenerimaanobat, returpenerimaanobat_t.tgl_retur, ruangan_m.instalasi_id, returpenerimaanobat_t.ruanganretur_id, ruangan_m.ruangan_nama, supplier_m.supplier_id, supplier_m.supplier_nama, payterm_m.payterm_kode, pajak_m.pajak_kode, penerimaansupp_t.no_faktur
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
                    returpenerimaanobatdetail_t_1.qty_input AS qty_retur,
                    penerimaanobatdetail_t_1.harga,
                    returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaanobatdetail_t_1.harga AS jumlah,
                    penerimaanobatdetail_t_1.discount,
                    penerimaanobatdetail_t_1.harga * returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaanobatdetail_t_1.discount / 100::double precision AS discount_amount
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
                    sum(returpenerimaanobatdetail_t_1.qty_input) AS qty_retur,
                    penerimaanobatdetail_t_1.harga,
                    sum(returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaanobatdetail_t_1.harga) AS jumlah,
                    sum(penerimaanobatdetail_t_1.harga * returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaanobatdetail_t_1.discount / 100::double precision) AS discount_amount
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
          WHERE syncakuntansi_r.is_sync IS TRUE))
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
                    returpenerimaanbarangdetail_t_1.qty_input AS qty_retur,
                    penerimaanbarangdetail_t_1.harga,
                    returpenerimaanbarangdetail_t_1.qty_input::double precision * penerimaanbarangdetail_t_1.harga AS jumlah,
                    penerimaanbarangdetail_t_1.discount,
                    penerimaanbarangdetail_t_1.harga * returpenerimaanbarangdetail_t_1.qty_input::double precision * penerimaanbarangdetail_t_1.discount / 100::double precision AS discount_amount
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
                    sum(returpenerimaanbarangdetail_t_1.qty_input) AS qty_retur,
                    penerimaanbarangdetail_t_1.harga,
                    sum(returpenerimaanbarangdetail_t_1.qty_input::double precision * penerimaanbarangdetail_t_1.harga) AS jumlah,
                    sum(penerimaanbarangdetail_t_1.harga * returpenerimaanbarangdetail_t_1.qty_input::double precision * penerimaanbarangdetail_t_1.discount / 100::double precision) AS discount_amount
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
          WHERE syncakuntansi_r.is_sync IS TRUE))
  GROUP BY returpenerimaanbarang_t.returpenerimaanbarang_id, returpenerimaanbarang_t.no_returpenerimaanbarang, returpenerimaanbarang_t.tgl_retur, ruangan_m.instalasi_id, penerimaanbarang_t.ruanganpenerima_id, ruangan_m.ruangan_nama, supplier_m.supplier_id, supplier_m.supplier_nama, payterm_m.payterm_kode, pajak_m.pajak_kode;
");

        $this->execute('ALTER TABLE public.sync_retursupplier
  OWNER TO postgres;');

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
                    concat(obatalkes_m.obatalkes_nama, ' (1', sat_besar.satuanunit_nama, ' = ', penerimaansuppdetail_t_1.qty_besar / penerimaansuppdetail_t_1.qty_kecil, ' ', sat_kecil.satuanunit_nama, ')') AS \"desc\",
                    penerimaansuppdetail_t_1.obatalkes_id,
                    jenisobatalkes_m.jenisobatalkes_kode AS category_code,
                    returpenerimaanobatdetail_t_1.qty_input AS qty_retur,
                    penerimaansuppdetail_t_1.harga_netto AS harga,
                    returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaansuppdetail_t_1.harga_netto AS jumlah,
                    penerimaansuppdetail_t_1.diskon AS discount,
                    penerimaansuppdetail_t_1.harga_netto * returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaansuppdetail_t_1.diskon::double precision / 100::double precision AS discount_amount,
                    penerimaansupp_t.no_faktur
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
                    sum(returpenerimaanobatdetail_t_1.qty_input) AS qty_retur,
                    penerimaansuppdetail_t_1.harga_netto AS harga,
                    sum(penerimaansuppdetail_t_1.harga_netto * returpenerimaanobatdetail_t_1.qty_input::double precision) AS jumlah,
                    sum(penerimaansuppdetail_t_1.harga_netto * returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaansuppdetail_t_1.diskon::double precision / 100::double precision) AS discount_amount
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
  GROUP BY returpenerimaanobat_t.returpenerimaanobat_id, returpenerimaanobat_t.no_returpenerimaanobat, returpenerimaanobat_t.tgl_retur, ruangan_m.instalasi_id, returpenerimaanobat_t.ruanganretur_id, ruangan_m.ruangan_nama, supplier_m.supplier_id, supplier_m.supplier_nama, payterm_m.payterm_kode, pajak_m.pajak_kode, penerimaansupp_t.no_faktur
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
                    concat(obatalkes_m.obatalkes_nama, ' (1', sat_besar.satuanunit_nama, ' = ', satuankonversi_m.nilai_konversi, ' ', sat_kecil.satuanunit_nama, ')') AS \"desc\",
                    penerimaanobatdetail_t_1.obatalkes_id,
                    jenisobatalkes_m.jenisobatalkes_kode AS category_code,
                    returpenerimaanobatdetail_t_1.qty_input AS qty_retur,
                    penerimaanobatdetail_t_1.harga,
                    returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaanobatdetail_t_1.harga AS jumlah,
                    penerimaanobatdetail_t_1.discount,
                    penerimaanobatdetail_t_1.harga * returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaanobatdetail_t_1.discount / 100::double precision AS discount_amount
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
                    sum(returpenerimaanobatdetail_t_1.qty_input) AS qty_retur,
                    penerimaanobatdetail_t_1.harga,
                    sum(returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaanobatdetail_t_1.harga) AS jumlah,
                    sum(penerimaanobatdetail_t_1.harga * returpenerimaanobatdetail_t_1.qty_input::double precision * penerimaanobatdetail_t_1.discount / 100::double precision) AS discount_amount
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
                    concat(barang_m.barang_nama, ' (1', sat_besar.satuanunit_nama, ' = ', satuankonversibrg_m.nilai_konversi, ' ', sat_kecil.satuanunit_nama, ')') AS \"desc\",
                    penerimaanbarangdetail_t_1.barang_id AS obatalkes_id,
                    kelompokbarang_m.kelompokbarang_kode AS category_code,
                    returpenerimaanbarangdetail_t_1.qty_input AS qty_retur,
                    penerimaanbarangdetail_t_1.harga,
                    returpenerimaanbarangdetail_t_1.qty_input::double precision * penerimaanbarangdetail_t_1.harga AS jumlah,
                    penerimaanbarangdetail_t_1.discount,
                    penerimaanbarangdetail_t_1.harga * returpenerimaanbarangdetail_t_1.qty_input::double precision * penerimaanbarangdetail_t_1.discount / 100::double precision AS discount_amount
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
                    sum(returpenerimaanbarangdetail_t_1.qty_input) AS qty_retur,
                    penerimaanbarangdetail_t_1.harga,
                    sum(returpenerimaanbarangdetail_t_1.qty_input::double precision * penerimaanbarangdetail_t_1.harga) AS jumlah,
                    sum(penerimaanbarangdetail_t_1.harga * returpenerimaanbarangdetail_t_1.qty_input::double precision * penerimaanbarangdetail_t_1.discount / 100::double precision) AS discount_amount
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

        $this->execute('ALTER TABLE public.sync_retursupplier_de
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191007_064347_retur_penerimaan_barang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191007_064347_retur_penerimaan_barang cannot be reverted.\n";

        return false;
    }
    */
}
