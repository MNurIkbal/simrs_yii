-- DROP FUNCTION public.get_reservasi(varchar);

CREATE OR REPLACE FUNCTION public.get_reservasi(xnoreservasi character varying)
 RETURNS TABLE(kodebooking character varying, kodebooking_bpjs character varying, pendaftaran_id integer, pendaftaranol_id integer, no_pendaftaran character varying, nama_pasien character varying, jenis_pasien character varying, nopeserta_bpjs character varying, nomorreferensi character varying, tgl_pendaftaran timestamp without time zone, task1 text, task2 text, task3 text, task4 text, task5 text, task6 text, task7 text, task99 text, noantrean character varying, status character varying, status_pasien character varying, tgl_mulai_antrian timestamp without time zone, tgl_mulai_pendaftaran timestamp without time zone, tgl_selesai_pendaftaran timestamp without time zone, tgl_masukperiksa timestamp without time zone, tgl_selesaiperiksa timestamp without time zone, tgl_order_resep timestamp without time zone, tgl_created_soap_by_dpjp timestamp without time zone, tgl_created_resumemedis timestamp without time zone, tgl_tindaklanjut_pasien timestamp without time zone, tgl_cetak_etiket timestamp without time zone, tgl_serahkan_resep timestamp without time zone, is_success_antrean boolean)
 LANGUAGE plpgsql
AS $function$
	DECLARE
		xPendaftaranId INTEGER;
	BEGIN
		IF
			( xnoreservasi ~* '\mRSV' = TRUE ) THEN
			SELECT
				xx.pendaftaran_id INTO xPendaftaranId 
			FROM
				pendaftaranol_t xx 
			WHERE
				xx.no_pendaftaranol = xnoreservasi;
			ELSE xPendaftaranId := regexp_replace( xnoreservasi, '^[^0-9]*([0-9]+).*$', '\1' );
			
		END IF;
		RETURN QUERY WITH pendaftaran_relation_cte AS (
			SELECT COALESCE
				( pt.no_pendaftaranol, CONCAT ( 'RS', pendaftaran_t.pendaftaran_id ) :: VARCHAR ) AS kodebooking,
				COALESCE ( pt.pendaftaran_id, pendaftaran_t.pendaftaran_id ) AS pendaftaran_id,
				pt.pendaftaranol_id,
				COALESCE ( pendaftaran_t.no_pendaftaran, 'Belum Daftar' ) AS no_pendaftaran,
				pt.status_pasien :: VARCHAR AS status_pasien,
				pendaftaran_t.tgl_pendaftaran,
				COALESCE ( pt.tgl_checkin, pt.tgl_pendaftaranol, pendaftaran_t.tgl_pendaftaran ) AS tgl_mulai_antrian,
				pendaftaran_t.created_date AS tgl_selesai_pendaftaran,
				pendaftaran_t.tgl_masukperiksa,
				pendaftaran_t.tgl_selesaiperiksa,
				COALESCE ( pt.pasien_id, pendaftaran_t.pasien_id ) AS pasien_id,
				COALESCE ( pt.antrian_id, pendaftaran_t.antrian_id ) AS antrian_id,
				row_to_json ( pendaftaran_t.* ) AS detail_pendaftaran,
				row_to_json ( pt.* ) AS detail_pendaftaranol 
			FROM
				pendaftaran_t
				LEFT JOIN (
				SELECT
					pendaftaranol_t.pendaftaran_id,
					pendaftaranol_t.pendaftaranol_id,
					pendaftaranol_t.pasien_id,
					pendaftaranol_t.antrian_id,
					pendaftaranol_t.no_pendaftaranol,
					pendaftaranol_t.tgl_checkin,
					pendaftaranol_t.tgl_pendaftaranol,
					pendaftaranol_t.status_pasien 
				FROM
					pendaftaranol_t 
				) pt ON pendaftaran_t.pendaftaran_id = pt.pendaftaran_id 
			WHERE
				pt.pendaftaran_id =xPendaftaranId OR pendaftaran_t.pendaftaran_id=xPendaftaranId 
				AND pendaftaran_t.instalasi_id = 1 
			),
			pasienpulang_cte AS (
			SELECT
				pasienpulang_t.pendaftaran_id,
				MIN ( pasienpulang_t.tglpasienpulang ) AS tgl_pasien_pulang 
			FROM
				pasienpulang_t 
			WHERE
				pasienpulang_t.pendaftaran_id = ( SELECT pendaftaran_relation_cte.pendaftaran_id FROM pendaftaran_relation_cte ) 
			GROUP BY
				pasienpulang_t.pendaftaran_id 
			),
			resep_cte AS (
			SELECT A
				.pendaftaran_id,
				MIN ( CASE WHEN reseptur_t.tglreseptur IS NOT NULL THEN reseptur_t.tglreseptur ELSE A.tglresep END ) AS tglreseptur,
				MIN ( CASE WHEN reseptur_t.tgl_cetak_etiket IS NOT NULL THEN reseptur_t.tgl_cetak_etiket ELSE A.tgl_cetak_etiket END ) AS tgl_cetak_etiket,
				MIN ( A.tgl_menyerahkan ) AS tgl_menyerahkan 
			FROM
				penjualanresep_t
				A LEFT JOIN ( SELECT b.penjualanresep_id, b.tglreseptur, b.tgl_cetak_etiket FROM reseptur_t b ) reseptur_t ON A.penjualanresep_id = reseptur_t.penjualanresep_id 
			WHERE
				A.pendaftaran_id = ( SELECT pendaftaran_relation_cte.pendaftaran_id FROM pendaftaran_relation_cte ) 
				AND A.is_deleted = FALSE 
			GROUP BY
				A.pendaftaran_id 
			),
			soap_cte AS (
			SELECT
				st.pendaftaran_id,
				st.created_date AS tgl_created_by_dpjp 
			FROM
				(
				SELECT
					st.pendaftaran_id,
					st.created_date,
					ROW_NUMBER ( ) OVER ( PARTITION BY st.pendaftaran_id ORDER BY st.created_date ASC ) AS rn 
				FROM
					soaprj_t st
					JOIN pegawai_m pm ON pm.pegawai_id = st.pegawai_id 
				WHERE
					st.pendaftaran_id = ( SELECT pendaftaran_relation_cte.pendaftaran_id FROM pendaftaran_relation_cte ) 
					AND ( st.additional_data :: JSON ->> 'via_soap' ) = 'true' 
					AND st.is_deleted = FALSE 
					AND pm.kelompokpegawai_id = 1 
				) st 
			WHERE
				st.rn = 1 
			),
			resumemedis_cte AS (
			SELECT
				rt.pendaftaran_id,
				rt.created_date 
			FROM
				resumemedisri_t rt 
			WHERE
				rt.pendaftaran_id = ( SELECT pendaftaran_relation_cte.pendaftaran_id FROM pendaftaran_relation_cte ) 
				AND rt.is_deleted = FALSE 
			),
			antrian_t_cte AS (
			SELECT A
				.antrian_id,
				L.lookup_name AS jenis_cara_bayar,
				ajr.jenis_cara_bayar AS jenis_cara_bayar_id,
				ajr.nomorreferensi 
			FROM
				antrian_t
				A LEFT JOIN antrianjkn_r ajr ON ajr.antrian_id = A.antrian_id
				LEFT JOIN lookup_m L ON L.lookup_id = ajr.jenis_cara_bayar 
			WHERE
				A.antrian_id = ( SELECT pendaftaran_relation_cte.antrian_id FROM pendaftaran_relation_cte ) 
			),
			statusbpjs_cte AS (
			SELECT
				batt.kodebooking,
				MAX ( bltt.wakturs :: TEXT ) FILTER ( WHERE bltt.taskid = 1 ) AS task1,
				MAX ( bltt.wakturs :: TEXT ) FILTER ( WHERE bltt.taskid = 2 ) AS task2,
				MAX ( bltt.wakturs :: TEXT ) FILTER ( WHERE bltt.taskid = 3 ) AS task3,
				MAX ( bltt.wakturs :: TEXT ) FILTER ( WHERE bltt.taskid = 4 ) AS task4,
				MAX ( bltt.wakturs :: TEXT ) FILTER ( WHERE bltt.taskid = 5 ) AS task5,
				MAX ( bltt.wakturs :: TEXT ) FILTER ( WHERE bltt.taskid = 6 ) AS task6,
				MAX ( bltt.wakturs :: TEXT ) FILTER ( WHERE bltt.taskid = 7 ) AS task7,
				MAX ( bltt.wakturs :: TEXT ) FILTER ( WHERE bltt.taskid = 99 ) AS task99,
				batt.noantrean,
				batt.status,
				bltt.is_deleted AS taskdeleted 
			FROM
				bpjs_list_task_t bltt
				RIGHT JOIN bpjs_antrian_tanggal_t batt ON batt.kodebooking :: TEXT = bltt.kodebooking :: TEXT 
				AND ( bltt.is_deleted IS NULL OR bltt.is_deleted IS FALSE ) 
			WHERE
				batt.kodebooking :: TEXT = ( SELECT pendaftaran_relation_cte.kodebooking FROM pendaftaran_relation_cte ) 
				AND batt.is_deleted IS FALSE 
			GROUP BY
				batt.kodebooking,
				batt.noantrean,
				batt.status,
				bltt.is_deleted 
			),
			pendaftaran_final_cte AS (
			SELECT
				pendaftaran.kodebooking,
				pendaftaran.pendaftaran_id,
				pendaftaran.pendaftaranol_id,
				pendaftaran.no_pendaftaran,
				pendaftaran.status_pasien,
				pasien_m.nama_pasien,
				antrian_t.jenis_cara_bayar AS jenis_pasien,
				pasien_m.nopeserta_bpjs,
				antrian_t.nomorreferensi,
				pendaftaran.tgl_pendaftaran,
				pendaftaran.tgl_mulai_antrian,
				NULL :: TIMESTAMP AS tgl_mulai_pendaftaran,
				pendaftaran.tgl_selesai_pendaftaran,
				pendaftaran.tgl_masukperiksa,
				pendaftaran.tgl_selesaiperiksa,
				resep_cte.tglreseptur AS tgl_order_resep,
				resep_cte.tgl_cetak_etiket,
				resep_cte.tgl_menyerahkan AS tgl_serahkan_resep,
				soap_cte.tgl_created_by_dpjp,
				resumemedis_cte.created_date AS tgl_created_resumemedis,
				pasienpulang_cte.tgl_pasien_pulang AS tgl_tindaklanjut_pasien,
				pendaftaran.detail_pendaftaran,
				pendaftaran.detail_pendaftaranol 
			FROM
				pendaftaran_relation_cte pendaftaran
				LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran.pasien_id
				LEFT JOIN antrian_t_cte antrian_t ON antrian_t.antrian_id = pendaftaran.antrian_id
				LEFT JOIN resep_cte ON resep_cte.pendaftaran_id = pendaftaran.pendaftaran_id
				LEFT JOIN soap_cte ON soap_cte.pendaftaran_id = pendaftaran.pendaftaran_id
				LEFT JOIN resumemedis_cte ON resumemedis_cte.pendaftaran_id = pendaftaran.pendaftaran_id
				LEFT JOIN pasienpulang_cte ON pasienpulang_cte.pendaftaran_id = pendaftaran.pendaftaran_id 
			) SELECT
			pendaftaran.kodebooking,
			statusbpjs_cte.kodebooking AS kodebooking_bpjs,
			pendaftaran.pendaftaran_id,
			pendaftaran.pendaftaranol_id,
			pendaftaran.no_pendaftaran,
			pendaftaran.nama_pasien,
			pendaftaran.jenis_pasien,
			pendaftaran.nopeserta_bpjs,
			pendaftaran.nomorreferensi,
			pendaftaran.tgl_pendaftaran,
			statusbpjs_cte.task1,
			statusbpjs_cte.task2,
			statusbpjs_cte.task3,
			statusbpjs_cte.task4,
			statusbpjs_cte.task5,
			statusbpjs_cte.task6,
			statusbpjs_cte.task7,
			statusbpjs_cte.task99,
			statusbpjs_cte.noantrean,
			statusbpjs_cte.status,
			pendaftaran.status_pasien,
			pendaftaran.tgl_mulai_antrian,
			pendaftaran.tgl_mulai_pendaftaran,
			pendaftaran.tgl_selesai_pendaftaran,
			pendaftaran.tgl_masukperiksa,
			pendaftaran.tgl_selesaiperiksa,
			pendaftaran.tgl_order_resep,
			pendaftaran.tgl_created_by_dpjp AS tgl_created_soap_by_dpjp,
		CASE
			WHEN pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_by_dpjp THEN
				pendaftaran.tgl_created_by_dpjp + INTERVAL '00:05:00' ELSE pendaftaran.tgl_created_resumemedis 
			END AS tgl_created_resumemedis,
		
		CASE
			WHEN pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_by_dpjp 
			AND ( pendaftaran.tgl_created_by_dpjp + INTERVAL '00:05:00' ) > pendaftaran.tgl_tindaklanjut_pasien THEN
				pendaftaran.tgl_created_by_dpjp + INTERVAL '00:10:00' ELSE pendaftaran.tgl_tindaklanjut_pasien 
			END AS tgl_tindaklanjut_pasien,
		
		CASE
			WHEN pendaftaran.tgl_created_resumemedis IS NULL 
			OR pendaftaran.tgl_created_by_dpjp IS NULL 
			OR pendaftaran.tgl_tindaklanjut_pasien IS NULL THEN
				NULL :: TIMESTAMP 
			WHEN pendaftaran.tgl_cetak_etiket < pendaftaran.tgl_tindaklanjut_pasien THEN
				CASE	
					WHEN pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_by_dpjp 
					AND ( pendaftaran.tgl_created_by_dpjp + INTERVAL '00:05:00' ) > pendaftaran.tgl_tindaklanjut_pasien THEN
						pendaftaran.tgl_created_by_dpjp + INTERVAL '00:15:00' ELSE pendaftaran.tgl_cetak_etiket 
				END
			ELSE pendaftaran.tgl_cetak_etiket 
			END AS tgl_cetak_etiket,
		
		CASE				
			WHEN pendaftaran.tgl_created_resumemedis IS NULL 
			OR pendaftaran.tgl_created_by_dpjp IS NULL 
			OR pendaftaran.tgl_tindaklanjut_pasien IS NULL 
			OR pendaftaran.tgl_cetak_etiket IS NULL THEN
				NULL :: TIMESTAMP 
			WHEN pendaftaran.tgl_cetak_etiket < pendaftaran.tgl_tindaklanjut_pasien THEN
				pendaftaran.tgl_tindaklanjut_pasien + INTERVAL '00:15:00' 
			WHEN pendaftaran.tgl_serahkan_resep < pendaftaran.tgl_cetak_etiket 
				OR pendaftaran.tgl_serahkan_resep < ( pendaftaran.tgl_tindaklanjut_pasien + INTERVAL '00:05:00' ) THEN
				pendaftaran.tgl_cetak_etiket + INTERVAL '00:10:00' 
			ELSE pendaftaran.tgl_serahkan_resep 
			END AS tgl_serahkan_resep,
        CASE
            WHEN statusbpjs_cte.kodebooking IS NULL THEN false
            ELSE true
        END AS is_success_antrean

		FROM
			pendaftaran_final_cte pendaftaran
			LEFT JOIN statusbpjs_cte ON statusbpjs_cte.kodebooking :: TEXT = pendaftaran.kodebooking :: TEXT 
		ORDER BY
			pendaftaran.pendaftaran_id DESC NULLS LAST;
END
$function$
;
