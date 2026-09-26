<?php

use yii\db\Migration;

/**
 * Class m210114_071659_migrate_20200114_laporanthrupt_fn
 */
class m210114_071659_migrate_20200114_laporanthrupt_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"laporanthrupt_fn\"(\"xstart_date\" date=CURRENT_DATE, \"xend_date\" date=CURRENT_DATE)
  RETURNS TABLE(\"tanggal\" date, \"unit\" varchar, \"jenis\" varchar, \"total\" float8) AS \$BODY\$
BEGIN
    RETURN QUERY 
 SELECT 
  tgl_generate AS tanggal,
  COALESCE(thruput.unit, '')::VARCHAR ,
  COALESCE(thruput.jenis, '')::VARCHAR ,
  COALESCE(thruput.total, 0)::float8 
FROM (
  SELECT CURRENT_DATE + i AS tgl_generate
  FROM generate_series(date (xstart_date) - CURRENT_DATE, 
     date (xend_date)  - CURRENT_DATE ) i
) AS t_date
LEFT JOIN (
 SELECT 'LABORATORIUM'::text AS unit,
    'pasien_APS'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
  WHERE ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.is_bayar = true
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'pasien_RJ'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 1
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'pasien_RD'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 2
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'pasien_RI'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 3
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'pasien_MCU'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 21
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'Test_APS'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE tindakanpelayanan_t_1.is_deleted = false
          GROUP BY tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
  WHERE ruangan_m.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.is_bayar = true
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'Test_RJ'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
     JOIN ruangan_m ON laporankunjunganpenunjang_v.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 4 AND laporankunjunganpenunjang_v.instalasi_id = 1
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'Test_RD'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
     JOIN ruangan_m ON laporankunjunganpenunjang_v.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 4 AND laporankunjunganpenunjang_v.instalasi_id = 2
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'Test_RI'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
     JOIN ruangan_m ON laporankunjunganpenunjang_v.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 4 AND laporankunjunganpenunjang_v.instalasi_id = 3
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'Test_MCU'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
     JOIN ruangan_m ON laporankunjunganpenunjang_v.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 4 AND laporankunjunganpenunjang_v.instalasi_id = 21
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'PCR_pasien_APS'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text AND tindakanpelayanan_t_1.is_deleted = false
          GROUP BY tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
  WHERE ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.is_bayar = true
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'PCR_pasien_RJ'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text AND tindakanpelayanan_t_1.is_deleted = false
          GROUP BY tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pendaftaran_t.instalasi_id = 1
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'PCR_pasien_RD'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text AND tindakanpelayanan_t_1.is_deleted = false
          GROUP BY tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pendaftaran_t.instalasi_id = 2
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'PCR_pasien_RI'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text AND tindakanpelayanan_t_1.is_deleted = false
          GROUP BY tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pendaftaran_t.instalasi_id = 3
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'PCR_pasien_MCU'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text AND tindakanpelayanan_t_1.is_deleted = false
          GROUP BY tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pendaftaran_t.instalasi_id = 21
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'PCR_Test_APS'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text AND tindakanpelayanan_t_1.is_deleted = false) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
  WHERE ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.is_bayar = true
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'PCR_Test_RJ'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
  WHERE laporankunjunganpenunjang_v.daftartindakan_nama::text ~~* '%PCR%'::text AND laporankunjunganpenunjang_v.instalasi_id = 1
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'PCR_Test_RD'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
  WHERE laporankunjunganpenunjang_v.daftartindakan_nama::text ~~* '%PCR%'::text AND laporankunjunganpenunjang_v.instalasi_id = 2
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'PCR_Test_RI'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
  WHERE laporankunjunganpenunjang_v.daftartindakan_nama::text ~~* '%PCR%'::text AND laporankunjunganpenunjang_v.instalasi_id = 3
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'LABORATORIUM'::text AS unit,
    'PCR_Test_MCU'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
  WHERE laporankunjunganpenunjang_v.daftartindakan_nama::text ~~* '%PCR%'::text AND laporankunjunganpenunjang_v.instalasi_id = 21
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    'pasien_APS'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
  WHERE ruang_penunjang.instalasi_id = 5 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.is_bayar = true
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    'pasien_RJ'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 1
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    'pasien_RD'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 2
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    'pasien_RI'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 3
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    'pasien_MCU'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 21
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    'Test_RAD'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
     JOIN ruangan_m ON laporankunjunganpenunjang_v.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 5
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    'Test_RJ'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
     JOIN ruangan_m ON laporankunjunganpenunjang_v.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 5 AND laporankunjunganpenunjang_v.instalasi_id = 1
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    'Test_RD'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
     JOIN ruangan_m ON laporankunjunganpenunjang_v.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 5 AND laporankunjunganpenunjang_v.instalasi_id = 2
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    'Test_RI'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
     JOIN ruangan_m ON laporankunjunganpenunjang_v.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 5 AND laporankunjunganpenunjang_v.instalasi_id = 3
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    'Test_MCU'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
     JOIN ruangan_m ON laporankunjunganpenunjang_v.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 5 AND laporankunjunganpenunjang_v.instalasi_id = 21
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'RADIOLOGI'::text AS unit,
    tindakanpelayanan_t.nama_kelompok AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id,
            kelompokpemeriksaanrad_m.nama_kelompok
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
             JOIN daftartindakan_m ON tindakanpelayanan_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN pemeriksaanrad_m ON daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
             JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
          WHERE tindakanpelayanan_t_1.is_deleted = false) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
  WHERE ruang_penunjang.instalasi_id = 5 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date), tindakanpelayanan_t.nama_kelompok
UNION ALL
 SELECT 'MEDICAL_REHABILITATION'::text AS unit,
    'pasien_RJ'::text AS jenis,
    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pendaftaran_t.no_pendaftaran) AS total
   FROM pendaftaran_t
  WHERE pendaftaran_t.ruangan_id = 108
  GROUP BY (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'MEDICAL_REHABILITATION'::text AS unit,
    'treatments_RJ'::text AS jenis,
    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(tindakanpelayanan_t.daftartindakan_id) AS total
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
  WHERE pendaftaran_t.ruangan_id = 108 AND tindakanpelayanan_t.is_deleted = false
  GROUP BY (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'CATH_LAB'::text AS unit,
    'prosedur'::text AS jenis,
    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(tindakanpelayanan_t.daftartindakan_id) AS total
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
  WHERE pendaftaran_t.ruangan_id = 163 AND tindakanpelayanan_t.is_deleted = false
  GROUP BY (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'ENDOSCOPY'::text AS unit,
    'prosedur'::text AS jenis,
    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(tindakanpelayanan_t.daftartindakan_id) AS total
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
  WHERE pendaftaran_t.ruangan_id = 190 AND tindakanpelayanan_t.is_deleted = false
  GROUP BY (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'HAEMODIALYSIS'::text AS unit,
    'prosedur_rj'::text AS jenis,
    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(tindakanpelayanan_t.daftartindakan_id) AS total
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
  WHERE pendaftaran_t.ruangan_id = 138 AND tindakanpelayanan_t.is_deleted = false
  GROUP BY (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'OPERATING_THEATRE'::text AS unit,
    'pasien_RJ'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 1
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'OPERATING_THEATRE'::text AS unit,
    'pasien_RD'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 2
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'OPERATING_THEATRE'::text AS unit,
    'pasien_RI'::text AS jenis,
    to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.instalasiasal_id = 3
  GROUP BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
UNION ALL
 SELECT 'OPERATING_THEATRE'::text AS unit,
    'jumlah_tindakan'::text AS jenis,
    to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date AS tanggal,
    count(laporankunjunganpenunjang_v.daftartindakan_nama) AS total
   FROM laporankunjunganpenunjang_v
     JOIN ruangan_m ON laporankunjunganpenunjang_v.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 12
  GROUP BY (to_char(laporankunjunganpenunjang_v.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date)
    ) thruput ON t_date.tgl_generate = thruput.tanggal
    ORDER BY t_date.tgl_generate ;
END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000;");
     

     $this->execute('ALTER FUNCTION "public"."laporanthrupt_fn"("xstart_date" date, "xend_date" date) OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210114_071659_migrate_20200114_laporanthrupt_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210114_071659_migrate_20200114_laporanthrupt_fn cannot be reverted.\n";

        return false;
    }
    */
}
