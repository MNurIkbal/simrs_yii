<?php

use yii\db\Migration;

/**
 * Class m201008_073246_migrate_20201008_laporanrekappenjualanobat
 */
class m201008_073246_migrate_20201008_laporanrekappenjualanobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanrekappenjualanobat_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanrekappenjualanobat_v\" AS  SELECT rekap.obatalkes_id,
    rekap.tgl_pelayanan,
    rekap.kode_obat,
    rekap.nama_obat,
    rekap.satuan_kecil,
    rekap.harga_netto,
    sum(rekap.qty) AS qty,
    sum((rekap.harga_netto * rekap.qty)) AS total
   FROM ( SELECT obatalkespasien_t.obatalkes_id,
            (to_char(penjualanresep_t.tglresep, 'YYYY-MM-DD'::text))::date AS tgl_pelayanan,
            obatalkes_m.obatalkes_kode AS kode_obat,
            obatalkes_m.obatalkes_nama AS nama_obat,
            ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
            ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_kecil,
            ((obatalkespasien_t.additional_data)::json ->> 'qty_input'::text) AS qty_input,
                CASE
                    WHEN (obatalkespasien_t.det_konversi IS NOT NULL) THEN COALESCE(obatalkespasien_t.det_konversi, (0)::double precision)
                    ELSE COALESCE(obatalkespasien_t.qty_konversi, (0)::double precision)
                END AS qty,
            COALESCE(obatalkes_m.harganetto, (0)::double precision) AS harga_netto
           FROM (((obatalkespasien_t
             JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN ( SELECT stokobatalkes_t.obatalkespasien_id
                   FROM stokobatalkes_t
                  WHERE (stokobatalkes_t.is_deleted = false)
                  GROUP BY stokobatalkes_t.obatalkespasien_id) fix ON ((obatalkespasien_t.obatalkespasien_id = fix.obatalkespasien_id)))
          WHERE ((obatalkespasien_t.racikan_id <> 2) OR (obatalkespasien_t.is_deleted = false))) rekap
  GROUP BY rekap.obatalkes_id, rekap.tgl_pelayanan, rekap.kode_obat, rekap.nama_obat, rekap.satuan_kecil, rekap.harga_netto
  ORDER BY rekap.obatalkes_id;");

        $this->execute('ALTER TABLE "public"."laporanrekappenjualanobat_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201008_073246_migrate_20201008_laporanrekappenjualanobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201008_073246_migrate_20201008_laporanrekappenjualanobat cannot be reverted.\n";

        return false;
    }
    */
}
