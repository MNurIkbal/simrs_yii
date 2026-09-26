<?php

use yii\db\Migration;

/**
 * Class m200214_041419_alter_view_20200212_2
 */
class m200214_041419_alter_view_20200212_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."inforeseptur_v";');

        $this->execute("
            CREATE OR REPLACE VIEW public.inforeseptur_v AS
            SELECT reseptur_t.reseptur_id,
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
            reseptur_t.noresep,
            reseptur_t.penjualanresep_id,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
            ruangan_reseptur.ruangan_nama AS ruangan_reseptur,
            fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
            reseptur_t.pegawai_id,
            pegawai_m.nama_pegawai,
            ruangan_reseptur.instalasi_id AS instalasi_reseptur_id,
            instalasi_reseptur.instalasi_nama AS instalasi_reseptur,
            ruangan_tujuan.instalasi_id AS instalasi_tujuan_id,
            instalasi_tujuan.instalasi_nama AS instalasi_tujuan,
            sum(obatalkes_m.harganetto) AS total_harganetto,
            antrian_t.no_antrian,
            reseptur_t.status_reseptur AS status_reseptur_id,
            reseptur_t.is_hamil,
            reseptur_t.berat_badan,
            reseptur_t.tinggi_badan,
            reseptur_t.luas_tubuh,
            reseptur_t.diagnosa_id,
            concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama) AS diagnosa_nama,
            reseptur_t.instruksi_id,
            reseptur_t.antrian_id,
            string_agg((resepturdetail_t.racikan_id)::text, '-'::text) AS antrian_racikan,
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
            sum(resepturdetail_t.hargajual_reseptur) AS total_tagihan
           FROM (((((((((((((((((((((reseptur_t
             JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
             JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
             JOIN ruangan_m ruangan_reseptur ON ((reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id)))
             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
             JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
             JOIN instalasi_m instalasi_reseptur ON ((ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id)))
             JOIN instalasi_m instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
             JOIN resepturdetail_t ON (((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id) AND (resepturdetail_t.is_deleted = false))))
             JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             LEFT JOIN antrian_t ON ((reseptur_t.antrian_id = antrian_t.antrian_id)))
             LEFT JOIN diagnosa_m ON ((reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id)))
             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
             LEFT JOIN penjualanresep_t ON ((reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
             JOIN ( SELECT resepturdetail_t_1.reseptur_id,
                    resepturdetail_t_1.iter
                   FROM resepturdetail_t resepturdetail_t_1
                  WHERE (resepturdetail_t_1.is_deleted = false)
                  GROUP BY resepturdetail_t_1.reseptur_id, resepturdetail_t_1.iter) iter ON ((iter.reseptur_id = reseptur_t.reseptur_id)))
             LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
             LEFT JOIN asesmenperawatrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id)))
             LEFT JOIN asesmenawal_t ON ((pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id)))
             LEFT JOIN pasienmorbiditas_t ON (((pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false) AND (pasienmorbiditas_t.kelompokdiagnosa_id = 2))))
             LEFT JOIN ( SELECT instruksi_t.instruksi_id,
                    cppt_t.cppt_id,
                    cppt_t.pendaftaran_id,
                    (cppt_t.a_diag_utama ->> 'text'::text) AS diagnosa_utama
                   FROM (instruksi_t
                     JOIN cppt_t ON (((instruksi_t.cppt_id = cppt_t.cppt_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true))))
                  WHERE ((instruksi_t.is_deleted = false) AND (instruksi_t.is_active = true))) cppt_rd ON (((pendaftaran_t.pendaftaran_id = cppt_rd.pendaftaran_id) AND (reseptur_t.instruksi_id = cppt_rd.instruksi_id))))
          WHERE ((reseptur_t.is_deleted = false) AND (reseptur_t.is_active = true))
          GROUP BY reseptur_t.instruksi_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.umur, pasien_m.tanggal_lahir, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), diagnosa_m.diagnosa_namalainnya, reseptur_t.reseptur_id, reseptur_t.pasien_id, reseptur_t.pendaftaran_id, reseptur_t.pasienadmisi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, reseptur_t.ruangan_id, reseptur_t.ruanganreseptur_id, reseptur_t.tglreseptur, reseptur_t.noresep, reseptur_t.penjualanresep_id, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_tujuan.ruangan_nama, ruangan_reseptur.ruangan_nama, reseptur_t.status_reseptur, reseptur_t.pegawai_id, pegawai_m.nama_pegawai, ruangan_reseptur.instalasi_id, instalasi_reseptur.instalasi_nama, ruangan_tujuan.instalasi_id, instalasi_tujuan.instalasi_nama, antrian_t.no_antrian, reseptur_t.is_hamil, reseptur_t.berat_badan, reseptur_t.tinggi_badan, reseptur_t.luas_tubuh, reseptur_t.diagnosa_id, penjualanresep_t.catatan, resepturdetail_t.iter, penjualanresep_t.noresep,
                CASE
                    WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
                    WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
                    ELSE asesmenawal_t.nama_alergi
                END,
                CASE
                    WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
                    WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
                    WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
                    ELSE NULL::text
                END, (concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama));
        ");

        $this->execute('ALTER TABLE "public"."inforeseptur_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."inforesep_v";');

        $this->execute("
            CREATE OR REPLACE VIEW public.inforesep_v AS  SELECT 'reseptur'::text AS jenis,
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
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
                ruangan_tujuan.ruangan_nama AS ruangan_reseptur,
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
                reseptur_t.berat_badan,
                reseptur_t.tinggi_badan,
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
                string_agg((resepturdetail_t.racikan_id)::text, '-'::text) AS antrian_racikan,
                sum(obatalkes_m.harganetto) AS total_harganetto,
                penjualanresep_t.biayaadministrasi,
                penjualanresep_t.totalhargajual,
                (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihan,
                NULL::character varying AS nama_pembeli,
                penjualanresep_t.status_bayar
               FROM ((((((((((((((((((((reseptur_t
                 LEFT JOIN penjualanresep_t ON (((reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id) AND (penjualanresep_t.is_deleted = false))))
                 JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
                 JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
                 JOIN ruangan_m ruangan_reseptur ON ((reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id)))
                 JOIN instalasi_m instalasi_reseptur ON ((ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id)))
                 JOIN instalasi_m instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN resepturdetail_t ON (((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id) AND (resepturdetail_t.is_deleted = false))))
                 JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 LEFT JOIN antrian_t ON ((reseptur_t.antrian_id = antrian_t.antrian_id)))
                 LEFT JOIN diagnosa_m ON ((reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id)))
                 LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
                 LEFT JOIN asesmenperawatrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id)))
                 LEFT JOIN asesmenawal_t ON ((pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id)))
                 LEFT JOIN pasienmorbiditas_t ON (((pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false) AND (pasienmorbiditas_t.kelompokdiagnosa_id = 2))))
                 LEFT JOIN ( SELECT instruksi_t.instruksi_id,
                        cppt_t.cppt_id,
                        cppt_t.pendaftaran_id,
                        (cppt_t.a_diag_utama ->> 'text'::text) AS diagnosa_utama
                       FROM (instruksi_t
                         JOIN cppt_t ON (((instruksi_t.cppt_id = cppt_t.cppt_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true))))
                      WHERE ((instruksi_t.is_deleted = false) AND (instruksi_t.is_active = true))) cppt_rd ON (((pendaftaran_t.pendaftaran_id = cppt_rd.pendaftaran_id) AND (reseptur_t.instruksi_id = cppt_rd.instruksi_id))))
              WHERE ((reseptur_t.is_deleted = false) AND (reseptur_t.is_active = true))
              GROUP BY reseptur_t.instruksi_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.umur, pasien_m.tanggal_lahir, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), diagnosa_m.diagnosa_namalainnya, reseptur_t.reseptur_id, reseptur_t.pasien_id, reseptur_t.pendaftaran_id, reseptur_t.pasienadmisi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, reseptur_t.ruangan_id, reseptur_t.ruanganreseptur_id, reseptur_t.tglreseptur, reseptur_t.noresep, reseptur_t.penjualanresep_id, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_tujuan.ruangan_nama, ruangan_reseptur.ruangan_nama, reseptur_t.status_reseptur, reseptur_t.pegawai_id, pegawai_m.nama_pegawai, ruangan_reseptur.instalasi_id, instalasi_reseptur.instalasi_nama, ruangan_tujuan.instalasi_id, instalasi_tujuan.instalasi_nama, antrian_t.no_antrian, reseptur_t.is_hamil, reseptur_t.berat_badan, reseptur_t.tinggi_badan, reseptur_t.luas_tubuh, reseptur_t.diagnosa_id, penjualanresep_t.catatan, resepturdetail_t.iter, penjualanresep_t.noresep, penjualanresep_t.tglresep, penjualanresep_t.penjualanresep_id,
                    CASE
                        WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
                        WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
                        ELSE asesmenawal_t.nama_alergi
                    END,
                    CASE
                        WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
                        WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
                        WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
                        ELSE NULL::text
                    END, (concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama))
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
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                ruangan_resep.ruangan_nama AS ruangan_tujuan,
                NULL::text AS ruangan_reseptur,
                    CASE
                        WHEN (penjualanresep_t.status_reseptur = 347) THEN 'Dalam Proses'::character varying
                        ELSE fgetnamalookup((penjualanresep_t.status_reseptur)::integer)
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
                NULL::integer AS berat_badan,
                NULL::integer AS tinggi_badan,
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
                penjualanresep_t.status_bayar
               FROM (((((((((penjualanresep_t
                 LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
                 JOIN ruangan_m ruangan_resep ON ((penjualanresep_t.ruangan_id = ruangan_resep.ruangan_id)))
                 JOIN instalasi_m instalasi_resep ON ((ruangan_resep.instalasi_id = instalasi_resep.instalasi_id)))
                 LEFT JOIN kelaspelayanan_m ON ((penjualanresep_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN carabayar_m ON ((penjualanresep_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN antrian_t ON ((penjualanresep_t.antrian_id = antrian_t.antrian_id)))
              WHERE (penjualanresep_t.reseptur_id IS NULL);
        ");

        $this->execute('ALTER TABLE "public"."inforesep_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."inforesepdetail_v";');

        $this->execute("
            CREATE OR REPLACE VIEW public.inforesepdetail_v AS  SELECT 'reseptur'::text AS jenis,
                resepturdetail_t.resepturdetail_id,
                NULL::integer AS obatalkespasien_id,
                NULL::integer AS penjualanresep_id,
                resepturdetail_t.reseptur_id,
                reseptur_t.pendaftaran_id,
                reseptur_t.pasien_id,
                resepturdetail_t.obatalkes_id,
                resepturdetail_t.satuankecil_id, 
                resepturdetail_t.racikan_id,
                resepturdetail_t.signa_id,
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
                signaobat_m.signa_nama,
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
                ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
                (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
                (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
                ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
                ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
                (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
                pendaftaran_t.status_periksa,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
                reseptur_t.status_reseptur AS status_reseptur_id,
                fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
                resepturdetail_t.is_deleted,
                resepturdetail_t.is_active,
                obatalkespasien_t.additional_data,
                obatalkespasien_t.hargasatuan_oa,
                resepturdetail_t.qty_konversi,
                resepturdetail_t.additional_data AS additional_reseptur,
                (((resepturdetail_t.additional_data)::json ->> 'satuaninput_id'::text))::character varying AS satuaninput_id,
                (((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text))::character varying AS satuan_input,
                (((resepturdetail_t.additional_data)::json ->> 'satuankonversi_id'::text))::character varying AS satuankonversi_id,
                (((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text))::character varying AS satuan_konversi,
                (((resepturdetail_t.additional_data)::json ->> 'harga_konversi'::text))::character varying AS harga_konversi,
                (((resepturdetail_t.additional_data)::json ->> 'nilai_konversi'::text))::character varying AS nilai_konversi,
                (0)::double precision AS biayaadministrasiresep,
                (0)::double precision AS totalhargajualresep,
                (0)::double precision AS totaltagihanresep,
                    NULL AS nama_pembeli
               FROM ((((((((((((resepturdetail_t
                 JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
                 JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 JOIN satuanunit_m satuan_kecil ON ((resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
                 JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
                 LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
                 JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
                 LEFT JOIN rotd_t ON ((resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id)))
                 LEFT JOIN pegawai_m ON ((rotd_t.pegawairotd_id = rotd_t.pegawairotd_id)))
                 LEFT JOIN obatalkespasien_t ON ((resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id)))
                 JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
              WHERE ((resepturdetail_t.is_deleted = false) AND (resepturdetail_t.is_active = true))
            UNION ALL
             SELECT 'resep'::text AS jenis,
                NULL::integer AS resepturdetail_id,
                obatalkespasien_t.obatalkespasien_id,
                penjualanresep_t.penjualanresep_id,
                NULL::integer AS reseptur_id,
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
                obatalkespasien_t.qty_oa AS qty_reseptur,
                satuan_kecil.satuanunit_nama AS satuan_kecil,
                obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
                obatalkespasien_t.hargajual_oa AS totalharga_jual,
                obatalkespasien_t.etiket,
                NULL::integer AS iter,
                signaobat_m.signa_nama,
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
                ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
                (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
                (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
                ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
                ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
                (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
                pendaftaran_t.status_periksa,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
                penjualanresep_t.status_reseptur AS status_reseptur_id,
                fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur,
                obatalkespasien_t.is_deleted,
                obatalkespasien_t.is_active,
                obatalkespasien_t.additional_data,
                obatalkespasien_t.hargasatuan_oa,
                obatalkespasien_t.qty_konversi,
                obatalkespasien_t.additional_data AS additional_reseptur,
                (((obatalkespasien_t.additional_data)::json ->> 'satuaninput_id'::text))::character varying AS satuaninput_id,
                (((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text))::character varying AS satuan_input,
                (((obatalkespasien_t.additional_data)::json ->> 'satuankonversi_id'::text))::character varying AS satuankonversi_id,
                (((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text))::character varying AS satuan_konversi,
                (((obatalkespasien_t.additional_data)::json ->> 'harga_konversi'::text))::character varying AS harga_konversi,
                (((obatalkespasien_t.additional_data)::json ->> 'nilai_konversi'::text))::character varying AS nilai_konversi,
                penjualanresep_t.biayaadministrasi AS biayaadministrasiresep,
                penjualanresep_t.totalhargajual AS totalhargajualresep,
                (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihanresep,
                    penjualanresep_t.nama_pembeli
               FROM ((((((((((obatalkespasien_t
                 JOIN penjualanresep_t ON (((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id) AND (penjualanresep_t.reseptur_id IS NULL))))
                 LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
                 LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
                 LEFT JOIN ruangan_m ruangan_tujuan ON ((penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id)))
                 JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
                 LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
              WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.is_active = true));
        ");

        $this->execute('ALTER TABLE "public"."inforesepdetail_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200214_041419_alter_view_20200212_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200214_041419_alter_view_20200212_2 cannot be reverted.\n";

        return false;
    }
    */
}
