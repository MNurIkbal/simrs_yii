CREATE OR REPLACE FUNCTION public.generate_noreseptur()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$

DECLARE
    vId integer := 27; --> Transaksi Reseptur (RST)
    vPrefix VARCHAR;
    vNumber VARCHAR;

    -- perubahan stok qty dipesan
    var_status_reseptur INTEGER;
    var_ruangan_id INTEGER;
    var_reseptur_id INTEGER;
    
BEGIN
------------------------------------------no reseptur-----------------------------------------------------
	
--    SELECT 
--        (RIGHT('0' || date_part('YEAR',now()),4) ||
--        RIGHT('0' || date_part('month',now()),2) ||
--        RIGHT('0' || date_part('DAY',now()),2) ||
--        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(noresep), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
--    INTO 
--        vNumber
--    FROM penomoran_k
--        LEFT JOIN reseptur_t ON reseptur_t.created_date::DATE = CURRENT_DATE
--    WHERE penomoran_id = vId; 
--        
    SELECT 
        prefix 
    INTO 
        vPrefix 
    FROM penomoran_k 
    WHERE penomoran_id = vId; 
--    
--    UPDATE penomoran_k SET
--        last_number = vNumber,
--        last_generate = TRIM(vPrefix) || vNumber
--  WHERE penomoran_id = vId;
	select get_sequence_reseptur_t() into vNumber;
        
    NEW.noresep := TRIM(vPrefix) || vNumber;


------------------------------------------penambahan update qty_dipesan-----------------------------------------------------

    var_reseptur_id := new.reseptur_id;
    var_status_reseptur := new.status_reseptur;
    var_ruangan_id := NEW.ruangan_id;
    
    IF (var_status_reseptur = 347)
            THEN                                        
                    UPDATE stokobatalkes_r
                    SET qty_dipesan = (qty_dipesan - resepturdetail.total_qty  ), 
                            qty_tersedia = (qty_sisa - (qty_dipesan- resepturdetail.total_qty ))
                    FROM (
                            SELECT 
                                    reseptur_id as resid,
                                    obatalkes_id, 
                                    SUM(qty_konversi) as total_qty 
                            FROM resepturdetail_t 
                            WHERE reseptur_id = var_reseptur_id 
                                AND is_deleted = FALSE 
                            GROUP BY 
                                reseptur_id, 
                                obatalkes_id
                    ) as resepturdetail
                    WHERE resepturdetail.resid = var_reseptur_id 
                        AND (stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id) = (resepturdetail.obatalkes_id, var_ruangan_id);
        END IF;
    
    RETURN NEW;
END
$function$
;
