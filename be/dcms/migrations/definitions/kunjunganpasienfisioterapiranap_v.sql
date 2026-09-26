-- public.kunjunganpasienfisioterapiranap_v source

CREATE OR REPLACE VIEW public.kunjunganpasienfisioterapiranap_v
AS SELECT
	pendaftaran_t.pendaftaran_id,
	pendaftaran_t.pasien_id,
	pasienadmisi_t.status_ranap AS status_periksa_id,
	dokterdpjp.pegawai_id AS dokterperujuk_id,
	fgetnamalookup ( pasienadmisi_t.status_ranap ) AS status_periksa_nama,
	pasienadmisi_t.tgl_pendaftaran,
	pasien_m.nama_pasien,
	pasien_m.jeniskelamin AS jeniskelamin_id,
	fgetnamalookup ( pasien_m.jeniskelamin :: INTEGER ) AS jeniskelamin_nama,
	pasien_m.tanggal_lahir,
	pasien_m.no_rekam_medik,
	programterapidetail_t.terapi_nama,
	jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama,
	jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
	dokterdpjp.nama_pegawai AS dokterperujuk_nama,
	pendaftaran_t.status_bayar AS statusbayar_id,
	pasienmasukpenunjang_t.pasienmasukpenunjang_id,
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
	ruangan_m.ruangan_id,
	ruangan_m.ruangan_nama,
	kamarruangan_m.kamarruangan_nokamar AS kamar,
	kamarruangan_m.kamarruangan_id AS kamar_id,
	kamartempattidur_m.no_tempattidur,
	kamartempattidur_m.kamartempattidur_id,
	dokterdpjpranap.nama_pegawai AS dokterdpjp_nama,
	pasienadmisi_t.pasienadmisi_id,
	pasienmasukpenunjang_t.tglmasukpenunjang,
	pasienmasukpenunjang_t.status_periksa,
	programterapi_t.frekuensi,
	programterapi_t.realisasi,
	programterapi_t.sisa,
	COALESCE ( pembayaran.bayar, 0 :: BIGINT ) AS bayar,
CASE
		
		WHEN COALESCE ( soapfisioterapi_t.jumlah, 0 :: BIGINT ) = COALESCE ( pembayaran.bayar, 0 :: BIGINT ) 
		AND pembayaran.bayar > 0 THEN
			'LUNAS' :: TEXT 
			WHEN programterapidetail_t.is_paketfisio = TRUE 
			AND pembayaran.bayar = 1 THEN
				'LUNAS' :: TEXT ELSE'BELUM LUNAS' :: TEXT 
				END AS statusbayar_nama,
			programterapidetail_t.is_paketfisio,
			programterapi_t.status_fisio AS status_fisio_id,
			look_status_fisio.lookup_name AS status_fisio_nama,
			programterapi_t.status_program_fisio AS status_program_fisio_id,
			look_status_program_fisio.lookup_name AS status_program_fisio_nama 
		FROM
			pendaftaran_t
			LEFT JOIN (
			SELECT A
				.pasienadmisi_id,
				A.pasien_id,
				A.kelaspelayanan_id,
				A.penjamin_id,
				A.kamarruangan_id,
				A.pegawai_id,
				A.status_ranap,
				A.tgl_pendaftaran,
				A.kamartempattidur_id,
				A.ruangan_id 
			FROM
				pasienadmisi_t A 
			) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
			LEFT JOIN ( SELECT A.pasien_id, A.nama_pasien, A.jeniskelamin, A.tanggal_lahir, A.no_rekam_medik, A.alamat_pasien FROM pasien_m A ) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
			JOIN (
			SELECT A
				.programterapi_id,
				A.pendaftaran_id,
				A.pasienmasukpenunjang_id,
				A.dokterperujuk_id,
				A.frekuensi,
				A.status_fisio,
				A.status_program_fisio,
				A.realisasi,
				A.sisa 
			FROM
				programterapi_t A 
			) programterapi_t ON pendaftaran_t.pendaftaran_id = programterapi_t.pendaftaran_id
			JOIN ( SELECT pt.pasienmasukpenunjang_id, pt.tglmasukpenunjang, pt.pendaftaran_id, pt.programterapi_id, pt.pasienadmisi_id, pt.status_periksa FROM pasienmasukpenunjang_t pt ) pasienmasukpenunjang_t ON programterapi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
			LEFT JOIN (
			SELECT A
				.programterapi_id,
				A.is_paketfisio,
				string_agg ( dm.daftartindakan_nama :: TEXT, ',' :: TEXT ) AS terapi_nama,
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
				A.is_paketfisio,
				A.pemeriksaanfisio_id 
			) programterapidetail_t ON programterapi_t.programterapi_id = programterapidetail_t.programterapi_id
			LEFT JOIN ( SELECT A.pemeriksaanfisio_id, A.jenispemeriksaanfisio_id FROM pemeriksaanfisio_m A ) pemeriksaanfisio_m ON programterapidetail_t.pemeriksaanfisio_id = pemeriksaanfisio_m.pemeriksaanfisio_id
			LEFT JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id
			LEFT JOIN ( SELECT A.carabayar_id, A.carabayar_nama FROM carabayar_m A ) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
			LEFT JOIN ( SELECT A.jeniskasuspenyakit_id, A.jeniskasuspenyakit_nama FROM jeniskasuspenyakit_m A ) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
			LEFT JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
			LEFT JOIN ( SELECT A.penjamin_id, A.penjamin_nama FROM penjamin_m A ) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
			LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokterdpjp ON pasienadmisi_t.pegawai_id = dokterdpjp.pegawai_id
			LEFT JOIN ( SELECT A.kamarruangan_id, A.kamarruangan_nokamar FROM kamarruangan_m A ) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
			LEFT JOIN ( SELECT b.pegawai_id, b.nama_pegawai FROM pegawai_m b ) dokterdpjpranap ON pasienadmisi_t.pegawai_id = dokterdpjpranap.pegawai_id
			LEFT JOIN (
			SELECT COUNT
				( A.soapfisioterapi_id ) AS jumlah,
				A.pasien_id,
				A.programterapi_id,
				A.pendaftaran_id 
			FROM
				soapfisioterapi_t A 
			GROUP BY
				A.pasien_id,
				A.pendaftaran_id,
				A.programterapi_id 
			) soapfisioterapi_t ON programterapi_t.programterapi_id = soapfisioterapi_t.programterapi_id 
			AND programterapi_t.pendaftaran_id = soapfisioterapi_t.pendaftaran_id
			LEFT JOIN (
			SELECT
				pasienmasukpenunjang_t_1.programterapi_id,
				SUM ( CASE WHEN tindakanpelayanan_t.tindakan = tindakanpelayanan_t.jml_bayar THEN 1 ELSE 0 END ) AS bayar 
			FROM
				pasienmasukpenunjang_t pasienmasukpenunjang_t_1
				JOIN (
				SELECT
					tindakanpelayanan_t_1.pasienmasukpenunjang_id,
					COUNT ( tindakanpelayanan_t_1.tindakanpelayanan_id ) AS tindakan,
					SUM ( CASE WHEN tindakanpelayanan_t_1.tindakansudahbayar_id IS NOT NULL THEN 1 ELSE 0 END ) AS jml_bayar 
				FROM
					tindakanpelayanan_t tindakanpelayanan_t_1 
				WHERE
					tindakanpelayanan_t_1.instalasi_id = 3 
				GROUP BY
					tindakanpelayanan_t_1.pasienmasukpenunjang_id 
				) tindakanpelayanan_t ON pasienmasukpenunjang_t_1.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id 
			GROUP BY
				pasienmasukpenunjang_t_1.programterapi_id 
			) pembayaran ON programterapi_t.programterapi_id = pembayaran.programterapi_id
			JOIN ( SELECT A.kamartempattidur_id, A.no_tempattidur FROM kamartempattidur_m A ) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
			JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
			LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_status_fisio ON programterapi_t.status_fisio :: INTEGER = look_status_fisio.lookup_id
			LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_status_program_fisio ON programterapi_t.status_program_fisio :: INTEGER = look_status_program_fisio.lookup_id 
	WHERE
	pendaftaran_t.instalasi_id = ANY ( ARRAY [ 2, 3 ] );