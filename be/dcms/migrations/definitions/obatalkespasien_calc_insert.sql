CREATE OR REPLACE FUNCTION public.obatalkespasien_calc_insert()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
	BEGIN
		IF (new.penjualanresep_id IS NOT NULL)
		THEN
			INSERT INTO obatalkespasien_calc (obatalkespasien_id, qty_konversi, det_konversi, obatalkes_id, penjualanresep_id, is_deleted)
			VALUES (new.obatalkespasien_id, new.qty_konversi, new.det_konversi, new.obatalkes_id, new.penjualanresep_id, new.is_deleted);
		END IF;
	RETURN NEW;
	END;
$function$
;