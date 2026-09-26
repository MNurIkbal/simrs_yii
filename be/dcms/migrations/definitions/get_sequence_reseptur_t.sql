CREATE OR REPLACE FUNCTION get_sequence_reseptur_t()
    RETURNS TEXT
    LANGUAGE plpgsql
AS $$
DECLARE
    seq_val BIGINT;
    date_val TEXT;
    result TEXT;
BEGIN
    date_val := TO_CHAR(NOW(), 'YYYYMMDD');
    BEGIN
        -- Lock the row to prevent concurrent updates
        SELECT sequence INTO seq_val FROM tableseq_reseptur_t WHERE date = CURRENT_DATE FOR UPDATE;
        seq_val := seq_val + 1;
        UPDATE tableseq_reseptur_t SET sequence = seq_val WHERE date = CURRENT_DATE;
        result := LPAD(date_val, 8, '0') || LPAD(seq_val::TEXT, 4, '0');
       
      	IF result IS NULL then 
      		seq_val := 1;
	            INSERT INTO tableseq_reseptur_t VALUES (CURRENT_DATE, seq_val);
	            result := LPAD(date_val, 8, '0') || LPAD(seq_val::TEXT, 4, '0');
      	end if;
	            
    END;
    RETURN result;
END;
$$;