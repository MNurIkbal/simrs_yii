<?php

use yii\db\Migration;

/**
 * Class m200718_082640_migrate_mhkn_20200718_func
 */
class m200718_082640_migrate_mhkn_20200718_func extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE OR REPLACE FUNCTION "public"."infostokobatalkes_fn"("xpenjamin_id" int4=0, "xkelaspelayan_id" int4=0)
  RETURNS TABLE("periodestok_id" int4, "periodestok_nama" varchar, "tglperiodestok_awal" timestamp, "tglperiodestok_akhir" timestamp, "instalasi_id" int4, "instalasi_nama" varchar, "ruangan_id" int4, "ruangan_nama" varchar, "obatalkes_id" int4, "obatalkes_nama" varchar, "obatalkes_namalain" varchar, "obatalkes_kode" varchar, "qty_masuk" float8, "qty_keluar" float8, "qty_dipesan" float8, "qty_tersedia" float8, "qty_stok" float8, "nilai_ro" float8, "satuanbesar_id" int4, "satuanbesar_nama" varchar, "satuankecil_id" int4, "satuankecil_nama" varchar, "satuansedang_id" int4, "satuansedang_nama" varchar, "group_jenisobat" int4, "group_jenisobat_nama" varchar, "jenisobatalkes_id" int4, "jenisobatalkes_nama" varchar, "konfigygdigunakan" text, "hargajual" float8, "ppn" float8, "margin" float8, "disc" float8, "harganetto" float8, "hn_last_margin" float8, "hn_last_diskon" float8, "hn_last_margin_diskon" float8, "hn_last_ppn" float8, "hargajual_last" float8, "hargamaksimum" float8, "hn_max_margin" float8, "hn_max_diskon" float8, "hn_max_margin_diskon" float8, "hn_max_ppn" float8, "hargajual_max" float8, "hargaminimum" float8, "hn_min_margin" float8, "hn_min_diskon" float8, "hn_min_margin_diskon" float8, "hn_min_ppn" float8, "hargajual_min" float8, "hargaratarata" float8, "hn_avg_margin" float8, "hn_avg_diskon" float8, "hn_avg_margin_diskon" float8, "hn_avg_ppn" float8, "hargajual_avg" float8, "hargaygdipakai" float8, "harganetto_ygdipakai" float8, "harganetto_sugesstion" float8, "hn_margin" float8, "hn_diskon" float8, "hn_ppn" float8, "persen_ppn" float8, "persen_margin" float8, "persen_disc" float8, "jml_hargajual" float8, "jml_harganetto" float8, "jml_sugesstion" float8, "jml_margin" float8, "jml_discount" float8, "jml_ppn" float8, "persenmargin_id" int4, "embalase_racikan" float8, "embalase_nonracikan" float8) AS $BODY$
    
            BEGIN 
                        IF(xpenjamin_id = 0)
                        THEN
                            SELECT defaultpenjamin_id INTO xpenjamin_id
                            FROM konfigfarmasi_k
                            WHERE konfigfarmasi_id = 1;
                        END IF;
                        
                        IF(xkelaspelayan_id = 0)
                        THEN
                            SELECT defaultkelas_id INTO xkelaspelayan_id
                            FROM konfigfarmasi_k
                            WHERE konfigfarmasi_id = 1;
                        END IF;
                        
                    
              RETURN QUERY 
              SELECT 
                xpenjamin_id AS periodestok_id,
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
                hit.qty_masuk::float8,
                hit.qty_keluar::float8,
                hit.qty_dipesan::float8,
                hit.qty_tersedia::float8,
                hit.qty_stok::float8,
                hit.nilai_ro::float8,
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
                hit.harga_jual::float8 AS hargajual,
                hit.ppn,
                hit.margin,
                hit.disc,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.hargamaksimum
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.hargaminimum
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.hargaratarata
            --                         ELSE hit.harganetto
            --                     END AS harganetto,
                hit.harganetto::float8,
                 CASE
                   WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                   WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                   WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                   ELSE hit.a1
                 END::float8 AS hn_last_margin,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                  ELSE hit.a2
                END::float8 AS hn_last_diskon,
                hit.a3::float8 AS hn_last_margin_diskon,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                  ELSE hit.a4
                END::float8 AS hn_last_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_last,
                          hit.harga_jual::float8 AS hargajual_last,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargamaksimum,
                          hit.harga_jual::float8 AS hargamaksimum,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                  ELSE hit.a1
                END::float8 AS hn_max_margin,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                  ELSE hit.a2
                END::float8 AS hn_max_diskon,
                hit.c3::float8 AS hn_max_margin_diskon,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                  ELSE hit.a4
                END::float8 AS hn_max_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_max,
                          hit.harga_jual::float8 AS hargajual_max,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargaminimum,
                          hit.harga_jual::float8 AS hargaminimum,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                  ELSE hit.a1
                END::float8 AS hn_min_margin,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                  ELSE hit.a2
                END::float8 AS hn_min_diskon,
                hit.b3::float8 AS hn_min_margin_diskon,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                  ELSE hit.a4
                END::float8 AS hn_min_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_min,
                          hit.harga_jual::float8 AS hargajual_min,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
                  ELSE hit.a5
                END::float8 AS hargaratarata,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                  ELSE hit.a1
                END::float8 AS hn_avg_margin,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                  ELSE hit.a2
                END::float8 AS hn_avg_diskon,
                hit.d3::float8 AS hn_avg_margin_diskon,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                  ELSE hit.a4
                END::float8 AS hn_avg_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_avg,
                          hit.harga_jual::float8 AS hargajual_avg,
                hit.harga_jual::float8,
                hit.harganetto::float8, 
                CASE
                  WHEN ((hit.hargaygdigunakan)::text = \'MAX\'::text) THEN hit.hargamaksimum
                  WHEN ((hit.hargaygdigunakan)::text = \'MIN\'::text) THEN hit.hargaminimum
                  WHEN ((hit.hargaygdigunakan)::text = \'AVG\'::text) THEN hit.hargaratarata
                  ELSE hit.hargaterakhir
                END::float8 AS sugesstion,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                  ELSE hit.a1
                END::float8 AS hn_margin,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                  ELSE hit.a2
                END::float8 AS hn_diskon,
                CASE
                  WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                  WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                  WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                  ELSE hit.a4
                END::float8 AS hn_ppn,
                hit.ppn,
                fgetpersenmargin(hit.harganetto, xpenjamin_id,hit.jenisobatalkes_id) AS margin,
                hit.disc,
                hit.harga_jual::float8,
                hit.harganetto::float8, 
                CASE
                  WHEN ((hit.hargaygdigunakan)::text = \'MAX\'::text) THEN hit.hargamaksimum
                  WHEN ((hit.hargaygdigunakan)::text = \'MIN\'::text) THEN hit.hargaminimum
                  WHEN ((hit.hargaygdigunakan)::text = \'AVG\'::text) THEN hit.hargaratarata
                  ELSE hit.hargaterakhir
                END::float8 AS sugesstion,
            --    CASE
            --      WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
            --      WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
            --      WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
            --      ELSE hit.a1
            --    END::float8 ,
            --    CASE
            --      WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
            --      WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
            --      WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
            --      ELSE hit.a2
            --    END::float8 ,
            --    CASE
            --      WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
            --      WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
            --      WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
            --      ELSE hit.a4
            --    END::float8 ,
                hit.harga_margin::float8,
                hit.harga_diskon::float8,
                hit.harga_ppn::float8,
                fgetpersenmargin_id(hit.harganetto, xpenjamin_id, xkelaspelayan_id, hit.jenisobatalkes_id) AS margin_id,
                hit.e_r,
                hit.e_rc
              FROM ( 
                SELECT
                                    zz.periodestok_id,
                                    zz.periodestok_nama,
                                    zz.instalasi_id,
                                    zz.instalasi_nama,
                                    zz.ruangan_id,
                                    zz.ruangan_nama,
                                    zz.obatalkes_id,
                                    zz.obatalkes_namalain,
                                    zz.obatalkes_kode,
                                    zz.hargajual,
                                    zz.satuankecil_id,
                                    zz.satuanbesar_nama,
                                    zz.satuansedang_id,
                                    zz.satuansedang_nama,
                                    zz.satuankecil_nama,
                                    zz.satuanbesar_id,
                                    zz.jenisobatalkes_id,
                                    zz.jenisobatalkes_nama,
                                    zz.group_jenisobat,
                                    zz.ppn,
                                    zz.margin,
                                    zz.disc,
                                    zz.hargaygdigunakan,
                                    zz.harganetto,
                                    zz.hargamaksimum,
                                    zz.hargaminimum,
                                    zz.hargaratarata,
                                    zz.hargaterakhir,
                                    zz.qty_masuk,
                                    zz.qty_keluar,
                                    zz.qty_dipesan,
                                    zz.qty_tersedia,
                                    zz.qty_stok,
                                    zz.tglperiodestok_awal,
                                    zz.tglperiodestok_akhir,
                                    zz.obatalkes_nama,
                                    zz.nilai_ro,
                                    zz.hargaterakhir + zz.hargaterakhir * zz.pm_hlast / 100::double precision AS a1,
                                    (zz.hargaterakhir + zz.hargaterakhir * zz.pm_hlast / 100::double precision) * zz.pdisc AS a2,
                                    zz.hargaterakhir + zz.hargaterakhir * zz.pm_hlast / 100::double precision - (zz.hargaterakhir + zz.hargaterakhir * zz.pm_hlast / 100::double precision) * zz.pdisc AS a3,
                                    (zz.hargaterakhir + zz.hargaterakhir * zz.pm_hlast / 100::double precision - (zz.hargaterakhir + zz.hargaterakhir * zz.pm_hlast / 100::double precision) * zz.pdisc) * zz.pppn AS a4,
                                    zz.hargaterakhir + zz.hargaterakhir * zz.pm_hlast / 100::double precision - (zz.hargaterakhir + zz.hargaterakhir * zz.pm_hlast / 100::double precision) * zz.pdisc + (zz.hargaterakhir + zz.hargaterakhir * zz.pm_hlast / 100::double precision - (zz.hargaterakhir + zz.hargaterakhir * zz.pm_hlast / 100::double precision) * zz.pdisc) * zz.pppn AS a5,
                                    zz.hargaminimum + zz.hargaminimum * zz.pm_hmin / 100::double precision AS b1,
                                    (zz.hargaminimum + zz.hargaminimum * zz.pm_hmin / 100::double precision) * zz.pdisc AS b2,
                                    zz.hargaminimum + zz.hargaminimum * zz.pm_hmin / 100::double precision - (zz.hargaminimum + zz.hargaminimum * zz.pm_hmin / 100::double precision) * zz.pdisc AS b3,
                                    (zz.hargaminimum + zz.hargaminimum * zz.pm_hmin / 100::double precision - (zz.hargaminimum + zz.hargaminimum * zz.pm_hmin / 100::double precision) * zz.pdisc) * zz.pppn AS b4,
                                    zz.hargaminimum + zz.hargaminimum * zz.pm_hmin / 100::double precision - (zz.hargaminimum + zz.hargaminimum * zz.pm_hmin / 100::double precision) * zz.pdisc + (zz.hargaminimum + zz.hargaminimum * zz.pm_hmin / 100::double precision - (zz.hargaminimum + zz.hargaminimum * zz.pm_hmin / 100::double precision) * zz.pdisc) * zz.pppn AS b5,
                                    zz.hargamaksimum + zz.hargamaksimum * zz.pm_hmax / 100::double precision AS c1,
                                    (zz.hargamaksimum + zz.hargamaksimum * zz.pm_hmax / 100::double precision) * zz.pdisc AS c2,
                                    zz.hargamaksimum + zz.hargamaksimum * zz.pm_hmax / 100::double precision - (zz.hargamaksimum + zz.hargamaksimum * zz.pm_hmax / 100::double precision) * zz.pdisc AS c3,
                                    (zz.hargamaksimum + zz.hargamaksimum * zz.pm_hmax / 100::double precision - (zz.hargamaksimum + zz.hargamaksimum * zz.pm_hmax / 100::double precision) * zz.pdisc) * zz.pppn AS c4,
                                    zz.hargamaksimum + zz.hargamaksimum * zz.pm_hmax / 100::double precision - (zz.hargamaksimum + zz.hargamaksimum * zz.pm_hmax / 100::double precision) * zz.pdisc + (zz.hargamaksimum + zz.hargamaksimum * zz.pm_hmax / 100::double precision - (zz.hargamaksimum + zz.hargamaksimum * zz.pm_hmax / 100::double precision) * zz.pdisc) * zz.pppn AS c5,
                                    zz.hargaratarata + zz.hargaratarata * zz.pm_havg / 100::double precision AS d1,
                                    (zz.hargaratarata + zz.hargaratarata * zz.pm_havg / 100::double precision) * zz.pdisc AS d2,
                                    zz.hargaratarata + zz.hargaratarata * zz.pm_havg / 100::double precision - (zz.hargaratarata + zz.hargaratarata * zz.pm_havg / 100::double precision) * zz.pdisc AS d3,
                                    (zz.hargaratarata + zz.hargaratarata * zz.pm_havg / 100::double precision - (zz.hargaratarata + zz.hargaratarata * zz.pm_havg / 100::double precision) * zz.pdisc) * zz.pppn AS d4,
                                    zz.hargaratarata + zz.hargaratarata * zz.pm_havg / 100::double precision - (zz.hargaratarata + zz.hargaratarata * zz.pm_havg / 100::double precision) * zz.pdisc + (zz.hargaratarata + zz.hargaratarata * zz.pm_havg / 100::double precision - (zz.hargaratarata + zz.hargaratarata * zz.pm_havg / 100::double precision) * zz.pdisc) * zz.pppn AS d5,
                                    zz.harganetto + zz.harganetto * zz.pm_hnet / 100::double precision - (zz.harganetto + zz.harganetto * zz.pm_hnet / 100::double precision) * zz.pdisc + (zz.harganetto + zz.harganetto * zz.pm_hnet / 100::double precision - (zz.harganetto + zz.harganetto * zz.pm_hnet / 100::double precision) * zz.pdisc) * zz.pppn AS harga_jual,zz.harganetto * zz.pm_hnet / 100::double precision AS harga_margin,
                                    (zz.harganetto + zz.harganetto * zz.pm_hnet / 100::double precision) * zz.pdisc AS harga_diskon,
                                    (zz.harganetto + zz.harganetto * zz.pm_hnet / 100::double precision - (zz.harganetto + zz.harganetto * zz.pm_hnet / 100::double precision) * zz.pdisc) * zz.pppn AS harga_ppn,
                                    zz.e_r,
                                    zz.e_rc
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
                                        fgetpersenmargin(obatalkes_m.harganetto, xpenjamin_id, obatalkes_m.jenisobatalkes_id) AS margin,
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
                                        konfigfarmasi_k.persen_diskon / 100::double precision AS pdisc,
                                        konfigfarmasi_k.persenppn / 100::double precision as pppn,
                                        fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) AS pm_hlast,
                                        fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) AS pm_hmin,
                                        fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) AS pm_hmax,
                                        fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) AS pm_havg,
                                        fgetpersenmargin(obatalkes_m.harganetto, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) as pm_hnet,
                                        konfigfarmasi_k.embalase_racikan AS e_r,
                                        konfigfarmasi_k.embalase_nonracikan AS e_rc
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
                                    WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false AND stokobatalkes_r.is_periode = true
                                ) zz
                        ) hit;
            END; $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;');
     

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200718_082640_migrate_mhkn_20200718_func cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200718_082640_migrate_mhkn_20200718_func cannot be reverted.\n";

        return false;
    }
    */
}
