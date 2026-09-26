-- public.infoorderanrad_v source

CREATE OR REPLACE VIEW public.infoorderanrad_v
AS SELECT infoorderanrad.pasienkirimkeunitlain_id,
    infoorderanrad.pendaftaran_id,
    infoorderanrad.pasienadmisi_id,
    infoorderanrad.no_pendaftaran,
    infoorderanrad.tgl_rujukan,
    infoorderanrad.no_rujukan,
    infoorderanrad.pasien_id,
    infoorderanrad.no_rekam_medik,
    infoorderanrad.nama_pasien,
    infoorderanrad.umur,
    infoorderanrad.jenis_kelamin,
    infoorderanrad.kelaspelayanan_nama,
    infoorderanrad.instalasi_id,
    infoorderanrad.instalasi_nama,
    infoorderanrad.ruangan_nama,
    infoorderanrad.kamarruangan_nokamar,
    infoorderanrad.no_tempattidur,
    infoorderanrad.pegawai_id,
    infoorderanrad.dokter_perujuk,
    infoorderanrad.carabayar_id,
    infoorderanrad.carabayar_nama,
    infoorderanrad.penjamin_id,
    infoorderanrad.penjamin_nama,
    infoorderanrad.status_penunjang,
    look_status_penunjang.status_penunjang AS stat_penunjang,
    infoorderanrad.kelaspelayanan_id,
    infoorderanrad.jeniskasuspenyakit_id,
    infoorderanrad.ruangan_id,
    infoorderanrad.tgl_pendaftaran,
    infoorderanrad.kunjungan,
    infoorderanrad.ruanganpenunjang_id,
    infoorderanrad.tanggal_lahir,
    infoorderanrad.status_pasien,
    infoorderanrad.groupcarabayar_id,
    infoorderanrad.instalasipen_id,
    infoorderanrad.is_bayar,
    infoorderanrad.status_periksa,
    infoorderanrad.alamat_pasien,
    infoorderanrad.kode_pos,
    infoorderanrad.no_telepon_pasien,
    infoorderanrad.kode_ruangan,
    infoorderanrad.unit_asal,
        CASE
            WHEN (( SELECT rsmt.diag_utama
               FROM resumemedisri_t rsmt
              WHERE infoorderanrad.pendaftaran_id = rsmt.pendaftaran_id AND rsmt.diag_utama IS NOT NULL
             LIMIT 1)) IS NOT NULL THEN ( SELECT rsmt.diag_utama
               FROM resumemedisri_t rsmt
              WHERE infoorderanrad.pendaftaran_id = rsmt.pendaftaran_id AND rsmt.diag_utama IS NOT NULL
             LIMIT 1)
            ELSE (( SELECT cpt.a_diag_utama AS diagnosa_utama
               FROM cppt_t cpt
              WHERE cpt.pendaftaran_id = infoorderanrad.pendaftaran_id AND cpt.a_diag_utama IS NOT NULL AND cpt.is_deleted IS FALSE AND cpt.additional_data = '{"via_soap":true}'::text
              ORDER BY cpt.cppt_id DESC
             LIMIT 1)
            UNION ALL
            ( SELECT pmb.diagnosa_pasien AS diagnosa_utama
               FROM pasienmorbiditas_t pmb
              WHERE pmb.pendaftaran_id = infoorderanrad.pendaftaran_id AND pmb.is_deleted = false AND pmb.kelompokdiagnosa_id = 2 AND pmb.diagnosa_pasien IS NOT NULL
             LIMIT 1))
        END AS nama_diagnosa,
    infoorderanrad.nama_pemeriksaan,
    infoorderanrad.carabayar_kode,
    infoorderanrad.catatan_dokterpengirim,
    NULL::text AS no_pembayaran,
    infoorderanrad.nomorindukpegawai,
    infoorderanrad.jenis_kelamin_id,
        CASE
            WHEN (EXISTS ( SELECT 1
               FROM tindakanpelayanan_t
              WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted IS FALSE AND tindakanpelayanan_t.pasienmasukpenunjang_id = infoorderanrad.pasienmasukpenunjang_id
             LIMIT 1)) THEN 'Sudah Bayar'::text
            ELSE
            CASE
                WHEN (EXISTS ( SELECT 1
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.is_deleted IS FALSE AND tindakanpelayanan_t.pasienmasukpenunjang_id = infoorderanrad.pasienmasukpenunjang_id
                 LIMIT 1)) THEN 'Belum Bayar'::text
                ELSE 'Belum Bayar'::text
            END
        END AS status_bayar,
    infoorderanrad.is_rujukan,
    infoorderanrad.jml_pemeriksaan,
    infoorderanrad.cyto_tindakan,
    infoorderanrad.jenis_kelamin_kode,
    infoorderanrad.asalrujukan_id,
    infoorderanrad.rujukandari_id,
    infoorderanrad.asalrujukan_nama,
    infoorderanrad.rujukandari_nama,
    infoorderanrad.carabayar_kode_warna,
    infoorderanrad.is_referred,
    bpjs_t.no_sep,
    infoorderanrad.ruanganasal_id,
    infoorderanrad.ruanganasal_nama,
    infoorderanrad.instalasiasal_id
   FROM ( SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.pendaftaran_id,
            pasienkirimkeunitlain_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.umur,
            look_jeniskelamin.jeniskelamin AS jenis_kelamin,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::character varying AS no_tempattidur,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_perujuk,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienkirimkeunitlain_t.status_penunjang,
            pendaftaran_t.kelaspelayanan_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.kunjungan,
            pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
            pasien_m.tanggal_lahir,
            pendaftaran_t.status_pasien,
            carabayar_m.groupcarabayar_id,
            pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienmasukpenunjang_t.status_periksa,
            pasien_m.alamat_pasien,
            NULL::character varying AS kode_pos,
            pasien_m.no_telepon_pasien,
            ruangan_m.ruangan_singkatan AS kode_ruangan,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN 'Pendaftaran'::text
                    ELSE 'Unit'::text
                END AS unit_asal,
            pemeriksaan.nama_pemeriksaan,
            penjamin_m.penjamin_kode AS carabayar_kode,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pegawai_m.nomorindukpegawai,
            pasien_m.jeniskelamin AS jenis_kelamin_id,
            pasienkirimkeunitlain_t.is_rujukan,
            COALESCE(total_pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
            COALESCE(cyto_tindakan.is_cyto, false) AS cyto_tindakan,
            look_jeniskelamin.jeniskelamin_kode AS jenis_kelamin_kode,
            rujukan_t.asalrujukan_id,
            rujukan_t.rujukandari_id,
                CASE
                    WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN coalesce(ruanganasal.ruangan_nama, ruangan_m.ruangan_nama)
                    ELSE asalrujukan_m.asalrujukan_nama
                END AS asalrujukan_nama,
            perujuk_m.namaperujuk AS rujukandari_nama,
            carabayar_m.carabayar_kode_warna,
                CASE
                    WHEN permintaankepenunjang_t.qty_dirujuk > 0 THEN true
                    ELSE false
                END AS is_referred,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
		    COALESCE(ruanganasal.ruangan_id, ruangan_m.ruangan_id) AS ruanganasal_id,
		    COALESCE(ruanganasal.ruangan_nama, ruangan_m.ruangan_nama) AS ruanganasal_nama,
		    COALESCE(ruanganasal.instalasi_id, pendaftaran_t.instalasi_id) AS instalasiasal_id
           FROM pasienkirimkeunitlain_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasien_id,
                    a.instalasi_id,
                    a.ruangan_id,
                    a.pegawai_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.rujukan_id,
                    a.no_pendaftaran,
                    a.umur,
                    a.jeniskasuspenyakit_id,
                    a.tgl_pendaftaran,
                    a.kunjungan,
                    a.status_pasien
                   FROM pendaftaran_t a) pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.alamat_pasien,
                    a.no_telepon_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.ruangan_singkatan,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.nomorindukpegawai
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.penjamin_kode
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                    a.is_bayar,
                    a.status_periksa,
                    a.pasienkirimkeunitlain_id
                   FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.rujukandari_id
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.perujuk_id,
                    a.namaperujuk
                   FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             LEFT JOIN ( SELECT permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
                    string_agg(DISTINCT daftartindakan_m.daftartindakan_nama::text ||
                        CASE
                            WHEN permintaankepenunjang_t_1.is_referred IS TRUE THEN ' (Dirujuk)'::text
                            ELSE ''::text
                        END, ', '::text) AS nama_pemeriksaan
                   FROM permintaankepenunjang_t permintaankepenunjang_t_1
                     JOIN ( SELECT a.daftartindakan_id,
                            a.daftartindakan_nama
                           FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  WHERE permintaankepenunjang_t_1.is_deleted IS FALSE
                  GROUP BY permintaankepenunjang_t_1.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
                    count(*) AS jml_pemeriksaan
                   FROM permintaankepenunjang_t permintaankepenunjang_t_1
                  WHERE permintaankepenunjang_t_1.is_deleted IS FALSE
                  GROUP BY permintaankepenunjang_t_1.pasienkirimkeunitlain_id) total_pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = total_pemeriksaan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t_1.pasienkirimkeunitlain_id, permintaankepenunjang_t_1.is_cyto) permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
                    permintaankepenunjang_t_1.is_cyto
                   FROM permintaankepenunjang_t permintaankepenunjang_t_1
                  WHERE permintaankepenunjang_t_1.is_cyto = true) cyto_tindakan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    count(*) AS qty_dirujuk
                   FROM permintaankepenunjang_t a
                  WHERE a.is_referred IS TRUE
                  GROUP BY a.pasienkirimkeunitlain_id) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS jeniskelamin,
                    a.lookup_kode AS jeniskelamin_kode
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::text = look_jeniskelamin.jeniskelamin::text
            LEFT JOIN ( SELECT a.ruangan_id,
	        	    a.ruangan_nama,
    		        a.instalasi_id
          		 FROM ruangan_m a) ruanganasal ON ruanganasal.ruangan_id = pasienkirimkeunitlain_t.ruangan_asal
          WHERE pasienkirimkeunitlain_t.instalasi_id = 5
        UNION ALL
         SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
            pendaftaran_t.pendaftaran_id,
            pasienkirimkeunitlain_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.umur,
            look_jeniskelamin.jeniskelamin AS jenis_kelamin,
            kelaspelayanan_m.kelaspelayanan_nama,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            pasienadmisi_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_perujuk,
            penjamin_m.carabayar_id,
            carabayar_m.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienadmisi_t.kelaspelayanan_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pasienadmisi_t.ruangan_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.kunjungan,
            pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
            pasien_m.tanggal_lahir,
            pendaftaran_t.status_pasien,
            carabayar_m.groupcarabayar_id,
            pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienmasukpenunjang_t.status_periksa,
            pasien_m.alamat_pasien,
            NULL::character varying AS kode_pos,
            pasien_m.no_telepon_pasien,
            ruangan_m.ruangan_singkatan AS kode_ruangan,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN 'Pendaftaran'::text
                    ELSE 'Unit'::text
                END AS unit_asal,
            pemeriksaan.nama_pemeriksaan,
            penjamin_m.penjamin_kode AS carabayar_kode,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pegawai_m.nomorindukpegawai,
            pasien_m.jeniskelamin AS jenis_kelamin_id,
            pasienkirimkeunitlain_t.is_rujukan,
            COALESCE(total_pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
            COALESCE(cyto_tindakan.is_cyto, false) AS cyto_tindakan,
            look_jeniskelamin.jeniskelamin_kode AS jenis_kelamin_kode,
            rujukan_t.asalrujukan_id,
            rujukan_t.rujukandari_id,
                CASE
                    WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN coalesce(ruanganasal.ruangan_nama, ruangan_m.ruangan_nama)
                    ELSE asalrujukan_m.asalrujukan_nama
                END AS asalrujukan_nama,
            perujuk_m.namaperujuk AS rujukandari_nama,
            carabayar_m.carabayar_kode_warna,
                CASE
                    WHEN permintaankepenunjang_t.qty_dirujuk > 0 THEN true
                    ELSE false
                END AS is_referred,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
		    COALESCE(ruanganasal.ruangan_id, ruangan_m.ruangan_id) AS ruanganasal_id,
		    COALESCE(ruanganasal.ruangan_nama, ruangan_m.ruangan_nama) AS ruanganasal_nama,
		    COALESCE(ruanganasal.instalasi_id, ruangan_m.instalasi_id) AS instalasiasal_id
           FROM pasienkirimkeunitlain_t
             JOIN ( SELECT a.pasienadmisi_id,
                    a.pasien_id,
                    a.ruangan_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.pegawai_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.rujukan_id,
                    a.no_pendaftaran,
                    a.pasien_id,
                    a.umur,
                    a.jeniskasuspenyakit_id,
                    a.tgl_pendaftaran,
                    a.kunjungan,
                    a.status_pasien
                   FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.alamat_pasien,
                    a.no_telepon_pasien
                   FROM pasien_m a) pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id,
                    a.ruangan_singkatan
                   FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.nomorindukpegawai
                   FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.penjamin_id,
                    a.carabayar_id,
                    a.penjamin_nama,
                    a.penjamin_kode
                   FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                    a.is_bayar,
                    a.status_periksa,
                    a.pasienkirimkeunitlain_id
                   FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.rujukandari_id
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.perujuk_id,
                    a.namaperujuk
                   FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             LEFT JOIN ( SELECT permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
                    string_agg(DISTINCT daftartindakan_m.daftartindakan_nama::text ||
                        CASE
                            WHEN permintaankepenunjang_t_1.is_referred IS TRUE THEN ' (Dirujuk)'::text
                            ELSE ''::text
                        END, ', '::text) AS nama_pemeriksaan
                   FROM permintaankepenunjang_t permintaankepenunjang_t_1
                     JOIN ( SELECT a.daftartindakan_id,
                            a.daftartindakan_nama
                           FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  GROUP BY permintaankepenunjang_t_1.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
                    count(*) AS jml_pemeriksaan
                   FROM permintaankepenunjang_t permintaankepenunjang_t_1
                  WHERE permintaankepenunjang_t_1.is_deleted IS FALSE
                  GROUP BY permintaankepenunjang_t_1.pasienkirimkeunitlain_id) total_pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = total_pemeriksaan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t_1.pasienkirimkeunitlain_id, permintaankepenunjang_t_1.is_cyto) permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
                    permintaankepenunjang_t_1.is_cyto
                   FROM permintaankepenunjang_t permintaankepenunjang_t_1
                  WHERE permintaankepenunjang_t_1.is_cyto = true) cyto_tindakan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    count(*) AS qty_dirujuk
                   FROM permintaankepenunjang_t a
                  WHERE a.is_referred IS TRUE
                  GROUP BY a.pasienkirimkeunitlain_id) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS jeniskelamin,
                    a.lookup_kode AS jeniskelamin_kode
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::text = look_jeniskelamin.jeniskelamin::text
             LEFT JOIN ( SELECT a.ruangan_id,
	        	    a.ruangan_nama,
    		        a.instalasi_id
          		 FROM ruangan_m a) ruanganasal ON ruanganasal.ruangan_id = pasienkirimkeunitlain_t.ruangan_asal
          WHERE pasienkirimkeunitlain_t.instalasi_id = 5) infoorderanrad
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_penunjang
           FROM lookup_m a) look_status_penunjang ON infoorderanrad.status_penunjang::integer = look_status_penunjang.lookup_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            string_agg(a.nosep::text, '##'::text) AS no_sep,
            string_agg(a.norujukan::text, '##'::text) AS norujukan
           FROM bpjs_t a
          WHERE a.pendaftaran_id IS NOT NULL AND a.is_deleted = false
          GROUP BY a.pendaftaran_id) bpjs_t ON infoorderanrad.pendaftaran_id = bpjs_t.pendaftaran_id;