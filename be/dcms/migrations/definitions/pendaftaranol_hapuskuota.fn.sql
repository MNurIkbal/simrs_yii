CREATE OR REPLACE FUNCTION public.pendaftaranol_hapuskuota()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$DECLARE
   isBpjs BOOLEAN;
   isOnline BOOLEAN;
   jadwalDokterId INT;
   vAsalStokOnline integer;
   vAsalStok integer;  
begin
	IF(OLD.antrian_id is not null and  OLD.status_daftar_ol <> NEW.status_daftar_ol and NEW.status_daftar_ol = 566)
	then
        SELECT is_online,jadwaldokter_id, case when carabayar_id = 6 then true when groupcarabayar_id =418 then true else false end as is_bpjs
        INTO isOnline,jadwalDokterId,isBpjs
        FROM antrian_t 
        WHERE antrian_id = OLD.antrian_id;

        IF(jadwalDokterId is not null)
        THEN 
    		IF (isOnline) THEN
                --  Get Total Stok Dokter sebelumnya
                SELECT stokkuotadokter_id
                INTO vAsalStokOnline
                FROM stokkuotadokter_t
                WHERE jadwaldokter_id = jadwalDokterId AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0 AND is_online = TRUE;
                                                 
                IF (isBpjs = TRUE) THEN
                    -- Kembaliin stok kuota dokter sebelumnya
                    INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldoktertambahan_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_bpjs)
                    VALUES (OLD.antrian_id,true,jadwalDokterId,vAsalStokOnline,now(),-1,isOnline,-1);
                ELSE
                    -- Kembaliin stok kuota dokter sebelumnya
                    INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_nonbpjs)
                    VALUES (OLD.antrian_id,true,jadwalDokterId,vAsalStokOnline,now(),-1,isOnline,-1);
                END IF; -- isbpjs true
            ELSE --isonline
                -- Get Total Stok Dokter offline sebelumnya
                SELECT stokkuotadokter_id
                INTO vAsalStok
                FROM stokkuotadokter_t
                WHERE jadwaldokter_id = jadwalDokterId AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0;
                                             
                IF (isBpjs = TRUE ) then
                    -- Kembaliin Stok Kuota Dokter offline sebelumnya
                    INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_bpjs)
                    VALUES (OLD.antrian_id,true, jadwalDokterId,vAsalStok,now(),-1,isOnline,-1);
                ELSE --isbpjs true
                    -- Kembaliin Stok Kuota Dokter offline sebelumnya
                    INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_nonbpjs)
                    VALUES (OLD.antrian_id,true, jadwalDokterId,vAsalStok,now(),-1,isOnline,-1);
                END IF; -- if bpjs
            END IF; -- if isonline
        END IF; --jadwaldokter
	end if;
    RETURN NEW;
END$function$
;
