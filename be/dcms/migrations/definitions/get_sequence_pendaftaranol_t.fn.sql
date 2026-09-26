CREATE OR REPLACE FUNCTION public.get_sequence_pendaftaranol_t(vprefix character varying DEFAULT '-'::character varying, vdate date DEFAULT CURRENT_DATE)
 RETURNS text
 LANGUAGE plpgsql
AS $function$
DECLARE
    seq_val BIGINT;
    date_val TEXT;
    result TEXT;
BEGIN
    date_val := TO_CHAR(NOW(), 'YYMMDD');
    BEGIN
        IF(vDate <> CURRENT_DATE)
        then
        	date_val := TO_CHAR(vDate,'YYMMDD');
        end if;
	    -- Lock the row to prevent concurrent updates
        SELECT sequence INTO seq_val FROM tableseq_pendaftaranol_t WHERE prefix =vPrefix and date = vDate FOR UPDATE;
        seq_val := seq_val + 1;
        UPDATE tableseq_pendaftaranol_t SET sequence = seq_val WHERE date = vDate and prefix =vPrefix;
        result := LPAD(date_val, 6, '0') || LPAD(seq_val::TEXT, 5, '0');
       
      	IF result IS NULL then 
      		seq_val := 1;
	            INSERT INTO tableseq_pendaftaranol_t VALUES (vPrefix,vDate, seq_val);
	            result := LPAD(date_val, 6, '0') || LPAD(seq_val::TEXT, 5, '0');
      	end if;
	            
    END;
    RETURN result;
END;
$function$
;
