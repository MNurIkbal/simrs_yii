<?php

use yii\db\Migration;

/**
 * Class m220915_103611_migrate_ordh_163_laporanrekappenjualanobat_v
 */
class m220915_103611_migrate_ordh_163_laporanrekappenjualanobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanrekappenjualanobat_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"laporanrekappenjualanobat_v\" AS  SELECT rekap.obatalkes_id,
    rekap.tgl_pelayanan,
    rekap.kode_obat,
    rekap.nama_obat,
    rekap.satuan_kecil,
    rekap.harga_netto,
    sum(rekap.qty) AS qty,
    sum(rekap.harga_netto * rekap.qty) AS total
   FROM ( SELECT obatalkespasien_t.obatalkes_id,
            to_char(COALESCE(penjualanresep_t.tglresep, obatalkespasien_t.tglpelayanan), 'YYYY-MM-DD'::text)::date AS tgl_pelayanan,
            obatalkes_m.obatalkes_kode AS kode_obat,
            obatalkes_m.obatalkes_nama AS nama_obat,
            obatalkespasien_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
                CASE
                    WHEN (obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text) = ''::text THEN satuankecil.satuanunit_nama::text
                    WHEN (obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text) = NULL::text THEN satuankecil.satuanunit_nama::text
                    ELSE obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text
                END AS satuan_kecil,
            obatalkespasien_t.additional_data::json ->> 'qty_input'::text AS qty_input,
                CASE
                    WHEN obatalkespasien_t.det_konversi IS NOT NULL THEN COALESCE(obatalkespasien_t.det_konversi, 0::double precision)
                    ELSE COALESCE(((obatalkespasien_t.additional_data::json ->> 'qty_input'::text)::double precision) * konv.nilai_konversi, obatalkespasien_t.qty_oa)
                END AS qty,
            COALESCE(obatalkes_m.harganetto, 0::double precision) AS harga_netto
           FROM obatalkespasien_t
             JOIN (SELECT a.penjualanresep_id,a.status_reseptur,a.tglresep from penjualanresep_t a )penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN (SELECT a.obatalkes_id,a.obatalkes_kode,a.obatalkes_nama,a.harganetto,a.jenisobatalkes_id,a.satuankecil_id from obatalkes_m a)obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN (SELECT a.satuanunit_id,a.satuanunit_nama from satuanunit_m a) satuankecil ON obatalkes_m.satuankecil_id = satuankecil.satuanunit_id
             LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
                    satuankonversi_m.satuanbesar_id,
                    satuankonversi_m.satuankecil_id,
                    satuankonversi_m.nilai_konversi
                   FROM satuankonversi_m
                  WHERE satuankonversi_m.is_deleted = false
                  GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuanbesar_id, satuankonversi_m.satuankecil_id, satuankonversi_m.nilai_konversi) konv ON obatalkespasien_t.obatalkes_id = konv.obatalkes_id AND (obatalkespasien_t.additional_data::json ->> 'satuaninput_id'::text) = konv.satuanbesar_id::text AND obatalkespasien_t.satuankecil_id = konv.satuankecil_id
             JOIN ( SELECT stokobatalkes_t.obatalkespasien_id
                   FROM stokobatalkes_t
                  WHERE stokobatalkes_t.is_deleted = false
                  GROUP BY stokobatalkes_t.obatalkespasien_id) fix ON obatalkespasien_t.obatalkespasien_id = fix.obatalkespasien_id
          WHERE (obatalkespasien_t.racikan_id <> 2 OR obatalkespasien_t.is_deleted = false) AND (penjualanresep_t.status_reseptur IS NULL OR penjualanresep_t.status_reseptur = 432 OR penjualanresep_t.status_reseptur = 660) AND
                CASE
                    WHEN obatalkespasien_t.det_konversi IS NOT NULL THEN obatalkespasien_t.det_konversi > 0::double precision
                    ELSE COALESCE(((obatalkespasien_t.additional_data::json ->> 'qty_input'::text)::double precision) * konv.nilai_konversi, obatalkespasien_t.qty_oa) > 0::double precision
                END) rekap
  GROUP BY rekap.obatalkes_id, rekap.tgl_pelayanan, rekap.kode_obat, rekap.nama_obat, rekap.satuan_kecil, rekap.harga_netto; ");
  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220915_103611_migrate_ordh_163_laporanrekappenjualanobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220915_103611_migrate_ordh_163_laporanrekappenjualanobat_v cannot be reverted.\n";

        return false;
    }
    */
}
