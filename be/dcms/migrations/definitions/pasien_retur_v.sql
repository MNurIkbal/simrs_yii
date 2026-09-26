-- public.pasien_retur_v source

 CREATE OR REPLACE VIEW "public"."pasien_retur_v" as SELECT data.jenis,
    data.tanggal_pendaftaran,
    data.pendaftaran_id,
    data.returresep_id,
    data.nama_pasien,
    data.no_pendaftaran,
    data.instalasi_nama,
    data.carabayar_nama,
    data.penjamin_nama,
    data.no_rekam_medik,
    data.status_periksa,
    data.jumlah_transaksi,
    data.penanda_bayar,
    data.status_retur,
    data.no_returresep,
    data.ruanganakhir
   FROM ( SELECT 'NON-GABUNG-BILING'::text AS jenis,
            pendaftaran_t.created_date AS tanggal_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            returresep_t.returresep_id,
            pasien_m.nama_pasien,
            pendaftaran_t.no_pendaftaran,
            instalasi_m.instalasi_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            lookup_m.lookup_name,
            pasien_m.no_rekam_medik,
            ( SELECT count(c.pendaftaran_id) AS jumlah_transaksi
                   FROM obatalkespasien_t c
                  WHERE pendaftaran_t.pendaftaran_id = c.pendaftaran_id
                  GROUP BY c.pendaftaran_id) AS jumlah_transaksi,
            lookup_m.lookup_name AS status_periksa,
            returresep_t.no_returresep,
            returresep_t.status_retur,
                CASE
                    WHEN (( SELECT count(c.pembayaran_id) AS jumlah_pembayaran
                       FROM obatalkespasien_t c
                      WHERE pendaftaran_t.pendaftaran_id = c.pendaftaran_id
                      GROUP BY c.pendaftaran_id)) = 0 THEN NULL::text
                    WHEN (( SELECT count(c.pembayaran_id) AS jumlah_pembayaran
                       FROM obatalkespasien_t c
                      WHERE pendaftaran_t.pendaftaran_id = c.pendaftaran_id
                      GROUP BY c.pendaftaran_id)) IS NULL THEN NULL::text
                    ELSE '#ce9bca'::text
                END AS penanda_bayar,
            ruangan_m.ruangan_nama AS ruanganakhir
           FROM pendaftaran_t
             LEFT JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.returresep_id,
                    count(a.status_retur) AS status_retur,
                    a.no_returresep,
                    a.pendaftaran_id
                   FROM returresep_t a
                  WHERE a.is_deleted = false AND a.status_retur = 2118
                  GROUP BY a.pendaftaran_id, a.status_retur, a.returresep_id) returresep_t ON pendaftaran_t.pendaftaran_id = returresep_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.ruangan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON ruangan_m.ruangan_id = COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id)
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a
                  WHERE a.is_deleted = false) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a
                  WHERE a.is_deleted = false) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN lookup_m ON pendaftaran_t.status_periksa::integer = lookup_m.lookup_id
          WHERE NOT (pendaftaran_t.pendaftaran_id IN ( SELECT gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false AND gabungpelayanandetail_t.is_active = true)) AND NOT (pendaftaran_t.pendaftaran_id IN ( SELECT gabungpelayanandetail_t.pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false AND gabungpelayanandetail_t.is_active = true))
        UNION ALL
         SELECT 'GABUNG-BILING'::text AS jenis,
            pendaftaran_t.created_date AS tanggal_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            returresep_t.returresep_id,
            pasien_m.nama_pasien,
            pendaftaran_t.no_pendaftaran,
            instalasi_m.instalasi_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            lookup_m.lookup_name,
            pasien_m.no_rekam_medik,
            count_pendaftaran.jumlah_transaksi,
            lookup_m.lookup_name AS status_periksa,
            returresep_t.no_returresep,
            returresep_t.status_retur,
                CASE
                    WHEN count_pendaftaran.jumlah_pembayaran = 0 THEN NULL::text
                    WHEN count_pendaftaran.jumlah_pembayaran IS NULL THEN NULL::text
                    ELSE '#ce9bca'::text
                END AS penanda_bayar,
            ruangan_m.ruangan_nama AS ruanganakhir
           FROM pendaftaran_t
             JOIN gabungpelayanandetail_t ON pendaftaran_t.pendaftaran_id = gabungpelayanandetail_t.ref_pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.ruangan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON ruangan_m.ruangan_id = COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id)
             LEFT JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.returresep_id,
                    count(a.status_retur) AS status_retur,
                    a.no_returresep,
                    a.pendaftaran_id
                   FROM returresep_t a
                  WHERE a.is_deleted = false AND a.status_retur = 2118
                  GROUP BY a.pendaftaran_id, a.status_retur, a.returresep_id) returresep_t ON pendaftaran_t.pendaftaran_id = returresep_t.pendaftaran_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a
                  WHERE a.is_deleted = false) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a
                  WHERE a.is_deleted = false) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT c.pendaftaran_id,
                    count(c.pendaftaran_id) AS jumlah_transaksi,
                    count(c.pembayaran_id) AS jumlah_pembayaran
                   FROM obatalkespasien_t c
                  GROUP BY c.pendaftaran_id) count_pendaftaran ON pendaftaran_t.pendaftaran_id = count_pendaftaran.pendaftaran_id
             LEFT JOIN lookup_m ON pendaftaran_t.status_periksa::integer = lookup_m.lookup_id) data;