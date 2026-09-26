CREATE OR REPLACE FUNCTION public.reseptur_calc_update()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
	DECLARE
		vstatus_reseptur INTEGER;
		vreseptur_id INTEGER;	
	BEGIN
		vstatus_reseptur = new.status_reseptur;
		vreseptur_id = new.reseptur_id;

		IF (vstatus_reseptur = 660 OR vstatus_reseptur = 432)
		THEN
			DELETE FROM resepturdetail_calc WHERE reseptur_id = vreseptur_id;
		END IF;
	RETURN NEW;
	END;
$function$
;