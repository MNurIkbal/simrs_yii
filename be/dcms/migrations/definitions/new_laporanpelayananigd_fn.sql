CREATE OR REPLACE FUNCTION public.new_laporanpelayananigd_fn(xfirstdate date, xlastdate date)
 RETURNS TABLE(tgl_pendaftaran date, jenis_pelayanan character varying, pasienmasukrujukan bigint, pasienmasuknonrujukan bigint, triase_resusitasi bigint, triase_emergent bigint, triase_urgent bigint, triase_nonurgent bigint, triase_falseemergency bigint, pasientindaklanjut_dipulangkan bigint, pasientindaklanjut_dirujukrslain bigint, pasientindaklanjut_pulangpaksa bigint, pasientindaklanjut_meninggal bigint, pasientindaklanjut_dirujukri bigint, pasientindaklanjut_lainlain bigint, pasientindaklanjut_melarikandiri bigint, pasien_baru bigint, pasien_lama bigint, pasien_laki bigint, pasien_perempuan bigint)
 LANGUAGE plpgsql
 IMMUTABLE
AS $function$
	BEGIN
	RETURN QUERY
		with data_rujukan as (
			SELECT
				(to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
				(to_char(triase_t.tgl_triase, 'YYYY-MM-DD'::text))::date AS tgl_triase,
				pendaftaran_t.rujukan_id AS rujukan,
				pendaftaran_t.pendaftaran_id,
				triase_t.trauma AS jenis_pelayanan,
				triase_t.hasil_triase,
				pasien_m.pasien_id,
				pasienpulang_t.carakeluar_id AS carakeluar,
				carakeluar_m.carakeluar_nama,
				pasien_m.jeniskelamin AS jenis_kelamin,
				pendaftaran_t.kunjungan AS status_kunjungan
		    FROM pendaftaran_t
		    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
		    JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
		    LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
		    LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
		    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xfirstdate::DATE AND xlastdate::DATE
		    AND triase_t.is_active = TRUE 
		    AND triase_t.is_deleted = false
		    AND pendaftaran_t.instalasi_id = 2
		    GROUP BY 
				(to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date,
				(to_char(triase_t.tgl_triase, 'YYYY-MM-DD'::text))::date,
				pendaftaran_t.rujukan_id,
				triase_t.trauma,
				triase_t.hasil_triase,
				pasien_m.pasien_id,
				pasienpulang_t.carakeluar_id,
				carakeluar_m.carakeluar_nama,
				pasien_m.jeniskelamin,
				pendaftaran_t.kunjungan,
				pendaftaran_t.pendaftaran_id
		), count_data as (
			select
				xfirstdate::DATE as tgl_pendaftaran,
				'Non Trauma'::character varying as jenis_pelayanan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NOT NULL) as pasienmasukrujukan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NULL) as pasienmasuknonrujukan,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'resusitasi') as triase_resusitasi,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'emergent') as triase_emergent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'urgent') as triase_urgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'non_urgent') as triase_nonurgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'false_emergency') as triase_falseemergency,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 1) as pasientindaklanjut_dipulangkan,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 2) as pasientindaklanjut_dirujukrslain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 3) as pasientindaklanjut_pulangpaksa,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 4) as pasientindaklanjut_meninggal,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 5) as pasientindaklanjut_dirujukri,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 6) as pasientindaklanjut_lainlain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 7) as pasientindaklanjut_melarikandiri,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '180') as pasien_baru,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '181') as pasien_lama,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '15') as pasien_laki,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '16') as pasien_perempuan
			from data_rujukan
			where data_rujukan.jenis_pelayanan = 'non_trauma'
			union all
			select
				xfirstdate::DATE as tgl_pendaftaran,
				'Trauma'::character varying as jenis_pelayanan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NOT NULL) as pasienmasukrujukan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NULL) as pasienmasuknonrujukan,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'resusitasi') as triase_resusitasi,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'emergent') as triase_emergent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'urgent') as triase_urgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'non_urgent') as triase_nonurgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'false_emergency') as triase_falseemergency,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 1) as pasientindaklanjut_dipulangkan,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 2) as pasientindaklanjut_dirujukrslain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 3) as pasientindaklanjut_pulangpaksa,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 4) as pasientindaklanjut_meninggal,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 5) as pasientindaklanjut_dirujukri,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 6) as pasientindaklanjut_lainlain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 7) as pasientindaklanjut_melarikandiri,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '180') as pasien_baru,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '181') as pasien_lama,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '15') as pasien_laki,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '16') as pasien_perempuan
			from data_rujukan
			where data_rujukan.jenis_pelayanan = 'trauma'
			union all
			select
				xfirstdate::DATE as tgl_pendaftaran,
				'Kebidanan'::character varying as jenis_pelayanan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NOT NULL) as pasienmasukrujukan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NULL) as pasienmasuknonrujukan,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'resusitasi') as triase_resusitasi,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'emergent') as triase_emergent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'urgent') as triase_urgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'non_urgent') as triase_nonurgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'false_emergency') as triase_falseemergency,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 1) as pasientindaklanjut_dipulangkan,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 2) as pasientindaklanjut_dirujukrslain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 3) as pasientindaklanjut_pulangpaksa,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 4) as pasientindaklanjut_meninggal,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 5) as pasientindaklanjut_dirujukri,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 6) as pasientindaklanjut_lainlain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 7) as pasientindaklanjut_melarikandiri,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '180') as pasien_baru,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '181') as pasien_lama,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '15') as pasien_laki,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '16') as pasien_perempuan
			from data_rujukan
			where data_rujukan.jenis_pelayanan = 'kebidanan'
			union all
			select
				xfirstdate::DATE as tgl_pendaftaran,
				'Non Bedah'::character varying as jenis_pelayanan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NOT NULL) as pasienmasukrujukan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NULL) as pasienmasuknonrujukan,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'resusitasi') as triase_resusitasi,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'emergent') as triase_emergent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'urgent') as triase_urgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'non_urgent') as triase_nonurgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'false_emergency') as triase_falseemergency,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 1) as pasientindaklanjut_dipulangkan,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 2) as pasientindaklanjut_dirujukrslain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 3) as pasientindaklanjut_pulangpaksa,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 4) as pasientindaklanjut_meninggal,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 5) as pasientindaklanjut_dirujukri,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 6) as pasientindaklanjut_lainlain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 7) as pasientindaklanjut_melarikandiri,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '180') as pasien_baru,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '181') as pasien_lama,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '15') as pasien_laki,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '16') as pasien_perempuan
			from data_rujukan
			where data_rujukan.jenis_pelayanan = 'non_bedah'
			union all
			select
				xfirstdate::DATE as tgl_pendaftaran,
				'Psikiatri'::character varying as jenis_pelayanan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NOT NULL) as pasienmasukrujukan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NULL) as pasienmasuknonrujukan,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'resusitasi') as triase_resusitasi,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'emergent') as triase_emergent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'urgent') as triase_urgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'non_urgent') as triase_nonurgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'false_emergency') as triase_falseemergency,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 1) as pasientindaklanjut_dipulangkan,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 2) as pasientindaklanjut_dirujukrslain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 3) as pasientindaklanjut_pulangpaksa,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 4) as pasientindaklanjut_meninggal,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 5) as pasientindaklanjut_dirujukri,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 6) as pasientindaklanjut_lainlain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 7) as pasientindaklanjut_melarikandiri,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '180') as pasien_baru,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '181') as pasien_lama,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '15') as pasien_laki,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '16') as pasien_perempuan
			from data_rujukan
			where data_rujukan.jenis_pelayanan = 'psikiatri'
			union all
			select
				xfirstdate::DATE as tgl_pendaftaran,
				'Anak'::character varying as jenis_pelayanan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NOT NULL) as pasienmasukrujukan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NULL) as pasienmasuknonrujukan,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'resusitasi') as triase_resusitasi,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'emergent') as triase_emergent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'urgent') as triase_urgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'non_urgent') as triase_nonurgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'false_emergency') as triase_falseemergency,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 1) as pasientindaklanjut_dipulangkan,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 2) as pasientindaklanjut_dirujukrslain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 3) as pasientindaklanjut_pulangpaksa,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 4) as pasientindaklanjut_meninggal,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 5) as pasientindaklanjut_dirujukri,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 6) as pasientindaklanjut_lainlain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 7) as pasientindaklanjut_melarikandiri,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '180') as pasien_baru,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '181') as pasien_lama,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '15') as pasien_laki,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '16') as pasien_perempuan
			from data_rujukan
			where data_rujukan.jenis_pelayanan = 'anak'
			union all
			select
				xfirstdate::DATE as tgl_pendaftaran,
				'Bedah Trauma KLL'::character varying as jenis_pelayanan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NOT NULL) as pasienmasukrujukan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NULL) as pasienmasuknonrujukan,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'resusitasi') as triase_resusitasi,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'emergent') as triase_emergent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'urgent') as triase_urgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'non_urgent') as triase_nonurgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'false_emergency') as triase_falseemergency,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 1) as pasientindaklanjut_dipulangkan,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 2) as pasientindaklanjut_dirujukrslain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 3) as pasientindaklanjut_pulangpaksa,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 4) as pasientindaklanjut_meninggal,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 5) as pasientindaklanjut_dirujukri,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 6) as pasientindaklanjut_lainlain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 7) as pasientindaklanjut_melarikandiri,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '180') as pasien_baru,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '181') as pasien_lama,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '15') as pasien_laki,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '16') as pasien_perempuan
			from data_rujukan
			where data_rujukan.jenis_pelayanan = 'bedah_trauma_kll'
			union all
			select
				xfirstdate::DATE as tgl_pendaftaran,
				'Bedah Non Trauma KLL'::character varying as jenis_pelayanan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NOT NULL) as pasienmasukrujukan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NULL) as pasienmasuknonrujukan,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'resusitasi') as triase_resusitasi,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'emergent') as triase_emergent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'urgent') as triase_urgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'non_urgent') as triase_nonurgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'false_emergency') as triase_falseemergency,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 1) as pasientindaklanjut_dipulangkan,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 2) as pasientindaklanjut_dirujukrslain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 3) as pasientindaklanjut_pulangpaksa,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 4) as pasientindaklanjut_meninggal,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 5) as pasientindaklanjut_dirujukri,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 6) as pasientindaklanjut_lainlain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 7) as pasientindaklanjut_melarikandiri,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '180') as pasien_baru,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '181') as pasien_lama,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '15') as pasien_laki,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '16') as pasien_perempuan
			from data_rujukan
			where data_rujukan.jenis_pelayanan = 'bedah_trauma_non_kll'
			union all
			select
				xfirstdate::DATE as tgl_pendaftaran,
				'Bedah Non Trauma'::character varying as jenis_pelayanan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NOT NULL) as pasienmasukrujukan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NULL) as pasienmasuknonrujukan,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'resusitasi') as triase_resusitasi,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'emergent') as triase_emergent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'urgent') as triase_urgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'non_urgent') as triase_nonurgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'false_emergency') as triase_falseemergency,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 1) as pasientindaklanjut_dipulangkan,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 2) as pasientindaklanjut_dirujukrslain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 3) as pasientindaklanjut_pulangpaksa,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 4) as pasientindaklanjut_meninggal,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 5) as pasientindaklanjut_dirujukri,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 6) as pasientindaklanjut_lainlain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 7) as pasientindaklanjut_melarikandiri,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '180') as pasien_baru,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '181') as pasien_lama,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '15') as pasien_laki,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '16') as pasien_perempuan
			from data_rujukan
			where data_rujukan.jenis_pelayanan = 'bedah_non_trauma'
			union all
			select
				xfirstdate::DATE as tgl_pendaftaran,
				'Total'::character varying as jenis_pelayanan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NOT NULL) as pasienmasukrujukan,
				count(pendaftaran_id) filter (where data_rujukan.rujukan IS NULL) as pasienmasuknonrujukan,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'resusitasi') as triase_resusitasi,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'emergent') as triase_emergent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'urgent') as triase_urgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'non_urgent') as triase_nonurgent,
				count(pendaftaran_id) filter (where data_rujukan.hasil_triase = 'false_emergency') as triase_falseemergency,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 1) as pasientindaklanjut_dipulangkan,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 2) as pasientindaklanjut_dirujukrslain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 3) as pasientindaklanjut_pulangpaksa,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 4) as pasientindaklanjut_meninggal,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 5) as pasientindaklanjut_dirujukri,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 6) as pasientindaklanjut_lainlain,
				count(pendaftaran_id) filter (where data_rujukan.carakeluar = 7) as pasientindaklanjut_melarikandiri,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '180') as pasien_baru,
				count(pendaftaran_id) filter (where data_rujukan.status_kunjungan = '181') as pasien_lama,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '15') as pasien_laki,
				count(pendaftaran_id) filter (where data_rujukan.jenis_kelamin = '16') as pasien_perempuan
			from data_rujukan
			where data_rujukan.jenis_pelayanan in ('non_trauma', 'trauma', 'kebidanan', 'non_bedah', 'psikiatri', 'anak', 'bedah_trauma_kll', 'bedah_trauma_non_kll', 'bedah_non_trauma')
		)
		select
			count_data.tgl_pendaftaran,
			count_data.jenis_pelayanan, 
			count_data.pasienmasukrujukan, 
			count_data.pasienmasuknonrujukan, 
			count_data.triase_resusitasi, 
			count_data.triase_emergent, 
			count_data.triase_urgent, 
			count_data.triase_nonurgent, 
			count_data.triase_falseemergency, 
			count_data.pasientindaklanjut_dipulangkan, 
			count_data.pasientindaklanjut_dirujukrslain, 
			count_data.pasientindaklanjut_pulangpaksa, 
			count_data.pasientindaklanjut_meninggal, 
			count_data.pasientindaklanjut_dirujukri, 
			count_data.pasientindaklanjut_lainlain, 
			count_data.pasientindaklanjut_melarikandiri, 
			count_data.pasien_baru, 
			count_data.pasien_lama, 
			count_data.pasien_laki, 
			count_data.pasien_perempuan
		from count_data;
	END;
$function$
;