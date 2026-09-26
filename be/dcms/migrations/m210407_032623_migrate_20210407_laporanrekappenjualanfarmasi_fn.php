<?php

use yii\db\Migration;

/**
 * Class m210407_032623_migrate_20210407_laporanrekappenjualanfarmasi_fn
 */
class m210407_032623_migrate_20210407_laporanrekappenjualanfarmasi_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('CREATE OR REPLACE FUNCTION "public"."laporanrekappenjualanfarmasi_fn"("x_date" date, "y_date" date)
  RETURNS TABLE("kode_obat" varchar, "nama_obat" varchar, "satuan_kecil" varchar, "baseprice" float8, "qty" float8, "total" float8, "jenisobatalkes_id" int4, "jenisobatalkes_nama" varchar) AS $BODY$

BEGIN
    RETURN QUERY 
    SELECT *FROM (
         SELECT rekap.kode_obat::VARCHAR AS kode_obat,
    rekap.nama_obat::VARCHAR AS nama_obat,
    rekap.satuan_kecil::VARCHAR AS satuan_kecil,
    rekap.harga_netto::float8 AS baseprice,
    sum(rekap.qty) AS qty,
    sum(rekap.harga_netto * rekap.qty) AS total,
        rekap.jenisobatalkes_id,
        rekap.jenisobatalkes_nama
   FROM ( SELECT obatalkespasien_t.obatalkes_id,
            to_char(penjualanresep_t.tglresep, \'YYYY-MM-DD\'::text)::date AS tgl_pelayanan,
            obatalkes_m.obatalkes_kode AS kode_obat,
            obatalkes_m.obatalkes_nama AS nama_obat,
            obatalkespasien_t.additional_data::json ->> \'satuan_input\'::text AS satuan_input,
                CASE
                    WHEN (obatalkespasien_t.additional_data::json ->> \'satuan_konversi\'::text) = \'\'::text THEN satuankecil.satuanunit_nama::text
                    WHEN (obatalkespasien_t.additional_data::json ->> \'satuan_konversi\'::text) = NULL::text THEN satuankecil.satuanunit_nama::text
                    ELSE obatalkespasien_t.additional_data::json ->> \'satuan_konversi\'::text
                END AS satuan_kecil,
            obatalkespasien_t.additional_data::json ->> \'qty_input\'::text AS qty_input,
                CASE
                    WHEN obatalkespasien_t.det_konversi IS NOT NULL THEN COALESCE(obatalkespasien_t.det_konversi, 0::double precision)
                    ELSE COALESCE(((obatalkespasien_t.additional_data::json ->> \'qty_input\'::text)::double precision) * konv.nilai_konversi, obatalkespasien_t.qty_oa)
                END AS qty,
            COALESCE(obatalkes_m.harganetto, 0::double precision) AS harga_netto,
                        obatalkes_m.jenisobatalkes_id,
                        jenisobatalkes_m.jenisobatalkes_nama
           FROM obatalkespasien_t
             JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                         LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             LEFT JOIN satuanunit_m satuankecil ON obatalkes_m.satuankecil_id = satuankecil.satuanunit_id
             JOIN ( SELECT satuankonversi_m.obatalkes_id,
                    satuankonversi_m.satuanbesar_id,
                    satuankonversi_m.satuankecil_id,
                    satuankonversi_m.nilai_konversi
                   FROM satuankonversi_m
                  WHERE satuankonversi_m.is_deleted = false
                  GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuanbesar_id, satuankonversi_m.satuankecil_id, satuankonversi_m.nilai_konversi) konv ON obatalkespasien_t.obatalkes_id = konv.obatalkes_id AND (obatalkespasien_t.additional_data::json ->> \'satuaninput_id\'::text) = konv.satuanbesar_id::text AND obatalkespasien_t.satuankecil_id = konv.satuankecil_id
             JOIN ( SELECT stokobatalkes_t.obatalkespasien_id
                   FROM stokobatalkes_t
                  WHERE stokobatalkes_t.is_deleted = false
                  GROUP BY stokobatalkes_t.obatalkespasien_id) fix ON obatalkespasien_t.obatalkespasien_id = fix.obatalkespasien_id
          WHERE obatalkespasien_t.racikan_id <> 2 OR obatalkespasien_t.is_deleted = false AND (penjualanresep_t.status_reseptur = ANY (ARRAY[432, 660])) AND
                CASE
                    WHEN obatalkespasien_t.det_konversi IS NOT NULL THEN obatalkespasien_t.det_konversi > 0::double precision
                    ELSE COALESCE(((obatalkespasien_t.additional_data::json ->> \'qty_input\'::text)::double precision) * konv.nilai_konversi, obatalkespasien_t.qty_oa) > 0::double precision
                END) rekap
    WHERE rekap.tgl_pelayanan BETWEEN x_date::date and y_date::date
  GROUP BY rekap.obatalkes_id, rekap.kode_obat, rekap.nama_obat, rekap.satuan_kecil, rekap.harga_netto,
    rekap.jenisobatalkes_id,
        rekap.jenisobatalkes_nama
) x;
    
END
$BODY$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000;');

    $this->execute('ALTER FUNCTION "public"."laporanrekappenjualanfarmasi_fn"("x_date" date, "y_date" date) OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210407_032623_migrate_20210407_laporanrekappenjualanfarmasi_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210407_032623_migrate_20210407_laporanrekappenjualanfarmasi_fn cannot be reverted.\n";

        return false;
    }
    */
}
