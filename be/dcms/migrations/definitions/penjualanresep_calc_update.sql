CREATE OR REPLACE FUNCTION public.penjualanresep_calc_update()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
	DECLARE
		vstatus_reseptur INTEGER;
		vpenjualanresep_id INTEGER;	
	BEGIN
		vstatus_reseptur = new.status_reseptur;
		vpenjualanresep_id = new.penjualanresep_id;

		IF (vstatus_reseptur = 660 OR vstatus_reseptur = 432)
		THEN
			DELETE FROM penjualanresep_calc WHERE penjualanresep_id = vpenjualanresep_id;
			DELETE FROM obatalkespasien_calc WHERE penjualanresep_id = vpenjualanresep_id;
		END IF;
	RETURN NEW;
	END;
$function$
;