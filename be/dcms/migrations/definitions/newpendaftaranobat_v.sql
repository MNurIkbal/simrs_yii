-- public.newpendaftaranobat_v source

CREATE OR REPLACE VIEW public.newpendaftaranobat_v
AS SELECT a.transaksi,
    a.no_pendaftaran,
    a.pendaftaran_id,
    a.nama_pasien,
    a.no_rekam_medik,
    a.dokter_dpjp,
    a.obatalkes_id,
    a.obatalkes_nama,
    a.ruangan_nama,
    a.instalasi_nama,
    a.is_racikan,
    sum(a.qty_resep) AS qty_resep,
    a.satuan_resep,
    a.harga_satuan
   FROM ( SELECT 'resep'::text AS transaksi,
            pendaftaran.no_pendaftaran,
            pendaftaran.pendaftaran_id,
            pasien.nama_pasien,
            pasien.no_rekam_medik,
            dpjp.nama_pegawai AS dokter_dpjp,
            obat.obatalkes_id,
            obat.obatalkes_nama,
            COALESCE(ruangan.ruangan_nama, '-'::character varying) AS ruangan_nama,
            COALESCE(instalasi.instalasi_nama, '-'::character varying) AS instalasi_nama,
                CASE
                    WHEN oapasien.racikan_id = 2 THEN false
                    ELSE true
                END AS is_racikan,
                CASE
                    WHEN (min(resep.status_bayar) = ANY (ARRAY[348, 349])) AND oapasien.is_deleted = true THEN 0::double precision
                    WHEN (min(resep.status_bayar) = ANY (ARRAY[348, 349])) AND oapasien.is_deleted = false THEN
                    CASE
                        WHEN oapasien.is_deleted = true THEN 0::double precision
                        ELSE
                        CASE
                            WHEN oapasien.pembayaran_id IS NOT NULL AND pembayaran_t.is_deleted = false THEN 0::double precision
                            ELSE sum(coalesce(oapasien.det_konversi,oapasien.qty_konversi))
                        END
                    END
                    ELSE sum(coalesce(oapasien.det_konversi,oapasien.qty_konversi)) 
                END AS qty_resep,
            satuan.satuanunit_nama AS satuan_resep,
                CASE
                    WHEN btrim((oapasien.additional_data::json -> 'nilai_konversi'::text)::text, '"'::text) = 'null'::text THEN oapasien.hargasatuan_oa
                    ELSE ceil(oapasien.hargasatuan_oa / btrim((oapasien.additional_data::json -> 'nilai_konversi'::text)::text, '"'::text)::numeric::double precision)
                END AS harga_satuan,
            oapasien.is_deleted
           FROM obatalkespasien_t oapasien
             LEFT JOIN ( SELECT a_1.pembayaran_id,
                    a_1.is_deleted
                   FROM pembayaran_t a_1) pembayaran_t ON pembayaran_t.pembayaran_id = oapasien.pembayaran_id
             LEFT JOIN ( SELECT penjualanresep_t.penjualanresep_id,
                    penjualanresep_t.pendaftaran_id,
                    penjualanresep_t.status_reseptur,
                    penjualanresep_t.status_bayar
                   FROM penjualanresep_t) resep ON resep.penjualanresep_id = oapasien.penjualanresep_id
             LEFT JOIN ( SELECT obatalkes_m.obatalkes_id,
                    obatalkes_m.obatalkes_nama
                   FROM obatalkes_m) obat ON obat.obatalkes_id = oapasien.obatalkes_id
             LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
                    satuanunit_m.satuanunit_nama
                   FROM satuanunit_m) satuan ON satuan.satuanunit_id = oapasien.satuankecil_id
             LEFT JOIN ( SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.pasien_id,
                    pendaftaran_t.pegawai_id,
                    pendaftaran_t.pasienadmisi_id,
                    pendaftaran_t.ruangan_id
                   FROM pendaftaran_t) pendaftaran ON pendaftaran.pendaftaran_id = resep.pendaftaran_id
             LEFT JOIN ( SELECT pasien_m.pasien_id,
                    pasien_m.nama_pasien,
                    pasien_m.no_rekam_medik
                   FROM pasien_m) pasien ON pasien.pasien_id = pendaftaran.pasien_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    pasienadmisi_t.pendaftaran_id,
                    pasienadmisi_t.pegawai_id,
                    pasienadmisi_t.ruangan_id
                   FROM pasienadmisi_t) admisi ON admisi.pendaftaran_id = pendaftaran.pendaftaran_id
             LEFT JOIN pegawai_m dpjp ON dpjp.pegawai_id =
                CASE
                    WHEN pendaftaran.pasienadmisi_id IS NOT NULL THEN admisi.pegawai_id
                    ELSE pendaftaran.pegawai_id
                END
             LEFT JOIN ( SELECT ruangan_m.instalasi_id,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama
                   FROM ruangan_m) ruangan ON ruangan.ruangan_id = COALESCE(admisi.ruangan_id, pendaftaran.ruangan_id)
             LEFT JOIN ( SELECT instalasi_m.instalasi_id,
                    instalasi_m.instalasi_nama
                   FROM instalasi_m) instalasi ON instalasi.instalasi_id = ruangan.instalasi_id
          WHERE resep.status_reseptur = 660 AND oapasien.penjualanresep_id IS NOT NULL
          GROUP BY oapasien.is_deleted, pembayaran_t.is_deleted, pendaftaran.no_pendaftaran, pendaftaran.pendaftaran_id, pasien.nama_pasien, pasien.no_rekam_medik, dpjp.nama_pegawai, obat.obatalkes_id, obat.obatalkes_nama, resep.status_bayar, oapasien.pembayaran_id, ruangan.ruangan_nama, instalasi.instalasi_nama, oapasien.racikan_id, satuan.satuanunit_nama, oapasien.additional_data, oapasien.hargasatuan_oa
        UNION ALL
         SELECT 'bmhp'::text AS transaksi,
            pendaftaran.no_pendaftaran,
            pendaftaran.pendaftaran_id,
            pasien.nama_pasien,
            pasien.no_rekam_medik,
            dpjp.nama_pegawai AS dokter_dpjp,
            obat.obatalkes_id,
            obat.obatalkes_nama,
            COALESCE(ruangan.ruangan_nama, '-'::character varying) AS ruangan_nama,
            COALESCE(instalasi.instalasi_nama, '-'::character varying) AS instalasi_nama,
            false AS is_racikan,
                CASE
                    WHEN oapasien.is_deleted = true THEN 0::double precision
                    ELSE sum(oapasien.qty_oa)
                END AS qty_resep,
            satuan.satuanunit_nama AS satuan_resep,
            oapasien.hargasatuan_oa AS harga_satuan,
            oapasien.is_deleted
           FROM obatalkespasien_t oapasien
             LEFT JOIN ( SELECT obatalkes_m.obatalkes_id,
                    obatalkes_m.obatalkes_nama
                   FROM obatalkes_m) obat ON obat.obatalkes_id = oapasien.obatalkes_id
             LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
                    satuanunit_m.satuanunit_nama
                   FROM satuanunit_m) satuan ON satuan.satuanunit_id = oapasien.satuankecil_id
             LEFT JOIN ( SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.pasien_id,
                    pendaftaran_t.pegawai_id,
                    pendaftaran_t.pasienadmisi_id,
                    pendaftaran_t.ruangan_id
                   FROM pendaftaran_t) pendaftaran ON pendaftaran.pendaftaran_id = oapasien.pendaftaran_id
             LEFT JOIN ( SELECT instruksitindakanbmhp_t.instruksitindakanbmhp_id
                   FROM instruksitindakanbmhp_t) bmhp ON bmhp.instruksitindakanbmhp_id = oapasien.instruksitindakanbmhp_id
             LEFT JOIN ( SELECT pasien_m.pasien_id,
                    pasien_m.nama_pasien,
                    pasien_m.no_rekam_medik
                   FROM pasien_m) pasien ON pasien.pasien_id = pendaftaran.pasien_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    pasienadmisi_t.pendaftaran_id,
                    pasienadmisi_t.pegawai_id,
                    pasienadmisi_t.ruangan_id
                   FROM pasienadmisi_t) admisi ON admisi.pendaftaran_id = pendaftaran.pendaftaran_id
             LEFT JOIN pegawai_m dpjp ON dpjp.pegawai_id =
                CASE
                    WHEN pendaftaran.pasienadmisi_id IS NOT NULL THEN admisi.pegawai_id
                    ELSE pendaftaran.pegawai_id
                END
             LEFT JOIN ( SELECT ruangan_m.instalasi_id,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama
                   FROM ruangan_m) ruangan ON ruangan.ruangan_id = COALESCE(admisi.ruangan_id, pendaftaran.ruangan_id)
             LEFT JOIN ( SELECT instalasi_m.instalasi_id,
                    instalasi_m.instalasi_nama
                   FROM instalasi_m) instalasi ON instalasi.instalasi_id = ruangan.instalasi_id
          WHERE oapasien.penjualanresep_id IS NULL AND (oapasien.ruangan_id IN ( SELECT ruangan_m.ruangan_id
                   FROM ruangan_m
                  WHERE ruangan_m.instalasi_id <> (( SELECT lookuptransaksi_m.kode_id
                           FROM lookuptransaksi_m
                          WHERE lookuptransaksi_m.kode_transaksi::text = 'instalasi_bedah'::text))))
          GROUP BY oapasien.is_deleted, pendaftaran.no_pendaftaran, pendaftaran.pendaftaran_id, pasien.nama_pasien, pasien.no_rekam_medik, dpjp.nama_pegawai, obat.obatalkes_id, obat.obatalkes_nama, false::boolean, satuan.satuanunit_nama, oapasien.hargasatuan_oa, ruangan.ruangan_nama, instalasi.instalasi_nama, oapasien.pembayaran_id) a
  GROUP BY a.transaksi, a.no_pendaftaran, a.pendaftaran_id, a.nama_pasien, a.no_rekam_medik, a.dokter_dpjp, a.obatalkes_id, a.obatalkes_nama, a.ruangan_nama, a.instalasi_nama, a.is_racikan, a.satuan_resep, a.harga_satuan;