-- public.expertise_v source

CREATE OR REPLACE VIEW public.expertise_v
AS SELECT
	expertise_m.expertise_id,
	expertise_m.nama_expertise,
	expertise_m.pemeriksaanrad_id,
	pemeriksaanrad_m.pemeriksaanrad_nama,
	expertise_m.hasil_expertise,
	expertise_m.kesan,
	expertise_m.kesimpulan,
	pegawai_m.pegawai_id,
	pegawai_m.nama_pegawai 
FROM
	expertise_m
	JOIN pemeriksaanrad_m ON expertise_m.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
	LEFT JOIN pegawai_m ON expertise_m.pegawai_id = pegawai_m.pegawai_id 
WHERE
	expertise_m.is_active = TRUE 
	AND expertise_m.is_deleted = FALSE;