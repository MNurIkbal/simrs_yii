<?php

use yii\db\Migration;

/**
 * Class m211012_030256_migrate_issu_1520_1521
 */
class m211012_030256_migrate_issu_1520_1521 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."penjualanresep_t" ADD COLUMN if not exists "is_approve" bool DEFAULT false;');
        $this->execute('ALTER TABLE "public"."penjualanresep_t" ADD COLUMN if not exists "pegawai_approve_id" int4;');
        $this->execute('ALTER TABLE "public"."penjualanresep_t" ADD COLUMN if not exists "tgl_approve" timestamp(6);');
        
        $this->execute('ALTER TABLE "public"."resepturdetail_t" ADD COLUMN if not exists "nama_racikan" varchar(255) COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."resepturdetail_t" ADD COLUMN if not exists "qty_racikan" float8;');
        $this->execute('ALTER TABLE "public"."resepturdetail_t" ADD COLUMN if not exists "satuan_racikan_id" int4;');
        $this->execute('ALTER TABLE "public"."resepturdetail_t" ADD COLUMN if not exists "qty_medis" float8;');

        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN if not exists "qty_medis" float8;');
        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN if not exists "nama_racikan" varchar(255) COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN if not exists "det_medis" float8;');
        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN if not exists "qty_racikan" float8;');
        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN if not exists "satuan_racikan_id" int4;');

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
    penjualanresep_t.kelaspelayanan_id,
    penjualanresep_t.is_approve,
    pasien_m.alamat_pasien,
    pegawai_approve.nama_pegawai AS pegawai_approve,
    penjualanresep_t.tgl_approve,
    penjualanresep_t.additional_data
   FROM reseptur_t
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
            penjualanresep.additional_data
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
            pasien.namadepan,
            pasien.alamat_pasien
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
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_approve ON penjualanresep_t.pegawai_approve_id = pegawai_approve.pegawai_id
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
    penjualanresep_t.kelaspelayanan_id,
    penjualanresep_t.is_approve,
    pasien_m.alamat_pasien,
    pegawai_approve.nama_pegawai AS pegawai_approve,
    penjualanresep_t.tgl_approve,
    penjualanresep_t.additional_data
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
            pasien.namadepan,
            pasien.alamat_pasien
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
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_approve ON penjualanresep_t.pegawai_approve_id = pegawai_approve.pegawai_id
  WHERE penjualanresep_t.reseptur_id IS NULL;");

        $this->execute('DROP VIEW if exists "public"."inforesepdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inforesepdetail_v\" AS  SELECT 'reseptur'::text AS jenis,
    resepturdetail_t.resepturdetail_id,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS penjualanresep_id,
    resepturdetail_t.reseptur_id,
    reseptur_t.pendaftaran_id,
    reseptur_t.pasien_id,
    resepturdetail_t.obatalkes_id,
    resepturdetail_t.satuankecil_id,
    resepturdetail_t.racikan_id,
    COALESCE(resepturdetail_t.signa ->> 'id'::text, resepturdetail_t.signa_id::text)::integer AS signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    reseptur_t.noresep,
    reseptur_t.tglreseptur,
    racikan_m.racikan_nama,
    resepturdetail_t.r,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama,
    resepturdetail_t.qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    resepturdetail_t.hargasatuan_reseptur AS hargajual_satuan,
    resepturdetail_t.hargajual_reseptur AS totalharga_jual,
    resepturdetail_t.etiket,
    resepturdetail_t.iter,
    COALESCE(resepturdetail_t.signa ->> 'text'::text, signaobat_m.signa_nama::text) AS signa_nama,
    reseptur_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    rotd_t.interaksi,
    rotd_t.duplikasi,
    rotd_t.dosisi AS dosis,
    rotd_t.alergi,
    rotd_t.kontradiksi,
    rotd_t.review_note,
    rotd_t.wkt_review,
    pegawai_m.nama_pegawai,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS margin,
    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS hn_margin,
    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS disc,
    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS hn_diskon,
    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS ppn,
    obatalkes_m.harganetto + (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
    resepturdetail_t.is_deleted,
    resepturdetail_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data AS additional_reseptur,
    (resepturdetail_t.additional_data::json ->> 'satuaninput_id'::text)::character varying AS satuaninput_id,
    (resepturdetail_t.additional_data::json ->> 'satuan_input'::text)::character varying AS satuan_input,
    (resepturdetail_t.additional_data::json ->> 'satuankonversi_id'::text)::character varying AS satuankonversi_id,
    (resepturdetail_t.additional_data::json ->> 'satuan_konversi'::text)::character varying AS satuan_konversi,
    (resepturdetail_t.additional_data::json ->> 'harga_konversi'::text)::character varying AS harga_konversi,
    (resepturdetail_t.additional_data::json ->> 'nilai_konversi'::text)::character varying AS nilai_konversi,
    0::double precision AS biayaadministrasiresep,
    0::double precision AS totalhargajualresep,
    0::double precision AS totaltagihanresep,
    NULL::character varying AS nama_pembeli,
    resepturdetail_t.qty_reseptur AS qty_oa,
    reseptur_t.ruanganreseptur_id AS ruanganasal_id,
    ruangan_asal.instalasi_id AS instalasiasal_id,
    resepturdetail_t.det,
    resepturdetail_t.det_konversi,
    sr.qty_tersedia,
        CASE
            WHEN resepturdetail_t.qty_medis IS NOT NULL THEN resepturdetail_t.qty_medis
            ELSE resepturdetail_t.qty_reseptur
        END AS qty_transaksi,
    resepturdetail_t.nama_racikan,
    resepturdetail_t.qty_racikan,
    resepturdetail_t.satuan_racikan_id,
    resepturdetail_t.det AS det_transaksi,
    satuan_racikan.satuanunit_nama AS satuan_racikan_nama
   FROM resepturdetail_t
     JOIN ( SELECT a.reseptur_id,
            a.pendaftaran_id,
            a.pasien_id,
            a.ruangan_id,
            a.ruanganreseptur_id,
            a.noresep,
            a.tglreseptur,
            a.status_reseptur
           FROM reseptur_t a) reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.status_periksa
           FROM pendaftaran_t a) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien
           FROM pasien_m a) pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.harganetto
           FROM obatalkes_m a) obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_kecil ON resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_racikan ON resepturdetail_t.satuan_racikan_id = satuan_racikan.satuanunit_id
     JOIN ( SELECT a.racikan_id,
            a.racikan_nama
           FROM racikan_m a) racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ( SELECT a.signa_id,
            a.signa_nama
           FROM signaobat_m a) signaobat_m ON resepturdetail_t.signa_id = signaobat_m.signa_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT a.resepturdetail_id,
            a.pegawairotd_id,
            a.interaksi,
            a.duplikasi,
            a.dosisi,
            a.alergi,
            a.kontradiksi,
            a.review_note,
            a.wkt_review
           FROM rotd_t a) rotd_t ON resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON rotd_t.pegawairotd_id = rotd_t.pegawairotd_id
     LEFT JOIN ( SELECT a.resepturdetail_id,
            a.additional_data,
            a.hargajual_oa,
            a.hargasatuan_oa
           FROM obatalkespasien_t a) obatalkespasien_t ON resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id
     JOIN ( SELECT a.is_deleted,
            a.persen_diskon,
            a.persenppn
           FROM konfigfarmasi_k a) konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.ruangan_id,
            a.qty_tersedia
           FROM stokobatalkes_r a) sr ON sr.obatalkes_id = resepturdetail_t.obatalkes_id AND sr.ruangan_id = reseptur_t.ruangan_id
  WHERE resepturdetail_t.is_deleted = false AND resepturdetail_t.is_active = true
UNION ALL
 SELECT 'resep'::text AS jenis,
    obatalkespasien_t.resepturdetail_id,
    obatalkespasien_t.obatalkespasien_id,
    penjualanresep_t.penjualanresep_id,
    penjualanresep_t.reseptur_id,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkespasien_t.satuankecil_id,
    obatalkespasien_t.racikan_id,
    NULL::integer AS signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    penjualanresep_t.noresep,
    penjualanresep_t.tglresep AS tglreseptur,
    racikan_m.racikan_nama,
    obatalkespasien_t.r,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama,
    (obatalkespasien_t.additional_data::json ->> 'qty_input'::text)::double precision AS qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
    obatalkespasien_t.hargajual_oa AS totalharga_jual,
    obatalkespasien_t.etiket,
    NULL::integer AS iter,
    COALESCE(obatalkespasien_t.signa ->> 'text'::text, signaobat_m.signa_nama::text) AS signa_nama,
    penjualanresep_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    NULL::character varying AS interaksi,
    NULL::character varying AS duplikasi,
    NULL::character varying AS dosis,
    NULL::character varying AS alergi,
    NULL::character varying AS kontradiksi,
    NULL::character varying AS review_note,
    NULL::timestamp without time zone AS wkt_review,
    pegawai_m.nama_pegawai,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS margin,
    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS hn_margin,
    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS disc,
    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS hn_diskon,
    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS ppn,
    obatalkes_m.harganetto + (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(penjualanresep_t.status_reseptur::integer) AS status_reseptur,
    obatalkespasien_t.is_deleted,
    obatalkespasien_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data AS additional_reseptur,
    (obatalkespasien_t.additional_data::json ->> 'satuaninput_id'::text)::character varying AS satuaninput_id,
    (obatalkespasien_t.additional_data::json ->> 'satuan_input'::text)::character varying AS satuan_input,
    (obatalkespasien_t.additional_data::json ->> 'satuankonversi_id'::text)::character varying AS satuankonversi_id,
    (obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text)::character varying AS satuan_konversi,
    (obatalkespasien_t.additional_data::json ->> 'harga_konversi'::text)::character varying AS harga_konversi,
    (obatalkespasien_t.additional_data::json ->> 'nilai_konversi'::text)::character varying AS nilai_konversi,
    penjualanresep_t.biayaadministrasi AS biayaadministrasiresep,
    penjualanresep_t.totalhargajual AS totalhargajualresep,
    COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totaltagihanresep,
    penjualanresep_t.nama_pembeli,
    obatalkespasien_t.qty_oa,
    penjualanresep_t.ruangan_id AS ruanganasal_id,
    ruangan_tujuan.instalasi_id AS instalasiasal_id,
    COALESCE(obatalkespasien_t.det_medis, obatalkespasien_t.det) AS det,
    obatalkespasien_t.det_konversi,
    sr.qty_tersedia,
        CASE
            WHEN obatalkespasien_t.qty_medis IS NOT NULL THEN obatalkespasien_t.qty_medis
            ELSE obatalkespasien_t.qty_oa
        END AS qty_transaksi,
    obatalkespasien_t.nama_racikan,
    obatalkespasien_t.qty_racikan,
    obatalkespasien_t.satuan_racikan_id,
        CASE
            WHEN obatalkespasien_t.det_medis IS NOT NULL THEN obatalkespasien_t.det_medis
            ELSE obatalkespasien_t.det
        END AS det_transaksi,
    satuan_racikan.satuanunit_nama AS satuan_racikan_nama
   FROM obatalkespasien_t
     JOIN ( SELECT b.penjualanresep_id,
            b.pendaftaran_id,
            b.pasien_id,
            b.pegawai_id,
            b.ruangan_id,
            b.reseptur_id,
            b.noresep,
            b.tglresep,
            b.status_reseptur,
            b.biayaadministrasi,
            b.totalhargajual,
            b.nama_pembeli
           FROM penjualanresep_t b) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT b.pendaftaran_id,
            b.no_pendaftaran,
            b.status_periksa
           FROM pendaftaran_t b) pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT b.pasien_id,
            b.no_rekam_medik,
            b.nama_pasien
           FROM pasien_m b) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_nama,
            b.harganetto
           FROM obatalkes_m b) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT b.satuanunit_id,
            b.satuanunit_nama
           FROM satuanunit_m b) satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_racikan ON obatalkespasien_t.satuan_racikan_id = satuan_racikan.satuanunit_id
     LEFT JOIN ( SELECT b.racikan_id,
            b.racikan_nama
           FROM racikan_m b) racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama,
            b.instalasi_id
           FROM ruangan_m b) ruangan_tujuan ON penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id
     JOIN ( SELECT b.is_deleted,
            b.persen_diskon,
            b.persenppn
           FROM konfigfarmasi_k b) konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN ( SELECT b.signa_id,
            b.signa_nama
           FROM signaobat_m b) signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
     LEFT JOIN ( SELECT b.obatalkes_id,
            b.ruangan_id,
            b.qty_tersedia
           FROM stokobatalkes_r b) sr ON sr.obatalkes_id = obatalkespasien_t.obatalkes_id AND sr.ruangan_id = penjualanresep_t.ruangan_id
  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true;
");

        $this->execute('DROP VIEW if exists "public"."laporanallpo_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanallpo_v\" AS  SELECT 'OBAT'::text AS type,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr AS tanggal_pr,
    purchasereq_t.tgl_pr AS tanggal_verifikasi_pr,
    validasipoobat_t.created_date AS tanggal_po,
        CASE validasipoobat_t.is_validasi
            WHEN true THEN validasipoobat_t.tgl_validasi
            ELSE NULL::timestamp without time zone
        END AS tgl_verifikasi_po,
    btrim(validasipoobat_t.no_poobat::text) AS no_po,
    supplier_m.supplier_kode AS supplier_code,
    btrim(supplier_m.supplier_nama::text) AS supplier_name,
    manufaktur_m.nama AS manufacturer,
    obatalkes_m.obatalkes_kode AS item_code,
    obatalkes_m.obatalkes_nama AS item_name,
    validasipoobatdetail_t.qty_input::double precision AS qty_po,
    COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS po_balance,
    COALESCE(validasipoobatdetail_t.qty_sisa, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_outstanding,
    btrim(sat_besar.satuanunit_nama::text) AS uom,
    btrim(sat_besar.satuanunit_nama::text) AS from_uom,
    satuankonversi_m.nilai_konversi AS factor,
    btrim(sat_kecil.satuanunit_nama::text) AS to_uom,
    validasipoobatdetail_t.harga AS price,
    validasipoobatdetail_t.discount AS deduction_percent,
    pajak_m.pajak_persen AS addition_percent,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp AS gross_amount,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS nett_amount,
        CASE
            WHEN (COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0)) = 0 THEN 'Belum Diterima'::text
            WHEN (COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0)) < validasipoobatdetail_t.qty_input THEN 'Belum Semua Diterima'::text
            WHEN (COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0)) = validasipoobatdetail_t.qty_input THEN 'Sudah Diterima'::text
            ELSE 'Belum Diterima'::text
        END AS status_po,
    btrim(validasipoobat_t.catatan1) AS catatan_1,
    btrim(validasipoobat_t.catatan2) AS catatan_2,
    purchasereq_t.is_prcyto AS cyto,
        CASE purchasereq_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipoobat_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipoobat_t.catatan IS NOT NULL THEN validasipoobat_t.last_modified_date
            ELSE NULL::timestamp without time zone
        END AS reject_date,
    btrim(validasipoobat_t.catatan) AS reject_remarks,
    btrim(penerimaanobat_t.no_penerimaan::text) AS no_penerimaan,
    penerimaanobat_t.tgl_penerimaan AS tanggal_penerimaan,
    purchasereq_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqdetail_t.status::integer) AS status_pr,
    validasipoobatdetail_t.discount_rp AS deduction_rupiah
   FROM validasipoobat_t
     JOIN ( SELECT a.validasipoobat_id,
            a.purchasereqdetail_id,
            a.obatalkes_id,
            a.validasipoobatdetail_id,
            a.s_konversiobt_id,
            a.qty_input,
            a.qty_penerimaan,
            a.qty_retur,
            a.qty_sisa,
            a.harga,
            a.discount,
            a.discount_rp
           FROM validasipoobatdetail_t a
          WHERE a.is_deleted = false) validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     LEFT JOIN ( SELECT a.purchasereqdetail_id,
            a.purchasereq_id,
            a.status
           FROM purchasereqdetail_t a) purchasereqdetail_t ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t.purchasereqdetail_id
     LEFT JOIN ( SELECT a.purchasereq_id,
            a.no_pr,
            a.tgl_pr,
            a.is_prcyto
           FROM purchasereq_t a) purchasereq_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     JOIN ( SELECT a.obatalkes_id,
            a.manufaktur_id,
            a.obatalkes_kode,
            a.obatalkes_nama
           FROM obatalkes_m a) obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_nama,
            a.supplier_kode
           FROM supplier_m a) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.validasipoobatdetail_id,
            a.obatalkes_id,
            a.s_konversiobt_id,
            a.qty_diterima,
            a.jumlah,
            a.discount_rp,
            a.po_balance
           FROM penerimaanobatdetail_t a
             JOIN ( SELECT a1.validasipoobatdetail_id,
                    max(a1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t a1
                  GROUP BY a1.validasipoobatdetail_id) max_det ON a.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE a.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT a.penerimaanobat_id,
            a.validasipoobat_id,
            a.tgl_penerimaan,
            a.no_penerimaan
           FROM penerimaanobat_t a
             JOIN ( SELECT a1.validasipoobat_id,
                    max(a1.penerimaanobat_id) AS penerimaanobat_id
                   FROM penerimaanobat_t a1
                  GROUP BY a1.validasipoobat_id) max_pen ON a.penerimaanobat_id = max_pen.penerimaanobat_id) penerimaanobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.satuanbesar_id,
            a.satuankecil_id,
            a.nilai_konversi
           FROM satuankonversi_m a) satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
UNION ALL
 SELECT 'BARANG'::text AS type,
    purchasereqbrg_t.no_pr,
    purchasereqbrg_t.tgl_pr AS tanggal_pr,
    purchasereqbrg_t.tgl_pr AS tanggal_verifikasi_pr,
    validasipobarang_t.created_date AS tanggal_po,
        CASE validasipobarang_t.is_validasi
            WHEN true THEN validasipobarang_t.tgl_validasi
            ELSE NULL::timestamp without time zone
        END AS tgl_verifikasi_po,
    btrim(validasipobarang_t.no_pobarang::text) AS no_po,
    supplier_m.supplier_kode AS supplier_code,
    btrim(supplier_m.supplier_nama::text) AS supplier_name,
    manufaktur_m.nama AS manufacturer,
    barang_m.barang_kode::character varying(100) AS item_code,
    barang_m.barang_nama::character varying(255) AS item_name,
    validasipobarangdetail_t.qty_input::double precision AS qty_po,
    COALESCE(validasipobarangdetail_t.qty_penerimaan, 0) - COALESCE(returdetailjumlah.qty_retur::integer, 0) AS po_balance,
    COALESCE(validasipobarangdetail_t.qty_sisa, 0) + COALESCE(returdetailjumlah.qty_retur::integer, 0) AS qty_outstanding,
    btrim(sat_besar.satuanunit_nama::text) AS uom,
    btrim(sat_besar.satuanunit_nama::text) AS from_uom,
    satuankonversibrg_m.nilai_konversi AS factor,
    btrim(sat_kecil.satuanunit_nama::text) AS to_uom,
    validasipobarangdetail_t.harga AS price,
    validasipobarangdetail_t.discount AS deduction_percent,
    pajak_m.pajak_persen AS addition_percent,
    validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp AS gross_amount,
    validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp + (validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp) * validasipobarang_t.ppn_persen::double precision / 100::double precision AS nett_amount,
    btrim(fgetnamalookup(validasipobarang_t.status_penerimaan)::text) AS status_po,
    btrim(validasipobarang_t.catatan1) AS catatan_1,
    btrim(validasipobarang_t.catatan2) AS catatan_2,
    purchasereqbrg_t.is_prcyto AS cyto,
        CASE purchasereqbrg_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipobarang_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipobarang_t.catatan IS NOT NULL THEN validasipobarang_t.last_modified_date
            ELSE NULL::timestamp without time zone
        END AS reject_date,
    btrim(validasipobarang_t.catatan) AS reject_remarks,
    btrim(penerimaanbarang_t.no_penerimaan::text) AS no_penerimaan,
    penerimaanbarang_t.tgl_penerimaan AS tanggal_penerimaan,
    purchasereqbrg_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqbrgdetail_t.status::integer) AS status_pr,
    validasipobarangdetail_t.discount_rp AS deduction_rupiah
   FROM validasipobarangdetail_t
     JOIN ( SELECT a.validasipobarang_id,
            a.supplier_id,
            a.pajak_id,
            a.created_date,
            a.is_validasi,
            a.tgl_validasi,
            a.no_pobarang,
            a.ppn_persen,
            a.status_penerimaan,
            a.catatan1,
            a.catatan2,
            a.catatan,
            a.last_modified_date
           FROM validasipobarang_t a) validasipobarang_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
     LEFT JOIN ( SELECT a.purchasereqbrgdetail_id,
            a.purchasereqbrg_id,
            a.status
           FROM purchasereqbrgdetail_t a) purchasereqbrgdetail_t ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = validasipobarangdetail_t.purchasereqbrgdetail_id
     LEFT JOIN ( SELECT a.purchasereqbrg_id,
            a.no_pr,
            a.tgl_pr,
            a.is_prcyto
           FROM purchasereqbrg_t a) purchasereqbrg_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
     JOIN ( SELECT a.barang_id,
            a.barang_nama,
            a.manufaktur_id,
            a.barang_kode
           FROM barang_m a) barang_m ON validasipobarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_kode,
            a.supplier_nama
           FROM supplier_m a) supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.validasipobarangdetail_id,
            a.penerimaanbarangdetail_id,
            a.barang_id,
            a.s_konversibrg_id,
            a.qty_diterima,
            a.jumlah,
            a.discount_rp,
            a.po_balance
           FROM penerimaanbarangdetail_t a
             JOIN ( SELECT a1.validasipobarangdetail_id,
                    max(a1.penerimaanbarangdetail_id) AS penerimaanbarangdetail_id
                   FROM penerimaanbarangdetail_t a1
                  GROUP BY a1.validasipobarangdetail_id) max_det ON a.penerimaanbarangdetail_id = max_det.penerimaanbarangdetail_id
          WHERE a.is_deleted = false) penerimaanbarangdetail_t ON validasipobarangdetail_t.validasipobarangdetail_id = penerimaanbarangdetail_t.validasipobarangdetail_id
     LEFT JOIN ( SELECT a.penerimaanbarang_id,
            a.validasipobarang_id,
            a.tgl_penerimaan,
            a.no_penerimaan
           FROM penerimaanbarang_t a
             JOIN ( SELECT a1.validasipobarang_id,
                    max(a1.penerimaanbarang_id) AS penerimaanbarang_id
                   FROM penerimaanbarang_t a1
                  GROUP BY a1.validasipobarang_id) max_pen ON a.penerimaanbarang_id = max_pen.penerimaanbarang_id) penerimaanbarang_t ON validasipobarang_t.validasipobarang_id = penerimaanbarang_t.validasipobarang_id
     LEFT JOIN ( SELECT a.satuankonversibrg_id,
            a.satuanbesar_id,
            a.satuankecil_id,
            a.nilai_konversi
           FROM satuankonversibrg_m a) satuankonversibrg_m ON validasipobarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_kecil ON satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_besar ON satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON barang_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN ( SELECT penerimaanbarang_detail.validasipobarangdetail_id,
            sum(a.qty_input) AS qty_retur
           FROM returpenerimaanbarangdetail_t a
             LEFT JOIN ( SELECT a1.penerimaanbarangdetail_id,
                    a1.validasipobarangdetail_id
                   FROM penerimaanbarangdetail_t a1) penerimaanbarang_detail ON penerimaanbarang_detail.penerimaanbarangdetail_id = a.penerimaanbarangdetail_id
          GROUP BY penerimaanbarang_detail.validasipobarangdetail_id) returdetailjumlah ON validasipobarangdetail_t.validasipobarangdetail_id = returdetailjumlah.validasipobarangdetail_id
  WHERE validasipobarangdetail_t.is_deleted = false;
");

       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211012_030256_migrate_issu_1520_1521 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_030256_migrate_issu_1520_1521 cannot be reverted.\n";

        return false;
    }
    */
}
