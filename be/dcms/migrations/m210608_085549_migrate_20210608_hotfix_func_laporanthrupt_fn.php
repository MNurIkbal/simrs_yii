<?php

use yii\db\Migration;

/**
 * Class m210608_085549_migrate_20210608_hotfix_func_laporanthrupt_fn
 */
class m210608_085549_migrate_20210608_hotfix_func_laporanthrupt_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.laporanthrupt_fn;
        ");
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"laporanthrupt_fn\"(\"xstart_date\" date, \"xend_date\" date)
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
        --AND pendaftaran_t.pasienbatalperiksa_id IS NULL
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
    
UNION ALL   
----------------------------------------------------------LOS-------------------------------------------------------------              
    SELECT
        15 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  PHARMACY'::VARCHAR AS unit,
        '      SHEETS'::VARCHAR AS unit_thruput,
        '          OPD' AS detail_thruput,
        COALESCE(far.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT
                        a.tanggal,
                        COUNT(a.total) as total
                    FROM
                    ( SELECT
                            reseptur_t.tglreseptur::date as tanggal,
                            reseptur_t.noresep as total
                        FROM reseptur_t
                            JOIN penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id 
                            JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =1
                            WHERE reseptur_t.is_deleted=FALSE
                            AND reseptur_t.status_reseptur <> '432'
                            AND penjualanresep_t.status_reseptur = '660'
                        UNION ALL
                        SELECT
                            pendaftaran_t.tgl_pendaftaran::DATE as tanggal,
                            obatalkespasien_t.pendaftaran_id::VARCHAR as total
                        FROM obatalkespasien_t
                            JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id =1
                            JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id=6
                        WHERE obatalkespasien_t.is_deleted=FALSE
                            AND obatalkespasien_t.penjualanresep_id IS NULL 
                      GROUP BY pendaftaran_t.tgl_pendaftaran::DATE,
                                         obatalkespasien_t.pendaftaran_id::VARCHAR  
                        UNION ALL
                        SELECT
                            penjualanresep_t.tglresep::DATE as tanggal,
                            penjualanresep_t.noresep as total
                        FROM penjualanresep_t
                            JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id=1
                        WHERE penjualanresep_t.is_deleted=FALSE
                            AND penjualanresep_t.reseptur_id IS NULL
                            AND penjualanresep_t.status_reseptur = '660'
                        ) a
                    GROUP BY a.tanggal
    ) far ON t_date.tgl_generate = far.tanggal

UNION ALL

SELECT
        16 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  PHARMACY'::VARCHAR AS unit,
        '      SHEETS'::VARCHAR AS unit_thruput,
        '          IGD' AS detail_thruput,
        COALESCE(far.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
          SELECT
                        a.tanggal,
                        COUNT(a.total) as total
                    FROM
                    ( SELECT
                            reseptur_t.tglreseptur::date as tanggal,
                            reseptur_t.noresep as total
                        FROM reseptur_t
                            JOIN penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id 
                            JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =2
                            WHERE reseptur_t.is_deleted=FALSE
                            AND reseptur_t.status_reseptur <> '432'
                            AND penjualanresep_t.status_reseptur = '660'
                        UNION ALL
                        SELECT
                            obatalkespasien_t.tglpelayanan::DATE as tanggal,
                            obatalkespasien_t.implementasi_id::VARCHAR as total
                        FROM obatalkespasien_t
                            JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                            JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id=6
                            JOIN implementasi_t ON obatalkespasien_t.implementasi_id = implementasi_t.implementasi_id
                            JOIN instruksi_t ON implementasi_t.instruksi_id = instruksi_t.instruksi_id
                            JOIN cppt_t ON instruksi_t.cppt_id = cppt_t.cppt_id
                            JOIN ruangan_m ruangan_cppt ON cppt_t.ruangan_id = ruangan_cppt.ruangan_id AND ruangan_cppt.instalasi_id=2
                        WHERE obatalkespasien_t.is_deleted=FALSE
                            AND obatalkespasien_t.penjualanresep_id IS NULL 
                        GROUP BY obatalkespasien_t.tglpelayanan::DATE,
                                        obatalkespasien_t.implementasi_id::VARCHAR  
                        UNION ALL
                        SELECT
                            penjualanresep_t.tglresep::DATE as tanggal,
                            penjualanresep_t.noresep as total
                        FROM penjualanresep_t
                            JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id=2
                        WHERE penjualanresep_t.is_deleted=FALSE
                          AND penjualanresep_t.pasienadmisi_id IS NULL
                            AND penjualanresep_t.reseptur_id IS NULL
                            AND penjualanresep_t.status_reseptur = '660'
                        ) a
                    GROUP BY a.tanggal          
    ) far ON t_date.tgl_generate = far.tanggal

UNION ALL

    SELECT
        17 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  PHARMACY'::VARCHAR AS unit,
        '      SHEETS'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(far.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT
                        a.tanggal,
                        COUNT(a.total) as total
                    FROM
                    ( SELECT
                            reseptur_t.tglreseptur::date as tanggal,
                            reseptur_t.noresep as total
                        FROM reseptur_t
                            JOIN penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id 
                            JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =3
                            WHERE reseptur_t.is_deleted=FALSE
                            AND reseptur_t.status_reseptur <> '432'
                            AND penjualanresep_t.status_reseptur = '660'
                        UNION ALL
                        SELECT
                            obatalkespasien_t.tglpelayanan::DATE as tanggal,
                            obatalkespasien_t.implementasi_id::VARCHAR as total
                        FROM obatalkespasien_t
                            JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                            JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id=6
                            JOIN implementasi_t ON obatalkespasien_t.implementasi_id = implementasi_t.implementasi_id
                            JOIN instruksi_t ON implementasi_t.instruksi_id = instruksi_t.instruksi_id
                            JOIN cppt_t ON instruksi_t.cppt_id = cppt_t.cppt_id
                            JOIN ruangan_m ruangan_cppt ON cppt_t.ruangan_id = ruangan_cppt.ruangan_id AND ruangan_cppt.instalasi_id=3
                        WHERE obatalkespasien_t.is_deleted=FALSE
                            AND obatalkespasien_t.penjualanresep_id IS NULL 
                        GROUP BY obatalkespasien_t.tglpelayanan::DATE,
                                        obatalkespasien_t.implementasi_id::VARCHAR  
                        UNION ALL
                        SELECT
                            penjualanresep_t.tglresep::DATE as tanggal,
                            penjualanresep_t.noresep as total
                        FROM penjualanresep_t
                            JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        WHERE penjualanresep_t.is_deleted=FALSE
                          AND penjualanresep_t.pasienadmisi_id IS NOT NULL
                            AND penjualanresep_t.reseptur_id IS NULL
                            AND penjualanresep_t.status_reseptur = '660'
                        ) a
                    GROUP BY a.tanggal 
    ) far ON t_date.tgl_generate = far.tanggal

UNION ALL

    SELECT
        18 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  PHARMACY'::VARCHAR AS unit,
        '      SHEETS'::VARCHAR AS unit_thruput,
        '          MCU' AS detail_thruput,
        COALESCE(far.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT
                        a.tanggal,
                        COUNT(a.total) as total
                    FROM
                    ( SELECT
                            reseptur_t.tglreseptur::date as tanggal,
                            reseptur_t.noresep as total
                        FROM reseptur_t
                            JOIN penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id 
                            JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =21
                            WHERE reseptur_t.is_deleted=FALSE
                            AND reseptur_t.status_reseptur <> '432'
                            AND penjualanresep_t.status_reseptur = '660'
                        UNION ALL
                        SELECT
                            obatalkespasien_t.tglpelayanan::DATE as tanggal,
                            pendaftaran_t.no_pendaftaran as total
                        FROM obatalkespasien_t
                            JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id=21
                            JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id=6
                        WHERE obatalkespasien_t.is_deleted=FALSE
                            AND obatalkespasien_t.penjualanresep_id IS NULL 
                        UNION ALL
                        SELECT
                            penjualanresep_t.tglresep::DATE as tanggal,
                            penjualanresep_t.noresep as total
                        FROM penjualanresep_t
                            JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id=21
                        WHERE penjualanresep_t.is_deleted=FALSE
                            AND penjualanresep_t.reseptur_id IS NULL
                            AND penjualanresep_t.status_reseptur = '660'
                        ) a
                    GROUP BY a.tanggal 
    ) far ON t_date.tgl_generate = far.tanggal
    
UNION ALL

    SELECT
        19 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  PHARMACY'::VARCHAR AS unit,
        '      SHEETS'::VARCHAR AS unit_thruput,
        '          OTC' AS detail_thruput,
        COALESCE(far.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT
                penjualanresep_t.tglresep::date as tanggal,
                count(penjualanresep_t.noresep) as total
            FROM penjualanresep_t
            WHERE penjualanresep_t.is_deleted=FALSE 
                            AND penjualanresep_t.jenispenjualan = '343'
                            AND penjualanresep_t.status_reseptur = '660'
            GROUP BY penjualanresep_t.tglresep::date                    
        ) far ON t_date.tgl_generate = far.tanggal  

UNION ALL

    SELECT
        20 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  PHARMACY'::VARCHAR AS unit,
        '      R/'::VARCHAR AS unit_thruput,
        '          OPD' AS detail_thruput,
        COALESCE(far.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT
                        b.tanggal,
                        count(b.total) as total
                    FROM(               
                    SELECT
                            a.tanggal,
                            a.total as total
                    FROM
            (
                        SELECT
                                reseptur_t.tglreseptur::date as tanggal,
                                obatalkespasien_t.obatalkes_id as total
                        FROM penjualanresep_t
                                JOIN reseptur_t ON penjualanresep_t.reseptur_id = reseptur_t.reseptur_id
                                JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id 
                                JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =1
                         WHERE obatalkespasien_t.is_deleted = FALSE 
                            AND penjualanresep_t.status_reseptur = '660'
                            AND reseptur_t.status_reseptur <> '432'
            UNION ALL
                        SELECT
                            pendaftaran_t.tgl_pendaftaran::DATE as tanggal,
                            obatalkespasien_t.obatalkes_id as total
                        FROM obatalkespasien_t
                            JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id=1
                            JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id=6
                        WHERE obatalkespasien_t.is_deleted=FALSE
                            AND obatalkespasien_t.penjualanresep_id IS NULL
                        UNION ALL       
                        SELECT
                            penjualanresep_t.tglresep::DATE as tanggal,
                            obatalkespasien_t.obatalkes_id as total
                        FROM penjualanresep_t
                            JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id=1
                            JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
                        WHERE penjualanresep_t.is_deleted=FALSE AND obatalkespasien_t.is_deleted = FALSE 
                            AND penjualanresep_t.reseptur_id IS NULL
                            AND penjualanresep_t.status_reseptur = '660'
                    ) a
           GROUP BY a.tanggal,
                                        a.total) b
                    GROUP BY b.tanggal
        ) far ON t_date.tgl_generate = far.tanggal  

UNION ALL
        
    SELECT
        21 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  PHARMACY'::VARCHAR AS unit,
        '      R/'::VARCHAR AS unit_thruput,
        '          IGD' AS detail_thruput,
        COALESCE(far.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                SELECT
                    b.tanggal,
                    COUNT(b.total) as total 
                FROM
                    (SELECT
                        a.tanggal,
                        a.total as total
                    FROM
            (SELECT
                                reseptur_t.tglreseptur::date as tanggal,
                                obatalkespasien_t.obatalkes_id as total
                        FROM penjualanresep_t
                                JOIN reseptur_t ON penjualanresep_t.reseptur_id = reseptur_t.reseptur_id
                                JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id 
                                JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =2
                         WHERE obatalkespasien_t.is_deleted = FALSE 
                            AND penjualanresep_t.status_reseptur = '660'
                            AND reseptur_t.status_reseptur <> '432'
                         UNION ALL
                        SELECT
                            obatalkespasien_t.tglpelayanan::DATE as tanggal,
                            obatalkespasien_t.obatalkes_id as total
                        FROM obatalkespasien_t
                            JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                            JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id=6
                            JOIN implementasi_t ON obatalkespasien_t.implementasi_id = implementasi_t.implementasi_id
                            JOIN instruksi_t ON implementasi_t.instruksi_id = instruksi_t.instruksi_id
                            JOIN cppt_t ON instruksi_t.cppt_id = cppt_t.cppt_id
                            JOIN ruangan_m ruangan_cppt ON cppt_t.ruangan_id = ruangan_cppt.ruangan_id AND ruangan_cppt.instalasi_id=2
                        WHERE obatalkespasien_t.is_deleted=FALSE
                            AND obatalkespasien_t.penjualanresep_id IS NULL
                        UNION ALL       
                        SELECT
                            penjualanresep_t.tglresep::DATE as tanggal,
                            obatalkespasien_t.obatalkes_id as total
                        FROM penjualanresep_t
                            JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id=2
                            JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
                        WHERE penjualanresep_t.is_deleted=FALSE AND obatalkespasien_t.is_deleted = FALSE 
                            AND penjualanresep_t.pasienadmisi_id IS NULL
                            AND penjualanresep_t.reseptur_id IS NULL
                            AND penjualanresep_t.status_reseptur = '660'
                    ) a
           GROUP BY a.tanggal,
                                        a.total) b
                        GROUP BY b.tanggal
        ) far ON t_date.tgl_generate = far.tanggal  
        
UNION ALL

    SELECT
        22 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  PHARMACY'::VARCHAR AS unit,
        '      R/'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(far.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
         SELECT
                    b.tanggal,
                    COUNT(b.total) as total
                 FROM                
                        (SELECT
                        a.tanggal,
                        a.total as total
                    FROM
            (SELECT
                                reseptur_t.tglreseptur::date as tanggal,
                                obatalkespasien_t.obatalkes_id as total
                        FROM penjualanresep_t
                                JOIN reseptur_t ON penjualanresep_t.reseptur_id = reseptur_t.reseptur_id
                                JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id 
                                JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =3
                         WHERE obatalkespasien_t.is_deleted = FALSE 
                            AND penjualanresep_t.status_reseptur = '660'
                            AND reseptur_t.status_reseptur <> '432'
            UNION ALL
                        SELECT
                            obatalkespasien_t.tglpelayanan::DATE as tanggal,
                            obatalkespasien_t.obatalkes_id as total
                        FROM obatalkespasien_t
                            JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                            JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id=6
                            JOIN implementasi_t ON obatalkespasien_t.implementasi_id = implementasi_t.implementasi_id
                            JOIN instruksi_t ON implementasi_t.instruksi_id = instruksi_t.instruksi_id
                            JOIN cppt_t ON instruksi_t.cppt_id = cppt_t.cppt_id
                            JOIN ruangan_m ruangan_cppt ON cppt_t.ruangan_id = ruangan_cppt.ruangan_id AND ruangan_cppt.instalasi_id=3
                        WHERE obatalkespasien_t.is_deleted=FALSE
                            AND obatalkespasien_t.penjualanresep_id IS NULL
                        UNION ALL       
                        SELECT
                            penjualanresep_t.tglresep::DATE as tanggal,
                            obatalkespasien_t.obatalkes_id as total
                        FROM penjualanresep_t
                            JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id=3
                            JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
                        WHERE penjualanresep_t.is_deleted=FALSE AND obatalkespasien_t.is_deleted = FALSE
                          AND penjualanresep_t.pasienadmisi_id IS NOT NULL
                            AND penjualanresep_t.reseptur_id IS NULL
                            AND penjualanresep_t.status_reseptur = '660'
                    ) a
           GROUP BY a.tanggal,
                                        a.total) b
                        GROUP BY b.tanggal
        ) far ON t_date.tgl_generate = far.tanggal  
        
UNION ALL       

    SELECT
        23 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  PHARMACY'::VARCHAR AS unit,
        '      R/'::VARCHAR AS unit_thruput,
        '          MCU' AS detail_thruput,
        COALESCE(far.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
          SELECT
                     b.tanggal,
                     COUNT(b.total) as total
                    FROM    
                        (SELECT
                a.tanggal,
                a.total as total
            FROM
            (SELECT
                reseptur_t.tglreseptur::date as tanggal,
                obatalkespasien_t.obatalkes_id as total
            FROM reseptur_t
              JOIN penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id 
                            JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id 
                            JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =21
                        WHERE reseptur_t.is_deleted=FALSE
                            AND reseptur_t.status_reseptur <> '432'
                            AND penjualanresep_t.status_reseptur = '660'
            UNION ALL
            SELECT
                            obatalkespasien_t.tglpelayanan::DATE as tanggal,
                            obatalkespasien_t.obatalkes_id as total
                        FROM obatalkespasien_t
                            JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id=21
                            JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id=6
                        WHERE obatalkespasien_t.is_deleted=FALSE
                            AND obatalkespasien_t.penjualanresep_id IS NULL 
                        UNION ALL
                        SELECT
                            penjualanresep_t.tglresep::DATE as tanggal,
                            obatalkespasien_t.obatalkes_id as total
                        FROM penjualanresep_t
                            JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.instalasi_id=21
                            JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
                        WHERE penjualanresep_t.is_deleted=FALSE
                            AND penjualanresep_t.reseptur_id IS NULL
                            AND penjualanresep_t.status_reseptur = '660'    
                            ) a
            GROUP BY a.tanggal,
                                         a.total) b
                        GROUP BY b.tanggal               
        ) far ON t_date.tgl_generate = far.tanggal  
        
UNION ALL       

SELECT
        24 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  PHARMACY'::VARCHAR AS unit,
        '      R/'::VARCHAR AS unit_thruput,
        '          OTC' AS detail_thruput,
        COALESCE(far.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
          SELECT
                        a.tanggal,
                        COUNT(a.total) as total
                    FROM    
                        (SELECT
                penjualanresep_t.tglresep::date as tanggal,
                obatalkespasien_t.obatalkes_id as total
            FROM penjualanresep_t
                            JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
            WHERE penjualanresep_t.is_deleted=FALSE 
                          AND obatalkespasien_t.is_deleted = FALSE
                            AND penjualanresep_t.jenispenjualan = '343'
                            AND penjualanresep_t.status_reseptur = '660'
            GROUP BY penjualanresep_t.tglresep::date,
                                         obatalkespasien_t.obatalkes_id
                        ) a 
                        GROUP BY    a.tanggal
        ) far ON t_date.tgl_generate = far.tanggal  

UNION ALL
        
    SELECT
      25 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          OPD' AS detail_thruput,
        COALESCE(lab.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate    
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
                        COUNT(pasienmasukpenunjang_t.no_masukpenunjang) AS total
                    FROM pasienmasukpenunjang_t
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
                    WHERE pasienmasukpenunjang_t.instalasiasal_id = 1
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
    ) lab ON t_date.tgl_generate = lab.tanggal

UNION ALL

    SELECT
      26 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          IGD' AS detail_thruput,
        COALESCE(lab.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
                        COUNT(pasienmasukpenunjang_t.no_masukpenunjang) AS total
                    FROM pasienmasukpenunjang_t
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
                    WHERE pasienmasukpenunjang_t.instalasiasal_id = 2
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
    ) lab ON t_date.tgl_generate = lab.tanggal

UNION ALL

    SELECT
        27 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(lab.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
                        COUNT(pasienmasukpenunjang_t.no_masukpenunjang) AS total
                    FROM pasienmasukpenunjang_t
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
                    WHERE pasienmasukpenunjang_t.instalasiasal_id = 3
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
        ) lab ON t_date.tgl_generate = lab.tanggal

UNION ALL

    SELECT
        28 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          MCU' AS detail_thruput,
        COALESCE(lab.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT
            mcu.tanggal,
            COUNT(mcu.total) as total
        FROM
            (SELECT 
                    pendaftaran_t.tgl_pendaftaran::date AS tanggal,
                    pendaftaran_t.pendaftaran_id AS total
                FROM pendaftaran_t
                JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id 
                                                                AND tindakanpelayanan_t.is_deleted=FALSE
                JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id 
                JOIN ruangan_m ruangan_paket ON paketpelayanan_mp.ruangan_id = ruangan_paket.ruangan_id AND ruangan_paket.instalasi_id = 4
            WHERE pendaftaran_t.instalasi_id = 21
                GROUP BY pendaftaran_t.tgl_pendaftaran::date,
                pendaftaran_t.pendaftaran_id ) mcu
            GROUP BY mcu.tanggal
        ) lab ON t_date.tgl_generate = lab.tanggal

UNION ALL

    SELECT
        29 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          REFFERAL / APS' AS detail_thruput,
        COALESCE(lab.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT 
            pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
            count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
        FROM pasienmasukpenunjang_t
        JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
        JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
        WHERE ruang_penunjang.instalasi_id = 4 
            AND pendaftaran_t.is_aps = TRUE AND pendaftaran_t.instalasi_id <> 21
            AND pasienmasukpenunjang_t.is_bayar = TRUE
            AND pasienmasukpenunjang_t.status_periksa <> '476' 
        GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
        ) lab ON t_date.tgl_generate = lab.tanggal
        
UNION ALL
        
    SELECT
        30 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      TESTS'::VARCHAR AS unit_thruput,
        '          OPD' AS detail_thruput,
        COALESCE(lab_test_opd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT 
            tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
            sum(tindakanpelayanan_t.qty_tindakan) AS total
        FROM tindakanpelayanan_t
        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                                                             AND pasienmasukpenunjang_t.instalasiasal_id = 1 
        JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
        JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
        WHERE tindakanpelayanan_t.is_deleted = false
        GROUP BY tindakanpelayanan_t.tgl_tindakan::date
    ) lab_test_opd ON t_date.tgl_generate = lab_test_opd.tanggal
    
UNION ALL
        
    SELECT
        31 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      TESTS'::VARCHAR AS unit_thruput,
        '          IGD' AS detail_thruput,
        COALESCE(lab_test_opd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT 
            tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
            sum(tindakanpelayanan_t.qty_tindakan) AS total
        FROM tindakanpelayanan_t
        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                                                             AND pasienmasukpenunjang_t.instalasiasal_id = 2 
        JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
        JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
        WHERE tindakanpelayanan_t.is_deleted = false
        GROUP BY tindakanpelayanan_t.tgl_tindakan::date
    ) lab_test_opd ON t_date.tgl_generate = lab_test_opd.tanggal
    
UNION ALL
    
    SELECT
      32 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      TESTS'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(lab_test_opd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT 
            tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
            sum(tindakanpelayanan_t.qty_tindakan) AS total
        FROM tindakanpelayanan_t
        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                                                             AND pasienmasukpenunjang_t.instalasiasal_id = 3 
        JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
        JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
        WHERE tindakanpelayanan_t.is_deleted = false
        GROUP BY tindakanpelayanan_t.tgl_tindakan::date
    ) lab_test_opd ON t_date.tgl_generate = lab_test_opd.tanggal
    
UNION ALL
    
    SELECT
        33 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      TESTS'::VARCHAR AS unit_thruput,
        '          MCU' AS detail_thruput,
        COALESCE(lab_test_opd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT 
            tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
            sum(tindakanpelayanan_t.qty_tindakan) AS total
        FROM tindakanpelayanan_t
        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                                                             AND pasienmasukpenunjang_t.instalasiasal_id = 21 
        JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
        JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
        WHERE tindakanpelayanan_t.is_deleted = false
        GROUP BY tindakanpelayanan_t.tgl_tindakan::date
    ) lab_test_opd ON t_date.tgl_generate = lab_test_opd.tanggal
    
UNION ALL
    
    SELECT
        34 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      TESTS'::VARCHAR AS unit_thruput,
        '          REFFERAL / APS' AS detail_thruput,
        COALESCE(lab_test_opd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT 
            tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
            sum(tindakanpelayanan_t.qty_tindakan) AS total
        FROM tindakanpelayanan_t
        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
        JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id 
                                            and pendaftaran_t.is_aps=TRUE   
        JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
        JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
        WHERE tindakanpelayanan_t.is_deleted = false
        GROUP BY tindakanpelayanan_t.tgl_tindakan::date
    ) lab_test_opd ON t_date.tgl_generate = lab_test_opd.tanggal
    
UNION ALL

SELECT
      35 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      PATIENTS THRUPUT (PCR - COVID19)'::VARCHAR AS unit_thruput,
        '          OPD' AS detail_thruput,
        COALESCE(pcr_opd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT 
            pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
            count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
        FROM pasienmasukpenunjang_t
            JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =4 
            JOIN (SELECT 
                                        tindakanpelayanan_t.pasienmasukpenunjang_id
                                    FROM tindakanpelayanan_t 
                                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                        JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                                    WHERE jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text 
                                         OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text
                                        AND tindakanpelayanan_t.is_deleted = false
                                    GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id
                 ) tindakan_pelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_pelayanan.pasienmasukpenunjang_id
        WHERE pasienmasukpenunjang_t.instalasiasal_id = 1
          AND pasienmasukpenunjang_t.status_periksa <> '476' 
        GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
    ) pcr_opd ON t_date.tgl_generate = pcr_opd.tanggal
    
UNION ALL

SELECT
      36 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      PATIENTS THRUPUT (PCR - COVID19)'::VARCHAR AS unit_thruput,
        '          IGD' AS detail_thruput,
        COALESCE(pcr_igd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT 
            pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
            count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
        FROM pasienmasukpenunjang_t
            JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =4 
            JOIN (SELECT 
                                        tindakanpelayanan_t.pasienmasukpenunjang_id
                                    FROM tindakanpelayanan_t 
                                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                        JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                                    WHERE (jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text 
                                         OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text)
                                        AND tindakanpelayanan_t.is_deleted = false
                                    GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id
                 ) tindakan_pelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_pelayanan.pasienmasukpenunjang_id
        WHERE pasienmasukpenunjang_t.instalasiasal_id = 2
          AND pasienmasukpenunjang_t.status_periksa <> '476' 
        GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
    ) pcr_igd ON t_date.tgl_generate = pcr_igd.tanggal
    
UNION ALL

    SELECT
      37 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      PATIENTS THRUPUT (PCR - COVID19)'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(pcr_ipd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT 
            pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
            count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
        FROM pasienmasukpenunjang_t
            JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =4 
            JOIN (SELECT 
                                        tindakanpelayanan_t.pasienmasukpenunjang_id
                                    FROM tindakanpelayanan_t 
                                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                        JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                                    WHERE (jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text 
                                         OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text)
                                        AND tindakanpelayanan_t.is_deleted = false
                                    GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id
                 ) tindakan_pelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_pelayanan.pasienmasukpenunjang_id
        WHERE pasienmasukpenunjang_t.instalasiasal_id = 3
          AND pasienmasukpenunjang_t.status_periksa <> '476' 
        GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
    ) pcr_ipd ON t_date.tgl_generate = pcr_ipd.tanggal  

UNION ALL   

    SELECT
      38 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      PATIENTS THRUPUT (PCR - COVID19)'::VARCHAR AS unit_thruput,
        '          MCU' AS detail_thruput,
        COALESCE(pcr_mcu.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT
                        mcu.tanggal,
                        COUNT(mcu.total) as total
                    FROM
                    (SELECT 
                            pendaftaran_t.tgl_pendaftaran::date AS tanggal,
                            pendaftaran_t.pendaftaran_id AS total
                    FROM pendaftaran_t
                        JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id                                            
                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id 
                        JOIN ruangan_m ruangan_paket ON paketpelayanan_mp.ruangan_id = ruangan_paket.ruangan_id AND ruangan_paket.instalasi_id = 4
                        JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN pemeriksaanlab_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                    WHERE (jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text)
                    AND tindakanpelayanan_t.is_deleted = FALSE
                    AND pendaftaran_t.instalasi_id = 21
                    GROUP BY pendaftaran_t.tgl_pendaftaran::date,
                             pendaftaran_t.pendaftaran_id) mcu
                    GROUP BY mcu.tanggal
    ) pcr_mcu ON t_date.tgl_generate = pcr_mcu.tanggal  

UNION ALL

    SELECT
        39 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      PATIENTS THRUPUT (PCR - COVID19)'::VARCHAR AS unit_thruput,
        '          REFFERAL / APS' AS detail_thruput,
        COALESCE(pcr_aps.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                SELECT 
            pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
            count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
        FROM pasienmasukpenunjang_t
            JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
            JOIN (SELECT 
                            tindakanpelayanan_t.pasienmasukpenunjang_id
                        FROM tindakanpelayanan_t 
                            JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                            JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                            JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                            WHERE (jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text 
                                 OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text)
                                AND tindakanpelayanan_t.is_deleted = false
                        GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id
                        ) tindakan_pelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_pelayanan.pasienmasukpenunjang_id
        WHERE ruang_penunjang.instalasi_id = 4 
            AND pendaftaran_t.is_aps = true AND pendaftaran_t.instalasi_id <> 21
            AND pasienmasukpenunjang_t.is_bayar = true
        GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
    ) pcr_aps ON t_date.tgl_generate = pcr_aps.tanggal
    
UNION ALL
    
    SELECT
        40 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      TESTS (PCR - COVID19)'::VARCHAR AS unit_thruput,
        '          OPD' AS detail_thruput,
        COALESCE(testpcr_opd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        SUM(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =4 
                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                    WHERE (jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text  OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text)
                        AND tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.instalasiasal_id = 1
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    ) testpcr_opd ON t_date.tgl_generate = testpcr_opd.tanggal
    
UNION ALL

    SELECT
        41 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      TESTS (PCR - COVID19)'::VARCHAR AS unit_thruput,
        '          IGD' AS detail_thruput,
        COALESCE(testpcr_igd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
          SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        SUM(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =4 
                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                    WHERE (jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text  OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text)
                        AND tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.instalasiasal_id = 2
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
    ) testpcr_igd ON t_date.tgl_generate = testpcr_igd.tanggal
    
UNION ALL
    
    SELECT
        42 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      TESTS (PCR - COVID19)'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(testpcr_ipd.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
          SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        SUM(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =4 
                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                    WHERE (jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text  OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text)
                        AND tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.instalasiasal_id = 3
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
    ) testpcr_ipd ON t_date.tgl_generate = testpcr_ipd.tanggal
    
UNION ALL

    SELECT
        43 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      TESTS (PCR - COVID19)'::VARCHAR AS unit_thruput,
        '          MCU' AS detail_thruput,
        COALESCE(testpcr_mcu.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        COUNT(paketpelayanan_mp.daftartindakan_id) as total
                    FROM tindakanpelayanan_t 
                        JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id 
                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                        JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN pemeriksaanlab_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                    WHERE (jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text)
                        AND tindakanpelayanan_t.is_deleted = false
                        AND ruangan_m.instalasi_id = 4 
                        AND pendaftaran_t.instalasi_id = 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
    ) testpcr_mcu ON t_date.tgl_generate = testpcr_mcu.tanggal
    
UNION ALL
    
    SELECT
        44 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  LABORATORIUM'::VARCHAR AS unit,
        '      TESTS (PCR - COVID19)'::VARCHAR AS unit_thruput,
        '          REFFERAL / APS' AS detail_thruput,
        COALESCE(testpcr_aps.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        SUM(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                        JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                    WHERE (jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text)
                            AND tindakanpelayanan_t.is_deleted = FALSE
                            AND ruang_penunjang.instalasi_id = 4 
                            AND pendaftaran_t.is_aps = TRUE 
                            AND pasienmasukpenunjang_t.status_periksa <> '476'
                            AND pasienmasukpenunjang_t.is_bayar=TRUE  
                        GROUP BY tindakanpelayanan_t.tgl_tindakan::date
    ) testpcr_aps ON t_date.tgl_generate = testpcr_aps.tanggal
        
UNION ALL

    SELECT
        45 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          OPD' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
                        count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
                    FROM pasienmasukpenunjang_t
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =5 
                    WHERE pasienmasukpenunjang_t.instalasiasal_id = 1
                        AND pasienmasukpenunjang_t.status_periksa <> '476' 
                    GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
        ) rad ON t_date.tgl_generate = rad.tanggal  
        
UNION ALL

    SELECT
        46 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          IGD' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
                        count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
                    FROM pasienmasukpenunjang_t
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =5 
                    WHERE pasienmasukpenunjang_t.instalasiasal_id = 2
                        AND pasienmasukpenunjang_t.status_periksa <> '476' 
                    GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
        ) rad ON t_date.tgl_generate = rad.tanggal          
        
UNION ALL

    SELECT
        47 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
                        count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
                    FROM pasienmasukpenunjang_t
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id =5 
                    WHERE pasienmasukpenunjang_t.instalasiasal_id = 3
                        AND pasienmasukpenunjang_t.status_periksa <> '476' 
                    GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
        ) rad ON t_date.tgl_generate = rad.tanggal  
        
UNION ALL

    SELECT
        48 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          MCU' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT
            rad_mcu.tanggal,
            COUNT(rad_mcu.total) as total
        FROM
            (SELECT 
                    pendaftaran_t.tgl_pendaftaran::date AS tanggal,
                    pendaftaran_t.pendaftaran_id AS total
                FROM pendaftaran_t
                JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id 
                                                                AND tindakanpelayanan_t.is_deleted=FALSE
                JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id 
                JOIN ruangan_m ruangan_paket ON paketpelayanan_mp.ruangan_id = ruangan_paket.ruangan_id AND ruangan_paket.instalasi_id = 5
            WHERE pendaftaran_t.instalasi_id = 21
                GROUP BY pendaftaran_t.tgl_pendaftaran::date,
                pendaftaran_t.pendaftaran_id ) rad_mcu
            GROUP BY rad_mcu.tanggal
                ) rad ON t_date.tgl_generate = rad.tanggal      
        
UNION ALL

    SELECT
        49 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          REFFERAL / APS' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
        SELECT 
            pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
            count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
        FROM pasienmasukpenunjang_t
        JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
        JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
        WHERE ruang_penunjang.instalasi_id = 5 
            AND pendaftaran_t.is_aps = TRUE AND pendaftaran_t.instalasi_id <> 21
            AND pasienmasukpenunjang_t.status_periksa <> '476' 
            AND pasienmasukpenunjang_t.is_bayar =  true 
        GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
        ) rad ON t_date.tgl_generate = rad.tanggal

UNION ALL

    SELECT
        50 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      EXAMINATIONS'::VARCHAR AS unit_thruput,
        '          OPD' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                    WHERE  tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.instalasiasal_id = 1
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
        ) rad ON t_date.tgl_generate = rad.tanggal  

UNION ALL

    SELECT
        51 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      EXAMINATIONS'::VARCHAR AS unit_thruput,
        '          IGD' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                    WHERE  tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.instalasiasal_id = 2
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
        ) rad ON t_date.tgl_generate = rad.tanggal  

UNION ALL

    SELECT
        52 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      EXAMINATIONS'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                    WHERE  tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.instalasiasal_id = 3
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
        ) rad ON t_date.tgl_generate = rad.tanggal  

UNION ALL

    SELECT
        53 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      EXAMINATIONS'::VARCHAR AS unit_thruput,
        '          MCU' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        COUNT(paketpelayanan_mp.daftartindakan_id) as total
                    FROM tindakanpelayanan_t 
                        JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                        JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                    WHERE  tindakanpelayanan_t.is_deleted = false
                        AND ruangan_m.instalasi_id = 5 
                        AND pendaftaran_t.instalasi_id = 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
        ) rad ON t_date.tgl_generate = rad.tanggal  

UNION ALL

    SELECT
        54 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      EXAMINATIONS'::VARCHAR AS unit_thruput,
        '          REFFERAL / APS' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT 
                tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                sum(tindakanpelayanan_t.qty_tindakan) as total
            FROM tindakanpelayanan_t 
                JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
                JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
             WHERE tindakanpelayanan_t.is_deleted = FALSE
                AND ruang_penunjang.instalasi_id = 5 
                AND pendaftaran_t.is_aps = TRUE 
                                AND pasienmasukpenunjang_t.status_periksa <> '476'
                                AND pasienmasukpenunjang_t.is_bayar=TRUE  
            GROUP BY tindakanpelayanan_t.tgl_tindakan::date
        ) rad ON t_date.tgl_generate = rad.tanggal
        
UNION ALL

    SELECT
        55 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      MODALITY'::VARCHAR AS unit_thruput,
        '          Konvensional' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%CONVENTIONAL%'
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    UNION ALL
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%CONVENTIONAL%'
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND ruang_penunjang.instalasi_id = 5 
                        AND pendaftaran_t.is_aps = TRUE AND pendaftaran_t.instalasi_id <> 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476' 
                        AND pasienmasukpenunjang_t.is_bayar =  TRUE
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date 
                    UNION ALL
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        COUNT(paketpelayanan_mp.daftartindakan_id) as total
                    FROM tindakanpelayanan_t 
                        JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                        JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%CONVENTIONAL%'
                    WHERE  tindakanpelayanan_t.is_deleted = false
                        AND ruangan_m.instalasi_id = 5 
                        AND pendaftaran_t.instalasi_id = 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    ) rad ON t_date.tgl_generate = rad.tanggal
        
UNION ALL

    SELECT
        56 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      MODALITY'::VARCHAR AS unit_thruput,
        '          USG' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                    AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%USG%'
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.status_periksa <> '476' 
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    UNION ALL
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%USG%'
                        WHERE tindakanpelayanan_t.is_deleted = FALSE
                            AND ruang_penunjang.instalasi_id = 5 
                            AND pendaftaran_t.is_aps = TRUE AND pendaftaran_t.instalasi_id <> 21
                            AND pasienmasukpenunjang_t.status_periksa <> '476' 
                            AND pasienmasukpenunjang_t.is_bayar =  TRUE
                        GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    UNION ALL 
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        COUNT(paketpelayanan_mp.daftartindakan_id) as total
                    FROM tindakanpelayanan_t 
                        JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                        JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%USG%'
                    WHERE  tindakanpelayanan_t.is_deleted = false
                        AND ruangan_m.instalasi_id = 5 
                        AND pendaftaran_t.instalasi_id = 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date     
        ) rad ON t_date.tgl_generate = rad.tanggal      

UNION ALL

    SELECT
        57 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      MODALITY'::VARCHAR AS unit_thruput,
        '          CT-SCAN' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
            tindakanpelayanan_t.tgl_tindakan::date as tanggal,
            sum(tindakanpelayanan_t.qty_tindakan) as total
          FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
            JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
            JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                    AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%MSCT%'
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.status_periksa <> '476' 
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    UNION ALL
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%MSCT%'
                        WHERE tindakanpelayanan_t.is_deleted = FALSE
                            AND ruang_penunjang.instalasi_id = 5 
                            AND pendaftaran_t.is_aps = TRUE AND pendaftaran_t.instalasi_id <> 21
                            AND pasienmasukpenunjang_t.status_periksa <> '476' 
                            AND pasienmasukpenunjang_t.is_bayar =  TRUE
                        GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    UNION ALL 
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        COUNT(paketpelayanan_mp.daftartindakan_id) as total
                    FROM tindakanpelayanan_t 
                        JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                        JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%MSCT%'
                    WHERE  tindakanpelayanan_t.is_deleted = false
                        AND ruangan_m.instalasi_id = 5 
                        AND pendaftaran_t.instalasi_id = 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date     
      ) rad ON t_date.tgl_generate = rad.tanggal
        
UNION ALL

    SELECT
        58 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      MODALITY'::VARCHAR AS unit_thruput,
        '          MRI' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%MRI%'
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.status_periksa <> '476' 
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    UNION ALL
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%MRI%'
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND ruang_penunjang.instalasi_id = 5 
                        AND pendaftaran_t.is_aps = TRUE AND pendaftaran_t.instalasi_id <> 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476' 
                        AND pasienmasukpenunjang_t.is_bayar =  TRUE
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    UNION ALL
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        COUNT(paketpelayanan_mp.daftartindakan_id) as total
                    FROM tindakanpelayanan_t 
                        JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                        JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%MRI%'
                    WHERE  tindakanpelayanan_t.is_deleted = false
                        AND ruangan_m.instalasi_id = 5 
                        AND pendaftaran_t.instalasi_id = 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date     
       ) rad ON t_date.tgl_generate = rad.tanggal  

UNION ALL

    SELECT
        59 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      MODALITY'::VARCHAR AS unit_thruput,
        '          Panoramic & Dental X-Ray' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
            sum(tindakanpelayanan_t.qty_tindakan) as total
          FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
            JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
            JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%PANORAMIC%'
          WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
          GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    UNION ALL
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                             AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%PANORAMIC%'
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND ruang_penunjang.instalasi_id = 5 
                        AND pendaftaran_t.is_aps = TRUE AND pendaftaran_t.instalasi_id <> 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476' 
                        AND pasienmasukpenunjang_t.is_bayar =  TRUE
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    UNION ALL
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        COUNT(paketpelayanan_mp.daftartindakan_id) as total
                    FROM tindakanpelayanan_t 
                        JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                        JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%PANORAMIC%'
                    WHERE  tindakanpelayanan_t.is_deleted = false
                        AND ruangan_m.instalasi_id = 5 
                        AND pendaftaran_t.instalasi_id = 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date 
        ) rad ON t_date.tgl_generate = rad.tanggal
        
UNION ALL

    SELECT
        60 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      MODALITY'::VARCHAR AS unit_thruput,
        '          Mammografi' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
            sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
            JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
            JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%MAMMOGRAPHY%'
          WHERE tindakanpelayanan_t.is_deleted = FALSE
                     AND pasienmasukpenunjang_t.status_periksa <> '476'
          GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    UNION ALL
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        sum(tindakanpelayanan_t.qty_tindakan) as total
                    FROM tindakanpelayanan_t 
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
                        JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%MAMMOGRAPHY%'
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND ruang_penunjang.instalasi_id = 5 
                        AND pendaftaran_t.is_aps = TRUE AND pendaftaran_t.instalasi_id <> 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476' 
                        AND pasienmasukpenunjang_t.is_bayar =  TRUE
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date 
                    UNION ALL
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        COUNT(paketpelayanan_mp.daftartindakan_id) as total
                    FROM tindakanpelayanan_t 
                        JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                        JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%MAMMOGRAPHY%'
                    WHERE  tindakanpelayanan_t.is_deleted = false
                        AND ruangan_m.instalasi_id = 5 
                        AND pendaftaran_t.instalasi_id = 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date 
        ) rad ON t_date.tgl_generate = rad.tanggal
        
UNION ALL

    SELECT
        61 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  RADIOLOGY'::VARCHAR AS unit,
        '      MODALITY'::VARCHAR AS unit_thruput,
        '          BMD' AS detail_thruput,
        COALESCE(rad.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                SELECT 
                    tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                    sum(tindakanpelayanan_t.qty_tindakan) as total
                FROM tindakanpelayanan_t 
                    JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
          JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                    JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                    JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id    
                                                                             AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%BMD%'
                WHERE tindakanpelayanan_t.is_deleted = FALSE
                    AND pasienmasukpenunjang_t.status_periksa <> '476'
                GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                UNION ALL
                SELECT 
                    tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                    sum(tindakanpelayanan_t.qty_tindakan) as total
                FROM tindakanpelayanan_t 
                    JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
          JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                    JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
                    JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                    JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                             AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%BMD%'
                WHERE tindakanpelayanan_t.is_deleted = FALSE
                    AND ruang_penunjang.instalasi_id = 5 
                    AND pendaftaran_t.is_aps = TRUE AND pendaftaran_t.instalasi_id <> 21
                    AND pasienmasukpenunjang_t.status_periksa <> '476' 
                    AND pasienmasukpenunjang_t.is_bayar =  TRUE
                GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                UNION ALL
                SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date as tanggal,
                        COUNT(paketpelayanan_mp.daftartindakan_id) as total
                    FROM tindakanpelayanan_t 
                        JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                        JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                        JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id 
                                                                                 AND kelompokpemeriksaanrad_m.nama_kelompok ILIKE '%BMD%'
                    WHERE  tindakanpelayanan_t.is_deleted = false
                        AND ruangan_m.instalasi_id = 5 
                        AND pendaftaran_t.instalasi_id = 21
                        AND pasienmasukpenunjang_t.status_periksa <> '476'
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date     
      ) rad ON t_date.tgl_generate = rad.tanggal  
        
UNION ALL

    SELECT
        62 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  MEDICAL REHABILITATION'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          OPD' AS detail_thruput,
        COALESCE(mr.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT
                        a.tanggal,
                        COUNT(a.total) as total
                    FROM
                        (   SELECT 
                                tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                                tindakanpelayanan_t.pendaftaran_id AS total
                            FROM tindakanpelayanan_t
                                JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                                JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                            WHERE tindakanpelayanan_t.is_deleted = FALSE
                                AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text
                                AND pendaftaran_t.instalasi_id = 1
                            GROUP BY tindakanpelayanan_t.tgl_tindakan::date,
                                             tindakanpelayanan_t.pendaftaran_id
                        ) a
                    GROUP BY a.tanggal
        ) mr ON t_date.tgl_generate = mr.tanggal

UNION ALL

    SELECT
        63 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  MEDICAL REHABILITATION'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          IGD' AS detail_thruput,
        COALESCE(mr.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT
                    a.tanggal,
                    COUNT(a.total) as total
            FROM
                    (   SELECT 
                            tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                            tindakanpelayanan_t.pendaftaran_id AS total
                        FROM tindakanpelayanan_t
                            JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        WHERE tindakanpelayanan_t.is_deleted = FALSE
                            AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text
                            AND tindakanpelayanan_t.instalasi_id = 2
                        GROUP BY tindakanpelayanan_t.tgl_tindakan::date,
                                         tindakanpelayanan_t.pendaftaran_id
                    ) a
                GROUP BY a.tanggal
      ) mr ON t_date.tgl_generate = mr.tanggal

UNION ALL

    SELECT
        64 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  MEDICAL REHABILITATION'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(mr.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT
                a.tanggal,
                COUNT(a.total) as total
            FROM
                (   SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                        tindakanpelayanan_t.pendaftaran_id AS total
                    FROM tindakanpelayanan_t
                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND tindakanpelayanan_t.instalasi_id= 3 
                        AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date,
                                     tindakanpelayanan_t.pendaftaran_id) a
            GROUP BY a.tanggal
        ) mr ON t_date.tgl_generate = mr.tanggal 
        
UNION ALL

    SELECT
        65 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  MEDICAL REHABILITATION'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          MCU' AS detail_thruput,
        COALESCE(mr.total, 0)::float8 AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT 
                pendaftaran_t.tgl_pendaftaran::date AS tanggal,
                COUNT(pendaftaran_t.no_pendaftaran) AS total
            FROM pendaftaran_t
                JOIN (SELECT 
                                tindakanpelayanan_t.pendaftaran_id
                            FROM tindakanpelayanan_t
                                JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                                JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                            WHERE tindakanpelayanan_t.is_deleted = false
                                AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text
                            GROUP BY tindakanpelayanan_t.pendaftaran_id
                        ) tindakan_pelayanan ON pendaftaran_t.pendaftaran_id = tindakan_pelayanan.pendaftaran_id
            WHERE pendaftaran_t.instalasi_id =21
            GROUP BY pendaftaran_t.tgl_pendaftaran::date
        ) mr ON t_date.tgl_generate = mr.tanggal 
                                         
UNION ALL

    SELECT
        66 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  MEDICAL REHABILITATION'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          REFFERAL / APS' AS detail_thruput,
        COALESCE(mr.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT 
                pendaftaran_t.tgl_pendaftaran::date AS tanggal,
                COUNT(pendaftaran_t.no_pendaftaran) AS total
            FROM pendaftaran_t
                JOIN (SELECT 
                                tindakanpelayanan_t.pendaftaran_id
                            FROM tindakanpelayanan_t 
                                JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                            WHERE tindakanpelayanan_t.is_deleted = false
                                AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text
                            GROUP BY tindakanpelayanan_t.pendaftaran_id
                        ) tindakan_pelayanan ON pendaftaran_t.pendaftaran_id = tindakan_pelayanan.pendaftaran_id
            WHERE pendaftaran_t.is_aps = TRUE 
                AND pendaftaran_t.instalasi_id <> 21
            GROUP BY pendaftaran_t.tgl_pendaftaran::date
        ) mr ON t_date.tgl_generate = mr.tanggal 

UNION ALL

    SELECT
        67 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  MEDICAL REHABILITATION'::VARCHAR AS unit,
        '      TREATMENTS'::VARCHAR AS unit_thruput,
        '          OPD' AS detail_thruput,
        COALESCE(mr.total, 0)::float8 AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date 
    LEFT JOIN (
            SELECT 
                tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                SUM(tindakanpelayanan_t.qty_tindakan) AS total
            FROM tindakanpelayanan_t
                JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
            WHERE pendaftaran_t.instalasi_id = 1
                AND tindakanpelayanan_t.is_deleted = FALSE
                AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text  
         GROUP BY tindakanpelayanan_t.tgl_tindakan::date
    ) mr ON t_date.tgl_generate = mr.tanggal
            
UNION ALL

    SELECT
        68 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  MEDICAL REHABILITATION'::VARCHAR AS unit,
        '      TREATMENTS'::VARCHAR AS unit_thruput,
        '          IGD' AS detail_thruput,
        COALESCE(mr.total, 0)::float8 AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT 
                tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                SUM(tindakanpelayanan_t.qty_tindakan) AS total
            FROM tindakanpelayanan_t
                JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
            WHERE tindakanpelayanan_t.is_deleted = FALSE
                AND tindakanpelayanan_t.instalasi_id= 2 
                AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text
            GROUP BY tindakanpelayanan_t.tgl_tindakan::date
     ) mr ON t_date.tgl_generate = mr.tanggal 
        
UNION ALL

    SELECT
        69 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  MEDICAL REHABILITATION'::VARCHAR AS unit,
        '      TREATMENTS'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(mr.total, 0)::float8 AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT 
                tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                SUM(tindakanpelayanan_t.qty_tindakan) AS total
            FROM tindakanpelayanan_t
                JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
            WHERE tindakanpelayanan_t.is_deleted = FALSE
                AND tindakanpelayanan_t.instalasi_id= 3 
                AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text
            GROUP BY tindakanpelayanan_t.tgl_tindakan::date
     ) mr ON t_date.tgl_generate = mr.tanggal  
                            
UNION ALL

    SELECT
        70 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  MEDICAL REHABILITATION'::VARCHAR AS unit,
        '      TREATMENTS'::VARCHAR AS unit_thruput,
        '          MCU' AS detail_thruput,
        COALESCE(mr.total, 0)::float8 AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
            SELECT 
                tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                SUM(tindakanpelayanan_t.qty_tindakan) AS total
            FROM tindakanpelayanan_t
                JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
            WHERE pendaftaran_t.instalasi_id = 21
                AND tindakanpelayanan_t.is_deleted = FALSE
                AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text  
            GROUP BY tindakanpelayanan_t.tgl_tindakan::date
     ) mr ON t_date.tgl_generate = mr.tanggal   
        
UNION ALL

    SELECT
        71 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  MEDICAL REHABILITATION'::VARCHAR AS unit,
        '      TREATMENTS'::VARCHAR AS unit_thruput,
        '          REFFERAL / APS' AS detail_thruput,
        COALESCE(mr.total, 0)::float8 AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date 
        LEFT JOIN (
            SELECT 
                tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                SUM(tindakanpelayanan_t.qty_tindakan) AS total
            FROM tindakanpelayanan_t
                JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
            WHERE pendaftaran_t.instalasi_id <> 21
              AND pendaftaran_t.is_aps = TRUE
                AND tindakanpelayanan_t.is_deleted = FALSE
                AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text  
            GROUP BY tindakanpelayanan_t.tgl_tindakan::date
     ) mr ON t_date.tgl_generate = mr.tanggal 

UNION ALL

    SELECT
        72 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  CATH LAB'::VARCHAR AS unit,
        '      PROSEDUR'::VARCHAR AS unit_thruput,
        '          IPD / EMERGENCY' AS detail_thruput,
        COALESCE(cl.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                        SUM(tindakanpelayanan_t.qty_tindakan) AS total
                    FROM tindakanpelayanan_t
                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND tindakanpelayanan_t.instalasi_id in  (2,3)
                        AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 6) = '120503'::text
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
        ) cl ON t_date.tgl_generate = cl.tanggal
                
UNION ALL

    SELECT
            73 as urutan,
            'LOS'::VARCHAR AS tipe,
            tgl_generate AS tanggal,
            '  CATH LAB'::VARCHAR AS unit,
            '      PROSEDUR'::VARCHAR AS unit_thruput,
            '          ODC' AS detail_thruput,
            COALESCE(cl.total, 0)::float8  AS total
    FROM (
            SELECT 
                    CURRENT_DATE + i AS tgl_generate
            FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
            LEFT JOIN (
                SELECT 
                    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                    SUM(tindakanpelayanan_t.qty_tindakan) AS total
                FROM tindakanpelayanan_t
                    JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                WHERE tindakanpelayanan_t.is_deleted = FALSE
                    AND tindakanpelayanan_t.ruangan_id = 189 --One Day Care (ODC)
                    AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 6) = '120503'::text
                GROUP BY tindakanpelayanan_t.tgl_tindakan::date
            ) cl ON t_date.tgl_generate = cl.tanggal
                        
UNION ALL

    SELECT
        77 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  ENDOSCOPY'::VARCHAR AS unit,
        '      PROSEDUR'::VARCHAR AS unit_thruput,
                '          IPD' AS detail_thruput,
        COALESCE(endoscopy.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date 
    LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                        SUM(tindakanpelayanan_t.qty_tindakan) AS total
                    FROM tindakanpelayanan_t
                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND tindakanpelayanan_t.instalasi_id = 3
                        AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 6) = '120502'::text
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
     ) endoscopy ON  t_date.tgl_generate = endoscopy.tanggal

UNION ALL

    SELECT
        78 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  ENDOSCOPY'::VARCHAR AS unit,
        '      PROSEDUR'::VARCHAR AS unit_thruput,
                '          ODC' AS detail_thruput,
        COALESCE(endoscopy.total, 0)::float8  AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date 
    LEFT JOIN (
                    SELECT 
                        tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                        SUM(tindakanpelayanan_t.qty_tindakan) AS total
                    FROM tindakanpelayanan_t
                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                        AND tindakanpelayanan_t.ruangan_id = 189 --One Day Care (ODC)
                        AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 6) = '120502'::text
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
     ) endoscopy ON  t_date.tgl_generate = endoscopy.tanggal         
                            
UNION ALL

        SELECT
            79 as urutan,
            'LOS'::VARCHAR AS tipe,
            tgl_generate AS tanggal,
            '  HAEMODIALYSIS'::VARCHAR AS unit,
            '      PROSEDUR'::VARCHAR AS unit_thruput,
            '          OPD' AS detail_thruput,
            COALESCE(hemo.total, 0)::float8  AS total
        FROM (
            SELECT 
                    CURRENT_DATE + i AS tgl_generate
            FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date 
                LEFT JOIN (
                                    SELECT 
                                        tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                                        SUM(tindakanpelayanan_t.qty_tindakan) AS total
                                    FROM tindakanpelayanan_t
                                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                                        AND tindakanpelayanan_t.instalasi_id = 1
                                        AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 6) = '120800'::text
                                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    ) hemo ON t_date.tgl_generate = hemo.tanggal

UNION ALL

        SELECT
            80 as urutan,
            'LOS'::VARCHAR AS tipe,
            tgl_generate AS tanggal,
            '  HAEMODIALYSIS'::VARCHAR AS unit,
            '      PROSEDUR'::VARCHAR AS unit_thruput,
            '          IGD' AS detail_thruput,
            COALESCE(hemo.total, 0)::float8  AS total
        FROM (
            SELECT 
                    CURRENT_DATE + i AS tgl_generate
            FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date 
                LEFT JOIN (
                                    SELECT 
                                        tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                                        SUM(tindakanpelayanan_t.qty_tindakan) AS total
                                    FROM tindakanpelayanan_t
                                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                                        AND tindakanpelayanan_t.instalasi_id = 2
                                        AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 6) = '120800'::text
                                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    ) hemo ON t_date.tgl_generate = hemo.tanggal          

UNION ALL

        SELECT
            81 as urutan,
            'LOS'::VARCHAR AS tipe,
            tgl_generate AS tanggal,
            '  HAEMODIALYSIS'::VARCHAR AS unit,
            '      PROSEDUR'::VARCHAR AS unit_thruput,
            '          IPD' AS detail_thruput,
            COALESCE(hemo.total, 0)::float8  AS total
        FROM (
            SELECT 
                    CURRENT_DATE + i AS tgl_generate
            FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date 
                LEFT JOIN (
                                    SELECT 
                                        tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                                        SUM(tindakanpelayanan_t.qty_tindakan) AS total
                                    FROM tindakanpelayanan_t
                                        JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                                        AND tindakanpelayanan_t.instalasi_id = 3
                                        AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 6) = '120800'::text
                                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    ) hemo ON t_date.tgl_generate = hemo.tanggal  
                    
UNION ALL

        SELECT
            82 as urutan,
            'LOS'::VARCHAR AS tipe,
            tgl_generate AS tanggal,
            '  HAEMODIALYSIS'::VARCHAR AS unit,
            '      PROSEDUR'::VARCHAR AS unit_thruput,
            '          MCU' AS detail_thruput,
            COALESCE(hemo.total, 0)::float8  AS total
        FROM (
            SELECT 
                    CURRENT_DATE + i AS tgl_generate
            FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date 
                LEFT JOIN (
                                    SELECT 
                                        tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                                        COUNT(paketpelayanan_mp.daftartindakan_id) AS total
                                    FROM tindakanpelayanan_t
                                        JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                                        JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                    WHERE tindakanpelayanan_t.is_deleted = FALSE
                                        AND tindakanpelayanan_t.instalasi_id = 21
                                        AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 6) = '120800'::text
                                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
                    ) hemo ON t_date.tgl_generate = hemo.tanggal                    

UNION ALL

    SELECT
        83 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  OPERATING THEATRE (SURGERIES)'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          IPD' AS detail_thruput,
        COALESCE(operasi.total, 0)::float8 AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date 
      LEFT JOIN (
        SELECT 
            pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
            COUNT(pasienmasukpenunjang_t.no_masukpenunjang) AS total
        FROM pasienmasukpenunjang_t
                JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id and ruangan_m.instalasi_id = 12 
        WHERE pasienmasukpenunjang_t.instalasiasal_id = 3
                    AND pasienmasukpenunjang_t.status_periksa = '483'
        GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
            ) operasi ON t_date.tgl_generate = operasi.tanggal  
        
UNION ALL

    SELECT
        84 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  OPERATING THEATRE (SURGERIES)'::VARCHAR AS unit,
        '      PATIENTS THRUPUT'::VARCHAR AS unit_thruput,
        '          ODC' AS detail_thruput,
        COALESCE(operasi.total, 0)::float8 AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date
        LEFT JOIN (
                    SELECT 
                            pasienmasukpenunjang_t.tglmasukpenunjang::date AS tanggal,
                            COUNT(pasienmasukpenunjang_t.no_masukpenunjang) AS total
                    FROM pasienmasukpenunjang_t
                    JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                    JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id and ruangan_m.instalasi_id = 12 
                    WHERE pendaftaran_t.ruangan_id = 189
                        AND pasienmasukpenunjang_t.status_periksa = '483' 
                    GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang::date
        ) operasi ON t_date.tgl_generate = operasi.tanggal              

UNION ALL

    SELECT
        85 as urutan,
        'LOS'::VARCHAR AS tipe,
        tgl_generate AS tanggal,
        '  OPERATING THEATRE (SURGERIES)'::VARCHAR AS unit,
        '      SURGERY PERFORMED'::VARCHAR AS unit_thruput,
        NULL AS detail_thruput,
        COALESCE(operasi.total, 0)::float8 AS total
    FROM (
        SELECT 
            CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, date (xend_date)  - CURRENT_DATE ) i) AS t_date 
       LEFT JOIN (
                    SELECT 
                            tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
                            SUM(tindakanpelayanan_t.qty_tindakan) AS total
                    FROM tindakanpelayanan_t
                        JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                        JOIN operasi_m ON tindakanpelayanan_t.daftartindakan_id = operasi_m.daftartindakan_id
                        JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 12
                    WHERE tindakanpelayanan_t.is_deleted = false
                    GROUP BY tindakanpelayanan_t.tgl_tindakan::date
            ) operasi ON t_date.tgl_generate = operasi.tanggal
                                        
            ) x
        ORDER BY x.tanggal;
END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210608_085549_migrate_20210608_hotfix_func_laporanthrupt_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210608_085549_migrate_20210608_hotfix_func_laporanthrupt_fn cannot be reverted.\n";

        return false;
    }
    */
}
