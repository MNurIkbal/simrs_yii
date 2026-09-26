-- public.laporankunjunganpenunjang_v source

CREATE OR REPLACE VIEW public.laporankunjunganpenunjang_v
AS SELECT a.no_pendaftaran,
    a.tglmasukpenunjang,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE
            WHEN a.is_aps = true THEN 'APS'::text::character varying
            ELSE instalasiasal.instalasi_nama
        END AS unit,
    a.penjamin_id,
    penjamin_m.penjamin_nama,
    a.id AS jeniskegiatantindakan_id,
    a.jenis AS jeniskegiatantindakan_nama,
    a.daftartindakan_nama,
    count(
        CASE
            WHEN a.qty_tindakan IS NOT NULL THEN ''::text
            ELSE NULL::text
        END) AS jumlah_tindakan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama
   FROM ( SELECT 'LAB'::text AS tipe,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.is_aps,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            COALESCE(pemeriksaanlab_m.pemeriksaanlab_nama, daftartindakan_m.daftartindakan_nama) AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pasienmasukpenunjang_t_1.tglmasukpenunjang,
            jenispemeriksaanlab_m.jenispemeriksaanlab_id AS id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pasienmasukpenunjang_t_1.pasienkirimkeunitlain_id
           FROM tindakanpelayanan_t
             JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                    daftartindakan_m_1.daftartindakan_nama
                   FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT pemeriksaanlab_m_1.pemeriksaanlab_nama,
                    pemeriksaanlab_m_1.jenispemeriksaanlab_id,
                    pemeriksaanlab_m_1.daftartindakan_id
                   FROM pemeriksaanlab_m pemeriksaanlab_m_1
                  WHERE pemeriksaanlab_m_1.is_deleted = false) pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
             JOIN ( SELECT jenispemeriksaanlab_m_1.jenispemeriksaanlab_nama,
                    jenispemeriksaanlab_m_1.jenispemeriksaanlab_id
                   FROM jenispemeriksaanlab_m jenispemeriksaanlab_m_1) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
             JOIN ( SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                    pasienmasukpenunjang_t.ruangan_id,
                    pasienmasukpenunjang_t.tglmasukpenunjang,
                    pasienmasukpenunjang_t.pendaftaran_id,
                    pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                   FROM pasienmasukpenunjang_t
                  WHERE pasienmasukpenunjang_t.status_periksa::integer <> 476) pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
             JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.no_pendaftaran,
                    pendaftaran_t_1.is_aps,
                    pendaftaran_t_1.pasien_id,
                    pendaftaran_t_1.instalasi_id,
                    pendaftaran_t_1.penjamin_id,
                    pendaftaran_t_1.carabayar_id
                   FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = false
        UNION ALL
         SELECT 'LAB_PAKET'::text AS tipe,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.is_aps,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            pemeriksaanlab_m.pemeriksaanlab_nama AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pasienmasukpenunjang_t_1.tglmasukpenunjang,
            jenispemeriksaanlab_m.jenispemeriksaanlab_id AS id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pasienmasukpenunjang_t_1.pasienkirimkeunitlain_id
           FROM tindakanpelayanan_t
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    paketpelayanan_mp_1.daftartindakan_id
                   FROM paketpelayanan_mp paketpelayanan_mp_1) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                    daftartindakan_m_1.daftartindakan_nama
                   FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id
             JOIN ( SELECT pemeriksaanlab_m_1.pemeriksaanlab_nama,
                    pemeriksaanlab_m_1.daftartindakan_id,
                    pemeriksaanlab_m_1.jenispemeriksaanlab_id
                   FROM pemeriksaanlab_m pemeriksaanlab_m_1
                  WHERE pemeriksaanlab_m_1.is_deleted = false) pemeriksaanlab_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
             LEFT JOIN ( SELECT jenispemeriksaanlab_m_1.jenispemeriksaanlab_id,
                    jenispemeriksaanlab_m_1.jenispemeriksaanlab_nama
                   FROM jenispemeriksaanlab_m jenispemeriksaanlab_m_1) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
             JOIN ( SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                    pasienmasukpenunjang_t.pendaftaran_id,
                    pasienmasukpenunjang_t.ruangan_id,
                    pasienmasukpenunjang_t.tglmasukpenunjang,
                    pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                   FROM pasienmasukpenunjang_t
                  WHERE pasienmasukpenunjang_t.status_periksa::integer <> 476 AND pasienmasukpenunjang_t.is_bayar = true) pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
             JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.no_pendaftaran,
                    pendaftaran_t_1.is_aps,
                    pendaftaran_t_1.pasien_id,
                    pendaftaran_t_1.instalasi_id,
                    pendaftaran_t_1.penjamin_id,
                    pendaftaran_t_1.carabayar_id
                   FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE tindakanpelayanan_t.tipepaket_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = false
        UNION ALL
         SELECT 'RAD'::text AS tipe,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.is_aps,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            COALESCE(pemeriksaanrad_m.pemeriksaanrad_nama, daftartindakan_m.daftartindakan_nama) AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pasienmasukpenunjang_t_1.tglmasukpenunjang,
            jenispemeriksaanrad_m.jenispemeriksaanrad_id AS id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pasienmasukpenunjang_t_1.pasienkirimkeunitlain_id
           FROM tindakanpelayanan_t
             JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                    daftartindakan_m_1.daftartindakan_nama
                   FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT pemeriksaanrad_m_1.daftartindakan_id,
                    pemeriksaanrad_m_1.pemeriksaanrad_nama,
                    pemeriksaanrad_m_1.jenispemeriksaanrad_id
                   FROM pemeriksaanrad_m pemeriksaanrad_m_1
                  WHERE pemeriksaanrad_m_1.is_deleted = false) pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
             JOIN ( SELECT jenispemeriksaanrad_m_1.jenispemeriksaanrad_id,
                    jenispemeriksaanrad_m_1.jenispemeriksaanrad_nama
                   FROM jenispemeriksaanrad_m jenispemeriksaanrad_m_1) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
             JOIN ( SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                    pasienmasukpenunjang_t.ruangan_id,
                    pasienmasukpenunjang_t.tglmasukpenunjang,
                    pasienmasukpenunjang_t.pendaftaran_id,
                    pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                   FROM pasienmasukpenunjang_t
                  WHERE pasienmasukpenunjang_t.status_periksa::integer <> 476 AND pasienmasukpenunjang_t.is_bayar = true) pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
             JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.no_pendaftaran,
                    pendaftaran_t_1.is_aps,
                    pendaftaran_t_1.pasien_id,
                    pendaftaran_t_1.instalasi_id,
                    pendaftaran_t_1.penjamin_id,
                    pendaftaran_t_1.carabayar_id
                   FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = false
        UNION ALL
         SELECT 'RAD_PAKET'::text AS tipe,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.is_aps,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            pemeriksaanrad_m.pemeriksaanrad_nama AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pasienmasukpenunjang_t_1.tglmasukpenunjang,
            jenispemeriksaanrad_m.jenispemeriksaanrad_id AS id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pasienmasukpenunjang_t_1.pasienkirimkeunitlain_id
           FROM tindakanpelayanan_t
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    paketpelayanan_mp_1.daftartindakan_id
                   FROM paketpelayanan_mp paketpelayanan_mp_1) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                    daftartindakan_m_1.daftartindakan_nama
                   FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id
             JOIN ( SELECT pemeriksaanrad_m_1.daftartindakan_id,
                    pemeriksaanrad_m_1.pemeriksaanrad_nama,
                    pemeriksaanrad_m_1.jenispemeriksaanrad_id
                   FROM pemeriksaanrad_m pemeriksaanrad_m_1
                  WHERE pemeriksaanrad_m_1.is_deleted = false) pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
             JOIN ( SELECT jenispemeriksaanrad_m_1.jenispemeriksaanrad_id,
                    jenispemeriksaanrad_m_1.jenispemeriksaanrad_nama
                   FROM jenispemeriksaanrad_m jenispemeriksaanrad_m_1) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
             JOIN ( SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                    pasienmasukpenunjang_t.ruangan_id,
                    pasienmasukpenunjang_t.tglmasukpenunjang,
                    pasienmasukpenunjang_t.pendaftaran_id,
                    pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                   FROM pasienmasukpenunjang_t
                  WHERE pasienmasukpenunjang_t.status_periksa::integer <> 476 AND pasienmasukpenunjang_t.is_bayar = true) pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
             JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.no_pendaftaran,
                    pendaftaran_t_1.is_aps,
                    pendaftaran_t_1.pasien_id,
                    pendaftaran_t_1.instalasi_id,
                    pendaftaran_t_1.penjamin_id,
                    pendaftaran_t_1.carabayar_id
                   FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE tindakanpelayanan_t.tipepaket_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = false
        UNION ALL
         SELECT 'OPERASI'::text AS tipe,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.is_aps,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            kegiatanoperasi_m.kegiatanoperasi_nama AS jenis,
            operasi_m.operasi_nama,
            COALESCE(kegiatanoperasi_m.kegiatanoperasi_nama, daftartindakan_m.daftartindakan_nama) AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pasienmasukpenunjang_t_1.tglmasukpenunjang,
            kegiatanoperasi_m.kegiatanoperasi_id AS id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pasienmasukpenunjang_t_1.pasienkirimkeunitlain_id
           FROM tindakanpelayanan_t
             JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                    daftartindakan_m_1.daftartindakan_nama
                   FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON daftartindakan_m.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id
             JOIN ( SELECT operasi_m_1.daftartindakan_id,
                    operasi_m_1.operasi_nama,
                    operasi_m_1.kegiatanoperasi_id
                   FROM operasi_m operasi_m_1) operasi_m ON tindakanpelayanan_t.daftartindakan_id = operasi_m.daftartindakan_id
             JOIN ( SELECT kegiatanoperasi_m_1.kegiatanoperasi_nama,
                    kegiatanoperasi_m_1.kegiatanoperasi_id
                   FROM kegiatanoperasi_m kegiatanoperasi_m_1) kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
             JOIN ( SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                    pasienmasukpenunjang_t.ruangan_id,
                    pasienmasukpenunjang_t.tglmasukpenunjang,
                    pasienmasukpenunjang_t.pendaftaran_id,
                    pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                   FROM pasienmasukpenunjang_t
                  WHERE pasienmasukpenunjang_t.status_periksa::integer <> 476 AND pasienmasukpenunjang_t.is_bayar = true) pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
             JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.no_pendaftaran,
                    pendaftaran_t_1.is_aps,
                    pendaftaran_t_1.pasien_id,
                    pendaftaran_t_1.instalasi_id,
                    pendaftaran_t_1.penjamin_id,
                    pendaftaran_t_1.carabayar_id
                   FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = false
        UNION ALL
         SELECT 'OPERASI_PAKET'::text AS tipe,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.is_aps,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            kegiatanoperasi_m.kegiatanoperasi_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            operasi_m.operasi_nama AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pasienmasukpenunjang_t_1.tglmasukpenunjang,
            kegiatanoperasi_m.kegiatanoperasi_id AS id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pasienmasukpenunjang_t_1.pasienkirimkeunitlain_id
           FROM tindakanpelayanan_t
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    paketpelayanan_mp_1.daftartindakan_id
                   FROM paketpelayanan_mp paketpelayanan_mp_1) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                    daftartindakan_m_1.daftartindakan_nama
                   FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id
             JOIN ( SELECT operasi_m_1.daftartindakan_id,
                    operasi_m_1.operasi_nama,
                    operasi_m_1.kegiatanoperasi_id
                   FROM operasi_m operasi_m_1) operasi_m ON tindakanpelayanan_t.daftartindakan_id = operasi_m.daftartindakan_id
             JOIN ( SELECT kegiatanoperasi_m_1.kegiatanoperasi_nama,
                    kegiatanoperasi_m_1.kegiatanoperasi_id
                   FROM kegiatanoperasi_m kegiatanoperasi_m_1) kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
             JOIN ( SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                    pasienmasukpenunjang_t.ruangan_id,
                    pasienmasukpenunjang_t.tglmasukpenunjang,
                    pasienmasukpenunjang_t.pendaftaran_id,
                    pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                   FROM pasienmasukpenunjang_t
                  WHERE pasienmasukpenunjang_t.status_periksa::integer <> 476 AND pasienmasukpenunjang_t.is_bayar = true) pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
             JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.no_pendaftaran,
                    pendaftaran_t_1.is_aps,
                    pendaftaran_t_1.pasien_id,
                    pendaftaran_t_1.instalasi_id,
                    pendaftaran_t_1.penjamin_id,
                    pendaftaran_t_1.carabayar_id
                   FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE tindakanpelayanan_t.tipepaket_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pendaftaran_t.is_aps = false) a
     JOIN ( SELECT a_1.pasien_id,
            a_1.no_rekam_medik,
            a_1.nama_pasien
           FROM pasien_m a_1) pasien_m ON a.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN instalasi_m instalasiasal ON a.instalasi_id = instalasiasal.instalasi_id
     JOIN carabayar_m ON a.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON a.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasienkirimkeunitlain_t ON a.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE instalasi_m.instalasi_id = ANY (ARRAY[4, 5, 7])
  GROUP BY a.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, ruangan_m.ruangan_id, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, a.penjamin_id, a.is_aps, penjamin_m.penjamin_nama, a.daftartindakan_nama, a.jenis, a.id, (to_char(a.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date), (to_char(pasienkirimkeunitlain_t.tgl_kirimpasien, 'YYYY-MM-DD'::text)::date), a.tglmasukpenunjang, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, instalasiasal.instalasi_nama, pasien_m.no_rekam_medik
  ORDER BY (to_char(a.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date) DESC;