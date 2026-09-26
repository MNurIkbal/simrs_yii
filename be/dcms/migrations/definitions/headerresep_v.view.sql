-- public.headerresep_v source

CREATE OR REPLACE VIEW public.headerresep_v
AS SELECT resep.jenis,
    resep.nomor,
    resep.tgl_resep_dibuat,
    resep.status_reseptur_id,
    resep.no_pendaftaran,
    resep.pendaftaran_id,
    resep.no_rekam_medik,
    resep.nama,
    resep.carabayar_nama,
    resep.penjamin_nama,
    resep.pegawai_id,
    resep.nama_pegawai,
    resep.suratizinpraktek,
    resep.penjualanresep_id,
    resep.is_approve,
    resep.pegawai_approve,
    resep.tgl_approve,
    resep.additional_data,
    resep.ruangan_kamar_bed,
    resep.pasien_id,
    resep.nama_pasien,
    resep.nama_pembeli,
    resep.tanggal_lahir,
    resep.umur,
    resep.jenis_kelamin,
    resep.alamat_pasien,
    resep.no_mobile_pasien,
    resep.catatan,
        CASE
            WHEN resep.pasienadmisi_id IS NULL AND resep.instalasi_id_pendaftaran = 1 THEN pemeriksaanfisik_t.bb::text
            WHEN resep.pasienadmisi_id IS NULL AND resep.instalasi_id_pendaftaran = 2 THEN asesmenmedisrd_t.bb::text
            WHEN resep.pasienadmisi_id IS NOT NULL THEN asesmenmedis_t.bb::text
            ELSE '-'::text
        END AS berat_badan,
        CASE
            WHEN resep.pasienadmisi_id IS NULL AND resep.instalasi_id_pendaftaran = 1 THEN pemeriksaanfisik_t.tb::text
            WHEN resep.pasienadmisi_id IS NULL AND resep.instalasi_id_pendaftaran = 2 THEN asesmenmedisrd_t.tb::text
            WHEN resep.pasienadmisi_id IS NOT NULL THEN asesmenmedis_t.tb::text
            ELSE '-'::text
        END AS tinggi_badan,
        CASE
            WHEN anamnesa_t.alergi_obat IS NOT NULL THEN anamnesa_t.alergi_obat
            WHEN asesmenperawatrd_t.alergi_obat IS NOT NULL THEN asesmenperawatrd_t.alergi_obat
            ELSE asesmenawal_t.alergi_obat
        END AS riwayat_alergi,
    resep.biayaadministrasi
   FROM ( SELECT 'reseptur'::text AS jenis,
            reseptur_t.noresep AS nomor,
            COALESCE(penjualanresep_t.tglresep, reseptur_t.tglreseptur) AS tgl_resep_dibuat,
            reseptur_t.status_reseptur AS status_reseptur_id,
            pendaftaran_t.no_pendaftaran,
            reseptur_t.pendaftaran_id,
            reseptur_t.pasienadmisi_id,
            pendaftaran_t.instalasi_id AS instalasi_id_pendaftaran,
            pasien_m.no_rekam_medik,
            concat(namadepan.lookup_name, ' ', pasien_m.nama_pasien) AS nama,
            COALESCE(carabayar_admisi.carabayar_nama, carabayar_m.carabayar_nama) AS carabayar_nama,
            COALESCE(penjamin_admisi.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            reseptur_t.pegawai_id,
            pegawai_m.nama_pegawai,
            pegawai_m.suratizinpraktek,
            penjualanresep_t.penjualanresep_id,
            penjualanresep_t.is_approve,
            pegawai_approve.nama_pegawai AS pegawai_approve,
            penjualanresep_t.tgl_approve,
            penjualanresep_t.additional_data,
            pasien_m.no_mobile_pasien,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.umur,
            jenis_kelamin.lookup_name AS jenis_kelamin,
            NULL::character varying AS nama_pembeli,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN pegawai_m.tgl_lahirpegawai
                    ELSE pasien_m.tanggal_lahir
                END AS tanggal_lahir,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN pegawai_m.alamat_pegawai
                    ELSE pasien_m.alamat_pasien
                END AS alamat_pasien,
                CASE
                    WHEN pendaftaran_t.ruangan_pasien_admisi IS NOT NULL THEN concat(ruangan_admisi.ruangan_nama, '/', kamarruangan_m.kamarruangan_nokamar, '/', kamartempattidur_m.no_tempattidur)::character varying
                    ELSE ruangan_daftar.ruangan_nama
                END AS ruangan_kamar_bed,
                CASE
                    WHEN reseptur_t.catatan IS NOT NULL THEN reseptur_t.catatan
                    ELSE penjualanresep_t.catatan
                END AS catatan,
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN reseptur_t.biaya_administrasi
                    ELSE penjualanresep_t.biayaadministrasi
                END AS biayaadministrasi
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
                    penjualanresep.additional_data,
                    penjualanresep.jenispenjualan,
                    penjualanresep.reseptur_kronis_asal_id,
                    penjualanresep.resep_kronis_asal_id,
                    penjualanresep.nosep
                   FROM penjualanresep_t penjualanresep
                  WHERE penjualanresep.is_deleted = false) penjualanresep_t ON reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN ( SELECT pendaftaran.pendaftaran_id,
                    pendaftaran.pasienadmisi_id,
                    pendaftaran.kelaspelayanan_id,
                    pendaftaran.carabayar_id,
                    pendaftaran.penjamin_id,
                    pendaftaran.umur,
                    pendaftaran.no_pendaftaran,
                    pendaftaran.instalasi_id,
                    pasienadmisi_t.penjamin_id AS penjamin_admisi_id,
                    pasienadmisi_t.bpjs_id AS bpjsadmisi_id,
                    pendaftaran.bpjs_id,
                    pendaftaran.ruangan_id,
                    pasienadmisi_t.ruangan_id AS ruangan_pasien_admisi,
                    pasienadmisi_t.kamarruangan_id AS kamarruangan_pasien_admisi,
                    pasienadmisi_t.kamartempattidur_id AS kamartempattidur_pasien_admisi
                   FROM pendaftaran_t pendaftaran
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.pasienadmisi_id,
                            a.penjamin_id,
                            a.bpjs_id,
                            a.kamarruangan_id,
                            a.kamartempattidur_id,
                            a.ruangan_id
                           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT pasien.pasien_id,
                    pasien.nama_pasien,
                    pasien.no_rekam_medik,
                    pasien.tanggal_lahir,
                    pasien.jeniskelamin,
                    pasien.namadepan,
                    pasien.alamat_pasien,
                    pasien.no_mobile_pasien
                   FROM pasien_m pasien) pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT carabayar.carabayar_id,
                    carabayar.carabayar_nama
                   FROM carabayar_m carabayar) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT penjamin.penjamin_id,
                    penjamin.penjamin_nama
                   FROM penjamin_m penjamin) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT penjamin.penjamin_id,
                    penjamin.penjamin_nama,
                    penjamin.carabayar_id
                   FROM penjamin_m penjamin) penjamin_admisi ON pendaftaran_t.penjamin_admisi_id = penjamin_admisi.penjamin_id
             LEFT JOIN ( SELECT carabayar.carabayar_id,
                    carabayar.carabayar_nama
                   FROM carabayar_m carabayar) carabayar_admisi ON penjamin_admisi.carabayar_id = carabayar_admisi.carabayar_id
             JOIN ( SELECT pegawai.pegawai_id,
                    pegawai.tgl_lahirpegawai,
                    pegawai.nama_pegawai,
                    pegawai.alamat_pegawai,
                    pegawai.suratizinpraktek
                   FROM pegawai_m pegawai) pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_approve ON penjualanresep_t.pegawai_approve_id = pegawai_approve.pegawai_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) namadepan ON pasien_m.namadepan::integer = namadepan.lookup_id
             LEFT JOIN ( SELECT ruangan_daftar_1.ruangan_id,
                    ruangan_daftar_1.ruangan_nama,
                    ruangan_daftar_1.instalasi_id
                   FROM ruangan_m ruangan_daftar_1) ruangan_daftar ON ruangan_daftar.ruangan_id = pendaftaran_t.ruangan_id
             LEFT JOIN ( SELECT ruangan_admisi_1.ruangan_id,
                    ruangan_admisi_1.ruangan_nama,
                    ruangan_admisi_1.instalasi_id
                   FROM ruangan_m ruangan_admisi_1) ruangan_admisi ON ruangan_admisi.ruangan_id = pendaftaran_t.ruangan_pasien_admisi
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pendaftaran_t.kamarruangan_pasien_admisi = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pendaftaran_t.kamartempattidur_pasien_admisi = kamartempattidur_m.kamartempattidur_id
          WHERE reseptur_t.is_deleted = false AND reseptur_t.is_active = true AND reseptur_t.penjualanresep_id IS NULL
        UNION ALL
         SELECT 'resep'::text AS jenis,
            penjualanresep_t.noresep AS nomor,
            penjualanresep_t.tglresep AS tgl_resep_dibuat,
            penjualanresep_t.status_reseptur AS status_reseptur_id,
                CASE
                    WHEN penjualanresep_t.resep_kronis_asal_id IS NULL AND penjualanresep_t.reseptur_kronis_asal_id IS NULL THEN pendaftaran_t.no_pendaftaran
                    ELSE NULL::character varying
                END AS no_pendaftaran,
            COALESCE(penjualanresep_t.pendaftaran_id, penjualanresep_kronis.pendaftaran_id, penjualanreseptur_kronis.pendaftaran_id) AS pendaftaran_id,
            penjualanresep_t.pasienadmisi_id,
            pendaftaran_t.instalasi_id AS instalasi_id_pendaftaran,
            COALESCE(pasien_m.no_rekam_medik, pasien_kornis.no_rekam_medik, pasien_reseptur_kornis.no_rekam_medik) AS no_rekam_medik,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN penjualanresep_t.nama_pembeli
                    WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN concat(namadepan.lookup_name, ' ', COALESCE(pasien_m.nama_pasien, pasien_kornis.nama_pasien, pasien_reseptur_kornis.nama_pasien))::character varying
                    WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat(gelar.lookup_name, ' ', karyawan.nama_pegawai)::character varying
                    ELSE NULL::character varying
                END AS nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            penjualanresep_t.pegawai_id,
            pegawai_m.nama_pegawai,
            pegawai_m.suratizinpraktek,
            penjualanresep_t.penjualanresep_id,
            penjualanresep_t.is_approve,
            pegawai_approve.nama_pegawai AS pegawai_approve,
            penjualanresep_t.tgl_approve,
            penjualanresep_t.additional_data,
            pasien_m.no_mobile_pasien,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.umur,
            COALESCE(jenis_kelamin.lookup_name, pasien_kornis.jenis_kelamin, pasien_reseptur_kornis.jenis_kelamin) AS jenis_kelamin,
            penjualanresep_t.nama_pembeli,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN karyawan.tgl_lahirpegawai
                    ELSE COALESCE(pasien_m.tanggal_lahir, pasien_kornis.tanggal_lahir, pasien_reseptur_kornis.tanggal_lahir)
                END AS tanggal_lahir,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN karyawan.alamat_pegawai
                    ELSE COALESCE(pasien_m.alamat_pasien, pasien_kornis.alamat_pasien, pasien_reseptur_kornis.alamat_pasien)
                END AS alamat_pasien,
                CASE
                    WHEN pasienadmisi_t.ruangan_id IS NOT NULL THEN concat(ruangan_admisi.ruangan_nama, '/', kamarruangan_m.kamarruangan_nokamar, '/', kamartempattidur_m.no_tempattidur)::character varying
                    ELSE ruangan_daftar.ruangan_nama
                END AS ruangan_kamar_bed,
            reseptur_t.catatan,
            penjualanresep_t.biayaadministrasi
           FROM penjualanresep_t
             LEFT JOIN ( SELECT b.pendaftaran_id,
                    a.resep_kronis_asal_id,
                    a.penjualanresep_id,
                    b.pasien_id,
                    c.no_pendaftaran
                   FROM penjualanresep_t a
                     LEFT JOIN penjualanresep_t b ON a.resep_kronis_asal_id = b.penjualanresep_id
                     LEFT JOIN ( SELECT x.pendaftaran_id,
                            x.no_pendaftaran
                           FROM pendaftaran_t x) c ON b.pendaftaran_id = c.pendaftaran_id
                  WHERE a.resep_kronis_asal_id IS NOT NULL) penjualanresep_kronis ON penjualanresep_kronis.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT c.pendaftaran_id,
                    a.reseptur_kronis_asal_id,
                    a.reseptur_id,
                    a.penjualanresep_id,
                    c.pasien_id,
                    a.antrian_id
                   FROM penjualanresep_t a
                     LEFT JOIN reseptur_t c ON a.reseptur_kronis_asal_id = c.reseptur_id
                  WHERE a.reseptur_kronis_asal_id IS NOT NULL) penjualanreseptur_kronis ON penjualanreseptur_kronis.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT pasien.pasien_id,
                    pasien.nama_pasien,
                    pasien.no_rekam_medik,
                    pasien.tanggal_lahir,
                    pasien.jeniskelamin,
                    pasien.namadepan,
                    pasien.alamat_pasien,
                    jenis_kelamin_1.lookup_name AS jenis_kelamin
                   FROM pasien_m pasien
                     LEFT JOIN ( SELECT lookup_m.lookup_id,
                            lookup_m.lookup_name
                           FROM lookup_m) jenis_kelamin_1 ON pasien.jeniskelamin::integer = jenis_kelamin_1.lookup_id) pasien_kornis ON penjualanresep_kronis.pasien_id = pasien_kornis.pasien_id
             LEFT JOIN ( SELECT pasien.pasien_id,
                    pasien.nama_pasien,
                    pasien.no_rekam_medik,
                    pasien.tanggal_lahir,
                    pasien.jeniskelamin,
                    pasien.namadepan,
                    pasien.alamat_pasien,
                    jenis_kelamin_1.lookup_name AS jenis_kelamin
                   FROM pasien_m pasien
                     LEFT JOIN ( SELECT lookup_m.lookup_id,
                            lookup_m.lookup_name
                           FROM lookup_m) jenis_kelamin_1 ON pasien.jeniskelamin::integer = jenis_kelamin_1.lookup_id) pasien_reseptur_kornis ON penjualanreseptur_kronis.pasien_id = pasien_reseptur_kornis.pasien_id
             LEFT JOIN ( SELECT pendaftaran.pendaftaran_id,
                    pendaftaran.pasien_id,
                    pendaftaran.pasienadmisi_id,
                    pendaftaran.instalasi_id,
                    pendaftaran.umur,
                    pendaftaran.no_pendaftaran,
                    pendaftaran.bpjs_id,
                    pendaftaran.ruangan_id
                   FROM pendaftaran_t pendaftaran) pendaftaran_t ON COALESCE(penjualanresep_t.pendaftaran_id, penjualanresep_kronis.pendaftaran_id, penjualanreseptur_kronis.pendaftaran_id) = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.pendaftaran_id,
                    a.bpjs_id,
                    a.kamartempattidur_id,
                    a.kamarruangan_id,
                    a.ruangan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id
             LEFT JOIN ( SELECT pasien.pasien_id,
                    pasien.nama_pasien,
                    pasien.no_rekam_medik,
                    pasien.tanggal_lahir,
                    pasien.jeniskelamin,
                    pasien.namadepan,
                    pasien.alamat_pasien,
                    pasien.no_mobile_pasien
                   FROM pasien_m pasien) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT carabayar.carabayar_id,
                    carabayar.carabayar_nama
                   FROM carabayar_m carabayar) carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT penjamin.penjamin_id,
                    penjamin.penjamin_nama
                   FROM penjamin_m penjamin) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT peg_1.pegawai_id,
                    peg_1.nama_pegawai,
                    peg_1.gelardepan,
                    peg_1.suratizinpraktek
                   FROM pegawai_m peg_1) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT peg_2.pegawai_id,
                    peg_2.nama_pegawai,
                    peg_2.alamat_pegawai,
                    peg_2.tgl_lahirpegawai
                   FROM pegawai_m peg_2) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_approve ON penjualanresep_t.pegawai_approve_id = pegawai_approve.pegawai_id
             LEFT JOIN ( SELECT a.reseptur_id,
                    a.tglreseptur,
                    a.noresep,
                    a.ruanganreseptur_id,
                    a.catatan,
                    a.antrian_id,
                    a.kategori_resep
                   FROM reseptur_t a) reseptur_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) namadepan ON pasien_m.namadepan::integer = namadepan.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) gelar ON pegawai_m.gelardepan::integer = gelar.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) jenispenjualan ON penjualanresep_t.jenispenjualan::integer = gelar.lookup_id
             LEFT JOIN ( SELECT ruangan_daftar_1.ruangan_id,
                    ruangan_daftar_1.ruangan_nama,
                    ruangan_daftar_1.instalasi_id
                   FROM ruangan_m ruangan_daftar_1) ruangan_daftar ON ruangan_daftar.ruangan_id = pendaftaran_t.ruangan_id
             LEFT JOIN ( SELECT ruangan_admisi_1.ruangan_id,
                    ruangan_admisi_1.ruangan_nama,
                    ruangan_admisi_1.instalasi_id
                   FROM ruangan_m ruangan_admisi_1) ruangan_admisi ON ruangan_admisi.ruangan_id = pasienadmisi_t.ruangan_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id) resep
     LEFT JOIN ( SELECT DISTINCT ON (ass_medisrd.pendaftaran_id) ass_medisrd.pendaftaran_id,
            ass_medisrd.tinggi_badan AS tb,
            ass_medisrd.berat_badan AS bb
           FROM asesmenmedisrd_t ass_medisrd
          WHERE ass_medisrd.is_deleted = false) asesmenmedisrd_t ON resep.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (ass_medis.pendaftaran_id) ass_medis.pendaftaran_id,
            ass_medis.tinggi_badan AS tb,
            ass_medis.berat_badan AS bb
           FROM asesmenmedis_t ass_medis
          WHERE ass_medis.is_deleted = false) asesmenmedis_t ON resep.pendaftaran_id = asesmenmedis_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (pemeriksaan_fisik.pendaftaran_id) pemeriksaan_fisik.pendaftaran_id,
            pemeriksaan_fisik.beratbadan_kg AS tb,
            pemeriksaan_fisik.tinggibadan_cm AS bb
           FROM pemeriksaanfisik_t pemeriksaan_fisik
          WHERE pemeriksaan_fisik.is_deleted = false) pemeriksaanfisik_t ON resep.pendaftaran_id = pemeriksaanfisik_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (anamnesa.pendaftaran_id) anamnesa.pendaftaran_id,
            anamnesa.alergi_obat
           FROM anamnesa_t anamnesa) anamnesa_t ON resep.pendaftaran_id = anamnesa_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
            concat(a.additional_data::json ->> 'alergi_obat'::text) AS alergi_obat
           FROM asesmenawal_t a
          WHERE a.is_deleted = false) asesmenawal_t ON resep.pendaftaran_id = asesmenawal_t.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (asesmenperawatrd.pendaftaran_id) asesmenperawatrd.pendaftaran_id,
            asesmenperawatrd.alergi_obat
           FROM asesmenperawatrd_t asesmenperawatrd) asesmenperawatrd_t ON resep.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id;