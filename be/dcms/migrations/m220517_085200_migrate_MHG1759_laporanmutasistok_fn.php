<?php

use yii\db\Migration;

/**
 * Class m220517_085200_migrate_MHG1759_laporanmutasistok_fn
 */
class m220517_085200_migrate_MHG1759_laporanmutasistok_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('DROP FUNCTION if exists public.laporanmutasistok_fn;');
		
        $this->execute("
			CREATE OR REPLACE FUNCTION public.laporanmutasistok_fn(x_startdate date, x_finishdate date)
			  RETURNS TABLE(obatalkes_id int4, ruangan_id int4, ruangan_nama varchar, obatalkes_nama varchar, obatalkes_kode varchar, jenisobatalkes_nama varchar, manufaktur_nama varchar, uom varchar, hna float8, qty_total_awal float8, total_nilai_awal float8, qtystok_in float8, qtystok_out float8, total_qty_diterima float8, total_nilai_diterima float8, total_qty_keluar float8, total_nilai_keluar float8, total_qty_akhir float8, total_nilai_akhir float8, turn_over float8) AS \$BODY\$
                    
											  BEGIN
											      RETURN QUERY 
						select  a.* from (
						select 
						--'a' as tipe,
						obatalkes_m.obatalkes_id::int4 ,
						ruangan_m.ruangan_id::int4,
						ruangan_m.ruangan_nama,
						obatalkes_m.obatalkes_nama,
						obatalkes_m.obatalkes_kode,
						jenisobatalkes_m.jenisobatalkes_nama,
						manufaktur_m.nama AS manufaktur_nama,
						satuan_kecil.satuanunit_nama AS uom,
						obatalkes_m.harganetto AS hna,
						stok_awal.qty_total_awal,
						COALESCE(stok_awal.qty_total_awal,0) * obatalkes_m.harganetto AS total_nilai_awal,
						COALESCE(SUM(stokobatalkes_t.qtystok_in ),0) as qtystok_in,
						COALESCE(SUM(stokobatalkes_t.qtystok_out),0) as qtystok_out,
						COALESCE(stok_awal.qty_total_awal,0) + COALESCE(SUM( stokobatalkes_t.qtystok_in),0) as total_qty_diterima,
						COALESCE(stok_awal.qty_total_awal,0) + COALESCE(SUM( stokobatalkes_t.qtystok_in),0) * obatalkes_m.harganetto as total_nilai_diterima,
						COALESCE(SUM(stokobatalkes_t.qtystok_out),0) as total_qty_keluar,
						COALESCE(SUM(stokobatalkes_t.qtystok_out),0) * obatalkes_m.harganetto as total_nilai_keluar,
						COALESCE(stok_awal.qty_total_awal,0) + COALESCE(SUM( stokobatalkes_t.qtystok_in),0) - COALESCE(SUM(stokobatalkes_t.qtystok_out),0) as total_qty_akhir,
						(COALESCE(stok_awal.qty_total_awal,0) + COALESCE(SUM( stokobatalkes_t.qtystok_in),0) - COALESCE(SUM(stokobatalkes_t.qtystok_out),0)) * obatalkes_m.harganetto as total_nilai_akhir,
						(COALESCE(stok_awal.qty_total_awal,0) + COALESCE(SUM( stokobatalkes_t.qtystok_in),0) - COALESCE(SUM(stokobatalkes_t.qtystok_out),0)) - COALESCE(stok_awal.qty_total_awal,0) as turn_over
						from obatalkes_m
						LEFT JOIN ( SELECT a.obatalkes_id, a.ruangan_id, a.qtystok_in, a.qtystok_out, a.tglstok_in, a.tglstok_out, a.is_deleted FROM stokobatalkes_t a where a.is_deleted = false ) stokobatalkes_t ON  obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id 
						LEFT JOIN ( SELECT j.jenisobatalkes_id, j.jenisobatalkes_kode, j.jenisobatalkes_nama FROM jenisobatalkes_m j) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
						LEFT JOIN ( SELECT manufaktur_id, kode, nama FROM manufaktur_m ) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id 
						LEFT	JOIN ( SELECT r.ruangan_id, r.ruangan_nama, r.instalasi_id FROM ruangan_m r) ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
						 left JOIN ( SELECT b.obatalkes_id, b.ruangan_id, 	SUM ( b.qtystok_in ) - SUM ( b.qtystok_out ) AS qty_total_awal FROM stokobatalkes_t b where b.is_deleted = false 
						 and (b.tglstok_in < x_startdate::date or b.tglstok_out < x_startdate::date) GROUP BY b.obatalkes_id, b.ruangan_id) stok_awal on stok_awal.obatalkes_id = obatalkes_m.obatalkes_id and stok_awal.ruangan_id = ruangan_m.ruangan_id
						 LEFT JOIN ( SELECT satuanunit_id, satuanunit_nama FROM satuanunit_m ) satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id 
						 	WHERE obatalkes_m.is_deleted = false and (stokobatalkes_t.tglstok_in::date BETWEEN x_startdate::date  and x_finishdate::date or stokobatalkes_t.tglstok_out::date BETWEEN x_startdate::date and x_finishdate::date ) 
								--and obatalkes_m.obatalkes_id in(3909, 3306) and ruangan_m.ruangan_id=6
						GROUP BY
						obatalkes_m.obatalkes_id,
						ruangan_m.ruangan_id,
						ruangan_m.ruangan_nama,
						obatalkes_m.obatalkes_nama,
						obatalkes_m.obatalkes_kode,
						jenisobatalkes_m.jenisobatalkes_nama,
						manufaktur_m.nama ,
						satuan_kecil.satuanunit_nama,
						stok_awal.qty_total_awal,
						obatalkes_m.harganetto
						-- ORDER BY obatalkes_m.obatalkes_id,
						-- ruangan_m.ruangan_id asc
						UNION ALL
						select
						--'b' as tipe,
						obatalkes_m.obatalkes_id::int4 ,
						ruangan_m.ruangan_id::int4,
						ruangan_m.ruangan_nama,
						obatalkes_m.obatalkes_nama,
						obatalkes_m.obatalkes_kode,
						jenisobatalkes_m.jenisobatalkes_nama,
						manufaktur_m.nama AS manufaktur_nama,
						satuan_kecil.satuanunit_nama AS uom,
						obatalkes_m.harganetto AS hna,
						stok_awal.qty_total_awal,
						COALESCE(stok_awal.qty_total_awal,0) * obatalkes_m.harganetto AS total_nilai_awal,
						0 as qtystok_in,
						0 as qtystok_out,
						COALESCE(stok_awal.qty_total_awal,0) + 0 as total_qty_diterima,
						COALESCE(stok_awal.qty_total_awal,0) + 0 * obatalkes_m.harganetto as total_nilai_diterima,
						0 as total_qty_keluar,
						0 * obatalkes_m.harganetto as total_nilai_keluar,
						COALESCE(stok_awal.qty_total_awal,0) + 0 - 0 as total_qty_akhir,
						(COALESCE(stok_awal.qty_total_awal,0) + 0 - 0) * obatalkes_m.harganetto as total_nilai_akhir,
						(COALESCE(stok_awal.qty_total_awal,0) + 0 - 0) - COALESCE(stok_awal.qty_total_awal,0) as turn_over
						from obatalkes_m
						LEFT JOIN ( SELECT a.stokobatalkes_id,a.obatalkes_id, a.ruangan_id, a.qtystok_in, a.qtystok_out, a.tglstok_in, a.tglstok_out, a.is_deleted FROM stokobatalkes_t a where a.is_deleted = false ) stokobatalkes_t ON  obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id 
						LEFT JOIN ( SELECT j.jenisobatalkes_id, j.jenisobatalkes_kode, j.jenisobatalkes_nama FROM jenisobatalkes_m j) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
						LEFT JOIN ( SELECT manufaktur_id, kode, nama FROM manufaktur_m ) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id 
						LEFT	JOIN ( SELECT r.ruangan_id, r.ruangan_nama, r.instalasi_id FROM ruangan_m r) ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
						 left JOIN ( SELECT b.obatalkes_id, b.ruangan_id, 	SUM ( b.qtystok_in ) - SUM ( b.qtystok_out ) AS qty_total_awal FROM stokobatalkes_t b where b.is_deleted = false 
						 and (b.tglstok_in < x_startdate::date or b.tglstok_out < x_startdate::date) GROUP BY b.obatalkes_id, b.ruangan_id) stok_awal on stok_awal.obatalkes_id = obatalkes_m.obatalkes_id and stok_awal.ruangan_id = ruangan_m.ruangan_id
						 LEFT JOIN ( SELECT satuanunit_id, satuanunit_nama FROM satuanunit_m ) satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id 
						  	WHERE obatalkes_m.is_deleted = false 
						and stokobatalkes_t.obatalkes_id  not in (
						select DISTINCT
						stokobatalkes_t.obatalkes_id
						from obatalkes_m
						LEFT JOIN ( SELECT a.stokobatalkes_id, a.obatalkes_id, a.ruangan_id, a.qtystok_in, a.qtystok_out, a.tglstok_in, a.tglstok_out, a.is_deleted FROM stokobatalkes_t a where a.is_deleted = false ) stokobatalkes_t ON  obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id 
						LEFT	JOIN ( SELECT r.ruangan_id, r.ruangan_nama, r.instalasi_id FROM ruangan_m r) ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
						 left JOIN ( SELECT b.obatalkes_id, b.ruangan_id, 	SUM ( b.qtystok_in ) - SUM ( b.qtystok_out ) AS qty_total_awal FROM stokobatalkes_t b where b.is_deleted = false 
						 and (b.tglstok_in < x_startdate::date or b.tglstok_out < x_startdate::date) GROUP BY b.obatalkes_id, b.ruangan_id) stok_awal on stok_awal.obatalkes_id = obatalkes_m.obatalkes_id and stok_awal.ruangan_id = ruangan_m.ruangan_id
						 LEFT JOIN ( SELECT satuanunit_id, satuanunit_nama FROM satuanunit_m ) satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id 
						 	WHERE obatalkes_m.is_deleted = false and (stokobatalkes_t.tglstok_in::date BETWEEN x_startdate::date  and x_finishdate::date or stokobatalkes_t.tglstok_out::date BETWEEN x_startdate::date and x_finishdate::date ) 
							) AND stokobatalkes_t.ruangan_id not in (
							select DISTINCT
						stokobatalkes_t.ruangan_id
						from obatalkes_m
						LEFT JOIN ( SELECT a.stokobatalkes_id, a.obatalkes_id, a.ruangan_id, a.qtystok_in, a.qtystok_out, a.tglstok_in, a.tglstok_out, a.is_deleted FROM stokobatalkes_t a where a.is_deleted = false ) stokobatalkes_t ON  obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id 
						LEFT	JOIN ( SELECT r.ruangan_id, r.ruangan_nama, r.instalasi_id FROM ruangan_m r) ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
						 left JOIN ( SELECT b.obatalkes_id, b.ruangan_id, 	SUM ( b.qtystok_in ) - SUM ( b.qtystok_out ) AS qty_total_awal FROM stokobatalkes_t b where b.is_deleted = false 
						 and (b.tglstok_in < x_startdate::date or b.tglstok_out < x_startdate::date) GROUP BY b.obatalkes_id, b.ruangan_id) stok_awal on stok_awal.obatalkes_id = obatalkes_m.obatalkes_id and stok_awal.ruangan_id = ruangan_m.ruangan_id
						 LEFT JOIN ( SELECT satuanunit_id, satuanunit_nama FROM satuanunit_m ) satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id 
						 	WHERE obatalkes_m.is_deleted = false and (stokobatalkes_t.tglstok_in::date BETWEEN x_startdate::date  and x_finishdate::date or stokobatalkes_t.tglstok_out::date BETWEEN x_startdate::date and x_finishdate::date ) 
								--and obatalkes_m.obatalkes_id in(3909, 3306) and ruangan_m.ruangan_id=6
							) 
						GROUP BY
						obatalkes_m.obatalkes_id,
						ruangan_m.ruangan_id,
						ruangan_m.ruangan_nama,
						obatalkes_m.obatalkes_nama,
						obatalkes_m.obatalkes_kode,
						jenisobatalkes_m.jenisobatalkes_nama,
						manufaktur_m.nama ,
						satuan_kecil.satuanunit_nama,
						stok_awal.qty_total_awal,
						obatalkes_m.harganetto
						)a;
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
        echo "m220517_085200_migrate_MHG1759_laporanmutasistok_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220517_085200_migrate_MHG1759_laporanmutasistok_fn cannot be reverted.\n";

        return false;
    }
    */
}
