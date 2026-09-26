-- public.prescribe_v source

CREATE OR REPLACE VIEW public.prescribe_v
AS  SELECT resep.jenis,
    resep.reseptur_id,
    resep.resep_id,
    resep.pasien_id,
    resep.pendaftaran_id,
    resep.pasienadmisi_id,
    resep.carabayar_id,
    resep.penjamin_id,
    resep.nosep,
    pendaftaran_t.umur,
    kelaspelayanan_m.kelaspelayanan_nama,
    resep.ruangan_id,
    resep.ruanganreseptur_id,
    resep.tglreseptur,
    resep.tglresep,
    resep.no_reseptur,
    resep.no_resep,
    resep.nomor,
    resep.penjualanresep_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jenis_kelamin.lookup_name AS jenis_kelamin,
        CASE
            WHEN resep.jenispenjualan_id::text = '345'::text THEN karyawan.tgl_lahirpegawai
            ELSE pasien_m.tanggal_lahir
        END AS tanggal_lahir,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_resep.ruangan_nama AS ruangan_tujuan,
    rm.ruangan_nama AS ruangan_reseptur,
    resep.status_reseptur,
    resep.pegawai_id,
    pegawai_m.nama_pegawai,
    rm.instalasi_id AS instalasi_reseptur_id,
    im.instalasi_nama AS instalasi_reseptur,
    ruangan_resep.instalasi_id AS instalasi_resep_id,
    instalasi_resep.instalasi_nama AS instalasi_resep,
    resep.no_antrian,
    resep.status_reseptur_id,
    resep.is_hamil,
    resep.luas_tubuh,
    resep.diagnosa_id,
    resep.diagnosa_nama,
    resep.instruksi_id,
    resep.antrian_id,
    resep.catatan,
    resep.noresep_penjualan,
    resep.iter_penjualan,
    resep.antrian_racikan,
    resep.iter,
    resep.total_harganetto,
    resep.biayaadministrasi,
    resep.totalhargajual,
    resep.totalhargajual + resep.biayaadministrasi AS totaltagihan,
    resep.nama_pembeli,
    resep.status_bayar,
        CASE
            WHEN resep.tglreseptur IS NOT NULL THEN resep.tglreseptur
            ELSE resep.tglresep
        END AS tgl_resep_dibuat,
        CASE
            WHEN resep.jenispenjualan_id::text = '343'::text THEN resep.nama_pembeli
            WHEN resep.jenispenjualan_id::text = '344'::text THEN concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien)::character varying
            WHEN resep.jenispenjualan_id::text = '345'::text THEN concat(fgetnamalookup(pegawai_m.gelardepan::integer), ' ', karyawan.nama_pegawai)::character varying
            ELSE NULL::character varying
        END AS nama,
    resep.jenispenjualan_id,
    jenispenjualan.lookup_name AS jenispenjualan_nama,
    resep.status_worklist,
        CASE
            WHEN resep.jenispenjualan_id::text = '343'::text THEN NULL::character varying
            WHEN resep.jenispenjualan_id::text = '344'::text THEN fgetnamalookup(pasien_m.namadepan::integer)
            WHEN resep.jenispenjualan_id::text = '345'::text THEN fgetnamalookup(pegawai_m.gelardepan::integer)
            ELSE NULL::character varying
        END AS nama_depan,
    resep.kelaspelayanan_id,
    resep.is_approve,
    pegawai_approve.nama_pegawai AS pegawai_approve,
    resep.tgl_approve,
    resep.additional_data,
        CASE
            WHEN resep.jenispenjualan_id::text = '345'::text THEN karyawan.alamat_pegawai
            ELSE pasien_m.alamat_pasien
        END AS alamat_pasien,
    bpjs_t.nosep AS nosep_bpjs,
    resep.status_etiket_reseptur,
    resep.is_cetak_etiket,
    resep.status_etiket_jual_resep,
        CASE
            WHEN pasienadmisi_t.ruangan_id IS NOT NULL THEN concat(ruangan_admisi.ruangan_nama, '/', kamarruangan_m.kamarruangan_nokamar, '/', kamartempattidur_m.no_tempattidur)::character varying
            ELSE ruangan_daftar.ruangan_nama
        END AS ruangan_kamar_bed,
    resep.kategori_resep,
    kategori_resep_lookup.lookup_name::text AS kategori_resep_nama,
    kategori_resep_lookup.lookup_kode::text AS kategori_resep_kode,
    pendaftaran_t.instalasi_id AS instalasi_id_pendaftaran,
    resep.countkronis,
    resep.countretur,
    resep.countracikan,
    resep.countracikan_freetext,
        CASE
            WHEN COALESCE(resep.countkronis, 0::bigint) >= 1 THEN true
            ELSE false
        END AS is_kronis,
        CASE
            WHEN COALESCE(resep.countretur, 0::bigint) >= 1 THEN true
            ELSE false
        END AS is_retur,
        CASE
            WHEN COALESCE(resep.countracikan_freetext, 0::bigint) >= 1 THEN 1
            WHEN COALESCE(resep.countracikan, 0::bigint) >= 1 THEN 1
            ELSE 2
        END AS racikan_id,
        CASE
            WHEN COALESCE(resep.countracikan_freetext, 0::bigint) >= 1 THEN 'Racikan'::text
            WHEN COALESCE(resep.countracikan, 0::bigint) >= 1 THEN 'Racikan'::text
            ELSE 'Non Racikan'::text
        END AS status_racikan
   FROM ( SELECT 'resep1'::text AS jenis,
            reseptur_t.reseptur_id,
            penjualanresep_t.penjualanresep_id AS resep_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.pendaftaran_id,
            penjualanresep_t.pasienadmisi_id,
            penjualanresep_t.carabayar_id,
            penjualanresep_t.penjamin_id,
            penjualanresep_t.nosep,
            penjualanresep_t.ruangan_id,
            reseptur_t.ruanganreseptur_id,
            reseptur_t.tglreseptur,
            penjualanresep_t.tglresep,
            reseptur_t.noresep AS no_reseptur,
            penjualanresep_t.noresep AS no_resep,
            penjualanresep_t.noresep AS nomor,
            penjualanresep_t.penjualanresep_id,
                CASE
                    WHEN penjualanresep_t.status_reseptur = 347 THEN 'Dalam Proses'::character varying
                    ELSE fgetnamalookup(penjualanresep_t.status_reseptur::integer)
                END AS status_reseptur,
            penjualanresep_t.pegawai_id,
            antrian_t.no_antrian,
            penjualanresep_t.status_reseptur AS status_reseptur_id,
            NULL::boolean AS is_hamil,
            NULL::character varying AS luas_tubuh,
            NULL::integer AS diagnosa_id,
            NULL::text AS diagnosa_nama,
            NULL::integer AS instruksi_id,
            antrian_t.antrian_id,
            reseptur_t.catatan,
            penjualanresep_t.noresep AS noresep_penjualan,
            NULL::integer AS iter_penjualan,
            NULL::text AS antrian_racikan,
            penjualanresep_t.iter,
            NULL::double precision AS total_harganetto,
            COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS biayaadministrasi,
            ( SELECT sum(a.hargajual_oa) AS hargajual_oa
                   FROM obatalkespasien_t a
                  WHERE a.penjualanresep_id = penjualanresep_t.penjualanresep_id AND a.is_deleted = false
                  GROUP BY a.penjualanresep_id) AS totalhargajual,
            NULL::double precision AS totaltagihan,
            penjualanresep_t.nama_pembeli,
            penjualanresep_t.status_bayar,
            penjualanresep_t.tglresep AS tgl_resep_dibuat,
            penjualanresep_t.jenispenjualan AS jenispenjualan_id,
            penjualanresep_t.status_worklist,
            penjualanresep_t.kelaspelayanan_id,
            penjualanresep_t.is_approve,
            penjualanresep_t.tgl_approve,
            penjualanresep_t.pegawai_approve_id,
            penjualanresep_t.additional_data,
            NULL::text AS status_etiket_reseptur,
            penjualanresep_t.is_cetak_etiket,
            penjualanresep_t.is_cetak_etiket::text AS status_etiket_jual_resep,
            reseptur_t.kategori_resep,
            ( SELECT DISTINCT ON (a.penjualanresep_id) count(a.is_kronis) FILTER (WHERE a.is_kronis IS TRUE) AS count_obat_racikan
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false AND a.penjualanresep_id = penjualanresep_t.penjualanresep_id
                  GROUP BY a.penjualanresep_id) AS countkronis,
            ( SELECT DISTINCT ON (a.penjualanresep_id) count(a.is_retur) FILTER (WHERE a.is_retur IS TRUE) AS count_retur
                   FROM obatalkespasien_t a
                  WHERE a.penjualanresep_id IS NOT NULL AND a.penjualanresep_id = penjualanresep_t.penjualanresep_id
                  GROUP BY a.penjualanresep_id) AS countretur,
            ( SELECT DISTINCT ON (a.penjualanresep_id) count(a.racikan_id) FILTER (WHERE a.racikan_id = 1) AS count_obat_racikan
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false AND a.penjualanresep_id = penjualanresep_t.penjualanresep_id
                  GROUP BY a.penjualanresep_id) AS countracikan,
            ( SELECT DISTINCT ON (rt2.reseptur_id) count(rt2.resepturracikan_id) FILTER (WHERE rt2.type::text = 'OR'::text) AS count_obat_racikan_freetext
                   FROM resepturracikan_t rt2
                  WHERE rt2.reseptur_id = penjualanresep_t.reseptur_id
                  GROUP BY rt2.reseptur_id) AS countracikan_freetext,
            penjualanresep_t.karyawan_id
           FROM penjualanresep_t
             LEFT JOIN reseptur_t ON penjualanresep_t.reseptur_id = reseptur_t.reseptur_id
             LEFT JOIN ( SELECT antrian.antrian_id,
                    antrian.no_antrian
                   FROM antrian_t antrian
                  ORDER BY antrian.antrian_id) antrian_t ON COALESCE(penjualanresep_t.antrian_id, reseptur_t.antrian_id) = antrian_t.antrian_id
          WHERE penjualanresep_t.reseptur_id IS NOT NULL AND penjualanresep_t.reseptur_kronis_asal_id IS NULL AND penjualanresep_t.resep_kronis_asal_id IS NULL
        UNION ALL
         SELECT 'resep'::text AS jenis,
            NULL::integer AS reseptur_id,
            penjualanresep_t.penjualanresep_id AS resep_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.pendaftaran_id,
            penjualanresep_t.pasienadmisi_id,
            penjualanresep_t.carabayar_id,
            penjualanresep_t.penjamin_id,
            penjualanresep_t.nosep,
            penjualanresep_t.ruangan_id,
            NULL::integer AS ruanganreseptur_id,
            NULL::timestamp without time zone AS tglreseptur,
            penjualanresep_t.tglresep,
            NULL::character varying AS no_reseptur,
            penjualanresep_t.noresep AS no_resep,
            penjualanresep_t.noresep AS nomor,
            penjualanresep_t.penjualanresep_id,
                CASE
                    WHEN penjualanresep_t.status_reseptur = 347 THEN 'Dalam Proses'::character varying
                    ELSE fgetnamalookup(penjualanresep_t.status_reseptur::integer)
                END AS status_reseptur,
            penjualanresep_t.pegawai_id,
            antrian_t.no_antrian,
            penjualanresep_t.status_reseptur AS status_reseptur_id,
            NULL::boolean AS is_hamil,
            NULL::character varying AS luas_tubuh,
            NULL::integer AS diagnosa_id,
            NULL::text AS diagnosa_nama,
            NULL::integer AS instruksi_id,
            antrian_t.antrian_id,
            penjualanresep_t.catatan,
            penjualanresep_t.noresep AS noresep_penjualan,
            NULL::integer AS iter_penjualan,
            NULL::text AS antrian_racikan,
            penjualanresep_t.iter,
            NULL::double precision AS total_harganetto,
            COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS biayaadministrasi,
            ( SELECT sum(a.hargajual_oa) AS hargajual_oa
                   FROM obatalkespasien_t a
                  WHERE a.penjualanresep_id = penjualanresep_t.penjualanresep_id AND a.is_deleted = false
                  GROUP BY a.penjualanresep_id) AS totalhargajual,
            NULL::double precision AS totaltagihan,
            penjualanresep_t.nama_pembeli,
            penjualanresep_t.status_bayar,
            penjualanresep_t.tglresep AS tgl_resep_dibuat,
            penjualanresep_t.jenispenjualan AS jenispenjualan_id,
            penjualanresep_t.status_worklist,
            penjualanresep_t.kelaspelayanan_id,
            penjualanresep_t.is_approve,
            penjualanresep_t.tgl_approve,
            penjualanresep_t.pegawai_approve_id,
            penjualanresep_t.additional_data,
            NULL::text AS status_etiket_reseptur,
            penjualanresep_t.is_cetak_etiket,
            penjualanresep_t.is_cetak_etiket::text AS status_etiket_jual_resep,
            NULL::integer AS kategori_resep,
            ( SELECT DISTINCT ON (a.penjualanresep_id) count(a.is_kronis) FILTER (WHERE a.is_kronis IS TRUE) AS count_obat_racikan
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false AND a.penjualanresep_id = penjualanresep_t.penjualanresep_id
                  GROUP BY a.penjualanresep_id) AS countkronis,
            ( SELECT DISTINCT ON (a.penjualanresep_id) count(a.is_retur) FILTER (WHERE a.is_retur IS TRUE) AS count_retur
                   FROM obatalkespasien_t a
                  WHERE a.penjualanresep_id IS NOT NULL AND a.penjualanresep_id = penjualanresep_t.penjualanresep_id
                  GROUP BY a.penjualanresep_id) AS countretur,
            ( SELECT DISTINCT ON (a.penjualanresep_id) count(a.racikan_id) FILTER (WHERE a.racikan_id = 1) AS count_obat_racikan
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false AND a.penjualanresep_id = penjualanresep_t.penjualanresep_id
                  GROUP BY a.penjualanresep_id) AS countracikan,
            0 AS countracikan_freetext,
            penjualanresep_t.karyawan_id
           FROM penjualanresep_t
             LEFT JOIN ( SELECT antrian.antrian_id,
                    antrian.no_antrian
                   FROM antrian_t antrian
                  ORDER BY antrian.antrian_id) antrian_t ON penjualanresep_t.antrian_id = antrian_t.antrian_id
          WHERE penjualanresep_t.reseptur_id IS NOT NULL AND (penjualanresep_t.resep_kronis_asal_id IS NOT NULL OR penjualanresep_t.reseptur_kronis_asal_id IS NOT NULL)
        UNION ALL
         SELECT 'resep'::text AS jenis,
            NULL::integer AS reseptur_id,
            penjualanresep_t.penjualanresep_id AS resep_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.pendaftaran_id,
            penjualanresep_t.pasienadmisi_id,
            penjualanresep_t.carabayar_id,
            penjualanresep_t.penjamin_id,
            penjualanresep_t.nosep,
            penjualanresep_t.ruangan_id,
            NULL::integer AS ruanganreseptur_id,
            NULL::timestamp without time zone AS tglreseptur,
            penjualanresep_t.tglresep AS tglresep,
            NULL::character varying AS no_reseptur,
            penjualanresep_t.noresep AS no_resep,
            penjualanresep_t.noresep AS nomor,
            penjualanresep_t.penjualanresep_id,
                CASE
                    WHEN penjualanresep_t.status_reseptur = 347 THEN 'Dalam Proses'::character varying
                    ELSE fgetnamalookup(penjualanresep_t.status_reseptur::integer)
                END AS status_reseptur,
            penjualanresep_t.pegawai_id,
            antrian_t.no_antrian,
            penjualanresep_t.status_reseptur AS status_reseptur_id,
            NULL::boolean AS is_hamil,
            NULL::character varying AS luas_tubuh,
            NULL::integer AS diagnosa_id,
            NULL::text AS diagnosa_nama,
            NULL::integer AS instruksi_id,
            antrian_t.antrian_id,
            NULL::text AS catatan,
            penjualanresep_t.noresep AS noresep_penjualan,
            NULL::integer AS iter_penjualan,
            NULL::text AS antrian_racikan,
            penjualanresep_t.iter,
            NULL::double precision AS total_harganetto,
            COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS biayaadministrasi,
            ( SELECT sum(a.hargajual_oa) AS hargajual_oa
                   FROM obatalkespasien_t a
                  WHERE a.penjualanresep_id = penjualanresep_t.penjualanresep_id AND a.is_deleted = false
                  GROUP BY a.penjualanresep_id) AS totalhargajual,
            NULL::double precision AS totaltagihan,
            penjualanresep_t.nama_pembeli,
            penjualanresep_t.status_bayar,
            penjualanresep_t.tglresep AS tgl_resep_dibuat,
            penjualanresep_t.jenispenjualan AS jenispenjualan_id,
            penjualanresep_t.status_worklist,
            penjualanresep_t.kelaspelayanan_id,
            penjualanresep_t.is_approve,
            penjualanresep_t.tgl_approve,
            penjualanresep_t.pegawai_approve_id,
            penjualanresep_t.additional_data,
            NULL::text AS status_etiket_reseptur,
            penjualanresep_t.is_cetak_etiket,
            penjualanresep_t.is_cetak_etiket::text AS status_etiket_jual_resep,
            NULL::integer AS kategori_resep,
            ( SELECT DISTINCT ON (a.penjualanresep_id) count(a.is_kronis) FILTER (WHERE a.is_kronis IS TRUE) AS count_obat_racikan
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false AND a.penjualanresep_id = penjualanresep_t.penjualanresep_id
                  GROUP BY a.penjualanresep_id) AS countkronis,
            ( SELECT DISTINCT ON (a.penjualanresep_id) count(a.is_retur) FILTER (WHERE a.is_retur IS TRUE) AS count_retur
                   FROM obatalkespasien_t a
                  WHERE a.penjualanresep_id IS NOT NULL AND a.penjualanresep_id = penjualanresep_t.penjualanresep_id
                  GROUP BY a.penjualanresep_id) AS countretur,
            ( SELECT DISTINCT ON (a.penjualanresep_id) count(a.racikan_id) FILTER (WHERE a.racikan_id = 1) AS count_obat_racikan
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false AND a.penjualanresep_id = penjualanresep_t.penjualanresep_id
                  GROUP BY a.penjualanresep_id) AS countracikan,
            0 AS countracikan_freetext,
            penjualanresep_t.karyawan_id
           FROM penjualanresep_t
             LEFT JOIN ( SELECT antrian.antrian_id,
                    antrian.no_antrian
                   FROM antrian_t antrian
                  ORDER BY antrian.antrian_id) antrian_t ON penjualanresep_t.antrian_id = antrian_t.antrian_id
          WHERE penjualanresep_t.reseptur_id IS NULL
        UNION ALL
         SELECT 'reseptur'::text AS jenis,
            reseptur_t.reseptur_id,
            reseptur_t.penjualanresep_id AS resep_id,
            reseptur_t.pasien_id,
            reseptur_t.pendaftaran_id,
            reseptur_t.pasienadmisi_id,
            COALESCE(penjamin_admisi.carabayar_id, pendaftaran_t_1.carabayar_id) AS carabayar_id,
            COALESCE(pendaftaran_t_1.penjamin_admisi_id, pendaftaran_t_1.penjamin_id) AS penjamin_id,
            NULL::character varying AS nosep,
            reseptur_t.ruangan_id,
            reseptur_t.ruanganreseptur_id,
            reseptur_t.tglreseptur,
            NULL::timestamp without time zone AS tglresep,
            reseptur_t.noresep AS no_reseptur,
            NULL::character varying AS no_resep,
            reseptur_t.noresep AS nomor,
            NULL::integer AS penjualanresep_id,
            status_reseptur.lookup_name AS status_reseptur,
            reseptur_t.pegawai_id,
            antrian_t.no_antrian,
            reseptur_t.status_reseptur AS status_reseptur_id,
            reseptur_t.is_hamil,
            reseptur_t.luas_tubuh,
            reseptur_t.diagnosa_id,
            concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama) AS diagnosa_nama,
            reseptur_t.instruksi_id,
            reseptur_t.antrian_id,
            reseptur_t.catatan,
            NULL::character varying AS noresep_penjualan,
            NULL::integer AS iter_penjualan,
            ( SELECT string_agg(resepturdetail_t_1.racikan_id::text, '-'::text) AS antrian_racikan
                   FROM resepturdetail_t resepturdetail_t_1
                  WHERE resepturdetail_t_1.reseptur_id = reseptur_t.reseptur_id AND resepturdetail_t_1.is_deleted = false AND resepturdetail_t_1.is_active = true
                  GROUP BY resepturdetail_t_1.reseptur_id) AS antrian_racikan,
            ( SELECT resepturdetail_t_1.iter
                   FROM resepturdetail_t resepturdetail_t_1
                  WHERE resepturdetail_t_1.reseptur_id = reseptur_t.reseptur_id AND resepturdetail_t_1.is_deleted = false AND resepturdetail_t_1.is_active = true
                  GROUP BY resepturdetail_t_1.reseptur_id, resepturdetail_t_1.iter) AS iter,
            ( SELECT sum(resepturdetail_t_1.harganetto_reseptur) AS totalharganetto
                   FROM resepturdetail_t resepturdetail_t_1
                  WHERE resepturdetail_t_1.reseptur_id = reseptur_t.reseptur_id AND resepturdetail_t_1.is_deleted = false AND resepturdetail_t_1.is_active = true
                  GROUP BY resepturdetail_t_1.reseptur_id) AS totalharganetto,
            COALESCE(reseptur_t.biaya_administrasi, 0::double precision) AS biayaadministrasi,
            ( SELECT sum(resepturdetail_t_1.hargajual_reseptur) AS totalhargajual
                   FROM resepturdetail_t resepturdetail_t_1
                  WHERE resepturdetail_t_1.reseptur_id = reseptur_t.reseptur_id AND resepturdetail_t_1.is_deleted = false AND resepturdetail_t_1.is_active = true
                  GROUP BY resepturdetail_t_1.reseptur_id) AS totalhargajual,
            NULL::double precision AS totaltagihan,
            NULL::character varying AS nama_pembeli,
            NULL::smallint AS status_bayar,
            reseptur_t.tglreseptur AS tgl_resep_dibuat,
            '344'::character varying AS jenispenjualan_id,
            reseptur_t.status_worklist,
            NULL::integer AS kelaspelayanan_id,
            NULL::boolean AS is_approve,
            NULL::timestamp without time zone AS tgl_approve,
            NULL::integer AS pegawai_approve_id,
            NULL::text AS additional_data,
            reseptur_t.is_cetak_etiket::text AS status_etiket_reseptur,
            reseptur_t.is_cetak_etiket,
            NULL::text AS status_etiket_jual_resep,
            reseptur_t.kategori_resep,
            ( SELECT DISTINCT ON (a.reseptur_id) count(a.is_kronis) FILTER (WHERE a.is_kronis IS TRUE) AS count_kronis
                   FROM resepturdetail_t a
                  WHERE a.is_deleted = false AND a.reseptur_id = reseptur_t.reseptur_id
                  GROUP BY a.reseptur_id) AS countkronis,
            ( SELECT DISTINCT ON (a.reseptur_id) count(a.is_retur) FILTER (WHERE a.is_retur IS TRUE) AS count_retur
                   FROM resepturdetail_t a
                  WHERE a.reseptur_id = reseptur_t.reseptur_id
                  GROUP BY a.reseptur_id) AS countretur,
            ( SELECT DISTINCT ON (a.reseptur_id) count(a.racikan_id) FILTER (WHERE a.racikan_id = 1) AS count_obat_racikan
                   FROM resepturdetail_t a
                  WHERE a.is_deleted = false AND a.reseptur_id = reseptur_t.reseptur_id
                  GROUP BY a.reseptur_id) AS countracikan,
            ( SELECT DISTINCT ON (rt2.reseptur_id) count(rt2.resepturracikan_id) FILTER (WHERE rt2.type::text = 'OR'::text) AS count_obat_racikan_freetext
                   FROM resepturracikan_t rt2
                  WHERE rt2.reseptur_id = reseptur_t.reseptur_id
                  GROUP BY rt2.reseptur_id) AS countracikan_freetext,
            NULL::integer AS karyawan_id
           FROM reseptur_t
             JOIN ( SELECT pendaftaran.pendaftaran_id,
                    pendaftaran.pasienadmisi_id,
                    pendaftaran.kelaspelayanan_id,
                    pendaftaran.carabayar_id,
                    pendaftaran.penjamin_id,
                    pendaftaran.umur,
                    pendaftaran.no_pendaftaran,
                    pendaftaran.instalasi_id,
                    pasienadmisi_t_1.penjamin_id AS penjamin_admisi_id,
                    pasienadmisi_t_1.bpjs_id AS bpjsadmisi_id,
                    pendaftaran.bpjs_id,
                    pendaftaran.ruangan_id,
                    pasienadmisi_t_1.ruangan_id AS ruangan_pasien_admisi,
                    pasienadmisi_t_1.kamarruangan_id AS kamarruangan_pasien_admisi,
                    pasienadmisi_t_1.kamartempattidur_id AS kamartempattidur_pasien_admisi
                   FROM pendaftaran_t pendaftaran
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.pasienadmisi_id,
                            a.penjamin_id,
                            a.bpjs_id,
                            a.kamarruangan_id,
                            a.kamartempattidur_id,
                            a.ruangan_id
                           FROM pasienadmisi_t a) pasienadmisi_t_1 ON pendaftaran.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id) pendaftaran_t_1 ON reseptur_t.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
             JOIN ( SELECT pasien.pasien_id,
                    pasien.nama_pasien,
                    pasien.no_rekam_medik,
                    pasien.tanggal_lahir,
                    pasien.jeniskelamin,
                    pasien.namadepan,
                    pasien.alamat_pasien
                   FROM pasien_m pasien) pasien_m_1 ON reseptur_t.pasien_id = pasien_m_1.pasien_id
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
                   FROM kelaspelayanan_m kelas) kelaspelayanan_m_1 ON pendaftaran_t_1.kelaspelayanan_id = kelaspelayanan_m_1.kelaspelayanan_id
             LEFT JOIN ( SELECT carabayar.carabayar_id,
                    carabayar.carabayar_nama
                   FROM carabayar_m carabayar) carabayar_m_1 ON pendaftaran_t_1.carabayar_id = carabayar_m_1.carabayar_id
             LEFT JOIN ( SELECT penjamin.penjamin_id,
                    penjamin.penjamin_nama
                   FROM penjamin_m penjamin) penjamin_m_1 ON pendaftaran_t_1.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN ( SELECT penjamin.penjamin_id,
                    penjamin.penjamin_nama,
                    penjamin.carabayar_id
                   FROM penjamin_m penjamin) penjamin_admisi ON pendaftaran_t_1.penjamin_admisi_id = penjamin_admisi.penjamin_id
             LEFT JOIN ( SELECT carabayar.carabayar_id,
                    carabayar.carabayar_nama
                   FROM carabayar_m carabayar) carabayar_admisi ON penjamin_admisi.carabayar_id = carabayar_admisi.carabayar_id
             JOIN ( SELECT pegawai.pegawai_id,
                    pegawai.tgl_lahirpegawai,
                    pegawai.nama_pegawai,
                    pegawai.alamat_pegawai
                   FROM pegawai_m pegawai) pegawai_m_1 ON reseptur_t.pegawai_id = pegawai_m_1.pegawai_id
             LEFT JOIN ( SELECT antrian.antrian_id,
                    antrian.no_antrian
                   FROM antrian_t antrian
                  ORDER BY antrian.antrian_id) antrian_t ON reseptur_t.antrian_id = antrian_t.antrian_id
             LEFT JOIN ( SELECT diagnosa.diagnosa_id,
                    diagnosa.diagnosa_kode,
                    diagnosa.diagnosa_nama
                   FROM diagnosa_m diagnosa) diagnosa_m ON reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) jenis_kelamin_1 ON pasien_m_1.jeniskelamin::integer = jenis_kelamin_1.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) status_reseptur ON reseptur_t.status_reseptur = status_reseptur.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) namadepan_1 ON pasien_m_1.namadepan::integer = namadepan_1.lookup_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep,
                    a.norujukan,
                    a.pendaftaran_id
                   FROM bpjs_t a) bpjs_t_1 ON bpjs_t_1.bpjs_id = pendaftaran_t_1.bpjs_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep,
                    a.norujukan,
                    a.pendaftaran_id
                   FROM bpjs_t a) bpjspasienadmisi_t ON bpjspasienadmisi_t.bpjs_id = pendaftaran_t_1.bpjsadmisi_id
             LEFT JOIN ( SELECT ruangan_daftar_1_1.ruangan_id,
                    ruangan_daftar_1_1.ruangan_nama,
                    ruangan_daftar_1_1.instalasi_id
                   FROM ruangan_m ruangan_daftar_1_1) ruangan_daftar_1 ON ruangan_daftar_1.ruangan_id = pendaftaran_t_1.ruangan_id
             LEFT JOIN ( SELECT ruangan_admisi_1_1.ruangan_id,
                    ruangan_admisi_1_1.ruangan_nama,
                    ruangan_admisi_1_1.instalasi_id
                   FROM ruangan_m ruangan_admisi_1_1) ruangan_admisi_1 ON ruangan_admisi_1.ruangan_id = pendaftaran_t_1.ruangan_pasien_admisi
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m_1 ON pendaftaran_t_1.kamarruangan_pasien_admisi = kamarruangan_m_1.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m_1 ON pendaftaran_t_1.kamartempattidur_pasien_admisi = kamartempattidur_m_1.kamartempattidur_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name,
                    lookup_m.lookup_kode
                   FROM lookup_m) kategori_resep_lookup_1 ON reseptur_t.kategori_resep = kategori_resep_lookup_1.lookup_id
          WHERE reseptur_t.is_deleted = false AND reseptur_t.is_active = true AND reseptur_t.penjualanresep_id IS NULL) resep
     LEFT JOIN ( SELECT pendaftaran.pendaftaran_id,
            pendaftaran.pasien_id,
            pendaftaran.pasienadmisi_id,
            pendaftaran.instalasi_id,
            pendaftaran.umur,
            pendaftaran.no_pendaftaran,
            pendaftaran.bpjs_id,
            pendaftaran.ruangan_id
           FROM pendaftaran_t pendaftaran) pendaftaran_t ON resep.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pendaftaran_id,
            a.bpjs_id,
            a.kamartempattidur_id,
            a.kamarruangan_id,
            a.ruangan_id
           FROM pasienadmisi_t a) pasienadmisi_t ON resep.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT ruangan.ruangan_id,
            ruangan.ruangan_nama,
            ruangan.instalasi_id
           FROM ruangan_m ruangan) ruangan_resep ON resep.ruangan_id = ruangan_resep.ruangan_id
     JOIN ( SELECT instalasi.instalasi_id,
            instalasi.instalasi_nama
           FROM instalasi_m instalasi) instalasi_resep ON ruangan_resep.instalasi_id = instalasi_resep.instalasi_id
     LEFT JOIN ( SELECT kelas.kelaspelayanan_id,
            kelas.kelaspelayanan_nama
           FROM kelaspelayanan_m kelas) kelaspelayanan_m ON resep.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT carabayar.carabayar_id,
            carabayar.carabayar_nama
           FROM carabayar_m carabayar) carabayar_m ON resep.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT penjamin.penjamin_id,
            penjamin.penjamin_nama
           FROM penjamin_m penjamin) penjamin_m ON resep.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT peg_1.pegawai_id,
            peg_1.nama_pegawai,
            peg_1.gelardepan
           FROM pegawai_m peg_1) pegawai_m ON resep.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT peg_2.pegawai_id,
            peg_2.nama_pegawai,
            peg_2.alamat_pegawai,
            peg_2.tgl_lahirpegawai
           FROM pegawai_m peg_2) karyawan ON resep.karyawan_id = karyawan.pegawai_id
     LEFT JOIN ( SELECT a.bpjs_id,
            a.nosep,
            a.norujukan,
            a.pendaftaran_id
           FROM bpjs_t a) bpjs_t ON bpjs_t.bpjs_id = COALESCE(pendaftaran_t.bpjs_id, pasienadmisi_t.bpjs_id)
     LEFT JOIN ( SELECT ruangan_admisi_1.ruangan_id,
            ruangan_admisi_1.ruangan_nama,
            ruangan_admisi_1.instalasi_id
           FROM ruangan_m ruangan_admisi_1) ruangan_admisi ON ruangan_admisi.ruangan_id = pasienadmisi_t.ruangan_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT ruangan_daftar_1.ruangan_id,
            ruangan_daftar_1.ruangan_nama,
            ruangan_daftar_1.instalasi_id
           FROM ruangan_m ruangan_daftar_1) ruangan_daftar ON ruangan_daftar.ruangan_id = pendaftaran_t.ruangan_id
     LEFT JOIN ( SELECT pasien.pasien_id,
            pasien.nama_pasien,
            pasien.no_rekam_medik,
            pasien.tanggal_lahir,
            pasien.jeniskelamin,
            pasien.namadepan,
            pasien.alamat_pasien
           FROM pasien_m pasien) pasien_m ON resep.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) namadepan ON pasien_m.namadepan::integer = namadepan.lookup_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name,
            lookup_m.lookup_kode
           FROM lookup_m) kategori_resep_lookup ON resep.kategori_resep = kategori_resep_lookup.lookup_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) rm ON rm.ruangan_id = resep.ruanganreseptur_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) im ON im.instalasi_id = rm.instalasi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_approve ON resep.pegawai_approve_id = pegawai_approve.pegawai_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) jenispenjualan ON resep.jenispenjualan_id::integer = jenispenjualan.lookup_id;