<?php

use yii\db\Migration;

/**
 * Class m211115_035118_migrate_worklistresep_v
 */
class m211115_035118_migrate_worklistresep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('ALTER TABLE "public"."penjualanresep_t" 
  ADD COLUMN if not exists "pegawai_menyerahkan_id" int4;');

       $this->execute('ALTER TABLE "public"."penjualanresep_t" 
  ADD COLUMN if not exists "tgl_menyerahkan" timestamp(6);');

       $this->execute('DROP VIEW if exists "public"."worklistresep_v";');

       $this->execute("
        CREATE VIEW \"public\".\"worklistresep_v\" AS  SELECT 'a'::text AS jenis,
    penjualanresep_t.penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien) AS nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    anamnesa_t.riwayat_alergiobat AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup(reseptur_t.status_worklist::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup(penjualanresep_t.status_bayar::integer) AS status_bayar,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(penjualanresep_t.status_reseptur::integer) AS status_reseptur,
    periksa_fisik_rj.tinggi::character varying AS tinggi_badan,
    periksa_fisik_rj.berat::character varying AS berat_badan,
    reseptur_t.additional_data AS add_reseptur,
    penjualanresep_t.additional_data AS add_penjualaanresep,
    pendaftaran_t.pasienadmisi_id,
    pembayaranpelayanan_t.tgl_pembayaran,
    COALESCE(cppt_t.diagnosa_utama, resumemedisri_t.diagnosa_utama) AS diagnosa_utama,
    penjamin.penjamin_nama,
    penjamin.carabayar_nama,
        CASE
            WHEN jenis_resep.racikan_id = '1'::text THEN 'RACIKAN'::text
            WHEN jenis_resep.racikan_id = '2'::text THEN 'NON-RACIKAN'::text
            ELSE '-'::text
        END AS jenis_resep,
    ruangan.ruangan_nama AS ruangan,
    pendaftaran_t.kamarruangan_nokamar AS kamar,
    pendaftaran_t.no_tempattidur AS no_bed,
    pendaftaran_t.umur,
    peg_penginput.nama_pegawai AS pegawai_penginput,
    pembayaranpelayanan_t.nama_kasir,
    penjualanresep_t.log_user AS log_user_menyerahkan,
    penjualanresep_t.pegawai_menyerahkan_id,
    penjualanresep_t.tgl_menyerahkan,
    ruangan_asal.instalasi_id AS instalasi_resptur_id,
        CASE
            WHEN pasien_m.jeniskelamin::text = '15'::text THEN 'M'::text
            WHEN pasien_m.jeniskelamin::text = '16'::text THEN 'F'::text
            ELSE NULL::text
        END AS jeniskelamin
   FROM reseptur_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.pasien_id,
            a.no_pendaftaran,
            a.umur,
            kamar.kamarruangan_nokamar,
            bed.no_tempattidur,
                CASE
                    WHEN a.pasienadmisi_id IS NULL THEN a.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id
           FROM pendaftaran_t a
             LEFT JOIN ( SELECT a1.pasienadmisi_id,
                    a1.penjamin_id,
                    a1.kamarruangan_id,
                    a1.kamartempattidur_id
                   FROM pasienadmisi_t a1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a1.kamarruangan_id,
                    a1.kamarruangan_nokamar
                   FROM kamarruangan_m a1) kamar ON pasienadmisi_t.kamarruangan_id = kamar.kamarruangan_id
             LEFT JOIN ( SELECT a1.kamartempattidur_id,
                    a1.no_tempattidur
                   FROM kamartempattidur_m a1) bed ON pasienadmisi_t.kamartempattidur_id = bed.kamartempattidur_id) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.tanggal_lahir,
            a.no_rekam_medik,
            a.namadepan,
            a.jeniskelamin
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.penjualanresep_id,
            a.reseptur_id,
            a.pegawai_id,
            a.noresep,
            a.tglpenjualan,
            a.status_bayar,
            a.status_reseptur,
            a.additional_data,
            a.log_user,
            a.pegawai_menyerahkan_id,
            a.tgl_menyerahkan
           FROM penjualanresep_t a
          WHERE a.is_deleted IS TRUE AND a.status_reseptur = 432 OR a.is_deleted IS FALSE) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     LEFT JOIN ( SELECT a.penjualanresep_id,
            a.pembayaran_id
           FROM obatalkespasien_t a
          GROUP BY a.penjualanresep_id, a.pembayaran_id) pembayaran_obat ON penjualanresep_t.penjualanresep_id = pembayaran_obat.penjualanresep_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.tgl_pembayaran,
            pembayaran_t.created_by,
            pegawai_kasir.nama_pegawai AS nama_kasir
           FROM pembayaranpelayanan_t a
             LEFT JOIN ( SELECT a1.pembayaran_id,
                    a1.created_by
                   FROM pembayaran_t a1) pembayaran_t ON a.pembayaran_id = pembayaran_t.pembayaran_id
             LEFT JOIN ( SELECT a1.loginpemakai_id,
                    a1.pegawai_id
                   FROM loginpemakai_k a1) loginpemakai_k ON pembayaran_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a1.pegawai_id,
                    a1.nama_pegawai
                   FROM pegawai_m a1) pegawai_kasir ON loginpemakai_k.pegawai_id = pegawai_kasir.pegawai_id
          WHERE a.is_deleted = false) pembayaranpelayanan_t ON pembayaran_obat.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a
          WHERE a.instalasi_id = 1) ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
            a.alergi_obat AS riwayat_alergiobat
           FROM anamnesa_t a) anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
            a.tinggibadan_cm AS tinggi,
            a.beratbadan_kg AS berat
           FROM pemeriksaanfisik_t a
          WHERE a.is_deleted = false) periksa_fisik_rj ON pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (a.pasienadmisi_id) a.pasienadmisi_id,
            concat(a.a_diag_utama ->> 'kode'::text, ' - ', a.a_diag_utama ->> 'nama'::text) AS diagnosa_utama
           FROM cppt_t a
          WHERE a.is_deleted = false) cppt_t ON pendaftaran_t.pasienadmisi_id = cppt_t.pasienadmisi_id
     LEFT JOIN ( SELECT DISTINCT ON (a.pasienadmisi_id) a.pasienadmisi_id,
            concat(a.diag_utama ->> 'kode'::text, ' - ', a.diag_utama ->> 'nama'::text) AS diagnosa_utama
           FROM resumemedisri_t a
          WHERE a.is_deleted = false) resumemedisri_t ON pendaftaran_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama,
            carabayar_m.carabayar_nama
           FROM penjamin_m a
             JOIN ( SELECT a1.carabayar_id,
                    a1.carabayar_nama
                   FROM carabayar_m a1) carabayar_m ON a.carabayar_id = carabayar_m.carabayar_id) penjamin ON pendaftaran_t.penjamin_id = penjamin.penjamin_id
     LEFT JOIN ( SELECT string_agg(racikan.racikan_id::text, '-'::text) AS racikan_id,
            racikan.penjualanresep_id
           FROM ( SELECT a.racikan_id,
                    a.penjualanresep_id
                   FROM obatalkespasien_t a
                  GROUP BY a.racikan_id, a.penjualanresep_id
                  ORDER BY a.racikan_id) racikan
          GROUP BY racikan.penjualanresep_id) jenis_resep ON penjualanresep_t.penjualanresep_id = jenis_resep.penjualanresep_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan ON reseptur_t.ruanganreseptur_id = ruangan.ruangan_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id,
            pegawai.nama_pegawai
           FROM loginpemakai_k a
             JOIN ( SELECT a1.pegawai_id,
                    a1.nama_pegawai
                   FROM pegawai_m a1) pegawai ON a.pegawai_id = pegawai.pegawai_id) peg_penginput ON reseptur_t.created_by = peg_penginput.loginpemakai_id
UNION ALL
 SELECT 'b'::text AS jenis,
    penjualanresep_t.penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien) AS nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    anamnesa_t.riwayat_alergiobat AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup(reseptur_t.status_worklist::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    reseptur_t.tglreseptur AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup(penjualanresep_t.status_bayar::integer) AS status_bayar,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN periksa_fisik_rd.tinggi::character varying
            ELSE periksa_fisik_ri.tinggi
        END AS tinggi_badan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN periksa_fisik_rd.berat::character varying
            ELSE periksa_fisik_ri.berat
        END AS berat_badan,
    reseptur_t.additional_data AS add_reseptur,
    penjualanresep_t.additional_data AS add_penjualaanresep,
    pendaftaran_t.pasienadmisi_id,
    pembayaranpelayanan_t.tgl_pembayaran,
    COALESCE(cppt_t.diagnosa_utama, resumemedisri_t.diagnosa_utama) AS diagnosa_utama,
    penjamin.penjamin_nama,
    penjamin.carabayar_nama,
        CASE
            WHEN jenis_resep.racikan_id = '1'::text THEN 'RACIKAN'::text
            WHEN jenis_resep.racikan_id = '2'::text THEN 'NON-RACIKAN'::text
            ELSE 'RACIKAN'::text
        END AS jenis_resep,
    ruangan.ruangan_nama AS ruangan,
    pendaftaran_t.kamarruangan_nokamar AS kamar,
    pendaftaran_t.no_tempattidur AS no_bed,
    pendaftaran_t.umur,
    peg_penginput.nama_pegawai AS pegawai_penginput,
    pembayaranpelayanan_t.nama_kasir,
    penjualanresep_t.log_user AS log_user_menyerahkan,
    penjualanresep_t.pegawai_menyerahkan_id,
    penjualanresep_t.tgl_menyerahkan,
    ruangan_asal.instalasi_id AS instalasi_resptur_id,
        CASE
            WHEN pasien_m.jeniskelamin::text = '15'::text THEN 'M'::text
            WHEN pasien_m.jeniskelamin::text = '16'::text THEN 'F'::text
            ELSE '-'::text
        END AS jeniskelamin
   FROM reseptur_t
     LEFT JOIN ( SELECT b.reseptur_id,
            b.penjualanresep_id,
            b.noresep,
            b.status_bayar,
            b.additional_data,
            b.log_user,
            b.pegawai_menyerahkan_id,
            b.tgl_menyerahkan
           FROM penjualanresep_t b) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     JOIN ( SELECT b.pendaftaran_id,
            b.pasienadmisi_id,
            b.pasien_id,
            b.no_pendaftaran,
            b.umur,
            kamar.kamarruangan_nokamar,
            bed.no_tempattidur,
                CASE
                    WHEN b.pasienadmisi_id IS NULL THEN b.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id
           FROM pendaftaran_t b
             LEFT JOIN ( SELECT b1.pasienadmisi_id,
                    b1.penjamin_id,
                    b1.kamarruangan_id,
                    b1.kamartempattidur_id
                   FROM pasienadmisi_t b1) pasienadmisi_t ON b.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT b1.kamarruangan_id,
                    b1.kamarruangan_nokamar
                   FROM kamarruangan_m b1) kamar ON pasienadmisi_t.kamarruangan_id = kamar.kamarruangan_id
             LEFT JOIN ( SELECT b1.kamartempattidur_id,
                    b1.no_tempattidur
                   FROM kamartempattidur_m b1) bed ON pasienadmisi_t.kamartempattidur_id = bed.kamartempattidur_id) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT b.pasien_id,
            b.nama_pasien,
            b.tanggal_lahir,
            b.no_rekam_medik,
            b.namadepan,
            b.jeniskelamin
           FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama,
            b.instalasi_id
           FROM ruangan_m b
          WHERE b.instalasi_id <> 1) ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT b.penjualanresep_id,
            b.pembayaran_id
           FROM obatalkespasien_t b
          GROUP BY b.penjualanresep_id, b.pembayaran_id) pembayaran_obat ON penjualanresep_t.penjualanresep_id = pembayaran_obat.penjualanresep_id
     LEFT JOIN ( SELECT b.pembayaran_id,
            b.tgl_pembayaran,
            pembayaran_t.created_by,
            pegawai_kasir.nama_pegawai AS nama_kasir
           FROM pembayaranpelayanan_t b
             LEFT JOIN ( SELECT b1.pembayaran_id,
                    b1.created_by
                   FROM pembayaran_t b1) pembayaran_t ON b.pembayaran_id = pembayaran_t.pembayaran_id
             LEFT JOIN ( SELECT b1.loginpemakai_id,
                    b1.pegawai_id
                   FROM loginpemakai_k b1) loginpemakai_k ON pembayaran_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT b1.pegawai_id,
                    b1.nama_pegawai
                   FROM pegawai_m b1) pegawai_kasir ON loginpemakai_k.pegawai_id = pegawai_kasir.pegawai_id
          WHERE b.is_deleted = false) pembayaranpelayanan_t ON pembayaran_obat.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
     LEFT JOIN ( SELECT DISTINCT ON (b.pendaftaran_id) b.pendaftaran_id,
            b.tinggi_badan AS tinggi,
            b.berat_badan AS berat
           FROM asesmenperawatrd_t b
          WHERE b.is_deleted = false) periksa_fisik_rd ON pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (b.pendaftaran_id) b.pendaftaran_id,
            b.alergi_obat AS riwayat_alergiobat
           FROM anamnesa_t b) anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (b.pendaftaran_id) b.pendaftaran_id,
            b.tinggi_badan::character varying AS tinggi,
            b.berat_badan::character varying AS berat,
            b.diagnosa_id
           FROM asesmenmedis_t b
          WHERE b.is_deleted = false) periksa_fisik_ri ON pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (b.pasienadmisi_id) b.pasienadmisi_id,
            concat(b.a_diag_utama ->> 'kode'::text, ' - ', b.a_diag_utama ->> 'nama'::text) AS diagnosa_utama
           FROM cppt_t b
          WHERE b.is_deleted = false) cppt_t ON pendaftaran_t.pasienadmisi_id = cppt_t.pasienadmisi_id
     LEFT JOIN ( SELECT DISTINCT ON (b.pasienadmisi_id) b.pasienadmisi_id,
            concat(b.diag_utama ->> 'kode'::text, ' - ', b.diag_utama ->> 'nama'::text) AS diagnosa_utama
           FROM resumemedisri_t b
          WHERE b.is_deleted = false) resumemedisri_t ON pendaftaran_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
     LEFT JOIN ( SELECT b.penjamin_id,
            b.penjamin_nama,
            carabayar_m.carabayar_nama
           FROM penjamin_m b
             JOIN ( SELECT b1.carabayar_id,
                    b1.carabayar_nama
                   FROM carabayar_m b1) carabayar_m ON b.carabayar_id = carabayar_m.carabayar_id) penjamin ON pendaftaran_t.penjamin_id = penjamin.penjamin_id
     LEFT JOIN ( SELECT string_agg(racikan.racikan_id::text, '-'::text) AS racikan_id,
            racikan.reseptur_id
           FROM ( SELECT b.racikan_id,
                    b.reseptur_id
                   FROM resepturdetail_t b
                  GROUP BY b.racikan_id, b.reseptur_id
                  ORDER BY b.racikan_id) racikan
          GROUP BY racikan.reseptur_id) jenis_resep ON reseptur_t.reseptur_id = jenis_resep.reseptur_id
     LEFT JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama
           FROM ruangan_m b) ruangan ON reseptur_t.ruanganreseptur_id = ruangan.ruangan_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id,
            pegawai.nama_pegawai
           FROM loginpemakai_k a
             JOIN ( SELECT a1.pegawai_id,
                    a1.nama_pegawai
                   FROM pegawai_m a1) pegawai ON a.pegawai_id = pegawai.pegawai_id) peg_penginput ON reseptur_t.created_by = peg_penginput.loginpemakai_id
UNION ALL
 SELECT 'c'::text AS jenis,
    penjualanresep_t.penjualanresep_id,
    NULL::integer AS reseptur_id,
    NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN penjualanresep_t.nama_pembeli
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien)::character varying
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat(fgetnamalookup(pegawai_m.gelardepan::integer), ' ', karyawan.nama_pegawai)::character varying
            ELSE NULL::character varying
        END AS nama_pasien,
        CASE
            WHEN penjualanresep_t.pasien_id IS NOT NULL THEN pasien_m.tanggal_lahir
            ELSE NULL::date
        END AS tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    anamnesa_t.riwayat_alergiobat AS alergi,
    penjualanresep_t.status_worklist AS status_worklist_id,
    fgetnamalookup(penjualanresep_t.status_worklist::integer) AS status_worklist,
    pendaftaran_t.instalasi_id,
    penjualanresep_t.jenispenjualan AS jenispenjualan_id,
    fgetnamalookup(penjualanresep_t.jenispenjualan::integer) AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup(penjualanresep_t.status_bayar::integer) AS status_bayar,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(penjualanresep_t.status_reseptur::integer) AS status_reseptur,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN periksa_fisik_rj.tinggi::character varying
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN periksa_fisik_rd.tinggi::character varying
            ELSE periksa_fisik_ri.tinggi
        END AS tinggi_badan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN periksa_fisik_rj.berat::character varying
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN periksa_fisik_rd.berat::character varying
            ELSE periksa_fisik_ri.berat
        END AS berat_badan,
    NULL::text AS add_reseptur,
    penjualanresep_t.additional_data AS add_penjualaanresep,
    pendaftaran_t.pasienadmisi_id,
    pembayaranpelayanan_t.tgl_pembayaran,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN soaprj_t.diagnosa_utama
            ELSE COALESCE(cppt_t.diagnosa_utama, resumemedisri_t.diagnosa_utama)
        END AS diagnosa_utama,
    penjamin.penjamin_nama,
    penjamin.carabayar_nama,
        CASE
            WHEN jenis_resep.racikan_id = '1'::text THEN 'RACIKAN'::text
            WHEN jenis_resep.racikan_id = '2'::text THEN 'NON-RACIKAN'::text
            ELSE 'RACIKAN'::text
        END AS jenis_resep,
    COALESCE(ruangan_rs.ruangan_nama, ruangan.ruangan_nama) AS ruangan,
    COALESCE(pendaftaran_t.kamarruangan_nokamar, NULL::character varying) AS kamar,
    COALESCE(pendaftaran_t.no_tempattidur, NULL::character varying) AS no_bed,
    pendaftaran_t.umur,
    peg_penginput.nama_pegawai AS pegawai_penginput,
    pembayaranpelayanan_t.nama_kasir,
    penjualanresep_t.log_user AS log_user_menyerahkan,
    penjualanresep_t.pegawai_menyerahkan_id,
    penjualanresep_t.tgl_menyerahkan,
    NULL::integer AS instalasi_resptur_id,
        CASE
            WHEN pasien_m.jeniskelamin::text = '15'::text THEN 'M'::text
            WHEN pasien_m.jeniskelamin::text = '16'::text THEN 'F'::text
            ELSE '-'::text
        END AS jeniskelamin
   FROM penjualanresep_t
     LEFT JOIN ( SELECT c.pendaftaran_id,
            c.pasienadmisi_id,
            c.pasien_id,
            c.no_pendaftaran,
            c.instalasi_id,
            c.umur,
            kamar.kamarruangan_nokamar,
            bed.no_tempattidur,
                CASE
                    WHEN c.pasienadmisi_id IS NULL THEN c.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id,
                CASE
                    WHEN c.pasienadmisi_id IS NULL THEN c.ruangan_id
                    ELSE pasienadmisi_t.ruangan_id
                END AS ruangan_id
           FROM pendaftaran_t c
             LEFT JOIN ( SELECT c1.pasienadmisi_id,
                    c1.penjamin_id,
                    c1.kamarruangan_id,
                    c1.kamartempattidur_id,
                    c1.ruangan_id
                   FROM pasienadmisi_t c1) pasienadmisi_t ON c.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT c1.kamarruangan_id,
                    c1.kamarruangan_nokamar
                   FROM kamarruangan_m c1) kamar ON pasienadmisi_t.kamarruangan_id = kamar.kamarruangan_id
             LEFT JOIN ( SELECT c1.kamartempattidur_id,
                    c1.no_tempattidur
                   FROM kamartempattidur_m c1) bed ON pasienadmisi_t.kamartempattidur_id = bed.kamartempattidur_id) pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT c.pasien_id,
            c.nama_pasien,
            c.tanggal_lahir,
            c.no_rekam_medik,
            c.namadepan,
            c.jeniskelamin
           FROM pasien_m c) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai,
            c.gelardepan
           FROM pegawai_m c) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
           FROM pegawai_m c) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     LEFT JOIN ( SELECT c.penjualanresep_id,
            c.pembayaran_id
           FROM obatalkespasien_t c
          GROUP BY c.penjualanresep_id, c.pembayaran_id) pembayaran_obat ON penjualanresep_t.penjualanresep_id = pembayaran_obat.penjualanresep_id
     LEFT JOIN ( SELECT c.pembayaran_id,
            c.created_date AS tgl_pembayaran,
            c.created_by,
            pegawai_kasir.nama_pegawai AS nama_kasir
           FROM pembayaran_t c
             LEFT JOIN ( SELECT c1.loginpemakai_id,
                    c1.pegawai_id
                   FROM loginpemakai_k c1) loginpemakai_k ON c.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT c1.pegawai_id,
                    c1.nama_pegawai
                   FROM pegawai_m c1) pegawai_kasir ON loginpemakai_k.pegawai_id = pegawai_kasir.pegawai_id
          WHERE c.is_deleted = false) pembayaranpelayanan_t ON pembayaran_obat.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
     LEFT JOIN ( SELECT DISTINCT ON (c.pendaftaran_id) c.pendaftaran_id,
            c.tinggi_badan AS tinggi,
            c.berat_badan AS berat
           FROM asesmenperawatrd_t c
          WHERE c.is_deleted = false) periksa_fisik_rd ON pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (c.pendaftaran_id) c.pendaftaran_id,
            c.tinggi_badan::character varying AS tinggi,
            c.berat_badan::character varying AS berat
           FROM asesmenmedis_t c
          WHERE c.is_deleted = false) periksa_fisik_ri ON pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (c.pendaftaran_id) c.pendaftaran_id,
            c.tinggibadan_cm AS tinggi,
            c.beratbadan_kg AS berat
           FROM pemeriksaanfisik_t c
          WHERE c.is_deleted = false) periksa_fisik_rj ON pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id
     LEFT JOIN ( SELECT c.penjamin_id,
            c.penjamin_nama,
            carabayar_m.carabayar_nama
           FROM penjamin_m c
             JOIN ( SELECT c1.carabayar_id,
                    c1.carabayar_nama
                   FROM carabayar_m c1) carabayar_m ON c.carabayar_id = carabayar_m.carabayar_id) penjamin ON penjualanresep_t.penjamin_id = penjamin.penjamin_id
     LEFT JOIN ( SELECT string_agg(racikan.racikan_id::text, '-'::text) AS racikan_id,
            racikan.penjualanresep_id
           FROM ( SELECT c.racikan_id,
                    c.penjualanresep_id
                   FROM obatalkespasien_t c
                  GROUP BY c.racikan_id, c.penjualanresep_id
                  ORDER BY c.racikan_id) racikan
          GROUP BY racikan.penjualanresep_id) jenis_resep ON penjualanresep_t.penjualanresep_id = jenis_resep.penjualanresep_id
     LEFT JOIN ( SELECT c.ruangan_id,
            c.instalasi_id,
            c.ruangan_nama
           FROM ruangan_m c) ruangan ON penjualanresep_t.ruangan_id = ruangan.ruangan_id
     LEFT JOIN ( SELECT c.ruangan_id,
            c.ruangan_nama
           FROM ruangan_m c) ruangan_rs ON pendaftaran_t.ruangan_id = ruangan_rs.ruangan_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id,
            pegawai.nama_pegawai
           FROM loginpemakai_k a
             JOIN ( SELECT a1.pegawai_id,
                    a1.nama_pegawai
                   FROM pegawai_m a1) pegawai ON a.pegawai_id = pegawai.pegawai_id) peg_penginput ON penjualanresep_t.created_by = peg_penginput.loginpemakai_id
     LEFT JOIN ( SELECT DISTINCT ON (c.pendaftaran_id) c.pendaftaran_id,
            c.alergi_obat AS riwayat_alergiobat
           FROM anamnesa_t c) anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (c.pasienadmisi_id) c.pasienadmisi_id,
            concat(c.a_diag_utama ->> 'kode'::text, ' - ', c.a_diag_utama ->> 'nama'::text) AS diagnosa_utama
           FROM cppt_t c
          WHERE c.is_deleted = false) cppt_t ON pendaftaran_t.pasienadmisi_id = cppt_t.pasienadmisi_id
     LEFT JOIN ( SELECT DISTINCT ON (c.pasienadmisi_id) c.pasienadmisi_id,
            concat(c.diag_utama ->> 'kode'::text, ' - ', c.diag_utama ->> 'nama'::text) AS diagnosa_utama
           FROM resumemedisri_t c
          WHERE c.is_deleted = false) resumemedisri_t ON pendaftaran_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
     LEFT JOIN ( SELECT DISTINCT ON (c.pendaftaran_id) c.pendaftaran_id,
            concat(c.a_diag_utama ->> 'kode'::text, ' - ', c.a_diag_utama ->> 'nama'::text) AS diagnosa_utama
           FROM soaprj_t c
          WHERE c.is_deleted = false) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
  WHERE penjualanresep_t.reseptur_id IS NULL;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211115_035118_migrate_worklistresep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211115_035118_migrate_worklistresep_v cannot be reverted.\n";

        return false;
    }
    */
}
