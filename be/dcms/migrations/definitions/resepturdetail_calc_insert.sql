CREATE OR REPLACE FUNCTION public.resepturdetail_calc_insert()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
	BEGIN
		INSERT INTO resepturdetail_calc (resepturdetail_id, obatalkes_id, qty_konversi, det_konversi, reseptur_id, is_deleted)
		VALUES (new.resepturdetail_id, new.obatalkes_id, new.qty_konversi, new.det_konversi, new.reseptur_id, new.is_deleted);
	RETURN NEW;
	END;
$function$
;