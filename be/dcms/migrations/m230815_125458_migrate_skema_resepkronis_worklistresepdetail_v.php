<?php

use yii\db\Migration;

/**
 * Class m230815_125458_migrate_skema_resepkronis_worklistresepdetail_v
 */
class m230815_125458_migrate_skema_resepkronis_worklistresepdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."worklistresepdetail_v";');
        $this->execute("CREATE OR REPLACE VIEW public.worklistresepdetail_v
        AS  SELECT 'a'::text AS jenis,
    reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    COALESCE(signaobat_m.signa_nama::text, obatalkespasien_t.signa ->> 'text'::text) AS signa,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
    obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text AS satuan_konversi,
    obatalkespasien_t.etiket,
    obatalkes_m.is_oral,
    NULL::date AS tglkadaluarsa,
        CASE
            WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS det,
    obatalkespasien_t.is_deleted AS detail_is_deleted,
    obatalkespasien_t.racikan_id,
    (obatalkespasien_t.additional_data::json ->> 'nilai_konversi'::text)::character varying AS nilai_konversi,
    obatalkespasien_t.nama_racikan,
    obatalkespasien_t.qty_racikan,
    obatalkespasien_t.satuan_racikan_id,
    COALESCE(obatalkespasien_t.det_medis, obatalkespasien_t.det, obatalkespasien_t.qty_medis, obatalkespasien_t.qty_oa) AS det_medis,
        CASE
            WHEN obatalkespasien_t.qty_medis IS NOT NULL THEN obatalkespasien_t.qty_medis
            ELSE obatalkespasien_t.qty_oa
        END AS qty_resep,
        CASE
            WHEN obatalkespasien_t.det IS NOT NULL THEN obatalkespasien_t.det
            ELSE obatalkespasien_t.qty_oa
        END AS qty_bayar,
    satuan_racikan.satuan_racikan,
    obatalkespasien_t.is_kronis
   FROM reseptur_t
     JOIN ( SELECT a.penjualanresep_id,
            a.noresep,
            a.reseptur_id
           FROM penjualanresep_t a) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     JOIN ( SELECT a.obatalkes_id,
            a.penjualanresep_id,
            a.rke,
            a.signa,
            a.qty_oa,
            a.qty_konversi,
            a.additional_data,
            a.det,
            a.is_deleted,
            a.racikan_id,
            a.signa_oa,
            a.etiket,
            a.nama_racikan,
            a.qty_racikan,
            a.satuan_racikan_id,
            a.det_medis,
            a.qty_medis,
            a.is_kronis
           FROM obatalkespasien_t a) obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.is_oral
           FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.racikan_id,
            a.racikan_nama
           FROM racikan_m a) racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a
          WHERE a.instalasi_id = 1) ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT a.signa_id,
            a.signa_nama
           FROM signaobat_m a) signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama AS satuan_racikan
           FROM satuanunit_m a) satuan_racikan ON obatalkespasien_t.satuan_racikan_id = satuan_racikan.satuanunit_id
UNION ALL
 SELECT 'b'::text AS jenis,
    reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    COALESCE(signaobat_m.signa_nama::text, resepturdetail_t.signa ->> 'text'::text) AS signa,
    resepturdetail_t.qty_reseptur AS qty_obat,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
    resepturdetail_t.additional_data::json ->> 'satuan_konversi'::text AS satuan_konversi,
    resepturdetail_t.etiket,
    obatalkes_m.is_oral,
    NULL::date AS tglkadaluarsa,
        CASE
            WHEN resepturdetail_t.det IS NULL THEN resepturdetail_t.qty_reseptur
            ELSE resepturdetail_t.det
        END AS det,
    resepturdetail_t.is_deleted AS detail_is_deleted,
    resepturdetail_t.racikan_id,
    (resepturdetail_t.additional_data::json ->> 'nilai_konversi'::text)::character varying AS nilai_konversi,
    resepturdetail_t.nama_racikan,
    resepturdetail_t.qty_racikan,
    resepturdetail_t.satuan_racikan_id,
    COALESCE(resepturdetail_t.det_medis, resepturdetail_t.det, resepturdetail_t.qty_medis, resepturdetail_t.qty_reseptur) AS det_medis,
        CASE
            WHEN resepturdetail_t.qty_medis IS NOT NULL THEN resepturdetail_t.qty_medis
            ELSE resepturdetail_t.qty_reseptur
        END AS qty_resep,
        CASE
            WHEN resepturdetail_t.det IS NOT NULL THEN resepturdetail_t.det
            ELSE resepturdetail_t.qty_reseptur
        END AS qty_bayar,
    satuan_racikan.satuan_racikan,
    resepturdetail_t.is_kronis
   FROM reseptur_t
     LEFT JOIN ( SELECT b.reseptur_id,
            b.noresep,
            b.penjualanresep_id
           FROM penjualanresep_t b) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     JOIN ( SELECT b.reseptur_id,
            b.signa,
            b.qty_reseptur,
            b.qty_konversi,
            b.additional_data,
            b.etiket,
            b.det,
            b.is_deleted,
            b.racikan_id,
            b.signa_id,
            b.obatalkes_id,
            b.rke,
            b.nama_racikan,
            b.qty_racikan,
            b.satuan_racikan_id,
            b.qty_medis,
            b.det_medis,
            b.is_kronis
           FROM resepturdetail_t b) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
     JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_nama,
            b.is_oral
           FROM obatalkes_m b) obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama
           FROM ruangan_m b
          WHERE b.instalasi_id <> 1) ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT b.racikan_id,
            b.racikan_nama
           FROM racikan_m b) racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ( SELECT b.signa_id,
            b.signa_nama
           FROM signaobat_m b) signaobat_m ON resepturdetail_t.signa_id = signaobat_m.signa_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama AS satuan_racikan
           FROM satuanunit_m a) satuan_racikan ON resepturdetail_t.satuan_racikan_id = satuan_racikan.satuanunit_id
UNION ALL
 SELECT 'c'::text AS jenis,
    NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    COALESCE(signaobat_m.signa_nama::text, obatalkespasien_t.signa ->> 'text'::text) AS signa,
    (obatalkespasien_t.additional_data::json ->> 'qty_input'::text)::double precision AS qty_obat,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
    obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text AS satuan_konversi,
    obatalkespasien_t.etiket,
    obatalkes_m.is_oral,
    NULL::date AS tglkadaluarsa,
    obatalkespasien_t.det,
    obatalkespasien_t.is_deleted AS detail_is_deleted,
    obatalkespasien_t.racikan_id,
    (obatalkespasien_t.additional_data::json ->> 'nilai_konversi'::text)::character varying AS nilai_konversi,
    obatalkespasien_t.nama_racikan,
    obatalkespasien_t.qty_racikan,
    obatalkespasien_t.satuan_racikan_id,
    COALESCE(obatalkespasien_t.det_medis, obatalkespasien_t.det, obatalkespasien_t.qty_medis, obatalkespasien_t.qty_oa) AS det_medis,
        CASE
            WHEN obatalkespasien_t.qty_medis IS NOT NULL THEN obatalkespasien_t.qty_medis
            ELSE obatalkespasien_t.qty_oa
        END AS qty_resep,
        CASE
            WHEN obatalkespasien_t.det IS NOT NULL THEN obatalkespasien_t.det
            ELSE obatalkespasien_t.qty_oa
        END AS qty_bayar,
    satuan_racikan.satuan_racikan,
    obatalkespasien_t.is_kronis
   FROM penjualanresep_t
     JOIN ( SELECT c.obatalkes_id,
            c.penjualanresep_id,
            c.rke,
            c.signa,
            c.qty_oa,
            c.qty_konversi,
            c.additional_data,
            c.det,
            c.is_deleted,
            c.racikan_id,
            c.signa_oa,
            c.etiket,
            c.obatalkespasien_id,
            c.nama_racikan,
            c.qty_racikan,
            c.satuan_racikan_id,
            c.det_medis,
            c.qty_medis,
            c.is_kronis
           FROM obatalkespasien_t c) obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
     JOIN ( SELECT c.obatalkes_id,
            c.obatalkes_nama,
            c.is_oral
           FROM obatalkes_m c) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT c.racikan_id,
            c.racikan_nama
           FROM racikan_m c) racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ( SELECT c.signa_id,
            c.signa_nama
           FROM signaobat_m c) signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama AS satuan_racikan
           FROM satuanunit_m a) satuan_racikan ON obatalkespasien_t.satuan_racikan_id = satuan_racikan.satuanunit_id
  WHERE penjualanresep_t.reseptur_id IS NULL  ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230815_125458_migrate_skema_resepkronis_worklistresepdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_125458_migrate_skema_resepkronis_worklistresepdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
