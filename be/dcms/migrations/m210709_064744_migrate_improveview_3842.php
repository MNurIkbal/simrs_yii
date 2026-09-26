<?php

use yii\db\Migration;

/**
 * Class m210709_064744_migrate_improveview_3842
 */
class m210709_064744_migrate_improveview_3842 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
           $this->execute('DROP VIEW if exists "public"."infodatakunjungan_v";');

           $this->execute("
            CREATE VIEW \"public\".\"infodatakunjungan_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.pasienpulang_id,
    pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS dokter_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN dok_rj.nama_pegawai
            ELSE dok_ri.nama_pegawai
        END AS dokter_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelaspelayanan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
            ELSE ruang_ri.instalasi_id
        END AS instalasi_id,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN instalasi_rj.instalasi_nama
            ELSE instalasi_ri.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_rj.ruangan_nama
            ELSE ruang_ri.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_rj.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_rj.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN int_freezebill_r.status = 1 THEN true
            ELSE false
        END AS is_freezebill,
        CASE
            WHEN pendaftaran_t.instalasi_id = 2 THEN true
            ELSE false
        END AS is_rd,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN periksa_fisik_rj.bb
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN periksa_fisik_rd.bb::double precision
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN periksa_fisik_ri.bb::double precision
            ELSE NULL::double precision
        END AS berat_badan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN periksa_fisik_rj.tb
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN periksa_fisik_rd.tb::double precision
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN periksa_fisik_ri.tb::double precision
            ELSE NULL::double precision
        END AS tinggi_badan
   FROM pendaftaran_t
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pasienpulang_id,
            a.pegawai_id,
            a.kelaspelayanan_id,
            a.carabayar_id,
            a.penjamin_id,
            a.ruangan_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) dok_rj ON pendaftaran_t.pegawai_id = dok_rj.pegawai_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
           FROM pegawai_m c) dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
     LEFT JOIN ( SELECT d.ruangan_id,
            d.instalasi_id,
            d.ruangan_nama
           FROM ruangan_m d) ruang_rj ON pendaftaran_t.ruangan_id = ruang_rj.ruangan_id
     LEFT JOIN ( SELECT d.ruangan_id,
            d.instalasi_id,
            d.ruangan_nama
           FROM ruangan_m d) ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
     JOIN ( SELECT e.pasien_id,
            e.nama_pasien,
            e.no_rekam_medik,
            e.tanggal_lahir
           FROM pasien_m e) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT f.instalasi_id,
            f.instalasi_nama
           FROM instalasi_m f) instalasi_rj ON pendaftaran_t.instalasi_id = instalasi_rj.instalasi_id
     LEFT JOIN ( SELECT g.instalasi_id,
            g.instalasi_nama
           FROM instalasi_m g) instalasi_ri ON ruang_ri.instalasi_id = instalasi_ri.instalasi_id
     LEFT JOIN ( SELECT h.carabayar_id,
            h.carabayar_nama
           FROM carabayar_m h) carabayar_rj ON pendaftaran_t.carabayar_id = carabayar_rj.carabayar_id
     LEFT JOIN ( SELECT i.carabayar_id,
            i.carabayar_nama
           FROM carabayar_m i) carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
     LEFT JOIN ( SELECT j.penjamin_id,
            j.penjamin_nama
           FROM penjamin_m j) penjamin_rj ON pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id
     LEFT JOIN ( SELECT k.penjamin_id,
            k.penjamin_nama
           FROM penjamin_m k) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
     LEFT JOIN ( SELECT DISTINCT ON ((int_freezebill_r_1.pendaftaran_id::integer)) int_freezebill_r_1.pendaftaran_id::integer AS pendaftaran_id,
            int_freezebill_r_1.status
           FROM int_freezebill_r int_freezebill_r_1) int_freezebill_r ON pendaftaran_t.pendaftaran_id = int_freezebill_r.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (pemeriksaanfisik_t.pendaftaran_id) pemeriksaanfisik_t.pendaftaran_id,
            pemeriksaanfisik_t.tinggibadan_cm AS tb,
            pemeriksaanfisik_t.beratbadan_kg AS bb
           FROM pemeriksaanfisik_t
          WHERE pemeriksaanfisik_t.is_deleted = false) periksa_fisik_rj ON pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (asesmenperawatrd_t.pendaftaran_id) asesmenperawatrd_t.pendaftaran_id,
            asesmenperawatrd_t.tinggi_badan AS tb,
            asesmenperawatrd_t.berat_badan AS bb
           FROM asesmenperawatrd_t
          WHERE asesmenperawatrd_t.is_deleted = false) periksa_fisik_rd ON pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (asesmenmedis_t.pendaftaran_id) asesmenmedis_t.pendaftaran_id,
            asesmenmedis_t.tinggi_badan AS tb,
            asesmenmedis_t.berat_badan AS bb
           FROM asesmenmedis_t
          WHERE asesmenmedis_t.is_deleted = false) periksa_fisik_ri ON pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id
  WHERE pendaftaran_t.is_deleted = false
  ORDER BY pendaftaran_t.created_date DESC;");

           $this->execute('DROP VIEW if exists "public"."inforesep_v";');

           $this->execute("
            CREATE VIEW \"public\".\"inforesep_v\" AS  SELECT 'reseptur'::text AS jenis,
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
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    ruangan_reseptur.ruangan_nama AS ruangan_reseptur,
        CASE
            WHEN penjualanresep_t.penjualanresep_id IS NULL THEN 'Belum Proses'::character varying
            WHEN penjualanresep_t.status_reseptur = 347 THEN 'Dalam Proses'::character varying
            ELSE fgetnamalookup(penjualanresep_t.status_reseptur::integer)
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
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN pemeriksaanfisik_t.bb::text
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN asesmenmedisrd_t.bb::text
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN asesmenmedis_t.bb::text
            ELSE '-'::text
        END AS berat_badan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN pemeriksaanfisik_t.tb::text
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN asesmenmedisrd_t.tb::text
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN asesmenmedis_t.tb::text
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
            WHEN ruangan_reseptur.instalasi_id = 1 THEN anamnesa_t.riwayat_alergiobat::character varying
            WHEN ruangan_reseptur.instalasi_id = 2 THEN asesmenperawatrd_t.alergi_obat::character varying
            ELSE asesmenawal_t.nama_alergi
        END AS riwayat_alergi,
        CASE
            WHEN ruangan_reseptur.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text
            WHEN ruangan_reseptur.instalasi_id = 2 THEN cppt_rd.diagnosa_utama
            WHEN ruangan_reseptur.instalasi_id = 3 THEN cppt_rd.diagnosa_utama
            ELSE NULL::text
        END AS diagnosa_text,
    resepturdetail_t.antrian_racikan,
    resepturdetail_t.harga_netto AS total_harganetto,
        CASE
            WHEN reseptur_t.penjualanresep_id IS NULL THEN reseptur_t.biaya_administrasi
            ELSE penjualanresep_t.biayaadministrasi
        END AS biayaadministrasi,
    penjualanresep_t.totalhargajual,
    COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totaltagihan,
    NULL::character varying AS nama_pembeli,
    penjualanresep_t.status_bayar,
    COALESCE(penjualanresep_t.tglresep, reseptur_t.tglreseptur) AS tgl_resep_dibuat,
    concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien) AS nama,
    NULL::character varying AS jenispenjualan_id,
    NULL::character varying AS jenispenjualan_nama,
    reseptur_t.status_worklist,
    fgetnamalookup(pasien_m.namadepan::integer) AS nama_depan,
    penjualanresep_t.kelaspelayanan_id
   FROM reseptur_t
     LEFT JOIN ( SELECT penjualanresep.penjualanresep_id,
            penjualanresep.tglresep,
            penjualanresep.noresep,
            penjualanresep.status_reseptur,
            penjualanresep.status_bayar,
            penjualanresep.catatan,
            penjualanresep.biayaadministrasi,
            penjualanresep.totalhargajual,
            penjualanresep.kelaspelayanan_id
           FROM penjualanresep_t penjualanresep
          WHERE penjualanresep.is_deleted = false) penjualanresep_t ON reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     JOIN ( SELECT pendaftaran.pendaftaran_id,
            pendaftaran.pasienadmisi_id,
            pendaftaran.kelaspelayanan_id,
            pendaftaran.carabayar_id,
            pendaftaran.penjamin_id,
            pendaftaran.umur,
            pendaftaran.no_pendaftaran,
            pendaftaran.instalasi_id
           FROM pendaftaran_t pendaftaran) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT pasien.pasien_id,
            pasien.nama_pasien,
            pasien.no_rekam_medik,
            pasien.tanggal_lahir,
            pasien.jeniskelamin,
            pasien.namadepan
           FROM pasien_m pasien) pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT ruangan_1.ruangan_id,
            ruangan_1.ruangan_nama,
            ruangan_1.instalasi_id
           FROM ruangan_m ruangan_1) ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
     JOIN ( SELECT ruangan_2.ruangan_id,
            ruangan_2.ruangan_nama,
            ruangan_2.instalasi_id
           FROM ruangan_m ruangan_2) ruangan_reseptur ON reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id
     JOIN ( SELECT instalasi_1.instalasi_id,
            instalasi_1.instalasi_nama
           FROM instalasi_m instalasi_1) instalasi_reseptur ON ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id
     JOIN ( SELECT instalasi_2.instalasi_id,
            instalasi_2.instalasi_nama
           FROM instalasi_m instalasi_2) instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     JOIN ( SELECT kelas.kelaspelayanan_id,
            kelas.kelaspelayanan_nama
           FROM kelaspelayanan_m kelas) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT carabayar.carabayar_id,
            carabayar.carabayar_nama
           FROM carabayar_m carabayar) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT penjamin.penjamin_id,
            penjamin.penjamin_nama
           FROM penjamin_m penjamin) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT pegawai.pegawai_id,
            pegawai.nama_pegawai
           FROM pegawai_m pegawai) pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT reseptur_detail.reseptur_id,
            reseptur_detail.iter,
            sum(reseptur_detail.harganetto_reseptur) AS harga_netto,
            string_agg(reseptur_detail.racikan_id::text, '-'::text) AS antrian_racikan
           FROM resepturdetail_t reseptur_detail
          WHERE reseptur_detail.is_deleted = false
          GROUP BY reseptur_detail.reseptur_id, reseptur_detail.iter) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
     LEFT JOIN ( SELECT antrian.antrian_id,
            antrian.no_antrian
           FROM antrian_t antrian) antrian_t ON reseptur_t.antrian_id = antrian_t.antrian_id
     LEFT JOIN ( SELECT diagnosa.diagnosa_id,
            diagnosa.diagnosa_kode,
            diagnosa.diagnosa_nama
           FROM diagnosa_m diagnosa) diagnosa_m ON reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id
     LEFT JOIN ( SELECT DISTINCT ON (anamnesa.pendaftaran_id) anamnesa.pendaftaran_id,
            anamnesa.riwayat_alergiobat
           FROM anamnesa_t anamnesa) anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (asesmenperawatrd.pendaftaran_id) asesmenperawatrd.pendaftaran_id,
            asesmenperawatrd.alergi_obat
           FROM asesmenperawatrd_t asesmenperawatrd) asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (asesmenawal.pendaftaran_id) asesmenawal.pendaftaran_id,
            asesmenawal.nama_alergi
           FROM asesmenawal_t asesmenawal) asesmenawal_t ON pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (ass_medisrd.pendaftaran_id) ass_medisrd.pendaftaran_id,
            ass_medisrd.tinggi_badan AS tb,
            ass_medisrd.berat_badan AS bb
           FROM asesmenmedisrd_t ass_medisrd
          WHERE ass_medisrd.is_deleted = false) asesmenmedisrd_t ON pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (ass_medis.pendaftaran_id) ass_medis.pendaftaran_id,
            ass_medis.tinggi_badan AS tb,
            ass_medis.berat_badan AS bb
           FROM asesmenmedis_t ass_medis
          WHERE ass_medis.is_deleted = false) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (pemeriksaan_fisik.pendaftaran_id) pemeriksaan_fisik.pendaftaran_id,
            pemeriksaan_fisik.beratbadan_kg AS tb,
            pemeriksaan_fisik.tinggibadan_cm AS bb
           FROM pemeriksaanfisik_t pemeriksaan_fisik
          WHERE pemeriksaan_fisik.is_deleted = false) pemeriksaanfisik_t ON pendaftaran_t.pendaftaran_id = pemeriksaanfisik_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (pasienmorbiditas.pendaftaran_id) pasienmorbiditas.pendaftaran_id,
            pasienmorbiditas.diagnosa_pasien
           FROM pasienmorbiditas_t pasienmorbiditas
          WHERE pasienmorbiditas.is_deleted = false AND pasienmorbiditas.kelompokdiagnosa_id = 2) pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id
     LEFT JOIN ( SELECT instruksi_t.instruksi_id,
            cppt_t.cppt_id,
            cppt_t.pendaftaran_id,
            cppt_t.a_diag_utama ->> 'text'::text AS diagnosa_utama
           FROM instruksi_t
             JOIN ( SELECT DISTINCT ON (cppt.pendaftaran_id) cppt.pendaftaran_id,
                    cppt.cppt_id,
                    cppt.a_diag_utama
                   FROM cppt_t cppt
                  WHERE cppt.is_deleted = false) cppt_t ON instruksi_t.cppt_id = cppt_t.cppt_id
          WHERE instruksi_t.is_deleted = false AND instruksi_t.is_active = true) cppt_rd ON pendaftaran_t.pendaftaran_id = cppt_rd.pendaftaran_id AND reseptur_t.instruksi_id = cppt_rd.instruksi_id
  WHERE reseptur_t.is_deleted = false AND reseptur_t.is_active = true
UNION ALL
 SELECT 'resep'::text AS jenis,
    NULL::integer AS reseptur_id,
    penjualanresep_t.penjualanresep_id AS resep_id,
    penjualanresep_t.pasien_id,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasienadmisi_id,
    penjualanresep_t.carabayar_id,
    penjualanresep_t.penjamin_id,
    pendaftaran_t.umur,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjualanresep_t.ruangan_id,
    NULL::integer AS ruanganreseptur_id,
    NULL::date AS tglreseptur,
    penjualanresep_t.tglresep,
    NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    penjualanresep_t.noresep AS nomor,
    penjualanresep_t.penjualanresep_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_resep.ruangan_nama AS ruangan_tujuan,
    NULL::text AS ruangan_reseptur,
        CASE
            WHEN penjualanresep_t.status_reseptur = 347 THEN 'Dalam Proses'::character varying
            ELSE fgetnamalookup(penjualanresep_t.status_reseptur::integer)
        END AS status_reseptur,
    penjualanresep_t.pegawai_id,
    pegawai_m.nama_pegawai,
    NULL::integer AS instalasi_reseptur_id,
    NULL::character varying AS instalasi_reseptur,
    ruangan_resep.instalasi_id AS instalasi_resep_id,
    instalasi_resep.instalasi_nama AS instalasi_resep,
    antrian_t.no_antrian,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    NULL::boolean AS is_hamil,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN periksa_fisik_rj.bb::text
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN periksa_fisik_rd.bb::text
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN periksa_fisik_ri.bb::text
            ELSE '-'::text
        END AS berat_badan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN periksa_fisik_rj.tb::text
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN periksa_fisik_rd.tb::text
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN periksa_fisik_ri.tb::text
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
    COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totaltagihan,
    penjualanresep_t.nama_pembeli,
    penjualanresep_t.status_bayar,
    penjualanresep_t.tglresep AS tgl_resep_dibuat,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN penjualanresep_t.nama_pembeli
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien)::character varying
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat(fgetnamalookup(pegawai_m.gelardepan::integer), ' ', karyawan.nama_pegawai)::character varying
            ELSE NULL::character varying
        END AS nama,
    penjualanresep_t.jenispenjualan AS jenispenjualan_id,
    fgetnamalookup(penjualanresep_t.jenispenjualan::integer) AS jenispenjualan_nama,
    penjualanresep_t.status_worklist,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN NULL::character varying
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN fgetnamalookup(pasien_m.namadepan::integer)
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN fgetnamalookup(pegawai_m.gelardepan::integer)
            ELSE NULL::character varying
        END AS nama_depan,
    penjualanresep_t.kelaspelayanan_id
   FROM penjualanresep_t
     LEFT JOIN ( SELECT pendaftaran.pendaftaran_id,
            pendaftaran.pasien_id,
            pendaftaran.pasienadmisi_id,
            pendaftaran.instalasi_id,
            pendaftaran.umur,
            pendaftaran.no_pendaftaran
           FROM pendaftaran_t pendaftaran) pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pasien.pasien_id,
            pasien.nama_pasien,
            pasien.no_rekam_medik,
            pasien.tanggal_lahir,
            pasien.jeniskelamin,
            pasien.namadepan
           FROM pasien_m pasien) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT ruangan.ruangan_id,
            ruangan.ruangan_nama,
            ruangan.instalasi_id
           FROM ruangan_m ruangan) ruangan_resep ON penjualanresep_t.ruangan_id = ruangan_resep.ruangan_id
     JOIN ( SELECT instalasi.instalasi_id,
            instalasi.instalasi_nama
           FROM instalasi_m instalasi) instalasi_resep ON ruangan_resep.instalasi_id = instalasi_resep.instalasi_id
     LEFT JOIN ( SELECT kelas.kelaspelayanan_id,
            kelas.kelaspelayanan_nama
           FROM kelaspelayanan_m kelas) kelaspelayanan_m ON penjualanresep_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT carabayar.carabayar_id,
            carabayar.carabayar_nama
           FROM carabayar_m carabayar) carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT penjamin.penjamin_id,
            penjamin.penjamin_nama
           FROM penjamin_m penjamin) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT peg_1.pegawai_id,
            peg_1.nama_pegawai,
            peg_1.gelardepan
           FROM pegawai_m peg_1) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT antrian.antrian_id,
            antrian.no_antrian
           FROM antrian_t antrian) antrian_t ON penjualanresep_t.antrian_id = antrian_t.antrian_id
     LEFT JOIN ( SELECT peg_2.pegawai_id,
            peg_2.nama_pegawai
           FROM pegawai_m peg_2) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     LEFT JOIN ( SELECT DISTINCT ON (pemeriksaanfisik_t.pendaftaran_id) pemeriksaanfisik_t.pendaftaran_id,
            pemeriksaanfisik_t.tinggibadan_cm AS tb,
            pemeriksaanfisik_t.beratbadan_kg AS bb
           FROM pemeriksaanfisik_t
          WHERE pemeriksaanfisik_t.is_deleted = false) periksa_fisik_rj ON pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (asesmenperawatrd_t.pendaftaran_id) asesmenperawatrd_t.pendaftaran_id,
            asesmenperawatrd_t.tinggi_badan AS tb,
            asesmenperawatrd_t.berat_badan AS bb
           FROM asesmenperawatrd_t
          WHERE asesmenperawatrd_t.is_deleted = false) periksa_fisik_rd ON pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (asesmenmedis_t.pendaftaran_id) asesmenmedis_t.pendaftaran_id,
            asesmenmedis_t.tinggi_badan AS tb,
            asesmenmedis_t.berat_badan AS bb
           FROM asesmenmedis_t
          WHERE asesmenmedis_t.is_deleted = false) periksa_fisik_ri ON pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id
  WHERE penjualanresep_t.reseptur_id IS NULL;");

           $this->execute('DROP VIEW if exists "public"."worklistresep_v";');

           $this->execute("
            CREATE VIEW \"public\".\"worklistresep_v\" AS  SELECT penjualanresep_t.penjualanresep_id,
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
    pembayaranpelayanan_t.tgl_pembayaran
   FROM reseptur_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.pasien_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT b.pasien_id,
            b.nama_pasien,
            b.tanggal_lahir,
            b.no_rekam_medik,
            b.namadepan
           FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT c.penjualanresep_id,
            c.reseptur_id,
            c.pegawai_id,
            c.noresep,
            c.tglpenjualan,
            c.status_bayar,
            c.status_reseptur,
            c.additional_data
           FROM penjualanresep_t c) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     LEFT JOIN ( SELECT DISTINCT ON (d.pendaftaran_id) d.pendaftaran_id,
            d.tgl_pembayaran
           FROM pembayaranpelayanan_t d
          WHERE d.is_deleted = false) pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
     LEFT JOIN ( SELECT e.pegawai_id,
            e.nama_pegawai
           FROM pegawai_m e) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT f.ruangan_id,
            f.ruangan_nama,
            f.instalasi_id
           FROM ruangan_m f
          WHERE f.instalasi_id = 1) ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT DISTINCT ON (g.pendaftaran_id) g.pendaftaran_id,
            array_agg(g.riwayat_alergiobat) AS riwayat_alergiobat
           FROM anamnesa_t g
          GROUP BY g.pendaftaran_id) anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (h.pendaftaran_id) h.pendaftaran_id,
            h.tinggibadan_cm AS tinggi,
            h.beratbadan_kg AS berat
           FROM pemeriksaanfisik_t h
          WHERE h.is_deleted = false) periksa_fisik_rj ON pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id
UNION ALL
 SELECT NULL::integer AS penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    NULL::character varying AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien) AS nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    NULL::text[] AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup(reseptur_t.status_worklist::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    reseptur_t.tglreseptur AS tanggal,
    NULL::smallint AS status_bayar_id,
    NULL::character varying AS status_bayar,
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
    NULL::text AS add_penjualaanresep,
    pendaftaran_t.pasienadmisi_id,
    pembayaranpelayanan_t.tgl_pembayaran
   FROM reseptur_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.pasien_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT b.pasien_id,
            b.nama_pasien,
            b.tanggal_lahir,
            b.no_rekam_medik,
            b.namadepan
           FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
           FROM pegawai_m c) pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT d.ruangan_id,
            d.ruangan_nama,
            d.instalasi_id
           FROM ruangan_m d
          WHERE d.instalasi_id <> 1) ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT DISTINCT ON (e.pendaftaran_id) e.pendaftaran_id,
            e.tgl_pembayaran
           FROM pembayaranpelayanan_t e
          WHERE e.is_deleted = false) pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (f.pendaftaran_id) f.pendaftaran_id,
            f.tinggi_badan AS tinggi,
            f.berat_badan AS berat
           FROM asesmenperawatrd_t f
          WHERE f.is_deleted = false) periksa_fisik_rd ON pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (g.pendaftaran_id) g.pendaftaran_id,
            g.tinggi_badan::character varying AS tinggi,
            g.berat_badan::character varying AS berat
           FROM asesmenmedis_t g
          WHERE g.is_deleted = false) periksa_fisik_ri ON pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id
UNION ALL
 SELECT penjualanresep_t.penjualanresep_id,
    NULL::integer AS reseptur_id,
    NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_rm,
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
    NULL::text[] AS alergi,
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
    pembayaranpelayanan_t.tgl_pembayaran
   FROM penjualanresep_t
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.pasien_id,
            a.no_pendaftaran,
            a.instalasi_id
           FROM pendaftaran_t a) pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT b.pasien_id,
            b.nama_pasien,
            b.tanggal_lahir,
            b.no_rekam_medik,
            b.namadepan
           FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai,
            c.gelardepan
           FROM pegawai_m c) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT d.pegawai_id,
            d.nama_pegawai
           FROM pegawai_m d) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     LEFT JOIN ( SELECT DISTINCT ON (e.penjualanresep_id) e.penjualanresep_id,
            e.tgl_pembayaran
           FROM pembayaranpelayanan_t e
          WHERE e.is_deleted = false) pembayaranpelayanan_t ON penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
     LEFT JOIN ( SELECT DISTINCT ON (f.pendaftaran_id) f.pendaftaran_id,
            f.tinggi_badan AS tinggi,
            f.berat_badan AS berat
           FROM asesmenperawatrd_t f
          WHERE f.is_deleted = false) periksa_fisik_rd ON pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (g.pendaftaran_id) g.pendaftaran_id,
            g.tinggi_badan::character varying AS tinggi,
            g.berat_badan::character varying AS berat
           FROM asesmenmedis_t g
          WHERE g.is_deleted = false) periksa_fisik_ri ON pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (h.pendaftaran_id) h.pendaftaran_id,
            h.tinggibadan_cm AS tinggi,
            h.beratbadan_kg AS berat
           FROM pemeriksaanfisik_t h
          WHERE h.is_deleted = false) periksa_fisik_rj ON pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id
  WHERE penjualanresep_t.reseptur_id IS NULL;");

           $this->execute('DROP VIEW if exists "public"."worklistresepdetail_v";');

           $this->execute("
            CREATE VIEW \"public\".\"worklistresepdetail_v\" AS  SELECT reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    COALESCE(obatalkespasien_t.signa ->> 'text'::text, signaobat_m.signa_nama::text) AS signa,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
    obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text AS satuan_konversi,
    obatalkespasien_t.etiket,
    obatalkes_m.is_oral,
    NULL::date AS tglkadaluarsa,
        CASE
            WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS det,
    obatalkespasien_t.is_deleted AS detail_is_deleted,
    obatalkespasien_t.racikan_id
   FROM reseptur_t
     JOIN ( SELECT a.penjualanresep_id,
            a.noresep,
            a.reseptur_id
           FROM penjualanresep_t a) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     JOIN ( SELECT b.obatalkes_id,
            b.penjualanresep_id,
            b.rke,
            b.signa,
            b.qty_oa,
            b.qty_konversi,
            b.additional_data,
            b.det,
            b.is_deleted,
            b.racikan_id,
            b.signa_oa,
            b.etiket
           FROM obatalkespasien_t b) obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
     JOIN ( SELECT c.obatalkes_id,
            c.obatalkes_nama,
            c.is_oral
           FROM obatalkes_m c) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT d.racikan_id,
            d.racikan_nama
           FROM racikan_m d) racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     JOIN ( SELECT e.ruangan_id,
            e.ruangan_nama
           FROM ruangan_m e
          WHERE e.instalasi_id = 1) ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT f.signa_id,
            f.signa_nama
           FROM signaobat_m f) signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
UNION ALL
 SELECT reseptur_t.noresep AS no_reseptur,
    NULL::text AS no_resep,
    racikan_m.racikan_nama AS racikan,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    COALESCE(resepturdetail_t.signa ->> 'text'::text, signaobat_m.signa_nama::text) AS signa,
    resepturdetail_t.qty_reseptur AS qty_obat,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
    resepturdetail_t.additional_data::json ->> 'satuan_konversi'::text AS satuan_konversi,
    resepturdetail_t.etiket,
    obatalkes_m.is_oral,
    NULL::date AS tglkadaluarsa,
        CASE
            WHEN resepturdetail_t.det IS NULL THEN resepturdetail_t.qty_reseptur
            ELSE resepturdetail_t.det
        END AS det,
    resepturdetail_t.is_deleted AS detail_is_deleted,
    resepturdetail_t.racikan_id
   FROM reseptur_t
     JOIN ( SELECT a.reseptur_id,
            a.signa,
            a.qty_reseptur,
            a.qty_konversi,
            a.additional_data,
            a.etiket,
            a.det,
            a.is_deleted,
            a.racikan_id,
            a.signa_id,
            a.obatalkes_id,
            a.rke
           FROM resepturdetail_t a) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
     JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_nama,
            b.is_oral
           FROM obatalkes_m b) obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT c.ruangan_id,
            c.ruangan_nama
           FROM ruangan_m c
          WHERE c.instalasi_id <> 1) ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT d.racikan_id,
            d.racikan_nama
           FROM racikan_m d) racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ( SELECT e.signa_id,
            e.signa_nama
           FROM signaobat_m e) signaobat_m ON resepturdetail_t.signa_id = signaobat_m.signa_id
UNION ALL
 SELECT NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    COALESCE(obatalkespasien_t.signa ->> 'text'::text, signaobat_m.signa_nama::text) AS signa,
    (obatalkespasien_t.additional_data::json ->> 'qty_input'::text)::double precision AS qty_obat,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
    obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text AS satuan_konversi,
    obatalkespasien_t.etiket,
    obatalkes_m.is_oral,
    stokobatalkes_t.tglkadaluarsa,
        CASE
            WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS det,
    obatalkespasien_t.is_deleted AS detail_is_deleted,
    obatalkespasien_t.racikan_id
   FROM penjualanresep_t
     JOIN ( SELECT a.obatalkes_id,
            a.penjualanresep_id,
            a.rke,
            a.signa,
            a.qty_oa,
            a.qty_konversi,
            a.additional_data,
            a.det,
            a.is_deleted,
            a.racikan_id,
            a.signa_oa,
            a.etiket,
            a.obatalkespasien_id
           FROM obatalkespasien_t a) obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
     JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_nama,
            b.is_oral
           FROM obatalkes_m b) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT c.racikan_id,
            c.racikan_nama
           FROM racikan_m c) racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ( SELECT d.signa_id,
            d.signa_nama
           FROM signaobat_m d) signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
     LEFT JOIN ( SELECT e.obatalkespasien_id,
            e.obatalkes_id,
            e.tglkadaluarsa
           FROM stokobatalkes_t e) stokobatalkes_t ON obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id AND obatalkespasien_t.obatalkes_id = stokobatalkes_t.obatalkes_id
  WHERE penjualanresep_t.reseptur_id IS NULL;");
           

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210709_064744_migrate_improveview_3842 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210709_064744_migrate_improveview_3842 cannot be reverted.\n";

        return false;
    }
    */
}
