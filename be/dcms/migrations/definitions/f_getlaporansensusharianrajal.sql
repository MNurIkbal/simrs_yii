CREATE OR REPLACE FUNCTION public.f_getlaporansensusharianrajal(xfirstdate date, xlastdate date, xjenis_pendaftaran integer)
RETURNS TABLE(instalasi_id integer, ruangan_id integer, ruangan_nama character varying, jenis_ruangan integer, carabayar_id integer, carabayar_nama character varying, baru_laki integer, baru_perempuan integer, jml_baru integer, lama_laki integer, lama_perempuan integer, jml_lama integer, kunjungan integer, hp integer, adoa integer, adoad integer, adoapp integer)
LANGUAGE plpgsql
IMMUTABLE
AS $function$
	BEGIN
		IF xjenis_pendaftaran = 726 THEN
			RETURN QUERY
			SELECT * FROM f_getlaporansensusharianrajal_langsung(xfirstdate, xlastdate);
		END IF;

		IF xjenis_pendaftaran = 727 THEN
			RETURN QUERY
			SELECT * FROM f_getlaporansensusharianrajal_online(xfirstdate, xlastdate);
		END IF;

		IF xjenis_pendaftaran = 1145 THEN
			RETURN QUERY
			SELECT 
				data_gabung.instalasi_id::integer as instalasi_id, 
				data_gabung.ruangan_id::integer as ruangan_id, 
				data_gabung.ruangan_nama::character varying as ruangan_nama, 
				data_gabung.jenis_ruangan::integer as jenis_ruangan, 
				data_gabung.carabayar_id::integer as carabayar_id,
				data_gabung.carabayar_nama::character varying as carabayar_nama, 
				COALESCE(sum(data_gabung.baru_laki), 0)::integer AS baru_laki, 
				COALESCE(sum(data_gabung.baru_perempuan), 0)::integer AS baru_perempuan, 
				COALESCE(sum(data_gabung.jml_baru), 0)::integer AS jml_baru, 
				COALESCE(sum(data_gabung.lama_laki), 0)::integer AS lama_laki, 
				COALESCE(sum(data_gabung.lama_perempuan), 0)::integer AS lama_perempuan, 
				COALESCE(sum(data_gabung.jml_lama), 0)::integer AS jml_lama, 
				COALESCE(sum(data_gabung.jml_baru), 0)::integer + COALESCE(sum(data_gabung.jml_lama), 0)::integer AS kunjungan,
				COALESCE(max(data_gabung.hp), 0)::integer AS hp,
				0::integer AS adoa, 
				0::integer AS adoad, 
				0::integer AS adoapp
			FROM (
				SELECT * FROM f_getlaporansensusharianrajal_langsung(xfirstdate, xlastdate)
				UNION ALL
				SELECT * FROM f_getlaporansensusharianrajal_online(xfirstdate, xlastdate)
			) AS data_gabung
			GROUP BY
				data_gabung.instalasi_id, 
				data_gabung.ruangan_id, 
				data_gabung.ruangan_nama, 
				data_gabung.jenis_ruangan, 
				data_gabung.carabayar_id,
				data_gabung.carabayar_nama
			ORDER BY 
				data_gabung.ruangan_nama ASC, 
				data_gabung.carabayar_nama ASC;	
		END IF;
	END;
$function$
;
