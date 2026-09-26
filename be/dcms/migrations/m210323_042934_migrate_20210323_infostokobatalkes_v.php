<?php

use yii\db\Migration;

/**
 * Class m210323_042934_migrate_20210323_infostokobatalkes_v
 */
class m210323_042934_migrate_20210323_infostokobatalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infostokobatalkes_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"infostokobatalkes_v\" AS  SELECT hit.periodestok_id,
    hit.periodestok_nama,
    hit.tglperiodestok_awal,
    hit.tglperiodestok_akhir,
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
    hit.jenisobatalkes_id,
    hit.jenisobatalkes_nama,
    hit.group_jenisobat,
    hit.hargajual,
    hit.ppn,
    hit.margin,
    hit.disc,
    hit.harganetto,
    hit.a1 AS hn_last_margin,
    hit.a2 AS hn_last_diskon,
    hit.a3 AS hn_last_margin_diskon,
    hit.a4 AS hn_last_ppn,
    hit.a5 AS hargajual_last,
    hit.hargamaksimum,
    hit.c1 AS hn_max_margin,
    hit.c2 AS hn_max_diskon,
    hit.c3 AS hn_max_margin_diskon,
    hit.c4 AS hn_max_ppn,
    hit.c5 AS hargajual_max,
    hit.hargaminimum,
    hit.b1 AS hn_min_margin,
    hit.b2 AS hn_min_diskon,
    hit.b3 AS hn_min_margin_diskon,
    hit.b4 AS hn_min_ppn,
    hit.b5 AS hargajual_min,
    hit.hargaratarata,
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
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.hargamaksimum
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.hargaminimum
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.hargaratarata
            ELSE hit.harganetto
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
        CASE
            WHEN hit.min_stok IS NULL THEN 0::double precision
            ELSE hit.min_stok
        END AS min_stok,
        CASE
            WHEN hit.max_stok IS NULL THEN 0::double precision
            ELSE hit.max_stok
        END AS max_stok
   FROM ( SELECT periodestokobat_m.periodestokobat_id AS periodestok_id,
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
            konfigfarmasi_k.persenmargin AS margin,
            konfigfarmasi_k.persen_diskon AS disc,
            konfigfarmasi_k.hargaygdigunakan,
            obatalkes_m.harganetto,
            obatalkes_m.hargamaksimum,
            obatalkes_m.hargaminimum,
            obatalkes_m.hargaratarata,
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
            (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b2,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b3,
            (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b4,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b5,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision AS c1,
            (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c2,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c3,
            (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c4,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c5,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision AS d1,
            (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d2,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d3,
            (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d4,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d5,
            konfigrak_m.min_stok,
            konfigrak_m.max_stok
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
             JOIN konfigrak_m ON stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id
          WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false AND stokobatalkes_r.is_periode = true) hit;
");

        $this->execute('ALTER TABLE "public"."infostokobatalkes_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210323_042934_migrate_20210323_infostokobatalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210323_042934_migrate_20210323_infostokobatalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
