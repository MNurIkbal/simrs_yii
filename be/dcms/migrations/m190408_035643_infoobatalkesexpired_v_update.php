<?php

use yii\db\Migration;

/**
 * Class m190408_035643_infoobatalkesexpired_v_update
 */
class m190408_035643_infoobatalkesexpired_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           DROP VIEW infoobatalkesexpired_v;
        ');

        $this->execute("
         CREATE OR REPLACE VIEW infoobatalkesexpired_v AS 
 SELECT hit.obatalkes_id,
    sum(hit.qtystok_in - hit.qtystok_out) AS stok,
    hit.obatalkes_nama,
    hit.satuankecil_id,
    hit.s_kecil AS satuan_kecil,
    hit.tglkadaluarsa,
    hit.harganetto,
    hit.harganetto * sum(hit.qtystok_in - hit.qtystok_out) AS jumlah_harganetto,
    hit.instalasi_nama,
    hit.ruangan_nama,
    hit.periodestokobat_id,
    hit.tglperiodestok_awal AS tglperiodeposting_awal,
    hit.tglperiodestok_akhir AS tglperiodeposting_akhir,
    hit.ruangan_id,
    hit.instalasi_id,
    hit.id_stok,
    hit.nobatch,
    hit.margin,
    hit.ppn,
    hit.disc,
    hit.hn_last,
    hit.a1 AS hn_last_margin,
    hit.a2 AS hn_last_diskon,
    hit.a3 AS hn_last_margin_diskon,
    hit.a4 AS hn_last_ppn,
    hit.a5 AS hargajual_last,
    hit.hn_min,
    hit.b1 AS hn_min_margin,
    hit.b2 AS hn_min_diskon,
    hit.b3 AS hn_min_margin_diskon,
    hit.b4 AS hn_min_ppn,
    hit.b5 AS hargajual_min,
    hit.hn_max,
    hit.c1 AS hn_max_margin,
    hit.c2 AS hn_max_diskon,
    hit.c3 AS hn_max_margin_diskon,
    hit.c4 AS hn_max_ppn,
    hit.c5 AS hargajual_max,
    hit.hn_avg,
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
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.hn_max
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.hn_min
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.hn_avg
            ELSE hit.hn_last
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
        END AS hn_ppn
   FROM ( SELECT
                CASE
                    WHEN stokobatalkes_t.stokobatalkesasal_id IS NULL THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END AS id_stok,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.tglkadaluarsa,
            obatalkes_m.obatalkes_nama,
            stokobatalkes_t.satuankecil_id,
            satuan_kecil.satuanunit_nama AS s_kecil,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            stokobatalkes_r.periodestokobat_id,
            periodestokobat_m.tglperiodestok_awal,
            periodestokobat_m.tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            stokobatalkes_t.nobatch,
            obatalkes_m.harganetto,
            obatalkes_m.hargaterakhir AS hn_last,
            obatalkes_m.hargaminimum AS hn_min,
            obatalkes_m.hargamaksimum AS hn_max,
            obatalkes_m.hargaratarata AS hn_avg,
            konfigfarmasi_k.persenppn AS ppn,
            konfigfarmasi_k.persenmargin AS margin,
            konfigfarmasi_k.persen_diskon AS disc,
            konfigfarmasi_k.hargaygdigunakan,
            obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin / 100::double precision AS a1,
            (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a2,
            obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a3,
            (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a4,
            obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a5,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin / 100::double precision AS b1,
            (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b2,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b3,
            (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b4,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b5,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin / 100::double precision AS c1,
            (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c2,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c3,
            (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c4,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c5,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin / 100::double precision AS d1,
            (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d2,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d3,
            (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d4,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d5
           FROM stokobatalkes_t
             JOIN obatalkes_m ON stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN formstokopname_t ON stokobatalkes_t.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes_t.ruangan_id = formstokopname_t.ruangan_id
             JOIN stokobatalkes_r ON stokobatalkes_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND stokobatalkes_t.ruangan_id = stokobatalkes_r.ruangan_id
             LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
             LEFT JOIN satuanunit_m satuan_kecil ON stokobatalkes_t.satuankecil_id = satuan_kecil.satuanunit_id
             JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
          WHERE stokobatalkes_t.stokoa_aktif = true AND stokobatalkes_r.is_periode = true) hit
  GROUP BY hit.obatalkes_id, hit.obatalkes_nama, hit.tglkadaluarsa, hit.instalasi_nama, hit.ruangan_nama, hit.harganetto, hit.periodestokobat_id, hit.tglperiodestok_awal, hit.tglperiodestok_akhir, hit.ruangan_id, hit.instalasi_id, hit.id_stok, hit.s_kecil, hit.nobatch, hit.satuankecil_id, hit.margin, hit.ppn, hit.disc, hit.hn_last, hit.a1, hit.a2, hit.a3, hit.a4, hit.a5, hit.hn_min, hit.b1, hit.b2, hit.b3, hit.b4, hit.b5, hit.hn_max, hit.c1, hit.c2, hit.c3, hit.c4, hit.c5, hit.hn_avg, hit.d1, hit.d2, hit.d3, hit.d4, hit.d5,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c5
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b5
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d5
            ELSE hit.a5
        END,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.hn_max
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.hn_min
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.hn_avg
            ELSE hit.hn_last
        END,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c1
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b1
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d1
            ELSE hit.a1
        END,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c2
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b2
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d2
            ELSE hit.a2
        END,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c4
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b4
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d4
            ELSE hit.a4
        END;

                    ");
        
        $this->execute('
         ALTER TABLE infoobatalkesexpired_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190408_035643_infoobatalkesexpired_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_035643_infoobatalkesexpired_v_update cannot be reverted.\n";

        return false;
    }
    */
}
