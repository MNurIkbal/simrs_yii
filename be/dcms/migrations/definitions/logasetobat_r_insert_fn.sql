CREATE OR REPLACE FUNCTION public.logasetobat_r_insert()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
                    
        DECLARE  
            v_qty_aset FLOAT8;
            v_harga_aset FLOAT8;
            v_weigthed_avg FLOAT8;
            v_harganetto FLOAT8;
            v_baseprice FLOAT8;
            v_ruangan INT4;
            v_is_weighted_avg_rs BOOL;

        BEGIN
            SELECT 
                ROUND(harganetto::NUMERIC, 2)
            INTO 
                v_baseprice
            FROM obatalkes_m
            WHERE obatalkes_id = new.obatalkes_id;

            SELECT 
                is_weighted_avg_rs 
            INTO 
                v_is_weighted_avg_rs 
            FROM konfigfarmasi_k kk 
            WHERE konfigfarmasi_id = 1;

            SELECT 
                kode_id 
            INTO v_ruangan 
            FROM lookuptransaksi_m 
            WHERE kode_transaksi = 'gudang_farmasi';

            IF v_is_weighted_avg_rs = false THEN
                v_ruangan = new.ruangan_id;
            END IF;

            --kondisi kalau udah ada di log bisa dipake harga aset
            SELECT
                harga_aset,
                ROUND(weighted_avg::NUMERIC, 2),
                qty_aset
            INTO
                v_harga_aset,
                v_weigthed_avg,
                v_qty_aset
            FROM logasetobat_r
            WHERE obatalkes_id = NEW.obatalkes_id 
            AND ruangan_id = v_ruangan --new.ruangan_id
            ORDER BY logasetobat_id DESC
            LIMIT 1;

            --kalau belum ada v_qty_aset dikali dengan latest price grn
            IF v_harga_aset IS NULL THEN
                SELECT
                    SUM(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)
                INTO
                    v_qty_aset
                FROM stokobatalkes_t
                WHERE obatalkes_id = NEW.obatalkes_id 
                AND ruangan_id = new.ruangan_id
                GROUP BY obatalkes_id;

                SELECT DISTINCT ON (obatalkes_id) harga * v_qty_aset
                INTO
                    v_harga_aset
                FROM (
                    SELECT
                        penerimaanobatdetail_t.obatalkes_id,
                        CASE WHEN penerimaanobatdetail_t.qty_diterima = 0 THEN
                            v_baseprice
                        ELSE
                            (
                                (penerimaanobatdetail_t.jumlah - penerimaanobatdetail_t.discount_rp) * 
                                (1 + (pajak_m.pajak_persen::FLOAT / 100))
                            )  / COALESCE(NULLIF(penerimaanobatdetail_t.qty_diterima, 0), 1)
                        END AS harga,
                        penerimaanobatdetail_t.created_date
                    FROM penerimaanobatdetail_t
                    LEFT JOIN validasipoobatdetail_t ON penerimaanobatdetail_t.validasipoobatdetail_id = validasipoobatdetail_t.validasipoobatdetail_id
                    LEFT JOIN validasipoobat_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
                    LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
                    
                    UNION ALL
                    
                    SELECT
                        penerimaansuppdetail_t.obatalkes_id,
                        CASE WHEN penerimaansuppdetail_t.qty_kecil = 0 THEN
                            v_baseprice
                        ELSE
                            (
                                (penerimaansuppdetail_t.harga_netto - ((penerimaansuppdetail_t.harga_netto * penerimaansuppdetail_t.diskon) / 100)) * 
                                (1 + (pajak_m.pajak_persen::FLOAT / 100)) / 
                                COALESCE(NULLIF(penerimaansuppdetail_t.qty_kecil, 0), 1)
                            ) 
                        END AS harga,
                        penerimaansuppdetail_t.created_date
                    FROM penerimaansuppdetail_t
                    LEFT JOIN penerimaansupp_t ON penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id
                    LEFT JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
                ) t
                WHERE obatalkes_id = new.obatalkes_id
                ORDER BY obatalkes_id, created_date DESC;
            END IF;

            IF v_weigthed_avg IS NULL THEN
                v_weigthed_avg = ROUND(v_baseprice::NUMERIC, 2);
            END IF;

            v_harganetto = ROUND(new.harganetto::NUMERIC, 2);

            IF (
                new.stokopnamedetail_id IS NOT NULL
                OR new.mutasiobatdetail_id IS NOT NULL
                OR new.terimamutasidetail_id IS NOT NULL
                -- or new.returpenerimaanobatdetail_id is not null -- Recalculated return qty with grn price
                OR new.adjusmenobatkeluar_id IS NOT NULL
                OR (new.adjusmenobatmasuk_id IS NOT NULL AND new.harganetto = 0)
            ) THEN
                v_harganetto = v_weigthed_avg;
            END IF;

            INSERT INTO logasetobat_r (     
                obatalkes_id,
                ruangan_id,
                tipe,
                qty_transaksi,
                harga_transaksi,
                qty_aset,
                harga_aset,
                weighted_avg,
                stokobatalkes_id
            )VALUES(
                NEW.obatalkes_id ,
                NEW.ruangan_id ,
                CASE
                    WHEN COALESCE(NEW.qtystok_in, 0) <> 0 THEN 'IN'
                    ELSE 'OUT'
                END, --tipe
                CASE
                    WHEN COALESCE(NEW.qtystok_in, 0) <> 0 THEN NEW.qtystok_in
                    ELSE NEW.qtystok_out
                END, --qty_transaksi
                CASE
                    WHEN COALESCE(NEW.qtystok_in, 0) <> 0 THEN 
                        ROUND(
                            (NEW.qtystok_in * ROUND(v_harganetto::NUMERIC, 2))::NUMERIC
                        , 2)
                    WHEN COALESCE(NEW.qtystok_out, 0) <> 0 AND new.returpenerimaanobatdetail_id IS NOT NULL THEN 
                        ROUND(
                            (NEW.qtystok_out * ROUND(v_harganetto::NUMERIC, 2))::NUMERIC
                        , 2)
                    ELSE NEW.qtystok_out * ROUND(COALESCE(v_weigthed_avg, v_baseprice)::NUMERIC, 2)
                END, -- harga_transaksi
                CASE
                    WHEN COALESCE(NEW.qtystok_in, 0) <> 0 THEN COALESCE(v_qty_aset, 0) + NEW.qtystok_in
                    ELSE COALESCE(v_qty_aset, 0) - NEW.qtystok_out
                END, -- qty_aset
                CASE
                    WHEN COALESCE(NEW.qtystok_in, 0) <> 0 THEN 
                        ROUND((
                            COALESCE(v_harga_aset, 0)::NUMERIC + 
                            (NEW.qtystok_in * ROUND(v_harganetto::NUMERIC, 2))
                        )::NUMERIC, 2)
                    WHEN COALESCE(NEW.qtystok_out, 0) <> 0 AND new.returpenerimaanobatdetail_id IS NOT NULL THEN 
                        ROUND((
                            ROUND(COALESCE(v_harga_aset, 0)::NUMERIC, 2) - 
                            (NEW.qtystok_out * ROUND(v_harganetto::NUMERIC, 2))
                        )::NUMERIC, 2)
                    ELSE 
                        ROUND((
                            ROUND(COALESCE(v_harga_aset, 0)::NUMERIC, 2) - 
                            (NEW.qtystok_out * ROUND(COALESCE(v_weigthed_avg, v_baseprice)::NUMERIC, 2))
                        )::NUMERIC, 2)
                END, -- harga_aset
                CASE
                    WHEN COALESCE(NEW.qtystok_in, 0) > 0 and NEW.ruangan_id = v_ruangan THEN 
                        ROUND((
                            (COALESCE(v_harga_aset, 0) + (NEW.qtystok_in * v_harganetto))::NUMERIC / 
                            COALESCE(
                                NULLIF((COALESCE(v_qty_aset,0) + NEW.qtystok_in), 0)
                            , 1)::NUMERIC
                        )::NUMERIC, 2)
                    WHEN COALESCE(NEW.qtystok_out, 0) > 0 AND NEW.ruangan_id = v_ruangan AND new.returpenerimaanobatdetail_id IS NOT NULL THEN
                        ROUND((
                                (COALESCE(v_harga_aset,0) - (NEW.qtystok_out * v_harganetto))::NUMERIC / 
                                COALESCE(
                                    NULLIF((COALESCE(v_qty_aset,0) - NEW.qtystok_out), 0)
                                , 1)::NUMERIC
                        )::NUMERIC, 2)
                    ELSE 
                        ROUND(COALESCE(v_weigthed_avg, v_baseprice)::numeric, 2)
                END, -- weighted_avg
                NEW.stokobatalkes_id
            );
            RETURN NEW;
        end
    $function$
;