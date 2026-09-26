-- public.bpjs_infoantrean_v source

CREATE OR REPLACE VIEW public.bpjs_infoantrean_v
AS SELECT pendaftaran.kodebooking,
    statusbpjs.kodebooking AS kodebooking_bpjs,
    pendaftaran.pendaftaran_id,
    pendaftaran.pendaftaranol_id,
    pendaftaran.no_pendaftaran,
    pendaftaran.nama_pasien,
    pendaftaran.jenis_pasien,
    pendaftaran.nopeserta_bpjs,
    pendaftaran.nomorreferensi,
    pendaftaran.tgl_pendaftaran,
    statusbpjs.task1,
    statusbpjs.task2,
    statusbpjs.task3,
    statusbpjs.task4,
    statusbpjs.task5,
    statusbpjs.task6,
    statusbpjs.task7,
    statusbpjs.task99,
    statusbpjs.noantrean,
    statusbpjs.status,
    pendaftaran.status_pasien,
    pendaftaran.tgl_mulai_antrian,
    pendaftaran.tgl_mulai_pendaftaran,
    pendaftaran.tgl_selesai_pendaftaran,
    pendaftaran.tgl_masukperiksa,
    pendaftaran.tgl_selesaiperiksa,
    pendaftaran.tgl_order_resep,
    pendaftaran.tgl_created_soap_by_dpjp,
        CASE
            WHEN pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_soap_by_dpjp THEN pendaftaran.tgl_created_soap_by_dpjp + '00:05:00'::interval
            ELSE pendaftaran.tgl_created_resumemedis
        END AS tgl_created_resumemedis,
        CASE
            WHEN pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_soap_by_dpjp AND (pendaftaran.tgl_created_soap_by_dpjp + '00:05:00'::interval) > pendaftaran.tgl_tindaklanjut_pasien THEN pendaftaran.tgl_created_soap_by_dpjp + '00:10:00'::interval
            ELSE pendaftaran.tgl_tindaklanjut_pasien
        END AS tgl_tindaklanjut_pasien,
        CASE
            WHEN pendaftaran.tgl_created_resumemedis IS NULL OR pendaftaran.tgl_created_soap_by_dpjp IS NULL OR pendaftaran.tgl_tindaklanjut_pasien IS NULL THEN NULL::timestamp without time zone
            WHEN pendaftaran.tgl_cetak_etiket < pendaftaran.tgl_tindaklanjut_pasien THEN
            CASE
                WHEN pendaftaran.tgl_created_resumemedis < pendaftaran.tgl_created_soap_by_dpjp AND (pendaftaran.tgl_created_soap_by_dpjp + '00:05:00'::interval) > pendaftaran.tgl_tindaklanjut_pasien THEN pendaftaran.tgl_created_soap_by_dpjp + '00:15:00'::interval
                ELSE pendaftaran.tgl_cetak_etiket
            END
            ELSE pendaftaran.tgl_cetak_etiket
        END AS tgl_cetak_etiket,
        CASE
            WHEN pendaftaran.tgl_created_resumemedis IS NULL OR pendaftaran.tgl_created_soap_by_dpjp IS NULL OR pendaftaran.tgl_tindaklanjut_pasien IS NULL OR pendaftaran.tgl_cetak_etiket IS NULL THEN NULL::timestamp without time zone
            WHEN pendaftaran.tgl_cetak_etiket < pendaftaran.tgl_tindaklanjut_pasien THEN pendaftaran.tgl_tindaklanjut_pasien + '00:15:00'::interval
            WHEN pendaftaran.tgl_serahkan_resep < pendaftaran.tgl_cetak_etiket OR pendaftaran.tgl_serahkan_resep < (pendaftaran.tgl_tindaklanjut_pasien + '00:05:00'::interval) THEN pendaftaran.tgl_cetak_etiket + '00:10:00'::interval
            ELSE pendaftaran.tgl_serahkan_resep
        END AS tgl_serahkan_resep,
        CASE
            WHEN statusbpjs.kodebooking IS NULL THEN false
            ELSE true
        END AS is_success_antrean
   FROM ( SELECT COALESCE(pt.no_pendaftaranol, concat('RS', pendaftaran_t.pendaftaran_id)::character varying) AS kodebooking,
            COALESCE(pt.pendaftaran_id, pendaftaran_t.pendaftaran_id) AS pendaftaran_id,
            pt.pendaftaranol_id,
            COALESCE(pendaftaran_t.no_pendaftaran, 'Belum Daftar'::character varying) AS no_pendaftaran,
            pt.status_pasien::character varying AS status_pasien,
            pasien_m.nama_pasien,
            fgetnamalookup(ar.jenis_cara_bayar) AS jenis_pasien,
            pasien_m.nopeserta_bpjs,
            ar.nomorreferensi,
            pendaftaran_t.tgl_pendaftaran,
            COALESCE(pt.tgl_checkin, pt.tgl_pendaftaranol, pendaftaran_t.tgl_pendaftaran) AS tgl_mulai_antrian,
            NULL::timestamp without time zone AS tgl_mulai_pendaftaran,
            pendaftaran_t.created_date AS tgl_selesai_pendaftaran,
            pendaftaran_t.tgl_masukperiksa,
            pendaftaran_t.tgl_selesaiperiksa,
            resep.tglreseptur AS tgl_order_resep,
            resep.tgl_cetak_etiket,
            resep.tgl_menyerahkan AS tgl_serahkan_resep,
            soap.tgl_created_by_dpjp AS tgl_created_soap_by_dpjp,
            resumemedis.created_date AS tgl_created_resumemedis,
            pasienpulang.tgl_pasien_pulang AS tgl_tindaklanjut_pasien
           FROM pendaftaran_t
             LEFT JOIN pendaftaranol_t pt ON pendaftaran_t.pendaftaran_id = pt.pendaftaran_id
             LEFT JOIN pasien_m ON pasien_m.pasien_id = COALESCE(pt.pasien_id, pendaftaran_t.pasien_id)
             LEFT JOIN antrian_t ON antrian_t.antrian_id = COALESCE(pt.antrian_id, pendaftaran_t.antrian_id)
             LEFT JOIN antrianjkn_r ar ON ar.antrian_id = antrian_t.antrian_id
             LEFT JOIN ( SELECT resep_1.pendaftaran_id,
                    COALESCE(min(reseptur_t.tglreseptur), min(resep_1.tglresep)) AS tglreseptur,
                    COALESCE(min(reseptur_t.tgl_cetak_etiket), min(resep_1.tgl_cetak_etiket)) AS tgl_cetak_etiket,
                    min(resep_1.tgl_menyerahkan) AS tgl_menyerahkan
                   FROM penjualanresep_t resep_1
                     LEFT JOIN reseptur_t ON resep_1.penjualanresep_id = reseptur_t.penjualanresep_id
                  WHERE resep_1.is_deleted = false
                  GROUP BY resep_1.pendaftaran_id) resep ON resep.pendaftaran_id = COALESCE(pt.pendaftaran_id, pendaftaran_t.pendaftaran_id)
             LEFT JOIN ( SELECT DISTINCT ON (st.pendaftaran_id) st.pendaftaran_id,
                    st.pegawai_id,
                    st.created_date AS tgl_created_by_dpjp
                   FROM soaprj_t st
                     JOIN pegawai_m pm ON pm.pegawai_id = st.pegawai_id
                  WHERE (st.additional_data::json ->> 'via_soap'::text) = 'true'::text AND st.is_deleted IS FALSE AND pm.kelompokpegawai_id = 1
                  ORDER BY st.pendaftaran_id, st.created_date) soap ON soap.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT rt.pendaftaran_id,
                    rt.created_date
                   FROM resumemedisri_t rt
                  WHERE rt.is_deleted IS FALSE) resumemedis ON resumemedis.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT pt_1.pendaftaran_id,
                    min(pt_1.tglpasienpulang) AS tgl_pasien_pulang
                   FROM pasienpulang_t pt_1
                  GROUP BY pt_1.pendaftaran_id) pasienpulang ON pasienpulang.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE pendaftaran_t.instalasi_id = 1) pendaftaran
     LEFT JOIN ( SELECT batt.kodebooking,
            max(bltt.wakturs::text) FILTER (WHERE bltt.taskid = 1) AS task1,
            max(bltt.wakturs::text) FILTER (WHERE bltt.taskid = 2) AS task2,
            max(bltt.wakturs::text) FILTER (WHERE bltt.taskid = 3) AS task3,
            max(bltt.wakturs::text) FILTER (WHERE bltt.taskid = 4) AS task4,
            max(bltt.wakturs::text) FILTER (WHERE bltt.taskid = 5) AS task5,
            max(bltt.wakturs::text) FILTER (WHERE bltt.taskid = 6) AS task6,
            max(bltt.wakturs::text) FILTER (WHERE bltt.taskid = 7) AS task7,
            max(bltt.wakturs::text) FILTER (WHERE bltt.taskid = 99) AS task99,
            batt.noantrean,
            batt.status,
            bltt.is_deleted AS taskdeleted
           FROM bpjs_list_task_t bltt
             RIGHT JOIN bpjs_antrian_tanggal_t batt ON batt.kodebooking::text = bltt.kodebooking::text
          WHERE batt.is_deleted IS FALSE
          GROUP BY batt.kodebooking, batt.noantrean, batt.status, bltt.is_deleted) statusbpjs ON statusbpjs.kodebooking::text = pendaftaran.kodebooking::text
  WHERE statusbpjs.taskdeleted IS NULL OR statusbpjs.taskdeleted IS FALSE
  ORDER BY pendaftaran.pendaftaran_id DESC NULLS LAST;