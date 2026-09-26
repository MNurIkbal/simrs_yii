-- public.dokumeneklaimparams_v source

CREATE OR REPLACE VIEW public.dokumeneklaimparams_v
AS SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.pasienadmisi_id,
    rujukanbpjs_t.rujukanbpjs_id,
    COALESCE(pasienadmisi_t.bpjs_id, pendaftaran_t.bpjs_id, rencanakontrol_t.bpjs_id) AS bpjs_id,
    NULL::timestamp without time zone AS tgl_cppt,
    COALESCE(rencanakontrol_t.no_spri, rencanakontrol_t.nosuratkontrol) AS nosuratkontrol,
    pembayaran_admisi.pembayaran_id,
    COALESCE(penjamin_admisi.penjamin_id, penjamin_m.penjamin_id) AS penjamin_id,
    pasien_m.pasien_id,
    rencanakontrol_t.rencanakontrol_id,
    rencanakontrol_t.jenis_rencana,
    pendaftaran_t.instalasi_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    laporanoperasi_r.laporanoperasi_id,
    bpjs_admisi.nosep,
    hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
    hasilpemeriksaanrad_t.tindakanpelayanan_id,
    hasilpemeriksaanrad_t.daftartindakan_id,
    permintaankonsul_t.permintaankonsul_id,
    pemeriksaanfisik_t.pemeriksaanfisik_id,
    konsulpoli_t.konsulpoli_id,
    gabungpelayanandetail_t.pendaftaran_id AS ref_pendaftaran_id,
    pendaftaran_t.ruangan_id,
    hasilusg.hasilusg_id
   FROM pendaftaran_t
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pendaftaran_id,
            a.bpjs_id,
            a.penjamin_id,
            a.carabayar_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.rujukanbpjs_id,
            a.pendaftaran_id
           FROM rujukanbpjs_t a) rujukanbpjs_t ON rujukanbpjs_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.bpjs_id,
            a.pendaftaran_id,
            a.pasienadmisi_id,
            a.no_surat_kontrol,
            a.nosep
           FROM bpjs_t a
          WHERE a.is_deleted = false) bpjs_admisi ON bpjs_admisi.bpjs_id = COALESCE(pasienadmisi_t.bpjs_id, pendaftaran_t.bpjs_id)
     LEFT JOIN ( SELECT a.rencanakontrol_id,
            a.pendaftaran_id,
            a.bpjs_id,
            a.nosuratkontrol,
            a.no_spri,
            a.jenis_rencana
           FROM rencanakontrol_t a
          WHERE a.is_deleted = false) rencanakontrol_t ON COALESCE(rencanakontrol_t.no_spri, rencanakontrol_t.nosuratkontrol)::text = bpjs_admisi.no_surat_kontrol::text
     LEFT JOIN ( SELECT pembayaran_t_1.pembayaran_id,
            pembayaran_t_1.pendaftaran_id,
            pembayaran_t_1.is_deleted,
            pembayaran_t_1.pasienadmisi_id
           FROM pembayaran_t pembayaran_t_1
          WHERE pembayaran_t_1.is_deleted = false
          ORDER BY pembayaran_t_1.pendaftaran_id) pembayaran_admisi ON
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pendaftaran_t.pasienadmisi_id = pembayaran_admisi.pasienadmisi_id AND pasienadmisi_t.carabayar_id = 6
            ELSE pendaftaran_t.pendaftaran_id = pembayaran_admisi.pendaftaran_id AND pendaftaran_t.carabayar_id = 6
        END
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama,
            a.carabayar_id
           FROM penjamin_m a
          WHERE a.is_deleted = false) penjamin_m ON penjamin_m.penjamin_id = pendaftaran_t.penjamin_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama,
            a.carabayar_id
           FROM penjamin_m a
          WHERE a.is_deleted = false) penjamin_admisi ON penjamin_admisi.penjamin_id = pasienadmisi_t.penjamin_id
     LEFT JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik
           FROM pasien_m a
          WHERE a.is_deleted = false) pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienmasukpenunjang_id
           FROM pasienmasukpenunjang_t a
          WHERE a.is_deleted = false AND (a.status_periksa::text = ANY (ARRAY['475'::character varying::text, '483'::character varying::text]))) pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.laporanoperasi_id,
            a.pasienmasukpenunjang_id
           FROM laporanoperasi_r a) laporanoperasi_r ON laporanoperasi_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.hasilpemeriksaanrad_id,
            a.tindakanpelayanan_id,
            a.daftartindakan_id,
            a.pasienmasukpenunjang_id
           FROM hasilpemeriksaanrad_t a
          WHERE a.tgl_verifikasi IS NOT NULL) hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.permintaankonsul_id
           FROM permintaankonsul_t a) permintaankonsul_t ON pasienadmisi_t.pasienadmisi_id = permintaankonsul_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.pemeriksaanfisik_id,
            a.pendaftaran_id
           FROM pemeriksaanfisik_t a) pemeriksaanfisik_t ON pendaftaran_t.pendaftaran_id = pemeriksaanfisik_t.pendaftaran_id
     LEFT JOIN ( SELECT a.konsulpoli_id,
            a.pendaftaran_id
           FROM konsulpoli_t a
          WHERE a.is_deleted = false) konsulpoli_t ON pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.ref_pendaftaran_id
           FROM gabungpelayanandetail_t a
          WHERE a.is_deleted = false) gabungpelayanandetail_t ON pendaftaran_t.pendaftaran_id = gabungpelayanandetail_t.ref_pendaftaran_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pendaftaran_id,
            a.hasilusg_id
           FROM hasilusg_t a
          WHERE a.is_deleted IS FALSE) hasilusg ON pendaftaran_t.pendaftaran_id = hasilusg.pendaftaran_id;