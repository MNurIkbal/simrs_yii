CREATE OR REPLACE FUNCTION public.get_sequence_antrian_t(xprefix character varying DEFAULT '-'::character varying, xdate date DEFAULT CURRENT_DATE)
 RETURNS text
 LANGUAGE plpgsql
AS $function$
DECLARE
    seq_val BIGINT;
    date_val DATE;
    result TEXT;
BEGIN
    date_val := TO_CHAR(xdate::date, 'YYYY-MM-DD');
    begin
	    IF(xprefix IS NULL) then
        xprefix := '-';
        END IF;
        -- Lock the row to prevent concurrent updates
        SELECT sequence INTO seq_val FROM tableseq_antrian_t WHERE prefix =xprefix and date = date_val FOR UPDATE;
        seq_val := seq_val + 1;
        UPDATE tableseq_antrian_t SET sequence = seq_val WHERE date = date_val and prefix =xprefix;
        result := LPAD(seq_val::TEXT, 3, '0');
       
      	IF result IS NULL then 
      		seq_val := 1;
	            INSERT INTO tableseq_antrian_t VALUES (xprefix,date_val, seq_val);
	            result := LPAD(seq_val::TEXT, 3, '0');
      	end if;
	            
    END;
    RETURN result;
END;
$function$
;
