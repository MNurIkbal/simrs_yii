<?php

use yii\db\Migration;

/**
 * Class m210711_033022_imporve_3854_optimizedetailformulirstokopname
 */
class m210711_033022_imporve_3854_optimizedetailformulirstokopname extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW if exists "public"."detailformulirstokopname_v";');

    $this->execute("
        CREATE VIEW \"public\".\"detailformulirstokopname_v\" AS  SELECT formstokopname_t.formstokopname_id,
    formstokopname_t.formulirstokopname_id,
    formstokopname_t.volume_stok AS stok_sistem,
    formstokopname_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nama,
    formstokopname_t.nobatch,
        CASE
            WHEN stokobatalkes_t.tglkadaluarsa IS NULL THEN formstokopname_t.tglkadaluarsa
            ELSE stokobatalkes_t.tglkadaluarsa
        END AS tglkadaluarsa,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS hargajual,
    obatalkes_m.harganetto,
    formstokopname_t.stokobatalkes_id,
    obatalkes_m.satuankecil_id,
    sat_kecil.satuanunit_nama,
    rakobat_m.rakobat_nama,
    obatalkes_m.obatalkes_kode,
        CASE
            WHEN rakobat_m.parentrakobat_id IS NOT NULL THEN rakobat_m.parentrakobat_id::bigint
            ELSE rakobat_m.rakobat_id
        END AS rakobat_id,
    rakobat_m.rakobat_id AS laciobat_id,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    stokobatalkes_r.qty_sisa AS stok_saatini,
    stokobatalkes.stok_in,
    stokobatalkes.stok_out
   FROM formstokopname_t
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.obatalkes_namalain,
            a.obatalkes_kode,
            a.harganetto,
            a.satuankecil_id,
            a.satuanbesar_id
           FROM obatalkes_m a) obatalkes_m ON formstokopname_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT b.stokobatalkes_id,
            b.tglkadaluarsa
           FROM stokobatalkes_t b) stokobatalkes_t ON formstokopname_t.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
     LEFT JOIN ( SELECT c.obatalkes_id,
            c.ruangan_id,
            c.qty_sisa
           FROM stokobatalkes_r c) stokobatalkes_r ON formstokopname_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND formstokopname_t.ruangan_id = stokobatalkes_r.ruangan_id
     LEFT JOIN ( SELECT d.satuanunit_id,
            d.satuanunit_nama
           FROM satuanunit_m d) sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT e.ruangan_id,
            e.obatalkes_id,
            e.rakobat_id
           FROM konfigrak_m e) konfigrak_m ON formstokopname_t.ruangan_id = konfigrak_m.ruangan_id AND formstokopname_t.obatalkes_id = konfigrak_m.obatalkes_id
     LEFT JOIN ( SELECT f.rakobat_id,
            f.rakobat_nama,
            f.parentrakobat_id
           FROM rakobat_m f) rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
     LEFT JOIN ( SELECT g.obatalkes_id,
            g.satuankecil_id,
            g.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            g.nilai_konversi
           FROM satuankonversi_m g
             LEFT JOIN ( SELECT g1.satuanunit_id,
                    g1.satuanunit_nama
                   FROM satuanunit_m g1) uom_besar ON g.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN ( SELECT g2.satuanunit_id,
                    g2.satuanunit_nama
                   FROM satuanunit_m g2) uom_kecil ON g.satuankecil_id = uom_kecil.satuanunit_id
          WHERE g.is_deleted = false AND g.is_active = true
          GROUP BY g.obatalkes_id, g.satuankecil_id, g.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, g.nilai_konversi) uom ON obatalkes_m.obatalkes_id = uom.obatalkes_id AND obatalkes_m.satuanbesar_id = uom.satuanbesar_id AND obatalkes_m.satuankecil_id = uom.satuankecil_id
     LEFT JOIN ( SELECT h.obatalkes_id,
            h.ruangan_id,
            sum(h.qtystok_in) AS stok_in,
            sum(h.qtystok_out) AS stok_out,
            form_so.formulirstokopname_id
           FROM stokobatalkes_t h
             LEFT JOIN ( SELECT h1.created_date,
                    h1.formulirstokopname_id
                   FROM formulirstokopname_t h1) form_so ON h.created_date > form_so.created_date
          GROUP BY h.obatalkes_id, form_so.formulirstokopname_id, h.ruangan_id) stokobatalkes ON stokobatalkes.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes.formulirstokopname_id = formstokopname_t.formulirstokopname_id AND stokobatalkes.ruangan_id = formstokopname_t.ruangan_id
  WHERE formstokopname_t.is_active = true AND formstokopname_t.is_deleted = false;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"fgethargajualobat\"(\"vobatalkes_id\" int4)
  RETURNS \"pg_catalog\".\"float8\" AS \$BODY\$
            DECLARE vharga_jual float8;
            BEGIN
                SELECT
                    (((
                    obatalkes_m.harganetto + (( obatalkes_m.harganetto * fgetpersenmargin ( obatalkes_m.harganetto )) / ( 100 ) :: DOUBLE PRECISION )) - (((
                    obatalkes_m.harganetto + (( obatalkes_m.harganetto * fgetpersenmargin ( obatalkes_m.harganetto )) / ( 100 ) :: DOUBLE PRECISION )) * konfigfarmasi_k.persen_diskon 
                    ) / ( 100 ) :: DOUBLE PRECISION 
                    )) + ((((
                    obatalkes_m.harganetto + (( obatalkes_m.harganetto * fgetpersenmargin ( obatalkes_m.harganetto )) / ( 100 ) :: DOUBLE PRECISION )) - (((
                    obatalkes_m.harganetto + (( obatalkes_m.harganetto * fgetpersenmargin ( obatalkes_m.harganetto )) / ( 100 ) :: DOUBLE PRECISION )) * konfigfarmasi_k.persen_diskon 
                    ) / ( 100 ) :: DOUBLE PRECISION 
                    )) * konfigfarmasi_k.persenppn 
                    ) / ( 100 ) :: DOUBLE PRECISION 
                    )) AS harga_jual 
                INTO vharga_jual
                FROM obatalkes_m
                    JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted =FALSE 
                              WHERE obatalkes_m.obatalkes_id = vobatalkes_id ;
                
                RETURN COALESCE(vharga_jual,0);
                    
            END
            \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210711_033022_imporve_3854_optimizedetailformulirstokopname cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210711_033022_imporve_3854_optimizedetailformulirstokopname cannot be reverted.\n";

        return false;
    }
    */
}
