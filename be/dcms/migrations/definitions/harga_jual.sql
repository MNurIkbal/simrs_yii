CREATE OR REPLACE FUNCTION public.harga_jual()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
DECLARE

    
BEGIN
		IF (NEW.det is null)
				THEN 
						NEW.hargajual_oa = ROUND(NEW.hargasatuan_oa) * NEW.qty_oa;
						NEW.hargasatuan_oa = ROUND(NEW.hargasatuan_oa);
				ELSE 
						NEW.hargajual_oa = ROUND(NEW.hargasatuan_oa) * NEW.det;
						NEW.hargasatuan_oa = ROUND(NEW.hargasatuan_oa);
		END IF; 

    RETURN NEW;
END
$function$
;