<?php

use yii\db\Migration;

/**
 * Class m220216_102318_migrate_cdh148_basecalrofn
 */
class m220216_102318_migrate_cdh148_basecalrofn extends Migration
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
			              sum(detail.qty_resep) AS min_resep,
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
			  									  JOIN (select obatalkespasien_id, pendaftaran_id from obatalkespasien_t where pendaftaran_id is not null) obatalkespasien_t
			  										on stokobatalkes_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
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
			       where obatalkes_m.is_deleted is false and obatalkes_m.is_active is true
			    GROUP BY count_stok.obatalkes_id, obatalkes_m.jenisobatalkes_id
			  ) calc
			  left join movingcriteria_m mc on calc.count <= mc.max and calc.count >= mc.min;
			  END; 
			  \$BODY\$
			    LANGUAGE plpgsql VOLATILE
			    COST 100
			    ROWS 1000;");
				
				$this->execute('DROP VIEW if exists public.basecalro_minresep_fn;');
				
		        $this->execute("
					CREATE OR REPLACE FUNCTION public.basecalro_minresep_fn()
					  RETURNS TABLE(obatalkes_id int4, min_resep float8, jenisobatalkes_id int4) AS  \$BODY\$
					declare vdate date;
					begin
					vdate = CURRENT_DATE;
					return query 
					select
						calc.obatalkes_id::int,
						calc.min_resep::float,
					  calc.jenisobatalkes_id::int
					from (
					    SELECT count_stok.obatalkes_id,
					    min(count_stok.min_resep) AS min_resep,
					    obatalkes_m.jenisobatalkes_id
					   FROM ( SELECT detail.obatalkes_id,
					            detail.tanggal,
					            sum(detail.qtystok_out) AS stok_out,
					            sum(detail.qty_resep) AS min_resep
					           FROM ( SELECT stokobatalkes_t.obatalkes_id,
					           			stokobatalkes_t.obatalkespasien_id,
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
														  JOIN (select obatalkespasien_id, pendaftaran_id from obatalkespasien_t where pendaftaran_id is not null) obatalkespasien_t
															on stokobatalkes_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
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
					          , detail.obatalkespasien_id
					          ORDER BY detail.obatalkes_id, detail.tanggal) count_stok
					     LEFT JOIN obatalkes_m ON count_stok.obatalkes_id = obatalkes_m.obatalkes_id
					     where obatalkes_m.is_deleted is false and obatalkes_m.is_active is true
					  GROUP BY count_stok.obatalkes_id, obatalkes_m.jenisobatalkes_id
					) calc;
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
        echo "m220216_102318_migrate_cdh148_basecalrofn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220216_102318_migrate_cdh148_basecalrofn cannot be reverted.\n";

        return false;
    }
    */
}
