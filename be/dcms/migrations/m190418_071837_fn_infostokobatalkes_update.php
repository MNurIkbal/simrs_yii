<?php

use yii\db\Migration;

/**
 * Class m190418_071837_fn_infostokobatalkes_update
 */
class m190418_071837_fn_infostokobatalkes_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
   {
        $this->execute('
            CREATE OR REPLACE FUNCTION infostokobatalkes_fn()
  RETURNS TABLE(periodestok_id integer, periodestok_nama character varying, tglperiodestok_awal timestamp without time zone, tglperiodestok_akhir timestamp without time zone, instalasi_id integer, instalasi_nama character varying, ruangan_id integer, ruangan_nama character varying, obatalkes_id integer, obatalkes_nama character varying, obatalkes_namalain character varying, obatalkes_kode character varying, qty_masuk integer, qty_keluar integer, qty_dipesan integer, qty_tersedia integer, qty_stok integer, nilai_ro integer, satuanbesar_id integer, satuanbesar_nama character varying, satuankecil_id integer, satuankecil_nama character varying, satuansedang_id integer, satuansedang_nama character varying, group_jenisobat integer, group_jenisobat_nama character varying, jenisobatalkes_id integer, jenisobatalkes_nama character varying, konfigygdigunakan text, hargajual double precision, ppn double precision, margin double precision, disc double precision, harganetto double precision, hn_last_margin double precision, hn_last_diskon double precision, hn_last_margin_diskon double precision, hn_last_ppn double precision, hargajual_last double precision, hargamaksimum double precision, hn_max_margin double precision, hn_max_diskon double precision, hn_max_margin_diskon double precision, hn_max_ppn double precision, hargajual_max double precision, hargaminimum double precision, hn_min_margin double precision, hn_min_diskon double precision, hn_min_margin_diskon double precision, hn_min_ppn double precision, hargajual_min double precision, hargaratarata double precision, hn_avg_margin double precision, hn_avg_diskon double precision, hn_avg_margin_diskon double precision, hn_avg_ppn double precision, hargajual_avg double precision, hargaygdipakai double precision, harganetto_ygdipakai double precision, harganetto_sugesstion double precision, hn_margin double precision, hn_diskon double precision, hn_ppn double precision, persen_ppn double precision, persen_margin double precision, persen_disc double precision, jml_hargajual double precision, jml_harganetto double precision, jml_sugesstion double precision, jml_margin double precision, jml_discount double precision, jml_ppn double precision, persenmargin_id integer) AS
$BODY$
                        BEGIN 
                            RETURN QUERY 
                            SELECT 
                                hit.periodestok_id,
                                hit.periodestok_nama,
                                hit.tglperiodestok_awal::timestamp,
                                hit.tglperiodestok_akhir::timestamp,
                                hit.instalasi_id,
                                hit.instalasi_nama,
                                hit.ruangan_id,
                                hit.ruangan_nama,
                                hit.obatalkes_id,
                                hit.obatalkes_nama,
                                hit.obatalkes_nama AS obatalkes_namalain,
                                hit.obatalkes_kode,
                                hit.qty_masuk,
                                hit.qty_keluar,
                                hit.qty_dipesan,
                                hit.qty_tersedia,
                                hit.qty_stok,
                                hit.nilai_ro,
                                hit.satuanbesar_id,
                                hit.satuanbesar_nama,
                                hit.satuankecil_id,
                                hit.satuankecil_nama,
                                hit.satuansedang_id,
                                hit.satuansedang_nama,
                                hit.group_jenisobat::int4,
                                fgetnamalookup(hit.group_jenisobat)::varchar,
                                hit.jenisobatalkes_id::int4,
                                hit.jenisobatalkes_nama,
                                hit.hargaygdigunakan::text,
                                hit.harga_jual AS hargajual,
                                hit.ppn,
                                hit.margin,
                                hit.disc,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.hargamaksimum
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.hargaminimum
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.hargaratarata
            --                         ELSE hit.harganetto
            --                     END AS harganetto,
                                                    hit.harganetto,
                                 CASE
                                     WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                                     WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                                     WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                                     ELSE hit.a1
                                 END AS hn_last_margin,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                                    ELSE hit.a2
                                END AS hn_last_diskon,
                                hit.a3 AS hn_last_margin_diskon,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                                    ELSE hit.a4
                                END AS hn_last_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_last,
                                                    hit.harga_jual AS hargajual_last,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargamaksimum,
                                                    hit.harga_jual AS hargamaksimum,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                                    ELSE hit.a1
                                END AS hn_max_margin,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                                    ELSE hit.a2
                                END AS hn_max_diskon,
                                hit.c3 AS hn_max_margin_diskon,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                                    ELSE hit.a4
                                END AS hn_max_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_max,
                                                    hit.harga_jual AS hargajual_max,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargaminimum,
                                                    hit.harga_jual AS hargaminimum,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                                    ELSE hit.a1
                                END AS hn_min_margin,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                                    ELSE hit.a2
                                END AS hn_min_diskon,
                                hit.b3 AS hn_min_margin_diskon,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                                    ELSE hit.a4
                                END AS hn_min_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_min,
                                                    hit.harga_jual AS hargajual_min,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
                                    ELSE hit.a5
                                END AS hargaratarata,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                                    ELSE hit.a1
                                END AS hn_avg_margin,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                                    ELSE hit.a2
                                END AS hn_avg_diskon,
                                hit.d3 AS hn_avg_margin_diskon,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                                    ELSE hit.a4
                                END AS hn_avg_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_avg,
                                                    hit.harga_jual AS hargajual_avg,
                                hit.harga_jual,
                                hit.harganetto, 
                                CASE
                                    WHEN ((hit.hargaygdigunakan)::text = \'MAX\'::text) THEN hit.hargamaksimum
                                    WHEN ((hit.hargaygdigunakan)::text = \'MIN\'::text) THEN hit.hargaminimum
                                    WHEN ((hit.hargaygdigunakan)::text = \'AVG\'::text) THEN hit.hargaratarata
                                    ELSE hit.hargaterakhir
                                END AS sugesstion,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                                    ELSE hit.a1
                                END AS hn_margin,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                                    ELSE hit.a2
                                END AS hn_diskon,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                                    ELSE hit.a4
                                END AS hn_ppn,
                                hit.ppn,
                                fgetpersenmargin(hit.harganetto) AS margin,
                                hit.disc,
                                hit.harga_jual,
                                hit.harganetto, 
                                CASE
                                    WHEN ((hit.hargaygdigunakan)::text = \'MAX\'::text) THEN hit.hargamaksimum
                                    WHEN ((hit.hargaygdigunakan)::text = \'MIN\'::text) THEN hit.hargaminimum
                                    WHEN ((hit.hargaygdigunakan)::text = \'AVG\'::text) THEN hit.hargaratarata
                                    ELSE hit.hargaterakhir
                                END AS sugesstion,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                                    ELSE hit.a1
                                END ,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                                    ELSE hit.a2
                                END ,
                                CASE
                                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                                    ELSE hit.a4
                                END ,
                                        fgetpersenmargin_id(hit.harganetto) AS margin_id
                            FROM ( 
                                SELECT 
                                    periodestokobat_m.periodestokobat_id AS periodestok_id,
                                    periodestokobat_m.periodestok_nama,
                                    instalasi_m.instalasi_id,
                                    instalasi_m.instalasi_nama,
                                    stokobatalkes_r.ruangan_id,
                                    ruangan_m.ruangan_nama,
                                    stokobatalkes_r.obatalkes_id,
                                    obatalkes_m.obatalkes_namalain,
                                    obatalkes_m.obatalkes_kode,
                                    obatalkes_m.hargajual,
                                    obatalkes_m.satuankecil_id,
                                    satuan_besar.satuanunit_nama AS satuanbesar_nama,
                                    obatalkes_m.satuansedang_id,
                                    satuan_sedang.satuanunit_nama AS satuansedang_nama,
                                    satuan_kecil.satuanunit_nama AS satuankecil_nama,
                                    obatalkes_m.satuanbesar_id,
                                    obatalkes_m.jenisobatalkes_id,
                                    jenisobatalkes_m.jenisobatalkes_nama,
                                    jenisobatalkes_m.group_jenisobat,
                                    konfigfarmasi_k.persenppn AS ppn,
                                    fgetpersenmargin(obatalkes_m.harganetto) AS margin,
                                    konfigfarmasi_k.persen_diskon AS disc,
                                    konfigfarmasi_k.hargaygdigunakan,
                                    obatalkes_m.harganetto,
                                    obatalkes_m.hargamaksimum,
                                    obatalkes_m.hargaminimum,
                                    obatalkes_m.hargaratarata,
                                                obatalkes_m.hargaterakhir,
                                    stokobatalkes_r.qty_masuk,
                                    stokobatalkes_r.qty_keluar,
                                    stokobatalkes_r.qty_dipesan,
                                    stokobatalkes_r.qty_tersedia,
                                    stokobatalkes_r.qty_sisa AS qty_stok,
                                    periodestokobat_m.tglperiodestok_awal,
                                    periodestokobat_m.tglperiodestok_akhir,
                                    obatalkes_m.obatalkes_nama,
                                    obatalkes_m.nilai_ro,
                                    obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision AS a1,
                                    (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a2,
                                    obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a3,
                                    (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a4,
                                    obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a5,
                                    obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision AS b1,
                                    (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision AS b2,
                                    obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 
                                    100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 
                                    100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b3,
                                    (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - 
                                    (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b4,
                                    obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - 
                                    (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * 
                                    fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * 
                                    fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * 
                                    konfigfarmasi_k.persenppn / 100::double precision AS b5,
                                    obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision AS c1,
                                    (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision AS c2,
                                    obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - 
                                    (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision AS c3,
                                    (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - 
                                    (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c4,
                                    obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - 
                                    (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * 
                                    fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * 
                                    fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * 
                                    konfigfarmasi_k.persenppn / 100::double precision AS c5,
                                    obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision AS d1,
                                    (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision AS d2,
                                    obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - 
                                    (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision AS d3,
                                    (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - 
                                    (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d4,
                                    obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - 
                                    (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * 
                                    fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * 
                                    fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * 
                                    konfigfarmasi_k.persenppn / 100::double precision AS d5,
                                    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - 
                                    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * 
                                    konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.harganetto + obatalkes_m.harganetto * 
                                    fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * 
                                    fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * 
                                    konfigfarmasi_k.persenppn / 100::double precision AS harga_jual
                                FROM stokobatalkes_r
                                JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
                                JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                                JOIN obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
                                LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
                                LEFT JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
                                LEFT JOIN satuanunit_m satuan_besar ON obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id
                                LEFT JOIN satuanunit_m satuan_sedang ON obatalkes_m.satuansedang_id = satuan_sedang.satuanunit_id
                                JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                                JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                                WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false AND stokobatalkes_r.is_periode = true) hit;
                        END; $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
        ');
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190418_071837_fn_infostokobatalkes_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190418_071837_fn_infostokobatalkes_update cannot be reverted.\n";

        return false;
    }
    */
}
