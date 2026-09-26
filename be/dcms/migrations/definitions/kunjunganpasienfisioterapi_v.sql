-- public.kunjunganpasienfisioterapi_v source

CREATE OR REPLACE VIEW public.kunjunganpasienfisioterapi_v
AS SELECT
	pendaftaran_t.pendaftaran_id,
	soapfisioterapi_t.parent_pendaftaran_id,
	soapfisioterapi_t.parent_pasienmasukpenunjang_id,
	pasienmasukpenunjang_t.pasienmasukpenunjang_id,
	pendaftaran_t.pasien_id,
	pendaftaran_t.status_periksa AS status_periksa_id,
	dokterdpjp.pegawai_id AS dokterperujuk_id,
	fgetnamalookup ( pendaftaran_t.status_periksa :: INTEGER ) AS status_periksa_nama,
	pendaftaran_t.tgl_pendaftaran,
	pasien_m.nama_pasien,
	pasien_m.jeniskelamin AS jeniskelamin_id,
	fgetnamalookup ( pasien_m.jeniskelamin :: INTEGER ) AS jeniskelamin_nama,
	pasien_m.tanggal_lahir,
	pasien_m.no_rekam_medik,
	programterapidetail_t.terapi_nama,
	soapfisioterapi_t.terapis_nama,
	dokterdpjp.nama_pegawai AS dokterperujuk_nama,
	soapfisioterapi_t.tindakansudahbayar_id,
	pendaftaran_t.status_bayar AS statusbayar_id,
	fgetnamalookup ( pendaftaran_t.status_bayar ) AS statusbayar_nama,
	programterapi_t.programterapi_id,
	pendaftaran_t.no_pendaftaran,
	pasien_m.alamat_pasien,
	pendaftaran_t.jeniskasuspenyakit_id,
	jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
	pendaftaran_t.kelaspelayanan_id,
	kelaspelayanan_m.kelaspelayanan_nama,
	pendaftaran_t.umur,
	pendaftaran_t.carabayar_id,
	carabayar_m.carabayar_nama,
	pendaftaran_t.penjamin_id,
	penjamin_m.penjamin_nama,
	'-' :: TEXT AS kamar,
	'-' :: TEXT AS dokterdpjp_nama,
	programterapi_t.status_fisio AS status_fisio_id,
	look_status_fisio.lookup_name AS status_fisio_nama,
	soapfisioterapi_t.is_paket,
	soapfisioterapi_t.is_edit,
	programterapi_t.status_program_fisio AS status_program_fisio_id,
	look_status_program_fisio.lookup_name AS status_program_fisio_nama,
	programterapidetail_t.terapi_nama_lain,
	programterapidetail_t.jenispemeriksaanfisio_nama,
	programterapidetail_t.jenispemeriksaanfisio_id 
FROM
	pendaftaran_t
	JOIN ( SELECT A.pasien_id, A.nama_pasien, A.jeniskelamin, A.tanggal_lahir, A.no_rekam_medik, A.alamat_pasien FROM pasien_m A ) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
	LEFT JOIN ( SELECT pt.pasienmasukpenunjang_id, pt.pendaftaran_id, pt.programterapi_id FROM pasienmasukpenunjang_t pt WHERE pt.programterapi_id IS NOT NULL ) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
	LEFT JOIN ( SELECT A.programterapi_id, A.pendaftaran_id, A.pasienmasukpenunjang_id, A.dokterperujuk_id, A.status_fisio, A.status_program_fisio FROM programterapi_t A ) programterapi_t ON pasienmasukpenunjang_t.programterapi_id = programterapi_t.programterapi_id
	LEFT JOIN (
	SELECT A
		.programterapi_id,
		string_agg ( dm.daftartindakan_nama :: TEXT, ',' :: TEXT ) AS terapi_nama,
		string_agg ( concat ( dm.daftartindakan_nama :: TEXT, ' | ', jpf.jenispemeriksaanfisio_nama :: TEXT ), ' || ' :: TEXT ) AS terapi_nama_lain,
		string_agg ( DISTINCT terapis.nama_pegawai :: TEXT, ',' :: TEXT ) AS terapis_nama,
		string_agg ( DISTINCT dokterperujuk.nama_pegawai :: TEXT, ',' :: TEXT ) AS dokterperujuk_nama,
		string_agg ( DISTINCT jpf.jenispemeriksaanfisio_nama :: TEXT, ',' :: TEXT ) AS jenispemeriksaanfisio_nama,
		string_agg ( DISTINCT jpf.jenispemeriksaanfisio_id :: TEXT, ',' :: TEXT ) AS jenispemeriksaanfisio_id 
	FROM
		programterapidetail_t
		A LEFT JOIN daftartindakan_m dm ON dm.daftartindakan_id = A.daftartindakan_id
		LEFT JOIN pemeriksaanfisio_m pf ON pf.pemeriksaanfisio_id = A.pemeriksaanfisio_id
		LEFT JOIN jenispemeriksaanfisio_m jpf ON jpf.jenispemeriksaanfisio_id = pf.jenispemeriksaanfisio_id
		LEFT JOIN pegawai_m terapis ON terapis.pegawai_id = A.terapis_id
		LEFT JOIN pegawai_m dokterperujuk ON dokterperujuk.pegawai_id = A.dokterperujuk_id 
	WHERE
		A.is_deleted = FALSE 
	GROUP BY
		A.programterapi_id 
	) programterapidetail_t ON programterapi_t.programterapi_id = programterapidetail_t.programterapi_id
	LEFT JOIN ( SELECT A.carabayar_id, A.carabayar_nama FROM carabayar_m A ) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
	LEFT JOIN ( SELECT A.jeniskasuspenyakit_id, A.jeniskasuspenyakit_nama FROM jeniskasuspenyakit_m A ) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
	LEFT JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
	LEFT JOIN ( SELECT A.penjamin_id, A.penjamin_nama FROM penjamin_m A ) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
	LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokterdpjp ON pendaftaran_t.pegawai_id = dokterdpjp.pegawai_id
	LEFT JOIN (
	SELECT A
		.pendaftaran_id,
		A.terapis_id,
		terapis.nama_pegawai AS terapis_nama,
		A.programterapi_id,
		parent.pendaftaran_id AS parent_pendaftaran_id,
		parent.pasienmasukpenunjang_id AS parent_pasienmasukpenunjang_id,
		parent.is_paket,
		parent_bayar.tindakansudahbayar_id,
		A.is_edit,
		A.is_deleted 
	FROM
		soapfisioterapi_t
		A LEFT JOIN pegawai_m terapis ON terapis.pegawai_id = A.terapis_id
		LEFT JOIN programterapi_t parent ON parent.programterapi_id = A.programterapi_id
		LEFT JOIN tindakanpelayanan_t parent_bayar ON parent.pasienmasukpenunjang_id = parent_bayar.pasienmasukpenunjang_id 
		AND parent.pendaftaran_id = parent_bayar.pendaftaran_id 
	) soapfisioterapi_t ON pendaftaran_t.pendaftaran_id = soapfisioterapi_t.pendaftaran_id 
	AND soapfisioterapi_t.is_deleted = FALSE 
	AND soapfisioterapi_t.is_edit =
	FALSE LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_status_fisio ON programterapi_t.status_fisio :: INTEGER = look_status_fisio.lookup_id
	LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_status_program_fisio ON programterapi_t.status_program_fisio :: INTEGER = look_status_program_fisio.lookup_id 
WHERE
	pendaftaran_t.instalasi_id = ( ( SELECT lookuptransaksi_m.kode_id FROM lookuptransaksi_m WHERE lookuptransaksi_m.kode_transaksi :: TEXT = 'instalasi_fisio' :: TEXT ) );