CREATE OR REPLACE FUNCTION public.obatalkespasien_calc_update()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
	DECLARE
		vobatalkespasien_id INTEGER;	
	BEGIN
		vobatalkespasien_id = new.obatalkespasien_id;

		IF (new.det_konversi != old.det_konversi) THEN
			UPDATE obatalkespasien_calc
			SET det_konversi = new.det_konversi
			WHERE obatalkespasien_id = new.obatalkespasien_id;
		END IF;

		IF (new.is_deleted IS TRUE)
		THEN
			DELETE FROM obatalkespasien_calc WHERE obatalkespasien_id = vobatalkespasien_id;
		END IF;
	RETURN NEW;
	END;
$function$
;