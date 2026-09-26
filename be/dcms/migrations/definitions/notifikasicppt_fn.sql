-- DROP FUNCTION public.notifikasicppt_fn(int4);

CREATE OR REPLACE FUNCTION public.notifikasicppt_fn(xpegawai_id integer)
 RETURNS TABLE(nama_pasien character varying, no_pendaftaran character varying, pendaftaran_id integer, tgl_cppt timestamp without time zone, ruangan_id integer, jenis_pendaftaran character varying, instalasi_id integer, cppt_id integer)
 LANGUAGE sql
 STABLE
AS $function$
		WITH
			datacppt AS (
				SELECT 
					cppt_t.pendaftaran_id, 
					cppt_t.tgl_cppt, 
					cppt_t.cppt_id, 
					cppt_t.pegawai_id, 
					cppt_t.ruangan_id, 
					cppt_t.created_date, 
					cppt_t.subject, 
					cppt_t.object, 
					cppt_t.a_diag_utama, 
					cppt_t.planning
				FROM cppt_t
				WHERE cppt_t.pegawai_id = xpegawai_id
				AND cppt_t.is_deleted = FALSE
				AND (cppt_t.additional_data::json->>'via_soap')::boolean IS TRUE
			),
			datasoaprj AS (
				SELECT
					soaprj_t.pendaftaran_id,
					soaprj_t.soaprj_id AS cppt_id,
					soaprj_t.tgl_soaprj AS tgl_cppt,
					soaprj_t.pegawai_id,
					soaprj_t.ruangan_id,
					soaprj_t.created_date,
					soaprj_t.subject,
					soaprj_t.object,
					soaprj_t.a_diag_utama,
					soaprj_t.planning
				FROM soaprj_t
				WHERE soaprj_t.pegawai_id = xpegawai_id
				AND soaprj_t.is_deleted = FALSE
				AND (soaprj_t.additional_data::json->>'via_soap')::boolean IS TRUE
			),
			cpptdistinct AS (
				SELECT 
					distinct on (datacppt.pendaftaran_id) datacppt.pendaftaran_id,
					datacppt.tgl_cppt,
		            datacppt.cppt_id,
		            datacppt.ruangan_id
				FROM datacppt
			),
			soaprjdistinct AS (
				SELECT 
					distinct on (datasoaprj.pendaftaran_id) datasoaprj.pendaftaran_id,
					datasoaprj.cppt_id,
		            datasoaprj.tgl_cppt,
		            datasoaprj.ruangan_id
				FROM datasoaprj
			),
			cppt_belum AS (
				SELECT 
					datacppt.pendaftaran_id, 
					count(datacppt.cppt_id) AS ct_belum
				FROM datacppt
				WHERE COALESCE(datacppt.subject, ''::text) = ''::text
				OR COALESCE(datacppt.object, ''::text) = ''::text
				OR COALESCE(datacppt.planning, ''::text) = ''::text
				OR COALESCE(datacppt.a_diag_utama::text, ''::text) = ''::text
				OR datacppt.subject = '-'::text
				OR datacppt.object = '-'::text
				OR datacppt.planning = '-'::text
				OR datacppt.a_diag_utama::text = '-'::text
				GROUP BY datacppt.pendaftaran_id
			),
			soaprj_belum AS (
				SELECT 
					datasoaprj.pendaftaran_id, 
					count(datasoaprj.cppt_id) AS ct_belum
				FROM datasoaprj
				WHERE COALESCE(datasoaprj.subject, ''::text) = ''::text
				OR COALESCE(datasoaprj.object, ''::text) = ''::text
				OR COALESCE(datasoaprj.planning, ''::text) = ''::text
				OR COALESCE(datasoaprj.a_diag_utama::text, ''::text) = ''::text
				OR datasoaprj.subject = '-'::text
				OR datasoaprj.object = '-'::text
				OR datasoaprj.planning = '-'::text
				OR datasoaprj.a_diag_utama::text = '-'::text
				GROUP BY datasoaprj.pendaftaran_id
			),
			pendaftaranrd AS (
				SELECT 
					'RD'::VARCHAR AS jenis_pendaftaran,
					pendaftaran.pendaftaran_id,
					pendaftaran.no_pendaftaran,
					pendaftaran.ruangan_id,
					pendaftaran.pasien_id
				FROM (
					select
						pendaftaran_t.pendaftaran_id,
						pendaftaran_t.no_pendaftaran,
						pendaftaran_t.ruangan_id,
						pendaftaran_t.instalasi_id,
						pendaftaran_t.status_periksa,
						pendaftaran_t.pasien_id	
					from pendaftaran_t 
					where pendaftaran_t.is_deleted = false
				) pendaftaran
				WHERE pendaftaran.instalasi_id = 2
				AND pendaftaran.status_periksa = '2'
				AND pendaftaran.pendaftaran_id IN (SELECT cppt_belum.pendaftaran_id FROM cppt_belum)
			),
			pendaftaranri AS (
				SELECT 
					'RI'::VARCHAR AS jenis_pendaftaran,
					pendaftaran.pendaftaran_id,
					pendaftaran.no_pendaftaran,
					pendaftaran.ruangan_id,
					pendaftaran.pasien_id
				FROM (
					select
						pendaftaran_t.pendaftaran_id,
						pendaftaran_t.no_pendaftaran,
						pendaftaran_t.ruangan_id,
						pendaftaran_t.pasien_id,
						pendaftaran_t.pasienadmisi_id 
					from pendaftaran_t 
					where pendaftaran_t.is_deleted = false
				) pendaftaran
				JOIN (
					SELECT pasienadmisi_t.pasienadmisi_id
					FROM pasienadmisi_t 
					WHERE pasienadmisi_t.status_ranap = 441
					AND pasienadmisi_t.pegawai_id = xpegawai_id
				) pasienadmisi ON pendaftaran.pasienadmisi_id = pasienadmisi.pasienadmisi_id
				WHERE pendaftaran.pendaftaran_id IN (SELECT cppt_belum.pendaftaran_id FROM cppt_belum)
			),
			pendaftaranrj AS (
				SELECT 
					'RJ'::VARCHAR AS jenis_pendaftaran,
					pendaftaran.pendaftaran_id,
					pendaftaran.no_pendaftaran,
					pendaftaran.ruangan_id,
					pendaftaran.pasien_id
				FROM (
					select
						pendaftaran_t.pendaftaran_id,
						pendaftaran_t.no_pendaftaran,
						pendaftaran_t.ruangan_id,
						pendaftaran_t.instalasi_id,
						pendaftaran_t.status_periksa,
						pendaftaran_t.pasien_id
					from pendaftaran_t 
					where pendaftaran_t.is_deleted = false
				) pendaftaran
				WHERE pendaftaran.instalasi_id = 1
				AND pendaftaran.status_periksa = '2'
				AND pendaftaran.pendaftaran_id IN (SELECT soaprj_belum.pendaftaran_id FROM soaprj_belum)
			),
			pendaftaranrd_pegawai AS (
				SELECT 
					'RD'::VARCHAR AS jenis_pendaftaran,
					pendaftaran.pendaftaran_id,
					pendaftaran.no_pendaftaran,
					pendaftaran.ruangan_id,
					pendaftaran.pasien_id
				FROM (
					select
						pendaftaran_t.pendaftaran_id,
						pendaftaran_t.no_pendaftaran,
						pendaftaran_t.ruangan_id,
						pendaftaran_t.instalasi_id,
						pendaftaran_t.status_periksa,
						pendaftaran_t.pegawai_id,
						pendaftaran_t.pasien_id
					from pendaftaran_t 
					where pendaftaran_t.is_deleted = false
				) pendaftaran
				WHERE pendaftaran.instalasi_id = 2
				AND pendaftaran.status_periksa = '2'
				AND pendaftaran.pegawai_id = xpegawai_id
				AND pendaftaran.pendaftaran_id NOT IN (SELECT datacppt.pendaftaran_id FROM datacppt)
			),
			pendaftaranri_pegawai AS (
				SELECT 
					'RI'::VARCHAR AS jenis_pendaftaran,
					pendaftaran.pendaftaran_id,
					pendaftaran.no_pendaftaran,
					pendaftaran.ruangan_id,
					pendaftaran.pasien_id
				FROM (
					select
						pendaftaran_t.pendaftaran_id,
						pendaftaran_t.no_pendaftaran,
						pendaftaran_t.ruangan_id,
						pendaftaran_t.pasien_id,
						pendaftaran_t.pasienadmisi_id
					from pendaftaran_t 
					left join datacppt on pendaftaran_t.pendaftaran_id = datacppt.pendaftaran_id
					where pendaftaran_t.is_deleted = false
					and pendaftaran_t.is_active = true
					and datacppt.pendaftaran_id is null
				) pendaftaran
				JOIN (
					SELECT pasienadmisi_id
					FROM pasienadmisi_t 
					WHERE pasienadmisi_t.status_ranap = 441
					AND pasienadmisi_t.pegawai_id = xpegawai_id
				) pasienadmisi ON pendaftaran.pasienadmisi_id = pasienadmisi.pasienadmisi_id
			),
			pendaftaranrj_pegawai AS (
				SELECT 
					'RJ'::VARCHAR AS jenis_pendaftaran,
					pendaftaran.pendaftaran_id,
					pendaftaran.no_pendaftaran,
					pendaftaran.ruangan_id,
					pendaftaran.pasien_id
				FROM (select * from pendaftaran_t where pendaftaran_t.is_deleted = FALSE) pendaftaran
				WHERE pendaftaran.instalasi_id = 1
				AND pendaftaran.status_periksa = '2'
				AND pendaftaran.pegawai_id = xpegawai_id
				AND pendaftaran.pendaftaran_id NOT IN (SELECT datasoaprj.pendaftaran_id FROM datasoaprj)
			),
			datarird AS (
				SELECT
					datapendaftaran.jenis_pendaftaran,
					pasien_m.nama_pasien,
					datapendaftaran.no_pendaftaran,
					datapendaftaran.pendaftaran_id,
					cpptdistinct.tgl_cppt,
					cpptdistinct.cppt_id,
					ruangan_m.ruangan_id,
					ruangan_m.instalasi_id,
					CASE COALESCE(cpptdistinct.pendaftaran_id, 0)
						WHEN 0 THEN TRUE
						ELSE CASE COALESCE(cppt_belum.ct_belum, 0)
							WHEN 0 THEN FALSE
							ELSE TRUE
						END
					END AS is_belum
				FROM (
					SELECT * FROM pendaftaranrd
					UNION ALL
					SELECT * FROM pendaftaranri
					UNION ALL
					SELECT * FROM pendaftaranrd_pegawai
					UNION ALL
					SELECT * FROM pendaftaranri_pegawai
				) AS datapendaftaran
				LEFT JOIN cppt_belum ON datapendaftaran.pendaftaran_id = cppt_belum.pendaftaran_id
				LEFT JOIN cpptdistinct ON datapendaftaran.pendaftaran_id = cpptdistinct.pendaftaran_id
				LEFT JOIN pasien_m ON datapendaftaran.pasien_id = pasien_m.pasien_id
				LEFT JOIN ruangan_m ON coalesce(datapendaftaran.ruangan_id, cpptdistinct.ruangan_id) = ruangan_m.ruangan_id
			),
			datarj AS (
				SELECT
					datapendaftaran.jenis_pendaftaran,
					pasien_m.nama_pasien,
					datapendaftaran.no_pendaftaran,
					datapendaftaran.pendaftaran_id,
					soaprjdistinct.tgl_cppt,
					soaprjdistinct.cppt_id,
					ruangan_m.ruangan_id,
					ruangan_m.instalasi_id,
					CASE COALESCE(soaprjdistinct.pendaftaran_id, 0)
						WHEN 0 THEN TRUE
						ELSE CASE COALESCE(soaprj_belum.ct_belum, 0)
							WHEN 0 THEN FALSE
							ELSE TRUE
						END
					END AS is_belum
				FROM (
					SELECT * FROM pendaftaranrj
					UNION ALL
					SELECT * FROM pendaftaranrj_pegawai
				) AS datapendaftaran
				LEFT JOIN soaprj_belum ON datapendaftaran.pendaftaran_id = soaprj_belum.pendaftaran_id
				LEFT JOIN soaprjdistinct ON datapendaftaran.pendaftaran_id = soaprjdistinct.pendaftaran_id
				LEFT JOIN pasien_m ON datapendaftaran.pasien_id = pasien_m.pasien_id
				LEFT JOIN ruangan_m ON coalesce(datapendaftaran.ruangan_id, soaprjdistinct.ruangan_id) = ruangan_m.ruangan_id
			)
		SELECT
			datasource.nama_pasien::varchar,
			datasource.no_pendaftaran::varchar,
			datasource.pendaftaran_id::integer,
			datasource.tgl_cppt::timestamp,
			datasource.ruangan_id::integer,
			datasource.jenis_pendaftaran::varchar,
			datasource.instalasi_id::integer,
			datasource.cppt_id::integer
		FROM (
				SELECT * FROM datarird
				UNION ALL
				SELECT * FROM datarj
			) AS datasource
		WHERE is_belum IS TRUE;
$function$
;