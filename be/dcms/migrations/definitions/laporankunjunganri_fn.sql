-- DROP FUNCTION public.laporankunjunganri_fn(timestamp, timestamp);

CREATE OR REPLACE FUNCTION public.laporankunjunganri_fn(xfirstdate timestamp without time zone, xlastdate timestamp without time zone)
 RETURNS TABLE(pasien_id integer, no_identitas_pasien character varying, nama_pasien character varying, nama_bin character varying, tempat_lahir character varying, tanggal_lahir date, alamat_pasien text, rt smallint, rw smallint, photopasien character varying, alamatemail character varying, statusrekammedis character varying, no_rekam_medik character varying, tgl_rekam_medik date, pekerjaan_id integer, pekerjaan_nama character varying, kabupaten_id integer, kabupaten_nama character varying, pendaftaran_id integer, no_pendaftaran character varying, tgl_pendaftaran timestamp without time zone, no_urutantri character varying, transportasi character varying, keadaan_masuk character varying, alih_status boolean, by_phone boolean, kunjungan_rumah boolean, umur character varying, golonganumur_id integer, no_asuransi character varying, namapemilik_asuransi character varying, nopokokperusahaan character varying, carabayar_id integer, carabayar_nama character varying, penjamin_id integer, penjamin_nama character varying, caramasuk_id integer, caramasuk_nama character varying, shift_id integer, no_rujukan character varying, nama_perujuk character varying, tanggal_rujukan timestamp without time zone, kodediagnosa_rujukan character varying, asalrujukan_id integer, asalrujukan_nama character varying, penanggungjawab_id integer, pengantar character varying, hubungankeluarga character varying, penanggungjawab_nama character varying, ruangan_id integer, ruangan_nama character varying, instalasi_id integer, instalasi_nama character varying, jeniskasuspenyakit_id integer, jeniskasuspenyakit_nama character varying, kelaspelayanan_id integer, kelaspelayanan_nama character varying, pasienadmisi_id integer, tgl_admisi timestamp without time zone, tgl_pulang timestamp without time zone, status_keluar boolean, rawat_gabung boolean, kamarruangan_id integer, nama_pegawai character varying, status_konfirmasi character varying, tgl_konfirmasi timestamp without time zone, pegawai_id integer, anakke smallint, jumlah_bersaudara smallint, no_telepon_pasien character varying, no_mobile_pasien character varying, warga_negara character varying, suku_id integer, suku_nama character varying, pendidikan_id integer, pendidikan_nama character varying, nama_ibu character varying, nama_ayah character varying, nopeserta character varying, tglcetakkartuasuransi timestamp without time zone, kodefeskestk1 character varying, nama_feskestk1 character varying, masaberlakukartu date, nokartukeluarga character varying, nopassport character varying, is_active boolean, keterangan_pendaftaran text, kelompokpegawai_id integer, is_deleted boolean, status_periksa character varying, kamarruangan_nokamar character varying, no_tempattidur character varying, pasienpulang_id integer, kondisikeluar_id integer, kondisikeluar_nama character varying, carakeluar_id integer, carakeluar_namalain character varying, jenis_kelamin character varying, agama character varying, jenisidentitas character varying, namadepan character varying, golongandarah character varying, statusperkawinan character varying, status_pasien character varying, kunjungan character varying, gelardepan character varying, gelarbelakang character varying, rhesus character varying, status_masuk character varying, jeniskelamin character varying, alasan_batal text, status_ranap integer, status_ranap_nama character varying, golonganumur_nama character varying, carakeluar_nama character varying, kamartempattidur_id integer, nosep character varying, bpjs_id integer, is_pasientitipan boolean, is_stoptitipan boolean, pindahkamar_id integer, kelas_ditagihkan_id integer, kelas_ditagihkan_nama character varying, is_stoppasientitipan boolean, is_pasientitipan_pk boolean, tgl_keluar timestamp without time zone, diagnosa text)
 LANGUAGE plpgsql
 STABLE
AS $function$
	BEGIN
	RETURN QUERY
	WITH pasienadmisi_t AS (
	    SELECT
	        pasienadmisi_t.pendaftaran_id,
	        pasienadmisi_t.caramasuk_id,
	        pasienadmisi_t.kamarruangan_id,
	        pasienadmisi_t.carabayar_id,
	        pasienadmisi_t.kelaspelayanan_id,
	        pasienadmisi_t.penjamin_id,
	        pasienadmisi_t.ruangan_id,
	        pasienadmisi_t.pasienadmisi_id,
	        pasienadmisi_t.tgl_admisi,
	        pasienadmisi_t.tgl_pulang,
	        pasienadmisi_t.status_keluar,
	        pasienadmisi_t.rawat_gabung,
	        pasienadmisi_t.pegawai_id,
	        pasienadmisi_t.status_ranap,
	        pasienadmisi_t.kamartempattidur_id,
	        pasienadmisi_t.is_pasientitipan,
	        pasienadmisi_t.kelas_ditagihkan_id,
	        pasienadmisi_t.is_stoptitipan,
	        pasienadmisi_t.pasienpulang_id,
	        pasienadmisi_t.pasienbatalperiksa_id
	    FROM pasienadmisi_t
	    WHERE pasienadmisi_t.tgl_admisi >= xfirstdate 
		AND pasienadmisi_t.tgl_admisi <= xlastdate
	), 
	pendaftaran_t AS (
	    SELECT
	        pendaftaran_t.pendaftaran_id,
	        pendaftaran_t.pasien_id,
	        pendaftaran_t.no_pendaftaran,
	        pendaftaran_t.no_urutantri,
	        pendaftaran_t.transportasi,
	        pendaftaran_t.keadaan_masuk,
	        pendaftaran_t.alih_status,
	        pendaftaran_t.by_phone,
	        pendaftaran_t.kunjungan_rumah,
	        pendaftaran_t.umur,
	        pendaftaran_t.golonganumur_id,
	        pendaftaran_t.shift_id,
	        pendaftaran_t.keterangan_pendaftaran,
	        pendaftaran_t.status_periksa,
	        pendaftaran_t.status_pasien,
	        pendaftaran_t.kunjungan,
	        pendaftaran_t.status_masuk,
	        pendaftaran_t.pasienadmisi_id,
	        pendaftaran_t.asuransipasien_id,
	        pendaftaran_t.rujukan_id,
	        pendaftaran_t.penanggungjawab_id,
	        pendaftaran_t.jeniskasuspenyakit_id,
	        pendaftaran_t.bpjs_id
	    FROM pendaftaran_t
	    JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
	    WHERE pendaftaran_t.is_active = true 
	    AND pendaftaran_t.is_deleted = false 
	    AND pendaftaran_t.status_periksa::text <> '402'::text 
	    AND pendaftaran_t.status_periksa::text <> '628'::text 
	    AND pendaftaran_t.status_periksa::text <> '453'::text
	), bpjs_t AS (
	    SELECT 
	        bpjs_t.nosep, 
	        bpjs_t.bpjs_id, 
	        bpjs_t.is_deleted
	    FROM bpjs_t 
	    join pendaftaran_t on bpjs_t.bpjs_id = pendaftaran_t.bpjs_id 
	    WHERE bpjs_t.is_deleted = false
	), data_pindah_kamar AS (
	    SELECT 
	        pindahkamar_t.pindahkamar_id,
	        pindahkamar_t.pasienadmisi_id,
	        pindahkamar_t.kelas_ditagihkan_id,
	        pindahkamar_t.is_pasientitipan,
	        pindahkamar_t.is_stoptitipan
	    FROM pindahkamar_t
	    JOIN ( 
	        SELECT 
	            max(pindahkamar_t.pindahkamar_id) AS pindahkamar_id,
	            pindahkamar_t.pasienadmisi_id
	        FROM pindahkamar_t
	        JOIN pasienadmisi_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
	        GROUP BY pindahkamar_t.pasienadmisi_id
	    ) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
	    WHERE pindahkamar_t.is_deleted = false
	), pindah_kamar AS (
	    SELECT 
	        data_pindah_kamar.pindahkamar_id,
	        data_pindah_kamar.pasienadmisi_id,
	        data_pindah_kamar.kelas_ditagihkan_id,
	        kelaspelayanan_m.kelaspelayanan_nama AS kelas_ditagihkan,
	        data_pindah_kamar.is_stoptitipan
	    FROM data_pindah_kamar
	    LEFT JOIN kelaspelayanan_m ON data_pindah_kamar.kelas_ditagihkan_id = kelaspelayanan_m.kelaspelayanan_id
	    WHERE data_pindah_kamar.is_pasientitipan = true
	), stop_titipan AS (
	    SELECT
	        data_pindah_kamar.pindahkamar_id,
	        data_pindah_kamar.pasienadmisi_id,
	        data_pindah_kamar.is_pasientitipan,
	        data_pindah_kamar.is_stoptitipan
	    FROM data_pindah_kamar
	), resume AS (
	    SELECT 
	        resumemedisri_t.pendaftaran_id,
	        resumemedisri_t.tgl_keluar,
	        resumemedisri_t.diag_utama ->> 'text'::text AS diagnosa
	    FROM resumemedisri_t
	    JOIN pendaftaran_t ON resumemedisri_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
	    JOIN ( 
	        SELECT 
	            max(resumemedisri_t.resumemedisri_id) AS resumemedisri_id,
	            resumemedisri_t.pendaftaran_id
	        FROM resumemedisri_t
	        JOIN pendaftaran_t ON resumemedisri_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
	        GROUP BY resumemedisri_t.pendaftaran_id
	    ) resume_max ON resumemedisri_t.resumemedisri_id = resume_max.resumemedisri_id
	)
	SELECT
	    pasien_m.pasien_id,
	    pasien_m.no_identitas_pasien,
	    pasien_m.nama_pasien,
	    pasien_m.nama_panggilan AS nama_bin,
	    pasien_m.tempat_lahir,
	    pasien_m.tanggal_lahir,
	    pasien_m.alamat_pasien,
	    pasien_m.rt,
	    pasien_m.rw,
	    pasien_m.photopasien,
	    pasien_m.alamatemail,
	    pasien_m.statusrekammedis,
	    pasien_m.no_rekam_medik,
	    pasien_m.tgl_rekam_medik,
	    pasien_m.pekerjaan_id,
	    pekerjaan_m.pekerjaan_nama, pasien_m.kabupaten_id,
	    CASE
	        WHEN (EXISTS ( SELECT kabupaten_m.kabupaten_nama
	            FROM kabupaten_m
	            WHERE kabupaten_m.kabupaten_id = pasien_m.kabupaten_id
	            LIMIT 1)) THEN ( SELECT kabupaten_m.kabupaten_nama
	            FROM kabupaten_m
	            WHERE kabupaten_m.kabupaten_id = pasien_m.kabupaten_id)
	        ELSE NULL::character varying
	    END AS kabupaten_nama,
	    pendaftaran_t.pendaftaran_id,
	    pendaftaran_t.no_pendaftaran,
	    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
	    pendaftaran_t.no_urutantri,
	    pendaftaran_t.transportasi,
	    pendaftaran_t.keadaan_masuk,
	    pendaftaran_t.alih_status,
	    pendaftaran_t.by_phone,
	    pendaftaran_t.kunjungan_rumah,
	    pendaftaran_t.umur,
	    pendaftaran_t.golonganumur_id,
	    asuransipasien_m.nokartuasuransi AS no_asuransi,
	    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
	    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
	    carabayar_m.carabayar_id,
	    carabayar_m.carabayar_nama,
	    penjamin_m.penjamin_id,
	    penjamin_m.penjamin_nama,
	    caramasuk_m.caramasuk_id,
	    caramasuk_m.caramasuk_nama,
	    pendaftaran_t.shift_id,
	    rujukan_t.no_rujukan,
	    rujukan_t.nama_perujuk,
	    rujukan_t.tanggal_rujukan,
	    rujukan_t.kodediagnosa_rujukan,
	    asalrujukan_m.asalrujukan_id,
	    asalrujukan_m.asalrujukan_nama,
	    penanggungjawab_m.penanggungjawab_id,
	    penanggungjawab_m.pengantar,
	    penanggungjawab_m.hubungankeluarga,
	    penanggungjawab_m.penanggungjawab_nama,
	    ruangan_m.ruangan_id,
	    ruangan_m.ruangan_nama,
	    instalasi_m.instalasi_id,
	    instalasi_m.instalasi_nama,
	    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
	    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
	    kelaspelayanan_m.kelaspelayanan_id,
	    kelaspelayanan_m.kelaspelayanan_nama,
	    pasienadmisi_t.pasienadmisi_id,
	    pasienadmisi_t.tgl_admisi,
	    pasienadmisi_t.tgl_pulang,
	    pasienadmisi_t.status_keluar,
	    pasienadmisi_t.rawat_gabung,
	    kamarruangan_m.kamarruangan_id,
	    pegawai_m.nama_pegawai,
	    asuransipasien_m.status_konfirmasi,
	    asuransipasien_m.tgl_konfirmasi,
	    pasienadmisi_t.pegawai_id,
	    pasien_m.anakke,
	    pasien_m.jumlah_bersaudara,
	    pasien_m.no_telepon_pasien,
	    pasien_m.no_mobile_pasien,
	    pasien_m.warga_negara,
	    suku_m.suku_id,
	    suku_m.suku_nama,
	    pendidikan_m.pendidikan_id,
	    pendidikan_m.pendidikan_nama,
	    pasien_m.nama_ibu,
	    pasien_m.nama_ayah,
	    asuransipasien_m.nopeserta,
	    asuransipasien_m.tglcetakkartuasuransi,
	    asuransipasien_m.kodefeskestk1,
	    asuransipasien_m.nama_feskestk1,
	    asuransipasien_m.masaberlakukartu,
	    asuransipasien_m.nokartukeluarga,
	    asuransipasien_m.nopassport,
	    asuransipasien_m.is_active,
	    pendaftaran_t.keterangan_pendaftaran,
	    pegawai_m.kelompokpegawai_id,
	    pasien_m.is_deleted,
	    status_periksa.lookup_name as status_periksa,
	    kamarruangan_m.kamarruangan_nokamar,
	    kamartempattidur_m.no_tempattidur,
	    pasienpulang_t.pasienpulang_id,
	    pasienpulang_t.kondisikeluar_id,
	    kondisikeluar_m.kondisikeluar_nama,
	    pasienpulang_t.carakeluar_id,
	    carakeluar_m.carakeluar_namalain,
	    jenis_kelamin.lookup_name as jenis_kelamin,
	    agama.lookup_name as agama,
	    jenisidentitas.lookup_name as jenisidentitas,
	    namadepan.lookup_name as namadepan,
	    golongandarah.lookup_name as golongandarah,
	    statusperkawinan.lookup_name as statusperkawinan,
	    status_pasien.lookup_name as status_pasien,
	    kunjungan.lookup_name as kunjungan,
	    gelardepan.lookup_name as gelardepan,
	    gelarbelakang.lookup_name as gelarbelakang,
	    rhesus.lookup_name as rhesus,
	    status_masuk.lookup_name as status_masuk,
	    pasien_m.jeniskelamin,
	    pasienbatalperiksa_t.alasan_batal,
	    pasienadmisi_t.status_ranap,
	    status_ranap_nama.lookup_name as status_ranap_nama,
	    golonganumur_m.golonganumur_nama,
	    carakeluar_m.carakeluar_nama,
	    pasienadmisi_t.kamartempattidur_id,
	    bpjs_t.nosep,
	    bpjs_t.bpjs_id,
	    pasienadmisi_t.is_pasientitipan,
	    pasienadmisi_t.is_stoptitipan,
	    pindah_kamar.pindahkamar_id,
	    CASE
	        WHEN pindah_kamar.pindahkamar_id IS NULL THEN pasienadmisi_t.kelas_ditagihkan_id
	        ELSE pindah_kamar.kelas_ditagihkan_id
	    END AS kelas_ditagihkan_id,
	    CASE
	        WHEN pindah_kamar.pindahkamar_id IS NULL THEN kelas_ditagihkan.kelaspelayanan_nama
	        ELSE pindah_kamar.kelas_ditagihkan
	    END AS kelas_ditagihkan_nama,
	    CASE
	        WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS FALSE THEN false
	        WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS TRUE THEN true
	        WHEN stop_titipan.is_pasientitipan IS FALSE AND stop_titipan.is_stoptitipan IS FALSE THEN true
	        WHEN stop_titipan.is_pasientitipan IS TRUE AND stop_titipan.is_stoptitipan IS TRUE THEN true
	        ELSE false
	    END AS is_stoppasientitipan,
	    stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
	    resume.tgl_keluar,
	    resume.diagnosa
	FROM pendaftaran_t
	JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
	JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
	LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
	LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
	LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
	LEFT JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
	LEFT JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
	LEFT JOIN caramasuk_m ON pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id
	LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
	LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
	LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
	LEFT JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
	LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
	LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
	LEFT JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
	LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
	LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
	LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
	LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
	LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
	LEFT JOIN pasienbatalperiksa_t ON pasienadmisi_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
	LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
	LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
	LEFT JOIN pindah_kamar ON pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id
	LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
	LEFT JOIN stop_titipan ON pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id
	LEFT JOIN resume ON pendaftaran_t.pendaftaran_id = resume.pendaftaran_id
	LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
	LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
	LEFT JOIN lookup_m status_periksa ON status_periksa.lookup_id = pendaftaran_t.status_periksa::integer
	LEFT JOIN lookup_m jenis_kelamin ON jenis_kelamin.lookup_id = pasien_m.jeniskelamin::integer
	LEFT JOIN lookup_m agama ON agama.lookup_id = pasien_m.agama::integer
	LEFT JOIN lookup_m jenisidentitas ON jenisidentitas.lookup_id = pasien_m.jenisidentitas::integer
	LEFT JOIN lookup_m namadepan ON namadepan.lookup_id = pasien_m.namadepan::integer
	LEFT JOIN lookup_m golongandarah ON golongandarah.lookup_id = pasien_m.golongandarah::integer
	LEFT JOIN lookup_m statusperkawinan ON statusperkawinan.lookup_id = pasien_m.statusperkawinan::integer
	LEFT JOIN lookup_m status_pasien ON status_pasien.lookup_id = pendaftaran_t.status_pasien::integer
	LEFT JOIN lookup_m kunjungan ON kunjungan.lookup_id = pendaftaran_t.kunjungan::integer
	LEFT JOIN lookup_m gelardepan ON gelardepan.lookup_id = pegawai_m.gelardepan::integer
	LEFT JOIN lookup_m gelarbelakang ON gelarbelakang.lookup_id = pegawai_m.gelarbelakang::integer
	LEFT JOIN lookup_m rhesus ON rhesus.lookup_id = pasien_m.rhesus::integer
	LEFT JOIN lookup_m status_masuk ON status_masuk.lookup_id = pendaftaran_t.status_masuk::integer
	LEFT JOIN lookup_m status_ranap_nama ON status_ranap_nama.lookup_id = pasienadmisi_t.status_ranap::integer;

	END;
$function$
;