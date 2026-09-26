<?php

use yii\db\Migration;

/**
 * Class m210317_115813_migarate_20210317_laporanrevenue_v
 */
class m210317_115813_migarate_20210317_laporanrevenue_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanrevenue_v;');

        $this->execute("
            CREATE VIEW \"public\".\"laporanrevenue_v\" AS  SELECT 'LOB'::text AS tipe,
        CASE
            WHEN ruangan_m.instalasi_id = 1 OR pasienmasukpenunjang_t.instalasiasal_id = 1 OR ruangan_m.instalasi_id = 12 AND pendaftaran_t.instalasi_id = 1 THEN 'OPD'::text
            WHEN ruangan_m.instalasi_id = 2 OR pasienmasukpenunjang_t.instalasiasal_id = 2 THEN 'EMERGENCY'::text
            WHEN ruangan_m.instalasi_id = 3 OR pasienmasukpenunjang_t.instalasiasal_id = 3 OR ruangan_m.instalasi_id = 12 AND pendaftaran_t.instalasi_id <> 1 THEN 'IPD'::text
            ELSE NULL::text
        END AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM tindakanpelayanan_t
     LEFT JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     JOIN ( SELECT daftartindakan_m.daftartindakan_id
           FROM daftartindakan_m
          WHERE \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) <> '34'::text AND \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) <> '34'::text AND daftartindakan_m.daftartindakan_nama::text !~~* '%hemodialisa%'::text) med_rehab ON tindakanpelayanan_t.daftartindakan_id = med_rehab.daftartindakan_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND (ruangan_m.instalasi_id = ANY (ARRAY[1, 2, 3, 12]))
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY (tindakanpelayanan_t.tgl_tindakan::date), (
        CASE
            WHEN ruangan_m.instalasi_id = 1 OR pasienmasukpenunjang_t.instalasiasal_id = 1 OR ruangan_m.instalasi_id = 12 AND pendaftaran_t.instalasi_id = 1 THEN 'OPD'::text
            WHEN ruangan_m.instalasi_id = 2 OR pasienmasukpenunjang_t.instalasiasal_id = 2 THEN 'EMERGENCY'::text
            WHEN ruangan_m.instalasi_id = 3 OR pasienmasukpenunjang_t.instalasiasal_id = 3 OR ruangan_m.instalasi_id = 12 AND pendaftaran_t.instalasi_id <> 1 THEN 'IPD'::text
            ELSE NULL::text
        END)
UNION ALL
 SELECT 'LOB'::text AS tipe,
    'MCU'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM tindakanpelayanan_t
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 21
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY (tindakanpelayanan_t.tgl_tindakan::date)
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'LABORATORY'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY (tindakanpelayanan_t.tgl_tindakan::date)
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'RADIOLOGY'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY (tindakanpelayanan_t.tgl_tindakan::date)
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'PHARMACY'::text AS unit,
    obatalkespasien_t.tglpelayanan::date AS tanggal,
    sum(obatalkespasien_t.hargajual_oa::integer) AS total
   FROM obatalkespasien_t
     JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 6
  WHERE obatalkespasien_t.is_deleted = false
  GROUP BY (obatalkespasien_t.tglpelayanan::date)
UNION ALL
 SELECT 'LOS'::text AS tipe,
    '*     PCR_COVID19'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
     JOIN ( SELECT daftartindakan_m.daftartindakan_id
           FROM daftartindakan_m
             JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
             JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
          WHERE jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text = '0208'::text OR daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text) pcr ON tindakanpelayanan_t.daftartindakan_id = pcr.daftartindakan_id
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY (tindakanpelayanan_t.tgl_tindakan::date)
UNION ALL
 SELECT 'LOS'::text AS tipe,
    '*     NON_PCR'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
     JOIN ( SELECT daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            jenispemeriksaanlab_m.jenispemeriksaanlab_kode
           FROM daftartindakan_m
             JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
             JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
          WHERE jenispemeriksaanlab_m.jenispemeriksaanlab_kode::text <> '0208'::text AND daftartindakan_m.daftartindakan_nama::text !~~* '%PCR%'::text) non_pcr ON tindakanpelayanan_t.daftartindakan_id = non_pcr.daftartindakan_id
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY (tindakanpelayanan_t.tgl_tindakan::date)
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'MEDICAL_REHABILITATION'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM tindakanpelayanan_t
     JOIN ( SELECT daftartindakan_m.daftartindakan_id
           FROM daftartindakan_m
          WHERE \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '33'::text) med_rehab ON tindakanpelayanan_t.daftartindakan_id = med_rehab.daftartindakan_id
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY (tindakanpelayanan_t.tgl_tindakan::date)
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'HAEMODIALYSIS'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM tindakanpelayanan_t
     JOIN ( SELECT daftartindakan_m.daftartindakan_id
           FROM daftartindakan_m
          WHERE \"substring\"(daftartindakan_m.daftartindakan_kode::text, 1, 2) = '34'::text OR daftartindakan_m.daftartindakan_nama::text ~~* '%hemodialisa%'::text) med_rehab ON tindakanpelayanan_t.daftartindakan_id = med_rehab.daftartindakan_id
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY (tindakanpelayanan_t.tgl_tindakan::date)
UNION ALL
 SELECT 'PAYER'::text AS tipe,
        CASE
            WHEN carabayar_m.carabayar_id = 5 THEN 'PRIVATE'::text
            WHEN carabayar_m.carabayar_id = 1 AND penjamin_m.penjamin_nama::text !~~* '%mayapada%'::text THEN 'CORPORATE'::text
            WHEN carabayar_m.carabayar_id = 2 THEN 'INSURANCE'::text
            WHEN carabayar_m.carabayar_id = 6 THEN 'BPJS KESEHATAN'::text
            WHEN carabayar_m.carabayar_id = 7 THEN 'GOVERNMENT'::text
            WHEN penjamin_m.penjamin_nama::text ~~* '%mayapada%'::text THEN 'EMPLOYEE'::text
            ELSE NULL::text
        END AS unit,
    tagihan_payer.tanggal,
    sum(tagihan_payer.total) AS total
   FROM ( SELECT tindakanpelayanan_t.penjamin_id,
            tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
            tindakanpelayanan_t.tarif_tindakan AS total
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT obatalkespasien_t.penjamin_id,
            obatalkespasien_t.tglpelayanan::date AS tanggal,
            obatalkespasien_t.hargajual_oa::integer AS total
           FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted = false) tagihan_payer
     JOIN penjamin_m ON tagihan_payer.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id AND (carabayar_m.carabayar_id = ANY (ARRAY[5, 1, 2, 6, 7]))
  GROUP BY tagihan_payer.tanggal, (
        CASE
            WHEN carabayar_m.carabayar_id = 5 THEN 'PRIVATE'::text
            WHEN carabayar_m.carabayar_id = 1 AND penjamin_m.penjamin_nama::text !~~* '%mayapada%'::text THEN 'CORPORATE'::text
            WHEN carabayar_m.carabayar_id = 2 THEN 'INSURANCE'::text
            WHEN carabayar_m.carabayar_id = 6 THEN 'BPJS KESEHATAN'::text
            WHEN carabayar_m.carabayar_id = 7 THEN 'GOVERNMENT'::text
            WHEN penjamin_m.penjamin_nama::text ~~* '%mayapada%'::text THEN 'EMPLOYEE'::text
            ELSE NULL::text
        END);");

        $this->execute('ALTER TABLE "public"."laporanrevenue_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210317_115813_migarate_20210317_laporanrevenue_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210317_115813_migarate_20210317_laporanrevenue_v cannot be reverted.\n";

        return false;
    }
    */
}
