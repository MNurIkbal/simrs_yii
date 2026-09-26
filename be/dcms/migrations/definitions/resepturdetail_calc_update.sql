CREATE OR REPLACE FUNCTION public.resepturdetail_calc_update()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
	DECLARE
		vresepturdetail_id INTEGER;	
	BEGIN
		vresepturdetail_id = new.resepturdetail_id;

		IF (new.det_konversi != old.det_konversi)
		THEN
			UPDATE resepturdetail_calc
			SET det_konversi = new.det_konversi
			WHERE resepturdetail_id = new.resepturdetail_id;
		END IF;

		IF (new.is_deleted IS TRUE)
		THEN
			DELETE FROM resepturdetail_calc WHERE resepturdetail_id = vresepturdetail_id;
		END IF;
	RETURN NEW;
	END;
$function$
;