-- public.programfisioterapiranap_v source

CREATE OR REPLACE VIEW public.programfisioterapiranap_v
AS SELECT
	programterapi_t.pendaftaran_id,
	programterapi_t.pasien_id,
	programterapi_t.programterapi_id,
	dok_perujuk.pegawai_id AS dokter_perujuk_id,
	dok_perujuk.nama_pegawai AS dokter_perujuk,
	look_jenkel.lookup_kode AS jenis_kelamin,
	programterapi_t.tgl_permintaan AS tgl_rujukan,
	pasien_m.no_rekam_medik,
	pasien_m.nama_pasien,
	pasien_m.tanggal_lahir,
	programterapi_t.frekuensi,
	programterapi_t.realisasi,
	programterapi_t.sisa,
	COALESCE ( pembayaran.bayar, 0 :: BIGINT ) AS bayar,
CASE
		
		WHEN programterapi_t.frekuensi = soapfisioterapi_t.jumlah :: DOUBLE PRECISION THEN
		'CLOSE' :: TEXT ELSE'OPEN' :: TEXT 
	END AS status,
	COALESCE ( programterpilih.jumlah, 0 :: BIGINT ) AS programterpilih,
	COALESCE ( programterapi_t.frekuensi - COALESCE ( programterpilih.jumlah :: DOUBLE PRECISION, 0 :: DOUBLE PRECISION ), 0 :: DOUBLE PRECISION ) AS sisa_frekuensi,
	jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama,
	programterapidetail_t.terapi_nama,
	pasienadmisi_t.status_ranap AS status_periksa_id,
	lookup_m.lookup_name AS status_periksa_nama,
	soapfisioterapi_t.is_edit,
	programterapi_t.status_program_fisio AS status_program_fisio_id,
	lookup_statusprogramfisio.lookup_name AS status_program_fisio_nama,
	carabayar_m.carabayar_id,
	carabayar_m.carabayar_nama,
	COALESCE ( jadwalterapifisio_t.jumlah_ketidakhadiran, 0 :: BIGINT ) AS jumlah_ketidakhadiran 
FROM
	programterapi_t
	LEFT JOIN ( SELECT pt.programterapi_id, COUNT ( pt.pasienmasukpenunjang_id ) AS jumlah FROM pasienmasukpenunjang_t pt GROUP BY pt.programterapi_id ) programterpilih ON programterpilih.programterapi_id = programterapi_t.programterapi_id
	LEFT JOIN (
	SELECT COUNT
		( A.soapfisioterapi_id ) AS jumlah,
		A.pasien_id,
		A.programterapi_id,
		A.pendaftaran_id,
		A.tipe_instalasi,
		A.is_edit,
		A.is_deleted 
	FROM
		soapfisioterapi_t A 
	GROUP BY
		A.pasien_id,
		A.pendaftaran_id,
		A.programterapi_id,
		A.tipe_instalasi,
		A.is_edit,
		A.is_deleted 
	) soapfisioterapi_t ON programterapi_t.programterapi_id = soapfisioterapi_t.programterapi_id 
	AND programterapi_t.pendaftaran_id = soapfisioterapi_t.pendaftaran_id 
	AND soapfisioterapi_t.is_edit IS NOT TRUE 
	AND soapfisioterapi_t.is_deleted
	IS NOT TRUE JOIN ( SELECT A.pasien_id, A.nama_pasien, A.tanggal_lahir, A.no_rekam_medik, A.jeniskelamin FROM pasien_m A ) pasien_m ON programterapi_t.pasien_id = pasien_m.pasien_id
	LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dok_perujuk ON programterapi_t.dokterperujuk_id = dok_perujuk.pegawai_id
	LEFT JOIN (
	SELECT
		pasienmasukpenunjang_t_1.programterapi_id,
		SUM ( CASE WHEN tindakanpelayanan_t.tindakan = tindakanpelayanan_t.jml_bayar THEN 1 ELSE 0 END ) AS bayar 
	FROM
		pasienmasukpenunjang_t pasienmasukpenunjang_t_1
		JOIN (
		SELECT
			tindakanpelayanan_t_1.pasienmasukpenunjang_id,
			tindakanpelayanan_t_1.is_deleted,
			COUNT ( tindakanpelayanan_t_1.tindakanpelayanan_id ) AS tindakan,
			SUM ( CASE WHEN tindakanpelayanan_t_1.tindakansudahbayar_id IS NOT NULL THEN 1 ELSE 0 END ) AS jml_bayar 
		FROM
			tindakanpelayanan_t tindakanpelayanan_t_1 
		WHERE
			tindakanpelayanan_t_1.is_deleted = FALSE 
			AND tindakanpelayanan_t_1.instalasi_id = ( ( SELECT lookuptransaksi_m.kode_id FROM lookuptransaksi_m WHERE lookuptransaksi_m.kode_transaksi :: TEXT = 'instalasi_fisio' :: TEXT ) ) 
		GROUP BY
			tindakanpelayanan_t_1.is_deleted,
			tindakanpelayanan_t_1.pasienmasukpenunjang_id 
		) tindakanpelayanan_t ON pasienmasukpenunjang_t_1.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id 
	GROUP BY
		pasienmasukpenunjang_t_1.programterapi_id 
	) pembayaran ON programterapi_t.programterapi_id = pembayaran.programterapi_id
	JOIN ( SELECT A.lookup_id, A.lookup_kode, A.lookup_name FROM lookup_m A ) look_jenkel ON pasien_m.jeniskelamin :: INTEGER = look_jenkel.lookup_id
	LEFT JOIN (
	SELECT A
		.programterapi_id,
		string_agg ( dm.daftartindakan_nama :: TEXT, ',' :: TEXT ) AS terapi_nama,
		string_agg ( DISTINCT terapis.nama_pegawai :: TEXT, ',' :: TEXT ) AS terapis_nama,
		string_agg ( DISTINCT dokterperujuk.nama_pegawai :: TEXT, ',' :: TEXT ) AS dokterperujuk_nama,
		A.pemeriksaanfisio_id 
	FROM
		programterapidetail_t
		A LEFT JOIN daftartindakan_m dm ON dm.daftartindakan_id = A.daftartindakan_id
		LEFT JOIN pegawai_m terapis ON terapis.pegawai_id = A.terapis_id
		LEFT JOIN pegawai_m dokterperujuk ON dokterperujuk.pegawai_id = A.dokterperujuk_id 
	WHERE
		A.is_deleted = FALSE 
	GROUP BY
		A.programterapi_id,
		A.pemeriksaanfisio_id 
	) programterapidetail_t ON programterapi_t.programterapi_id = programterapidetail_t.programterapi_id
	LEFT JOIN ( SELECT A.pemeriksaanfisio_id, A.jenispemeriksaanfisio_id FROM pemeriksaanfisio_m A ) pemeriksaanfisio_m ON programterapidetail_t.pemeriksaanfisio_id = pemeriksaanfisio_m.pemeriksaanfisio_id
	LEFT JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id
	JOIN ( SELECT A.pendaftaran_id, A.status_ranap, A.carabayar_id FROM pasienadmisi_t A ) pasienadmisi_t ON programterapi_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id
	LEFT JOIN ( SELECT A.carabayar_id, A.carabayar_nama FROM carabayar_m A ) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
	JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) lookup_m ON pasienadmisi_t.status_ranap = lookup_m.lookup_id
	JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) lookup_statusprogramfisio ON programterapi_t.status_program_fisio :: INTEGER = lookup_statusprogramfisio.lookup_id
	LEFT JOIN ( SELECT A.programterapi_id, COUNT ( A.status_kunjungan_fisio ) AS jumlah_ketidakhadiran FROM jadwalterapifisio_t A WHERE A.status_kunjungan_fisio = 1208 GROUP BY A.programterapi_id ) jadwalterapifisio_t ON programterapi_t.programterapi_id = jadwalterapifisio_t.programterapi_id 
WHERE
	programterapi_t.tipe_instalasi :: TEXT = '3' :: TEXT;