<?php

use yii\db\Migration;

/**
 * Class m220420_180323_migrate_sobarang_kartustokbarang_fn
 */
class m220420_180323_migrate_sobarang_kartustokbarang_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists public.kartustokbarang_fn;');

        $this->execute("
			CREATE OR REPLACE FUNCTION public.kartustokbarang_fn(v_date_start date, v_date_end date, v_ruangan int4, v_barang int4=0)
			  RETURNS TABLE(stokobarang_id int4, tanggal_transaksi timestamp, ruangan_id int4, ruangan_asal_id int4, ruangan_tujuan_id int4, ruangan_asal_nama varchar, ruangan_tujuan_nama varchar, barang_id int4, barang_kode varchar, tglkadaluarsa date, no_transaksi varchar, barang_nama varchar, satuanunit_nama varchar, keterangan text, reference varchar, qtystok_in float8, qtystok_out float8, row_number int8, total float8) 
			 AS  \$BODY\$
			  
			begin
			return QUERY
			select * from (
			SELECT kartu_stok.stokbarang_id,
			kartu_stok.tanggal_transaksi,
			CASE
			    WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN kartu_stok.ruangan_tujuan_id
			    WHEN kartu_stok.keterangan = 'Mutasi Barang'::text THEN kartu_stok.ruangan_asal_id
			    ELSE kartu_stok.ruangan_asal_id
			end as ruangan_id,
			kartu_stok.ruangan_asal_id,
			kartu_stok.ruangan_tujuan_id,
			ruangan_asal.ruangan_nama AS ruangan_asal_nama,
			ruangan_tujuan.ruangan_nama AS ruangan_tujuan_nama,
			kartu_stok.barang_id, 
			barang_m.barang_kode,
			kartu_stok.tglkadaluarsa,
			kartu_stok.no_transaksi,
			barang_m.barang_nama,
			satuanunit_m.satuanunit_nama,
			kartu_stok.keterangan,
			CASE
			    WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN ruangan_asal.ruangan_nama
				WHEN kartu_stok.keterangan = 'Mutasi Barang'::text THEN ruangan_tujuan.ruangan_nama
			    ELSE kartu_stok.reference
			END AS reference,
			kartu_stok.qtystok_in, 
			kartu_stok.qtystok_out,
			       ROW_NUMBER() OVER(PARTITION BY kartu_stok.barang_id ,(CASE
			            WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN kartu_stok.ruangan_tujuan_id
			            WHEN kartu_stok.keterangan = 'Mutasi Barang'::text THEN kartu_stok.ruangan_asal_id
			            ELSE kartu_stok.ruangan_asal_id
			        end)
			       ORDER BY kartu_stok.tanggal_transaksi ROWS UNBOUNDED PRECEDING) AS row_number, 
			       sum(kartu_stok.qtystok_in-kartu_stok.qtystok_out) OVER(PARTITION BY kartu_stok.barang_id ,(CASE
			            WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN kartu_stok.ruangan_tujuan_id
			            WHEN kartu_stok.keterangan = 'Mutasi Barang'::text THEN kartu_stok.ruangan_asal_id
			            ELSE kartu_stok.ruangan_asal_id
			        end)
			       ORDER BY kartu_stok.tanggal_transaksi ROWS UNBOUNDED PRECEDING) AS total
			from (

			 --------------------------- START ADJUSTMEN MASUK BARANG ------------------------------------
 
			 SELECT  max(stokbarang_t.stokbarang_id) AS stokbarang_id,
			            'Adjusmen Masuk'::text AS keterangan,
			            adjusmen_masuk.no_adjusmen AS no_transaksi,
			            stokbarang_t.tglstok_in AS tanggal_transaksi,
			            stokbarang_t.barang_id,
			            stokbarang_t.satuankecil_id AS satuanunit_id,
			            stokbarang_t.tglkadaluarsa,
			             sum(stokbarang_t.qtystok_in) AS qtystok_in,
			             sum(stokbarang_t.qtystok_out) AS qtystok_out,
			             adjusmen_masuk.keterangan::character varying AS reference,
			             stokbarang_t.ruangan_id,
			             stokbarang_t.ruangan_id AS ruangan_asal_id,
			             stokbarang_t.ruangan_id AS ruangan_tujuan_id
			           FROM stokbarang_t
			             JOIN ( 
						 
									 SELECT adjusmenbarangmasuk_t.adjusmenbarangmasuk_id,
			                    adjusmenbarang_t.no_adjusmen,
			                    adjusmenbarang_t.tgl_adjusmen,
			                    adjusmenbarangmasuk_t.keterangan
			                   FROM adjusmenbarangmasuk_t
			                     JOIN adjusmenbarang_t ON adjusmenbarangmasuk_t.adjusmenbarang_id = adjusmenbarang_t.adjusmenbarang_id
										 
													 ) adjusmen_masuk ON stokbarang_t.adjusmenbarangmasuk_id = adjusmen_masuk.adjusmenbarangmasuk_id
			          WHERE stokbarang_t.is_deleted = false
			           GROUP BY stokbarang_t.stokbarang_id, adjusmen_masuk.no_adjusmen, stokbarang_t.tglstok_in, stokbarang_t.barang_id, stokbarang_t.satuankecil_id, stokbarang_t.tglkadaluarsa, stokbarang_t.ruangan_id,adjusmen_masuk.keterangan
			  --------------------------- END ADJUSTMEN MASUK BARANG ------------------------------------
				UNION ALL
				---------------------------- START ADJUSTMEN KELUAR BARANG ---------------------------------
				SELECT  max(stokbarang_t.stokbarang_id) AS stokbarang_id,
			            'Adjusmen keluar'::text AS keterangan,
			            adjusmen_keluar.no_adjusmen AS no_transaksi,
			            stokbarang_t.tglstok_out AS tanggal_transaksi,
			            stokbarang_t.barang_id,
			            stokbarang_t.satuankecil_id AS satuanunit_id,
			            stokbarang_t.tglkadaluarsa,
			             sum(stokbarang_t.qtystok_in) AS qtystok_in,
			             sum(stokbarang_t.qtystok_out) AS qtystok_out,
			             adjusmen_keluar.keterangan::character varying AS reference,
			             stokbarang_t.ruangan_id,
			             stokbarang_t.ruangan_id AS ruangan_asal_id,
			             stokbarang_t.ruangan_id AS ruangan_tujuan_id
			           FROM stokbarang_t
			             JOIN ( 
						 
									 SELECT adjusmenbarangkeluar_t.adjusmenbarangkeluar_id,
			                    adjusmenbarang_t.no_adjusmen,
			                    adjusmenbarang_t.tgl_adjusmen,
			                    adjusmenbarangkeluar_t.keterangan
			                   FROM adjusmenbarangkeluar_t
			                     JOIN adjusmenbarang_t ON adjusmenbarangkeluar_t.adjusmenbarang_id = adjusmenbarang_t.adjusmenbarang_id
										 
													 ) adjusmen_keluar ON stokbarang_t.adjusmenbarangkeluar_id = adjusmen_keluar.adjusmenbarangkeluar_id
			          WHERE stokbarang_t.is_deleted = false
			           GROUP BY stokbarang_t.stokbarang_id, adjusmen_keluar.no_adjusmen, stokbarang_t.tglstok_in, stokbarang_t.barang_id, stokbarang_t.satuankecil_id, stokbarang_t.tglkadaluarsa, stokbarang_t.ruangan_id,adjusmen_keluar.keterangan
			  ---------------------------- END ADJUSTMEN KELUAR BARANG ---------------------------------
					UNION ALL
				---------------------------= START PENERIMAAN ALTERNATIF ---------------------------------
				SELECT max(stokbarang_t.stokbarang_id) AS stokbarang_id,
			            'Penerimaan Alternatif'::text AS keterangan,
			            penerimaan_alternatif.no_penerimaan AS no_transaksi,
			            stokbarang_t.tglstok_in AS tanggal_transaksi,
			            stokbarang_t.barang_id,
			            stokbarang_t.satuankecil_id AS satuanunit_id,
			            stokbarang_t.tglkadaluarsa,
			            sum(stokbarang_t.qtystok_in) AS qtystok_in,
			            sum(stokbarang_t.qtystok_out) AS qtystok_out,
			            '-'::character varying AS reference,
			            stokbarang_t.ruangan_id,
			            stokbarang_t.ruangan_id AS ruangan_asal_id,
			            stokbarang_t.ruangan_id AS ruangan_tujuan_id
			           FROM stokbarang_t
			             JOIN ( SELECT penerimaansuppdetail_t.penerimaansuppdetail_id,
			                    penerimaansupp_t.no_penerimaan,
			                    penerimaansupp_t.tgl_penerimaan
			                   FROM penerimaansupp_t
			                     JOIN penerimaansuppdetail_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id) penerimaan_alternatif ON stokbarang_t.penerimaansuppdetail_id = penerimaan_alternatif.penerimaansuppdetail_id
			          WHERE stokbarang_t.is_deleted = false
			          GROUP BY 'Penerimaan Alternatif'::text, penerimaan_alternatif.no_penerimaan, stokbarang_t.tglstok_in, stokbarang_t.barang_id, stokbarang_t.satuankecil_id, stokbarang_t.tglkadaluarsa, '-'::character varying, stokbarang_t.ruangan_id
      
			-------------------------------------------END PENERIMAAN ALTERNATIF
							UNION ALL
			------------------------------------------ Start PEMAKAIAN ruangan
						SELECT stokbarang_t.stokbarang_id,
			            'Pemakaian Ruangan'::text AS keterangan,
			            pemakaian_ruangan.no_pemakaianbarang AS no_transaksi,
			            stokbarang_t.tglstok_out AS tanggal_transaksi,
			            stokbarang_t.barang_id,
			            stokbarang_t.satuankecil_id AS satuanunit_id,
			            stokbarang_t.tglkadaluarsa,
			            stokbarang_t.qtystok_in,
			            stokbarang_t.qtystok_out,
			            '-'::character varying AS reference,
			            stokbarang_t.ruangan_id,
			            stokbarang_t.ruangan_id AS ruangan_asal_id,
			            stokbarang_t.ruangan_id AS ruangan_tujuan_id
			           FROM stokbarang_t
			             JOIN ( SELECT pemakaianbarangdetail_t.pemakaianbarangdetail_id,
			                    pemakaianbarang_t.no_pemakaianbarang,
			                    pemakaianbarang_t.tgl_pemakaianbarang
			                   FROM pemakaianbarang_t
			                     JOIN pemakaianbarangdetail_t ON pemakaianbarangdetail_t.pemakaianbarang_id = pemakaianbarang_t.pemakaianbarang_id) pemakaian_ruangan ON stokbarang_t.pemakaianbarangdetail_id = pemakaian_ruangan.pemakaianbarangdetail_id
			          WHERE stokbarang_t.is_deleted = false
			------------------------------- END PEMAKAIAN RUANGAN
									UNION ALL
			------------------------------- START PEMUSNAHAN BARANG ------------------------------------
								SELECT stokbarang_t.stokbarang_id,
			            'Pemusnahan barang'::text AS keterangan,
			            pemusnahan_barang.nopemusnahan AS no_transaksi,
			            stokbarang_t.tglstok_out AS tanggal_transaksi,
			            stokbarang_t.barang_id,
			            stokbarang_t.satuankecil_id AS satuanunit_id,
			            stokbarang_t.tglkadaluarsa,
			            stokbarang_t.qtystok_in,
			            stokbarang_t.qtystok_out,
			            '-'::character varying AS reference,
			            stokbarang_t.ruangan_id,
			            stokbarang_t.ruangan_id AS ruangan_asal_id,
			            stokbarang_t.ruangan_id AS ruangan_tujuan_id
			           FROM stokbarang_t
			             JOIN ( SELECT pemusnahanbarangdetail_t.pemusnahanbarangdetail_id,
			                    pemusnahanbarang_t.nopemusnahan,
			                    pemusnahanbarang_t.tglpemusnahan
			                   FROM pemusnahanbarang_t
			                     JOIN pemusnahanbarangdetail_t ON pemusnahanbarang_t.pemusnahanbarang_id = pemusnahanbarangdetail_t.pemusnahanbarang_id) pemusnahan_barang ON stokbarang_t.pemusnahanbarangdetail_id = pemusnahan_barang.pemusnahanbarangdetail_id
			          WHERE stokbarang_t.is_deleted = false
			--------------------------------------------- END PEMUSNAHAN BARANG ----------------------------------------
					UNION ALL
			--------------------------------------------- START STOKOPNAME ----------------------------------------
				  SELECT max(stokbarang_t.stokbarang_id),
			            'Stok Opname'::text AS keterangan,
			            stok_opname.nostokopname AS no_transaksi,
			            ( case when tglstok_in is null then tglstok_out else tglstok_in end) AS tanggal_transaksi,
			            stokbarang_t.barang_id,
			            stokbarang_t.satuankecil_id AS satuanunit_id,
			            stokbarang_t.tglkadaluarsa,
			            sum(stokbarang_t.qtystok_in),
			            sum(stokbarang_t.qtystok_out),
			            '-'::character varying AS reference,
			            stokbarang_t.ruangan_id,
			            stokbarang_t.ruangan_id AS ruangan_asal_id,
			            stokbarang_t.ruangan_id AS ruangan_tujuan_id
			           FROM stokbarang_t
			             JOIN (
						
										SELECT stokopnamebarangdetail_t.stokopnamebarangdetail_id,
			                    stokopnamebarang_t.nostokopname,
			                    stokopnamebarang_t.tglstokopname
			                   FROM stokopnamebarang_t
			                     JOIN stokopnamebarangdetail_t ON stokopnamebarangdetail_t.stokopnamebarang_id = stokopnamebarang_t.stokopnamebarang_id
													 )
										
														stok_opname ON stokbarang_t.stokopnamebarangdetail_id = stok_opname.stokopnamebarangdetail_id
			          WHERE stokbarang_t.is_deleted = false
			          group by stok_opname.stokopnamebarangdetail_id,
			 stok_opname.nostokopname,
			 ( case when tglstok_in is null then tglstok_out else tglstok_in end),
			 stokbarang_t.barang_id,
			 stokbarang_t.stokbarang_id,
			stokbarang_t.satuankecil_id,
			stokbarang_t.tglkadaluarsa,
			stokbarang_t.ruangan_id

			--------------------------------------------- END STOKOPNAME ----------------------------------------
					UNION ALL
			----------------------- START PENERIMAAN ------------
			 SELECT stokbarang_t.stokbarang_id,
			            'Penerimaan Supplier'::text AS keterangan,
			            penerimaan_supp.no_penerimaan AS no_transaksi,
			            stokbarang_t.tglstok_in AS tanggal_transaksi,
			            stokbarang_t.barang_id,
			            stokbarang_t.satuankecil_id AS satuanunit_id,
			            stokbarang_t.tglkadaluarsa,
			            stokbarang_t.qtystok_in,
			            stokbarang_t.qtystok_out,
			            '-'::character varying AS reference,
			            stokbarang_t.ruangan_id,
			            stokbarang_t.ruangan_id AS ruangan_asal_id,
			            stokbarang_t.ruangan_id AS ruangan_tujuan_id
			           FROM stokbarang_t
			             JOIN ( SELECT penerimaanbarangdetail_t.penerimaanbarangdetail_id,
			                    penerimaanbarang_t.no_penerimaan,
			                    penerimaanbarang_t.tgl_penerimaan
			                   FROM penerimaanbarang_t
			                     JOIN penerimaanbarangdetail_t ON penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id) penerimaan_supp ON stokbarang_t.penerimaandetail_id = penerimaan_supp.penerimaanbarangdetail_id
			          WHERE stokbarang_t.is_deleted = false
			------------------- END penerimaanobat ----------------
									UNION ALL
			----------------- START RETUR  -----------------------------------
								SELECT stokbarang_t.stokbarang_id,
			            'Retur Penerimaan Supplier'::text AS keterangan,
			            retur_penerimaan.no_returpenerimaanbarang AS no_transaksi,
			            stokbarang_t.tglstok_out AS tanggal_transaksi,
			            stokbarang_t.barang_id,
			            stokbarang_t.satuankecil_id AS satuanunit_id,
			            stokbarang_t.tglkadaluarsa,
			            stokbarang_t.qtystok_in,
			            stokbarang_t.qtystok_out,
			            '-'::character varying AS reference,
			            stokbarang_t.ruangan_id,
			            stokbarang_t.ruangan_id AS ruangan_asal_id,
			            stokbarang_t.ruangan_id AS ruangan_tujuan_id
			           FROM stokbarang_t
			             JOIN ( SELECT returpenerimaanbarangdetail_t.returpenerimaanbarangdetail_id,
			                    returpenerimaanbarang_t.no_returpenerimaanbarang,
			                    returpenerimaanbarang_t.tgl_retur
			                   FROM returpenerimaanbarang_t
			                     JOIN returpenerimaanbarangdetail_t ON returpenerimaanbarangdetail_t.returpenerimaanbarang_id = returpenerimaanbarang_t.returpenerimaanbarang_id) retur_penerimaan ON stokbarang_t.returbarangdetail_id = retur_penerimaan.returpenerimaanbarangdetail_id
			          WHERE stokbarang_t.is_deleted = false
					
			---------------------- END RETUR ------------------------------
									UNION ALL
			---------------------- STAR mutasi ----------------------
								         SELECT stokbarang_t.stokbarang_id,
			            'Mutasi barang'::text AS keterangan,
			            mutasi_barang.nomutasi_barang AS no_transaksi,
			            stokbarang_t.tglstok_out AS tanggal_transaksi,
			            stokbarang_t.barang_id,
			            stokbarang_t.satuankecil_id AS satuanunit_id,
			            stokbarang_t.tglkadaluarsa,
			            stokbarang_t.qtystok_in,
			            stokbarang_t.qtystok_out,
			            '-'::character varying AS reference,
			            mutasi_barang.ruanganasal_id as ruangan_id,
			            mutasi_barang.ruanganasal_id AS ruangan_asal_id,
			            mutasi_barang.ruangantujuan_id AS ruangan_tujuan_id
			           FROM stokbarang_t
			             JOIN ( SELECT mutasibarangdetail_t.mutasibarangdetail_id,
			                    mutasibarang_t.nomutasi_barang,
			                    mutasibarang_t.tgl_mutasibarang,
			                    mutasibarang_t.ruanganasal_id,
			                    mutasibarang_t.ruangantujuan_id
			                   FROM mutasibarang_t
			                     JOIN mutasibarangdetail_t ON mutasibarangdetail_t.mutasibarang_id = mutasibarang_t.mutasibarang_id) mutasi_barang ON stokbarang_t.mutasibarangdetail_id = mutasi_barang.mutasibarangdetail_id
			          WHERE stokbarang_t.is_deleted = false
					
			--------------------------------	END MUTASI	-----------------------------------------
					
			)  kartu_stok


			JOIN barang_m ON kartu_stok.barang_id = barang_m.barang_id
			LEFT JOIN satuanunit_m ON kartu_stok.satuanunit_id = satuanunit_m.satuanunit_id
			LEFT JOIN ruangan_m ruangan_asal ON kartu_stok.ruangan_asal_id = ruangan_asal.ruangan_id
			LEFT JOIN ruangan_m ruangan_tujuan ON kartu_stok.ruangan_tujuan_id = ruangan_tujuan.ruangan_id
			--where kartu_stok.tanggal_transaksi <= v_date::date + interval '23 hours 59 minutes'
			-- and (CASE
			--             WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN kartu_stok.ruangan_tujuan_id
			--             WHEN kartu_stok.keterangan = 'Mutasi Barang'::text THEN kartu_stok.ruangan_asal_id
			--             ELSE kartu_stok.ruangan_asal_id
			--         end) = v_ruangan
			order by (CASE
			            WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN kartu_stok.ruangan_tujuan_id
			            WHEN kartu_stok.keterangan = 'Mutasi Barang'::text THEN kartu_stok.ruangan_asal_id
			            ELSE kartu_stok.ruangan_asal_id
			        end),kartu_stok.barang_id ,kartu_stok.tanggal_transaksi
							)ks 
							where ks.tanggal_transaksi >= v_date_start::date 
			and ks.tanggal_transaksi <= v_date_end::date + interval '23 hours 59 minutes 59 seconds'
			and ks.ruangan_id = v_ruangan and ks.barang_id = v_barang;
 
				
			 END
			 \$BODY\$
			  LANGUAGE plpgsql VOLATILE
			  COST 100
			  ROWS 1000;

           ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220420_180323_migrate_sobarang_kartustokbarang_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220420_180323_migrate_sobarang_kartustokbarang_fn cannot be reverted.\n";

        return false;
    }
    */
}
