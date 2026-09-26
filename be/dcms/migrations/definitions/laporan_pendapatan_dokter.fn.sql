-- DROP FUNCTION public.laporan_pendapatan_dokter(date, date);

CREATE OR REPLACE FUNCTION public.laporan_pendapatan_dokter(start_date date, end_date date)
 RETURNS TABLE(no_pendaftaran character varying, nama_pegawai character varying, nomor_induk_pegawai character varying, pangkat_nama character varying, jabatan_nama character varying, spesialis_nama character varying, unit_kerja character varying, ruangan_nama character varying, status character varying, pendapatan numeric, jumlah_pasien integer)
 LANGUAGE plpgsql
AS $function$
BEGIN
  RETURN QUERY
  SELECT
    x.no_pendaftaran,
    x.nama_pegawai,
    x.nomorindukpegawai AS nomor_induk_pegawai,
    x.pangkat_nama,
    x.jabatan_nama,
    x.spesialis_nama,
    x.unit_kerja,
    x.ruangan_nama,
    x.status,
    SUM(x.jumlah_tindakan::DECIMAL) AS pendapatan,
    x.jumlah_pasien
  FROM (
    -- Bagian tindakan
    SELECT
      pt.pendaftaran_id,
      pt.no_pendaftaran,
      pg.nama_pegawai,
      pg.nomorindukpegawai,
      pk.pangkat_nama,
      jb.jabatan_nama,
      'RSUD Labuang Baji'::VARCHAR AS unit_kerja,
      sp.spesialis_nama,
      ''::VARCHAR  AS status,
      ru.ruangan_nama,
      SUM(tp.tarif_tindakan) AS jumlah_tindakan,
      1::INT AS jumlah_pasien
    FROM
      pendaftaran_t pt
      LEFT JOIN pasienadmisi_t pa ON pt.pasienadmisi_id = pa.pasienadmisi_id
      JOIN pasien_m ps ON pt.pasien_id = ps.pasien_id
      JOIN pembayaran_t pb ON pt.pendaftaran_id = pb.pendaftaran_id
      JOIN tindakanpelayanan_t tp ON pt.pendaftaran_id = tp.pendaftaran_id
      JOIN ruangan_m ru ON tp.ruangan_id = ru.ruangan_id
      JOIN pegawai_m pg ON tp.dokterpenanggungjawab_id = pg.pegawai_id
      LEFT JOIN pangkat_m pk ON pg.pangkat_id = pk.pangkat_id
      LEFT JOIN jabatan_m jb ON pg.jabatan_id = jb.jabatan_id
      LEFT JOIN spesialis_m sp ON pg.spesialis_id = sp.spesialis_id
    WHERE
      pt.status_periksa NOT IN ('402', '628') AND
      pb.created_date::DATE BETWEEN start_date AND end_date AND
      (
        (pa.tgl_admisi IS NOT NULL AND pa.tgl_admisi::DATE BETWEEN start_date AND end_date)
        OR (pa.tgl_admisi IS NULL AND pt.tgl_pendaftaran::DATE BETWEEN start_date AND end_date)
      ) AND
      pg.kelompokpegawai_id = 1 AND
      tp.is_deleted IS FALSE AND
      tp.tindakansudahbayar_id IS NOT NULL
    GROUP BY pt.pendaftaran_id, pt.no_pendaftaran, pg.nama_pegawai, pg.nomorindukpegawai,
             pk.pangkat_nama, jb.jabatan_nama, sp.spesialis_nama, ru.ruangan_nama

    UNION ALL

    -- Bagian obat via reseptur
    SELECT
      pt.pendaftaran_id,
      pt.no_pendaftaran,
      pg.nama_pegawai,
      pg.nomorindukpegawai,
      pk.pangkat_nama,
      jb.jabatan_nama,
      'RSUD Labuang Baji'::VARCHAR  AS unit_kerja,
      sp.spesialis_nama,
      ''::VARCHAR  AS status,
      ru.ruangan_nama,
      SUM(oa.hargajual_oa) AS jumlah_obat,
      1::INT AS jumlah_pasien
    FROM
      pendaftaran_t pt
      LEFT JOIN pasienadmisi_t pa ON pt.pasienadmisi_id = pa.pasienadmisi_id
      JOIN pasien_m ps ON pt.pasien_id = ps.pasien_id
      JOIN pembayaran_t pb ON pt.pendaftaran_id = pb.pendaftaran_id
      JOIN obatalkespasien_t oa ON pt.pendaftaran_id = oa.pendaftaran_id
      JOIN penjualanresep_t pr ON oa.penjualanresep_id = pr.penjualanresep_id
      JOIN reseptur_t rs ON pr.penjualanresep_id = rs.penjualanresep_id
      JOIN ruangan_m ru ON rs.ruanganreseptur_id = ru.ruangan_id
      JOIN pegawai_m pg ON rs.pegawai_id = pg.pegawai_id
      LEFT JOIN pangkat_m pk ON pg.pangkat_id = pk.pangkat_id
      LEFT JOIN jabatan_m jb ON pg.jabatan_id = jb.jabatan_id
      LEFT JOIN spesialis_m sp ON pg.spesialis_id = sp.spesialis_id
    WHERE
      pt.status_periksa NOT IN ('402', '628') AND
      (
        (pa.tgl_admisi IS NOT NULL AND pa.tgl_admisi::DATE BETWEEN start_date AND end_date)
        OR (pa.tgl_admisi IS NULL AND pt.tgl_pendaftaran::DATE BETWEEN start_date AND end_date)
      ) AND
      pb.created_date::DATE BETWEEN start_date AND end_date AND
      pr.tglpenjualan::DATE BETWEEN start_date AND end_date AND
      rs.tglreseptur::DATE BETWEEN start_date AND end_date AND
      pg.kelompokpegawai_id = 1 AND
      oa.is_deleted IS FALSE AND
      oa.obatsudahbayar_id IS NOT NULL
    GROUP BY pt.pendaftaran_id, pt.no_pendaftaran, pg.nama_pegawai, pg.nomorindukpegawai,
             pk.pangkat_nama, jb.jabatan_nama, sp.spesialis_nama, ru.ruangan_nama

    UNION ALL

    -- Bagian obat tanpa reseptur
    SELECT
      pt.pendaftaran_id,
      pt.no_pendaftaran,
      pg.nama_pegawai,
      pg.nomorindukpegawai,
      pk.pangkat_nama,
      jb.jabatan_nama,
      'RSUD Labuang Baji'::VARCHAR  AS unit_kerja,
      sp.spesialis_nama,
      ''::VARCHAR  AS status,
      ru.ruangan_nama,
      SUM(oa.hargajual_oa) AS jumlah_obat,
      1::INT AS jumlah_pasien
    FROM
      pendaftaran_t pt
      LEFT JOIN pasienadmisi_t pa ON pt.pasienadmisi_id = pa.pasienadmisi_id
      JOIN pasien_m ps ON pt.pasien_id = ps.pasien_id
      JOIN pembayaran_t pb ON pt.pendaftaran_id = pb.pendaftaran_id
      JOIN obatalkespasien_t oa ON pt.pendaftaran_id = oa.pendaftaran_id
      JOIN ruangan_m ru ON oa.ruangan_id = ru.ruangan_id
      JOIN pegawai_m pg ON oa.pegawai_id = pg.pegawai_id
      LEFT JOIN pangkat_m pk ON pg.pangkat_id = pk.pangkat_id
      LEFT JOIN jabatan_m jb ON pg.jabatan_id = jb.jabatan_id
      LEFT JOIN spesialis_m sp ON pg.spesialis_id = sp.spesialis_id
    WHERE
      pt.status_periksa NOT IN ('402', '628') AND
      (
        (pa.tgl_admisi IS NOT NULL AND pa.tgl_admisi::DATE BETWEEN start_date AND end_date)
        OR (pa.tgl_admisi IS NULL AND pt.tgl_pendaftaran::DATE BETWEEN start_date AND end_date)
      ) AND
      pb.created_date::DATE BETWEEN start_date AND end_date AND
      oa.penjualanresep_id IS NULL AND
      oa.is_deleted IS FALSE AND
      oa.obatsudahbayar_id IS NOT NULL AND
      pg.kelompokpegawai_id = 1
    GROUP BY pt.pendaftaran_id, pt.no_pendaftaran, pg.nama_pegawai, pg.nomorindukpegawai,
             pk.pangkat_nama, jb.jabatan_nama, sp.spesialis_nama, ru.ruangan_nama

  ) AS x
  GROUP BY
    x.pendaftaran_id,
    x.no_pendaftaran,
    x.nama_pegawai,
    x.nomorindukpegawai,
    x.pangkat_nama,
    x.jabatan_nama,
    x.spesialis_nama,
    x.unit_kerja,
    x.ruangan_nama,
    x.status,
    x.jumlah_pasien
  ORDER BY
    x.pendaftaran_id,
    x.nama_pegawai;
END;
$function$
;
