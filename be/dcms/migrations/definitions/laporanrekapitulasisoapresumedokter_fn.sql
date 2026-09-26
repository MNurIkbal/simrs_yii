CREATE OR REPLACE FUNCTION public.laporanrekapitulasisoapresumedokter_fn(xfirstdate date, xlastdate date, xjenis_laporan integer)
 RETURNS TABLE(instalasi_id integer, instalasi_nama character varying, ruangan_id integer, ruangan_nama character varying, pegawai_id integer, tgl_pendaftaran_filter character varying, nama_dokter character varying, jumlah_pasien double precision, jenis_laporan character varying)
 LANGUAGE plpgsql
AS $function$
	BEGIN
	IF(xjenis_laporan = 729::int4) THEN
		RETURN QUERY
		WITH data_pegawai AS (
			SELECT
				pegawai_m.pegawai_id,
				pegawai_m.nama_pegawai
			FROM pegawai_m
			WHERE kelompokpegawai_id = 1
			AND is_deleted = false
		), master_ruangan_instalasi AS (
			SELECT
				ruangan_m.ruangan_id,
				ruangan_m.ruangan_nama,
				ruangan_m.instalasi_id,
				instalasi_m.instalasi_nama
			FROM ruangan_m
			JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
		), data_pendaftaran AS (
			SELECT	
				to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::TEXT)::DATE AS tgl_pendaftaran,
				pendaftaran_t.no_pendaftaran,
				pendaftaran_t.pendaftaran_id,
				pendaftaran_t.pasienadmisi_id,
				pendaftaran_t.pegawai_id,
				pendaftaran_t.instalasi_id,
				pendaftaran_t.ruangan_id,
				pendaftaran_t.pasien_id 
			FROM pendaftaran_t
			WHERE pendaftaran_t.pasienbatalperiksa_id IS NULL
			AND pendaftaran_t.pasienpulang_id IS NOT NULL
			AND tgl_pendaftaran BETWEEN xfirstdate::DATE AND xlastdate::DATE
		), data_soaprj AS (
			SELECT 
				data_pendaftaran.tgl_pendaftaran,
				data_pendaftaran.no_pendaftaran,
				data_pendaftaran.pegawai_id,
				data_pendaftaran.ruangan_id,
				soaprj_t.soaprj_id 
			FROM data_pendaftaran
			left JOIN soaprj_t ON data_pendaftaran.pasien_id = soaprj_t.pasien_id AND soaprj_t.is_deleted = false
			WHERE data_pendaftaran.instalasi_id = 1
			AND data_pendaftaran.pegawai_id IS NOT NULL
			AND soaprj_t.soaprj_id IS NULL
		), data_cppt AS (
			SELECT
				data_pendaftaran.tgl_pendaftaran,
				data_pendaftaran.no_pendaftaran,
				data_pendaftaran.pegawai_id,
				data_pendaftaran.ruangan_id
			FROM data_pendaftaran
			LEFT JOIN cppt_t ON data_pendaftaran.pendaftaran_id = cppt_t.pendaftaran_id
			WHERE data_pendaftaran.instalasi_id = 2
			AND cppt_t.cppt_id IS NULL 
			AND data_pendaftaran.pegawai_id IS NOT NULL
			AND (cppt_t.cppt_id IS NULL OR cppt_t.pegawai_id = data_pendaftaran.pegawai_id)
		), data_pasienadmisi AS (
			SELECT 
				to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::TEXT)::DATE AS tgl_pendaftaran,
				pendaftaran_t.pendaftaran_id,
				pendaftaran_t.no_pendaftaran,
				pendaftaran_t.pasienadmisi_id,
				pasienadmisi_t.ruangan_id,
				COALESCE(cppt_t.pegawai_id, pasienadmisi_t.pegawai_id) AS pegawai_id,
				cppt_t.cppt_id,
				CASE
					WHEN pasienadmisi_t.pegawai_id = cppt_t.pegawai_id THEN 't'::TEXT
					ELSE 'f'::TEXT
				END AS ket
			FROM pendaftaran_t
			JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
			left JOIN cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND pendaftaran_t.pasienadmisi_id = cppt_t.pasienadmisi_id AND pasienadmisi_t.pegawai_id = cppt_t.pegawai_id
			WHERE pendaftaran_t.pasienbatalperiksa_id IS NULL 
			AND pasienadmisi_t.pasienpulang_id IS NOT NULL
			AND pasienadmisi_t.tgl_pendaftaran BETWEEN xfirstdate::DATE AND xlastdate::DATE
		), data_rj AS (
			SELECT
				'RJ'::TEXT AS jenis,
				data_soaprj.tgl_pendaftaran,
				data_soaprj.ruangan_id,
				data_pegawai.pegawai_id,
				data_pegawai.nama_pegawai AS dokter,
				data_soaprj.no_pendaftaran
			FROM data_pegawai
			JOIN data_soaprj ON data_soaprj.pegawai_id = data_pegawai.pegawai_id
		), data_rd AS (
			SELECT
				'RD'::TEXT AS jenis,
				data_cppt.tgl_pendaftaran,
				data_cppt.ruangan_id,
				data_pegawai.pegawai_id,
				data_pegawai.nama_pegawai AS dokter,
				data_cppt.no_pendaftaran
			FROM data_pegawai
			JOIN data_cppt ON data_cppt.pegawai_id = data_pegawai.pegawai_id	
		), data_ri AS (
			SELECT 
				'RI'::TEXT AS jenis,
				CASE
					WHEN data_pasienadmisi.cppt_id IS NOT NULL AND data_pasienadmisi.ket = 'f'::TEXT THEN data_pasienadmisi.tgl_pendaftaran
					WHEN data_pasienadmisi.cppt_id IS NULL AND data_pasienadmisi.ket = 'f'::TEXT THEN data_pasienadmisi.tgl_pendaftaran
					WHEN data_pasienadmisi.cppt_id IS NOT NULL THEN NULL::DATE
					ELSE NULL::DATE
				END AS tgl_pendaftaran,
				CASE
					WHEN data_pasienadmisi.cppt_id IS NOT NULL AND data_pasienadmisi.ket = 'f'::TEXT THEN data_pasienadmisi.ruangan_id
					WHEN data_pasienadmisi.cppt_id IS NULL AND data_pasienadmisi.ket = 'f'::TEXT THEN data_pasienadmisi.ruangan_id
					WHEN data_pasienadmisi.cppt_id IS NOT NULL THEN NULL::integer
					ELSE NULL::integer
				END AS ruangan_id,
				CASE
					WHEN data_pasienadmisi.cppt_id IS NOT NULL AND data_pasienadmisi.ket = 'f'::TEXT THEN data_pegawai.pegawai_id
					WHEN data_pasienadmisi.cppt_id IS NULL AND data_pasienadmisi.ket = 'f'::TEXT THEN data_pegawai.pegawai_id
					WHEN data_pasienadmisi.cppt_id IS NOT NULL THEN NULL::integer
					ELSE NULL::integer
				END AS pegawai_id,
				CASE
					WHEN data_pasienadmisi.cppt_id IS NOT NULL AND data_pasienadmisi.ket = 'f'::TEXT THEN data_pegawai.nama_pegawai
					WHEN data_pasienadmisi.cppt_id IS NULL AND data_pasienadmisi.ket = 'f'::TEXT THEN data_pegawai.nama_pegawai
					WHEN data_pasienadmisi.cppt_id IS NOT NULL THEN NULL::CHARACTER VARYING
					ELSE NULL::CHARACTER VARYING
				END AS dokter,
				CASE
					WHEN data_pasienadmisi.cppt_id IS NOT NULL AND data_pasienadmisi.ket = 'f'::TEXT THEN data_pasienadmisi.no_pendaftaran
					WHEN data_pasienadmisi.cppt_id IS NULL AND data_pasienadmisi.ket = 'f'::TEXT THEN data_pasienadmisi.no_pendaftaran
					WHEN data_pasienadmisi.cppt_id IS NOT NULL THEN NULL::CHARACTER VARYING
					ELSE NULL::CHARACTER VARYING
				END AS no_pendaftaran
			FROM data_pegawai
			JOIN data_pasienadmisi ON data_pegawai.pegawai_id = data_pasienadmisi.pegawai_id
		) SELECT
			master_ruangan_instalasi.instalasi_id::int4,
			master_ruangan_instalasi.instalasi_nama::CHARACTER VARYING,
			master_ruangan_instalasi.ruangan_id::int4,
			master_ruangan_instalasi.ruangan_nama::CHARACTER VARYING,
			union_data.pegawai_id::int4,
			(xfirstDATE::DATE || '/' || xlastDATE::DATE)::varchar as tgl_pendaftaran_filter,
			union_data.dokter::CHARACTER VARYING AS nama_dokter,
			count(no_pendaftaran)::float AS jumlah_pasien,
			729::CHARACTER VARYING AS jenis_laporan
		FROM (
			SELECT * FROM data_rj
			UNION ALL
			SELECT * FROM data_rd
			UNION ALL
			SELECT * FROM data_ri WHERE data_ri.tgl_pendaftaran is not NULL
		) AS union_data
		JOIN master_ruangan_instalasi ON union_data.ruangan_id = master_ruangan_instalasi.ruangan_id
		GROUP BY
			union_data.jenis,
			union_data.ruangan_id,
			union_data.pegawai_id,
			master_ruangan_instalasi.ruangan_id,
			master_ruangan_instalasi.ruangan_nama,
			master_ruangan_instalasi.instalasi_id,
			master_ruangan_instalasi.instalasi_nama,
			union_data.dokter,
			729::CHARACTER VARYING;
	ELSE 
		IF (xjenis_laporan = 730::int4) THEN
			RETURN QUERY
			WITH data_pegawai AS (
				SELECT
					pegawai_m.pegawai_id,
					pegawai_m.nama_pegawai
				FROM pegawai_m
				WHERE kelompokpegawai_id = 1
				AND is_deleted = false
			), master_ruangan_instalasi AS (
				SELECT
					ruangan_m.ruangan_id,
					ruangan_m.ruangan_nama,
					ruangan_m.instalasi_id,
					instalasi_m.instalasi_nama
				FROM ruangan_m
				JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
			), data_pendaftaran AS (
				SELECT	
					to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::TEXT)::DATE AS tgl_pendaftaran,
					pendaftaran_t.no_pendaftaran,
					pendaftaran_t.pendaftaran_id,
					pendaftaran_t.pasienadmisi_id,
					pendaftaran_t.pegawai_id,
					pendaftaran_t.instalasi_id,
					pendaftaran_t.ruangan_id,
					pendaftaran_t.pasien_id 
				FROM pendaftaran_t
				WHERE pendaftaran_t.pasienbatalperiksa_id IS null
				AND tgl_pendaftaran BETWEEN xfirstdate::DATE AND xlastdate::DATE
			), data_pasienadmisi as (
				SELECT	
					to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
			        pendaftaran_t.no_pendaftaran,
			        pasienadmisi_t.pegawai_id,
			        pasienadmisi_t.ruangan_id,
			        ruangan_m.instalasi_id 
				FROM pendaftaran_t
				LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
			 	LEFT JOIN resumemedis_t ON pendaftaran_t.pendaftaran_id = resumemedis_t.pendaftaran_id AND resumemedis_t.is_deleted = false
			 	JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
			    WHERE ruangan_m.instalasi_id = 3 
			    AND pendaftaran_t.pegawai_id IS NOT NULL 
			    AND resumemedis_t.resumemedis_id IS NULL 
			    AND pendaftaran_t.pasienbatalperiksa_id IS null
			    AND pasienadmisi_t.tgl_pendaftaran BETWEEN xfirstdate::DATE AND xlastdate::DATE
			), data_resumemedis as (
				select
					to_char(data_pendaftaran.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
			        data_pendaftaran.no_pendaftaran,
			        data_pendaftaran.pasienadmisi_id,
			        data_pendaftaran.pegawai_id,
			        data_pendaftaran.instalasi_id,
			        data_pendaftaran.ruangan_id
				from data_pendaftaran
				LEFT JOIN resumemedis_t ON data_pendaftaran.pendaftaran_id = resumemedis_t.pendaftaran_id AND resumemedis_t.is_deleted = false
				WHERE resumemedis_t.resumemedis_id IS null
				AND data_pendaftaran.pegawai_id IS NOT NULL 
			), data_resumemedis_rd as (
				SELECT 
					to_char(data_pendaftaran.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
			        data_pendaftaran.no_pendaftaran,
			            CASE
			                WHEN data_pendaftaran.pasienadmisi_id IS NULL THEN peg_rd.pegawai_id
			                ELSE peg_ri.pegawai_id
			            END AS pegawai_id,
			            CASE
			                WHEN resumemedisri_t.pasienadmisi_id IS NULL THEN rd.instalasi_id
			                WHEN kesimpulanrd_t.pendaftaran_id IS NULL THEN ri.instalasi_id
			                ELSE NULL::integer
			            END AS instalasi_id,
			            CASE
			                WHEN resumemedisri_t.pasienadmisi_id IS NULL THEN rd.ruangan_id
			                WHEN kesimpulanrd_t.pendaftaran_id IS NULL THEN ri.ruangan_id
			                ELSE NULL::integer
			            END AS ruangan_id
			       FROM data_pendaftaran
			         LEFT JOIN pasienadmisi_t ON data_pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
			         LEFT JOIN resumemedisri_t ON data_pendaftaran.pendaftaran_id = resumemedisri_t.pendaftaran_id
			         LEFT JOIN kesimpulanrd_t ON data_pendaftaran.pendaftaran_id = kesimpulanrd_t.pendaftaran_id
			         LEFT JOIN data_pegawai peg_rd ON data_pendaftaran.pegawai_id = peg_rd.pegawai_id
			         LEFT JOIN data_pegawai peg_ri ON pasienadmisi_t.pegawai_id = peg_ri.pegawai_id
			         LEFT JOIN ruangan_m rd ON data_pendaftaran.ruangan_id = rd.ruangan_id
			         LEFT JOIN ruangan_m ri ON pasienadmisi_t.ruangan_id = ri.ruangan_id
			         LEFT JOIN instalasi_m ins_rd ON rd.instalasi_id = ins_rd.instalasi_id
			         LEFT JOIN instalasi_m ins_ri ON ri.instalasi_id = ins_ri.instalasi_id
			      WHERE resumemedisri_t.resumemedisri_id IS NULL 
			      AND kesimpulanrd_t.kesimpulanrd_id IS NULL 
			      AND data_pendaftaran.instalasi_id <> 1
			), data_rj as (
				select
					data_resumemedis.tgl_pendaftaran,
					data_resumemedis.no_pendaftaran,
			        data_resumemedis.pegawai_id,
			        data_pegawai.nama_pegawai AS dokter,
			        data_resumemedis.instalasi_id,
			        data_resumemedis.ruangan_id
				from data_pegawai
				join data_resumemedis ON data_pegawai.pegawai_id = data_resumemedis.pegawai_id
				where data_resumemedis.instalasi_id = 1
			), data_ri as (
				select
					data_pasienadmisi.tgl_pendaftaran,
					data_pasienadmisi.no_pendaftaran,
			        data_pasienadmisi.pegawai_id,
			        data_pegawai.nama_pegawai AS dokter,
			        data_pasienadmisi.instalasi_id,
			        data_pasienadmisi.ruangan_id
				from data_pegawai
				join data_pasienadmisi ON data_pegawai.pegawai_id = data_pasienadmisi.pegawai_id
			), data_rd as (
				select
					data_resumemedis_rd.tgl_pendaftaran,
					data_resumemedis_rd.no_pendaftaran,
			        data_resumemedis_rd.pegawai_id,
			        data_pegawai.nama_pegawai AS dokter,
			        data_resumemedis_rd.instalasi_id,
			        data_resumemedis_rd.ruangan_id
				from data_pegawai
				join data_resumemedis_rd ON data_pegawai.pegawai_id = data_resumemedis_rd.pegawai_id
			) SELECT
				master_ruangan_instalasi.instalasi_id::int4,
				master_ruangan_instalasi.instalasi_nama::CHARACTER VARYING,
				master_ruangan_instalasi.ruangan_id::int4,
				master_ruangan_instalasi.ruangan_nama::CHARACTER VARYING,
				union_data.pegawai_id::int4,
				(xfirstDATE::DATE || '/' || xlastDATE::DATE)::varchar as tgl_pendaftaran_filter,
				union_data.dokter::CHARACTER VARYING AS nama_dokter,
				count(no_pendaftaran)::float AS jumlah_pasien,
				730::CHARACTER VARYING AS jenis_laporan
			FROM (
				SELECT * FROM data_rj
				UNION ALL
				SELECT * FROM data_rd
				UNION ALL
				SELECT * FROM data_ri
			) AS union_data
			JOIN master_ruangan_instalasi ON union_data.ruangan_id = master_ruangan_instalasi.ruangan_id
			GROUP BY
				union_data.ruangan_id,
				union_data.pegawai_id,
				master_ruangan_instalasi.ruangan_id,
				master_ruangan_instalasi.ruangan_nama,
				master_ruangan_instalasi.instalasi_id,
				master_ruangan_instalasi.instalasi_nama,
				union_data.dokter,
				730::CHARACTER VARYING;
		ELSE
			RETURN QUERY
			SELECT
				NULL::int4 AS instalasi_id,
				NULL::CHARACTER VARYING AS instalasi_nama,
				NULL::int4 AS ruangan_id,
				NULL::CHARACTER VARYING AS ruangan_nama,
				NULL::int4 AS pegawai_id,
				NULL::CHARACTER VARYING AS nama_dokter,
				NULL::FLOAT AS jumlah_pasien,
				NULL::int4 AS jenis_laporan;
		END IF;
	END IF;
END;
$function$
;
