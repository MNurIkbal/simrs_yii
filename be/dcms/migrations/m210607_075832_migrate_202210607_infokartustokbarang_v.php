<?php

use yii\db\Migration;

/**
 * Class m210607_075832_migrate_202210607_infokartustokbarang_v
 */
class m210607_075832_migrate_202210607_infokartustokbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infokartustokbarang_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokartustokbarang_v\" AS  SELECT stok_brg.stokbarang_id,
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
                     JOIN terimamutasibarangdetail_t ON terimamutasibarang_t.terimamutasibarang_id = terimamutasibarangdetail_t.terimamutasibarang_id) terima_mutasi ON terima_mutasi.terimamutasibarangdetail_id = stokbarang_t.terimamutasibarangdetail_id
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

        $this->execute('ALTER TABLE "public"."infokartustokbarang_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210607_075832_migrate_202210607_infokartustokbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210607_075832_migrate_202210607_infokartustokbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
