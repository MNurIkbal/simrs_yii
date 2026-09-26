-- public.pasienpulangmeninggal source

CREATE OR REPLACE VIEW public.pasienpulangmeninggal_v
AS SELECT pasienmeninggal.pendaftaran_id,
    pasienmeninggal.pasien_id,
    pasienmeninggal.no_rekam_medik,
    pasienmeninggal.nama_pasien,
    pasienmeninggal.no_pendaftaran,
    pasienmeninggal.tgl_pendaftaran,
    pasienmeninggal.tglpasienpulang,
    pasienmeninggal.carakeluar_id,
    pasienmeninggal.is_deleted,
    pasienmeninggal.is_active
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            pendaftaran_t.is_deleted,
            pendaftaran_t.is_active
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang,
                    a.carakeluar_id
                   FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pendaftaran_t.pasienadmisi_id IS NULL AND pasienpulang_t.carakeluar_id = 4
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            pendaftaran_t.is_deleted,
            pendaftaran_t.is_active
           FROM pasienadmisi_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.tgl_pendaftaran,
                    a.pasienpulang_id,
                    a.pasien_id,
                    a.is_deleted,
                    a.is_active,
                    a.pasienadmisi_id
                   FROM pendaftaran_t a) pendaftaran_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang,
                    a.carakeluar_id
                   FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pendaftaran_t.pasienadmisi_id IS NOT NULL AND pasienpulang_t.carakeluar_id = 4) pasienmeninggal;