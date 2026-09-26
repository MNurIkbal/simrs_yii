CREATE OR REPLACE FUNCTION public.f_getlaporansensusharianrajal_langsung(xfirstdate date, xlastdate date)
RETURNS TABLE(instalasi_id integer, ruangan_id integer, ruangan_nama character varying, jenis_ruangan integer, carabayar_id integer, carabayar_nama character varying, baru_laki integer, baru_perempuan integer, jml_baru integer, lama_laki integer, lama_perempuan integer, jml_lama integer, kunjungan integer, hp integer, adoa integer, adoad integer, adoapp integer)
LANGUAGE plpgsql
IMMUTABLE
AS $function$
	BEGIN
	RETURN QUERY
	with date_converter as (
		SELECT 
			CASE trim(to_char(tgl, 'day')) 
				WHEN 'sunday' THEN 81 
				WHEN 'monday' THEN 75 
				WHEN 'tuesday' THEN 76 
				WHEN 'wednesday' THEN 77 
				WHEN 'thursday' THEN 78 
				WHEN 'friday' THEN 79 
				WHEN 'saturday' THEN 80 
			END AS vidhari 
		FROM generate_series(xfirstdate :: date, xlastdate :: date, '1 day') AS tgl
	), 
	master_ruangan AS (
		SELECT
			ruangan_m.instalasi_id, 
			ruangan_m.ruangan_id, 
			ruangan_m.ruangan_nama, 
			ruangan_m.jenis_ruangan, 
			carabayar_m.carabayar_id, 
			carabayar_m.carabayar_nama 
		FROM ruangan_m 
	    JOIN carabayar_m ON 1 = 1 
	  	WHERE ruangan_m.jenis_ruangan IS NOT NULL
	    AND carabayar_m.is_active = TRUE 
	    AND carabayar_m.is_deleted = FALSE
	), 
	pendaftaran as (
		SELECT
			pendaftaran_t.pendaftaran_id, 
			pendaftaran_t.tgl_pendaftaran, 
			pendaftaran_t.instalasi_id, 
			pendaftaran_t.ruangan_id, 
			pendaftaran_t.status_periksa, 
			pendaftaran_t.carabayar_id, 
			pendaftaran_t.kunjungan, 
			pendaftaran_t.pasien_id, 
			pendaftaran_t.pasienpulang_id 
		FROM pendaftaran_t 
		WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xfirstdate::DATE AND xlastdate::DATE
	), 
	data_pendaftaran AS (
		SELECT
			pendaftaran.pendaftaran_id, 
			pendaftaran.tgl_pendaftaran, 
			pendaftaran.instalasi_id, 
			pendaftaran.ruangan_id, 
			pendaftaran.status_periksa, 
			pendaftaran.carabayar_id, 
			pendaftaran.kunjungan, 
			pasien_m.jeniskelamin, 
			pasienpulang_t.pasienpulang_id, 
			pasienpulang_t.carakeluar_id 
		FROM pendaftaran 
	    JOIN pasien_m ON pendaftaran.pasien_id = pasien_m.pasien_id 
	    LEFT JOIN pasienpulang_t ON pendaftaran.pasienpulang_id = pasienpulang_t.pasienpulang_id
	), 
	data_konsul AS (
		SELECT
			pendaftaran_t.pendaftaran_id, 
			pendaftaran_t.tgl_pendaftaran, 
			pendaftaran_t.instalasi_id, 
			pendaftaran_t.ruangan_id, 
			pendaftaran_t.status_periksa, 
			pendaftaran_t.carabayar_id, 
			pendaftaran_t.kunjungan, 
			pasien_m.jeniskelamin, 
			pasienpulang_t.pasienpulang_id, 
			pasienpulang_t.carakeluar_id 
		FROM pendaftaran_t 
	    JOIN konsulpoli_t ON pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id 
	    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id 
	    LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id 
	  where 
	    konsulpoli_t.tgl_konsulpoli::DATE BETWEEN xfirstdate::DATE AND xlastdate::DATE 
	    AND pendaftaran_t.instalasi_id = 1 
	    AND konsulpoli_t.ruangan_id::INTEGER in (
	      SELECT ruangan_m.ruangan_id FROM ruangan_m WHERE ruangan_m.jenis_ruangan = 722::varchar
	    ) 
	    AND pendaftaran_t.status_periksa::INTEGER NOT IN (402, 628)
	), 
	jenisruangan_722 AS (
		SELECT
			data_pendaftaran.pendaftaran_id, 
			data_pendaftaran.tgl_pendaftaran, 
			data_pendaftaran.instalasi_id, 
			data_pendaftaran.ruangan_id, 
			data_pendaftaran.status_periksa, 
			data_pendaftaran.carabayar_id, 
			data_pendaftaran.kunjungan, 
			data_pendaftaran.jeniskelamin, 
			data_pendaftaran.pasienpulang_id, 
			data_pendaftaran.carakeluar_id
	  	FROM data_pendaftaran 
	  	WHERE data_pendaftaran.ruangan_id::INTEGER IN (
			SELECT ruangan_m.ruangan_id FROM ruangan_m WHERE ruangan_m.jenis_ruangan = 722::varchar
	    ) 
	    AND pendaftaran_id NOT IN (
			SELECT pendaftaran_t.pendaftaran_id FROM pendaftaranol_t 
	        JOIN pendaftaran_t ON pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
	    ) 
	    AND status_periksa::INTEGER NOT IN (402, 628) 
		UNION ALL
	  	SELECT
	    	data_konsul.pendaftaran_id, 
			data_konsul.tgl_pendaftaran, 
			data_konsul.instalasi_id, 
			data_konsul.ruangan_id, 
			data_konsul.status_periksa, 
			data_konsul.carabayar_id, 
			data_konsul.kunjungan, 
			data_konsul.jeniskelamin, 
			data_konsul.pasienpulang_id, 
			data_konsul.carakeluar_id 
	  	FROM data_konsul
	), 
	jenisruangan_723 as (
		SELECT
	    	data_pendaftaran.pendaftaran_id, 
			data_pendaftaran.tgl_pendaftaran, 
			data_pendaftaran.instalasi_id, 
			data_pendaftaran.ruangan_id, 
			data_pendaftaran.status_periksa, 
			data_pendaftaran.carabayar_id, 
			data_pendaftaran.kunjungan, 
			data_pendaftaran.jeniskelamin, 
			data_pendaftaran.pasienpulang_id, 
			data_pendaftaran.carakeluar_id
	  	FROM data_pendaftaran 
	  	WHERE data_pendaftaran.ruangan_id::INTEGER IN (
	    	SELECT ruangan_m.ruangan_id FROM ruangan_m WHERE ruangan_m.jenis_ruangan = 723::varchar
	    ) 
	    AND pendaftaran_id NOT IN (
			SELECT pendaftaran.pendaftaran_id FROM pendaftaranol_t 
	        JOIN pendaftaran ON pendaftaranol_t.pendaftaran_id = pendaftaran.pendaftaran_id
	    )
	), 
	jenisruangan_724 AS (
		SELECT
	    	data_pendaftaran.pendaftaran_id, 
			data_pendaftaran.tgl_pendaftaran, 
			data_pendaftaran.instalasi_id, 
			data_pendaftaran.ruangan_id, 
			data_pendaftaran.status_periksa, 
			data_pendaftaran.carabayar_id, 
			data_pendaftaran.kunjungan, 
			data_pendaftaran.jeniskelamin, 
			data_pendaftaran.pasienpulang_id, 
			data_pendaftaran.carakeluar_id
		FROM data_pendaftaran 
		WHERE data_pendaftaran.instalasi_id = 2 
	    AND pasienpulang_id IS NOT NULL
	), 
	jenisruangan_725 as (
		SELECT
			pasienmasukpenunjang_t.pendaftaran_id, 
			data_pendaftaran.tgl_pendaftaran, 
			data_pendaftaran.instalasi_id, 
			data_pendaftaran.ruangan_id, 
			data_pendaftaran.status_periksa, 
			data_pendaftaran.carabayar_id, 
			data_pendaftaran.kunjungan, 
			data_pendaftaran.jeniskelamin, 
			data_pendaftaran.pasienpulang_id, 
			data_pendaftaran.carakeluar_id 
		FROM data_pendaftaran 
	    JOIN pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = data_pendaftaran.pendaftaran_id 
		WHERE data_pendaftaran.ruangan_id::INTEGER IN (
			SELECT ruangan_m.ruangan_id FROM ruangan_m WHERE ruangan_m.jenis_ruangan = 725::VARCHAR
	    ) 
	    AND pasienmasukpenunjang_t.is_bayar = true 
	    AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL 
	    AND pasienmasukpenunjang_t.status_periksa :: INTEGER NOT IN (476, 477) 
	  	UNION ALL
	  	SELECT
			pendaftaran_t.pendaftaran_id, 
			pendaftaran_t.tgl_pendaftaran, 
			pendaftaran_t.instalasi_id, 
			pendaftaran_t.ruangan_id, 
			pendaftaran_t.status_periksa, 
			pendaftaran_t.carabayar_id, 
			pendaftaran_t.kunjungan, 
			pasien_m.jeniskelamin, 
			null as pasienpulang_id, 
			null as carakeluar_id 
	  	FROM pendaftaran_t 
	    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id 
	    JOIN (
			SELECT 
				a.pendaftaran_id, 
				a.ruangan_id, 
				a.tglmasukpenunjang, 
				a.pasienmasukpenunjang_id, 
				a.status_periksa 
	      	FROM pasienmasukpenunjang_t a 
	        JOIN (
				SELECT 
					max(b.pasienmasukpenunjang_id) as max_penunjang_id, 
					b.pendaftaran_id, 
					b.ruangan_id 
	          	FROM pasienmasukpenunjang_t b 
	          	WHERE b.status_periksa::INTEGER NOT IN (476, 477) 
	            AND b.pasienadmisi_id IS NOT NULL 
	          	GROUP BY 
	            b.pendaftaran_id, 
	            b.ruangan_id, 
	            b.tglmasukpenunjang::date
	        ) max_penunjang ON a.pendaftaran_id = max_penunjang.pendaftaran_id 
	        AND a.pasienmasukpenunjang_id = max_penunjang.max_penunjang_id 
	        AND a.ruangan_id = max_penunjang.ruangan_id
	    ) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id 
	    JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id 
	  	WHERE pasienmasukpenunjang_t.tglmasukpenunjang::DATE BETWEEN xfirstdate::DATE AND xlastdate::DATE 
	    AND ruangan_m.ruangan_id::INTEGER IN (
			SELECT ruangan_m.ruangan_id FROM ruangan_m WHERE ruangan_m.jenis_ruangan = 725::VARCHAR
	    ) 
	    AND pendaftaran_t.is_aps = FALSE
	    AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT null 
	  	UNION ALL
	  	SELECT
			pendaftaran_t.pendaftaran_id, 
			pendaftaran_t.tgl_pendaftaran, 
			pendaftaran_t.instalasi_id, 
			pendaftaran_t.ruangan_id, 
			pendaftaran_t.status_periksa, 
			pendaftaran_t.carabayar_id, 
			pendaftaran_t.kunjungan, 
			pasien_m.jeniskelamin, 
			NULL AS pasienpulang_id, 
			NULL AS carakeluar_id 
		FROM pendaftaran_t 
	    JOIN pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id 
	    JOIN (
			SELECT 
				a.pendaftaran_id, 
				a.ruangan_id, 
				a.pasienmasukpenunjang_id, 
				a.tglmasukpenunjang, 
				a.status_periksa 
	      	FROM pasienmasukpenunjang_t a 
	        JOIN (
				SELECT 
					max(b.pasienmasukpenunjang_id) as max_id, 
					b.pendaftaran_id, 
					b.ruangan_id 
	          	FROM pasienmasukpenunjang_t b 
	          	WHERE b.status_periksa::INTEGER NOT IN (476, 477) 
	            AND b.pasienadmisi_id IS NULL 
	          	GROUP BY 
					b.pendaftaran_id, 
					b.ruangan_id, 
					b.tglmasukpenunjang::DATE
	        ) max_penunjang ON a.pendaftaran_id = max_penunjang.pendaftaran_id 
	        AND a.pasienmasukpenunjang_id = max_penunjang.max_id 
	        AND a.ruangan_id = max_penunjang.ruangan_id
	    ) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id 
	    JOIN ruangan_m on pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id 
	  	WHERE pasienmasukpenunjang_t.tglmasukpenunjang::DATE BETWEEN xfirstdate::DATE AND xlastdate::DATE 
	    AND ruangan_m.ruangan_id :: INTEGER in (
			SELECT ruangan_m.ruangan_id FROM ruangan_m WHERE ruangan_m.jenis_ruangan = 725::VARCHAR
	    ) 
	    AND pendaftaran_t.is_aps = false 
	    AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL
	  	UNION ALL
	  	SELECT
			pasienmasukpenunjang_t.pendaftaran_id, 
			pendaftaran_t.tgl_pendaftaran, 
			pendaftaran_t.instalasi_id, 
			pendaftaran_t.ruangan_id, 
			pendaftaran_t.status_periksa, 
			pendaftaran_t.carabayar_id, 
			pendaftaran_t.kunjungan, 
			pasien_m.jeniskelamin, 
			null as pasienpulang_id, 
			null as carakeluar_id 
		FROM pendaftaran_t 
	    JOIN pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id 
	    JOIN (
			SELECT 
				a.pasienmasukpenunjang_id, 
				a.pendaftaran_id, 
				a.tglmasukpenunjang, 
				a.ruangan_id, 
				a.status_periksa 
	      	FROM pasienmasukpenunjang_t a 
	        JOIN (
				SELECT 
					max(b.pasienmasukpenunjang_id) as max_penunjang_id, 
					b.pendaftaran_id, 
					b.ruangan_id 
	          	FROM pasienmasukpenunjang_t b 
				WHERE b.status_periksa::INTEGER NOT IN (476, 477) 
	          	GROUP BY 
					b.pendaftaran_id, 
					b.ruangan_id, 
					b.tglmasukpenunjang::DATE
	        ) max_penunjang ON a.pendaftaran_id = max_penunjang.pendaftaran_id 
	        AND a.pasienmasukpenunjang_id = max_penunjang.max_penunjang_id 
	        AND a.ruangan_id = max_penunjang.ruangan_id
	    ) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id 
	    JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id 
	  	WHERE pasienmasukpenunjang_t.tglmasukpenunjang::DATE BETWEEN xfirstdate::DATE AND xlastdate::DATE
	    AND pendaftaran_t.instalasi_id = 21 
	    AND ruangan_m.ruangan_id::INTEGER IN (
			SELECT ruangan_m.ruangan_id FROM ruangan_m WHERE ruangan_m.jenis_ruangan = 725 :: varchar
	    ) 
	    AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL
	), 
	data_jadwal AS (
		SELECT 
	    	jadwalbukapoli_m.ruangan_id, 
	    	COUNT(jadwalbukapoli_id) as jumlah_jadwal 
	  	FROM jadwalbukapoli_m 
	    JOIN date_converter ON jadwalbukapoli_m.hari = date_converter.vidhari 
	    AND jadwalbukapoli_m.ruangan_id IN (SELECT master_ruangan.ruangan_id FROM master_ruangan) 
	  	GROUP BY 
	    	jadwalbukapoli_m.ruangan_id
	), 
	summary AS (
	  	SELECT
			jenisruangan_722.pendaftaran_id, 
			jenisruangan_722.tgl_pendaftaran, 
			jenisruangan_722.instalasi_id, 
			jenisruangan_722.ruangan_id, 
			jenisruangan_722.status_periksa, 
			jenisruangan_722.carabayar_id, 
			jenisruangan_722.kunjungan, 
			jenisruangan_722.jeniskelamin, 
			jenisruangan_722.pasienpulang_id, 
			jenisruangan_722.carakeluar_id
		FROM jenisruangan_722 
	  	UNION ALL
	  	SELECT
			jenisruangan_723.pendaftaran_id, 
			jenisruangan_723.tgl_pendaftaran, 
			jenisruangan_723.instalasi_id, 
			jenisruangan_723.ruangan_id, 
			jenisruangan_723.status_periksa, 
			jenisruangan_723.carabayar_id, 
			jenisruangan_723.kunjungan, 
			jenisruangan_723.jeniskelamin, 
			jenisruangan_723.pasienpulang_id, 
			jenisruangan_723.carakeluar_id
		FROM jenisruangan_723 
		UNION ALL
		SELECT
			jenisruangan_724.pendaftaran_id, 
			jenisruangan_724.tgl_pendaftaran, 
			jenisruangan_724.instalasi_id, 
			jenisruangan_724.ruangan_id, 
			jenisruangan_724.status_periksa, 
			jenisruangan_724.carabayar_id, 
			jenisruangan_724.kunjungan, 
			jenisruangan_724.jeniskelamin, 
			jenisruangan_724.pasienpulang_id, 
			jenisruangan_724.carakeluar_id
		FROM jenisruangan_724 
		UNION ALL
		SELECT
			jenisruangan_725.pendaftaran_id, 
			jenisruangan_725.tgl_pendaftaran, 
			jenisruangan_725.instalasi_id, 
			jenisruangan_725.ruangan_id, 
			jenisruangan_725.status_periksa, 
			jenisruangan_725.carabayar_id, 
			jenisruangan_725.kunjungan, 
			jenisruangan_725.jeniskelamin, 
			jenisruangan_725.pasienpulang_id, 
			jenisruangan_725.carakeluar_id
		FROM jenisruangan_725
	), 
	count_data AS (
		SELECT
	    	summary.instalasi_id, 
	    	summary.ruangan_id, 
	    	summary.carabayar_id, 
	    	COUNT(summary.pendaftaran_id) FILTER (WHERE summary.jeniskelamin = '15'::TEXT AND summary.kunjungan = '180'::TEXT) AS baru_laki, 
	    	COUNT(summary.pendaftaran_id) FILTER (WHERE summary.jeniskelamin = '16'::TEXT AND summary.kunjungan = '180'::TEXT) AS baru_perempuan, 
	    	COUNT(summary.pendaftaran_id) FILTER (WHERE summary.kunjungan = '180'::TEXT) AS jml_baru, 
	    	COUNT(summary.pendaftaran_id) FILTER (WHERE summary.jeniskelamin = '15'::TEXT AND summary.kunjungan = '181'::TEXT) AS lama_laki, 
	    	COUNT(summary.pendaftaran_id) FILTER (WHERE summary.jeniskelamin = '16'::TEXT AND summary.kunjungan = '181'::TEXT) AS lama_perempuan, 
	    	COUNT(summary.pendaftaran_id) FILTER (WHERE summary.kunjungan = '181'::TEXT) AS jml_lama 
	  	FROM summary
	  	GROUP BY
			summary.instalasi_id, 
			summary.ruangan_id, 
			summary.carabayar_id
	)
	SELECT
		master_ruangan.instalasi_id::integer as instalasi_id, 
		master_ruangan.ruangan_id::integer as ruangan_id, 
		master_ruangan.ruangan_nama::character varying as ruangan_nama, 
		master_ruangan.jenis_ruangan::integer as jenis_ruangan, 
		master_ruangan.carabayar_id::integer as carabayar_id,
		master_ruangan.carabayar_nama::character varying as carabayar_nama, 
		COALESCE(count_data.baru_laki, 0)::integer as baru_laki, 
		COALESCE(count_data.baru_perempuan, 0)::integer as baru_perempuan, 
		COALESCE(count_data.jml_baru, 0)::integer as jml_baru, 
		COALESCE(count_data.lama_laki, 0)::integer as lama_laki, 
		COALESCE(count_data.lama_perempuan, 0)::integer as lama_perempuan, 
		COALESCE(count_data.jml_lama, 0)::integer as jml_lama, 
		COALESCE(count_data.jml_baru, 0)::integer + COALESCE(count_data.jml_lama, 0)::integer as kunjungan, 
		COALESCE(data_jadwal.jumlah_jadwal, 0)::integer as hp, 
		0::integer AS adoa, 
		0::integer AS adoad, 
		0::integer AS adoapp 
	FROM master_ruangan 
	LEFT JOIN count_data ON master_ruangan.instalasi_id = count_data.instalasi_id 
	AND master_ruangan.ruangan_id = count_data.ruangan_id 
	AND master_ruangan.carabayar_id = count_data.carabayar_id 
	LEFT JOIN data_jadwal ON master_ruangan.ruangan_id = data_jadwal.ruangan_id
	ORDER BY
		master_ruangan.ruangan_nama ASC, 
		master_ruangan.carabayar_nama ASC;
END;

$function$
;
