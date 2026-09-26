CREATE OR REPLACE FUNCTION public.penjualanresep_calc_insert()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
	BEGIN
		INSERT INTO penjualanresep_calc (penjualanresep_id, ruangan_id, noresep, reseptur_id, status_reseptur, is_deleted)
		VALUES (new.penjualanresep_id, new.ruangan_id, new.noresep, new.reseptur_id, new.status_reseptur, new.is_deleted);
	RETURN NEW;
	END;
$function$
;