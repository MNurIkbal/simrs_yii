<?php

use yii\db\Migration;

/**
 * Class m191018_093847_perubahan_schema_20191011_1659
 */
class m191018_093847_perubahan_schema_20191011_1659 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."returpenerimaanbarangdetail_t" 
  ADD COLUMN "no_batch" varchar(100);');

        $this->execute('ALTER TABLE "public"."returpenerimaanbarangdetail_t" 
  ADD COLUMN "stokbarang_id" int4;');

/*infopenerimaansuppbrgdetail_v*/
        $this->execute('DROP VIEW if exists public.infopenerimaansuppbrgdetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopenerimaansuppbrgdetail_v AS 
 SELECT penerimaansupp_t.penerimaansupp_id,
    penerimaansuppdetail_t.penerimaansuppdetail_id,
    penerimaansupp_t.no_penerimaan,
    penerimaansupp_t.tgl_penerimaan,
    penerimaansupp_t.no_faktur,
    penerimaansupp_t.supplier_id,
    supplier_m.supplier_nama,
    penerimaansupp_t.peg_menyetujui,
    peg_menyetujui.nama_pegawai AS peg_menyetujui_nama,
    penerimaansupp_t.peg_mengetahui,
    peg_mengetahui.nama_pegawai AS peg_mengetahui_nama,
    penerimaansupp_t.ruanganpenerima_id,
    ruangan_m.ruangan_nama,
    penerimaansuppdetail_t.barang_id,
    barang_m.barang_nama,
    penerimaansuppdetail_t.tgl_kadaluarsa,
    penerimaansuppdetail_t.satuanbesar_id,
    sat_besar.satuanunit_nama AS satuan_besar,
    penerimaansuppdetail_t.satuankecil_id,
    sat_kecil.satuanunit_nama AS satuan_kecil,
    penerimaansuppdetail_t.qty_besar,
    penerimaansuppdetail_t.qty_kecil,
    penerimaansuppdetail_t.harga_netto,
    penerimaansuppdetail_t.ppn,
    penerimaansuppdetail_t.diskon,
    penerimaansuppdetail_t.qty_besar - COALESCE(retur.total_retur, 0::bigint) AS qty_sisa,
    penerimaansuppdetail_t.no_batch,
    penerimaansuppdetail_t.keterangan,
    satuankonversibrg_m.nilai_konversi,
    stokbarang_t.stokbarang_id
   FROM penerimaansupp_t
     JOIN penerimaansuppdetail_t ON penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id
     JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pegawai_m peg_menyetujui ON penerimaansupp_t.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m peg_mengetahui ON penerimaansupp_t.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
     JOIN barang_m ON penerimaansuppdetail_t.barang_id = barang_m.barang_id
     JOIN satuanunit_m sat_besar ON penerimaansuppdetail_t.satuanbesar_id = sat_besar.satuanunit_id
     JOIN satuanunit_m sat_kecil ON penerimaansuppdetail_t.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id,
            sum(returpenerimaanbarangdetail_t.qty_input) AS total_retur
           FROM returpenerimaanbarangdetail_t
          GROUP BY returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id) retur ON retur.penerimaansuppbrgdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
     LEFT JOIN satuankonversibrg_m ON penerimaansuppdetail_t.barang_id = satuankonversibrg_m.barang_id AND penerimaansuppdetail_t.satuankecil_id = satuankonversibrg_m.satuankecil_id AND penerimaansuppdetail_t.satuanbesar_id = satuankonversibrg_m.satuanbesar_id
     LEFT JOIN stokbarang_t ON stokbarang_t.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id;
");

        $this->execute('ALTER TABLE public.infopenerimaansuppbrgdetail_v
  OWNER TO postgres;');

/*infokartustokbarang_v*/
        $this->execute('DROP VIEW if exists public.infokartustokbarang_v;');

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
                    WHEN penerimaan_supp.no_penerimaan IS NOT NULL THEN penerimaan_supp.no_penerimaan::text
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
                    WHEN penerimaan_supp.penerimaansuppdetail_id IS NOT NULL THEN 'Penerimaan Dari Supplier'::text
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
             LEFT JOIN ( SELECT penerimaansuppdetail_t.penerimaansuppdetail_id,
                    penerimaansupp_t.no_penerimaan
                   FROM penerimaansupp_t
                     JOIN penerimaansuppdetail_t ON penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id
                  WHERE penerimaansupp_t.is_tipe = 1) penerimaan_supp ON stokbarang_t.penerimaansuppdetail_id = penerimaan_supp.penerimaansuppdetail_id
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

/*inforeturpenerimaanbarang_v*/
        $this->execute('DROP VIEW if exists public.inforeturpenerimaanbarang_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.inforeturpenerimaanbarang_v AS 
 SELECT returpenerimaanbarang_t.returpenerimaanbarang_id,
    returpenerimaanbarang_t.no_returpenerimaanbarang,
    returpenerimaanbarang_t.tgl_retur,
    returpenerimaanbarang_t.alasan_retur,
    pegawai_m.nama_pegawai AS pegawai_retur,
    supplier.supplier_nama
   FROM returpenerimaanbarang_t
     LEFT JOIN pegawai_m ON returpenerimaanbarang_t.pegawairetur_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT returpenerimaanbarangdetail_t.returpenerimaanbarang_id,
            supplier_m.supplier_nama
           FROM returpenerimaanbarangdetail_t
             JOIN penerimaansuppdetail_t ON returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
             JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
             JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
          GROUP BY returpenerimaanbarangdetail_t.returpenerimaanbarang_id, supplier_m.supplier_nama) supplier ON returpenerimaanbarang_t.returpenerimaanbarang_id = supplier.returpenerimaanbarang_id;
");

        $this->execute('ALTER TABLE public.inforeturpenerimaanbarang_v
  OWNER TO postgres;');

/*inforeturpenerimaanbarangdetail_v*/
        $this->execute('DROP VIEW if exists public.inforeturpenerimaanbarangdetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.inforeturpenerimaanbarangdetail_v AS 
 SELECT returpenerimaanbarang_t.returpenerimaanbarang_id,
    returpenerimaanbarang_t.tgl_retur,
    returpenerimaanbarang_t.no_returpenerimaanbarang,
        CASE
            WHEN returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id IS NULL THEN penerimaanbarang_t.no_penerimaan
            ELSE penerimaaan_supp.no_penerimaan
        END AS no_penerimaan,
        CASE
            WHEN returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id IS NULL THEN penerimaanbarang_t.no_faktur
            ELSE penerimaaan_supp.no_faktur
        END AS no_faktur,
    supplier_m.supplier_nama,
    barang_m.barang_nama,
    satuanunit_m.satuanunit_nama,
    COALESCE(returpenerimaanbarangdetail_t.qty_retur) AS qty_retur,
    COALESCE(penerimaanbarangdetail_t.qty_diterima) AS qty_diterima,
    COALESCE(returdetailjumlah.on_retur, 0::bigint) AS on_retur,
    returpenerimaanbarangdetail_t.tgl_kadaluarsa,
    returpenerimaanbarang_t.alasan_retur,
        CASE
            WHEN returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id IS NULL THEN penerimaanbarangdetail_t.no_batch
            ELSE penerimaaan_supp.no_batch
        END AS no_batch,
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
          GROUP BY returpenerimaanbarangdetail_t_1.penerimaanbarangdetail_id) returdetailjumlah ON penerimaanbarangdetail_t.penerimaanbarangdetail_id = returdetailjumlah.penerimaanbarangdetail_id
     LEFT JOIN ( SELECT penerimaansuppdetail_t.penerimaansuppdetail_id,
            penerimaansupp_t.no_penerimaan,
            penerimaansupp_t.no_faktur,
            penerimaansuppdetail_t.no_batch
           FROM penerimaansuppdetail_t
             JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id) penerimaaan_supp ON penerimaaan_supp.penerimaansuppdetail_id = returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id;
");

        $this->execute('ALTER TABLE public.inforeturpenerimaanbarangdetail_v
  OWNER TO postgres;');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191018_093847_perubahan_schema_20191011_1659 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191018_093847_perubahan_schema_20191011_1659 cannot be reverted.\n";

        return false;
    }
    */
}
