CREATE OR REPLACE FUNCTION public.upd_noresep()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$

DECLARE
vId integer := 4; --> Transaksi Resep (RSP)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
--    SELECT 
--        (RIGHT('0' || date_part('YEAR',now()),4) ||
--        RIGHT('0' || date_part('month',now()),2) ||
--        RIGHT('0' || date_part('DAY',now()),2) ||
--        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(noresep), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
--    INTO 
--        vNumber
--    FROM penomoran_k
--        LEFT JOIN penjualanresep_t ON penjualanresep_t.created_date::DATE = CURRENT_DATE
--    WHERE penomoran_id = vId; 
        
    SELECT 
        prefix 
    INTO 
        vPrefix 
    FROM penomoran_k 
    WHERE penomoran_id = vId; 
    
--    UPDATE penomoran_k SET
--        last_number = vNumber,
--        last_generate = TRIM(vPrefix) || vNumber
--  WHERE penomoran_id = vId;
    select get_sequence_penjualanresep_t() into vNumber;
        
    NEW.noresep := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
$function$
;
