<?php

use yii\db\Migration;

/**
 * Class m190923_034332_stokobatalkes_r_tipedata
 */
class m190923_034332_stokobatalkes_r_tipedata extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    /*laporanformulirstokopname_v*/
     $this->execute('DROP VIEW if exists public.laporanformulirstokopname_v;');

    /*infostokobatalkes_v*/
     $this->execute('DROP VIEW if exists public.infostokobatalkes_v;');

     /*infostokobatdetail_v*/
     $this->execute('DROP VIEW if exists public.infostokobatdetail_v;');

    /*infoobatalkesexpired_v*/
     $this->execute('DROP VIEW if exists public.infoobatalkesexpired_v;');

    /*laporanobatalkesexpired_v*/
     $this->execute('DROP VIEW if exists public.laporanobatalkesexpired_v;');

    /*laporanstokobatalkes_v*/
     $this->execute('DROP VIEW if exists public.laporanstokobatalkes_v;');

       /*rekomendasiorderobat_v*/
     $this->execute('DROP VIEW if exists public.rekomendasiorderobat_v;');


     /*stokobatalkes_r*/
     $this->execute('ALTER TABLE "public"."stokobatalkes_r" 
  ALTER COLUMN "qty_awal" TYPE float8 USING "qty_awal"::float8,
  ALTER COLUMN "qty_masuk" TYPE float8 USING "qty_masuk"::float8,
  ALTER COLUMN "qty_keluar" TYPE float8 USING "qty_keluar"::float8,
  ALTER COLUMN "qty_sisa" TYPE float8 USING "qty_sisa"::float8,
  ALTER COLUMN "qty_tersedia" TYPE float8 USING "qty_tersedia"::float8,
  ALTER COLUMN "qty_dipesan" TYPE float8 USING "qty_dipesan"::float8,
  ALTER COLUMN "last_modified_date" DROP NOT NULL,
  ALTER COLUMN "last_modified_date" DROP DEFAULT;');

    /*laporanformulirstokopname_v*/
     $this->execute("
        CREATE OR REPLACE VIEW public.laporanformulirstokopname_v AS 
 SELECT formulirstokopname_t.formulirstokopname_id,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    formulirstokopname_t.ruangan_id,
    formulirstokopname_t.totalharga AS harganetto_sistem,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    formstokopname_t.formulirstokopname_id AS formulirstokopname_id1,
    stokobatalkes_r.periodestokobat_id,
    periodestokobat_m.tglperiodestok_awal,
    periodestokobat_m.tglperiodestok_akhir
   FROM formulirstokopname_t
     JOIN ruangan_m ON formulirstokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN formstokopname_t ON formulirstokopname_t.formulirstokopname_id = formstokopname_t.formulirstokopname_id
     JOIN stokobatalkes_r ON formstokopname_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND formstokopname_t.ruangan_id = stokobatalkes_r.ruangan_id
     JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
  WHERE formulirstokopname_t.is_active = true AND formulirstokopname_t.is_deleted = false
  GROUP BY formulirstokopname_t.formulirstokopname_id, formulirstokopname_t.tglformulir, formulirstokopname_t.noformulir, formulirstokopname_t.ruangan_id, formulirstokopname_t.totalharga, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, formstokopname_t.formulirstokopname_id, stokobatalkes_r.periodestokobat_id, periodestokobat_m.tglperiodestok_awal, periodestokobat_m.tglperiodestok_akhir;
");

     $this->execute('ALTER TABLE public.laporanformulirstokopname_v
  OWNER TO postgres;');

    /*infostokobatalkes_v*/
     $this->execute("
        CREATE OR REPLACE VIEW public.infostokobatalkes_v AS 
 SELECT hit.periodestok_id,
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
        END AS hn_ppn
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
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d5
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
");

     $this->execute('ALTER TABLE public.infostokobatalkes_v
  OWNER TO postgres;');

     /*infostokobatdetail_v*/
     $this->execute("
        CREATE OR REPLACE VIEW public.infostokobatdetail_v AS 
 SELECT proses.obatalkes_id,
    sum(proses.qtystok_in - proses.qtystok_out) AS stok_sistem,
    proses.obatalkes_nama,
    proses.nobatch,
    proses.tglkadaluarsa,
    proses.harganetto,
    proses.instalasi_nama,
    proses.ruangan_nama,
    proses.periodestokobat_id,
    proses.tglperiodestok_awal AS tglperiodeposting_awal,
    proses.tglperiodestok_akhir AS tglperiodeposting_akhir,
    proses.ruangan_id,
    proses.instalasi_id,
    proses.sop_obatalkes_id,
    proses.periodestok_nama
   FROM ( SELECT
                CASE
                    WHEN stokobatalkes_t.stokobatalkesasal_id IS NULL THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END AS id_stok,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.nobatch,
            stokobatalkes_t.tglkadaluarsa,
            obatalkes_m.obatalkes_nama,
            obatalkes_m.harganetto AS harganetto2,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            stokobatalkes_r.periodestokobat_id,
            periodestokobat_m.tglperiodestok_awal,
            periodestokobat_m.tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            formstokopname_t.obatalkes_id AS sop_obatalkes_id,
            formstokopname_t.stokopnamedetail_id AS sop_stokopnamedetail_id,
            periodestokobat_m.periodestok_nama,
            konfigfarmasi_k.hargaygdigunakan,
            obatalkes_m.hargamaksimum,
            obatalkes_m.hargaminimum,
            obatalkes_m.hargaratarata,
                CASE
                    WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MAX'::text THEN obatalkes_m.hargamaksimum
                    WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MIN'::text THEN obatalkes_m.hargaminimum
                    WHEN konfigfarmasi_k.hargaygdigunakan::text = 'AVG'::text THEN obatalkes_m.hargaratarata
                    ELSE obatalkes_m.harganetto
                END AS harganetto
           FROM stokobatalkes_t
             JOIN obatalkes_m ON stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT formstokopname_t_1.formstokopname_id,
                    formstokopname_t_1.stokopnamedetail_id,
                    formstokopname_t_1.obatalkes_id,
                    formstokopname_t_1.formulirstokopname_id,
                    formstokopname_t_1.volume_stok,
                    formstokopname_t_1.periodestok_id,
                    formstokopname_t_1.ruangan_id,
                    formstokopname_t_1.additional_data,
                    formstokopname_t_1.created_date,
                    formstokopname_t_1.created_by,
                    formstokopname_t_1.modified_count,
                    formstokopname_t_1.last_modified_date,
                    formstokopname_t_1.last_modified_by,
                    formstokopname_t_1.is_deleted,
                    formstokopname_t_1.is_active,
                    formstokopname_t_1.deleted_date,
                    formstokopname_t_1.deleted_by,
                    formstokopname_t_1.nobatch,
                    formstokopname_t_1.stokobatalkes_id,
                    formstokopname_t_1.tglkadaluarsa
                   FROM formstokopname_t formstokopname_t_1
                  WHERE formstokopname_t_1.stokopnamedetail_id IS NULL) formstokopname_t ON stokobatalkes_t.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes_t.ruangan_id = formstokopname_t.ruangan_id
             JOIN stokobatalkes_r ON stokobatalkes_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND stokobatalkes_t.ruangan_id = stokobatalkes_r.ruangan_id AND stokobatalkes_r.is_periode = true
             LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
             JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
          WHERE stokobatalkes_t.stokoa_aktif = true AND formstokopname_t.formstokopname_id IS NULL) proses
  GROUP BY proses.obatalkes_id, proses.obatalkes_nama, proses.nobatch, proses.tglkadaluarsa, proses.instalasi_nama, proses.ruangan_nama, proses.harganetto, proses.periodestokobat_id, proses.tglperiodestok_awal, proses.tglperiodestok_akhir, proses.ruangan_id, proses.instalasi_id, proses.sop_obatalkes_id, proses.periodestok_nama;
");

     $this->execute('ALTER TABLE public.infostokobatdetail_v
  OWNER TO postgres;');

    /*infoobatalkesexpired_v*/
     $this->execute("
        CREATE OR REPLACE VIEW public.infoobatalkesexpired_v AS 
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
  GROUP BY hit.obatalkes_id, hit.obatalkes_nama, hit.tglkadaluarsa, hit.instalasi_nama, hit.ruangan_nama, hit.harganetto, hit.periodestokobat_id, hit.tglperiodestok_awal, hit.tglperiodestok_akhir, hit.ruangan_id, hit.instalasi_id, hit.id_stok, hit.s_kecil, hit.nobatch, hit.satuankecil_id, hit.margin, hit.ppn, hit.disc, hit.hn_last, hit.a1, hit.a2, hit.a3, hit.a4, hit.a5, hit.hn_min, hit.b1, hit.b2, hit.b3, hit.b4, hit.b5, hit.hn_max, hit.c1, hit.c2, hit.c3, hit.c4, hit.c5, hit.hn_avg, hit.d1, hit.d2, hit.d3, hit.d4, hit.d5, (
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c5
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b5
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d5
            ELSE hit.a5
        END), (
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.hn_max
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.hn_min
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.hn_avg
            ELSE hit.hn_last
        END), (
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c1
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b1
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d1
            ELSE hit.a1
        END), (
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c2
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b2
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d2
            ELSE hit.a2
        END), (
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c4
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b4
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d4
            ELSE hit.a4
        END);");

     $this->execute('ALTER TABLE public.infoobatalkesexpired_v
  OWNER TO postgres;
');

    /*laporanobatalkesexpired_v*/
     $this->execute("
CREATE OR REPLACE VIEW public.laporanobatalkesexpired_v AS 
 SELECT proses.obatalkes_id,
    sum(proses.qtystok_in - proses.qtystok_out) AS stok,
    proses.obatalkes_nama,
    proses.s_kecil AS satuan_kecil,
    proses.tglkadaluarsa,
    proses.harganetto,
    proses.harganetto * sum(proses.qtystok_in - proses.qtystok_out) AS jumlah_harganetto,
    proses.instalasi_nama,
    proses.ruangan_nama,
    proses.periodestokobat_id,
    proses.tglperiodestok_awal AS tglperiodeposting_awal,
    proses.tglperiodestok_akhir AS tglperiodeposting_akhir,
    proses.ruangan_id,
    proses.instalasi_id,
    proses.id_stok
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
            satuan_kecil.satuanunit_nama AS s_kecil,
            obatalkes_m.harganetto,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            stokobatalkes_r.periodestokobat_id,
            periodestokobat_m.tglperiodestok_awal,
            periodestokobat_m.tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id
           FROM stokobatalkes_t
             JOIN obatalkes_m ON stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN formstokopname_t ON stokobatalkes_t.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes_t.ruangan_id = formstokopname_t.ruangan_id
             JOIN stokobatalkes_r ON stokobatalkes_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND stokobatalkes_t.ruangan_id = stokobatalkes_r.ruangan_id
             LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
             JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
          WHERE stokobatalkes_t.stokoa_aktif = true) proses
  GROUP BY proses.obatalkes_id, proses.obatalkes_nama, proses.tglkadaluarsa, proses.instalasi_nama, proses.ruangan_nama, proses.harganetto, proses.periodestokobat_id, proses.tglperiodestok_awal, proses.tglperiodestok_akhir, proses.ruangan_id, proses.instalasi_id, proses.id_stok, proses.s_kecil;
");

     $this->execute('ALTER TABLE public.laporanobatalkesexpired_v
  OWNER TO postgres;');

    /*laporanstokobatalkes_v*/
     $this->execute("
        CREATE OR REPLACE VIEW public.laporanstokobatalkes_v AS 
 SELECT periodestokobat_m.periodestokobat_id AS periodestok_id,
    periodestokobat_m.periodestok_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    stokobatalkes_r.ruangan_id,
    ruangan_m.ruangan_nama,
    stokobatalkes_r.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.hargajual,
    obatalkes_m.hargajual * obatalkes_m.ppn_persen / 100::double precision AS ppn,
    stokobatalkes_r.qty_masuk,
    stokobatalkes_r.qty_keluar,
    stokobatalkes_r.qty_dipesan,
    stokobatalkes_r.qty_tersedia,
    stokobatalkes_r.qty_sisa AS qty_stok,
    periodestokobat_m.tglperiodestok_awal,
    periodestokobat_m.tglperiodestok_akhir
   FROM stokobatalkes_r
     JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
  WHERE stokobatalkes_r.is_active = true AND stokobatalkes_r.is_deleted = false AND stokobatalkes_r.is_periode = true;
");

     $this->execute('ALTER TABLE public.laporanstokobatalkes_v
  OWNER TO postgres;');

       /*rekomendasiorderobat_v*/
     $this->execute("
        CREATE OR REPLACE VIEW public.rekomendasiorderobat_v AS 
 SELECT periodestokobat_m.periodestokobat_id AS periodestok_id,
    periodestokobat_m.periodestok_nama,
    instalasi_m.instalasi_nama,
    stokobatalkes_r.ruangan_id,
    ruangan_m.ruangan_nama,
    stokobatalkes_r.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.nilai_ro,
    stokobatalkes_r.qty_tersedia AS sisa_stok,
    stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision AS stok,
    obatalkes_m.nilai_ro::double precision - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision) AS ro_stok,
        CASE
            WHEN (obatalkes_m.nilai_ro::double precision - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision)) = obatalkes_m.min_order::double precision THEN obatalkes_m.min_order::double precision
            WHEN (obatalkes_m.nilai_ro::double precision - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision)) > obatalkes_m.max_order::double precision AND obatalkes_m.max_order = 0 THEN obatalkes_m.nilai_ro::double precision - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision)
            WHEN obatalkes_m.min_order = 0 AND obatalkes_m.max_order = 0 THEN obatalkes_m.nilai_ro::double precision - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision)
            WHEN (obatalkes_m.nilai_ro::double precision - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision)) < obatalkes_m.min_order::double precision THEN obatalkes_m.min_order::double precision
            WHEN (obatalkes_m.nilai_ro::double precision - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision)) > obatalkes_m.max_order::double precision THEN obatalkes_m.max_order::double precision
            WHEN (obatalkes_m.nilai_ro::double precision - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision)) > obatalkes_m.min_order::double precision THEN obatalkes_m.nilai_ro::double precision - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision)
            WHEN obatalkes_m.min_order = 0 THEN obatalkes_m.nilai_ro::double precision - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision)
            ELSE 0::double precision
        END AS rekomendasi,
    obatalkes_m.min_order,
    obatalkes_m.max_order,
    obatalkes_m.on_ro,
    obatalkes_m.on_po
   FROM stokobatalkes_r
     JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
  WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false AND stokobatalkes_r.is_periode = true AND stokobatalkes_r.ruangan_id = 25 AND obatalkes_m.nilai_ro::double precision > (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro::double precision + obatalkes_m.on_po::double precision);
");

     $this->execute('ALTER TABLE public.rekomendasiorderobat_v
  OWNER TO postgres;
');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190923_034332_stokobatalkes_r_tipedata cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190923_034332_stokobatalkes_r_tipedata cannot be reverted.\n";

        return false;
    }
    */
}
