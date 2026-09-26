<?php

use yii\db\Migration;

/**
 * Class m211227_052522_migrate_function_basecalrofn_databasecalrofn_rekapdatabasecalrofn_27122021
 */
class m211227_052522_migrate_function_basecalrofn_databasecalrofn_rekapdatabasecalrofn_27122021 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('DROP VIEW if exists public.basecalrofn;');
		
        $this->execute("
			CREATE OR REPLACE FUNCTION public.basecalrofn()
			  RETURNS TABLE(obatalkes_id int4, count int8, max float8, min float8, avg float8, min_resep float8, jenisobatalkes_id int4, last_7 float8, last_14 float8, last_30 float8, movingcriteria_id int4, move_category varchar) AS \$BODY\$
			declare vdate date;
			begin
			vdate = CURRENT_DATE;
			return query 
			select
			calc.obatalkes_id,
			calc.count,
			calc.max,
			calc.min,
			calc.avg,
			calc.min_resep,
			calc.jenisobatalkes_id::int,
			calc.last_7,
			calc.last_14,
			calc.last_30,
			mc.movingcriteria_id::int,
			mc.criteria as move_category
			from (
			    SELECT count_stok.obatalkes_id,
			    count(count_stok.tanggal) AS count,
			    max(count_stok.stok_out) AS max,
			    min(count_stok.stok_out) AS min,
			    sum(count_stok.stok_out) / count(count_stok.tanggal)::double precision AS avg,
			    min(count_stok.min_resep) AS min_resep,
			    obatalkes_m.jenisobatalkes_id,
			    sum(count_stok.last_7) AS last_7,
			    sum(count_stok.last_14) AS last_14,
			    sum(count_stok.last_30) AS last_30
			   FROM ( SELECT detail.obatalkes_id,
			            detail.tanggal,
			            sum(detail.qtystok_out) AS stok_out,
			            min(detail.qty_resep) AS min_resep,
			            sum(
			                CASE
			                    WHEN detail.tanggal >= (vdate - '7 days'::interval) THEN detail.qtystok_out
			                    ELSE 0::double precision
			                END) AS last_7,
			            sum(
			                CASE
			                    WHEN detail.tanggal >= (vdate - '14 days'::interval) THEN detail.qtystok_out
			                    ELSE 0::double precision
			                END) AS last_14,
			            sum(
			                CASE
			                    WHEN detail.tanggal >= (vdate - '30 days'::interval) THEN detail.qtystok_out
			                    ELSE 0::double precision
			                END) AS last_30
			           FROM ( SELECT stokobatalkes_t.obatalkes_id,
			                    to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date AS tanggal,
			                    stokobatalkes_t.qtystok_out,
			                        CASE
			                            WHEN stokobatalkes_t.obatalkespasien_id IS NOT NULL AND stokobatalkes_t.tglstok_out IS NOT NULL THEN stokobatalkes_t.qtystok_out
			                            ELSE NULL::double precision
			                        END AS qty_resep,
			                    stokobatalkes_t.mutasiobatdetail_id,
			                    mutasiobatdetail.ruanganasal_id,
			                    mutasiobatdetail.ruangantujuan_id
			                   FROM stokobatalkes_t
			                     LEFT JOIN ( SELECT mutasiobatdetail_t.mutasiobatdetail_id,
			                            mutasiobatruangan_t.ruanganasal_id,
			                            mutasiobatruangan_t.ruangantujuan_id
			                           FROM mutasiobatdetail_t
			                             JOIN mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id) mutasiobatdetail ON stokobatalkes_t.mutasiobatdetail_id = mutasiobatdetail.mutasiobatdetail_id
			                  WHERE (stokobatalkes_t.mutasiobatdetail_id IS NOT NULL 
			                  AND NOT (mutasiobatdetail.ruangantujuan_id IN 
			                  ( SELECT ruangan_m.ruangan_id
			                           FROM ruangan_m
			                          WHERE ruangan_m.instalasi_id = 6)) 
			                          AND mutasiobatdetail.ruanganasal_id = 25 OR stokobatalkes_t.obatalkespasien_id IS NOT NULL AND stokobatalkes_t.tglstok_out IS NOT NULL) 
			                  AND (stokobatalkes_t.tglstok_out >= vdate::timestamp without time zone 
			                  AND stokobatalkes_t.tglstok_out <= (vdate - '30 days'::interval) 
			                  OR stokobatalkes_t.tglstok_out >= (vdate - '30 days'::interval) 
			                  AND stokobatalkes_t.tglstok_out <= vdate::timestamp without time zone)
			                  ORDER BY stokobatalkes_t.obatalkes_id, (to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date)) detail
			          GROUP BY detail.obatalkes_id, detail.tanggal
			          ORDER BY detail.obatalkes_id, detail.tanggal) count_stok
			     LEFT JOIN obatalkes_m ON count_stok.obatalkes_id = obatalkes_m.obatalkes_id
			  GROUP BY count_stok.obatalkes_id, obatalkes_m.jenisobatalkes_id
			) calc
			left join movingcriteria_m mc on calc.count <= mc.max and calc.count >= mc.min;
			END; 
			\$BODY\$
			  LANGUAGE plpgsql VOLATILE
			  COST 100
			  ROWS 1000;");
			  
			  $this->execute('DROP VIEW if exists public.databasecalrofn;');
			  
	          $this->execute("
				  CREATE OR REPLACE FUNCTION public.databasecalrofn()
				    RETURNS TABLE(obatalkes_id int4, tanggal date, qtystok_out float8, qty_resep float8, mutasiobatdetail_id int4, ruanganasal_id int4, ruangantujuan_id int4, nomutasioa varchar, ruanganresep_id int4, penjualanresep_id int4, noresep varchar, ruanganresep_nama varchar, ruanganasalmutasi_nama varchar, ruangantujuanmutasi_nama varchar, obatalkes_nama varchar, jenisobatalkes_nama varchar) AS \$BODY\$
				  declare vdate date;
				  begin
				  vdate = CURRENT_DATE;
				  return query 
				             select  
				             	detailtrans.obatalkes_id,
				             	detailtrans.tanggal,
				             	detailtrans.qtystok_out,
				             	detailtrans.qty_resep,
				                      detailtrans.mutasiobatdetail_id,
				                      detailtrans.ruanganasal_id,
				                      detailtrans.ruangantujuan_id,
				                      detailtrans.nomutasioa,
				                      detailtrans.ruanganresep_id,
				                      detailtrans.penjualanresep_id,
				                      detailtrans.noresep,
				                      ruangan_resep.ruangan_nama as ruanganresep_nama,
				                      ruangan_asal_mutasi.ruangan_nama as ruanganasalmutasi_nama,
				                      ruangan_tujuan_mutasi.ruangan_nama as ruangantujuanmutasi_nama,
				                      om.obatalkes_nama,
				                      jm.jenisobatalkes_nama
				             from (
				             SELECT stokobatalkes_t.obatalkes_id,
				                      to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date AS tanggal,
				                      stokobatalkes_t.qtystok_out,
				                          CASE
				                              WHEN stokobatalkes_t.obatalkespasien_id IS NOT NULL AND stokobatalkes_t.tglstok_out IS NOT NULL THEN stokobatalkes_t.qtystok_out
				                              ELSE NULL::double precision
				                          END AS qty_resep,
				                      stokobatalkes_t.mutasiobatdetail_id,
				                      mutasiobatdetail.ruanganasal_id,
				                      mutasiobatdetail.ruangantujuan_id,
				                      mutasiobatdetail.nomutasioa,
				                      resep.ruangan_id as ruanganresep_id,
				                      resep.penjualanresep_id,
				                      resep.noresep
				                     FROM stokobatalkes_t
				                       LEFT JOIN ( SELECT mutasiobatdetail_t.mutasiobatdetail_id,
				                              mutasiobatruangan_t.ruanganasal_id,
				                              mutasiobatruangan_t.ruangantujuan_id,
				                              mutasiobatruangan_t.nomutasioa
				                             FROM mutasiobatdetail_t
				                               JOIN mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id) mutasiobatdetail ON stokobatalkes_t.mutasiobatdetail_id = mutasiobatdetail.mutasiobatdetail_id
				                       left join ( select obatalkespasien_t.obatalkespasien_id,obatalkespasien_t.ruangan_id ,obatalkespasien_t.penjualanresep_id,penjualanresep_t.noresep
				                       	from obatalkespasien_t 
				                       	left join penjualanresep_t on penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id 
				                       ) resep ON stokobatalkes_t.obatalkespasien_id = resep.obatalkespasien_id
				                    WHERE (stokobatalkes_t.mutasiobatdetail_id IS NOT NULL AND NOT (mutasiobatdetail.ruangantujuan_id IN ( SELECT ruangan_m.ruangan_id
				                             FROM ruangan_m
				                            WHERE ruangan_m.instalasi_id = 6)) 
				                            AND mutasiobatdetail.ruanganasal_id = 25 OR stokobatalkes_t.obatalkespasien_id IS NOT NULL 
				                            AND stokobatalkes_t.tglstok_out IS NOT NULL) 
				                            AND (stokobatalkes_t.tglstok_out >= vdate::timestamp without time zone 
				                            AND stokobatalkes_t.tglstok_out <= (vdate - '30 days'::interval) 
				                            OR stokobatalkes_t.tglstok_out >= (vdate - '30 days'::interval) 
				                            AND stokobatalkes_t.tglstok_out <= vdate::timestamp without time zone)
				                    ORDER BY stokobatalkes_t.obatalkes_id, (to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date)
				                    ) detailtrans
				                    left join obatalkes_m om on om.obatalkes_id = detailtrans.obatalkes_id
				                    left join jenisobatalkes_m jm on jm.jenisobatalkes_id = om.jenisobatalkes_id 
				                    left join ruangan_m ruangan_resep on ruangan_resep.ruangan_id = detailtrans.ruanganresep_id
				                    left join ruangan_m ruangan_asal_mutasi on ruangan_asal_mutasi.ruangan_id = detailtrans.ruanganasal_id
				                    left join ruangan_m ruangan_tujuan_mutasi on ruangan_tujuan_mutasi.ruangan_id = detailtrans.ruangantujuan_id;
				  END; 
				  \$BODY\$
				    LANGUAGE plpgsql VOLATILE
				    COST 100
				    ROWS 1000;");
					
					 $this->execute('DROP VIEW if exists public.rekapdatabasecalrofn;');
					 
	   	          $this->execute("
					  CREATE OR REPLACE FUNCTION public.rekapdatabasecalrofn()
					    RETURNS TABLE(obatalkes_nama varchar, tgl_generate date, obatalkes_id int4, stok_out float8, min_resep float8, last_7 float8, last_14 float8, last_30 float8) AS \$BODY\$
					  declare vdate date;
					  begin
					  vdate = CURRENT_DATE;
					  return query 
					  select 
					  om.obatalkes_nama ,
					  t_date.tgl_generate,
					  datadetail.obatalkes_id,
					  datadetail.stok_out,
					  datadetail.min_resep,
					  datadetail.last_7,
					  datadetail.last_14,
					  datadetail.last_30
					  		from (SELECT 
					              CURRENT_DATE + i AS tgl_generate
					          FROM generate_series(-30, -1 ) i 
					          )t_date
					          left join (
					          SELECT detail.obatalkes_id,
					              detail.tanggal,
					              sum(detail.qtystok_out) AS stok_out,
					              min(detail.qty_resep) AS min_resep,
					              sum(
					                  CASE
					                      WHEN detail.tanggal >= (current_date - '7 days'::interval) THEN detail.qtystok_out
					                      ELSE 0::double precision
					                  END) AS last_7,
					              sum(
					                  CASE
					                      WHEN detail.tanggal >= (current_date - '14 days'::interval) THEN detail.qtystok_out
					                      ELSE 0::double precision
					                  END) AS last_14,
					              sum(
					                  CASE
					                      WHEN detail.tanggal >= (current_date - '30 days'::interval) THEN detail.qtystok_out
					                      ELSE 0::double precision
					                  END) AS last_30
					             FROM (
					             select  
					             	detailtrans.obatalkes_id,
					             	detailtrans.tanggal,
					             	detailtrans.qtystok_out,
					             	detailtrans.qty_resep,
					                      detailtrans.mutasiobatdetail_id,
					                      detailtrans.ruanganasal_id,
					                      detailtrans.ruangantujuan_id,
					                      detailtrans.nomutasioa,
					                      detailtrans.ruanganresep_id,
					                      detailtrans.penjualanresep_id,
					                      detailtrans.noresep,
					                      ruangan_resep.ruangan_nama as ruanganresep_nama,
					                      ruangan_asal_mutasi.ruangan_nama as ruanganasalmutasi_nama,
					                      ruangan_tujuan_mutasi.ruangan_nama as ruangantujuanmutasi_nama,
					                      om.obatalkes_nama,
					                      jm.jenisobatalkes_nama
					             from (
					             SELECT stokobatalkes_t.obatalkes_id,
					                      to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date AS tanggal,
					                      stokobatalkes_t.qtystok_out,
					                          CASE
					                              WHEN stokobatalkes_t.obatalkespasien_id IS NOT NULL AND stokobatalkes_t.tglstok_out IS NOT NULL THEN stokobatalkes_t.qtystok_out
					                              ELSE NULL::double precision
					                          END AS qty_resep,
					                      stokobatalkes_t.mutasiobatdetail_id,
					                      mutasiobatdetail.ruanganasal_id,
					                      mutasiobatdetail.ruangantujuan_id,
					                      mutasiobatdetail.nomutasioa,
					                      resep.ruangan_id as ruanganresep_id,
					                      resep.penjualanresep_id,
					                      resep.noresep
					                     FROM stokobatalkes_t
					                       LEFT JOIN ( SELECT mutasiobatdetail_t.mutasiobatdetail_id,
					                              mutasiobatruangan_t.ruanganasal_id,
					                              mutasiobatruangan_t.ruangantujuan_id,
					                              mutasiobatruangan_t.nomutasioa
					                             FROM mutasiobatdetail_t
					                               JOIN mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id) mutasiobatdetail ON stokobatalkes_t.mutasiobatdetail_id = mutasiobatdetail.mutasiobatdetail_id
					                       left join ( select obatalkespasien_t.obatalkespasien_id,obatalkespasien_t.ruangan_id ,obatalkespasien_t.penjualanresep_id,penjualanresep_t.noresep
					                       	from obatalkespasien_t 
					                       	left join penjualanresep_t on penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id 
					                       ) resep ON stokobatalkes_t.obatalkespasien_id = resep.obatalkespasien_id
					                    WHERE (stokobatalkes_t.mutasiobatdetail_id IS NOT NULL AND NOT (mutasiobatdetail.ruangantujuan_id IN ( SELECT ruangan_m.ruangan_id
					                             FROM ruangan_m
					                            WHERE ruangan_m.instalasi_id = 6)) 
					                            AND mutasiobatdetail.ruanganasal_id = 25 OR stokobatalkes_t.obatalkespasien_id IS NOT NULL 
					                            AND stokobatalkes_t.tglstok_out IS NOT NULL) 
					                            AND (stokobatalkes_t.tglstok_out >= current_date::timestamp without time zone 
					                            AND stokobatalkes_t.tglstok_out <= (current_date - '30 days'::interval) 
					                            OR stokobatalkes_t.tglstok_out >= (current_date - '30 days'::interval) 
					                            AND stokobatalkes_t.tglstok_out <= current_date::timestamp without time zone)
					                    ORDER BY stokobatalkes_t.obatalkes_id, (to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date)
					                    ) detailtrans
					                    left join obatalkes_m om on om.obatalkes_id = detailtrans.obatalkes_id
					                    left join jenisobatalkes_m jm on jm.jenisobatalkes_id = om.jenisobatalkes_id 
					                    left join ruangan_m ruangan_resep on ruangan_resep.ruangan_id = detailtrans.ruanganresep_id
					                    left join ruangan_m ruangan_asal_mutasi on ruangan_asal_mutasi.ruangan_id = detailtrans.ruanganasal_id
					                    left join ruangan_m ruangan_tujuan_mutasi on ruangan_tujuan_mutasi.ruangan_id = detailtrans.ruangantujuan_id
					                   )detail
					            GROUP BY detail.obatalkes_id, detail.tanggal
					            ORDER BY detail.obatalkes_id, detail.tanggal
					            )datadetail on datadetail.tanggal = t_date.tgl_generate
					            left join obatalkes_m om on om.obatalkes_id = datadetail.obatalkes_id;
					  END; 
					  \$BODY\$
					    LANGUAGE plpgsql VOLATILE
					    COST 100
					    ROWS 1000;");
					 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211227_052522_migrate_function_basecalrofn_databasecalrofn_rekapdatabasecalrofn_27122021 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211227_052522_migrate_function_basecalrofn_databasecalrofn_rekapdatabasecalrofn_27122021 cannot be reverted.\n";

        return false;
    }
    */
}
