CREATE OR REPLACE VIEW "public"."infomonitoringbpjs_v" AS  SELECT a.pendaftaran_id,
    a.no_pendaftaran,
    a.pasienadmisi_id,
    a.tgl_pendaftaran,
    a.tglpasienpulang,
    a.kelaspelayanan_id,
    a.kelaspelayanan_nama,
    a.urutankelas,
    a.no_rekam_medik,
    a.nosep,
    a.nama_pasien,
    a.jeniskelamin,
    a.tanggal_lahir,
    a.carabayar_id,
    a.carabayar_nama,
    a.penjamin_id,
    a.penjamin_nama,
    a.ruangan_id,
    a.ruangan_nama,
    a.kamarruangan_id,
    a.kamarruangan_nokamar,
    a.no_tempattidur,
    a.hak_kelas,
    a.pegawai_id,
    a.dokter_dpjp,
    a.default_diagnosa,
    a.diagnosa_utama,
    a.set_diagnosautama,
    a.diagnosa_penyerta,
    a.set_diagnosapenyerta,
    a.diagnosa_tindakan,
    a.set_diagnosatindakan,
    a.is_stopakomodasi,
    a.status_periksa,
    a.status_monitor_id,
    a.status_monitor,
    a.keterangan_kelas,
    a.total_tindakan,
    a.total_obat,
    a.total_tindakan + a.total_obat AS tagihan_rs,
    a.tarif_inacbg,
    a.cek_diagnosa,
    a.is_dokter
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            kelaspelayanan_m.urutankelas,
            pasien_m.no_rekam_medik,
            bpjs_t.nosep,
            pasien_m.nama_pasien,
            pasien_m.jeniskelamin,
            pasien_m.tanggal_lahir,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_id,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            bpjs_t.klsrawat AS hak_kelas,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_dpjp,
            NULL::text AS default_diagnosa,
            diag_ri.diagnosa_utama,
            (monitorsetdiagnosa.diagnosa_kode::text || ' - '::text) || monitorsetdiagnosa.diagnosa_nama::text AS set_diagnosautama,
            diag_ri.diagnosa_penyerta,
            monitorsetdiagnosa.diag_penyerta AS set_diagnosapenyerta,
            resumemedisri_t.prosedur_diag AS diagnosa_tindakan,
            monitorsetdiagnosa.diag_tindakan AS set_diagnosatindakan,
            pendaftaran_t.is_stopakomodasi,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN look_statusranap.lookup_name
                    ELSE lkp_status_periksa.lookup_name
                END AS status_periksa,
                CASE
                    WHEN monitorsetdiagnosa.diag_utama_id IS NULL THEN 0
                    ELSE 1
                END AS status_monitor_id,
                CASE
                    WHEN monitorsetdiagnosa.diag_utama_id IS NULL THEN 'BELUM DIMONITOR'::text
                    ELSE 'SUDAH DIMONITOR'::text
                END AS status_monitor,
                CASE
                    WHEN bpjs_t.klsrawat > kelaspelayanan_m.urutankelas THEN 'NAIK KELAS'::text
                    WHEN bpjs_t.klsrawat = kelaspelayanan_m.urutankelas THEN 'KELAS SAMA'::text
                    ELSE 'TURUN KELAS'::text
                END AS keterangan_kelas,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                      WHERE tindakanpelayanan_t.is_deleted IS FALSE AND pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a_1.tarif_tindakan)::numeric, 2), 0::numeric) AS tarif_tindakan
                       FROM tindakanpelayanan_t a_1
                      WHERE a_1.is_deleted IS FALSE AND a_1.pendaftaran_id IS NOT NULL AND pendaftaran_t.pendaftaran_id = a_1.pendaftaran_id)
                    ELSE 0::numeric
                END AS total_tindakan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM obatalkespasien_t
                      WHERE obatalkespasien_t.is_deleted IS FALSE AND pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a_1.hargajual_oa)::numeric, 2), 0::numeric) AS "coalesce"
                       FROM obatalkespasien_t a_1
                      WHERE a_1.is_deleted IS FALSE AND a_1.pendaftaran_id IS NOT NULL AND pendaftaran_t.pendaftaran_id = a_1.pendaftaran_id)
                    ELSE 0::numeric
                END AS total_obat,
            COALESCE(monitorsetdiagnosa.total, 0::double precision) AS tarif_inacbg,
                CASE
                    WHEN ((diag_ri.diagnosa_utama ->> 'id'::text) ~ '^[0-9\.]+$'::text) = true THEN
                    CASE
                        WHEN ((diag_ri.diagnosa_utama ->> 'id'::text)::integer) <> monitorsetdiagnosa.diag_utama_id THEN 'BEDA'::text
                        ELSE 'SAMA'::text
                    END
                    ELSE 'BEDA'::text
                END AS cek_diagnosa,
            monitorsetdiagnosa.is_dokter
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id AND carabayar_m.carabayar_id = 6
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT diagnosa.cppt_id,
                    diagnosa.pasienadmisi_id,
                    diagnosa.a_diag_utama AS diagnosa_utama,
                    diagnosa.a_diag_penyerta AS diagnosa_penyerta
                   FROM cppt_t diagnosa
                     JOIN ( SELECT max(diagnosa_max.cppt_id) AS cppt_id,
                            diagnosa_max.pasienadmisi_id
                           FROM cppt_t diagnosa_max
                          WHERE diagnosa_max.is_deleted = false
                          GROUP BY diagnosa_max.pasienadmisi_id) cppt_max ON diagnosa.pasienadmisi_id = cppt_max.pasienadmisi_id AND diagnosa.cppt_id = cppt_max.cppt_id) diag_ri ON pendaftaran_t.pasienadmisi_id = diag_ri.pasienadmisi_id
             LEFT JOIN ( SELECT monitorsetdiagnosa_t.monitorsetdiagnosa_id,
                    monitorsetdiagnosa_t.pendaftaran_id,
                    monitorsetdiagnosa_t.pasienadmisi_id,
                    monitorsetdiagnosa_t.diag_utama_id,
                    diagnosa_m.diagnosa_kode,
                    diagnosa_m.diagnosa_nama,
                    monitorsetdiagnosa_t.diag_penyerta,
                    monitorsetdiagnosa_t.diag_tindakan,
                    monitorsetdiagnosa_t.total,
                    monitorsetdiagnosa_t.is_dokter
                   FROM monitorsetdiagnosa_t
                     JOIN diagnosa_m ON monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id
                  WHERE monitorsetdiagnosa_t.is_deleted = false) monitorsetdiagnosa ON pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id
             LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN resumemedisri_t ON pendaftaran_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id AND resumemedisri_t.is_deleted = false
             LEFT JOIN ( SELECT a_1.lookup_id,
                    a_1.lookup_name
                   FROM lookup_m a_1) look_statusranap ON pasienadmisi_t.status_ranap = look_statusranap.lookup_id
             LEFT JOIN lookup_m lkp_status_periksa ON pendaftaran_t.status_periksa::integer = lkp_status_periksa.lookup_id) a;