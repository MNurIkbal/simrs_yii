-- DROP FUNCTION public.sp_ubah_dokter_tindakan(int4, int4, int4);

CREATE OR REPLACE FUNCTION public.sp_ubah_dokter_tindakan(xpendaftaran_id integer, xpegawai_id integer, xuser_id integer)
 RETURNS TABLE(status integer, message text)
 LANGUAGE plpgsql
AS $function$
DECLARE 
    vstatus int4;
    vmessage TEXT;
    vpegawai_id_sebelum int4;
    vinstalasi_id int4;
    vno_pendaftaran VARCHAR;
		
     
BEGIN
	SELECT 
		no_pendaftaran instalasi_id, pegawai_id INTO vno_pendaftaran, vinstalasi_id, vpegawai_id_sebelum
	FROM pendaftaran_t
	WHERE pendaftaran_id = xpendaftaran_id;
				
			
	IF NOT EXISTS (
		SELECT 1
		FROM pegawai_m
		WHERE pegawai_id = xpegawai_id 
		AND is_deleted IS FALSE 
		AND is_active IS TRUE
	)
	THEN 				
	vstatus := 2;
	vmessage := CONCAT('Dokter ' , xno_pendaftaran , ' Tidak Ditemukan') ;
	ELSE
		SELECT
			pegawai_id
		INTO vpegawai_id_sebelum
		FROM pendaftaran_t
		WHERE pendaftaran_id = xpendaftaran_id;
		
		IF(vpegawai_id_sebelum <> xpegawai_id)
		THEN
-- 			UPDATE pendaftaran_t
-- 			SET pegawai_id = xpegawai_id,
-- 					last_modified_date = CURRENT_TIMESTAMP::TIMESTAMP(0),
-- 					last_modified_by = xuser_id
-- 			WHERE pendaftaran_id = xpendaftaran_id;
			
			UPDATE tindakanpelayanan_t
			SET dokterpenanggungjawab_id = xpegawai_id,
					last_modified_date = CURRENT_TIMESTAMP::TIMESTAMP(0),
					last_modified_by = xuser_id
			WHERE pendaftaran_id = xpendaftaran_id
			AND tindakansudahbayar_id IS NULL 
			AND is_deleted IS FALSE
			AND dokterpenanggungjawab_id = vpegawai_id_sebelum;
			
			IF(vinstalasi_id = 4)
			THEN
				UPDATE pasienmasukpenunjang_t
				SET pegawai_id = xpegawai_id,
					last_modified_date = CURRENT_TIMESTAMP::TIMESTAMP(0),
					last_modified_by = xuser_id
				WHERE pendaftaran_id = xpendaftaran_id;
			END IF;
		END IF;
		
		vstatus := 0;
		vmessage := 'Success';
	END IF;

	
	RETURN QUERY 
	SELECT vstatus, vmessage;
        
END; $function$
;
