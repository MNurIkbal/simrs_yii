<?php

use yii\db\Migration;

/**
 * Class m220527_111307_migrate_mhg_1998_laporanstockinventory_fn
 */
class m220527_111307_migrate_mhg_1998_laporanstockinventory_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->execute('DROP FUNCTION if exists public.laporanstockinventory_fn;');

	           $this->execute("
	   			CREATE OR REPLACE FUNCTION public.laporanstockinventory_fn(x_date date)
	   			  RETURNS TABLE(ruangan_nama varchar, obatalkes_kode varchar, obatalkes_nama varchar, jenis_obat varchar, is_generik varchar, is_oral varchar, nilai_konv float8, stok_fisik float8, qty_satuankecil float8, qty_satuanbesar float8, stok float8, satuan_besar varchar, satuan_kecil varchar, baseprice_besar float8, baseprice_kecil float8, harga_konversi float8, harga_netto float8, harga float8, total_harga float8, total_satuankecil float8, total_satuanbesar float8, total_satuankecil_netto float8, total_satuanbesar_netto float8
	   			) AS  \$BODY\$
                    
	   								  BEGIN
	   								      RETURN QUERY 
	   								      --select * from laporanstockinventory_fn('2021-12-25')

										  SELECT  x.ruangan_nama,
										 			x.obatalkes_kode,
										 			x.obatalkes_nama,
										 			x.jenis_obat,
										 			x.is_generik,
										 			x.is_oral,
										 			x.nilai_konv,
										 			x.stok_fisik,
										 			x.qty_satuankecil,
										 			x.qty_satuanbesar,
										 			x.stok,
										 			x.satuan_besar,
										 			x.satuan_kecil,
										 			x.wa_satuan_kecil,
										 			x.wa_satuan_besar,
										 			x.baseprice_besar,
										 			x.baseprice_kecil,
										 			x.harga_konversi,
										 			x.harga_netto,
										 			x.harga,
										 			x.total_harga,
										 			x.total_satuankecil,
										 			x.total_satuanbesar,
										 			x.total_satuankecil_netto,
										 			x.total_satuanbesar_netto FROM (
										 			SELECT 
										 								              ruangan_m.ruangan_nama as ruangan_nama,
										 								              obatalkes_m.obatalkes_kode as obatalkes_kode,
										 								              obatalkes_m.obatalkes_nama as obatalkes_nama, 
										 								              jenisobatalkes_m.jenisobatalkes_nama as jenis_obat,
										 								              CASE
										 								                  WHEN obatalkes_m.is_generik = TRUE THEN 'Generik'::VARCHAR
										 								                  ELSE 'Non Generik'::VARCHAR
										 								              end as is_generik,
										 								                  CASE
										 								                  WHEN obatalkes_m.is_oral = TRUE THEN 'Oral'::VARCHAR
										 								                  ELSE 'Non Oral'::VARCHAR
										 								              end as is_oral,
										 								             konversi.nilai_konversi as nilai_konv,
										 								             stok.fisik as stok_fisik,
										 								             stok.fisik as qty_satuankecil,
										 								             (stok.fisik / konversi.nilai_konversi) as qty_satuanbesar,
										 								             stok.fisik as stok,
										 								             satuanbesar.satuanunit_nama as satuan_besar,
										 								             satuankecil.satuanunit_nama as satuan_kecil,
										 								             coalesce(obatalkes_m.harganetto) as baseprice_kecil,
										 								             (coalesce(obatalkes_m.harganetto) * obatalkes_m.kemasan_besar) as baseprice_besar,
										 								             (obatalkes_m.harganetto * konversi.nilai_konversi) as harga_konversi,
										 								             obatalkes_m.harganetto as harga_netto,
										 								             -- (obatalkes_m.harganetto * konversi.nilai_konversi * (stok.fisik / konversi.nilai_konversi)) as harga,
										 								            (coalesce(lr.weighted_avg,obatalkes_m.harganetto) * konversi.nilai_konversi * (stok.fisik / konversi.nilai_konversi)) as harga,
										 								            (stok.fisik * (coalesce(lr.weighted_avg,obatalkes_m.harganetto)))  as total_harga,
										 								            (stok.fisik * (coalesce(lr.weighted_avg,obatalkes_m.harganetto))) as total_satuankecil,
										 								            ((stok.fisik) * (COALESCE(lr.weighted_avg,obatalkes_m.harganetto) )) as total_satuanbesar,
										 														(stok.fisik * (coalesce(obatalkes_m.harganetto))) as total_satuankecil_netto,
										 								            (((COALESCE(obatalkes_m.harganetto)* konversi.nilai_konversi) * (stok.fisik / konversi.nilai_konversi))) as total_satuanbesar_netto,
										 														lr.weighted_avg as wa_satuan_kecil,
										 														lr.weighted_avg * konversi.nilai_konversi as wa_satuan_besar
										 								              FROM (
										 								              SELECT 
										 								                  ruangan_id,
										 								                  obatalkes_id,
										 								                  SUM(qtystok_in-qtystok_out) as fisik
										 								              FROM stokobatalkes_t
										 								              WHERE (CASE 
										 								                              WHEN stokobatalkes_t.tglstok_in is not null THEN stokobatalkes_t.tglstok_in 
										 								                              ELSE stokobatalkes_t.tglstok_out 
										 								                          END) <= x_date::date + interval '23 hours 59 minutes'
										 								              GROUP BY ruangan_id,obatalkes_id
										 								          ) stok
										 								          LEFT join (select r.ruangan_id,r.ruangan_nama from ruangan_m r) ruangan_m on ruangan_m.ruangan_id = stok.ruangan_id
										 								          LEFT join (SELECT ob.obatalkes_id,ob.obatalkes_kode,ob.obatalkes_nama,ob.jenisobatalkes_id,ob.satuanbesar_id,ob.satuankecil_id,ob.is_generik,ob.is_oral,ob.harganetto,ob.kemasan_besar from obatalkes_m ob ) obatalkes_m on obatalkes_m.obatalkes_id = stok.obatalkes_id
										 								          LEFT JOIN (select j.jenisobatalkes_id,j.jenisobatalkes_nama from jenisobatalkes_m j ) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
										 								          LEFT JOIN (SELECT 
										 								                           satuankecil_id,
										 								                           satuanbesar_id,
										 								                           nilai_konversi,
										 								                           obatalkes_id,
										 								                           is_deleted,
										 								                           is_active
										 								                           FROM satuankonversi_m) konversi ON konversi.satuanbesar_id = obatalkes_m.satuanbesar_id 
										 								                           AND konversi.satuankecil_id = obatalkes_m.satuankecil_id 
										 								                           AND konversi.obatalkes_id = stok.obatalkes_id
										 								                           and konversi.is_deleted = false 
										 								                           and konversi.is_active = true
										 								          LEFT JOIN (select  s1.satuanunit_id,s1.satuanunit_nama from satuanunit_m s1) satuankecil ON satuankecil.satuanunit_id = obatalkes_m.satuankecil_id
										 								          LEFT JOIN  (select  s2.satuanunit_id,s2.satuanunit_nama from satuanunit_m s2) satuanbesar ON satuanbesar.satuanunit_id = obatalkes_m.satuanbesar_id
										 								  				-- JOIN Waigthed Average
										 								          LEFt JOIN ( select stok.obatalkes_id,
										 								  					stok.ruangan_id,
										 								             coalesce(logstok.weighted_avg,om.harganetto) as weighted_avg
										 								             FROM (
										 								             SELECT 
										 								                  ruangan_id,
										 								                  obatalkes_id,
										 								                  max(stokobatalkes_id) as id_stok,
										 								                  SUM(qtystok_in-qtystok_out) as fisik
										 								              FROM stokobatalkes_t
										 								              WHERE (CASE 
										 								                         WHEN stokobatalkes_t.tglstok_in is not null THEN stokobatalkes_t.tglstok_in 
										 								                         ELSE stokobatalkes_t.tglstok_out 
										 								                         END) <=  x_date::date + interval '23 hours 59 minutes'
										 								              GROUP BY ruangan_id,obatalkes_id
										 								          ) stok
										 								          left join (select o.obatalkes_id,o.obatalkes_kode,o.obatalkes_nama,o.harganetto from obatalkes_m o) om on om.obatalkes_id = stok.obatalkes_id
										 								          left join (SELECT l1.logasetobat_id,l1.weighted_avg,l1.obatalkes_id,l1.ruangan_id,l1.stokobatalkes_id from logasetobat_r l1) logstok on logstok.stokobatalkes_id = stok.id_stok ) lr on lr.obatalkes_id = stok.obatalkes_id and lr.ruangan_id = stok.ruangan_id
										 								          --LEFT JOIN (select lr3.weighted_avg,lr3.obatalkes_id,lr3.ruangan_id from logasetobat_r lr3 
										 								          --order by lr3.stokobatalkes_id desc) lr3 on stok.obatalkes_id = lr3.obatalkes_id and lr3.ruangan_id =ruangan_m.ruangan_id
										 								          ORDER by ruangan_m.ruangan_nama,obatalkes_m.obatalkes_kode) x;
	   								  END
	   								 \$BODY\$
	   			  LANGUAGE plpgsql IMMUTABLE
	   			  COST 100
	   			  ROWS 1000			
	              ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220527_111307_migrate_mhg_1998_laporanstockinventory_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220527_111307_migrate_mhg_1998_laporanstockinventory_fn cannot be reverted.\n";

        return false;
    }
    */
}
