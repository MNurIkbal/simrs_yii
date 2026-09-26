CREATE OR REPLACE FUNCTION public.generate_kodebooking_antrianjkn()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
                    
DECLARE
vNumber VARCHAR;
    
begin
    if(new.kodebooking is not null) then
        vNumber := new.kodebooking;
    
    elsIF(new.pendaftaranol_id is not null) THEN
        vNumber := concat('OL',new.pendaftaranol_id);
    
    elsif(new.pendaftaran_id is not null) then 
        vNumber := concat('RS',new.pendaftaran_id);
    else
        vNumber := 0;
    end if;

    new.kodebooking := vNumber;

    RETURN NEW;
END
$function$;