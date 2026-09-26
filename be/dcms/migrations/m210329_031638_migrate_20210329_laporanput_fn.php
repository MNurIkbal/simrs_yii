<?php

use yii\db\Migration;

/**
 * Class m210329_031638_migrate_20210329_laporanput_fn
 */
class m210329_031638_migrate_20210329_laporanput_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"laporanput_fn\"(\"xstart_date\" date, \"xend_date\" date)
  RETURNS TABLE(\"urutan\" int4, \"tipe\" varchar, \"tanggal\" date, \"unit\" varchar, \"unit_thruput\" varchar, \"detail_thruput\" varchar, \"total\" float8) AS \$BODY\$
  BEGIN
    RETURN QUERY 
SELECT *FROM (
----------------------------------------------------------LOB-----------------------------------------------------------        
    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  x.total AS total
FROM (          
    SELECT
      1 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    OPD'::VARCHAR AS unit,
        null::VARCHAR AS unit_thruput,
        null::VARCHAR AS detail_thruput,
        COALESCE(pasien_rj.total, 0)::float8  AS total
    FROM (
        SELECT CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, 
             date (xend_date)  - CURRENT_DATE ) i
    ) AS t_date
        LEFT JOIN (
        SELECT 
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
            COUNT(pendaftaran_t.pendaftaran_id) AS total
        FROM pendaftaran_t
        WHERE pendaftaran_t.instalasi_id = 1 --RJ
        AND pendaftaran_t.is_active = TRUE 
        AND pendaftaran_t.is_deleted = FALSE
        AND pendaftaran_t.pasienbatalperiksa_id IS NULL
        GROUP BY to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date
    ) pasien_rj ON t_date.tgl_generate = pasien_rj.tanggal
    ) AS x

UNION ALL

    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  SUM((COALESCE(
    (
      SELECT 
        SUM(pasien_akhir) AS pasien_akhir
      FROM sensuspasienranap_r
      WHERE id IN (SELECT max(id) FROM sensuspasienranap_r WHERE sensuspasienranap_r.tgl_sensus < x.tanggal GROUP BY ruangan_id,kelaspelayanan_id)
    )
  , 0) + COALESCE(x.total,0)))::float8 AS total
FROM (
SELECT
    2 as urutan,
    'LOB'::VARCHAR AS tipe,
    tgl_generate AS tanggal,
    '    IPD TOTAL'::VARCHAR AS unit,
    null::VARCHAR AS unit_thruput,
    null::VARCHAR AS detail_thruput,
        SUM((COALESCE(pasien_masuk.total, 0) + COALESCE(pasien_pindahan.total, 0) - COALESCE(pasien_keluarhidup.total,0) - COALESCE(pasien_keluardipindahkan.total,0) - COALESCE(pasien_keluarmeninggalkur48.total,0) - COALESCE(pasien_keluarmeninggalleb48.total,0)))::float8  AS total
  FROM (
    SELECT CURRENT_DATE + i AS tgl_generate
    FROM generate_series(date (xstart_date) - CURRENT_DATE, 
       date (xend_date)  - CURRENT_DATE ) i
  ) AS t_date
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_masuk) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_masuk ON t_date.tgl_generate = pasien_masuk.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_pindahan) AS total
    FROM sensuspasienranap_r
    GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_pindahan ON t_date.tgl_generate = pasien_pindahan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarhidup) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarhidup ON t_date.tgl_generate = pasien_keluarhidup.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluardipindahkan) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluardipindahkan ON t_date.tgl_generate = pasien_keluardipindahkan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalkur48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalkur48 ON t_date.tgl_generate = pasien_keluarmeninggalkur48.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalleb48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalleb48 ON t_date.tgl_generate = pasien_keluarmeninggalleb48.tanggal
    GROUP BY tgl_generate
) AS x
    WHERE x.tanggal <= CURRENT_DATE
    GROUP BY 
    x.urutan,
  x.tipe,
  x.tanggal,
  x.unit,
  x.unit_thruput,
    x.detail_thruput
        
UNION ALL

SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  x.total AS total
FROM (  
    SELECT
        3 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    IPD ADMISSION'::VARCHAR AS unit,
        null::VARCHAR AS unit_thruput,
        null::VARCHAR AS detail_thruput,
        COALESCE(masuk_ri.total, 0)::float8  AS total
    FROM (
        SELECT CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, 
             date (xend_date)  - CURRENT_DATE ) i
    ) AS t_date
    LEFT JOIN (
        SELECT 
            to_char(sensuspasienranap_r.tgl_sensus, 'YYYY-MM-DD'::text)::date AS tanggal,
            sensuspasienranap_r.pasien_masuk AS total
        FROM sensuspasienranap_r
    ) masuk_ri ON t_date.tgl_generate = masuk_ri.tanggal
    ) AS x
-- 
UNION ALL
    
    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  SUM((COALESCE(
    (
      SELECT 
        SUM(pasien_akhir) AS pasien_akhir
      FROM sensuspasienranap_r
      WHERE id IN (SELECT max(id) FROM sensuspasienranap_r WHERE sensuspasienranap_r.tgl_sensus < x.tanggal GROUP BY ruangan_id,kelaspelayanan_id)
    )
  , 0) + COALESCE(x.total,0)))::float8 AS total
FROM (
SELECT
    4 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    PATIENT DAYS'::VARCHAR AS unit,
    null::VARCHAR AS unit_thruput,
    null::VARCHAR AS detail_thruput,
        SUM((COALESCE(pasien_masuk.total, 0) + COALESCE(pasien_pindahan.total, 0) - COALESCE(pasien_keluarhidup.total,0) - COALESCE(pasien_keluardipindahkan.total,0) - COALESCE(pasien_keluarmeninggalkur48.total,0) - COALESCE(pasien_keluarmeninggalleb48.total,0)))::float8  AS total
  FROM (
    SELECT CURRENT_DATE + i AS tgl_generate
    FROM generate_series(date (xstart_date) - CURRENT_DATE, 
       date (xend_date)  - CURRENT_DATE ) i
  ) AS t_date
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_masuk) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_masuk ON t_date.tgl_generate = pasien_masuk.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_pindahan) AS total
    FROM sensuspasienranap_r
    GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_pindahan ON t_date.tgl_generate = pasien_pindahan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarhidup) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarhidup ON t_date.tgl_generate = pasien_keluarhidup.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluardipindahkan) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluardipindahkan ON t_date.tgl_generate = pasien_keluardipindahkan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalkur48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalkur48 ON t_date.tgl_generate = pasien_keluarmeninggalkur48.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalleb48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalleb48 ON t_date.tgl_generate = pasien_keluarmeninggalleb48.tanggal
    GROUP BY tgl_generate
) AS x
    WHERE x.tanggal <= CURRENT_DATE
    GROUP BY 
    x.urutan,
  x.tipe,
  x.tanggal,
  x.unit,
  x.unit_thruput,
    x.detail_thruput
            
UNION ALL

    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  x.total AS total
    FROM (
    SELECT
        5 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    IPD DISCHARGE'::VARCHAR AS unit,
        null::VARCHAR AS unit_thruput,
        null::VARCHAR AS detail_thruput,
        SUM((COALESCE(pasien_keluarhidup.total,0) + COALESCE(pasien_keluarmeninggalkur48.total,0) + COALESCE(pasien_keluarmeninggalleb48.total,0)))::float8  AS total
    FROM (
        SELECT CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, 
             date (xend_date)  - CURRENT_DATE ) i
    ) AS t_date
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarhidup) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarhidup ON t_date.tgl_generate = pasien_keluarhidup.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalkur48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalkur48 ON t_date.tgl_generate = pasien_keluarmeninggalkur48.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalleb48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalleb48 ON t_date.tgl_generate = pasien_keluarmeninggalleb48.tanggal
    GROUP BY tgl_generate
    ) AS x
    
UNION ALL

    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  x.total AS total
    FROM (
    SELECT
        6 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    MORTALITY'::VARCHAR AS unit,
        null::VARCHAR AS unit_thruput,
        null::VARCHAR AS detail_thruput,
        SUM((COALESCE(pasien_keluarmeninggalkur48.total,0) + COALESCE(pasien_keluarmeninggalleb48.total,0)))::float8  AS total
    FROM (
        SELECT CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, 
             date (xend_date)  - CURRENT_DATE ) i
    ) AS t_date
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalkur48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalkur48 ON t_date.tgl_generate = pasien_keluarmeninggalkur48.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalleb48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalleb48 ON t_date.tgl_generate = pasien_keluarmeninggalleb48.tanggal
    GROUP BY tgl_generate
    ) AS x
--  
UNION ALL

    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  x.total AS total
    FROM (
    SELECT
        7 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    BED COUNT'::VARCHAR AS unit,
        null::VARCHAR AS unit_thruput,
        null::VARCHAR AS detail_thruput,
        (SELECT
            COUNT(kamartempattidur_m.kamartempattidur_id) AS jumlah_bed
        FROM kamartempattidur_m
        JOIN kamarruangan_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id 
        WHERE kamartempattidur_m.is_active=TRUE 
        AND kamartempattidur_m.is_deleted=FALSE
        AND kamarruangan_m.is_deleted=FALSE)::float8 AS total
    FROM (
        SELECT CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, 
             date (xend_date)  - CURRENT_DATE ) i
    ) AS t_date
    ) AS x
    WHERE x.tanggal <= CURRENT_DATE
--  
UNION ALL

--  SELECT 
--   x.urutan AS urutan,
--   x.tipe AS tipe,
--   x.tanggal AS tanggal,
--   x.unit AS unit,
--   x.unit_thruput AS unit_thruput,
--  x.detail_thruput AS detail_thruput,
--   x.total AS total
--  FROM (
--  SELECT
--      8 as urutan,
--      'LOB'::VARCHAR AS tipe,
--      tgl_generate AS tanggal,
--      '    BOR'::VARCHAR AS unit,
--      null::VARCHAR AS unit_thruput,
--      null::VARCHAR AS detail_thruput,
--      0::float8 AS total
--  FROM (
--      SELECT CURRENT_DATE + i AS tgl_generate
--      FROM generate_series(date (xstart_date) - CURRENT_DATE, 
--           date (xend_date)  - CURRENT_DATE ) i
--  ) AS t_date
--  ) AS x
    
    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  NULLIF(SUM((COALESCE(
    (
      SELECT 
        SUM(pasien_akhir) AS pasien_akhir
      FROM sensuspasienranap_r
      WHERE id IN (SELECT max(id) FROM sensuspasienranap_r WHERE sensuspasienranap_r.tgl_sensus < x.tanggal GROUP BY ruangan_id,kelaspelayanan_id)
    )
  , 0) + COALESCE(x.total,0)))::float8, 0) / NULLIF(SUM((COALESCE((SELECT  
    DATE_PART('days', 
        DATE_TRUNC('month', NOW()) 
        + '1 MONTH'::INTERVAL 
        - '1 DAY'::INTERVAL
    )),0)))::float8 ,0) * NULLIF(SUM((COALESCE((SELECT
            COUNT(kamartempattidur_m.kamartempattidur_id) AS jumlah_bed
        FROM kamartempattidur_m
        JOIN kamarruangan_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id 
        WHERE kamartempattidur_m.is_active=TRUE 
        AND kamartempattidur_m.is_deleted=FALSE
        AND kamarruangan_m.is_deleted=FALSE),0)))::float8, 0) AS total
FROM (
SELECT
        8 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    BOR'::VARCHAR AS unit,
    null::VARCHAR AS unit_thruput,
    null::VARCHAR AS detail_thruput,
        SUM((COALESCE(pasien_masuk.total, 0) + COALESCE(pasien_pindahan.total, 0) - COALESCE(pasien_keluarhidup.total,0) - COALESCE(pasien_keluardipindahkan.total,0) - COALESCE(pasien_keluarmeninggalkur48.total,0) - COALESCE(pasien_keluarmeninggalleb48.total,0)))::float8  AS total
  FROM (
    SELECT CURRENT_DATE + i AS tgl_generate
    FROM generate_series(date (xstart_date) - CURRENT_DATE, 
       date (xend_date)  - CURRENT_DATE ) i
  ) AS t_date
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_masuk) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_masuk ON t_date.tgl_generate = pasien_masuk.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_pindahan) AS total
    FROM sensuspasienranap_r
    GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_pindahan ON t_date.tgl_generate = pasien_pindahan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarhidup) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarhidup ON t_date.tgl_generate = pasien_keluarhidup.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluardipindahkan) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluardipindahkan ON t_date.tgl_generate = pasien_keluardipindahkan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalkur48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalkur48 ON t_date.tgl_generate = pasien_keluarmeninggalkur48.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalleb48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalleb48 ON t_date.tgl_generate = pasien_keluarmeninggalleb48.tanggal
    GROUP BY tgl_generate
) AS x
    WHERE x.tanggal <= CURRENT_DATE
    GROUP BY 
    x.urutan,
  x.tipe,
  x.tanggal,
  x.unit,
  x.unit_thruput,
    x.detail_thruput
--  
UNION ALL
    
SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  NULLIF(SUM((COALESCE(
    (
      SELECT 
        SUM(pasien_akhir) AS pasien_akhir
      FROM sensuspasienranap_r
      WHERE id IN (SELECT max(id) FROM sensuspasienranap_r WHERE sensuspasienranap_r.tgl_sensus < x.tanggal GROUP BY ruangan_id,kelaspelayanan_id)
    )
  , 0) + COALESCE(x.total,0)))::float8,0) / NULLIF(SUM((COALESCE(x.totalpas_keluar, 0)))::float8,0) AS total
FROM (
SELECT
        9 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    AVERAGE LENGTH OF STAY'::VARCHAR AS unit,
    null::VARCHAR AS unit_thruput,
    null::VARCHAR AS detail_thruput,
        SUM(((COALESCE(pasien_masuk.total, 0) + COALESCE(pasien_pindahan.total, 0))))::float8  AS total,
        SUM(((COALESCE(pasien_keluarhidup.total, 0) + COALESCE(pasien_keluardipindahkan.total, 0) + COALESCE(pasien_keluarmeninggalkur48.total, 0) + COALESCE(pasien_keluarmeninggalleb48.total, 0))))::float8  AS totalpas_keluar
  FROM (
    SELECT CURRENT_DATE + i AS tgl_generate
    FROM generate_series(date (xstart_date) - CURRENT_DATE, 
       date (xend_date)  - CURRENT_DATE ) i
  ) AS t_date
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_masuk) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_masuk ON t_date.tgl_generate = pasien_masuk.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_pindahan) AS total
    FROM sensuspasienranap_r
    GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_pindahan ON t_date.tgl_generate = pasien_pindahan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarhidup) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarhidup ON t_date.tgl_generate = pasien_keluarhidup.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluardipindahkan) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluardipindahkan ON t_date.tgl_generate = pasien_keluardipindahkan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalkur48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalkur48 ON t_date.tgl_generate = pasien_keluarmeninggalkur48.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalleb48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalleb48 ON t_date.tgl_generate = pasien_keluarmeninggalleb48.tanggal
    GROUP BY tgl_generate
) AS x
    WHERE x.tanggal <= CURRENT_DATE
    GROUP BY 
    x.urutan,
  x.tipe,
  x.tanggal,
  x.unit,
  x.unit_thruput,
    x.detail_thruput
--  
UNION ALL

--  SELECT 
--   x.urutan AS urutan,
--   x.tipe AS tipe,
--   x.tanggal AS tanggal,
--   x.unit AS unit,
--   x.unit_thruput AS unit_thruput,
--  x.detail_thruput AS detail_thruput,
--   x.total AS total
--  FROM (
--  SELECT
--      10 as urutan,
--      'LOB'::VARCHAR AS tipe,
--      tgl_generate AS tanggal,
--      '    BED TURN OVER INTERVAL'::VARCHAR AS unit,
--      null::VARCHAR AS unit_thruput,
--      null::VARCHAR AS detail_thruput,
--      0::float8 AS total
--  FROM (
--      SELECT CURRENT_DATE + i AS tgl_generate
--      FROM generate_series(date (xstart_date) - CURRENT_DATE, 
--           date (xend_date)  - CURRENT_DATE ) i
--  ) AS t_date
--  ) AS x
    
    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
--   x.total AS total
    SUM((COALESCE(x.total,0) / COALESCE((SELECT
            COUNT(kamartempattidur_m.kamartempattidur_id) AS jumlah_bed
        FROM kamartempattidur_m
        JOIN kamarruangan_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id 
        WHERE kamartempattidur_m.is_active=TRUE 
        AND kamartempattidur_m.is_deleted=FALSE
        AND kamarruangan_m.is_deleted=FALSE))))::float8 AS total
    FROM (
    SELECT
        10 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    BED TURN OVER INTERVAL'::VARCHAR AS unit,
        null::VARCHAR AS unit_thruput,
        null::VARCHAR AS detail_thruput,
        SUM((COALESCE(pasien_keluarhidup.total,0) + COALESCE(pasien_keluardipindahkan.total,0) + COALESCE(pasien_keluarmeninggalkur48.total,0) + COALESCE(pasien_keluarmeninggalleb48.total,0)))::float8  AS total
    FROM (
        SELECT CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, 
             date (xend_date)  - CURRENT_DATE ) i
    ) AS t_date
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarhidup) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarhidup ON t_date.tgl_generate = pasien_keluarhidup.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluardipindahkan) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluardipindahkan ON t_date.tgl_generate = pasien_keluardipindahkan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalkur48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalkur48 ON t_date.tgl_generate = pasien_keluarmeninggalkur48.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalleb48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalleb48 ON t_date.tgl_generate = pasien_keluarmeninggalleb48.tanggal
    GROUP BY tgl_generate
    ) AS x
    WHERE x.tanggal <= CURRENT_DATE
    GROUP BY 
    x.urutan,
  x.tipe,
  x.tanggal,
  x.unit,
  x.unit_thruput,
    x.detail_thruput
    
--  
UNION ALL

    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  x.total AS total
FROM (  
    SELECT
        11 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    EMERGENCY'::VARCHAR AS unit,
        null::VARCHAR AS unit_thruput,
        null::VARCHAR AS detail_thruput,
        COALESCE(pasien_rd.total, 0)::float8 AS total
    FROM (
        SELECT CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, 
             date (xend_date)  - CURRENT_DATE ) i
    ) AS t_date
    LEFT JOIN (
        SELECT 
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
            COUNT(pendaftaran_t.pendaftaran_id) AS total
        FROM pendaftaran_t
        WHERE pendaftaran_t.instalasi_id = 2 --RD
        AND pendaftaran_t.is_active = TRUE 
        AND pendaftaran_t.is_deleted = FALSE
        AND pendaftaran_t.pasienbatalperiksa_id IS NULL
        AND pendaftaran_t.status_periksa <> '402'
        GROUP BY to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date
    ) pasien_rd ON t_date.tgl_generate = pasien_rd.tanggal
    ) AS x
    
UNION ALL

    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  x.total AS total
FROM (
    SELECT
        12 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    MCU'::VARCHAR AS unit,
        null::VARCHAR AS unit_thruput,
        null::VARCHAR AS detail_thruput,
        COALESCE(pasien_mcu.total, 0)::float8 AS total
    FROM (
        SELECT CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, 
             date (xend_date)  - CURRENT_DATE ) i
    ) AS t_date
    LEFT JOIN (
        SELECT 
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
            COUNT(pendaftaran_t.pendaftaran_id) AS total
        FROM pendaftaran_t
        WHERE pendaftaran_t.instalasi_id = 21 --MCU
        AND pendaftaran_t.is_active = TRUE 
        AND pendaftaran_t.is_deleted = FALSE
        AND pendaftaran_t.pasienbatalperiksa_id IS NULL
        AND pendaftaran_t.status_periksa <> '402'
        GROUP BY to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date
    ) pasien_mcu ON t_date.tgl_generate = pasien_mcu.tanggal
    ) AS x
    
UNION ALL
    
    SELECT 
  x.urutan AS urutan,
  x.tipe AS tipe,
  x.tanggal AS tanggal,
  x.unit AS unit,
  x.unit_thruput AS unit_thruput,
    x.detail_thruput AS detail_thruput,
  SUM((COALESCE(
    (
      SELECT 
        SUM(pasien_akhir) AS pasien_akhir
      FROM sensuspasienranap_r
      WHERE id IN (SELECT max(id) FROM sensuspasienranap_r WHERE sensuspasienranap_r.tgl_sensus < x.tanggal GROUP BY ruangan_id,kelaspelayanan_id)
    )
  , 0) + COALESCE(x.total,0)))::float8 AS total
FROM (
SELECT
    13 as urutan,
        'LOB'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '    TOTAL VISITS OF LOB'::VARCHAR AS unit,
    null::VARCHAR AS unit_thruput,
    null::VARCHAR AS detail_thruput,
        SUM((COALESCE(pasien_masuk.total, 0) + COALESCE(pasien_pindahan.total, 0) - COALESCE(pasien_keluarhidup.total,0) - COALESCE(pasien_keluardipindahkan.total,0) - COALESCE(pasien_keluarmeninggalkur48.total,0) - COALESCE(pasien_keluarmeninggalleb48.total,0) + COALESCE(pasien_rj.total,0) + COALESCE(pasien_rd.total,0) + COALESCE(pasien_mcu.total,0)))::float8  AS total
  FROM (
    SELECT CURRENT_DATE + i AS tgl_generate
    FROM generate_series(date (xstart_date) - CURRENT_DATE, 
       date (xend_date)  - CURRENT_DATE ) i
  ) AS t_date
    -- IPD TOTAL --
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_masuk) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_masuk ON t_date.tgl_generate = pasien_masuk.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_pindahan) AS total
    FROM sensuspasienranap_r
    GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_pindahan ON t_date.tgl_generate = pasien_pindahan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarhidup) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarhidup ON t_date.tgl_generate = pasien_keluarhidup.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluardipindahkan) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluardipindahkan ON t_date.tgl_generate = pasien_keluardipindahkan.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalkur48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalkur48 ON t_date.tgl_generate = pasien_keluarmeninggalkur48.tanggal
    LEFT JOIN (
        SELECT sensuspasienranap_r.tgl_sensus AS tanggal,
            SUM(pasien_keluarmeninggalleb48) AS total
        FROM sensuspasienranap_r
        GROUP BY sensuspasienranap_r.tgl_sensus
    ) pasien_keluarmeninggalleb48 ON t_date.tgl_generate = pasien_keluarmeninggalleb48.tanggal
    -- OPD --
    LEFT JOIN (
        SELECT 
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
            COUNT(pendaftaran_t.pendaftaran_id) AS total
        FROM pendaftaran_t
        WHERE pendaftaran_t.instalasi_id = 1 --RJ
        AND pendaftaran_t.is_active = TRUE 
        AND pendaftaran_t.is_deleted = FALSE
        AND pendaftaran_t.pasienbatalperiksa_id IS NULL
        GROUP BY to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date
    ) pasien_rj ON t_date.tgl_generate = pasien_rj.tanggal
    -- EMERGENCY --
    LEFT JOIN (
        SELECT 
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
            COUNT(pendaftaran_t.pendaftaran_id) AS total
        FROM pendaftaran_t
        WHERE pendaftaran_t.instalasi_id = 2 --RD
        AND pendaftaran_t.is_active = TRUE 
        AND pendaftaran_t.is_deleted = FALSE
        AND pendaftaran_t.pasienbatalperiksa_id IS NULL
        AND pendaftaran_t.status_periksa <> '402'
        GROUP BY to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date
    ) pasien_rd ON t_date.tgl_generate = pasien_rd.tanggal
    -- MCU --
    LEFT JOIN (
        SELECT 
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
            COUNT(pendaftaran_t.pendaftaran_id) AS total
        FROM pendaftaran_t
        WHERE pendaftaran_t.instalasi_id = 21 --MCU
        AND pendaftaran_t.is_active = TRUE 
        AND pendaftaran_t.is_deleted = FALSE
        AND pendaftaran_t.pasienbatalperiksa_id IS NULL
        AND pendaftaran_t.status_periksa <> '402'
        GROUP BY to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date
    ) pasien_mcu ON t_date.tgl_generate = pasien_mcu.tanggal
    GROUP BY tgl_generate
) AS x
    WHERE x.tanggal <= CURRENT_DATE
    GROUP BY 
    x.urutan,
  x.tipe,
  x.tanggal,
  x.unit,
  x.unit_thruput,
    x.detail_thruput
    
        
            ) x
        ORDER BY x.tanggal;
END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000;");

        $this->execute('ALTER FUNCTION "public"."laporanput_fn"("xstart_date" date, "xend_date" date) OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210329_031638_migrate_20210329_laporanput_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210329_031638_migrate_20210329_laporanput_fn cannot be reverted.\n";

        return false;
    }
    */
}
