<?php

use yii\db\Migration;

/**
 * Class m210826_105304_migrate_US658_infostokbarang
 */
class m210826_105304_migrate_US658_infostokbarang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infostokbarang_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infostokbarang_v\" AS  SELECT hit.periodestok_id,
    hit.periodestok_nama,
    hit.instalasi_id,
    hit.instalasi_nama,
    hit.ruangan_id,
    hit.ruangan_nama,
    hit.barang_id,
    hit.barang_nama,
    hit.qty_masuk,
    hit.qty_keluar,
    hit.qty_dipesan,
    hit.qty_tersedia,
    hit.qty_stok,
    hit.tglperiodestok_awal,
    hit.tglperiodestok_akhir,
    hit.barang_kode,
    hit.ppn,
    hit.harga_jual,
    hit.satuankecil_id,
    hit.satuankecil_nama,
    hit.satuansedang_id,
    hit.satuansedang_nama,
    hit.satuanbesar_id,
    hit.satuanbesar_nama,
    hit.on_ro,
    hit.on_po,
    hit.nilai_ro,
    hit.is_kadaluarsa,
    hit.ppn_konf,
    hit.margin,
    hit.disc,
    hit.hargaygdigunakan,
    hit.harga_netto,
    hit.a1 AS hn_last_margin,
    hit.a2 AS hn_last_diskon,
    hit.a3 AS hn_last_margin_diskon,
    hit.a4 AS hn_last_ppn,
    hit.a5 AS hargajual_last,
    hit.harga_min,
    hit.b1 AS hn_min_margin,
    hit.b2 AS hn_min_diskon,
    hit.b3 AS hn_min_margin_diskon,
    hit.b4 AS hn_min_ppn,
    hit.b5 AS hargajual_min,
    hit.harga_max,
    hit.c1 AS hn_max_margin,
    hit.c2 AS hn_max_diskon,
    hit.c3 AS hn_max_margin_diskon,
    hit.c4 AS hn_max_ppn,
    hit.c5 AS hargajual_max,
    hit.barang_average,
    hit.d1 AS hn_avg_margin,
    hit.d2 AS hn_avg_diskon,
    hit.d3 AS hn_avg_margin_diskon,
    hit.d4 AS hn_avg_ppn,
    hit.d5 AS hargajual_avg,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c5
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b5
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d5
            ELSE hit.a5
        END AS hargaygdipakai,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.harga_max
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.harga_min
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.barang_average
            ELSE hit.harga_netto
        END AS harganetto_ygdipakai,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c1
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b1
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d1
            ELSE hit.a1
        END AS hn_margin,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c2
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b2
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d2
            ELSE hit.a2
        END AS hn_diskon,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c4
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b4
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d4
            ELSE hit.a4
        END AS hn_ppn,
    hit.kelompokbarang_id,
    hit.kelompokbarang_nama
   FROM ( SELECT periodestokobat_m.periodestokobat_id AS periodestok_id,
            periodestokobat_m.periodestok_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            stokbarang_r.ruangan_id,
            ruangan_m.ruangan_nama,
            stokbarang_r.barang_id,
            barang_m.barang_nama,
            stokbarang_r.qty_masuk,
            stokbarang_r.qty_keluar,
            stokbarang_r.qty_dipesan,
            stokbarang_r.qty_tersedia,
            stokbarang_r.qty_sisa AS qty_stok,
            periodestokobat_m.tglperiodestok_awal,
            periodestokobat_m.tglperiodestok_akhir,
            barang_m.barang_kode,
            barang_m.barang_ppn AS ppn,
            barang_m.barang_hargajual AS harga_jual,
            barang_m.barang_harganetto AS harga_netto,
            barang_m.barang_max AS harga_max,
            barang_m.barang_min AS harga_min,
            barang_m.barang_average,
            barang_m.on_ro,
            barang_m.on_po,
            barang_m.satuankecil_id,
            satuanunit_m.satuanunit_nama AS satuankecil_nama,
            NULL::text AS satuansedang_id,
            NULL::text AS satuansedang_nama,
            NULL::text AS satuanbesar_id,
            NULL::text AS satuanbesar_nama,
            barang_m.nilai_ro,
            barang_m.is_kadaluarsa,
            konfigfarmasi_k.persenppn AS ppn_konf,
            konfigfarmasi_k.persenmargin AS margin,
            konfigfarmasi_k.persen_diskon AS disc,
            konfigfarmasi_k.hargaygdigunakan,
            barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision AS a1,
            (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a2,
            barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a3,
            (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a4,
            barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_harganetto + barang_m.barang_harganetto * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a5,
            barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision AS b1,
            (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b2,
            barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b3,
            (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b4,
            barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_min + barang_m.barang_min * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b5,
            barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision AS c1,
            (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c2,
            barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c3,
            (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c4,
            barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_max + barang_m.barang_max * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c5,
            barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision AS d1,
            (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d2,
            barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d3,
            (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d4,
            barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision - (barang_m.barang_average + barang_m.barang_average * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d5,
            barang_m.kelompokbarang_id,
            kelompokbarang_m.kelompokbarang_nama
           FROM stokbarang_r
             JOIN ruangan_m ON stokbarang_r.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN barang_m ON stokbarang_r.barang_id = barang_m.barang_id
             LEFT JOIN periodestokobat_m ON stokbarang_r.periodestokbarang_id = periodestokobat_m.periodestokobat_id
             JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
             JOIN satuanunit_m ON barang_m.satuankecil_id = satuanunit_m.satuanunit_id
             LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
          WHERE barang_m.is_active = true AND barang_m.is_deleted = false AND stokbarang_r.is_periode = true) hit;");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210826_105304_migrate_US658_infostokbarang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210826_105304_migrate_US658_infostokbarang cannot be reverted.\n";

        return false;
    }
    */
}
