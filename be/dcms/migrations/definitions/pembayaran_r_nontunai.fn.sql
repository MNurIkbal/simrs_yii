CREATE OR REPLACE FUNCTION public.pembayaran_r_nontunai()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$

DECLARE  
        v_tipe_pembayaran INT;
        v_bank_id INT;
        
BEGIN
    
            SELECT 
                jenisnontunai_m.tipe_pembayaran,
                jenisnontunai_m.bank_id
            INTO
                v_tipe_pembayaran,
                v_bank_id
            FROM jenisnontunai_m
            WHERE jenisnontunai_m.jenisnontunai_id = NEW.jenisnontunai_id;

        -- INSERT table history pembayaran_r untuk case non tunai
        INSERT INTO pembayaran_r (      
        pembayaran_id,
        pendaftaran_id,
        total_tunai,
        total_nontunai,
        total_dijamin,
        nama_edc,
        tipe_pembayaran,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
        )VALUES(
        NEW.pembayaran_id ,
        NEW.pendaftaran_id ,
        0, --total_tunai
        NEW.total_dibayar ,
        0, --total dijamin
        NEW.nama_edc,
        v_tipe_pembayaran,
        concat('{"pembayaranmetode_t":',row_to_json(new.*),',"jenisnontunai_m":',concat('{"bank_id":',v_bank_id,'}')::json,'}') ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'RECEIPT'
        );

    RETURN NEW;

END
$function$
;
