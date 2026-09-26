<?php

use yii\db\Migration;

/**
 * Class m220606_011957_migrate_skema_fisioterapi_hotfix_inforiwayatpasien_v
 */
class m220606_011957_migrate_skema_fisioterapi_hotfix_inforiwayatpasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.inforiwayatpasien_v;');
        $this->execute("
            CREATE VIEW \"public\".\"inforiwayatpasien_v\" AS
            SELECT 'RJ/RD'::text AS tes,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.pasienpulang_id,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.ruangan_id AS ruangan_pend_id,
            pend_ruangan.ruangan_nama AS ruangan_pend,
            NULL::integer AS ruangan_adm_id,
            NULL::character varying AS ruangan_adm,
            pendaftaran_t.pegawai_id AS dok_rjrd_id,
            dok_rjrd.nama_pegawai AS dok_rjrd,
            NULL::integer AS dok_ri_id,
            NULL::character varying AS dok_ri,
            CASE
            WHEN anamnesa_t.r_anamesa IS NULL THEN 0
            ELSE 1
            END AS r_anamesa,
            CASE
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL THEN 0
            ELSE 1
            END AS r_pemeriksaanfisik,
            CASE
            WHEN pasienmorbiditas_t.r_diagnosa IS NULL THEN 0
            ELSE 1
            END AS r_diagnosa,
            CASE
            WHEN konsulpoli_t.r_konsulpoli IS NULL THEN 0
            ELSE 1
            END AS r_konsulpoli,
            CASE
            WHEN tindakanpelayanan_t.r_tindakan IS NULL THEN 0
            ELSE 1
            END AS r_tindakan,
            CASE
            WHEN obatalkespasien_t.r_bmhp IS NULL THEN 0
            ELSE 1
            END AS r_bmhp,
            CASE
            WHEN reseptur_t.r_reseptur IS NULL THEN 0
            ELSE 1
            END AS r_reseptur,
            CASE
            WHEN anamnesa_t.r_anamesa IS NULL AND pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL AND pasienmorbiditas_t.r_diagnosa IS NULL AND tindakanpelayanan_t.r_tindakan IS NULL AND obatalkespasien_t.r_bmhp IS NULL AND reseptur_t.r_reseptur IS NULL AND COALESCE(hasilpemeriksaanlab_t.p_laboratorium, hasilpemeriksaanlab_wyna.p_laboratorium_wyna) IS NULL AND hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
            ELSE 1
            END AS r_resumemedis_rj_rd,
            CASE
            WHEN hasilpemeriksaanlab_t.p_laboratorium IS NULL THEN
            CASE
            WHEN hasilpemeriksaanlab_wyna.p_laboratorium_wyna IS NULL THEN 0
            ELSE 1
            END
            ELSE 1
            END AS hasil_laboratorium,
            CASE
            WHEN pendaftaran_t.ruangan_id = (( SELECT lookuptransaksi_m.kode_id
            FROM lookuptransaksi_m
            WHERE lookuptransaksi_m.kode_transaksi::text = 'ruang_fisio'::text)) THEN 0
            WHEN COALESCE(order_laboratorium_rd.order_lab, order_laboratorium_rj.order_lab) IS NULL THEN 0
            ELSE 1
            END AS p_laboratorium,
            CASE
            WHEN hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
            ELSE 1
            END AS p_radiologi,
            CASE
            WHEN operasi.p_operasi IS NULL THEN 0
            ELSE 1
            END AS p_operasi,
            CASE
            WHEN asesmenawal_t.r_asesmenawal IS NULL THEN 0
            ELSE 1
            END AS r_asesmenawal,
            CASE
            WHEN rekonsiliasiobat_t.r_rekonsiliasiobat IS NULL THEN 0
            ELSE 1
            END AS r_rekonsiliasiobat,
            CASE
            WHEN asesmenmedis_t.r_asesmenmedis IS NULL THEN 0
            ELSE 1
            END AS r_asesmenmedis,
            CASE
            WHEN rencanapulang_t.r_dischargeplan IS NULL THEN 0
            ELSE 1
            END AS r_dischargeplan,
            CASE
            WHEN cppt_t.r_cppt IS NULL THEN 0
            ELSE 1
            END AS r_cppt,
            CASE
            WHEN instruksitindakan_t.r_instruktitindakan IS NULL THEN 0
            ELSE 1
            END AS r_instruktitindakan,
            CASE
            WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp IS NULL THEN 0
            ELSE 1
            END AS r_instruktitindakanbmhp,
            CASE
            WHEN pemberianobat_t.r_pemberianobat IS NULL THEN 0
            ELSE 1
            END AS r_pemberianobat,
            CASE
            WHEN permintaankonsul_t.r_permintaankonsul IS NULL THEN 0
            ELSE 1
            END AS r_permintaankonsul,
            CASE
            WHEN pindahkamar_t.r_pindahkamar IS NULL THEN 0
            ELSE 1
            END AS r_pindahkamar,
            CASE
            WHEN resumemedisri_t.r_resumemedis_ri IS NULL THEN 0
            ELSE 1
            END AS r_resumemedis_ri,
            CASE
            WHEN visitdokter.r_visitedokter IS NULL THEN 0
            ELSE 1
            END AS r_visitedokter,
            CASE
            WHEN kesimpulanrd_t.r_kesimpulan_rd IS NULL THEN 0
            ELSE 1
            END AS r_kesimpulan_rd,
            CASE
            WHEN asesmenmedisrd_t.r_asesmendokter IS NULL THEN 0
            ELSE 1
            END AS r_asesmendokter,
            CASE
            WHEN asesmenperawatrd_t.r_asesmenperawat_rd IS NULL THEN 0
            ELSE 1
            END AS r_asesmenperawat_rd,
            CASE
            WHEN asuhangizi_t.r_asuhangizi IS NULL THEN 0
            ELSE 1
            END AS r_asuhangizi,
            CASE
            WHEN pendaftaran_t.ruangan_id = (( SELECT lookuptransaksi_m.kode_id
            FROM lookuptransaksi_m
            WHERE lookuptransaksi_m.kode_transaksi::text = 'ruang_fisio'::text)) AND programterapirajal_r.pendaftaran_rj_id IS NOT NULL THEN 1
            WHEN soaprj_t.r_soaprj IS NULL THEN 0
            ELSE 1
            END AS r_soaprj,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            CASE
            WHEN anamnesa_t.r_anamesa = 1 THEN 'SELESAI'::text
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN 'SELESAI'::text
            WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN 'SELESAI'::text
            WHEN konsulpoli_t.r_konsulpoli = 1 THEN 'SELESAI'::text
            WHEN obatalkespasien_t.r_bmhp = 1 THEN 'SELESAI'::text
            WHEN reseptur_t.r_reseptur = 1 THEN 'SELESAI'::text
            WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN 'SELESAI'::text
            WHEN operasi.p_operasi = 1 THEN 'SELESAI'::text
            ELSE 'BELUM SELESAI'::text
            END AS status_rj,
            CASE
            WHEN anamnesa_t.r_anamesa = 1 THEN 'SELESAI'::text
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN 'SELESAI'::text
            WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN 'SELESAI'::text
            WHEN konsulpoli_t.r_konsulpoli = 1 THEN 'SELESAI'::text
            WHEN obatalkespasien_t.r_bmhp = 1 THEN 'SELESAI'::text
            WHEN reseptur_t.r_reseptur = 1 THEN 'SELESAI'::text
            WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN 'SELESAI'::text
            WHEN operasi.p_operasi = 1 THEN 'SELESAI'::text
            WHEN asesmenawal_t.r_asesmenawal = 1 THEN 'SELESAI'::text
            WHEN rekonsiliasiobat_t.r_rekonsiliasiobat = 1 THEN 'SELESAI'::text
            WHEN asesmenmedis_t.r_asesmenmedis = 1 THEN 'SELESAI'::text
            WHEN rencanapulang_t.r_dischargeplan = 1 THEN 'SELESAI'::text
            WHEN cppt_t.r_cppt = 1 THEN 'SELESAI'::text
            WHEN instruksitindakan_t.r_instruktitindakan = 1 THEN 'SELESAI'::text
            WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp = 1 THEN 'SELESAI'::text
            WHEN pemberianobat_t.r_pemberianobat = 1 THEN 'SELESAI'::text
            WHEN pindahkamar_t.r_pindahkamar = 1 THEN 'SELESAI'::text
            WHEN permintaankonsul_t.r_permintaankonsul = 1 THEN 'SELESAI'::text
            WHEN resumemedisri_t.r_resumemedis_ri = 1 THEN 'SELESAI'::text
            WHEN visitdokter.r_visitedokter = 1 THEN 'SELESAI'::text
            WHEN kesimpulanrd_t.r_kesimpulan_rd = 1 THEN 'SELESAI'::text
            WHEN asesmenmedisrd_t.r_asesmendokter = 1 THEN 'SELESAI'::text
            WHEN asesmenperawatrd_t.r_asesmenperawat_rd = 1 THEN 'SELESAI'::text
            WHEN asuhangizi_t.r_asuhangizi = 1 THEN 'SELESAI'::text
            ELSE 'BELUM SELESAI'::text
            END AS status_rd_ri,
            pasienpulang_t.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama,
            pendaftaran_t.instalasi_id AS instalasi_pend_id,
            CASE
            WHEN dokumenupload.r_dokumenupload IS NULL THEN 0
            ELSE 1
            END AS is_dokumen,
            CASE pendaftaran_t.instalasi_id
            WHEN 1 THEN
            CASE
            WHEN anamnesa_t.r_anamesa IS NULL THEN 0
            ELSE 1
            END
            ELSE
            CASE
            WHEN asesmenperawatrd_t.r_asesmenperawat_rd IS NULL THEN 0
            ELSE 1
            END
            END AS is_askep,
            CASE carakeluar_m.carakeluar_id
            WHEN 4 THEN 1
            ELSE 0
            END AS is_meninggal,
            pemeriksaanfisik_t.pemeriksaanfisik_id,
            programterapirajal_r.pendaftaran_rj_id
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m pend_ruangan ON pendaftaran_t.ruangan_id = pend_ruangan.ruangan_id
            JOIN pegawai_m dok_rjrd ON pendaftaran_t.pegawai_id = dok_rjrd.pegawai_id
            LEFT JOIN ( SELECT anamnesa_t_1.pendaftaran_id,
            count(anamnesa_t_1.pendaftaran_id) AS r_anamesa
            FROM anamnesa_t anamnesa_t_1
            WHERE anamnesa_t_1.is_deleted = false
            GROUP BY anamnesa_t_1.pendaftaran_id) anamnesa_t ON anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT pemeriksaanfisik_t_1.pendaftaran_id,
            count(pemeriksaanfisik_t_1.pendaftaran_id) AS r_pemeriksaanfisik,
            max(pemeriksaanfisik_t_1.pemeriksaanfisik_id) AS pemeriksaanfisik_id
            FROM pemeriksaanfisik_t pemeriksaanfisik_t_1
            WHERE pemeriksaanfisik_t_1.is_deleted = false
            GROUP BY pemeriksaanfisik_t_1.pendaftaran_id) pemeriksaanfisik_t ON pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT pasienmorbiditas_t_1.pendaftaran_id,
            count(pasienmorbiditas_t_1.pendaftaran_id) AS r_diagnosa
            FROM pasienmorbiditas_t pasienmorbiditas_t_1
            WHERE pasienmorbiditas_t_1.is_deleted = false
            GROUP BY pasienmorbiditas_t_1.pendaftaran_id) pasienmorbiditas_t ON pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT konsulpoli_t_1.pendaftaran_id,
            count(konsulpoli_t_1.pendaftaran_id) AS r_konsulpoli
            FROM konsulpoli_t konsulpoli_t_1
            WHERE konsulpoli_t_1.is_deleted = false
            GROUP BY konsulpoli_t_1.pendaftaran_id) konsulpoli_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
            count(tindakanpelayanan_t_1.pendaftaran_id) AS r_tindakan
            FROM tindakanpelayanan_t tindakanpelayanan_t_1
            LEFT JOIN ( SELECT daftartindakan_m.daftartindakan_id
            FROM daftartindakan_m
            WHERE daftartindakan_m.kelompoktindakan_id <> ALL (ARRAY[17, 19])) karcis ON karcis.daftartindakan_id = tindakanpelayanan_t_1.daftartindakan_id
            GROUP BY tindakanpelayanan_t_1.pendaftaran_id) tindakanpelayanan_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT obatalkespasien_t_1.pendaftaran_id,
            count(obatalkespasien_t_1.pendaftaran_id) AS r_bmhp
            FROM obatalkespasien_t obatalkespasien_t_1
            GROUP BY obatalkespasien_t_1.pendaftaran_id) obatalkespasien_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT reseptur_t_1.pendaftaran_id,
            count(reseptur_t_1.pendaftaran_id) AS r_reseptur
            FROM reseptur_t reseptur_t_1
            WHERE reseptur_t_1.is_deleted = false
            GROUP BY reseptur_t_1.pendaftaran_id) reseptur_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT resumemedis_t_1.pendaftaran_id,
            count(resumemedis_t_1.pendaftaran_id) AS r_resumemedis_rj_rd
            FROM resumemedis_t resumemedis_t_1
            WHERE resumemedis_t_1.is_deleted = false
            GROUP BY resumemedis_t_1.pendaftaran_id) resumemedis_t ON resumemedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT hasilpemeriksaanlab_t_1.pendaftaran_id,
            count(hasilpemeriksaanlab_t_1.pendaftaran_id) AS p_laboratorium
            FROM hasilpemeriksaanlab_t hasilpemeriksaanlab_t_1
            WHERE hasilpemeriksaanlab_t_1.is_deleted = false
            GROUP BY hasilpemeriksaanlab_t_1.pendaftaran_id) hasilpemeriksaanlab_t ON hasilpemeriksaanlab_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT pasienkirimkeunitlain_t.pendaftaran_id,
            count(pasienkirimkeunitlain_t.pendaftaran_id) AS order_lab
            FROM pasienkirimkeunitlain_t
            WHERE pasienkirimkeunitlain_t.is_deleted = false AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL
            GROUP BY pasienkirimkeunitlain_t.pendaftaran_id) order_laboratorium_rd ON order_laboratorium_rd.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
            count(pasienmasukpenunjang_t.pendaftaran_id) AS order_lab
            FROM pasienmasukpenunjang_t
            WHERE pasienmasukpenunjang_t.is_deleted = false
            GROUP BY pasienmasukpenunjang_t.pendaftaran_id) order_laboratorium_rj ON order_laboratorium_rj.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
            count(pasienmasukpenunjang_t.pendaftaran_id) AS p_laboratorium_wyna
            FROM hasilpemeriksaanlab_wynacom_t
            JOIN pasienmasukpenunjang_t ON hasilpemeriksaanlab_wynacom_t.his_reg_no::text = pasienmasukpenunjang_t.no_masukpenunjang::text
            WHERE pasienmasukpenunjang_t.pasienadmisi_id IS NULL
            GROUP BY pasienmasukpenunjang_t.pendaftaran_id) hasilpemeriksaanlab_wyna ON hasilpemeriksaanlab_wyna.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT hasilpemeriksaanrad_t_1.pendaftaran_id,
            count(hasilpemeriksaanrad_t_1.pendaftaran_id) AS p_radiologi
            FROM hasilpemeriksaanrad_t hasilpemeriksaanrad_t_1
            WHERE hasilpemeriksaanrad_t_1.is_deleted = false AND hasilpemeriksaanrad_t_1.tgl_verifikasi IS NOT NULL
            GROUP BY hasilpemeriksaanrad_t_1.pendaftaran_id) hasilpemeriksaanrad_t ON hasilpemeriksaanrad_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
            count(*) AS p_operasi
            FROM inpostoperasi_t
            JOIN pasienmasukpenunjang_t ON inpostoperasi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            GROUP BY pasienmasukpenunjang_t.pendaftaran_id) operasi ON pendaftaran_t.pendaftaran_id = operasi.pendaftaran_id
            LEFT JOIN ( SELECT asesmenawal_t_1.pendaftaran_id,
            count(asesmenawal_t_1.pendaftaran_id) AS r_asesmenawal
            FROM asesmenawal_t asesmenawal_t_1
            WHERE asesmenawal_t_1.is_deleted = false
            GROUP BY asesmenawal_t_1.pendaftaran_id) asesmenawal_t ON pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id
            LEFT JOIN ( SELECT rekonsiliasiobat_t_1.pendaftaran_id,
            count(rekonsiliasiobat_t_1.pendaftaran_id) AS r_rekonsiliasiobat
            FROM rekonsiliasiobat_t rekonsiliasiobat_t_1
            WHERE rekonsiliasiobat_t_1.is_deleted = false
            GROUP BY rekonsiliasiobat_t_1.pendaftaran_id) rekonsiliasiobat_t ON pendaftaran_t.pendaftaran_id = rekonsiliasiobat_t.pendaftaran_id
            LEFT JOIN ( SELECT asesmenmedis_t_1.pendaftaran_id,
            count(asesmenmedis_t_1.pendaftaran_id) AS r_asesmenmedis
            FROM asesmenmedis_t asesmenmedis_t_1
            WHERE asesmenmedis_t_1.is_deleted = false
            GROUP BY asesmenmedis_t_1.pendaftaran_id) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id
            LEFT JOIN ( SELECT rencanapulang_t_1.pendaftaran_id,
            count(rencanapulang_t_1.pendaftaran_id) AS r_dischargeplan
            FROM rencanapulang_t rencanapulang_t_1
            WHERE rencanapulang_t_1.is_deleted = false
            GROUP BY rencanapulang_t_1.pendaftaran_id) rencanapulang_t ON pendaftaran_t.pendaftaran_id = rencanapulang_t.pendaftaran_id
            LEFT JOIN ( SELECT cppt_t_1.pendaftaran_id,
            count(cppt_t_1.pendaftaran_id) AS r_cppt
            FROM cppt_t cppt_t_1
            WHERE cppt_t_1.is_deleted = false
            GROUP BY cppt_t_1.pendaftaran_id) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
            LEFT JOIN ( SELECT instruksitindakan_t_1.pendaftaran_id,
            count(instruksitindakan_t_1.pendaftaran_id) AS r_instruktitindakan
            FROM instruksitindakan_t instruksitindakan_t_1
            WHERE instruksitindakan_t_1.is_deleted = false
            GROUP BY instruksitindakan_t_1.pendaftaran_id) instruksitindakan_t ON pendaftaran_t.pendaftaran_id = instruksitindakan_t.pendaftaran_id
            LEFT JOIN ( SELECT instruksitindakanbmhp_t_1.pendaftaran_id,
            count(instruksitindakanbmhp_t_1.pendaftaran_id) AS r_instruktitindakanbmhp
            FROM instruksitindakanbmhp_t instruksitindakanbmhp_t_1
            WHERE instruksitindakanbmhp_t_1.is_deleted = false
            GROUP BY instruksitindakanbmhp_t_1.pendaftaran_id) instruksitindakanbmhp_t ON pendaftaran_t.pendaftaran_id = instruksitindakanbmhp_t.pendaftaran_id
            LEFT JOIN ( SELECT pemberianobat_t_1.pendaftaran_id,
            count(pemberianobat_t_1.pendaftaran_id) AS r_pemberianobat
            FROM pemberianobat_t pemberianobat_t_1
            WHERE pemberianobat_t_1.is_deleted = false
            GROUP BY pemberianobat_t_1.pendaftaran_id) pemberianobat_t ON pendaftaran_t.pendaftaran_id = pemberianobat_t.pendaftaran_id
            LEFT JOIN ( SELECT permintaankonsul_t_1.pendaftaran_id,
            count(permintaankonsul_t_1.pendaftaran_id) AS r_permintaankonsul
            FROM permintaankonsul_t permintaankonsul_t_1
            WHERE permintaankonsul_t_1.is_deleted = false
            GROUP BY permintaankonsul_t_1.pendaftaran_id) permintaankonsul_t ON pendaftaran_t.pendaftaran_id = permintaankonsul_t.pendaftaran_id
            LEFT JOIN ( SELECT pindahkamar_t_1.pendaftaran_id,
            count(pindahkamar_t_1.pendaftaran_id) AS r_pindahkamar
            FROM pindahkamar_t pindahkamar_t_1
            WHERE pindahkamar_t_1.is_deleted = false
            GROUP BY pindahkamar_t_1.pendaftaran_id) pindahkamar_t ON pendaftaran_t.pendaftaran_id = pindahkamar_t.pendaftaran_id
            LEFT JOIN ( SELECT resumemedisri_t_1.pendaftaran_id,
            count(resumemedisri_t_1.pendaftaran_id) AS r_resumemedis_ri
            FROM resumemedisri_t resumemedisri_t_1
            WHERE resumemedisri_t_1.is_deleted = false
            GROUP BY resumemedisri_t_1.pendaftaran_id) resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id
            LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
            count(*) AS r_visitedokter
            FROM tindakanpelayanan_t tindakanpelayanan_t_1
            JOIN cppt_t cppt_t_1 ON tindakanpelayanan_t_1.tindakanpelayanan_id = cppt_t_1.tindakanvisite_id
            JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
            WHERE daftartindakan_m.kelompoktindakan_id = 32 AND cppt_t_1.is_visitedokter IS TRUE
            GROUP BY tindakanpelayanan_t_1.pendaftaran_id) visitdokter ON pendaftaran_t.pendaftaran_id = visitdokter.pendaftaran_id
            LEFT JOIN ( SELECT kesimpulanrd_t_1.pendaftaran_id,
            count(kesimpulanrd_t_1.pendaftaran_id) AS r_kesimpulan_rd
            FROM kesimpulanrd_t kesimpulanrd_t_1
            WHERE kesimpulanrd_t_1.is_deleted = false
            GROUP BY kesimpulanrd_t_1.pendaftaran_id) kesimpulanrd_t ON pendaftaran_t.pendaftaran_id = kesimpulanrd_t.pendaftaran_id
            LEFT JOIN ( SELECT asesmenmedisrd_t_1.pendaftaran_id,
            count(asesmenmedisrd_t_1.pendaftaran_id) AS r_asesmendokter
            FROM asesmenmedisrd_t asesmenmedisrd_t_1
            WHERE asesmenmedisrd_t_1.is_deleted = false
            GROUP BY asesmenmedisrd_t_1.pendaftaran_id) asesmenmedisrd_t ON pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id
            LEFT JOIN ( SELECT asesmenperawatrd_t_1.pendaftaran_id,
            count(asesmenperawatrd_t_1.pendaftaran_id) AS r_asesmenperawat_rd
            FROM asesmenperawatrd_t asesmenperawatrd_t_1
            WHERE asesmenperawatrd_t_1.is_deleted = false
            GROUP BY asesmenperawatrd_t_1.pendaftaran_id) asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
            LEFT JOIN ( SELECT asuhangizi_t_1.pendaftaran_id,
            count(asuhangizi_t_1.pendaftaran_id) AS r_asuhangizi
            FROM asuhangizi_t asuhangizi_t_1
            WHERE asuhangizi_t_1.is_deleted = false
            GROUP BY asuhangizi_t_1.pendaftaran_id) asuhangizi_t ON pendaftaran_t.pendaftaran_id = asuhangizi_t.pendaftaran_id
            LEFT JOIN ( SELECT soaprj_t_1.pendaftaran_id,
            count(soaprj_t_1.pendaftaran_id) AS r_soaprj
            FROM soaprj_t soaprj_t_1
            WHERE soaprj_t_1.is_deleted = false
            GROUP BY soaprj_t_1.pendaftaran_id) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
            LEFT JOIN ( SELECT dokumenupload_t.pendaftaran_id,
            count(dokumenupload_t.pendaftaran_id) AS r_dokumenupload
            FROM dokumenupload_t
            WHERE dokumenupload_t.pasienadmisi_id IS NULL AND dokumenupload_t.is_deleted = false
            GROUP BY dokumenupload_t.pendaftaran_id) dokumenupload ON pendaftaran_t.pendaftaran_id = dokumenupload.pendaftaran_id
            LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
            LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
            LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
            LEFT JOIN ( SELECT programterapirajal_r_1.pendaftaran_id,
            programterapi_t.pendaftaran_id AS pendaftaran_rj_id
            FROM programterapirajal_r programterapirajal_r_1
            JOIN programterapi_t ON programterapirajal_r_1.programterapi_id = programterapi_t.programterapi_id) programterapirajal_r ON pendaftaran_t.pendaftaran_id = programterapirajal_r.pendaftaran_id
            WHERE pendaftaran_t.instalasi_id <> 3
            UNION ALL
            SELECT 'RI'::text AS tes,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.pasien_id,
            pasienadmisi_t.pasienpulang_id,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            CASE pendaftaran_t.instalasi_id
            WHEN 12 THEN adm_ruangan.ruangan_id
            ELSE pendaftaran_t.ruangan_id
            END AS ruangan_pend_id,
            CASE pendaftaran_t.instalasi_id
            WHEN 12 THEN adm_ruangan.ruangan_nama
            ELSE ruangan_m.ruangan_nama
            END AS ruangan_pend,
            pasienadmisi_t.ruangan_id AS ruangan_adm_id,
            adm_ruangan.ruangan_nama AS ruangan_adm,
            pendaftaran_t.pegawai_id AS dok_rjrd_id,
            pegawai_m.nama_pegawai AS dok_rjrd,
            pasienadmisi_t.pegawai_id AS dok_ri_id,
            dok_ri.nama_pegawai AS dok_ri,
            CASE
            WHEN anamnesa_t.r_anamesa IS NULL THEN 0
            ELSE 1
            END AS r_anamesa,
            CASE
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL THEN 0
            ELSE 1
            END AS r_pemeriksaanfisik,
            CASE
            WHEN pasienmorbiditas_t.r_diagnosa IS NULL THEN 0
            ELSE 1
            END AS r_diagnosa,
            CASE
            WHEN konsulpoli_t.r_konsulpoli IS NULL THEN 0
            ELSE 1
            END AS r_konsulpoli,
            CASE
            WHEN tindakanpelayanan_t.r_tindakan IS NULL THEN 0
            ELSE 1
            END AS r_tindakan,
            CASE
            WHEN obatalkespasien_t.r_bmhp IS NULL THEN 0
            ELSE 1
            END AS r_bmhp,
            CASE
            WHEN reseptur_t.r_reseptur IS NULL THEN 0
            ELSE 1
            END AS r_reseptur,
            CASE
            WHEN anamnesa_t.r_anamesa IS NULL AND pemeriksaanfisik_t.r_pemeriksaanfisik IS NULL AND pasienmorbiditas_t.r_diagnosa IS NULL AND tindakanpelayanan_t.r_tindakan IS NULL AND obatalkespasien_t.r_bmhp IS NULL AND reseptur_t.r_reseptur IS NULL AND COALESCE(hasilpemeriksaanlab_t.p_laboratorium, hasilpemeriksaanlab_wyna.p_laboratorium_wyna) IS NULL AND hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
            ELSE 1
            END AS r_resumemedis_rj_rd,
            CASE
            WHEN hasilpemeriksaanlab_t.p_laboratorium IS NULL THEN
            CASE
            WHEN hasilpemeriksaanlab_wyna.p_laboratorium_wyna IS NULL THEN 0
            ELSE 1
            END
            ELSE 1
            END AS hasil_laboratorium,
            CASE
            WHEN ruangan_m.instalasi_id = 3 THEN 0
            WHEN order_laboratorium_ri.order_lab IS NULL THEN 0
            ELSE 1
            END AS p_laboratorium,
            CASE
            WHEN hasilpemeriksaanrad_t.p_radiologi IS NULL THEN 0
            ELSE 1
            END AS p_radiologi,
            CASE
            WHEN operasi.p_operasi IS NULL THEN 0
            ELSE 1
            END AS p_operasi,
            CASE
            WHEN asesmenawal_t.r_asesmenawal IS NULL THEN 0
            ELSE 1
            END AS r_asesmenawal,
            CASE
            WHEN rekonsiliasiobat_t.r_rekonsiliasiobat IS NULL THEN 0
            ELSE 1
            END AS r_rekonsiliasiobat,
            CASE
            WHEN asesmenmedis_t.r_asesmenmedis IS NULL THEN 0
            ELSE 1
            END AS r_asesmenmedis,
            CASE
            WHEN rencanapulang_t.r_dischargeplan IS NULL THEN 0
            ELSE 1
            END AS r_dischargeplan,
            CASE
            WHEN cppt_t.r_cppt IS NULL THEN 0
            ELSE 1
            END AS r_cppt,
            CASE
            WHEN instruksitindakan_t.r_instruktitindakan IS NULL THEN 0
            ELSE 1
            END AS r_instruktitindakan,
            CASE
            WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp IS NULL THEN 0
            ELSE 1
            END AS r_instruktitindakanbmhp,
            CASE
            WHEN pemberianobat_t.r_pemberianobat IS NULL THEN 0
            ELSE 1
            END AS r_pemberianobat,
            CASE
            WHEN permintaankonsul_t.r_permintaankonsul IS NULL THEN 0
            ELSE 1
            END AS r_permintaankonsul,
            CASE
            WHEN pindahkamar_t.r_pindahkamar IS NULL THEN 0
            ELSE 1
            END AS r_pindahkamar,
            CASE
            WHEN resumemedisri_t.r_resumemedis_ri IS NULL THEN 0
            ELSE 1
            END AS r_resumemedis_ri,
            CASE
            WHEN visitdokter.r_visitedokter IS NULL THEN 0
            ELSE 1
            END AS r_visitedokter,
            CASE
            WHEN kesimpulanrd_t.r_kesimpulan_rd IS NULL THEN 0
            ELSE 1
            END AS r_kesimpulan_rd,
            CASE
            WHEN asesmenmedisrd_t.r_asesmendokter IS NULL THEN 0
            ELSE 1
            END AS r_asesmendokter,
            CASE
            WHEN asesmenperawatrd_t.r_asesmenperawat_rd IS NULL THEN 0
            ELSE 1
            END AS r_asesmenperawat_rd,
            CASE
            WHEN asuhangizi_t.r_asuhangizi IS NULL THEN 0
            ELSE 1
            END AS r_asuhangizi,
            CASE
            WHEN soaprj_t.r_soaprj IS NULL THEN 0
            ELSE 1
            END AS r_soaprj,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            CASE
            WHEN anamnesa_t.r_anamesa = 1 THEN 'SELESAI'::text
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN 'SELESAI'::text
            WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN 'SELESAI'::text
            WHEN konsulpoli_t.r_konsulpoli = 1 THEN 'SELESAI'::text
            WHEN obatalkespasien_t.r_bmhp = 1 THEN 'SELESAI'::text
            WHEN reseptur_t.r_reseptur = 1 THEN 'SELESAI'::text
            WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN 'SELESAI'::text
            WHEN operasi.p_operasi = 1 THEN 'SELESAI'::text
            ELSE 'BELUM SELESAI'::text
            END AS status_rj,
            CASE
            WHEN anamnesa_t.r_anamesa = 1 THEN 'SELESAI'::text
            WHEN pemeriksaanfisik_t.r_pemeriksaanfisik = 1 THEN 'SELESAI'::text
            WHEN pasienmorbiditas_t.r_diagnosa = 1 THEN 'SELESAI'::text
            WHEN konsulpoli_t.r_konsulpoli = 1 THEN 'SELESAI'::text
            WHEN obatalkespasien_t.r_bmhp = 1 THEN 'SELESAI'::text
            WHEN reseptur_t.r_reseptur = 1 THEN 'SELESAI'::text
            WHEN resumemedis_t.r_resumemedis_rj_rd = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanlab_t.p_laboratorium = 1 THEN 'SELESAI'::text
            WHEN hasilpemeriksaanrad_t.p_radiologi = 1 THEN 'SELESAI'::text
            WHEN operasi.p_operasi = 1 THEN 'SELESAI'::text
            WHEN asesmenawal_t.r_asesmenawal = 1 THEN 'SELESAI'::text
            WHEN rekonsiliasiobat_t.r_rekonsiliasiobat = 1 THEN 'SELESAI'::text
            WHEN asesmenmedis_t.r_asesmenmedis = 1 THEN 'SELESAI'::text
            WHEN rencanapulang_t.r_dischargeplan = 1 THEN 'SELESAI'::text
            WHEN cppt_t.r_cppt = 1 THEN 'SELESAI'::text
            WHEN instruksitindakan_t.r_instruktitindakan = 1 THEN 'SELESAI'::text
            WHEN instruksitindakanbmhp_t.r_instruktitindakanbmhp = 1 THEN 'SELESAI'::text
            WHEN pemberianobat_t.r_pemberianobat = 1 THEN 'SELESAI'::text
            WHEN pindahkamar_t.r_pindahkamar = 1 THEN 'SELESAI'::text
            WHEN permintaankonsul_t.r_permintaankonsul = 1 THEN 'SELESAI'::text
            WHEN resumemedisri_t.r_resumemedis_ri = 1 THEN 'SELESAI'::text
            WHEN visitdokter.r_visitedokter = 1 THEN 'SELESAI'::text
            WHEN kesimpulanrd_t.r_kesimpulan_rd = 1 THEN 'SELESAI'::text
            WHEN asesmenmedisrd_t.r_asesmendokter = 1 THEN 'SELESAI'::text
            WHEN asesmenperawatrd_t.r_asesmenperawat_rd = 1 THEN 'SELESAI'::text
            WHEN asuhangizi_t.r_asuhangizi = 1 THEN 'SELESAI'::text
            ELSE 'BELUM SELESAI'::text
            END AS status_rd_ri,
            pasienpulang_t.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama,
            CASE pendaftaran_t.instalasi_id
            WHEN 12 THEN adm_ruangan.instalasi_id
            ELSE pendaftaran_t.instalasi_id
            END AS instalasi_pend_id,
            CASE
            WHEN dokumenupload.r_dokumenupload IS NULL THEN 0
            ELSE 1
            END AS is_dokumen,
            CASE
            WHEN asesmenawal_t.r_asesmenawal IS NULL THEN 0
            ELSE 1
            END AS is_askep,
            CASE carakeluar_m.carakeluar_id
            WHEN 4 THEN 1
            ELSE 0
            END AS is_meninggal,
            NULL::integer AS pemeriksaanfisik_id,
            programterapirajal_r.pendaftaran_rj_id
            FROM pendaftaran_t
            JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN ruangan_m adm_ruangan ON pasienadmisi_t.ruangan_id = adm_ruangan.ruangan_id
            LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
            JOIN pegawai_m dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
            LEFT JOIN ( SELECT anamnesa_t_1.pendaftaran_id,
            count(anamnesa_t_1.pendaftaran_id) AS r_anamesa
            FROM anamnesa_t anamnesa_t_1
            WHERE anamnesa_t_1.is_deleted = false
            GROUP BY anamnesa_t_1.pendaftaran_id) anamnesa_t ON anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT pemeriksaanfisik_t_1.pendaftaran_id,
            count(pemeriksaanfisik_t_1.pendaftaran_id) AS r_pemeriksaanfisik
            FROM pemeriksaanfisik_t pemeriksaanfisik_t_1
            WHERE pemeriksaanfisik_t_1.is_deleted = false
            GROUP BY pemeriksaanfisik_t_1.pendaftaran_id) pemeriksaanfisik_t ON pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT pasienmorbiditas_t_1.pendaftaran_id,
            count(pasienmorbiditas_t_1.pendaftaran_id) AS r_diagnosa
            FROM pasienmorbiditas_t pasienmorbiditas_t_1
            WHERE pasienmorbiditas_t_1.is_deleted = false
            GROUP BY pasienmorbiditas_t_1.pendaftaran_id) pasienmorbiditas_t ON pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT konsulpoli_t_1.pendaftaran_id,
            count(konsulpoli_t_1.pendaftaran_id) AS r_konsulpoli
            FROM konsulpoli_t konsulpoli_t_1
            WHERE konsulpoli_t_1.is_deleted = false
            GROUP BY konsulpoli_t_1.pendaftaran_id) konsulpoli_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
            count(tindakanpelayanan_t_1.pendaftaran_id) AS r_tindakan
            FROM tindakanpelayanan_t tindakanpelayanan_t_1
            LEFT JOIN ( SELECT daftartindakan_m.daftartindakan_id
            FROM daftartindakan_m
            WHERE daftartindakan_m.kelompoktindakan_id <> ALL (ARRAY[17, 19])) karcis ON karcis.daftartindakan_id = tindakanpelayanan_t_1.daftartindakan_id
            GROUP BY tindakanpelayanan_t_1.pendaftaran_id) tindakanpelayanan_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT obatalkespasien_t_1.pendaftaran_id,
            count(obatalkespasien_t_1.pendaftaran_id) AS r_bmhp
            FROM obatalkespasien_t obatalkespasien_t_1
            GROUP BY obatalkespasien_t_1.pendaftaran_id) obatalkespasien_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT reseptur_t_1.pendaftaran_id,
            count(reseptur_t_1.pendaftaran_id) AS r_reseptur
            FROM reseptur_t reseptur_t_1
            WHERE reseptur_t_1.is_deleted = false
            GROUP BY reseptur_t_1.pendaftaran_id) reseptur_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT resumemedis_t_1.pendaftaran_id,
            count(resumemedis_t_1.pendaftaran_id) AS r_resumemedis_rj_rd
            FROM resumemedis_t resumemedis_t_1
            WHERE resumemedis_t_1.is_deleted = false
            GROUP BY resumemedis_t_1.pendaftaran_id) resumemedis_t ON resumemedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT hasilpemeriksaanlab_t_1.pendaftaran_id,
            hasilpemeriksaanlab_t_1.pasienadmisi_id,
            count(hasilpemeriksaanlab_t_1.pendaftaran_id) AS p_laboratorium
            FROM hasilpemeriksaanlab_t hasilpemeriksaanlab_t_1
            WHERE hasilpemeriksaanlab_t_1.is_deleted = false
            GROUP BY hasilpemeriksaanlab_t_1.pendaftaran_id, hasilpemeriksaanlab_t_1.pasienadmisi_id) hasilpemeriksaanlab_t ON hasilpemeriksaanlab_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = hasilpemeriksaanlab_t.pasienadmisi_id
            LEFT JOIN ( SELECT pasienkirimkeunitlain_t.pasienadmisi_id,
            count(pasienkirimkeunitlain_t.pasienadmisi_id) AS order_lab
            FROM pasienkirimkeunitlain_t
            WHERE pasienkirimkeunitlain_t.is_deleted = false AND pasienkirimkeunitlain_t.pasienadmisi_id IS NOT NULL
            GROUP BY pasienkirimkeunitlain_t.pasienadmisi_id) order_laboratorium_ri ON order_laboratorium_ri.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
            LEFT JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
            count(pasienmasukpenunjang_t.pendaftaran_id) AS p_laboratorium_wyna
            FROM hasilpemeriksaanlab_wynacom_t
            JOIN pasienmasukpenunjang_t ON hasilpemeriksaanlab_wynacom_t.his_reg_no::text = pasienmasukpenunjang_t.no_masukpenunjang::text
            WHERE pasienmasukpenunjang_t.pasienadmisi_id IS NOT NULL
            GROUP BY pasienmasukpenunjang_t.pendaftaran_id) hasilpemeriksaanlab_wyna ON hasilpemeriksaanlab_wyna.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN ( SELECT hasilpemeriksaanrad_t_1.pendaftaran_id,
            hasilpemeriksaanrad_t_1.pasienadmisi_id,
            count(hasilpemeriksaanrad_t_1.pendaftaran_id) AS p_radiologi
            FROM hasilpemeriksaanrad_t hasilpemeriksaanrad_t_1
            WHERE hasilpemeriksaanrad_t_1.is_deleted = false AND hasilpemeriksaanrad_t_1.tgl_verifikasi IS NOT NULL
            GROUP BY hasilpemeriksaanrad_t_1.pendaftaran_id, hasilpemeriksaanrad_t_1.pasienadmisi_id) hasilpemeriksaanrad_t ON hasilpemeriksaanrad_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = hasilpemeriksaanrad_t.pasienadmisi_id
            LEFT JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienadmisi_id,
            count(*) AS p_operasi
            FROM inpostoperasi_t
            JOIN pasienmasukpenunjang_t ON inpostoperasi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            GROUP BY pasienmasukpenunjang_t.pendaftaran_id, pasienmasukpenunjang_t.pasienadmisi_id) operasi ON pendaftaran_t.pendaftaran_id = operasi.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = operasi.pasienadmisi_id
            LEFT JOIN ( SELECT asesmenawal_t_1.pendaftaran_id,
            count(asesmenawal_t_1.pendaftaran_id) AS r_asesmenawal
            FROM asesmenawal_t asesmenawal_t_1
            WHERE asesmenawal_t_1.is_deleted = false
            GROUP BY asesmenawal_t_1.pendaftaran_id) asesmenawal_t ON pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id
            LEFT JOIN ( SELECT rekonsiliasiobat_t_1.pendaftaran_id,
            count(rekonsiliasiobat_t_1.pendaftaran_id) AS r_rekonsiliasiobat
            FROM rekonsiliasiobat_t rekonsiliasiobat_t_1
            WHERE rekonsiliasiobat_t_1.is_deleted = false
            GROUP BY rekonsiliasiobat_t_1.pendaftaran_id) rekonsiliasiobat_t ON pendaftaran_t.pendaftaran_id = rekonsiliasiobat_t.pendaftaran_id
            LEFT JOIN ( SELECT asesmenmedis_t_1.pendaftaran_id,
            count(asesmenmedis_t_1.pendaftaran_id) AS r_asesmenmedis
            FROM asesmenmedis_t asesmenmedis_t_1
            WHERE asesmenmedis_t_1.is_deleted = false
            GROUP BY asesmenmedis_t_1.pendaftaran_id) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id
            LEFT JOIN ( SELECT rencanapulang_t_1.pendaftaran_id,
            count(rencanapulang_t_1.pendaftaran_id) AS r_dischargeplan
            FROM rencanapulang_t rencanapulang_t_1
            WHERE rencanapulang_t_1.is_deleted = false
            GROUP BY rencanapulang_t_1.pendaftaran_id) rencanapulang_t ON pendaftaran_t.pendaftaran_id = rencanapulang_t.pendaftaran_id
            LEFT JOIN ( SELECT cppt_t_1.pendaftaran_id,
            cppt_t_1.pasienadmisi_id,
            count(cppt_t_1.pendaftaran_id) AS r_cppt
            FROM cppt_t cppt_t_1
            WHERE cppt_t_1.is_deleted = false
            GROUP BY cppt_t_1.pendaftaran_id, cppt_t_1.pasienadmisi_id) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = cppt_t.pasienadmisi_id
            LEFT JOIN ( SELECT instruksitindakan_t_1.pendaftaran_id,
            count(instruksitindakan_t_1.pendaftaran_id) AS r_instruktitindakan
            FROM instruksitindakan_t instruksitindakan_t_1
            WHERE instruksitindakan_t_1.is_deleted = false
            GROUP BY instruksitindakan_t_1.pendaftaran_id) instruksitindakan_t ON pendaftaran_t.pendaftaran_id = instruksitindakan_t.pendaftaran_id
            LEFT JOIN ( SELECT instruksitindakanbmhp_t_1.pendaftaran_id,
            count(instruksitindakanbmhp_t_1.pendaftaran_id) AS r_instruktitindakanbmhp
            FROM instruksitindakanbmhp_t instruksitindakanbmhp_t_1
            WHERE instruksitindakanbmhp_t_1.is_deleted = false
            GROUP BY instruksitindakanbmhp_t_1.pendaftaran_id) instruksitindakanbmhp_t ON pendaftaran_t.pendaftaran_id = instruksitindakanbmhp_t.pendaftaran_id
            LEFT JOIN ( SELECT pemberianobat_t_1.pendaftaran_id,
            count(pemberianobat_t_1.pendaftaran_id) AS r_pemberianobat
            FROM pemberianobat_t pemberianobat_t_1
            WHERE pemberianobat_t_1.is_deleted = false
            GROUP BY pemberianobat_t_1.pendaftaran_id) pemberianobat_t ON pendaftaran_t.pendaftaran_id = pemberianobat_t.pendaftaran_id
            LEFT JOIN ( SELECT permintaankonsul_t_1.pendaftaran_id,
            count(permintaankonsul_t_1.pendaftaran_id) AS r_permintaankonsul
            FROM permintaankonsul_t permintaankonsul_t_1
            WHERE permintaankonsul_t_1.is_deleted = false
            GROUP BY permintaankonsul_t_1.pendaftaran_id) permintaankonsul_t ON pendaftaran_t.pendaftaran_id = permintaankonsul_t.pendaftaran_id
            LEFT JOIN ( SELECT pindahkamar_t_1.pendaftaran_id,
            count(pindahkamar_t_1.pendaftaran_id) AS r_pindahkamar
            FROM pindahkamar_t pindahkamar_t_1
            WHERE pindahkamar_t_1.is_deleted = false
            GROUP BY pindahkamar_t_1.pendaftaran_id) pindahkamar_t ON pendaftaran_t.pendaftaran_id = pindahkamar_t.pendaftaran_id
            LEFT JOIN ( SELECT resumemedisri_t_1.pendaftaran_id,
            count(resumemedisri_t_1.pendaftaran_id) AS r_resumemedis_ri
            FROM resumemedisri_t resumemedisri_t_1
            WHERE resumemedisri_t_1.is_deleted = false
            GROUP BY resumemedisri_t_1.pendaftaran_id) resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id
            LEFT JOIN ( SELECT tindakanpelayanan_t_1.pendaftaran_id,
            count(*) AS r_visitedokter
            FROM tindakanpelayanan_t tindakanpelayanan_t_1
            JOIN cppt_t cppt_t_1 ON tindakanpelayanan_t_1.tindakanpelayanan_id = cppt_t_1.tindakanvisite_id
            JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
            WHERE daftartindakan_m.kelompoktindakan_id = 32 AND cppt_t_1.is_visitedokter IS TRUE
            GROUP BY tindakanpelayanan_t_1.pendaftaran_id) visitdokter ON pendaftaran_t.pendaftaran_id = visitdokter.pendaftaran_id
            LEFT JOIN ( SELECT kesimpulanrd_t_1.pendaftaran_id,
            count(kesimpulanrd_t_1.pendaftaran_id) AS r_kesimpulan_rd
            FROM kesimpulanrd_t kesimpulanrd_t_1
            WHERE kesimpulanrd_t_1.is_deleted = false
            GROUP BY kesimpulanrd_t_1.pendaftaran_id) kesimpulanrd_t ON pendaftaran_t.pendaftaran_id = kesimpulanrd_t.pendaftaran_id
            LEFT JOIN ( SELECT asesmenmedisrd_t_1.pendaftaran_id,
            count(asesmenmedisrd_t_1.pendaftaran_id) AS r_asesmendokter
            FROM asesmenmedisrd_t asesmenmedisrd_t_1
            WHERE asesmenmedisrd_t_1.is_deleted = false
            GROUP BY asesmenmedisrd_t_1.pendaftaran_id) asesmenmedisrd_t ON pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id
            LEFT JOIN ( SELECT asesmenperawatrd_t_1.pendaftaran_id,
            count(asesmenperawatrd_t_1.pendaftaran_id) AS r_asesmenperawat_rd
            FROM asesmenperawatrd_t asesmenperawatrd_t_1
            WHERE asesmenperawatrd_t_1.is_deleted = false
            GROUP BY asesmenperawatrd_t_1.pendaftaran_id) asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
            LEFT JOIN ( SELECT asuhangizi_t_1.pendaftaran_id,
            count(asuhangizi_t_1.pendaftaran_id) AS r_asuhangizi
            FROM asuhangizi_t asuhangizi_t_1
            WHERE asuhangizi_t_1.is_deleted = false
            GROUP BY asuhangizi_t_1.pendaftaran_id) asuhangizi_t ON pendaftaran_t.pendaftaran_id = asuhangizi_t.pendaftaran_id
            LEFT JOIN ( SELECT soaprj_t_1.pendaftaran_id,
            count(soaprj_t_1.pendaftaran_id) AS r_soaprj
            FROM soaprj_t soaprj_t_1
            WHERE soaprj_t_1.is_deleted = false
            GROUP BY soaprj_t_1.pendaftaran_id) soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
            LEFT JOIN ( SELECT dokumenupload_t.pasienadmisi_id,
            count(dokumenupload_t.pasienadmisi_id) AS r_dokumenupload
            FROM dokumenupload_t
            WHERE dokumenupload_t.is_deleted = false
            GROUP BY dokumenupload_t.pasienadmisi_id) dokumenupload ON pasienadmisi_t.pasienadmisi_id = dokumenupload.pasienadmisi_id
            LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
            LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
            LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
            LEFT JOIN ( SELECT programterapirajal_r_1.pendaftaran_id,
            programterapi_t.pendaftaran_id AS pendaftaran_rj_id
            FROM programterapirajal_r programterapirajal_r_1
            JOIN programterapi_t ON programterapirajal_r_1.programterapi_id = programterapi_t.programterapi_id) programterapirajal_r ON pendaftaran_t.pendaftaran_id = programterapirajal_r.pendaftaran_id
            ;");
        $this->execute('
            ALTER TABLE public.inforiwayatpasien_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220606_011957_migrate_skema_fisioterapi_hotfix_inforiwayatpasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220606_011957_migrate_skema_fisioterapi_hotfix_inforiwayatpasien_v cannot be reverted.\n";

        return false;
    }
    */
}
