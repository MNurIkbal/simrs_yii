CREATE OR REPLACE FUNCTION public.status_kamartempattidur_m_insert()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$

BEGIN
    INSERT INTO historystatuskamartempattidur_r (    	
        kamartempattidur_id,
        kamarruangan_id,
	    kettempattidur_id,
        updatestatus_date,
        loginpemakai_id
    ) VALUES (
        new.kamartempattidur_id,
        new.kamarruangan_id,
        new.kettempattidur_id,
        new.created_date,
        new.created_by
    );
RETURN NEW;
END;
$function$
;
