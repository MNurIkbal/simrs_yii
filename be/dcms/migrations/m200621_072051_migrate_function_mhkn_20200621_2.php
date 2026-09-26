<?php

use yii\db\Migration;

/**
 * Class m200621_072051_migrate_function_mhkn_20200621_2
 */
class m200621_072051_migrate_function_mhkn_20200621_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE OR REPLACE FUNCTION "public"."infostokobatalkes_fnv"("xpenjamin_id" int4=0, "xkelaspelayan_id" int4=0, "xjenisobatalkes_id" int4=0)
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
                        
                        IF(xjenisobatalkes_id = 0)
                        THEN
                            SELECT defaultjenisobat_id INTO xjenisobatalkes_id
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
                fgetpersenmargin_id(hit.harganetto, xpenjamin_id) AS margin_id,
                hit.e_r,
                hit.e_rc
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
                  obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision AS a1,
                  (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a2,
                  obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a3,
                  (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a4,
                  obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a5,
                  obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision AS b1,
                  (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision AS b2,
                  obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 
                  100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 
                  100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b3,
                  (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - 
                  (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b4,
                  obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - 
                  (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * 
                  fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * 
                  fgetpersenmargin(obatalkes_m.hargaminimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * 
                  konfigfarmasi_k.persenppn / 100::double precision AS b5,
                  obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision AS c1,
                  (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision AS c2,
                  obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - 
                  (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision AS c3,
                  (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - 
                  (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c4,
                  obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - 
                  (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * 
                  fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * 
                  fgetpersenmargin(obatalkes_m.hargamaksimum, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * 
                  konfigfarmasi_k.persenppn / 100::double precision AS c5,
                  obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision AS d1,
                  (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision AS d2,
                  obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - 
                  (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision AS d3,
                  (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - 
                  (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d4,
                  obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - 
                  (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * 
                  fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * 
                  fgetpersenmargin(obatalkes_m.hargaratarata, xpenjamin_id, xkelaspelayan_id, xjenisobatalkes_id) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * 
                  konfigfarmasi_k.persenppn / 100::double precision AS d5,
                  obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) / 100::double precision - 
                  (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.harganetto + obatalkes_m.harganetto * 
                  fgetpersenmargin(obatalkes_m.harganetto, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * 
                  fgetpersenmargin(obatalkes_m.harganetto, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * 
                  konfigfarmasi_k.persenppn / 100::double precision AS harga_jual,
                  obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) / 100::double precision AS harga_margin,
                  (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) / 100::double precision) * 
                  konfigfarmasi_k.persen_diskon / 100::double precision AS harga_diskon,
                  (obatalkes_m.harganetto + obatalkes_m.harganetto * 
                  fgetpersenmargin(obatalkes_m.harganetto, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * 
                  fgetpersenmargin(obatalkes_m.harganetto, xpenjamin_id, xkelaspelayan_id, obatalkes_m.jenisobatalkes_id) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * 
                  konfigfarmasi_k.persenppn / 100::double precision AS harga_ppn,
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
                WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false AND stokobatalkes_r.is_periode = true) hit;
            END; $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;');

        $this->execute('CREATE OR REPLACE FUNCTION "public"."tarifkomponenrs_fn"("xruangan_id" int4=0, "xpenjamin_id" int4=0, "xkelaspelayanan_id" int4=0)
  RETURNS TABLE("jenis" text, "tariftindakan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "instalasi_id" int4, "instalasi_nama" varchar, "ruanganpaket_id" int4, "ruanganpaket_nama" varchar, "perdatarif_id" int4, "perdanama_sk" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "kelompoktindakan_id" int4, "kelompoktindakan_nama" varchar, "kategoritindakan_id" int4, "kategoritindakan_nama" varchar, "daftartindakan_id" int4, "daftartindakan_nama" varchar, "tipepaket_id" int4, "tipepaket_nama" varchar, "komponentarif_id" int4, "komponentarif_nama" varchar, "harga_tariftindakan" numeric, "persencyto_tindakan" numeric, "persendiskon_tindakan" numeric, "is_default" bool, "is_akomodasi" bool, "carabayar_id" int4, "is_konsultasi" bool) AS $BODY$ 
DECLARE vpenjamin_id int4;
BEGIN
    IF(xruangan_id = 0)
    THEN
        SELECT default_ruangan INTO xruangan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;
    
    IF(xkelaspelayanan_id = 0)
    THEN
        SELECT default_kelas INTO xkelaspelayanan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;
    
    SELECT default_penjamin INTO vpenjamin_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xpenjamin_id = vpenjamin_id)
    THEN
        xpenjamin_id := 0;
    END IF;
    
    RETURN QUERY 
    SELECT *FROM (
        SELECT \'tindakan\'::text AS jenis,
            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            tindakanruangan_mp.ruangan_id,
            r_tindakan.ruangan_nama,
            r_tindakan.instalasi_id,
            ins_tindakan.instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
            perdatarif_m.perdanama_sk,
            tariftindakan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            daftartindakan_m.kategoritindakan_id,
            kategoritindakan_m.kategoritindakan_nama,
            COALESCE( tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id ,
            daftartindakan_m.daftartindakan_nama,
            NULL::integer AS tipepaket_id,
            NULL::character varying AS tipepaket_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
            tindakanruangan_mp.is_default,
            daftartindakan_m.is_akomodasi,
            penjamin_m.carabayar_id,
            daftartindakan_m.is_konsultasi
         FROM tariftindakan_m
             JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
             JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
             JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
             JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
             LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id           
             LEFT JOIN (
                    SELECT tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        tariftindakan_m.penjamin_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.perdatarif_id,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                    AND perdatarif_m.is_active = true 
                    AND tariftindakan_m.is_deleted = false 
                    AND tariftindakan_m.is_active = true 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = xpenjamin_id
             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            WHERE tindakanruangan_mp.is_deleted = false 
            AND komponentarif_m.is_deleted IS FALSE
            AND perdatarif_m.is_active = true 
            AND tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NULL
            AND tariftindakan_m.komponentarif_id <> 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
    UNION ALL
     SELECT \'paket\'::text AS jenis,
            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            paketruangan_mp.ruangan_id,
            r_paket.ruangan_nama,
            r_paket.instalasi_id,
            ins_paket.instalasi_nama,
            paketruangan_mp.ruangan_id AS ruanganpaket_id,
            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
            perdatarif_m.perdanama_sk,
            tariftindakan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            NULL::integer AS kelompoktindakan_id,
            NULL::character varying AS kelompoktindakan_nama,
            NULL::integer AS kategoritindakan_id,
            NULL::character varying AS kategoritindakan_nama,
            NULL::integer AS daftartindakan_id,
            NULL::character varying AS daftartindakan_nama,
            tariftindakan_m.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
            paketruangan_mp.is_default,
            NULL::boolean AS is_akomodasi,
            penjamin_m.carabayar_id,
            false AS is_konsultasi
         FROM tariftindakan_m
             JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
             JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
             JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
             JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
             LEFT JOIN (
                    SELECT tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        tariftindakan_m.penjamin_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.perdatarif_id,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                    AND perdatarif_m.is_active = true 
                    AND tariftindakan_m.is_deleted = false 
                    AND tariftindakan_m.is_active = true 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = xpenjamin_id
             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
        WHERE paketruangan_mp.is_deleted = false
        AND komponentarif_m.is_deleted IS FALSE 
        AND perdatarif_m.is_active = true 
        AND tariftindakan_m.is_deleted = false 
        AND tariftindakan_m.is_active = true 
        AND tariftindakan_m.tarifparent_id IS NULL
        AND tariftindakan_m.komponentarif_id <> 6
        AND tariftindakan_m.penjamin_id = vpenjamin_id
    UNION ALL
     SELECT \'paket\'::text AS jenis,
            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            paketruangan_mp.ruangan_id,
            r_paket.ruangan_nama,
            r_paket.instalasi_id,
            ins_paket.instalasi_nama,
            paketruangan_mp.ruangan_id AS ruanganpaket_id,
            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
            perdatarif_m.perdanama_sk,
            tariftindakan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            NULL::integer AS kelompoktindakan_id,
            NULL::character varying AS kelompoktindakan_nama,
            NULL::integer AS kategoritindakan_id,
            NULL::character varying AS kategoritindakan_nama,
            NULL::integer AS daftartindakan_id,
            NULL::character varying AS daftartindakan_nama,
            tariftindakan_m.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
            paketruangan_mp.is_default,
            NULL::boolean AS is_akomodasi,
            penjamin_m.carabayar_id,
            false AS is_konsultasi
         FROM tariftindakan_m
             JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
             JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
             JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
             JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
             LEFT JOIN (
                    SELECT tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        tariftindakan_m.penjamin_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.perdatarif_id,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                    AND perdatarif_m.is_active = true 
                    AND tariftindakan_m.is_deleted = false 
                    AND tariftindakan_m.is_active = true 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = xpenjamin_id
             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
        WHERE paketruangan_mp.is_deleted = false  
        AND komponentarif_m.is_deleted IS FALSE
        AND perdatarif_m.is_active = true 
        AND tariftindakan_m.is_deleted = false 
        AND tariftindakan_m.is_active = true 
        AND tariftindakan_m.tarifparent_id IS NOT NULL
        AND tariftindakan_m.komponentarif_id <> 6
        AND tariftindakan_m.penjamin_id = vpenjamin_id
    ) AS x
    WHERE x.ruangan_id = xruangan_id
    AND x.kelaspelayanan_id = xkelaspelayanan_id;
END; 
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;');

        $this->execute('CREATE OR REPLACE FUNCTION "public"."tariftotalrs_fn"("xruangan_id" int4=0, "xpenjamin_id" int4=0, "xkelaspelayanan_id" int4=0)
  RETURNS TABLE("jenis" text, "tariftindakan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "instalasi_id" int4, "instalasi_nama" varchar, "ruanganpaket_id" int4, "ruanganpaket_nama" varchar, "perdatarif_id" int4, "perdanama_sk" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "kelompoktindakan_id" int4, "kelompoktindakan_nama" varchar, "kategoritindakan_id" int4, "kategoritindakan_nama" varchar, "daftartindakan_id" int4, "daftartindakan_nama" varchar, "tipepaket_id" int4, "tipepaket_nama" varchar, "komponentarif_id" int4, "komponentarif_nama" varchar, "harga_tariftindakan" numeric, "persencyto_tindakan" numeric, "persendiskon_tindakan" numeric, "is_default" bool, "is_akomodasi" bool, "carabayar_id" int4, "is_konsultasi" bool) AS $BODY$ 
DECLARE vpenjamin_id int4;
BEGIN
    IF(xruangan_id = 0)
    THEN
        SELECT default_ruangan INTO xruangan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;
    
    IF(xkelaspelayanan_id = 0)
    THEN
        SELECT default_kelas INTO xkelaspelayanan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;
    
    SELECT default_penjamin INTO vpenjamin_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xpenjamin_id = vpenjamin_id)
    THEN
        xpenjamin_id := 0;
    END IF;
    
    RETURN QUERY 
    SELECT *FROM (
        SELECT \'tindakan\'::text AS jenis,
            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            tindakanruangan_mp.ruangan_id,
            r_tindakan.ruangan_nama,
            r_tindakan.instalasi_id,
            ins_tindakan.instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
            perdatarif_m.perdanama_sk,
            tariftindakan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            daftartindakan_m.kategoritindakan_id,
            kategoritindakan_m.kategoritindakan_nama,
            COALESCE( tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id ,
            daftartindakan_m.daftartindakan_nama,
            NULL::integer AS tipepaket_id,
            NULL::character varying AS tipepaket_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
            tindakanruangan_mp.is_default,
            daftartindakan_m.is_akomodasi,
            penjamin_m.carabayar_id,
            daftartindakan_m.is_konsultasi
         FROM tariftindakan_m
             JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
             JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
             JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
             JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
             LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id           
             LEFT JOIN (
                    SELECT tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        tariftindakan_m.penjamin_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.perdatarif_id,
                        penjamin_m.penjamin_nama
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                    AND perdatarif_m.is_active = true 
                    AND tariftindakan_m.is_deleted = false 
                    AND tariftindakan_m.is_active = true 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = xpenjamin_id
             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id
            WHERE tindakanruangan_mp.is_deleted = false 
            AND komponentarif_m.is_deleted IS FALSE
            AND perdatarif_m.is_active = true 
            AND tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NULL
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
    UNION ALL
     SELECT \'paket\'::text AS jenis,
            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            paketruangan_mp.ruangan_id,
            r_paket.ruangan_nama,
            r_paket.instalasi_id,
            ins_paket.instalasi_nama,
            paketruangan_mp.ruangan_id AS ruanganpaket_id,
            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
            perdatarif_m.perdanama_sk,
            tariftindakan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            NULL::integer AS kelompoktindakan_id,
            NULL::character varying AS kelompoktindakan_nama,
            NULL::integer AS kategoritindakan_id,
            NULL::character varying AS kategoritindakan_nama,
            NULL::integer AS daftartindakan_id,
            NULL::character varying AS daftartindakan_nama,
            tariftindakan_m.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
            paketruangan_mp.is_default,
            NULL::boolean AS is_akomodasi,
            penjamin_m.carabayar_id,
            false AS is_konsultasi
         FROM tariftindakan_m
             JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
             JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
             JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
             JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
             LEFT JOIN (
                    SELECT tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        tariftindakan_m.penjamin_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.perdatarif_id,
                        penjamin_m.penjamin_nama
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                    AND perdatarif_m.is_active = true 
                    AND tariftindakan_m.is_deleted = false 
                    AND tariftindakan_m.is_active = true 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = xpenjamin_id
             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id
        WHERE paketruangan_mp.is_deleted = false
        AND komponentarif_m.is_deleted IS FALSE 
        AND perdatarif_m.is_active = true 
        AND tariftindakan_m.is_deleted = false 
        AND tariftindakan_m.is_active = true 
        AND tariftindakan_m.tarifparent_id IS NULL
        AND tariftindakan_m.komponentarif_id = 6
        AND tariftindakan_m.penjamin_id = vpenjamin_id
    UNION ALL
     SELECT \'paket\'::text AS jenis,
            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            paketruangan_mp.ruangan_id,
            r_paket.ruangan_nama,
            r_paket.instalasi_id,
            ins_paket.instalasi_nama,
            paketruangan_mp.ruangan_id AS ruanganpaket_id,
            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
            perdatarif_m.perdanama_sk,
            tariftindakan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            NULL::integer AS kelompoktindakan_id,
            NULL::character varying AS kelompoktindakan_nama,
            NULL::integer AS kategoritindakan_id,
            NULL::character varying AS kategoritindakan_nama,
            NULL::integer AS daftartindakan_id,
            NULL::character varying AS daftartindakan_nama,
            tariftindakan_m.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
            paketruangan_mp.is_default,
            NULL::boolean AS is_akomodasi,
            penjamin_m.carabayar_id,
            false AS is_konsultasi
         FROM tariftindakan_m
             JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
             JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
             JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
             JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
             LEFT JOIN (
                    SELECT tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        tariftindakan_m.penjamin_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.perdatarif_id,
                        penjamin_m.penjamin_nama
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                    AND perdatarif_m.is_active = true 
                    AND tariftindakan_m.is_deleted = false 
                    AND tariftindakan_m.is_active = true 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = xpenjamin_id
             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id
        WHERE paketruangan_mp.is_deleted = false 
        AND komponentarif_m.is_deleted IS FALSE
        AND perdatarif_m.is_active = true 
        AND tariftindakan_m.is_deleted = false 
        AND tariftindakan_m.is_active = true 
        AND tariftindakan_m.tarifparent_id IS NOT NULL
        AND tariftindakan_m.komponentarif_id = 6
        AND tariftindakan_m.penjamin_id = vpenjamin_id
    ) AS x
    WHERE x.ruangan_id = xruangan_id
    AND x.kelaspelayanan_id = xkelaspelayanan_id;
END; 
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;');
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200621_072051_migrate_function_mhkn_20200621_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200621_072051_migrate_function_mhkn_20200621_2 cannot be reverted.\n";

        return false;
    }
    */
}
