-- public.laporancarapulang_v source

CREATE OR REPLACE VIEW public.laporancarapulang_v
AS SELECT 'RJ'::text AS jenis,
    pasienpulang_t.tglpasienpulang AS tgl_pulang,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.nama_pasien,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    carakeluar_m.carakeluar_nama AS cara_pulang,
    kondisikeluar_m.kondisikeluar_nama AS kondisi_pulang,
    carakeluar_m.carakeluar_id AS carapulang_id,
    kondisikeluar_m.kondisikeluar_id AS kondisipulang_id,
    rujukanpulang_t.rujukanpulang_id AS rujukankeluar_id,
    rujukanpulang_t.rujukan_dituju AS rumahsakit_rujukan,
    carabayar_m.carabayar_nama AS cara_bayar,
    carabayar_m.carabayar_id AS carabayar_id
   FROM pendaftaran_t
     LEFT JOIN ( SELECT a.tglpasienpulang,
            a.pasienpulang_id,
            a.carakeluar_id,
            a.kondisikeluar_id,
            a.pasiendirujukkeluar_id
           FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_nama
           FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN ( SELECT a.kondisikeluar_id,
            a.kondisikeluar_nama
           FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN ( SELECT a.rujukanpulang_id,
            a.pendaftaran_id,
            a.rujukan_dituju
           FROM rujukanpulang_t a) rujukanpulang_t ON pendaftaran_t.pendaftaran_id = rujukanpulang_t.pendaftaran_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m  a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
  WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.pasienbatalperiksa_id IS NULL
UNION ALL
 SELECT 'RI-RD'::text AS jenis,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rd.tglpasienpulang
            ELSE pulang_ri.tglpasienpulang
        END AS tgl_pulang,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.nama_pasien,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruangan_rd.ruangan_id
            ELSE ruangan_ri.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruangan_rd.ruangan_nama
            ELSE ruangan_ri.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ins_rd.instalasi_id
            ELSE ins_ri.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ins_rd.instalasi_nama
            ELSE ins_ri.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN keluar_rd.carakeluar_nama
            ELSE keluar_ri.carakeluar_nama
        END AS cara_pulang,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kondisi_rd.kondisikeluar_nama
            ELSE kondisi_ri.kondisikeluar_nama
        END AS kondisi_pulang,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN keluar_rd.carakeluar_id
            ELSE keluar_ri.carakeluar_id
        END AS carapulang_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kondisi_rd.kondisikeluar_id
            ELSE kondisi_ri.kondisikeluar_id
        END AS kondisipulang_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pasiendirujukkeluar_rd.rujukankeluar_id
            ELSE pasiendirujukkeluar_ri.rujukankeluar_id
        END AS rujukankeluar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN rujukankeluar_rd.rujukan_dituju
            ELSE rujukankeluar_ri.rujukan_dituju
        END AS rumahsakit_rujukan,
    carabayar_m.carabayar_nama AS cara_bayar,
    carabayar_m.carabayar_id AS carabayar_id
   FROM pendaftaran_t
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pasienpulang_id,
            a.ruangan_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.tglpasienpulang,
            a.pasienpulang_id,
            a.carakeluar_id,
            a.kondisikeluar_id,
            a.pasiendirujukkeluar_id
           FROM pasienpulang_t a) pulang_rd ON pendaftaran_t.pasienpulang_id = pulang_rd.pasienpulang_id
     LEFT JOIN ( SELECT a.tglpasienpulang,
            a.pasienpulang_id,
            a.carakeluar_id,
            a.kondisikeluar_id,
            a.pasiendirujukkeluar_id
           FROM pasienpulang_t a) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_rd ON pendaftaran_t.ruangan_id = ruangan_rd.ruangan_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_ri ON pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) ins_rd ON pendaftaran_t.instalasi_id = ins_rd.instalasi_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) ins_ri ON ruangan_ri.instalasi_id = ins_ri.instalasi_id
     LEFT JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_nama
           FROM carakeluar_m a) keluar_rd ON pulang_rd.carakeluar_id = keluar_rd.carakeluar_id
     LEFT JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_nama
           FROM carakeluar_m a) keluar_ri ON pulang_ri.carakeluar_id = keluar_ri.carakeluar_id
     LEFT JOIN ( SELECT a.kondisikeluar_id,
            a.kondisikeluar_nama
           FROM kondisikeluar_m a) kondisi_rd ON pulang_rd.kondisikeluar_id = kondisi_rd.kondisikeluar_id
     LEFT JOIN ( SELECT a.kondisikeluar_id,
            a.kondisikeluar_nama
           FROM kondisikeluar_m a) kondisi_ri ON pulang_ri.kondisikeluar_id = kondisi_ri.kondisikeluar_id
     LEFT JOIN ( SELECT a.pasiendirujukkeluar_id,
            a.rujukankeluar_id
           FROM pasiendirujukkeluar_t a) pasiendirujukkeluar_rd ON pulang_rd.pasiendirujukkeluar_id = pasiendirujukkeluar_rd.pasiendirujukkeluar_id
     LEFT JOIN ( SELECT a.pasiendirujukkeluar_id,
            a.rujukankeluar_id
           FROM pasiendirujukkeluar_t a) pasiendirujukkeluar_ri ON pulang_ri.pasiendirujukkeluar_id = pasiendirujukkeluar_ri.pasiendirujukkeluar_id
     LEFT JOIN ( SELECT a.rujukanpulang_id,
            a.pendaftaran_id,
            a.rujukan_dituju
           FROM rujukanpulang_t a) rujukankeluar_rd ON pendaftaran_t.pendaftaran_id = rujukankeluar_rd.pendaftaran_id
     LEFT JOIN ( SELECT a.rujukanpulang_id,
            a.pendaftaran_id,
            a.rujukan_dituju
           FROM rujukanpulang_t a) rujukankeluar_ri ON pendaftaran_t.pendaftaran_id = rujukankeluar_ri.pendaftaran_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m  a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
  WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[2, 3])) AND pendaftaran_t.pasienbatalperiksa_id IS NULL;