<?php

use yii\db\Migration;

/**
 * Class m210622_052000_improvment_function_obalalkes_add_param_ruangan
 */
class m210622_052000_improvment_function_obalalkes_add_param_ruangan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP FUNCTION IF EXISTS infostokobatalkes_fnr(int4, int4, int4);
        ');

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."infostokobatalkes_fnr"("xpenjamin_id" int4 DEFAULT 0, "xkelaspelayan_id" int4 DEFAULT 0, xruangan_id int4 DEFAULT 0)
            RETURNS TABLE("periodestok_id" int4, "periodestok_nama" varchar, "tglperiodestok_awal" timestamp, "tglperiodestok_akhir" timestamp, "instalasi_id" int4, "instalasi_nama" varchar, "ruangan_id" int4, "ruangan_nama" varchar, "obatalkes_id" int4, "obatalkes_nama" varchar, "obatalkes_namalain" varchar, "obatalkes_kode" varchar, "qty_masuk" float8, "qty_keluar" float8, "qty_dipesan" float8, "qty_tersedia" float8, "qty_stok" float8, "nilai_ro" float8, "satuanbesar_id" int4, "satuanbesar_nama" varchar, "satuankecil_id" int4, "satuankecil_nama" varchar, "satuansedang_id" int4, "satuansedang_nama" varchar, "group_jenisobat" int4, "group_jenisobat_nama" varchar, "jenisobatalkes_id" int4, "jenisobatalkes_nama" varchar, "konfigygdigunakan" text, "hargajual" float8, "ppn" float8, "margin" float8, "disc" float8, "harganetto" float8, "hn_last_margin" float8, "hn_last_diskon" float8, "hn_last_margin_diskon" float8, "hn_last_ppn" float8, "hargajual_last" float8, "hargamaksimum" float8, "hn_max_margin" float8, "hn_max_diskon" float8, "hn_max_margin_diskon" float8, "hn_max_ppn" float8, "hargajual_max" float8, "hargaminimum" float8, "hn_min_margin" float8, "hn_min_diskon" float8, "hn_min_margin_diskon" float8, "hn_min_ppn" float8, "hargajual_min" float8, "hargaratarata" float8, "hn_avg_margin" float8, "hn_avg_diskon" float8, "hn_avg_margin_diskon" float8, "hn_avg_ppn" float8, "hargajual_avg" float8, "hargaygdipakai" float8, "harganetto_ygdipakai" float8, "harganetto_sugesstion" float8, "hn_margin" float8, "hn_diskon" float8, "hn_ppn" float8, "persen_ppn" float8, "persen_margin" float8, "persen_disc" float8, "jml_hargajual" float8, "jml_harganetto" float8, "jml_sugesstion" float8, "jml_margin" float8, "jml_discount" float8, "jml_ppn" float8, "persenmargin_id" int4, "embalase_racikan" float8, "embalase_nonracikan" float8) AS $BODY$
            DECLARE
            vmarginkhusus_id int4;
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

            SELECT marginkhusus_id INTO vmarginkhusus_id
            FROM marginkhusus_k 
            WHERE marginkhusus_k.mulai_berlaku <=  CURRENT_DATE
            AND marginkhusus_k.is_deleted IS FALSE
            ORDER BY marginkhusus_k.mulai_berlaku DESC
            LIMIT 1;

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
                ROUND(hit.harga_jual::numeric, 2)::float8 AS hargajual,
                hit.ppn,
                hit.margin,
                hit.disc,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.hargamaksimum
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.hargaminimum
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.hargaratarata
            --                         ELSE hit.harganetto
            --                     END AS harganetto,
                ROUND(hit.harganetto::numeric, 2)::float8,
                ROUND(CASE
                     WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                     WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                     WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                     ELSE hit.a1
                 END::numeric, 2)::float8 AS hn_last_margin,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                    ELSE hit.a2
                END::numeric, 2)::float8 AS hn_last_diskon,
                ROUND(hit.a3::numeric, 2)::float8 AS hn_last_margin_diskon,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                    ELSE hit.a4
                END::numeric, 2)::float8 AS hn_last_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_last,
                ROUND(hit.harga_jual::numeric, 2)::float8 AS hargajual_last,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargamaksimum,
                ROUND(hit.harga_jual::numeric, 2)::float8 AS hargamaksimum,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                    ELSE hit.a1
                END::numeric, 2)::float8 AS hn_max_margin,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                    ELSE hit.a2
                END::numeric, 2)::float8 AS hn_max_diskon,
                ROUND(hit.c3::numeric, 2)::float8 AS hn_max_margin_diskon,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                    ELSE hit.a4
                END::numeric, 2)::float8 AS hn_max_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_max,
                ROUND(hit.harga_jual::numeric, 2)::float8 AS hargajual_max,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargaminimum,
                ROUND(hit.harga_jual::numeric, 2)::float8 AS hargaminimum,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                    ELSE hit.a1
                END::numeric, 2)::float8 AS hn_min_margin,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                    ELSE hit.a2
                END::numeric, 2)::float8 AS hn_min_diskon,
                ROUND(hit.b3::numeric, 2)::float8 AS hn_min_margin_diskon,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                    ELSE hit.a4
                END::numeric, 2)::float8 AS hn_min_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_min,
                ROUND(hit.harga_jual::numeric, 2)::float8 AS hargajual_min,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
                    ELSE hit.a5
                END::numeric, 2)::float8 AS hargaratarata,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                    ELSE hit.a1
                END::numeric, 2)::float8 AS hn_avg_margin,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                    ELSE hit.a2
                END::numeric, 2)::float8 AS hn_avg_diskon,
                ROUND(hit.d3::numeric, 2)::float8 AS hn_avg_margin_diskon,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                    ELSE hit.a4
                END::numeric, 2)::float8 AS hn_avg_ppn,
            --                     CASE
            --                         WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c5
            --                         WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b5
            --                         WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d5
            --                         ELSE hit.a5
            --                     END AS hargajual_avg,
                ROUND(hit.harga_jual::numeric, 2)::float8 AS hargajual_avg,
                ROUND(hit.harga_jual::numeric, 2)::float8,
                ROUND(hit.harganetto::numeric, 2)::float8, 
                ROUND(CASE
                    WHEN ((hit.hargaygdigunakan)::text = \'MAX\'::text) THEN hit.hargamaksimum
                    WHEN ((hit.hargaygdigunakan)::text = \'MIN\'::text) THEN hit.hargaminimum
                    WHEN ((hit.hargaygdigunakan)::text = \'AVG\'::text) THEN hit.hargaratarata
                    ELSE hit.hargaterakhir
                END::numeric, 2)::float8 AS sugesstion,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1
                    ELSE hit.a1
                END::numeric, 2)::float8 AS hn_margin,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2
                    ELSE hit.a2
                END::numeric, 2)::float8 AS hn_diskon,
                ROUND(CASE
                    WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4
                    WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4
                    WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4
                    ELSE hit.a4
                END::numeric, 2)::float8 AS hn_ppn,
                hit.ppn,
                hit.margin,
                hit.disc,
                ROUND(hit.harga_jual::numeric, 2)::float8,
                ROUND(hit.harganetto::numeric, 2)::float8, 
                ROUND(CASE
                    WHEN ((hit.hargaygdigunakan)::text = \'MAX\'::text) THEN hit.hargamaksimum
                    WHEN ((hit.hargaygdigunakan)::text = \'MIN\'::text) THEN hit.hargaminimum
                    WHEN ((hit.hargaygdigunakan)::text = \'AVG\'::text) THEN hit.hargaratarata
                    ELSE hit.hargaterakhir
                END::numeric, 2)::float8 AS sugesstion,
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
                hit.konfigmargin_id AS margin_id,
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
                    zz.e_rc,
                    zz.konfigmargin_id
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
                            pm6.konfigmargin_id,
                            CASE 
                                WHEN konfigfarmasi_k.is_marginkhusus IS TRUE
                                THEN
                                    CASE 
                                        WHEN mkd.margin IS NULL 
                                        THEN 
                                            CASE WHEN pm6.margin IS NULL 
                                                THEN 0 
                                            ELSE pm6.margin 
                                            END 
                                        ELSE mkd.margin
                                    END
                                ELSE 
                                    CASE 
                                        WHEN pm6.margin IS NULL THEN 0 
                                        ELSE pm6.margin
                                    END 
                            END AS margin,
                            CASE 
                                WHEN konfigfarmasi_k.is_marginkhusus IS TRUE
                                THEN
                                    CASE 
                                        WHEN mkd.margin IS NULL 
                                        THEN 
                                            CASE WHEN pm1.margin IS NULL 
                                                THEN 0 
                                            ELSE pm1.margin 
                                            END 
                                        ELSE 0
                                    END
                                ELSE 
                                    CASE 
                                        WHEN pm1.margin IS NULL THEN 0 
                                        ELSE pm1.margin
                                    END 
                            END AS pm_hlast,
                            CASE 
                                WHEN konfigfarmasi_k.is_marginkhusus IS TRUE
                                THEN
                                    CASE 
                                        WHEN mkd.margin IS NULL 
                                        THEN 
                                            CASE WHEN pm2.margin IS NULL 
                                                THEN 0 
                                            ELSE pm2.margin 
                                            END 
                                        ELSE mkd.margin
                                    END
                                ELSE 
                                    CASE 
                                        WHEN pm2.margin IS NULL THEN 0 
                                        ELSE pm2.margin
                                    END 
                            END AS pm_hmin,
                            CASE 
                                WHEN konfigfarmasi_k.is_marginkhusus IS TRUE
                                THEN
                                    CASE 
                                        WHEN mkd.margin IS NULL 
                                        THEN 
                                            CASE WHEN pm3.margin IS NULL 
                                                THEN 0 
                                            ELSE pm3.margin 
                                            END 
                                        ELSE 0
                                    END
                                ELSE 
                                    CASE 
                                        WHEN pm3.margin IS NULL THEN 0 
                                        ELSE pm3.margin
                                    END 
                            END AS pm_hmax,
                            CASE 
                                WHEN konfigfarmasi_k.is_marginkhusus IS TRUE
                                THEN
                                    CASE 
                                        WHEN mkd.margin IS NULL 
                                        THEN 
                                            CASE WHEN pm4.margin IS NULL 
                                                THEN 0 
                                            ELSE pm4.margin 
                                            END 
                                        ELSE mkd.margin
                                    END
                                ELSE 
                                    CASE 
                                        WHEN pm4.margin IS NULL THEN 0 
                                        ELSE pm4.margin
                                    END 
                            END AS pm_havg,
                            CASE 
                                WHEN konfigfarmasi_k.is_marginkhusus IS TRUE
                                THEN
                                    CASE 
                                        WHEN mkd.margin IS NULL 
                                        THEN 
                                            CASE WHEN pm5.margin IS NULL 
                                                THEN 0 
                                            ELSE pm5.margin 
                                            END 
                                        ELSE mkd.margin
                                    END
                                ELSE 
                                    CASE 
                                        WHEN pm5.margin IS NULL THEN 0 
                                        ELSE pm5.margin
                                    END 
                            END AS pm_hnet,
                    --     CASE WHEN pm1.margin IS NULL THEN 0 ELSE pm1.margin END AS pm_hlast,
                    --     CASE WHEN pm2.margin IS NULL THEN 0 ELSE pm2.margin END AS pm_hmin,
                    --     CASE WHEN pm3.margin IS NULL THEN 0 ELSE pm3.margin END AS pm_hmax,
                    --     CASE WHEN pm4.margin IS NULL THEN 0 ELSE pm4.margin END AS pm_havg,
                    --     CASE WHEN pm5.margin IS NULL THEN 0 ELSE pm5.margin END AS pm_hnet,    
                            konfigfarmasi_k.embalase_racikan AS e_r,
                            konfigfarmasi_k.embalase_nonracikan AS e_rc
                        FROM stokobatalkes_r
                        JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
                        JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                        JOIN obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
                        JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                        JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                        LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
                        LEFT JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
                        LEFT JOIN satuanunit_m satuan_besar ON obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id
                        LEFT JOIN satuanunit_m satuan_sedang ON obatalkes_m.satuansedang_id = satuan_sedang.satuanunit_id  
                        LEFT JOIN marginkhususdetail_k AS mkd ON ((mkd.is_deleted IS FALSE) AND (mkd.jenisobat_id = obatalkes_m.jenisobatalkes_id) AND (mkd.marginkhusus_id = vmarginkhusus_id))
                        LEFT JOIN getpersenmargin_fn(xpenjamin_id,xkelaspelayan_id) AS pm1 ON ((pm1.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id) AND (obatalkes_m.hargaterakhir BETWEEN pm1.harga_min AND pm1.harga_max))
                        
                        LEFT JOIN getpersenmargin_fn(xpenjamin_id,xkelaspelayan_id) AS pm2 ON ((pm2.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id) AND (obatalkes_m.hargaminimum BETWEEN pm2.harga_min AND pm2.harga_max))
                        LEFT JOIN getpersenmargin_fn(xpenjamin_id,xkelaspelayan_id) AS pm3 ON ((pm3.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id) AND (obatalkes_m.hargamaksimum BETWEEN pm3.harga_min AND pm3.harga_max))
                        LEFT JOIN getpersenmargin_fn(xpenjamin_id,xkelaspelayan_id) AS pm4 ON ((pm4.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id) AND (obatalkes_m.hargaratarata BETWEEN pm4.harga_min AND pm4.harga_max))
                        LEFT JOIN getpersenmargin_fn(xpenjamin_id,xkelaspelayan_id) AS pm5 ON ((pm5.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id) AND (obatalkes_m.harganetto BETWEEN pm5.harga_min AND pm5.harga_max))
                        LEFT JOIN (
                            SELECT x.penjamin_id, x.jenisobatalkes_id, x.konfigmargin_id, xk.konfigmargindetail_id, xk.harga_min, xk.harga_max, xk.margin
                            FROM (
                                SELECT DISTINCT ON (pm.penjamin_id,kmk.jenisobatalkes_id)
                                    pm.penjamin_id,kmk.jenisobatalkes_id, kmk.konfigmargin_id, kmk.tgl_berlaku
                                FROM konfigmargin_k AS kmk JOIN penjamin_m AS pm ON (kmk.groupmargin_id = pm.groupmargin_id)
                                WHERE (kmk.tgl_berlaku <= CURRENT_DATE)
                                    AND (kmk.is_deleted IS FALSE)
                                    AND (pm.penjamin_id = xpenjamin_id)
                                    AND kmk.kelaspelayanan_id = xkelaspelayan_id
                                ORDER BY pm.penjamin_id ASC,kmk.jenisobatalkes_id ASC, kmk.tgl_berlaku DESC
                            ) AS x LEFT JOIN konfigmargindetail_k AS xk ON (xk.konfigmargin_id = x.konfigmargin_id)
                            WHERE (xk.is_deleted IS FALSE) AND (xk.is_active IS TRUE)
                        ) AS pm6 ON ((pm6.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id) AND (pm6.penjamin_id = xpenjamin_id) AND (obatalkes_m.harganetto BETWEEN pm6.harga_min AND pm6.harga_max))
                        WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false AND stokobatalkes_r.is_periode = true
                        AND stokobatalkes_r.ruangan_id = xruangan_id
                ) zz
            ) hit;
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
        echo "m210622_052000_improvment_function_obalalkes_add_param_ruangan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210622_052000_improvment_function_obalalkes_add_param_ruangan cannot be reverted.\n";

        return false;
    }
    */
}
