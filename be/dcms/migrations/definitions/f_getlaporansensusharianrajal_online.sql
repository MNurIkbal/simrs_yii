CREATE OR REPLACE FUNCTION public.f_getlaporansensusharianrajal_online(xfirstdate date, xlastdate date)
RETURNS TABLE(instalasi_id integer, ruangan_id integer, ruangan_nama character varying, jenis_ruangan integer, carabayar_id integer, carabayar_nama character varying, baru_laki integer, baru_perempuan integer, jml_baru integer, lama_laki integer, lama_perempuan integer, jml_lama integer, kunjungan integer, hp integer, adoa integer, adoad integer, adoapp integer)
LANGUAGE plpgsql
IMMUTABLE
AS $function$
	BEGIN
	RETURN QUERY
	WITH date_converter AS (
	 	SELECT 
		    CASE trim(to_char(tgl, 'day'))
		        WHEN 'sunday'    THEN 81
		        WHEN 'monday'    THEN 75
		        WHEN 'tuesday'   THEN 76
		        WHEN 'wednesday' THEN 77
		        WHEN 'thursday'  THEN 78
		        WHEN 'friday'    THEN 79
		        WHEN 'saturday'  THEN 80
		    END AS vidhari
		FROM generate_series(xfirstdate :: date, xlastdate :: date, '1 day') AS tgl
	), master_ruangan AS (
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
	), data_pendaftaran as (
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
	), pendaftaran AS (
		SELECT 
			data_pendaftaran.pendaftaran_id,
			data_pendaftaran.tgl_pendaftaran,
			data_pendaftaran.instalasi_id,
			ruangan_m.ruangan_id,
			ruangan_m.jenis_ruangan,
			data_pendaftaran.status_periksa,
			data_pendaftaran.carabayar_id,
			data_pendaftaran.kunjungan,
			data_pendaftaran.pasien_id,
			data_pendaftaran.pasienpulang_id,
			pasien_m.jeniskelamin 
		FROM data_pendaftaran
		JOIN pendaftaranol_t ON data_pendaftaran.pendaftaran_id = pendaftaranol_t.pendaftaran_id
		JOIN pasien_m ON data_pendaftaran.pasien_id = pasien_m.pasien_id
		JOIN ruangan_m ON data_pendaftaran.ruangan_id = ruangan_m.ruangan_id
		WHERE pendaftaranol_t.pendaftaran_id IS NOT NULL
	), jenisruangan_722_724 AS (
		SELECT
			pendaftaran.pendaftaran_id,
			pendaftaran.tgl_pendaftaran,
			pendaftaran.instalasi_id,
			pendaftaran.ruangan_id,
			pendaftaran.jenis_ruangan,
			pendaftaran.status_periksa,
			pendaftaran.carabayar_id,
			pendaftaran.kunjungan,
			pendaftaran.pasien_id,
			pendaftaran.pasienpulang_id,
			pendaftaran.jeniskelamin 
		FROM pendaftaran
		WHERE pendaftaran.status_periksa NOT IN ('402','628')
		AND pendaftaran.jenis_ruangan IN (722::VARCHAR, 724::VARCHAR)
	), jenisruangan_723 AS (
		SELECT
			pendaftaran.pendaftaran_id,
			pendaftaran.tgl_pendaftaran,
			pendaftaran.instalasi_id,
			pendaftaran.ruangan_id,
			pendaftaran.jenis_ruangan,
			pendaftaran.status_periksa,
			pendaftaran.carabayar_id,
			pendaftaran.kunjungan,
			pendaftaran.pasien_id,
			pendaftaran.pasienpulang_id,
			pendaftaran.jeniskelamin 
		FROM pendaftaran
		WHERE pendaftaran.status_periksa NOT IN ('402','628')
		AND pendaftaran.jenis_ruangan = 723::VARCHAR
	), jenisruangan_725 AS (
		SELECT
			pendaftaran.pendaftaran_id,
			pendaftaran.tgl_pendaftaran,
			pendaftaran.instalasi_id,
			pendaftaran.ruangan_id,
			pendaftaran.jenis_ruangan,
			pendaftaran.status_periksa,
			pendaftaran.carabayar_id,
			pendaftaran.kunjungan,
			pendaftaran.pasien_id,
			pendaftaran.pasienpulang_id,
			pendaftaran.jeniskelamin 
		FROM pendaftaran
		JOIN pasienmasukpenunjang_t ON pendaftaran.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
		JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
		WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL
		AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL
	   	AND pasienmasukpenunjang_t.status_periksa::INTEGER NOT IN (476,477)
	   	AND pendaftaran.jenis_ruangan = 725::varchar
	), data_jadwal AS (
		SELECT 
			jadwalbukapoli_m.ruangan_id,
			COUNT(jadwalbukapoli_m.jadwalbukapoli_id) as jumlah_jadwal
		FROM jadwalbukapoli_m
		JOIN date_converter ON jadwalbukapoli_m.hari = date_converter.vidhari
		AND jadwalbukapoli_m.ruangan_id IN (SELECT master_ruangan.ruangan_id FROM master_ruangan)
		GROUP BY jadwalbukapoli_m.ruangan_id
	), summary AS (
		SELECT
			jenisruangan_722_724.pendaftaran_id,
			jenisruangan_722_724.tgl_pendaftaran,
			jenisruangan_722_724.instalasi_id,
			jenisruangan_722_724.ruangan_id,
			jenisruangan_722_724.jenis_ruangan,
			jenisruangan_722_724.status_periksa,
			jenisruangan_722_724.carabayar_id,
			jenisruangan_722_724.kunjungan,
			jenisruangan_722_724.pasien_id,
			jenisruangan_722_724.pasienpulang_id,
			jenisruangan_722_724.jeniskelamin 
		FROM jenisruangan_722_724
		UNION ALL
		SELECT
			jenisruangan_723.pendaftaran_id,
			jenisruangan_723.tgl_pendaftaran,
			jenisruangan_723.instalasi_id,
			jenisruangan_723.ruangan_id,
			jenisruangan_723.jenis_ruangan,
			jenisruangan_723.status_periksa,
			jenisruangan_723.carabayar_id,
			jenisruangan_723.kunjungan,
			jenisruangan_723.pasien_id,
			jenisruangan_723.pasienpulang_id,
			jenisruangan_723.jeniskelamin 
		FROM jenisruangan_723
		UNION ALL
		SELECT 
			jenisruangan_725.pendaftaran_id,
			jenisruangan_725.tgl_pendaftaran,
			jenisruangan_725.instalasi_id,
			jenisruangan_725.ruangan_id,
			jenisruangan_725.jenis_ruangan,
			jenisruangan_725.status_periksa,
			jenisruangan_725.carabayar_id,
			jenisruangan_725.kunjungan,
			jenisruangan_725.pasien_id,
			jenisruangan_725.pasienpulang_id,
			jenisruangan_725.jeniskelamin 
		FROM jenisruangan_725
	), count_data AS (
		SELECT
			summary.instalasi_id,
			summary.ruangan_id,
			summary.carabayar_id,
			count(summary.pendaftaran_id) filter (where summary.jeniskelamin = '15'::text and summary.kunjungan = '180'::text) as baru_laki,
			count(summary.pendaftaran_id) filter (where summary.jeniskelamin = '16'::text and summary.kunjungan = '180'::text) as baru_perempuan,
			count(summary.pendaftaran_id) filter (where summary.kunjungan = '180'::text) as jml_baru,
			count(summary.pendaftaran_id) filter (where summary.jeniskelamin = '15'::text and summary.kunjungan = '181'::text) as lama_laki,
			count(summary.pendaftaran_id) filter (where summary.jeniskelamin = '16'::text and summary.kunjungan = '181'::text) as lama_perempuan,
			count(summary.pendaftaran_id) filter (where summary.kunjungan = '181'::text) as jml_lama
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
		COALESCE(count_data.baru_laki, 0)::integer AS baru_laki, 
		COALESCE(count_data.baru_perempuan, 0)::integer AS baru_perempuan, 
		COALESCE(count_data.jml_baru, 0)::integer AS jml_baru, 
		COALESCE(count_data.lama_laki, 0)::integer AS lama_laki, 
		COALESCE(count_data.lama_perempuan, 0)::integer AS lama_perempuan, 
		COALESCE(count_data.jml_lama, 0)::integer AS jml_lama, 
		COALESCE(count_data.jml_baru, 0)::integer + COALESCE(count_data.jml_lama, 0)::integer AS kunjungan, 
		COALESCE(data_jadwal.jumlah_jadwal, 0)::integer AS hp, 
		0::integer AS adoa, 
		0::integer AS adoad, 
		0::integer AS adoapp 
	FROM master_ruangan
	left join count_data ON master_ruangan.instalasi_id = count_data.instalasi_id AND master_ruangan.ruangan_id = count_data.ruangan_id AND master_ruangan.carabayar_id = count_data.carabayar_id
	left join data_jadwal ON master_ruangan.ruangan_id = data_jadwal.ruangan_id
	ORDER BY
		master_ruangan.ruangan_nama ASC, 
		master_ruangan.carabayar_nama ASC;
	END;
$function$
;
