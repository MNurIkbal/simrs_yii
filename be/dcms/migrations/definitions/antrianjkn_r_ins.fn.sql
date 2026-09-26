CREATE OR REPLACE FUNCTION public.antrianjkn_r_ins()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
    DECLARE
    dataAntrianJkn VARCHAR;
    paramJson VARCHAR;
    
    BEGIN
    paramJson := NEW.additional_data; 
    dataAntrianJkn := paramJson::json->>'jkn';
    
    -- di comment kebutuhan reservasi onsite
    --  IF(new.jenis_reservasi = 1102) THEN
        INSERT INTO antrianjkn_r (
            pendaftaranol_id,
            antrian_id,
            tanggal_periksa,
            nomorkartu,
            jenis_cara_bayar,
            jeniskunjungan,
            nomorreferensi,
            keterangan,
            no_rekam_medik,
            is_checkin,
            tgl_checkin,
            kodebooking
            ) VALUES (
            new.pendaftaranol_id,
            new.antrian_id,
            new.tgl_pendaftaranol,
            (dataAntrianJkn::json->>'nomorkartu')::VARCHAR,
            (dataAntrianJkn::json->>'jenis_cara_bayar')::INTEGER,
            (dataAntrianJkn::json->>'jeniskunjungan')::INTEGER,
            (dataAntrianJkn::json->>'nomorreferensi')::VARCHAR,
            (dataAntrianJkn::json->>'keterangan')::VARCHAR,
            (dataAntrianJkn::json->>'no_rekam_medik')::VARCHAR,
            new.is_checkin,
            new.tgl_checkin,
            new.no_pendaftaranol
            );
    --  END IF;
    RETURN NEW;
END$function$
;
