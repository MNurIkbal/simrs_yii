CREATE OR REPLACE FUNCTION "public"."pemberianpiutang_fn"("var_qwords" varchar=NULL::character varying, "var_limit" int4=10, "var_offset" int4=0)
  RETURNS TABLE("jenis" text, "no_pendaftaran" varchar, "no_resep" varchar, "no_rekam_medik" varchar, "pendaftaran_id" int4, "ref_pendaftaran_id" int4, "ref_no_pendaftaran" varchar, "is_gabungbilling" bool, "pasienadmisi_id" int4, "pasien_id" int4, "kelaspelayanan_id" int4, "penjamin_id" int4, "penjualanresep_id" int4, "nama_pasien" varchar, "total_tagihan" float8, "piutang_sudahbayar" float8, "tanggal_lahir" date, "umur" varchar, "tgl_pendaftaran" timestamp, "total_piutang" float8, "adm_persen" float4, "tarif_max" numeric, "tagihan_ranap" float8, "uang_muka" float8, "is_pembulatankeatas" bool, "satuanpembulatan" int4, "is_batal" bool, "created_date" timestamp) AS $BODY$ 
BEGIN
    RETURN QUERY
    WITH pendaftaran_pasien AS (
      SELECT
        pendaftaran.pendaftaran_id,
        pendaftaran.pasienadmisi_id,
        pendaftaran.pasien_id,
        pendaftaran.no_pendaftaran,
        pendaftaran.tgl_pendaftaran::date AS tgl_pendaftaran,
        pasien.no_rekam_medik,
        pasien.nama_pasien,
        pasien.tanggal_lahir::date AS tanggal_lahir,
        pendaftaran.umur,
        pasienadmisi_t.kelaspelayanan_id,
        pasienadmisi_t.penjamin_id,
        LOWER(CONCAT_WS(' ', pendaftaran.no_pendaftaran, pasien.no_rekam_medik, pasien.nama_pasien)) AS keywords
      FROM pendaftaran_t pendaftaran
      JOIN ( SELECT 
          a.pasien_id,
          a.no_rekam_medik,
          a.nama_pasien,
          a.tanggal_lahir
        FROM pasien_m a ) pasien ON pendaftaran.pasien_id = pasien.pasien_id 
      LEFT JOIN ( SELECT 
          a.pasienadmisi_id,
          a.kelaspelayanan_id,
          a.penjamin_id
        FROM pasienadmisi_t a ) pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
      WHERE pendaftaran.is_deleted IS FALSE
    ),   gabungpelayanandetail AS (
  SELECT 
    a.pendaftaran_id,
    a.ref_pendaftaran_id,
    b.no_pendaftaran
  FROM 
  gabungpelayanandetail_t a
  LEFT JOIN (SELECT a.pendaftaran_id,a.no_pendaftaran FROM pendaftaran_pasien a ) b 
  ON a.ref_pendaftaran_id = b.pendaftaran_id 
  WHERE 
  a.is_deleted IS FALSE
  ),tindakanpelayanan AS (
      SELECT 
        a.pendaftaran_id,
        a.tarif_tindakan,
        a.ruangan_id,
        a.pasienadmisi_id
      FROM tindakanpelayanan_t a
      WHERE a.is_deleted IS false 
        AND a.tindakansudahbayar_id IS NULL
    ), obatalkespasien AS (
      SELECT 
        a.pendaftaran_id,
        a.pasienadmisi_id,
        a.ruangan_id,
        a.penjualanresep_id,
        a.hargajual_oa
      FROM obatalkespasien_t a
      WHERE a.is_deleted IS false 
        AND a.obatsudahbayar_id IS NULL
    ), total_tagihan AS (
      SELECT tagihans.pendaftaran_id,
        SUM(tagihans.tagihan) AS total_tagihan
      FROM (
        SELECT a.pendaftaran_id,
          SUM(a.tarif_tindakan) AS tagihan
        FROM tindakanpelayanan a
        GROUP BY a.pendaftaran_id
        UNION ALL
        SELECT b.pendaftaran_id,
          SUM(b.hargajual_oa) AS tagihan
        FROM obatalkespasien b
        GROUP BY b.pendaftaran_id
      ) tagihans
      GROUP BY tagihans.pendaftaran_id
    ), 
--     tagihan_gabung AS (
--   SELECT
--     tagihans.pendaftaran_id,
--     SUM ( tagihans.tagihan ) AS total_tagihan 
--   FROM
--     (
--     SELECT A
--       .pendaftaran_id,
--       SUM ( A.tarif_tindakan ) AS tagihan 
--     FROM
--       tindakanpelayanan A 
--     GROUP BY
--       A.pendaftaran_id UNION ALL
--     SELECT
--       b.pendaftaran_id,
--       SUM ( b.hargajual_oa ) AS tagihan 
--     FROM
--       obatalkespasien b 
--     GROUP BY
--       b.pendaftaran_id 
--     ) tagihans 
--   GROUP BY
--     tagihans.pendaftaran_id 
--   ),
  pemberianpiutang AS (
      SELECT 
        a.penjualanresep_id,
        a.pendaftaran_id,
        a.total_piutang,
        a.total_bayarpiutang,
        a.is_deleted,
        a.created_date
      FROM pemberianpiutang_t a
      WHERE a.is_deleted IS FALSE

    ), bayaruangmuka AS (
      SELECT 
        a.pendaftaran_id,
        sum(a.jumlah_uangmuka) AS jumlah_uangmuka
      FROM bayaruangmuka_t a
      WHERE a.is_deleted IS FALSE
      GROUP BY a.pendaftaran_id
    ), ruangan AS (
      SELECT 
        a.ruangan_id, 
        a.ruangan_nama,
        a.instalasi_id
      FROM ruangan_m a
    ), tagihan_ranap AS (
      SELECT tagihans.pendaftaran_id,
        SUM(tagihans.tagihan) AS total_tagihan
      FROM (
        SELECT a.pendaftaran_id,
          SUM(a.tarif_tindakan) AS tagihan
        FROM tindakanpelayanan a
        JOIN ruangan ON ruangan.ruangan_id = a.ruangan_id
        WHERE a.pasienadmisi_id IS NOT NULL AND ruangan.instalasi_id = 3
        GROUP BY a.pendaftaran_id
        UNION ALL
        SELECT b.pendaftaran_id,
          SUM(b.hargajual_oa) AS tagihan
        FROM obatalkespasien b
        JOIN ruangan ON ruangan.ruangan_id = b.ruangan_id
        WHERE b.pasienadmisi_id IS NOT NULL AND ruangan.instalasi_id = 3
        GROUP BY b.pendaftaran_id
      ) tagihans
      GROUP BY tagihans.pendaftaran_id
    ), penjualanresep AS (
      SELECT 
        a.noresep,
        a.tglresep,
        a.penjualanresep_id,
        a.pendaftaran_id,
        a.nama_pembeli,
        a.biayaadministrasi,
        a.status_bayar,
        a.status_reseptur,
        LOWER(CONCAT_WS(' ', a.noresep, a.nama_pembeli)) AS keywords
      FROM penjualanresep_t a
      WHERE a.status_bayar = 349
        AND a.is_deleted IS false 
    ), reseptur AS (
      SELECT
        sum(a.biayaadministrasi) AS total_adm,
        a.pendaftaran_id
      FROM penjualanresep a
      GROUP BY a.pendaftaran_id
    ), tagihan_resep AS (
      SELECT 
        a.penjualanresep_id,
        sum(a.hargajual_oa) AS tagihan_obat
      FROM obatalkespasien a
      GROUP BY a.penjualanresep_id
    ), konfigsystem AS (
      SELECT
        a.is_pembulatankeatas,
        a.adm_tindakan_id,
        a.satuanpembulatan,
        a.is_deleted,
        a.adm_persen
      FROM konfigsystem_k a
      WHERE a.is_deleted IS FALSE
    ), tariftindakan AS (
      SELECT 
        a.tariftindakan_id,
        a.daftartindakan_id,
        a.kelaspelayanan_id,
        a.penjamin_id,
        a.harga_tariftindakan AS tarif_max
      FROM tariftindakan_m a
      JOIN ( SELECT 
          a.adm_tindakan_id
        FROM konfigsystem a) konfigtarif ON a.daftartindakan_id = konfigtarif.adm_tindakan_id
      WHERE a.is_deleted IS false AND a.komponentarif_id = 6
    ) 
    SELECT 
      'tagihan_rs'::text AS jenis,
      pendaftaran_pasien.no_pendaftaran,
      NULL::character varying AS no_resep,
      pendaftaran_pasien.no_rekam_medik,
      pendaftaran_pasien.pendaftaran_id,
      gabungpelayanandetail.ref_pendaftaran_id,
      gabungpelayanandetail.no_pendaftaran as ref_no_pendaftaran,
      CASE 
      WHEN gabungpelayanandetail.ref_pendaftaran_id::BOOLEAN IS NULL THEN
        FALSE
      ELSE
        TRUE
      END AS is_gabungbilling, 
      pendaftaran_pasien.pasienadmisi_id,
      pendaftaran_pasien.pasien_id,
      pendaftaran_pasien.kelaspelayanan_id,
      pendaftaran_pasien.penjamin_id,
      NULL::integer AS penjualanresep_id,
      pendaftaran_pasien.nama_pasien,
--   COALESCE ( total_tagihan.total_tagihan, 0 :: DOUBLE PRECISION ) +
--   COALESCE ( reseptur.total_adm, 0 :: DOUBLE PRECISION ) AS total_tagihan,
--       COALESCE (tagihan_gabung.total_tagihan, 0 :: DOUBLE PRECISION ) + COALESCE ( total_tagihan.total_tagihan, 0 :: DOUBLE PRECISION ) AS total_tagihan,
      COALESCE ( total_tagihan.total_tagihan, 0 :: DOUBLE PRECISION ) AS total_tagihan,
      COALESCE(pemberianpiutang.total_bayarpiutang, 0::double precision) AS piutang_sudahbayar,
      pendaftaran_pasien.tanggal_lahir,
      pendaftaran_pasien.umur,
      pendaftaran_pasien.tgl_pendaftaran,
      COALESCE(pemberianpiutang.total_piutang, 0::double precision) AS total_piutang,
      konfigsystem.adm_persen,
      tariftindakan.tarif_max,
      COALESCE(tagihan_ranap.total_tagihan, 0::double precision) AS tagihan_ranap,

      bayaruangmuka.jumlah_uangmuka AS uang_muka,
      konfigsystem.is_pembulatankeatas,
      konfigsystem.satuanpembulatan,
      (pemberianpiutang.is_deleted = true) AS is_batal,
      pemberianpiutang.created_date
    FROM pendaftaran_pasien
    LEFT JOIN total_tagihan ON pendaftaran_pasien.pendaftaran_id = total_tagihan.pendaftaran_id
    LEFT JOIN pemberianpiutang ON pendaftaran_pasien.pendaftaran_id = pemberianpiutang.pendaftaran_id
    LEFT JOIN bayaruangmuka ON pendaftaran_pasien.pendaftaran_id = bayaruangmuka.pendaftaran_id
    LEFT JOIN konfigsystem ON TRUE
    LEFT JOIN tariftindakan ON tariftindakan.kelaspelayanan_id = pendaftaran_pasien.kelaspelayanan_id AND tariftindakan.penjamin_id = pendaftaran_pasien.penjamin_id
    LEFT JOIN tagihan_ranap ON pendaftaran_pasien.pendaftaran_id = tagihan_ranap.pendaftaran_id
    LEFT JOIN gabungpelayanandetail ON 
    pendaftaran_pasien.pendaftaran_id = gabungpelayanandetail.pendaftaran_id 
    OR pendaftaran_pasien.pendaftaran_id = gabungpelayanandetail.ref_pendaftaran_id
--   LEFT JOIN tagihan_gabung ON gabungpelayanandetail.ref_pendaftaran_id = tagihan_gabung.pendaftaran_id
    LEFT JOIN reseptur ON reseptur.pendaftaran_id = pendaftaran_pasien.pendaftaran_id
    WHERE TRUE 
      AND pendaftaran_pasien.keywords LIKE '%'||LOWER(COALESCE(var_qwords, ''))||'%'
    UNION ALL
    SELECT 
      'resep_bebas'::text AS jenis,
      penjualanresep.noresep AS no_pendaftaran,
      penjualanresep.noresep AS no_resep,
      NULL::character varying AS no_rekam_medik,
      penjualanresep.penjualanresep_id AS pendaftaran_id,
      gabungpelayanandetail.ref_pendaftaran_id,
      gabungpelayanandetail.no_pendaftaran as ref_no_pendaftaran,
      CASE 
      WHEN gabungpelayanandetail.ref_pendaftaran_id::BOOLEAN IS NULL THEN
        FALSE
      ELSE
        TRUE
      END AS is_gabungbilling,
      NULL ::INT AS pasienadmisi_id,
      NULL ::INT AS pasien_id,
      NULL ::INT AS kelaspelayanan_id,
      NULL ::INT AS penjamin_id,
      penjualanresep.penjualanresep_id,
      penjualanresep.nama_pembeli AS nama_pasien,
--       COALESCE ( tagihan_gabung.total_tagihan, 0 :: DOUBLE PRECISION ) + tagihan_resep.tagihan_obat + COALESCE ( penjualanresep.biayaadministrasi, 0 :: DOUBLE PRECISION ) AS total_tagihan,
      tagihan_resep.tagihan_obat + COALESCE ( penjualanresep.biayaadministrasi, 0 :: DOUBLE PRECISION ) AS total_tagihan,
      COALESCE(pemberianpiutang.total_bayarpiutang, 0::double precision) AS piutang_sudahbayar,
      NULL::date AS tanggal_lahir,
      NULL::character varying AS umur,
      penjualanresep.tglresep AS tgl_pendaftaran,
      COALESCE(pemberianpiutang.total_piutang, 0::double precision) AS total_piutang,
      0 AS adm_persen,
      0 AS tarif_max,
      0 AS tagihan_ranap,
      0 AS uang_muka,
      konfigsystem.is_pembulatankeatas,
      konfigsystem.satuanpembulatan,
      (pemberianpiutang.is_deleted = TRUE) AS is_batal,
      pemberianpiutang.created_date
    FROM penjualanresep
    LEFT JOIN tagihan_resep ON penjualanresep.penjualanresep_id = tagihan_resep.penjualanresep_id
    LEFT JOIN gabungpelayanandetail ON penjualanresep.penjualanresep_id = gabungpelayanandetail.pendaftaran_id 
    OR penjualanresep.penjualanresep_id = gabungpelayanandetail.ref_pendaftaran_id 
--     LEFT JOIN tagihan_gabung ON gabungpelayanandetail.ref_pendaftaran_id = tagihan_gabung.pendaftaran_id
    LEFT JOIN pemberianpiutang ON penjualanresep.penjualanresep_id = pemberianpiutang.penjualanresep_id
    LEFT JOIN konfigsystem ON TRUE
    WHERE penjualanresep.status_reseptur <> 432 
      AND penjualanresep.pendaftaran_id IS NULL
      AND penjualanresep.keywords LIKE '%'||LOWER(COALESCE(var_qwords, ''))||'%'
    LIMIT COALESCE(var_limit, 10) OFFSET COALESCE(var_offset, 0);
END; 
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000