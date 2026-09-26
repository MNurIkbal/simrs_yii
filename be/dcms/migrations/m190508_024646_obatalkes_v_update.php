<?php

use yii\db\Migration;

/**
 * Class m190508_024646_obatalkes_v_update
 */
class m190508_024646_obatalkes_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        DROP VIEW obatalkes_v;
        ');

        $this->execute('
   CREATE OR REPLACE VIEW obatalkes_v AS 
 SELECT hit.obatalkes_id,
    hit.obatalkes_nama,
    hit.jenisobatalkes_id,
    hit.jenisobatalkes_nama,
    hit.ven_id,
    hit.ven,
    hit.groupinacbg_id,
    hit.lead_time,
    hit.avg_usage,
    hit.min_order,
    hit.max_order,
    hit.nilai_ro,
    hit.margin,
    hit.ppn,
    hit.disc,
    hit.hn_last::integer AS hn_last,
    hit.a1::integer AS hn_last_margin,
    hit.a2::integer AS hn_last_diskon,
    hit.a3::integer AS hn_last_margin_diskon,
    hit.a4::integer AS hn_last_ppn,
    hit.a5::integer AS hargajual_last,
    hit.hn_min::integer AS hn_min,
    hit.b1::integer AS hn_min_margin,
    hit.b2::integer AS hn_min_diskon,
    hit.b3::integer AS hn_min_margin_diskon,
    hit.b4::integer AS hn_min_ppn,
    hit.b5::integer AS hargajual_min,
    hit.hn_max::integer AS hn_max,
    hit.c1::integer AS hn_max_margin,
    hit.c2::integer AS hn_max_diskon,
    hit.c3::integer AS hn_max_margin_diskon,
    hit.c4::integer AS hn_max_ppn,
    hit.c5::integer AS hargajual_max,
    hit.hn_avg::integer AS hn_avg,
    hit.d1::integer AS hn_avg_margin,
    hit.d2::integer AS hn_avg_diskon,
    hit.d3::integer AS hn_avg_margin_diskon,
    hit.d4::integer AS hn_avg_ppn,
    hit.d5::integer AS hargajual_avg,
    hit.harga_jual::integer AS hargaygdipakai,
    hit.harganetto::integer AS harganetto_ygdipakai,
        CASE
            WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.hn_max::integer
            WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.hn_min::integer
            WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.hn_avg::integer
            ELSE hit.hn_last::integer
        END AS harga_sugesstion,
        CASE
            WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN
            CASE COALESCE(hit.harganetto, 0::double precision) - COALESCE(hit.hn_max, 0::double precision)
                WHEN 0 THEN 0
                ELSE 1
            END
            WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN
            CASE COALESCE(hit.harganetto, 0::double precision) - COALESCE(hit.hn_min, 0::double precision)
                WHEN 0 THEN 0
                ELSE 1
            END
            WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN
            CASE COALESCE(hit.harganetto, 0::double precision) - COALESCE(hit.hn_avg, 0::double precision)
                WHEN 0 THEN 0
                ELSE 1
            END
            ELSE
            CASE COALESCE(hit.harganetto, 0::double precision) - COALESCE(hit.hn_last, 0::double precision)
                WHEN 0 THEN 0
                ELSE 1
            END
        END AS selisih,
        CASE
            WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c1::integer
            WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b1::integer
            WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d1::integer
            ELSE hit.a1::integer
        END AS hn_margin,
        CASE
            WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c2::integer
            WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b2::integer
            WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d2::integer
            ELSE hit.a2::integer
        END AS hn_diskon,
        CASE
            WHEN hit.hargaygdigunakan::text = \'MAX\'::text THEN hit.c4::integer
            WHEN hit.hargaygdigunakan::text = \'MIN\'::text THEN hit.b4::integer
            WHEN hit.hargaygdigunakan::text = \'AVG\'::text THEN hit.d4::integer
            ELSE hit.a4::integer
        END AS hn_ppn,
    hit.satuankecil_id,
    hit.satuankecil_nama,
    hit.group_jenisobat
   FROM ( SELECT obatalkes_m.obatalkes_id,
            obatalkes_m.obatalkes_nama,
            obatalkes_m.jenisobatalkes_id,
            jenisobatalkes_m.jenisobatalkes_nama,
            obatalkes_m.ven AS ven_id,
            ven.lookup_name AS ven,
            obatalkes_m.harganetto,
            obatalkes_m.groupinacbg_id,
            obatalkes_m.lead_time,
            obatalkes_m.avg_usage,
            obatalkes_m.min_order,
            obatalkes_m.max_order,
            obatalkes_m.nilai_ro,
            obatalkes_m.hargaterakhir AS hn_last,
            obatalkes_m.hargaminimum AS hn_min,
            obatalkes_m.hargamaksimum AS hn_max,
            obatalkes_m.hargaratarata AS hn_avg,
            konfigfarmasi_k.persenppn AS ppn,
            fgetpersenmargin(obatalkes_m.harganetto) AS margin,
            konfigfarmasi_k.persen_diskon AS disc,
            konfigfarmasi_k.hargaygdigunakan,
            obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision AS a1,
            (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a2,
            obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a3,
            (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a4,
            obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a5,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision AS b1,
            (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b2,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b3,
            (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b4,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b5,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision AS c1,
            (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c2,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c3,
            (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c4,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c5,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision AS d1,
            (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d2,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d3,
            (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d4,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d5,
            obatalkes_m.satuankecil_id,
            satuan_kecil.satuanunit_nama AS satuankecil_nama,
            obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS harga_jual,
            jenisobatalkes_m.group_jenisobat
           FROM obatalkes_m
             JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             JOIN lookup_m ven ON obatalkes_m.ven = ven.lookup_id
             JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
             LEFT JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
          WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false) hit;

        ');

        $this->execute('
ALTER TABLE obatalkes_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190508_024646_obatalkes_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190508_024646_obatalkes_v_update cannot be reverted.\n";

        return false;
    }
    */
}
