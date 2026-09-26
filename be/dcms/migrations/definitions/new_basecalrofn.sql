CREATE OR REPLACE FUNCTION "public"."new_basecalrofn"("vpemakaianruangan" bool=false, "vbmhp" bool=false, "vmutasi" bool=false)
  RETURNS TABLE("obatalkes_id" int4, "count" int8, "max" float8, "real_max" float8, "min" float8, "avg" float8, "min_resep" float8, "jenisobatalkes_id" int4, "last_7" float8, "last_14" float8, "last_30" float8, "movingcriteria_id" int4, "move_category" varchar) AS $BODY$
DECLARE
    start_date date := current_date - interval '30 days';
    end_date date := current_date;
    last_7_date date := current_date - interval '7 days';
    last_14_date date := current_date - interval '14 days';
    gudang_farmasi_id int;
    farmasi_instalasi_id int;
BEGIN
    -- Cache lookup values to avoid repeated subqueries
    SELECT kode_id INTO gudang_farmasi_id 
    FROM lookuptransaksi_m 
    WHERE kode_transaksi = 'gudang_farmasi';
    
    SELECT kode_id INTO farmasi_instalasi_id 
    FROM lookuptransaksi_m 
    WHERE kode_transaksi = 'FARMASI';

    RETURN QUERY
    SELECT
        calc.obatalkes_id,
        calc.count,
        CASE 
            WHEN mc.criteria = 'FAST' THEN calc.max / 2 
            WHEN mc.criteria = 'MED FAST' THEN calc.max / 2
            ELSE calc.max 
        END as max,
        calc.max::double precision as real_max,
        calc.min,
        calc.avg,
        calc.min_resep,
        calc.jenisobatalkes_id::INT,
        calc.last_7,
        calc.last_14,
        calc.last_30,
        mc.movingcriteria_id::INT,
        mc.criteria AS move_category
    FROM (
        SELECT 
            count_stok.obatalkes_id,
            COUNT(count_stok.tanggal) AS count,
            MAX(count_stok.stok_out) AS max,
            MIN(count_stok.stok_out) AS min,
            SUM(count_stok.stok_out) / COUNT(count_stok.tanggal)::double precision AS avg,
            MIN(count_stok.min_resep) AS min_resep,
            om.jenisobatalkes_id,
            SUM(count_stok.last_7) AS last_7,
            SUM(count_stok.last_14) AS last_14,
            SUM(count_stok.last_30) AS last_30
        FROM ( 
            -- Optimized UNION ALL - keeping exact original logic
            SELECT 
                detail.obatalkes_id,
                detail.tanggal,
                SUM(detail.qtystok_out) AS stok_out,
                SUM(detail.qty_resep) AS min_resep,
                SUM(CASE 
                    WHEN detail.tanggal >= last_7_date 
                    THEN detail.qtystok_out 
                    ELSE 0::double precision 
                END) AS last_7,
                SUM(CASE 
                    WHEN detail.tanggal >= last_14_date 
                    THEN detail.qtystok_out 
                    ELSE 0::double precision 
                END) AS last_14,
                SUM(CASE 
                    WHEN detail.tanggal >= start_date 
                    THEN detail.qtystok_out 
                    ELSE 0::double precision 
                END) AS last_30
            FROM ( 
                -- First UNION: Resep - exact match with original
                SELECT 
                    s.stokobatalkes_id,
                    s.obatalkes_id,
                    s.tglstok_out::date AS tanggal,
                    s.qtystok_out,
                    CASE 
                        WHEN s.obatalkespasien_id IS NOT NULL 
                        AND s.tglstok_out IS NOT NULL 
                        THEN s.qtystok_out 
                        ELSE NULL::double precision 
                    END AS qty_resep,
                    NULL::double precision as qty_pemakaianruangan,
                    NULL::double precision as qty_bmhp,
                    NULL::double precision as qty_mutasi
                FROM stokobatalkes_t s
                INNER JOIN obatalkespasien_t op ON s.obatalkespasien_id = op.obatalkespasien_id
                    AND op.penjualanresep_id IS NOT NULL
                    AND op.is_deleted = false
                WHERE s.tglstok_out IS NOT NULL
                    AND (
                        s.tglstok_out >= current_date::timestamp without time zone AND s.tglstok_out <= (current_date - '30 days'::interval) 
                        OR 
                        s.tglstok_out >= (current_date - '30 days'::interval) AND s.tglstok_out <= current_date::timestamp without time zone
                    )
                
                UNION ALL
                
                -- Second UNION: Pemakaian Ruangan - exact match with original
                SELECT 
                    s.stokobatalkes_id,
                    s.obatalkes_id,
                    s.tglstok_out::date AS tanggal,
                    s.qtystok_out,
                    NULL::double precision as qty_resep,
                    CASE 
                        WHEN s.pemakaianobatdetail_id IS NOT NULL 
                        AND s.tglstok_out IS NOT NULL 
                        THEN s.qtystok_out 
                        ELSE NULL::double precision 
                    END AS qty_pemakaianruangan,
                    NULL::double precision as qty_bmhp,
                    NULL::double precision as qty_mutasi
                FROM stokobatalkes_t s
                INNER JOIN (
                    SELECT 
                        pemakaianobatdetail_id,
                        pemakaianobat_t.ruangan_id
                    FROM pemakaianobatdetail_t
                    JOIN pemakaianobat_t ON pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
                    WHERE pemakaianobat_t.is_deleted = false
                    AND pemakaianobatdetail_t.is_deleted = false
                ) pemakaianobatdetail_t ON s.pemakaianobatdetail_id = pemakaianobatdetail_t.pemakaianobatdetail_id AND vpemakaianruangan
                WHERE s.tglstok_out IS NOT NULL
                    AND (
                        s.tglstok_out >= current_date::timestamp without time zone AND s.tglstok_out <= (current_date - '30 days'::interval) 
                        OR 
                        s.tglstok_out >= (current_date - '30 days'::interval) AND s.tglstok_out <= current_date::timestamp without time zone
                    )
                
                UNION ALL
                
                -- Third UNION: BMHP - exact match with original
                SELECT 
                    s.stokobatalkes_id,
                    s.obatalkes_id,
                    s.tglstok_out::date AS tanggal,
                    s.qtystok_out,
                    NULL::double precision as qty_resep,
                    NULL::double precision as qty_pemakaianruangan,
                    CASE 
                        WHEN s.obatalkespasien_id IS NOT NULL 
                        AND s.tglstok_out IS NOT NULL 
                        THEN s.qtystok_out 
                        ELSE NULL::double precision 
                    END AS qty_bmhp,
                    NULL::double precision as qty_mutasi
                FROM stokobatalkes_t s
                INNER JOIN (
                    SELECT
                        obatalkespasien_t.obatalkespasien_id,
                        obatalkespasien_t.ruangan_id 
                    FROM obatalkespasien_t
                    WHERE penjualanresep_id IS NULL
                        AND is_deleted = false
                ) obatalkespasien_t ON s.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id AND vbmhp
                WHERE s.tglstok_out IS NOT NULL
                    AND (
                        s.tglstok_out >= current_date::timestamp without time zone AND s.tglstok_out <= (current_date - '30 days'::interval) 
                        OR 
                        s.tglstok_out >= (current_date - '30 days'::interval) AND s.tglstok_out <= current_date::timestamp without time zone
                    )
                
                UNION ALL
                
                -- Fourth UNION: Mutasi - exact match with original
                SELECT 
                    s.stokobatalkes_id,
                    s.obatalkes_id,
                    s.tglstok_out::date AS tanggal,
                    s.qtystok_out,
                    NULL::double precision as qty_resep,
                    NULL::double precision as qty_pemakaianruangan,
                    NULL::double precision as qty_bmhp,
                    CASE 
                        WHEN s.mutasiobatdetail_id IS NOT NULL 
                        AND s.tglstok_out IS NOT NULL 
                        THEN s.qtystok_out 
                        ELSE NULL::double precision 
                    END AS qty_mutasi
                FROM stokobatalkes_t s
                INNER JOIN (
                    SELECT
                        mutasiobatdetail_t.mutasiobatdetail_id,
                        mutasiobatruangan_t.ruanganasal_id,
                        mutasiobatruangan_t.ruangantujuan_id
                    FROM mutasiobatdetail_t
                    JOIN mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
                    WHERE mutasiobatruangan_t.ruanganasal_id = gudang_farmasi_id
                    AND mutasiobatruangan_t.ruangantujuan_id NOT IN (
                        SELECT ruangan_id FROM ruangan_m rm 
                        WHERE instalasi_id = farmasi_instalasi_id
                    )
                    AND mutasiobatruangan_t.is_deleted = false
                    AND mutasiobatdetail_t.is_deleted = false
                ) mutasiobatdetail_t ON s.mutasiobatdetail_id = mutasiobatdetail_t.mutasiobatdetail_id AND vmutasi
                WHERE s.tglstok_out IS NOT NULL
                    AND (
                        s.tglstok_out >= current_date::timestamp without time zone AND s.tglstok_out <= (current_date - '30 days'::interval) 
                        OR 
                        s.tglstok_out >= (current_date - '30 days'::interval) AND s.tglstok_out <= current_date::timestamp without time zone
                    )
                    
            ) detail
            GROUP BY detail.obatalkes_id, detail.tanggal
            ORDER BY detail.obatalkes_id, detail.tanggal
        ) count_stok
        LEFT JOIN obatalkes_m om ON count_stok.obatalkes_id = om.obatalkes_id
        WHERE om.is_deleted = false AND om.is_active = true
        GROUP BY count_stok.obatalkes_id, om.jenisobatalkes_id
    ) calc
    LEFT JOIN movingcriteria_m mc ON calc.count <= mc.max AND calc.count >= mc.min;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000