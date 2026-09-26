-- DROP FUNCTION public.upd_kuotadokter_r_try();

CREATE OR REPLACE FUNCTION public.upd_kuotadokter_r_try()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
    DECLARE
vIdJadwal integer;
vIdJ integer;
vNewId integer;
vJadwalExistOffline integer;
vJadwalExistOnline integer;
vMasuk integer;
vMasukOnline integer;
vKeluar integer;
vKeluarOnline integer;

vMasukBpjsOffline INTEGER;
vMasukNonBpjsOffline INTEGER;
vMasukBpjsOnline INTEGER;
vMasukNonBpjsOnline INTEGER;

vKeluarBpjsOffline INTEGER;
vKeluarNonBpjsOffline INTEGER;
vKeluarBpjsOnline INTEGER;
vKeluarNonBpjsOnline INTEGER;

qtyIn INTEGER;
qtyInOnline INTEGER;
qtyOut INTEGER;
qtyOutOnline INTEGER;
qtyMasuk INTEGER;
qtyMasukOnline INTEGER;

qtyInBpjsOffline INTEGER;
qtyInNonBpjsOffline INTEGER;
qtyInBpjsOnline INTEGER;
qtyInNonBpjsOnline INTEGER;

qtyOutBpjsOffline INTEGER;
qtyOutNonBpjsOffline INTEGER;
qtyOutBpjsOnline INTEGER;
qtyOutNonBpjsOnline INTEGER;

kuotaKeluar INTEGER;
kuotaTersedia INTEGER;
kuotaKeluarOnline INTEGER;
kuotaTersediaOnline INTEGER;

kuotaBpjsOffline INTEGER;
kuotaBpjsOnline INTEGER;
kuotaNonBpjsOnline INTEGER;
kuotaNonBpjsOffline INTEGER;

kuotaOutBpjsOffline INTEGER;
kuotaOutNonBpjsOffline INTEGER;
kuotaOutBpjsOnline INTEGER;
kuotaOutNonBpjsOnline INTEGER;

BEGIN
        qtyIn := 0;
        qtyInOnline := 0;
        qtyOut := 0;
        qtyOutOnline := 0;
        
        qtyInBpjsOffline := 0;
        qtyInNonBpjsOffline := 0;
        qtyInBpjsOnline := 0;
        qtyInNonBpjsOnline := 0;
        
        qtyOutBpjsOffline := 0;
        qtyOutNonBpjsOffline := 0;
        qtyOutBpjsOnline := 0;
        qtyOutNonBpjsOnline := 0;

        IF (NEW.tgltransaksi_in IS NOT NULL  AND NEW.tgltransaksi_out IS NULL AND NEW.is_online = false) THEN 
                qtyIn := NEW.kuota_in;
                qtyInBpjsOffline := NEW.kuota_bpjs_offline;
                qtyInNonBpjsOffline := NEW.kuota_nonbpjs_offline;
        ELSEIF (NEW.tgltransaksi_in IS NOT NULL  AND NEW.tgltransaksi_out IS NULL AND NEW.is_online = true) THEN
                qtyInOnline := NEW.kuota_in;
                qtyInBpjsOnline := NEW.kuota_bpjs_online;
                qtyInNonBpjsOnline := NEW.kuota_nonbpjs_online;
        ELSEIF (NEW.tgltransaksi_in IS NULL AND NEW.tgltransaksi_out IS NOT NULL AND NEW.is_online = false) THEN
                qtyOut := NEW.kuota_out;
                qtyOutBpjsOffline := NEW.kuota_out_bpjs;
                qtyOutNonBpjsOffline := NEW.kuota_out_nonbpjs;
        ELSEIF (NEW.tgltransaksi_in IS NULL AND NEW.tgltransaksi_out IS NOT NULL AND NEW.is_online = true) THEN
                qtyOutOnline := NEW.kuota_out;
                qtyOutBpjsOnline := NEW.kuota_out_bpjs;
                qtyOutNonBpjsOnline := NEW.kuota_out_nonbpjs;
        END IF;

        IF (NEW.jadwaldokter_id IS NOT NULL) THEN
                -- Offline kuotadokter_r
                SELECT 
                        jadwaldokter_id,
                        kuota_keluar,
                        kuota_tersedia,
                        kuota_masuk,
                        kuota_bpjs_offline,
                        kuota_nonbpjs_offline,
                        kuota_out_bpjs,
                        kuota_out_nonbpjs
                INTO 
                        vJadwalExistOffline,
                        kuotaKeluar,
                        kuotaTersedia,
                        qtyMasuk,
                        kuotaBpjsOffline,
                        kuotaNonBpjsOffline,
                        kuotaOutBpjsOffline,
                        kuotaOutNonBpjsOffline
                FROM kuotadokter_r
                WHERE jadwaldokter_id = NEW.jadwaldokter_id AND is_online = false;
                
                -- Online kuotadokter_r
                SELECT 
                        jadwaldokter_id,
                        kuota_keluar,
                        kuota_tersedia,
                        kuota_masuk,
                        kuota_bpjs_online,
                        kuota_nonbpjs_online,
                        kuota_out_bpjs,
                        kuota_out_nonbpjs
                INTO 
                        vJadwalExistOnline,
                        kuotaKeluarOnline,
                        kuotaTersediaOnline,
                        qtyMasukOnline,
                        kuotaBpjsOnline,
                        kuotaNonBpjsOnline,
                        kuotaOutBpjsOnline,
                        kuotaOutNonBpjsOnline
                FROM kuotadokter_r
                WHERE jadwaldokter_id = NEW.jadwaldokter_id AND is_online = true;
        ELSE
                -- Offline kuotadokter_r --kuotapoli
                SELECT 
                        jadwalbukapoli_id,
                        kuota_keluar,
                        kuota_tersedia,
                        kuota_masuk,
                        kuota_bpjs_offline,
                        kuota_nonbpjs_offline,
                        kuota_out_bpjs,
                        kuota_out_nonbpjs
                INTO 
                        vJadwalExistOffline,
                        kuotaKeluar,
                        kuotaTersedia,
                        qtyMasuk,
                        kuotaBpjsOffline,
                        kuotaNonBpjsOffline,
                        kuotaOutBpjsOffline,
                        kuotaOutNonBpjsOffline
                FROM kuotadokter_r
                WHERE jadwalbukapoli_id = NEW.jadwalbukapoli_id AND is_online = false;
                
                -- Online kuotadokter_r
                SELECT 
                        jadwalbukapoli_id,
                        kuota_keluar,
                        kuota_tersedia,
                        kuota_masuk,
                        kuota_bpjs_online,
                        kuota_nonbpjs_online,
                        kuota_out_bpjs,
                        kuota_out_nonbpjs
                INTO 
                        vJadwalExistOnline,
                        kuotaKeluarOnline,
                        kuotaTersediaOnline,
                        qtyMasukOnline,
                        kuotaBpjsOnline,
                        kuotaNonBpjsOnline,
                        kuotaOutBpjsOnline,
                        kuotaOutNonBpjsOnline
                FROM kuotadokter_r
                WHERE jadwalbukapoli_id = NEW.jadwalbukapoli_id AND is_online = true;
        END IF;
 
        IF (NEW.batalkonsolpoli_id IS NOT NULL OR NEW.bataljanjipoli_id IS NOT NULL OR NEW.batalantrian_id IS NOT NULL) THEN
                vMasuk := kuotaTersedia + qtyIn;
                vKeluar := kuotaKeluar - qtyIn;
                
                vMasukBpjsOffline := kuotaBpjsOffline + qtyOutBpjsOffline;
                vMasukNonBpjsOffline := kuotaNonBpjsOffline + qtyOutNonBpjsOffline;
                vMasukBpjsOnline := kuotaBpjsOnline + qtyOutBpjsOnline;
                vMasukNonBpjsOnline := kuotaNonBpjsOnline + qtyOutNonBpjsOnline;
                
                vKeluarBpjsOffline :=  kuotaOutBpjsOffline - qtyOutBpjsOffline;
                vKeluarNonBpjsOffline :=  kuotaOutNonBpjsOffline - qtyOutNonBpjsOffline;
                vKeluarBpjsOnline :=  kuotaOutBpjsOnline - qtyOutBpjsOnline;
                vKeluarNonBpjsOnline :=  kuotaOutNonBpjsOnline - qtyOutNonBpjsOnline;
        ELSEIF (NEW.jadwaldoktertambahan_id IS NOT NULL) THEN
                vMasuk := kuotaTersedia + qtyIn;
                qtyMasuk := qtyMasuk + qtyIn;
                vKeluar := kuotaKeluar;
        ELSE
                vMasuk := kuotaTersedia - qtyOut;
                vMasukOnline := kuotaTersediaOnline - qtyOutOnline;
                vKeluar := kuotaKeluar + qtyOut;
                vKeluarOnline := kuotaKeluarOnline + qtyOutOnline;
                
                vMasukBpjsOffline := kuotaBpjsOffline - qtyOutBpjsOffline;
                vMasukNonBpjsOffline := kuotaNonBpjsOffline - qtyOutNonBpjsOffline;
                vMasukBpjsOnline := kuotaBpjsOnline - qtyOutBpjsOnline;
                vMasukNonBpjsOnline := kuotaNonBpjsOnline - qtyOutNonBpjsOnline;
                
                vKeluarBpjsOffline :=  kuotaOutBpjsOffline + qtyOutBpjsOffline;
                vKeluarNonBpjsOffline :=  kuotaOutNonBpjsOffline + qtyOutNonBpjsOffline;
                vKeluarBpjsOnline :=  kuotaOutBpjsOnline + qtyOutBpjsOnline;
                vKeluarNonBpjsOnline :=  kuotaOutNonBpjsOnline + qtyOutNonBpjsOnline;
        END IF;

        IF ((vJadwalExistOffline IS NOT NULL AND NEW.is_online = FALSE) OR (vJadwalExistOnline IS NOT NULL AND NEW.is_online = TRUE)) THEN
                IF (NEW.jadwaldokter_id IS NOT NULL) THEN
                        IF (NEW.is_online = false) THEN
                                UPDATE kuotadokter_r
                                SET
                                        kuota_masuk = qtyMasuk,
                                        kuota_tersedia = vMasuk,
                                        kuota_keluar = vKeluar,
                                        kuota_bpjs_offline = vMasukBpjsOffline,
                                        kuota_nonbpjs_offline = vMasukNonBpjsOffline,
                                        kuota_out_bpjs = vKeluarBpjsOffline,
                                        kuota_out_nonbpjs = vKeluarNonBpjsOffline
                                WHERE jadwaldokter_id = NEW.jadwaldokter_id AND is_online = false;
                        END IF;

                        IF (NEW.is_online = true) THEN
                                UPDATE kuotadokter_r
                                SET
                                        kuota_masuk = qtyMasukOnline,
                                        kuota_tersedia = vMasukOnline,
                                        kuota_keluar = vKeluarOnline,
                                        kuota_bpjs_online = vMasukBpjsOnline,
                                        kuota_nonbpjs_online = vMasukNonBpjsOnline,
                                        kuota_out_bpjs = vKeluarBpjsOnline,
                                        kuota_out_nonbpjs = vKeluarNonBpjsOnline
                                WHERE jadwaldokter_id = NEW.jadwaldokter_id AND is_online = true;
                        END IF;
                ELSE
                -- poliklinik
                        IF (NEW.is_online = false) THEN
                                UPDATE kuotadokter_r
                                SET
                                        kuota_masuk = qtyMasuk,
                                        kuota_tersedia = vMasuk,
                                        kuota_keluar = vKeluar
                                WHERE jadwalbukapoli_id = NEW.jadwalbukapoli_id AND is_online = false;
                        END IF;

                        IF (NEW.is_online = true) THEN
                                UPDATE kuotadokter_r
                                SET
                                        kuota_masuk = qtyMasukOnline,
                                        kuota_tersedia = vMasukOnline,
                                        kuota_keluar = vKeluarOnline
                                WHERE jadwalbukapoli_id = NEW.jadwalbukapoli_id AND is_online = true;
                        END IF;
                END IF;

                RETURN NULL;
        END IF;

        IF (NEW.jadwaldokter_id IS NOT NULL) THEN
                IF (NEW.is_online = false) THEN
                        INSERT INTO kuotadokter_r(jadwaldokter_id,kuota_masuk,kuota_tersedia,kuota_keluar,is_online,kuota_bpjs_offline,kuota_nonbpjs_offline, kuota_out_bpjs , kuota_out_nonbpjs)
                        VALUES(NEW.jadwaldokter_id,NEW.kuota_in,NEW.kuota_in,0,false,NEW.kuota_bpjs_offline,NEW.kuota_nonbpjs_offline, new.kuota_out_bpjs , new.kuota_out_nonbpjs);
                END IF;

                IF (NEW.is_online = true) THEN
                        INSERT INTO kuotadokter_r(jadwaldokter_id,kuota_masuk,kuota_tersedia,kuota_keluar,is_online,kuota_bpjs_online,kuota_nonbpjs_online,  kuota_out_bpjs , kuota_out_nonbpjs)
                        VALUES(NEW.jadwaldokter_id,NEW.kuota_in,NEW.kuota_in,0,true,NEW.kuota_bpjs_online,NEW.kuota_nonbpjs_online, new.kuota_out_bpjs , new.kuota_out_nonbpjs);
                END IF;
        ELSE
        -- kuota poliklinik
                IF (NEW.is_online = false) THEN
                        INSERT INTO kuotadokter_r(jadwalbukapoli_id,kuota_masuk,kuota_tersedia,kuota_keluar,is_online)
                        VALUES(NEW.jadwalbukapoli_id,NEW.kuota_in,NEW.kuota_in,0,false);
                END IF;

                IF (NEW.is_online = true) THEN
                        INSERT INTO kuotadokter_r(jadwalbukapoli_id,kuota_masuk,kuota_tersedia,kuota_keluar,is_online)
                        VALUES(NEW.jadwalbukapoli_id,NEW.kuota_in,NEW.kuota_in,0,true);
                END IF;
        END IF;

        RETURN NULL;

        -- IF (NEW.jadwaldokter_id IS NOT NULL AND NEW.jadwaldoktertambahan_id IS NULL) THEN
        -- vNewId = NEW.jadwaldokter_id;
        -- ELSIF (NEW.jadwaldokter_id IS NOT NULL AND NEW.jadwaldoktertambahan_id IS NOT NULL) THEN
        -- vNewId = NEW.jadwaldoktertambahan_id;
        -- ELSIF (NEW.jadwaldokter_id IS NULL AND NEW.kuotaasal_id IS NOT NULL) THEN
        -- vNewId = NEW.kuotaasal_id;
        -- ELSE
        -- RAISE EXCEPTION 'Gagal';
        -- END IF;

        -- SELECT id_dokter,k_masuk,k_keluar
        -- FROM(
        -- SELECT 
        -- CASE    WHEN jadwaldokter_id IS NULL THEN kuotaasal_id
        -- WHEN jadwaldokter_id IS NOT NULL THEN jadwaldokter_id
        -- END AS jid_dokter,
        -- SUM(kuota_in) as k_masuk,
        -- SUM(kuota_out) as k_keluar
        -- INTO 
        -- vIdJ,
        -- vMasuk,
        -- vKeluar
        -- FROM stokkuotadokter_t
        -- WHERE flag IS TRUE AND is_deleted IS FALSE AND is_active IS TRUE
        -- GROUP BY(jid_dokter)
        -- ) t1
        -- WHERE jid_dokter = vNewId;
                        
        -- SELECT jadwaldokter_id
        -- INTO vJadwalExist
        -- FROM kuotadokter_r
        -- WHERE jadwaldokter_id = vNewId;

        -- IF (vJadwalExist IS NOT NULL) THEN
        -- UPDATE kuotadokter_r SET
        -- uota_masuk = vMasuk,
        -- kuota_keluar = vKeluar
        -- WHERE jadwaldokter_id = vNewId;
        -- RETURN NULL;
        -- END IF;
                        
        -- INSERT INTO kuotadokter_r(jadwaldokter_id,kuota_masuk,kuota_keluar)
        -- VALUES(vNewId,vMasuk,vKeluar);
        -- RETURN NULL;
END$function$
;
