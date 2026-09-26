<?php

use yii\db\Migration;

/**
 * Class m220929_054920_migrate_mhg_4127_new_basecalrofn
 */
class m220929_054920_migrate_mhg_4127_new_basecalrofn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		
        $this->execute("
			DROP FUNCTION public.new_basecalrofn; 
        ");

        $this->execute("
			CREATE OR REPLACE FUNCTION public.new_basecalrofn(vpemakaianruangan bool=false, vbmhp bool=false, vmutasi bool=false)
			  RETURNS TABLE(obatalkes_id int4, count int8, max float8, real_max float8, min float8, avg float8, min_resep float8, jenisobatalkes_id int4, last_7 float8, last_14 float8, last_30 float8, movingcriteria_id int4, move_category varchar) AS \$BODY\$
									    begin
									        return query
									        SELECT
									            calc.obatalkes_id,
									            calc.count,
									            CASE WHEN mc.criteria = 'FAST' THEN calc.max / 2 
									            WHEN mc.criteria = 'MED FAST' THEN calc.max / 2
									            ELSE calc.max END as max,
									            calc.max::double precision as real_max,
									            calc.min,
									            calc.avg,
									            calc.min_resep,
									            calc.jenisobatalkes_id::INT,
									            calc.last_7,
									            calc.last_14,
									            calc.last_30,
									            mc.movingcriteria_id::INT,
									            mc.criteria AS move_category
									        FROM (
									            SELECT 
									                count_stok.obatalkes_id,
									                COUNT(count_stok.tanggal) AS count,
									                MAX(count_stok.stok_out) AS max,
									                MIN(count_stok.stok_out) AS min,
									                SUM(count_stok.stok_out) / COUNT(count_stok.tanggal)::double precision AS avg,
									                MIN(count_stok.min_resep) AS min_resep,
									                obatalkes_m.jenisobatalkes_id,
									                SUM(count_stok.last_7) AS last_7,
									                SUM(count_stok.last_14) AS last_14,
									                SUM(count_stok.last_30) AS last_30
									            FROM ( 
									                SELECT 
									                    detail.obatalkes_id,
									                    detail.tanggal,
									                    SUM(detail.qtystok_out) AS stok_out,
									                    SUM(detail.qty_resep) AS min_resep,
									                    SUM(
									                        CASE WHEN detail.tanggal >= (current_date - '7 days'::interval) 
									                        THEN detail.qtystok_out ELSE 0::double precision END
									                    ) AS last_7,
									                    SUM(
									                        CASE WHEN detail.tanggal >= (current_date - '14 days'::interval) 
									                        THEN detail.qtystok_out ELSE 0::double precision END
									                    ) AS last_14,
									                    SUM(
									                        CASE WHEN detail.tanggal >= (current_date - '30 days'::interval) 
									                        THEN detail.qtystok_out ELSE 0::double precision END
									                    ) AS last_30
									                FROM ( 
									                    select 
									                        stokobatalkes_t.stokobatalkes_id,
									                        stokobatalkes_t.obatalkes_id,
									                        to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date AS tanggal,
									                        stokobatalkes_t.qtystok_out,
									                        CASE 
									                            WHEN stokobatalkes_t.obatalkespasien_id IS NOT NULL 
									                            AND stokobatalkes_t.tglstok_out IS NOT NULL 
									                            THEN stokobatalkes_t.qtystok_out ELSE NULL::double precision END 
									                        AS qty_resep,
									                        NULL::double precision as qty_pemakaianruangan,
									                        NULL::double precision as qty_bmhp,
									                        NULL::double precision as qty_mutasi
									                    from stokobatalkes_t
									                    join obatalkespasien_t 
									                        on stokobatalkes_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
									                        and obatalkespasien_t.penjualanresep_id is not null
									                        and obatalkespasien_t.is_deleted = false
									                    where stokobatalkes_t.tglstok_out is not null
									                    AND (
									                        stokobatalkes_t.tglstok_out >= current_date::timestamp without time zone AND stokobatalkes_t.tglstok_out <= (current_date - '30 days'::interval) 
									                        OR 
									                        stokobatalkes_t.tglstok_out >= (current_date - '30 days'::interval) AND stokobatalkes_t.tglstok_out <= current_date::timestamp without time zone
									                    )
									                    union all
									                    select
									                        stokobatalkes_t.stokobatalkes_id,
									                        stokobatalkes_t.obatalkes_id,
									                        to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date AS tanggal,
									                        stokobatalkes_t.qtystok_out,
									                        NULL::double precision as qty_resep,
									                        CASE 
									                            WHEN stokobatalkes_t.pemakaianobatdetail_id IS NOT NULL 
									                            AND stokobatalkes_t.tglstok_out IS NOT NULL 
									                            THEN stokobatalkes_t.qtystok_out ELSE NULL::double precision END 
									                        AS qty_pemakaianruangan,
									                        NULL::double precision as qty_bmhp,
									                        NULL::double precision as qty_mutasi
									                    from stokobatalkes_t
									                    join (
									                            select 
									                                pemakaianobatdetail_id,
									                                pemakaianobat_t.ruangan_id
									                            from pemakaianobatdetail_t
									                            join pemakaianobat_t 
									                                on pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
									                            where pemakaianobat_t.is_deleted = false
									                            and pemakaianobatdetail_t.is_deleted = false
									                        ) pemakaianobatdetail_t on stokobatalkes_t.pemakaianobatdetail_id = pemakaianobatdetail_t.pemakaianobatdetail_id and vpemakaianruangan
									                    where stokobatalkes_t.tglstok_out is not null
									                    AND (
									                        stokobatalkes_t.tglstok_out >= current_date::timestamp without time zone AND stokobatalkes_t.tglstok_out <= (current_date - '30 days'::interval) 
									                        OR 
									                        stokobatalkes_t.tglstok_out >= (current_date - '30 days'::interval) AND stokobatalkes_t.tglstok_out <= current_date::timestamp without time zone
									                    )
									                    union all 
									                    select 
									                        stokobatalkes_t.stokobatalkes_id,
									                        stokobatalkes_t.obatalkes_id,
									                        to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date AS tanggal,
									                        stokobatalkes_t.qtystok_out,
									                        NULL::double precision as qty_resep,
									                        NULL::double precision as qty_pemakaianruangan,
									                        CASE 
									                            WHEN stokobatalkes_t.obatalkespasien_id IS NOT NULL 
									                            AND stokobatalkes_t.tglstok_out IS NOT NULL 
									                            THEN stokobatalkes_t.qtystok_out ELSE NULL::double precision END 
									                        AS qty_bmhp,
									                        NULL::double precision as qty_mutasi
									                    from stokobatalkes_t
									                    join (
									                            select
									                                obatalkespasien_t.obatalkespasien_id,
									                                instruksitindakanbmhp_t.instruksitindakanbmhp_id,
									                                instruksitindakanbmhp_t.ruangan_id
									                            from obatalkespasien_t
									                            join instruksitindakanbmhp_t on obatalkespasien_t.instruksitindakanbmhp_id = instruksitindakanbmhp_t.instruksitindakanbmhp_id
									                            where instruksitindakanbmhp_t.is_ditagihkan = true
									                            and instruksitindakanbmhp_t.is_deleted = false
									                        ) obatalkespasien_t on stokobatalkes_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id and vbmhp
									                    where stokobatalkes_t.tglstok_out is not null
									                    AND (
									                        stokobatalkes_t.tglstok_out >= current_date::timestamp without time zone AND stokobatalkes_t.tglstok_out <= (current_date - '30 days'::interval) 
									                        OR 
									                        stokobatalkes_t.tglstok_out >= (current_date - '30 days'::interval) AND stokobatalkes_t.tglstok_out <= current_date::timestamp without time zone
									                    )
									                    union all 
									                    select 
									                        stokobatalkes_t.stokobatalkes_id,
									                        stokobatalkes_t.obatalkes_id,
									                        to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date AS tanggal,
									                        stokobatalkes_t.qtystok_out,
									                        NULL::double precision as qty_resep,
									                        NULL::double precision as qty_pemakaianruangan,
									                        NULL::double precision as qty_bmhp,
									                        CASE 
									                            WHEN stokobatalkes_t.mutasiobatdetail_id IS NOT NULL 
									                            AND stokobatalkes_t.tglstok_out IS NOT NULL 
									                            THEN stokobatalkes_t.qtystok_out ELSE NULL::double precision END 
									                        AS qty_mutasi
									                    from stokobatalkes_t
									                    join (
									                            select
									                                mutasiobatdetail_t.mutasiobatdetail_id,
									                                mutasiobatruangan_t.ruanganasal_id,
									                                mutasiobatruangan_t.ruangantujuan_id
									                            from mutasiobatdetail_t
									                            join mutasiobatruangan_t on mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
									                            where mutasiobatruangan_t.ruanganasal_id = (select kode_id from lookuptransaksi_m where kode_transaksi = 'gudang_farmasi')
									                            and mutasiobatruangan_t.ruangantujuan_id not in (select ruangan_id from ruangan_m rm where instalasi_id = (select kode_id from lookuptransaksi_m where kode_transaksi = 'FARMASI'))
									                            and mutasiobatruangan_t.is_deleted = false
									                            and mutasiobatdetail_t.is_deleted = false
									                        )mutasiobatdetail_t on stokobatalkes_t.mutasiobatdetail_id = mutasiobatdetail_t.mutasiobatdetail_id and vmutasi
									                    where stokobatalkes_t.tglstok_out is not null
									                    AND (
									                        stokobatalkes_t.tglstok_out >= current_date::timestamp without time zone AND stokobatalkes_t.tglstok_out <= (current_date - '30 days'::interval) 
									                        OR 
									                        stokobatalkes_t.tglstok_out >= (current_date - '30 days'::interval) AND stokobatalkes_t.tglstok_out <= current_date::timestamp without time zone
									                    )
									                ) detail
									                GROUP BY detail.obatalkes_id, detail.tanggal
									                ORDER BY detail.obatalkes_id, detail.tanggal
									            ) count_stok
									            LEFT JOIN obatalkes_m ON count_stok.obatalkes_id = obatalkes_m.obatalkes_id
									            where obatalkes_m.is_deleted is false and obatalkes_m.is_active is true
									            GROUP BY count_stok.obatalkes_id, obatalkes_m.jenisobatalkes_id
									        ) calc
									        left join movingcriteria_m mc on calc.count <= mc.max and calc.count >= mc.min;
									    END;
									\$BODY\$
			  LANGUAGE plpgsql VOLATILE
			  COST 100
			  ROWS 1000
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220929_054920_migrate_mhg_4127_new_basecalrofn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220929_054920_migrate_mhg_4127_new_basecalrofn cannot be reverted.\n";

        return false;
    }
    */
}
