CREATE OR REPLACE FUNCTION public.bpjs_info_antrean_fn(p_tgl_mulai_antrian date DEFAULT NULL::date, p_daftar_from date DEFAULT NULL::date, p_daftar_to date DEFAULT NULL::date)
 RETURNS TABLE(kodebooking character varying, kodebooking_bpjs character varying, pendaftaran_id integer, pendaftaranol_id integer, no_pendaftaran character varying, nama_pasien character varying, jenis_pasien character varying, nopeserta_bpjs character varying, nomorreferensi character varying, tgl_pendaftaran timestamp without time zone, task1 text, task2 text, task3 text, task4 text, task5 text, task6 text, task7 text, task99 text, noantrean character varying, status character varying, status_pasien character varying, tgl_mulai_antrian timestamp without time zone, tgl_mulai_pendaftaran timestamp without time zone, tgl_selesai_pendaftaran timestamp without time zone, tgl_masukperiksa timestamp without time zone, tgl_selesaiperiksa timestamp without time zone, tgl_order_resep timestamp without time zone, tgl_created_soap_by_dpjp timestamp without time zone, tgl_created_resumemedis timestamp without time zone, tgl_tindaklanjut_pasien timestamp without time zone, tgl_cetak_etiket timestamp without time zone, tgl_serahkan_resep timestamp without time zone, is_success_antrean boolean)
 LANGUAGE plpgsql
AS $function$
BEGIN

  IF p_tgl_mulai_antrian IS NOT NULL THEN
    RETURN QUERY
    WITH datapendaftaran AS (
	    SELECT
	        COALESCE(
	            pendaftaranol_t.no_pendaftaranol, 
	            concat('RS', pendaftaran_t.pendaftaran_id
	        )::CHARACTER VARYING) AS kodebooking,
	        COALESCE(
	            pendaftaranol_t.pendaftaran_id,
	            pendaftaran_t.pendaftaran_id
	        ) AS pendaftaran_id,
	        pendaftaranol_t.pendaftaranol_id,
	        COALESCE(
	            pendaftaran_t.no_pendaftaran, 
	            'Belum Daftar'::CHARACTER VARYING
	        ) AS no_pendaftaran,
	        pendaftaranol_t.status_pasien::CHARACTER VARYING AS status_pasien,
	        pendaftaran_t.tgl_pendaftaran,
	        COALESCE(
	            pendaftaranol_t.tgl_checkin,
	            pendaftaranol_t.tgl_pendaftaranol,
	            pendaftaran_t.tgl_pendaftaran
	        ) AS tgl_mulai_antrian,
	        NULL::TIMESTAMP WITHOUT TIME ZONE AS tgl_mulai_pendaftaran,
	        pendaftaran_t.created_date AS tgl_selesai_pendaftaran,
	        pendaftaran_t.tgl_masukperiksa,
	        pendaftaran_t.tgl_selesaiperiksa,
	        COALESCE( 
	            pendaftaranol_t.pasien_id, 
	            pendaftaran_t.pasien_id 
	        ) AS pasien_id,
	        COALESCE( 
	            pendaftaranol_t.antrian_id, 
	            pendaftaran_t.antrian_id 
	        ) AS antrian_id
	    FROM pendaftaran_t
	    LEFT JOIN pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id
	    WHERE pendaftaran_t.instalasi_id = 1
	    and COALESCE(
		        pendaftaranol_t.tgl_checkin,
		        pendaftaranol_t.tgl_pendaftaranol,
		        pendaftaran_t.tgl_pendaftaran
		    )::DATE = p_tgl_mulai_antrian::DATE
	), penjualanresep_t AS (
		SELECT 
			penjualanresep_t.penjualanresep_id,
			penjualanresep_t.pendaftaran_id,
			penjualanresep_t.tglresep,
			penjualanresep_t.tgl_cetak_etiket,
			penjualanresep_t.tgl_menyerahkan
		FROM penjualanresep_t
		JOIN datapendaftaran ON datapendaftaran.pendaftaran_id = penjualanresep_t.pendaftaran_id
		WHERE penjualanresep_t.is_deleted = FALSE 
	), resep AS (
	    SELECT
	        penjualanresep_t.pendaftaran_id,
	        COALESCE(
	            min(reseptur_t.tglreseptur),
	            min(penjualanresep_t.tglresep)
	        ) AS tglreseptur,
	        COALESCE(
	            min(reseptur_t.tgl_cetak_etiket),
	            min(penjualanresep_t.tgl_cetak_etiket)
	        ) AS tgl_cetak_etiket,
	        min(penjualanresep_t.tgl_menyerahkan) AS tgl_menyerahkan
	    FROM penjualanresep_t
	    LEFT JOIN reseptur_t ON penjualanresep_t.penjualanresep_id = reseptur_t.penjualanresep_id
	    GROUP BY penjualanresep_t.pendaftaran_id
	), soap AS (
	    SELECT 
	        DISTINCT ON (soaprj_t.pendaftaran_id) soaprj_t.pendaftaran_id,
	        soaprj_t.pegawai_id,
	        soaprj_t.created_date AS tgl_created_by_dpjp
	    FROM soaprj_t
	    JOIN datapendaftaran ON datapendaftaran.pendaftaran_id = soaprj_t.pendaftaran_id
	    JOIN pegawai_m ON pegawai_m.pegawai_id = soaprj_t.pegawai_id
	    WHERE (soaprj_t.additional_data::json ->> 'via_soap'::TEXT) = 'true'::TEXT
	    AND soaprj_t.is_deleted IS FALSE 
	    AND pegawai_m.kelompokpegawai_id = 1
	    ORDER BY soaprj_t.pendaftaran_id, soaprj_t.created_date
	), resumemedis AS (
		SELECT 
	        rt.pendaftaran_id, 
	        rt.created_date
	    FROM resumemedisri_t rt
	    JOIN datapendaftaran ON rt.pendaftaran_id = datapendaftaran.pendaftaran_id
	    WHERE rt.is_deleted IS FALSE
	), pasienpulang AS (
	    SELECT 
	        pt_1.pendaftaran_id, 
	        min(pt_1.tglpasienpulang) AS tgl_pasien_pulang
	    FROM pasienpulang_t pt_1
	    JOIN datapendaftaran ON pt_1.pendaftaran_id = datapendaftaran.pendaftaran_id
	    GROUP BY pt_1.pendaftaran_id
	), bpjs_antrian_tanggal_t AS (
	    SELECT
	        bpjs_antrian_tanggal_t.kodebooking,
	        bpjs_antrian_tanggal_t.noantrean,
	        bpjs_antrian_tanggal_t.status,
	        bpjs_antrian_tanggal_t.is_deleted
	    FROM bpjs_antrian_tanggal_t
	    JOIN datapendaftaran ON datapendaftaran.kodebooking::TEXT = bpjs_antrian_tanggal_t.kodebooking::TEXT
	    WHERE bpjs_antrian_tanggal_t.is_deleted IS false
	), statusbpjs AS (
	    SELECT 
	        batt.kodebooking,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 1) AS task1,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 2) AS task2,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 3) AS task3,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 4) AS task4,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 5) AS task5,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 6) AS task6,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 7) AS task7,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 99) AS task99,
	        batt.noantrean,
	        batt.status,
	        bltt.is_deleted AS taskdeleted
	    FROM bpjs_list_task_t bltt
	    RIGHT JOIN bpjs_antrian_tanggal_t batt ON batt.kodebooking::TEXT = bltt.kodebooking::TEXT
	    WHERE bltt.is_deleted IS NULL OR bltt.is_deleted IS FALSE
	    GROUP BY batt.kodebooking, batt.noantrean, batt.status, bltt.is_deleted
	), databooking AS (
	    SELECT 
	        datapendaftaran.kodebooking,
	        datapendaftaran.pendaftaran_id,
	        datapendaftaran.pendaftaranol_id,
	        datapendaftaran.no_pendaftaran,
	        datapendaftaran.status_pasien,
	        pasien_m.nama_pasien,
	        jenis_cara_bayar.lookup_value AS jenis_pasien,
	        pasien_m.nopeserta_bpjs,
	        antrianjkn_r.nomorreferensi,
	        datapendaftaran.tgl_pendaftaran,
	        datapendaftaran.tgl_mulai_antrian,
	        datapendaftaran.tgl_mulai_pendaftaran,
	        datapendaftaran.tgl_selesai_pendaftaran,
	        datapendaftaran.tgl_masukperiksa,
	        datapendaftaran.tgl_selesaiperiksa,
	        resep.tglreseptur AS tgl_order_resep,
	        resep.tgl_cetak_etiket,
	        resep.tgl_menyerahkan AS tgl_serahkan_resep,
	        soap.tgl_created_by_dpjp AS tgl_created_soap_by_dpjp,
	        resumemedis.created_date AS tgl_created_resumemedis,
	        pasienpulang.tgl_pasien_pulang AS tgl_tindaklanjut_pasien
	    FROM datapendaftaran
	    LEFT JOIN pasien_m ON datapendaftaran.pasien_id = pasien_m.pasien_id
	    LEFT JOIN antrian_t ON antrian_t.antrian_id = datapendaftaran.antrian_id
	    LEFT JOIN antrianjkn_r ON antrianjkn_r.antrian_id = antrian_t.antrian_id
	    LEFT JOIN lookup_m jenis_cara_bayar ON antrianjkn_r.jenis_cara_bayar = jenis_cara_bayar.lookup_id
	    LEFT JOIN resep ON resep.pendaftaran_id = datapendaftaran.pendaftaran_id
	    LEFT JOIN soap ON soap.pendaftaran_id = datapendaftaran.pendaftaran_id
	    LEFT JOIN resumemedis ON resumemedis.pendaftaran_id = datapendaftaran.pendaftaran_id
	    LEFT JOIN pasienpulang ON pasienpulang.pendaftaran_id = datapendaftaran.pendaftaran_id
	)
	SELECT 
	    pendaftaran.kodebooking,
	    statusbpjs.kodebooking AS kodebooking_bpjs,
	    pendaftaran.pendaftaran_id,
	    pendaftaran.pendaftaranol_id,
	    pendaftaran.no_pendaftaran,
	    pendaftaran.nama_pasien,
	    pendaftaran.jenis_pasien,
	    pendaftaran.nopeserta_bpjs,
	    pendaftaran.nomorreferensi,
	    pendaftaran.tgl_pendaftaran,
	    statusbpjs.task1,
	    statusbpjs.task2,
	    statusbpjs.task3,
	    statusbpjs.task4,
	    statusbpjs.task5,
	    statusbpjs.task6,
	    statusbpjs.task7,
	    statusbpjs.task99,
	    statusbpjs.noantrean,
	    statusbpjs.status,
	    pendaftaran.status_pasien,
	    pendaftaran.tgl_mulai_antrian,
	    pendaftaran.tgl_mulai_pendaftaran,
	    pendaftaran.tgl_selesai_pendaftaran,
	    pendaftaran.tgl_masukperiksa,
	    pendaftaran.tgl_selesaiperiksa,
	    pendaftaran.tgl_order_resep,
	    pendaftaran.tgl_created_soap_by_dpjp,
	    CASE
	        WHEN pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_soap_by_dpjp 
	        THEN pendaftaran.tgl_created_soap_by_dpjp + '00:04:59'::INTERVAL
	        ELSE pendaftaran.tgl_created_resumemedis
	    END AS tgl_created_resumemedis,
	    CASE
	        WHEN 
	            pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_soap_by_dpjp 
	            AND (pendaftaran.tgl_created_soap_by_dpjp + '00:04:59'::INTERVAL) > pendaftaran.tgl_tindaklanjut_pasien 
	        THEN pendaftaran.tgl_created_soap_by_dpjp + '00:10:15'::INTERVAL
	        ELSE pendaftaran.tgl_tindaklanjut_pasien
	    END AS tgl_tindaklanjut_pasien,
	    CASE
	        WHEN 
	            pendaftaran.tgl_created_resumemedis IS NULL 
	            OR pendaftaran.tgl_created_soap_by_dpjp IS NULL 
	            OR pendaftaran.tgl_tindaklanjut_pasien IS NULL 
	        THEN NULL::TIMESTAMP WITHOUT TIME ZONE
	        WHEN pendaftaran.tgl_cetak_etiket < pendaftaran.tgl_tindaklanjut_pasien 
	        THEN
	            CASE
	                WHEN 
	                    pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_soap_by_dpjp 
	                    AND (pendaftaran.tgl_created_soap_by_dpjp + '00:04:59'::INTERVAL) > pendaftaran.tgl_tindaklanjut_pasien 
	                THEN pendaftaran.tgl_created_soap_by_dpjp + '00:14:48'::INTERVAL
	                ELSE pendaftaran.tgl_cetak_etiket
	            END
	        ELSE pendaftaran.tgl_cetak_etiket
	    END AS tgl_cetak_etiket,
	    CASE
	        WHEN 
	            pendaftaran.tgl_created_resumemedis IS NULL 
	            OR pendaftaran.tgl_created_soap_by_dpjp IS NULL 
	            OR pendaftaran.tgl_tindaklanjut_pasien IS NULL 
	            OR pendaftaran.tgl_cetak_etiket IS NULL 
	        THEN NULL::TIMESTAMP WITHOUT TIME ZONE
	        WHEN pendaftaran.tgl_cetak_etiket < pendaftaran.tgl_tindaklanjut_pasien 
	        THEN pendaftaran.tgl_tindaklanjut_pasien + '00:14:48'::INTERVAL
	        WHEN 
	            pendaftaran.tgl_serahkan_resep < pendaftaran.tgl_cetak_etiket 
	            OR pendaftaran.tgl_serahkan_resep < (pendaftaran.tgl_tindaklanjut_pasien + '00:04:59'::INTERVAL) 
	        THEN pendaftaran.tgl_cetak_etiket + '00:10:15'::INTERVAL
	        ELSE pendaftaran.tgl_serahkan_resep
	    END AS tgl_serahkan_resep,
	    TRUE AS is_success_antrean
	FROM databooking pendaftaran
	LEFT JOIN statusbpjs ON statusbpjs.kodebooking::TEXT = pendaftaran.kodebooking::TEXT
	ORDER BY pendaftaran.pendaftaran_id DESC NULLS LAST;

  ELSIF p_daftar_from IS NOT NULL AND p_daftar_to IS NOT NULL THEN
    RETURN QUERY
	WITH datapendaftaran AS (
	    SELECT
	        COALESCE(
	            pendaftaranol_t.no_pendaftaranol, 
	            concat('RS', pendaftaran_t.pendaftaran_id
	        )::CHARACTER VARYING) AS kodebooking,
	        COALESCE(
	            pendaftaranol_t.pendaftaran_id,
	            pendaftaran_t.pendaftaran_id
	        ) AS pendaftaran_id,
	        pendaftaranol_t.pendaftaranol_id,
	        COALESCE(
	            pendaftaran_t.no_pendaftaran, 
	            'Belum Daftar'::CHARACTER VARYING
	        ) AS no_pendaftaran,
	        pendaftaranol_t.status_pasien::CHARACTER VARYING AS status_pasien,
	        pendaftaran_t.tgl_pendaftaran,
	        COALESCE(
	            pendaftaranol_t.tgl_checkin,
	            pendaftaranol_t.tgl_pendaftaranol,
	            pendaftaran_t.tgl_pendaftaran
	        ) AS tgl_mulai_antrian,
	        NULL::TIMESTAMP WITHOUT TIME ZONE AS tgl_mulai_pendaftaran,
	        pendaftaran_t.created_date AS tgl_selesai_pendaftaran,
	        pendaftaran_t.tgl_masukperiksa,
	        pendaftaran_t.tgl_selesaiperiksa,
	        COALESCE( 
	            pendaftaranol_t.pasien_id, 
	            pendaftaran_t.pasien_id 
	        ) AS pasien_id,
	        COALESCE( 
	            pendaftaranol_t.antrian_id, 
	            pendaftaran_t.antrian_id 
	        ) AS antrian_id
	    FROM pendaftaran_t
	    LEFT JOIN pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id
	    WHERE pendaftaran_t.instalasi_id = 1
	    AND	pendaftaran_t.tgl_pendaftaran::DATE BETWEEN p_daftar_from AND p_daftar_to
	), penjualanresep_t AS (
		SELECT 
			penjualanresep_t.penjualanresep_id,
			penjualanresep_t.pendaftaran_id,
			penjualanresep_t.tglresep,
			penjualanresep_t.tgl_cetak_etiket,
			penjualanresep_t.tgl_menyerahkan
		FROM penjualanresep_t
		JOIN datapendaftaran ON datapendaftaran.pendaftaran_id = penjualanresep_t.pendaftaran_id
		WHERE penjualanresep_t.is_deleted = FALSE 
	), resep AS (
	    SELECT
	        penjualanresep_t.pendaftaran_id,
	        COALESCE(
	            min(reseptur_t.tglreseptur),
	            min(penjualanresep_t.tglresep)
	        ) AS tglreseptur,
	        COALESCE(
	            min(reseptur_t.tgl_cetak_etiket),
	            min(penjualanresep_t.tgl_cetak_etiket)
	        ) AS tgl_cetak_etiket,
	        min(penjualanresep_t.tgl_menyerahkan) AS tgl_menyerahkan
	    FROM penjualanresep_t
	    LEFT JOIN reseptur_t ON penjualanresep_t.penjualanresep_id = reseptur_t.penjualanresep_id
	    GROUP BY penjualanresep_t.pendaftaran_id
	), soap AS (
	    SELECT 
	        DISTINCT ON (soaprj_t.pendaftaran_id) soaprj_t.pendaftaran_id,
	        soaprj_t.pegawai_id,
	        soaprj_t.created_date AS tgl_created_by_dpjp
	    FROM soaprj_t
	    JOIN datapendaftaran ON datapendaftaran.pendaftaran_id = soaprj_t.pendaftaran_id
	    JOIN pegawai_m ON pegawai_m.pegawai_id = soaprj_t.pegawai_id
	    WHERE (soaprj_t.additional_data::json ->> 'via_soap'::TEXT) = 'true'::TEXT
	    AND soaprj_t.is_deleted IS FALSE 
	    AND pegawai_m.kelompokpegawai_id = 1
	    ORDER BY soaprj_t.pendaftaran_id, soaprj_t.created_date
	), resumemedis AS (
		SELECT 
	        rt.pendaftaran_id, 
	        rt.created_date
	    FROM resumemedisri_t rt
	    JOIN datapendaftaran ON rt.pendaftaran_id = datapendaftaran.pendaftaran_id
	    WHERE rt.is_deleted IS FALSE
	), pasienpulang AS (
	    SELECT 
	        pt_1.pendaftaran_id, 
	        min(pt_1.tglpasienpulang) AS tgl_pasien_pulang
	    FROM pasienpulang_t pt_1
	    JOIN datapendaftaran ON pt_1.pendaftaran_id = datapendaftaran.pendaftaran_id
	    GROUP BY pt_1.pendaftaran_id
	), bpjs_antrian_tanggal_t AS (
	    SELECT
	        bpjs_antrian_tanggal_t.kodebooking,
	        bpjs_antrian_tanggal_t.noantrean,
	        bpjs_antrian_tanggal_t.status,
	        bpjs_antrian_tanggal_t.is_deleted
	    FROM bpjs_antrian_tanggal_t
	    JOIN datapendaftaran ON datapendaftaran.kodebooking::TEXT = bpjs_antrian_tanggal_t.kodebooking::TEXT
	    WHERE bpjs_antrian_tanggal_t.is_deleted IS false
	), statusbpjs AS (
	    SELECT 
	        batt.kodebooking,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 1) AS task1,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 2) AS task2,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 3) AS task3,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 4) AS task4,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 5) AS task5,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 6) AS task6,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 7) AS task7,
	        MAX(bltt.wakturs::TEXT) FILTER (WHERE bltt.taskid = 99) AS task99,
	        batt.noantrean,
	        batt.status,
	        bltt.is_deleted AS taskdeleted
	    FROM bpjs_list_task_t bltt
	    RIGHT JOIN bpjs_antrian_tanggal_t batt ON batt.kodebooking::TEXT = bltt.kodebooking::TEXT
	    WHERE bltt.is_deleted IS NULL OR bltt.is_deleted IS FALSE
	    GROUP BY batt.kodebooking, batt.noantrean, batt.status, bltt.is_deleted
	), databooking AS (
	    SELECT 
	        datapendaftaran.kodebooking,
	        datapendaftaran.pendaftaran_id,
	        datapendaftaran.pendaftaranol_id,
	        datapendaftaran.no_pendaftaran,
	        datapendaftaran.status_pasien,
	        pasien_m.nama_pasien,
	        jenis_cara_bayar.lookup_value AS jenis_pasien,
	        pasien_m.nopeserta_bpjs,
	        antrianjkn_r.nomorreferensi,
	        datapendaftaran.tgl_pendaftaran,
	        datapendaftaran.tgl_mulai_antrian,
	        datapendaftaran.tgl_mulai_pendaftaran,
	        datapendaftaran.tgl_selesai_pendaftaran,
	        datapendaftaran.tgl_masukperiksa,
	        datapendaftaran.tgl_selesaiperiksa,
	        resep.tglreseptur AS tgl_order_resep,
	        resep.tgl_cetak_etiket,
	        resep.tgl_menyerahkan AS tgl_serahkan_resep,
	        soap.tgl_created_by_dpjp AS tgl_created_soap_by_dpjp,
	        resumemedis.created_date AS tgl_created_resumemedis,
	        pasienpulang.tgl_pasien_pulang AS tgl_tindaklanjut_pasien
	    FROM datapendaftaran
	    LEFT JOIN pasien_m ON datapendaftaran.pasien_id = pasien_m.pasien_id
	    LEFT JOIN antrian_t ON antrian_t.antrian_id = datapendaftaran.antrian_id
	    LEFT JOIN antrianjkn_r ON antrianjkn_r.antrian_id = antrian_t.antrian_id
	    LEFT JOIN lookup_m jenis_cara_bayar ON antrianjkn_r.jenis_cara_bayar = jenis_cara_bayar.lookup_id
	    LEFT JOIN resep ON resep.pendaftaran_id = datapendaftaran.pendaftaran_id
	    LEFT JOIN soap ON soap.pendaftaran_id = datapendaftaran.pendaftaran_id
	    LEFT JOIN resumemedis ON resumemedis.pendaftaran_id = datapendaftaran.pendaftaran_id
	    LEFT JOIN pasienpulang ON pasienpulang.pendaftaran_id = datapendaftaran.pendaftaran_id
	)
	SELECT 
	    pendaftaran.kodebooking,
	    statusbpjs.kodebooking AS kodebooking_bpjs,
	    pendaftaran.pendaftaran_id,
	    pendaftaran.pendaftaranol_id,
	    pendaftaran.no_pendaftaran,
	    pendaftaran.nama_pasien,
	    pendaftaran.jenis_pasien,
	    pendaftaran.nopeserta_bpjs,
	    pendaftaran.nomorreferensi,
	    pendaftaran.tgl_pendaftaran,
	    statusbpjs.task1,
	    statusbpjs.task2,
	    statusbpjs.task3,
	    statusbpjs.task4,
	    statusbpjs.task5,
	    statusbpjs.task6,
	    statusbpjs.task7,
	    statusbpjs.task99,
	    statusbpjs.noantrean,
	    statusbpjs.status,
	    pendaftaran.status_pasien,
	    pendaftaran.tgl_mulai_antrian,
	    pendaftaran.tgl_mulai_pendaftaran,
	    pendaftaran.tgl_selesai_pendaftaran,
	    pendaftaran.tgl_masukperiksa,
	    pendaftaran.tgl_selesaiperiksa,
	    pendaftaran.tgl_order_resep,
	    pendaftaran.tgl_created_soap_by_dpjp,
	    CASE
	        WHEN pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_soap_by_dpjp 
	        THEN pendaftaran.tgl_created_soap_by_dpjp + '00:04:59'::INTERVAL
	        ELSE pendaftaran.tgl_created_resumemedis
	    END AS tgl_created_resumemedis,
	    CASE
	        WHEN 
	            pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_soap_by_dpjp 
	            AND (pendaftaran.tgl_created_soap_by_dpjp + '00:04:59'::INTERVAL) > pendaftaran.tgl_tindaklanjut_pasien 
	        THEN pendaftaran.tgl_created_soap_by_dpjp + '00:10:15'::INTERVAL
	        ELSE pendaftaran.tgl_tindaklanjut_pasien
	    END AS tgl_tindaklanjut_pasien,
	    CASE
	        WHEN 
	            pendaftaran.tgl_created_resumemedis IS NULL 
	            OR pendaftaran.tgl_created_soap_by_dpjp IS NULL 
	            OR pendaftaran.tgl_tindaklanjut_pasien IS NULL 
	        THEN NULL::TIMESTAMP WITHOUT TIME ZONE
	        WHEN pendaftaran.tgl_cetak_etiket < pendaftaran.tgl_tindaklanjut_pasien 
	        THEN
	            CASE
	                WHEN 
	                    pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_soap_by_dpjp 
	                    AND (pendaftaran.tgl_created_soap_by_dpjp + '00:04:59'::INTERVAL) > pendaftaran.tgl_tindaklanjut_pasien 
	                THEN pendaftaran.tgl_created_soap_by_dpjp + '00:14:48'::INTERVAL
	                ELSE pendaftaran.tgl_cetak_etiket
	            END
	        ELSE pendaftaran.tgl_cetak_etiket
	    END AS tgl_cetak_etiket,
	    CASE
	        WHEN 
	            pendaftaran.tgl_created_resumemedis IS NULL 
	            OR pendaftaran.tgl_created_soap_by_dpjp IS NULL 
	            OR pendaftaran.tgl_tindaklanjut_pasien IS NULL 
	            OR pendaftaran.tgl_cetak_etiket IS NULL 
	        THEN NULL::TIMESTAMP WITHOUT TIME ZONE
	        WHEN pendaftaran.tgl_cetak_etiket < pendaftaran.tgl_tindaklanjut_pasien 
	        THEN pendaftaran.tgl_tindaklanjut_pasien + '00:14:48'::INTERVAL
	        WHEN 
	            pendaftaran.tgl_serahkan_resep < pendaftaran.tgl_cetak_etiket 
	            OR pendaftaran.tgl_serahkan_resep < (pendaftaran.tgl_tindaklanjut_pasien + '00:04:59'::INTERVAL) 
	        THEN pendaftaran.tgl_cetak_etiket + '00:10:15'::INTERVAL
	        ELSE pendaftaran.tgl_serahkan_resep
	    END AS tgl_serahkan_resep,
	    TRUE AS is_success_antrean
	FROM databooking pendaftaran
	LEFT JOIN statusbpjs ON statusbpjs.kodebooking::TEXT = pendaftaran.kodebooking::TEXT
	ORDER BY pendaftaran.pendaftaran_id DESC NULLS LAST;  

  ELSE
    RETURN QUERY
    SELECT * FROM public.bpjs_infoantrean_fn(NOW()::DATE, NULL, NULL);
  END IF;
END;
$function$
;