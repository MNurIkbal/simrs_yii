<?php

use yii\db\Migration;

/**
 * Class m211210_061130_migrate_hotfix_inforesep_10212021
 */
class m211210_061130_migrate_hotfix_inforesep_10212021 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
 	   	$this->execute('DROP VIEW if exists public.inforesep_v;');
		
        $this->execute("
            CREATE VIEW \"public\".\"inforesep_v\" AS
	    SELECT 'reseptur'::text AS jenis,
	       reseptur_t.reseptur_id,
	       reseptur_t.penjualanresep_id AS resep_id,
	       reseptur_t.pasien_id,
	       reseptur_t.pendaftaran_id,
	       reseptur_t.pasienadmisi_id,
	       pendaftaran_t.carabayar_id,
	       pendaftaran_t.penjamin_id,
	       pendaftaran_t.umur,
	       kelaspelayanan_m.kelaspelayanan_nama,
	       reseptur_t.ruangan_id,
	       reseptur_t.ruanganreseptur_id,
	       reseptur_t.tglreseptur,
	       penjualanresep_t.tglresep,
	       reseptur_t.noresep AS no_reseptur,
	       penjualanresep_t.noresep AS no_resep,
	       reseptur_t.noresep AS nomor,
	       penjualanresep_t.penjualanresep_id,
	       pendaftaran_t.no_pendaftaran,
	       pasien_m.no_rekam_medik,
	       pasien_m.nama_pasien,
	           CASE
	               WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN pegawai_m.tgl_lahirpegawai
	               ELSE pasien_m.tanggal_lahir
	           END AS tanggal_lahir,
	       fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
	       carabayar_m.carabayar_nama,
	       penjamin_m.penjamin_nama,
	       ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
	       ruangan_reseptur.ruangan_nama AS ruangan_reseptur,
	           CASE
	               WHEN (penjualanresep_t.penjualanresep_id IS NULL) THEN 'Belum Proses'::character varying
	               WHEN (penjualanresep_t.status_reseptur = 347) THEN 'Dalam Proses'::character varying
	               ELSE fgetnamalookup((penjualanresep_t.status_reseptur)::integer)
	           END AS status_reseptur,
	       reseptur_t.pegawai_id,
	       pegawai_m.nama_pegawai,
	       ruangan_reseptur.instalasi_id AS instalasi_reseptur_id,
	       instalasi_reseptur.instalasi_nama AS instalasi_reseptur,
	       ruangan_tujuan.instalasi_id AS instalasi_resep_id,
	       instalasi_tujuan.instalasi_nama AS instalasi_resep,
	       antrian_t.no_antrian,
	       reseptur_t.status_reseptur AS status_reseptur_id,
	       reseptur_t.is_hamil,
	           CASE
	               WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN (pemeriksaanfisik_t.bb)::text
	               WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (asesmenmedisrd_t.bb)::text
	               WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (asesmenmedis_t.bb)::text
	               ELSE '-'::text
	           END AS berat_badan,
	           CASE
	               WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN (pemeriksaanfisik_t.tb)::text
	               WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (asesmenmedisrd_t.tb)::text
	               WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (asesmenmedis_t.tb)::text
	               ELSE '-'::text
	           END AS tinggi_badan,
	       reseptur_t.luas_tubuh,
	       reseptur_t.diagnosa_id,
	       concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama) AS diagnosa_nama,
	       reseptur_t.instruksi_id,
	       reseptur_t.antrian_id,
	       penjualanresep_t.catatan,
	       resepturdetail_t.iter,
	       penjualanresep_t.noresep AS noresep_penjualan,
	       resepturdetail_t.iter AS iter_penjualan,
	           CASE
	               WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
	               WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
	               ELSE asesmenawal_t.nama_alergi
	           END AS riwayat_alergi,
	           CASE
	               WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
	               WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
	               WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
	               ELSE NULL::text
	           END AS diagnosa_text,
	       resepturdetail_t.antrian_racikan,
	       resepturdetail_t.harga_netto AS total_harganetto,
	           CASE
	               WHEN (reseptur_t.penjualanresep_id IS NULL) THEN reseptur_t.biaya_administrasi
	               ELSE penjualanresep_t.biayaadministrasi
	           END AS biayaadministrasi,
	       penjualanresep_t.totalhargajual,
	       (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihan,
	       NULL::character varying AS nama_pembeli,
	       penjualanresep_t.status_bayar,
	       COALESCE(penjualanresep_t.tglresep, reseptur_t.tglreseptur) AS tgl_resep_dibuat,
	       concat(fgetnamalookup((pasien_m.namadepan)::integer), ' ', pasien_m.nama_pasien) AS nama,
	       NULL::character varying AS jenispenjualan_id,
	       NULL::character varying AS jenispenjualan_nama,
	       reseptur_t.status_worklist,
	       fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
	       penjualanresep_t.kelaspelayanan_id,
	       penjualanresep_t.is_approve,
	       pegawai_approve.nama_pegawai AS pegawai_approve,
	       penjualanresep_t.tgl_approve,
	       penjualanresep_t.additional_data,
	           CASE
	               WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN pegawai_m.alamat_pegawai
	               ELSE pasien_m.alamat_pasien
	           END AS alamat_pasien
	      FROM (((((((((((((((((((((((reseptur_t
	        LEFT JOIN ( SELECT penjualanresep.penjualanresep_id,
	               penjualanresep.tglresep,
	               penjualanresep.noresep,
	               penjualanresep.status_reseptur,
	               penjualanresep.status_bayar,
	               penjualanresep.catatan,
	               penjualanresep.biayaadministrasi,
	               penjualanresep.totalhargajual,
	               penjualanresep.kelaspelayanan_id,
	               penjualanresep.is_approve,
	               penjualanresep.tgl_approve,
	               penjualanresep.pegawai_approve_id,
	               penjualanresep.additional_data,
	               penjualanresep.jenispenjualan
	              FROM penjualanresep_t penjualanresep
	             WHERE (penjualanresep.is_deleted = false)) penjualanresep_t ON ((reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
	        JOIN ( SELECT pendaftaran.pendaftaran_id,
	               pendaftaran.pasienadmisi_id,
	               pendaftaran.kelaspelayanan_id,
	               pendaftaran.carabayar_id,
	               pendaftaran.penjamin_id,
	               pendaftaran.umur,
	               pendaftaran.no_pendaftaran,
	               pendaftaran.instalasi_id
	              FROM pendaftaran_t pendaftaran) pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
	        JOIN ( SELECT pasien.pasien_id,
	               pasien.nama_pasien,
	               pasien.no_rekam_medik,
	               pasien.tanggal_lahir,
	               pasien.jeniskelamin,
	               pasien.namadepan,
	               pasien.alamat_pasien
	              FROM pasien_m pasien) pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
	        JOIN ( SELECT ruangan_1.ruangan_id,
	               ruangan_1.ruangan_nama,
	               ruangan_1.instalasi_id
	              FROM ruangan_m ruangan_1) ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
	        JOIN ( SELECT ruangan_2.ruangan_id,
	               ruangan_2.ruangan_nama,
	               ruangan_2.instalasi_id
	              FROM ruangan_m ruangan_2) ruangan_reseptur ON ((reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id)))
	        JOIN ( SELECT instalasi_1.instalasi_id,
	               instalasi_1.instalasi_nama
	              FROM instalasi_m instalasi_1) instalasi_reseptur ON ((ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id)))
	        JOIN ( SELECT instalasi_2.instalasi_id,
	               instalasi_2.instalasi_nama
	              FROM instalasi_m instalasi_2) instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
	        JOIN ( SELECT kelas.kelaspelayanan_id,
	               kelas.kelaspelayanan_nama
	              FROM kelaspelayanan_m kelas) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
	        JOIN ( SELECT carabayar.carabayar_id,
	               carabayar.carabayar_nama
	              FROM carabayar_m carabayar) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
	        JOIN ( SELECT penjamin.penjamin_id,
	               penjamin.penjamin_nama
	              FROM penjamin_m penjamin) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
	        JOIN ( SELECT pegawai.pegawai_id,
	               pegawai.tgl_lahirpegawai,
	               pegawai.nama_pegawai,
	               pegawai.alamat_pegawai
	              FROM pegawai_m pegawai) pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
	        LEFT JOIN ( SELECT reseptur_detail.reseptur_id,
	               reseptur_detail.iter,
	               sum(reseptur_detail.harganetto_reseptur) AS harga_netto,
	               string_agg((reseptur_detail.racikan_id)::text, '-'::text) AS antrian_racikan
	              FROM resepturdetail_t reseptur_detail
	             WHERE (reseptur_detail.is_deleted = false)
	             GROUP BY reseptur_detail.reseptur_id, reseptur_detail.iter) resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
	        LEFT JOIN ( SELECT antrian.antrian_id,
	               antrian.no_antrian
	              FROM antrian_t antrian) antrian_t ON ((reseptur_t.antrian_id = antrian_t.antrian_id)))
	        LEFT JOIN ( SELECT diagnosa.diagnosa_id,
	               diagnosa.diagnosa_kode,
	               diagnosa.diagnosa_nama
	              FROM diagnosa_m diagnosa) diagnosa_m ON ((reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id)))
	        LEFT JOIN ( SELECT DISTINCT ON (anamnesa.pendaftaran_id) anamnesa.pendaftaran_id,
	               anamnesa.riwayat_alergiobat
	              FROM anamnesa_t anamnesa) anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
	        LEFT JOIN ( SELECT DISTINCT ON (asesmenperawatrd.pendaftaran_id) asesmenperawatrd.pendaftaran_id,
	               asesmenperawatrd.alergi_obat
	              FROM asesmenperawatrd_t asesmenperawatrd) asesmenperawatrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id)))
	        LEFT JOIN ( SELECT DISTINCT ON (asesmenawal.pendaftaran_id) asesmenawal.pendaftaran_id,
	               asesmenawal.nama_alergi
	              FROM asesmenawal_t asesmenawal) asesmenawal_t ON ((pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id)))
	        LEFT JOIN ( SELECT DISTINCT ON (ass_medisrd.pendaftaran_id) ass_medisrd.pendaftaran_id,
	               ass_medisrd.tinggi_badan AS tb,
	               ass_medisrd.berat_badan AS bb
	              FROM asesmenmedisrd_t ass_medisrd
	             WHERE (ass_medisrd.is_deleted = false)) asesmenmedisrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id)))
	        LEFT JOIN ( SELECT DISTINCT ON (ass_medis.pendaftaran_id) ass_medis.pendaftaran_id,
	               ass_medis.tinggi_badan AS tb,
	               ass_medis.berat_badan AS bb
	              FROM asesmenmedis_t ass_medis
	             WHERE (ass_medis.is_deleted = false)) asesmenmedis_t ON ((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id)))
	        LEFT JOIN ( SELECT DISTINCT ON (pemeriksaan_fisik.pendaftaran_id) pemeriksaan_fisik.pendaftaran_id,
	               pemeriksaan_fisik.beratbadan_kg AS tb,
	               pemeriksaan_fisik.tinggibadan_cm AS bb
	              FROM pemeriksaanfisik_t pemeriksaan_fisik
	             WHERE (pemeriksaan_fisik.is_deleted = false)) pemeriksaanfisik_t ON ((pendaftaran_t.pendaftaran_id = pemeriksaanfisik_t.pendaftaran_id)))
	        LEFT JOIN ( SELECT DISTINCT ON (pasienmorbiditas.pendaftaran_id) pasienmorbiditas.pendaftaran_id,
	               pasienmorbiditas.diagnosa_pasien
	              FROM pasienmorbiditas_t pasienmorbiditas
	             WHERE ((pasienmorbiditas.is_deleted = false) AND (pasienmorbiditas.kelompokdiagnosa_id = 2))) pasienmorbiditas_t ON ((pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id)))
	        LEFT JOIN ( SELECT instruksi_t.instruksi_id,
	               cppt_t.cppt_id,
	               cppt_t.pendaftaran_id,
	               (cppt_t.a_diag_utama ->> 'text'::text) AS diagnosa_utama
	              FROM (instruksi_t
	                JOIN ( SELECT DISTINCT ON (cppt.pendaftaran_id) cppt.pendaftaran_id,
	                       cppt.cppt_id,
	                       cppt.a_diag_utama
	                      FROM cppt_t cppt
	                     WHERE (cppt.is_deleted = false)) cppt_t ON ((instruksi_t.cppt_id = cppt_t.cppt_id)))
	             WHERE ((instruksi_t.is_deleted = false) AND (instruksi_t.is_active = true))) cppt_rd ON (((pendaftaran_t.pendaftaran_id = cppt_rd.pendaftaran_id) AND (reseptur_t.instruksi_id = cppt_rd.instruksi_id))))
	        LEFT JOIN ( SELECT a.pegawai_id,
	               a.nama_pegawai
	              FROM pegawai_m a) pegawai_approve ON ((penjualanresep_t.pegawai_approve_id = pegawai_approve.pegawai_id)))
	     WHERE ((reseptur_t.is_deleted = false) AND (reseptur_t.is_active = true) AND (reseptur_t.penjualanresep_id IS NULL))
	   UNION ALL
	    SELECT 'resep'::text AS jenis,
	       reseptur_t.reseptur_id,
	       penjualanresep_t.penjualanresep_id AS resep_id,
	       penjualanresep_t.pasien_id,
	       penjualanresep_t.pendaftaran_id,
	       penjualanresep_t.pasienadmisi_id,
	       penjualanresep_t.carabayar_id,
	       penjualanresep_t.penjamin_id,
	       pendaftaran_t.umur,
	       kelaspelayanan_m.kelaspelayanan_nama,
	       penjualanresep_t.ruangan_id,
	       reseptur_t.ruanganreseptur_id,
	       reseptur_t.tglreseptur,
	       penjualanresep_t.tglresep,
	       reseptur_t.noresep AS no_reseptur,
	       penjualanresep_t.noresep AS no_resep,
	       penjualanresep_t.noresep AS nomor,
	       penjualanresep_t.penjualanresep_id,
	       pendaftaran_t.no_pendaftaran,
	       pasien_m.no_rekam_medik,
	       pasien_m.nama_pasien,
	           CASE
	               WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN karyawan.tgl_lahirpegawai
	               ELSE pasien_m.tanggal_lahir
	           END AS tanggal_lahir,
	       fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
	       carabayar_m.carabayar_nama,
	       penjamin_m.penjamin_nama,
	       ruangan_resep.ruangan_nama AS ruangan_tujuan,
	       rm.ruangan_nama AS ruangan_reseptur,
	           CASE
	               WHEN (penjualanresep_t.status_reseptur = 347) THEN 'Dalam Proses'::character varying
	               ELSE fgetnamalookup((penjualanresep_t.status_reseptur)::integer)
	           END AS status_reseptur,
	       penjualanresep_t.pegawai_id,
	       pegawai_m.nama_pegawai,
	       rm.instalasi_id AS instalasi_reseptur_id,
	       im.instalasi_nama AS instalasi_reseptur,
	       ruangan_resep.instalasi_id AS instalasi_resep_id,
	       instalasi_resep.instalasi_nama AS instalasi_resep,
	       antrian_t.no_antrian,
	       penjualanresep_t.status_reseptur AS status_reseptur_id,
	       NULL::boolean AS is_hamil,
	           CASE
	               WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN (periksa_fisik_rj.bb)::text
	               WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (periksa_fisik_rd.bb)::text
	               WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (periksa_fisik_ri.bb)::text
	               ELSE '-'::text
	           END AS berat_badan,
	           CASE
	               WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN (periksa_fisik_rj.tb)::text
	               WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (periksa_fisik_rd.tb)::text
	               WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (periksa_fisik_ri.tb)::text
	               ELSE '-'::text
	           END AS tinggi_badan,
	       NULL::character varying AS luas_tubuh,
	       NULL::integer AS diagnosa_id,
	       NULL::text AS diagnosa_nama,
	       NULL::integer AS instruksi_id,
	       NULL::integer AS antrian_id,
	       penjualanresep_t.catatan,
	       penjualanresep_t.iter,
	       penjualanresep_t.noresep AS noresep_penjualan,
	       NULL::integer AS iter_penjualan,
	       NULL::text AS riwayat_alergi,
	       NULL::text AS diagnosa_text,
	       NULL::text AS antrian_racikan,
	       NULL::double precision AS total_harganetto,
	       penjualanresep_t.biayaadministrasi,
	       penjualanresep_t.totalhargajual,
	       (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihan,
	       penjualanresep_t.nama_pembeli,
	       penjualanresep_t.status_bayar,
	       penjualanresep_t.tglresep AS tgl_resep_dibuat,
	           CASE
	               WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN penjualanresep_t.nama_pembeli
	               WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN (concat(fgetnamalookup((pasien_m.namadepan)::integer), ' ', pasien_m.nama_pasien))::character varying
	               WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN (concat(fgetnamalookup((pegawai_m.gelardepan)::integer), ' ', karyawan.nama_pegawai))::character varying
	               ELSE NULL::character varying
	           END AS nama,
	       penjualanresep_t.jenispenjualan AS jenispenjualan_id,
	       fgetnamalookup((penjualanresep_t.jenispenjualan)::integer) AS jenispenjualan_nama,
	       penjualanresep_t.status_worklist,
	           CASE
	               WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN NULL::character varying
	               WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN fgetnamalookup((pasien_m.namadepan)::integer)
	               WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN fgetnamalookup((pegawai_m.gelardepan)::integer)
	               ELSE NULL::character varying
	           END AS nama_depan,
	       penjualanresep_t.kelaspelayanan_id,
	       penjualanresep_t.is_approve,
	       pegawai_approve.nama_pegawai AS pegawai_approve,
	       penjualanresep_t.tgl_approve,
	       penjualanresep_t.additional_data,
	           CASE
	               WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN karyawan.alamat_pegawai
	               ELSE pasien_m.alamat_pasien
	           END AS alamat_pasien
	      FROM (((((((((((((((((penjualanresep_t
	        LEFT JOIN ( SELECT pendaftaran.pendaftaran_id,
	               pendaftaran.pasien_id,
	               pendaftaran.pasienadmisi_id,
	               pendaftaran.instalasi_id,
	               pendaftaran.umur,
	               pendaftaran.no_pendaftaran
	              FROM pendaftaran_t pendaftaran) pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
	        LEFT JOIN ( SELECT pasien.pasien_id,
	               pasien.nama_pasien,
	               pasien.no_rekam_medik,
	               pasien.tanggal_lahir,
	               pasien.jeniskelamin,
	               pasien.namadepan,
	               pasien.alamat_pasien
	              FROM pasien_m pasien) pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
	        JOIN ( SELECT ruangan.ruangan_id,
	               ruangan.ruangan_nama,
	               ruangan.instalasi_id
	              FROM ruangan_m ruangan) ruangan_resep ON ((penjualanresep_t.ruangan_id = ruangan_resep.ruangan_id)))
	        JOIN ( SELECT instalasi.instalasi_id,
	               instalasi.instalasi_nama
	              FROM instalasi_m instalasi) instalasi_resep ON ((ruangan_resep.instalasi_id = instalasi_resep.instalasi_id)))
	        LEFT JOIN ( SELECT kelas.kelaspelayanan_id,
	               kelas.kelaspelayanan_nama
	              FROM kelaspelayanan_m kelas) kelaspelayanan_m ON ((penjualanresep_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
	        LEFT JOIN ( SELECT carabayar.carabayar_id,
	               carabayar.carabayar_nama
	              FROM carabayar_m carabayar) carabayar_m ON ((penjualanresep_t.carabayar_id = carabayar_m.carabayar_id)))
	        LEFT JOIN ( SELECT penjamin.penjamin_id,
	               penjamin.penjamin_nama
	              FROM penjamin_m penjamin) penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
	        LEFT JOIN ( SELECT peg_1.pegawai_id,
	               peg_1.nama_pegawai,
	               peg_1.gelardepan
	              FROM pegawai_m peg_1) pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
	        LEFT JOIN ( SELECT antrian.antrian_id,
	               antrian.no_antrian
	              FROM antrian_t antrian) antrian_t ON ((penjualanresep_t.antrian_id = antrian_t.antrian_id)))
	        LEFT JOIN ( SELECT peg_2.pegawai_id,
	               peg_2.nama_pegawai,
	               peg_2.alamat_pegawai,
	               peg_2.tgl_lahirpegawai
	              FROM pegawai_m peg_2) karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
	        LEFT JOIN ( SELECT DISTINCT ON (pemeriksaanfisik_t.pendaftaran_id) pemeriksaanfisik_t.pendaftaran_id,
	               pemeriksaanfisik_t.tinggibadan_cm AS tb,
	               pemeriksaanfisik_t.beratbadan_kg AS bb
	              FROM pemeriksaanfisik_t
	             WHERE (pemeriksaanfisik_t.is_deleted = false)) periksa_fisik_rj ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id)))
	        LEFT JOIN ( SELECT DISTINCT ON (asesmenperawatrd_t.pendaftaran_id) asesmenperawatrd_t.pendaftaran_id,
	               asesmenperawatrd_t.tinggi_badan AS tb,
	               asesmenperawatrd_t.berat_badan AS bb
	              FROM asesmenperawatrd_t
	             WHERE (asesmenperawatrd_t.is_deleted = false)) periksa_fisik_rd ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id)))
	        LEFT JOIN ( SELECT DISTINCT ON (asesmenmedis_t.pendaftaran_id) asesmenmedis_t.pendaftaran_id,
	               asesmenmedis_t.tinggi_badan AS tb,
	               asesmenmedis_t.berat_badan AS bb
	              FROM asesmenmedis_t
	             WHERE (asesmenmedis_t.is_deleted = false)) periksa_fisik_ri ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id)))
	        LEFT JOIN reseptur_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
	        LEFT JOIN ruangan_m rm ON ((rm.ruangan_id = reseptur_t.ruanganreseptur_id)))
	        LEFT JOIN instalasi_m im ON ((im.instalasi_id = rm.instalasi_id)))
	        LEFT JOIN ( SELECT a.pegawai_id,
	               a.nama_pegawai
	              FROM pegawai_m a) pegawai_approve ON ((penjualanresep_t.pegawai_approve_id = pegawai_approve.pegawai_id))) ;");
       
            $this->execute('
                ALTER TABLE public.inforesep_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211210_061130_migrate_hotfix_inforesep_10212021 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211210_061130_migrate_hotfix_inforesep_10212021 cannot be reverted.\n";

        return false;
    }
    */
}
