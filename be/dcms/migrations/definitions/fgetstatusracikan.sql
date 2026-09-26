CREATE FUNCTION public.fgetstatusracikan(xreseptur_id integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$
DECLARE vnama_racikan VARCHAR;
BEGIN
	IF EXISTS (
		SELECT
			1
		FROM resepturracikan_t
		WHERE reseptur_id = xreseptur_id
		AND resepturracikan_t.type::text = 'OR'
		LIMIT 1
	)
	THEN 
		vnama_racikan := 'Racikan';
	ELSE 
		IF EXISTS (
			SELECT
				1 
			FROM resepturdetail_t
			WHERE reseptur_id = xreseptur_id
			AND resepturdetail_t.racikan_id = 1
			LIMIT 1 
		)
		THEN 
			vnama_racikan := 'Racikan';
		ELSE 
			IF EXISTS (
				SELECT
					1 
				FROM obatalkespasien_t
				JOIN (
					SELECT penjualanresep_id
					FROM penjualanresep_t a
				) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
				JOIN (
					SELECT reseptur_id,
					penjualanresep_id
					FROM reseptur_t a
				) reseptur_t ON penjualanresep_t.penjualanresep_id = reseptur_t.penjualanresep_id
				WHERE reseptur_t.reseptur_id = xreseptur_id
				AND obatalkespasien_t.racikan_id = 1
				LIMIT 1 
			)
			THEN 
				vnama_racikan := 'Racikan';
			ELSE 
				vnama_racikan := 'Non Racikan';
			END IF;
		END IF;
	END IF;
	
	RETURN vnama_racikan;
		
END
$function$
;
