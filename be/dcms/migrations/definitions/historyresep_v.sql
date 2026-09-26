-- public.historyresep_v source

CREATE OR REPLACE VIEW public.historyresep_v
AS SELECT
	'reseptur' :: TEXT AS jenis,
	reseptur_t.reseptur_id,
	penjualanresep_t.penjualanresep_id AS resep_id,
	pendaftaran_t.pasien_id,
	pendaftaran_t.pendaftaran_id,
	reseptur_t.noresep AS no_reseptur,
	COALESCE ( penjualanresep_t.noresep, reseptur_t.noresep ) AS nomor,
	reseptur_t.tglreseptur,
	penjualanresep_t.tglresep,
	COALESCE ( reseptur_t.tglreseptur, penjualanresep_t.tglresep ) AS tgl_resep,
	pegawai_m.nama_pegawai,
	instalasi_reseptur.instalasi_nama AS instalasi_reseptur,
	ruangan_reseptur.ruangan_nama AS ruangan_reseptur,
	instalasi_tujuan.instalasi_nama AS instalasi_resep,
	ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
	reseptur_t.kategori_resep,
	kategori_resep_lookup.lookup_name AS kategori_resep_nama,
	kategori_resep_lookup.lookup_kode AS kategori_resep_kode 
FROM
	reseptur_t
	LEFT JOIN ( SELECT A.penjualanresep_id, A.noresep, A.reseptur_id, A.tglresep FROM penjualanresep_t A ) penjualanresep_t ON penjualanresep_t.reseptur_id = reseptur_t.reseptur_id
	JOIN (
	SELECT
		pendaftaran.pendaftaran_id,
		pendaftaran.pasien_id,
		pendaftaran.pasienadmisi_id,
		pendaftaran.kelaspelayanan_id 
	FROM
		pendaftaran_t pendaftaran
		LEFT JOIN ( SELECT A.pendaftaran_id, A.pasienadmisi_id FROM pasienadmisi_t A ) pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id 
	) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
	JOIN ( SELECT pegawai.pegawai_id, pegawai.tgl_lahirpegawai, pegawai.nama_pegawai, pegawai.alamat_pegawai FROM pegawai_m pegawai ) pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
	JOIN ( SELECT ruangan_2.ruangan_id, ruangan_2.ruangan_nama, ruangan_2.instalasi_id FROM ruangan_m ruangan_2 ) ruangan_reseptur ON reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id
	JOIN ( SELECT instalasi_1.instalasi_id, instalasi_1.instalasi_nama FROM instalasi_m instalasi_1 ) instalasi_reseptur ON ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id
	JOIN ( SELECT ruangan_1.ruangan_id, ruangan_1.ruangan_nama, ruangan_1.instalasi_id FROM ruangan_m ruangan_1 ) ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
	JOIN ( SELECT instalasi_2.instalasi_id, instalasi_2.instalasi_nama FROM instalasi_m instalasi_2 ) instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
	LEFT JOIN ( SELECT lookup_m.lookup_id, lookup_m.lookup_name, lookup_m.lookup_kode FROM lookup_m ) kategori_resep_lookup ON reseptur_t.kategori_resep = kategori_resep_lookup.lookup_id 
WHERE
	reseptur_t.is_deleted = FALSE 
	AND reseptur_t.is_active = TRUE 
	AND reseptur_t.penjualanresep_id IS NULL UNION ALL
SELECT
	'resep' :: TEXT AS jenis,
	reseptur_t.reseptur_id,
	penjualanresep_t.penjualanresep_id AS resep_id,
	penjualanresep_t.pasien_id,
	penjualanresep_t.pendaftaran_id,
	reseptur_t.noresep AS no_reseptur,
	COALESCE ( penjualanresep_t.noresep, reseptur_t.noresep ) AS nomor,
	reseptur_t.tglreseptur,
	penjualanresep_t.tglresep,
	COALESCE ( reseptur_t.tglreseptur, penjualanresep_t.tglresep ) AS tgl_resep,
	pegawai_m.nama_pegawai,
CASE
		
		WHEN reseptur_t.reseptur_id IS NULL THEN
		instalasi_resep.instalasi_nama 
		WHEN reseptur_t.reseptur_id IS NOT NULL THEN
		im.instalasi_nama ELSE NULL :: CHARACTER VARYING 
	END AS instalasi_reseptur,
CASE
		
		WHEN reseptur_t.reseptur_id IS NULL THEN
		ruangan_resep.ruangan_nama 
		WHEN reseptur_t.reseptur_id IS NOT NULL THEN
		rm.ruangan_nama ELSE NULL :: CHARACTER VARYING 
	END AS ruangan_reseptur,
	instalasi_resep.instalasi_nama AS instalasi_resep,
	ruangan_resep.ruangan_nama AS ruangan_tujuan,
	reseptur_t.kategori_resep,
	kategori_resep_lookup.lookup_name AS kategori_resep_nama,
	kategori_resep_lookup.lookup_kode AS kategori_resep_kode 
FROM
	penjualanresep_t
	LEFT JOIN ( SELECT A.reseptur_id, A.tglreseptur, A.noresep, A.ruanganreseptur_id, A.kategori_resep FROM reseptur_t A ) reseptur_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
	LEFT JOIN ( SELECT peg_1.pegawai_id, peg_1.nama_pegawai, peg_1.gelardepan FROM pegawai_m peg_1 ) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
	JOIN ( SELECT ruangan.ruangan_id, ruangan.ruangan_nama, ruangan.instalasi_id FROM ruangan_m ruangan ) ruangan_resep ON penjualanresep_t.ruangan_id = ruangan_resep.ruangan_id
	JOIN ( SELECT instalasi.instalasi_id, instalasi.instalasi_nama FROM instalasi_m instalasi ) instalasi_resep ON ruangan_resep.instalasi_id = instalasi_resep.instalasi_id
	LEFT JOIN ( SELECT A.ruangan_id, A.ruangan_nama, A.instalasi_id FROM ruangan_m A ) rm ON rm.ruangan_id = reseptur_t.ruanganreseptur_id
	LEFT JOIN ( SELECT lookup_m.lookup_id, lookup_m.lookup_name, lookup_m.lookup_kode FROM lookup_m ) kategori_resep_lookup ON kategori_resep_lookup.lookup_id = reseptur_t.kategori_resep
	LEFT JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) im ON im.instalasi_id = rm.instalasi_id;