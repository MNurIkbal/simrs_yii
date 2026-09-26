<?php

use yii\db\Migration;

/**
 * Class m230321_051519_migrate_glbj_92_kartustokobat_fn
 */
class m230321_051519_migrate_glbj_92_kartustokobat_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('DROP FUNCTION if exists public.kartustokobat_fn;');
		
        $this->execute("
			CREATE OR REPLACE FUNCTION public.kartustokobat_fn(v_date_start date, v_date_end date, v_ruangan int4, v_obat int4=0)
			  RETURNS TABLE(stokobatalkes_id int4, tanggal_transaksi timestamp, ruangan_id int4, ruangan_asal_id int4, ruangan_tujuan_id int4, ruangan_asal_nama varchar, ruangan_tujuan_nama varchar, obatalkes_id int4, obatalkes_kode varchar, tglkadaluarsa date, no_transaksi varchar, obatalkes_nama varchar, satuanunit_nama varchar, keterangan text, reference varchar, qtystok_in numeric, qtystok_out numeric, row_number int8, total numeric) AS \$BODY\$
									begin
									return QUERY

									SELECT
											* 
										FROM
											(
											SELECT
												kartu_stok.stokobatalkes_id,
												kartu_stok.tanggal_transaksi,
											CASE
				
													WHEN kartu_stok.keterangan = 'Kirim CSSD' :: TEXT THEN
													kartu_stok.ruangan_asal_id 
													WHEN kartu_stok.keterangan = 'Terima CSSD' :: TEXT THEN
													kartu_stok.ruangan_tujuan_id 
													WHEN kartu_stok.keterangan = 'Penerimaan Unit CSSD' :: TEXT THEN
													kartu_stok.ruangan_tujuan_id 
													WHEN kartu_stok.keterangan = 'Kirim Unit CSSD' :: TEXT THEN
													kartu_stok.ruangan_asal_id 
													WHEN kartu_stok.keterangan = 'Penerimaan Mutasi' :: TEXT THEN
													kartu_stok.ruangan_tujuan_id 
													WHEN kartu_stok.keterangan = 'Mutasi Obat' :: TEXT THEN
													kartu_stok.ruangan_asal_id ELSE kartu_stok.ruangan_asal_id 
												END AS ruangan_id,
												kartu_stok.ruangan_asal_id,
												kartu_stok.ruangan_tujuan_id,
												ruangan_asal.ruangan_nama AS ruangan_asal_nama,
												ruangan_tujuan.ruangan_nama AS ruangan_tujuan_nama,
												kartu_stok.obatalkes_id AS obat_id,
												obatalkes_m.obatalkes_kode,
												kartu_stok.tglkadaluarsa,
												kartu_stok.no_transaksi,
												obatalkes_m.obatalkes_nama,
												satuanunit_m.satuanunit_nama,
												kartu_stok.keterangan,
											CASE
				
													WHEN kartu_stok.keterangan = 'Kirim CSSD' :: TEXT THEN
													ruangan_tujuan.ruangan_nama 
													WHEN kartu_stok.keterangan = 'Terima CSSD' :: TEXT THEN
													ruangan_asal.ruangan_nama 
													WHEN kartu_stok.keterangan = 'Penerimaan Unit CSSD' :: TEXT THEN
													ruangan_tujuan.ruangan_nama 
													WHEN kartu_stok.keterangan = 'Kirim Unit CSSD' :: TEXT THEN
													ruangan_tujuan.ruangan_nama 
													WHEN kartu_stok.keterangan = 'Penerimaan Mutasi' :: TEXT THEN
													ruangan_asal.ruangan_nama 
													WHEN kartu_stok.keterangan = 'Mutasi Obat' :: TEXT THEN
													ruangan_tujuan.ruangan_nama ELSE kartu_stok.reference 
												END AS reference,
												round(kartu_stok.qtystok_in::numeric,2) as qtystok_in,
												round(kartu_stok.qtystok_out::numeric,2) as qtystok_out,
												--kartu_stok.qtystok_out,
												ROW_NUMBER ( ) OVER (
													PARTITION BY kartu_stok.obatalkes_id,
													(
													CASE
						
															WHEN kartu_stok.keterangan = 'Kirim CSSD' :: TEXT THEN
															kartu_stok.ruangan_asal_id 
															WHEN kartu_stok.keterangan = 'Terima CSSD' :: TEXT THEN
															kartu_stok.ruangan_tujuan_id 
															WHEN kartu_stok.keterangan = 'Penerimaan Unit CSSD' :: TEXT THEN
															kartu_stok.ruangan_tujuan_id 
															WHEN kartu_stok.keterangan = 'Kirim Unit CSSD' :: TEXT THEN
															kartu_stok.ruangan_asal_id 
															WHEN kartu_stok.keterangan = 'Penerimaan Mutasi' :: TEXT THEN
															kartu_stok.ruangan_tujuan_id 
															WHEN kartu_stok.keterangan = 'Mutasi Obat' :: TEXT THEN
															kartu_stok.ruangan_asal_id ELSE kartu_stok.ruangan_asal_id 
														END 
														) 
													ORDER BY
														kartu_stok.tanggal_transaksi ROWS UNBOUNDED PRECEDING 
													) AS ROW_NUMBER,
													---
													-- SUM ( kartu_stok.qtystok_in - kartu_stok.qtystok_out ) yang lama
													sum(round(kartu_stok.qtystok_in::numeric,3) - round(kartu_stok.qtystok_out::numeric,3)) 
													OVER (
														PARTITION BY kartu_stok.obatalkes_id,
														(
														CASE
							
																WHEN kartu_stok.keterangan = 'Kirim CSSD' :: TEXT THEN
																kartu_stok.ruangan_asal_id 
																WHEN kartu_stok.keterangan = 'Terima CSSD' :: TEXT THEN
																kartu_stok.ruangan_tujuan_id 
																WHEN kartu_stok.keterangan = 'Penerimaan Unit CSSD' :: TEXT THEN
																kartu_stok.ruangan_tujuan_id 
																WHEN kartu_stok.keterangan = 'Kirim Unit CSSD' :: TEXT THEN
																kartu_stok.ruangan_asal_id 
																WHEN kartu_stok.keterangan = 'Penerimaan Mutasi' :: TEXT THEN
																kartu_stok.ruangan_tujuan_id 
																WHEN kartu_stok.keterangan = 'Mutasi Obat' :: TEXT THEN
																kartu_stok.ruangan_asal_id ELSE kartu_stok.ruangan_asal_id 
															END 
															) 
														ORDER BY
															kartu_stok.tanggal_transaksi ROWS UNBOUNDED PRECEDING 
														) AS total 
					
														---
													FROM
														(
														SELECT MAX
															( stokobatalkes_t.stokobatalkes_id ) AS stokobatalkes_id,
														CASE
							
																WHEN penjualan_resep.penjualanresep_id IS NULL AND stokobatalkes_t.tglstok_in IS NOT NULL THEN	'BATAL BMHP' :: TEXT 
																		WHEN penjualan_resep.penjualanresep_id IS NULL THEN	'BMHP' :: TEXT 
																		WHEN penjualan_resep.penjualanresep_id IS NOT NULL and stokobatalkes_t.qtystok_in = 0  THEN	'Penjualan Resep'  :: TEXT :: TEXT 
																	  WHEN penjualan_resep.penjualanresep_id IS NOT NULL AND stokobatalkes_t.qtystok_in <> 0  THEN	'Edit Resep' :: TEXT ELSE NULL :: TEXT
								
																END AS keterangan,
																penjualan_resep.no_transaksi,
																( CASE WHEN tglstok_in IS NULL THEN tglstok_out ELSE tglstok_in END ) AS tanggal_transaksi,
																stokobatalkes_t.obatalkes_id,
																stokobatalkes_t.satuankecil_id AS satuanunit_id,
																stokobatalkes_t.tglkadaluarsa,
																SUM ( stokobatalkes_t.qtystok_in ) AS qtystok_in,
																SUM ( stokobatalkes_t.qtystok_out ) AS qtystok_out,
																penjualan_resep.reference,
																stokobatalkes_t.ruangan_id,
																stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
															FROM
																stokobatalkes_t
																JOIN (
																SELECT
																	obatalkespasien_t.obatalkespasien_id,
																	obatalkespasien_t.penjualanresep_id,
																CASE
									
																		WHEN penjualanresep_t.noresep IS NOT NULL THEN
																		penjualanresep_t.noresep ELSE pendaftaran_t.no_pendaftaran 
																	END AS no_transaksi,
																CASE
									
																		WHEN obatalkespasien_t.penjualanresep_id IS NULL THEN
																		pasien_pendaftaran.nama_pasien 
																		WHEN penjualanresep_t.pasien_id IS NOT NULL THEN
																		pasien_m.nama_pasien ELSE penjualanresep_t.nama_pembeli 
																	END AS reference 
																FROM
																	obatalkespasien_t
																	LEFT JOIN ( SELECT A.penjualanresep_id, A.noresep, A.pasien_id, A.nama_pembeli FROM penjualanresep_t A ) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
																	LEFT JOIN ( SELECT A.pasien_id, A.nama_pasien FROM pasien_m A ) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
																	LEFT JOIN ( SELECT A.pendaftaran_id, A.no_pendaftaran, A.pasien_id FROM pendaftaran_t A ) pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
																	LEFT JOIN ( SELECT A.pasien_id, A.nama_pasien FROM pasien_m A ) pasien_pendaftaran ON pendaftaran_t.pasien_id = pasien_pendaftaran.pasien_id 
																WHERE
																	obatalkespasien_t.udd_detail_id IS NULL 
																) penjualan_resep ON stokobatalkes_t.obatalkespasien_id = penjualan_resep.obatalkespasien_id 
															WHERE
																stokobatalkes_t.is_deleted = FALSE 
																AND stokobatalkes_t.returresepdetail_id IS NULL 
																AND stokobatalkes_t.pembatalanresep_id IS NULL 
															GROUP BY
																(
																CASE
									
																		WHEN penjualan_resep.penjualanresep_id IS NULL AND stokobatalkes_t.tglstok_in IS NOT NULL THEN	'BATAL BMHP' :: TEXT 
																		WHEN penjualan_resep.penjualanresep_id IS NULL THEN	'BMHP' :: TEXT 
																		WHEN penjualan_resep.penjualanresep_id IS NOT NULL and stokobatalkes_t.qtystok_in = 0  THEN	'Penjualan Resep'  :: TEXT :: TEXT 
																	  WHEN penjualan_resep.penjualanresep_id IS NOT NULL AND stokobatalkes_t.qtystok_in <> 0  THEN	'Edit Resep' :: TEXT ELSE NULL :: TEXT
																		END 
																		),
																		penjualan_resep.no_transaksi,
																		( 
																		CASE WHEN tglstok_in IS NULL THEN tglstok_out ELSE tglstok_in END ),
																		stokobatalkes_t.obatalkes_id,
																		stokobatalkes_t.satuankecil_id,
																		stokobatalkes_t.tglkadaluarsa,
																		stokobatalkes_t.qtystok_in,
																		penjualan_resep.reference,
																		stokobatalkes_t.ruangan_id 
									
																		UNION ALL
									
																	SELECT MAX
																		( stokobatalkes_t.stokobatalkes_id ) AS stokobatalkes_id,
																		'Pembatalan Resep' :: TEXT AS keterangan,
																		pembatalan_resep.no_pembatalan AS no_transaksi,
																		( CASE WHEN tglstok_in IS NULL THEN tglstok_out ELSE tglstok_in END ) AS tanggal_transaksi,
																		stokobatalkes_t.obatalkes_id,
																		stokobatalkes_t.satuankecil_id AS satuanunit_id,
																		stokobatalkes_t.tglkadaluarsa,
																		SUM ( stokobatalkes_t.qtystok_in ) AS qtystok_in,
																		SUM ( stokobatalkes_t.qtystok_out ) AS qtystok_out,
																		pembatalan_resep.reference,
																		stokobatalkes_t.ruangan_id,
																		stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																		stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																	FROM
																		stokobatalkes_t
																		JOIN (
																		SELECT
																			pembatalanresep_t.pembatalanresep_id,
																			pembatalanresep_t.tgl_pembatalan,
																			pembatalanresep_t.no_pembatalan,
																		CASE
											
																				WHEN penjualanresep_t.pasien_id IS NOT NULL THEN
																				pasien_m.nama_pasien ELSE penjualanresep_t.nama_pembeli 
																			END AS reference 
																		FROM
																			pembatalanresep_t
																			JOIN ( SELECT A.penjualanresep_id, A.noresep, A.pasien_id, A.nama_pembeli FROM penjualanresep_t A ) penjualanresep_t ON pembatalanresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
																			LEFT JOIN ( SELECT A.pasien_id, A.nama_pasien FROM pasien_m A ) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id 
																		) pembatalan_resep ON pembatalan_resep.pembatalanresep_id = stokobatalkes_t.pembatalanresep_id 
																	WHERE
																		stokobatalkes_t.is_deleted = FALSE 
																	GROUP BY
																		pembatalan_resep.no_pembatalan,
																		( CASE WHEN tglstok_in IS NULL THEN tglstok_out ELSE tglstok_in END ),
																		stokobatalkes_t.obatalkes_id,
																		stokobatalkes_t.satuankecil_id,
																		stokobatalkes_t.tglkadaluarsa,
																		pembatalan_resep.reference,
																		stokobatalkes_t.ruangan_id UNION ALL
																	SELECT MAX
																		( stokobatalkes_t.stokobatalkes_id ),
																		'Retur Resep' :: TEXT AS keterangan,
																		retur_resep.no_returresep AS no_transaksi,
																		stokobatalkes_t.tglstok_in AS tanggal_transaksi,
																		stokobatalkes_t.obatalkes_id,
																		stokobatalkes_t.satuankecil_id AS satuanunit_id,
																		stokobatalkes_t.tglkadaluarsa,
																		SUM ( stokobatalkes_t.qtystok_in ),
																		SUM ( stokobatalkes_t.qtystok_out ),
																		retur_resep.reference,
																		stokobatalkes_t.ruangan_id,
																		stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																		stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																	FROM
																		stokobatalkes_t
																		JOIN (
																		SELECT
																			returresepdetail_t.returresepdetail_id,
																			returresep_t.no_returresep,
																			returresep_t.tgl_retur,
																		CASE
											
																				WHEN penjualanresep_t.pasien_id IS NOT NULL THEN
																				pasien_m.nama_pasien ELSE penjualanresep_t.nama_pembeli 
																			END AS reference 
																		FROM
																			returresepdetail_t
																			JOIN ( SELECT A.returresep_id, A.no_returresep, A.tgl_retur, A.penjualanresep_id FROM returresep_t A ) returresep_t ON returresepdetail_t.returresep_id = returresep_t.returresep_id
																			JOIN ( SELECT A.penjualanresep_id, A.pasien_id, A.nama_pembeli FROM penjualanresep_t A ) penjualanresep_t ON returresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
																			LEFT JOIN ( SELECT A.pasien_id, A.nama_pasien FROM pasien_m A ) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id 
																		) retur_resep ON stokobatalkes_t.returresepdetail_id = retur_resep.returresepdetail_id 
																	WHERE
																		stokobatalkes_t.is_deleted = FALSE 
																	GROUP BY
																		retur_resep.no_returresep,
																		stokobatalkes_t.tglstok_in,
																		stokobatalkes_t.obatalkes_id,
																		stokobatalkes_t.satuankecil_id,
																		stokobatalkes_t.tglkadaluarsa,
																		retur_resep.reference,
																		stokobatalkes_t.ruangan_id UNION ALL
																	SELECT MAX
																		( stokobatalkes_t.stokobatalkes_id ) AS stokobatalkes_id,
																		'Adjusmen Masuk' :: TEXT AS keterangan,
																		adjusmen_masuk.no_adjusmen AS no_transaksi,
																		stokobatalkes_t.tglstok_in AS tanggal_transaksi,
																		stokobatalkes_t.obatalkes_id,
																		stokobatalkes_t.satuankecil_id AS satuanunit_id,
																		stokobatalkes_t.tglkadaluarsa,
																		SUM ( stokobatalkes_t.qtystok_in ) AS qtystok_in,
																		SUM ( stokobatalkes_t.qtystok_out ) AS qtystok_out,
																		adjusmen_masuk.keterangan :: CHARACTER VARYING AS reference,
																		stokobatalkes_t.ruangan_id,
																		stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																		stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																	FROM
																		stokobatalkes_t
																		JOIN (
																		SELECT
																			adjusmenobatmasuk_t.adjusmenobatmasuk_id,
																			adjusmenobat_t.no_adjusmen,
																			adjusmenobat_t.tgl_adjusmen,
																			adjusmenobatmasuk_t.keterangan 
																		FROM
																			adjusmenobatmasuk_t
																			JOIN ( SELECT A.adjusmenobat_id, A.no_adjusmen, A.tgl_adjusmen FROM adjusmenobat_t A ) adjusmenobat_t ON adjusmenobatmasuk_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id 
																		) adjusmen_masuk ON stokobatalkes_t.adjusmenobatmasuk_id = adjusmen_masuk.adjusmenobatmasuk_id 
																	WHERE
																		stokobatalkes_t.is_deleted = FALSE 
																	GROUP BY
																		adjusmen_masuk.no_adjusmen,
																		stokobatalkes_t.tglstok_in,
																		stokobatalkes_t.obatalkes_id,
																		stokobatalkes_t.satuankecil_id,
																		stokobatalkes_t.tglkadaluarsa,
																		stokobatalkes_t.ruangan_id,
																		adjusmen_masuk.keterangan
															
																		 UNION ALL
															 
															 
																		SELECT MAX
																		( stokobatalkes_t.stokobatalkes_id ) AS stokobatalkes_id,
																		'Adjusmen Keluar' :: TEXT AS keterangan,
																		adjusmen_keluar.no_adjusmen AS no_transaksi,
																		stokobatalkes_t.tglstok_out AS tanggal_transaksi,
																		stokobatalkes_t.obatalkes_id,
																		stokobatalkes_t.satuankecil_id AS satuanunit_id,
																		stokobatalkes_t.tglkadaluarsa,
																		SUM ( stokobatalkes_t.qtystok_in ) AS qtystok_in,
																		SUM ( stokobatalkes_t.qtystok_out ) AS qtystok_out,
																		adjusmen_keluar.keterangan :: CHARACTER VARYING AS reference,
																		stokobatalkes_t.ruangan_id,
																		stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																		stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																	FROM
																		stokobatalkes_t
																		JOIN (
																		SELECT
																			adjusmenobatkeluar_t.adjusmenobatkeluar_id,
																			adjusmenobat_t.no_adjusmen,
																			adjusmenobat_t.tgl_adjusmen,
																			adjusmenobatkeluar_t.keterangan
																		FROM
																			adjusmenobatkeluar_t
																			JOIN ( SELECT A.adjusmenobat_id, A.no_adjusmen, A.tgl_adjusmen FROM adjusmenobat_t A ) adjusmenobat_t ON adjusmenobatkeluar_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id 
																		) adjusmen_keluar ON stokobatalkes_t.adjusmenobatkeluar_id = adjusmen_keluar.adjusmenobatkeluar_id 
																	WHERE
																		stokobatalkes_t.is_deleted = FALSE 
																	GROUP BY
																		adjusmen_keluar.no_adjusmen,
																		stokobatalkes_t.tglstok_out,
																		stokobatalkes_t.obatalkes_id,
																		stokobatalkes_t.satuankecil_id,
																		stokobatalkes_t.tglkadaluarsa,
																		stokobatalkes_t.ruangan_id,
																		adjusmen_keluar.keterangan
															
																		UNION ALL-- 	SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
									--             'Penerimaan Alternatif'::text AS keterangan,
									--             penerimaan_alternatif.no_penerimaan AS no_transaksi,
									--             stokobatalkes_t.tglstok_in AS tanggal_transaksi,
									--             stokobatalkes_t.obatalkes_id,
									--             stokobatalkes_t.satuankecil_id AS satuanunit_id,
									--             stokobatalkes_t.tglkadaluarsa,
									--             sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
									--             sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
									--             '-'::character varying AS reference,
									--             stokobatalkes_t.ruangan_id,
									--             stokobatalkes_t.ruangan_id AS ruangan_asal_id,
									--             stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
									--            FROM stokobatalkes_t
									--              JOIN ( SELECT penerimaansuppdetail_t.penerimaansuppdetail_id,
									--                     penerimaansupp_t.no_penerimaan,
									--                     penerimaansupp_t.tgl_penerimaan
									--                    FROM penerimaansupp_t
									--                      JOIN penerimaansuppdetail_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id) penerimaan_alternatif ON stokobatalkes_t.penerimaansuppdetail_id = penerimaan_alternatif.penerimaansuppdetail_id
									--           WHERE stokobatalkes_t.is_deleted = false
									--           GROUP BY 'Penerimaan Alternatif'::text, penerimaan_alternatif.no_penerimaan, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, '-'::character varying, stokobatalkes_t.ruangan_id
																	SELECT MAX
																		( stokobatalkes_t.stokobatalkes_id ) AS stokobatalkes_id,
									-- 	'Penerimaan Alternatif' :: TEXT AS keterangan,
																	CASE
										
																			WHEN penerimaan_alternatif.is_consigment = TRUE 
																			AND penerimaan_alternatif.is_donasi = FALSE THEN
																				'Penerimaan Consigment' :: TEXT 
																				WHEN ( penerimaan_alternatif.is_consigment = FALSE OR penerimaan_alternatif.is_consigment IS NULL ) 
																				AND penerimaan_alternatif.is_donasi = FALSE THEN
																					'Penerimaan Manual' :: TEXT 
																					WHEN ( penerimaan_alternatif.is_consigment = FALSE OR penerimaan_alternatif.is_consigment IS NULL ) 
																					AND penerimaan_alternatif.is_donasi = TRUE THEN
																						'Penerimaan Donasi' :: TEXT 
																						END AS keterangan,
																					penerimaan_alternatif.no_penerimaan AS no_transaksi,
																					stokobatalkes_t.tglstok_in AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					SUM ( stokobatalkes_t.qtystok_in ) AS qtystok_in,
																					SUM ( stokobatalkes_t.qtystok_out ) AS qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					stokobatalkes_t.ruangan_id,
																					stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																					stokobatalkes_t.ruangan_id AS ruangan_tujuan_id -- 	penerimaan_alternatif.is_consigment,
									-- 	penerimaan_alternatif.is_donasi
												
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						penerimaansuppdetail_t.penerimaansuppdetail_id,
																						penerimaansupp_t.no_penerimaan,
																						penerimaansupp_t.tgl_penerimaan,
																						penerimaansupp_t.is_consigment,
																						penerimaansupp_t.is_donasi 
																					FROM
																						penerimaansupp_t
																						JOIN ( SELECT A.penerimaansuppdetail_id, A.penerimaansupp_id FROM penerimaansuppdetail_t A ) penerimaansuppdetail_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id 
																					) penerimaan_alternatif ON stokobatalkes_t.penerimaansuppdetail_id = penerimaan_alternatif.penerimaansuppdetail_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE 
																					GROUP BY-- 	'Penerimaan Alternatif' :: TEXT,
																					penerimaan_alternatif.no_penerimaan,
																					stokobatalkes_t.tglstok_in,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id,
																					stokobatalkes_t.tglkadaluarsa,
																					'-' :: CHARACTER VARYING,
																					stokobatalkes_t.ruangan_id,
																					penerimaan_alternatif.is_consigment,
																					penerimaan_alternatif.is_donasi UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Pemakaian Ruangan' :: TEXT AS keterangan,
																					pemakaian_ruangan.nopemakaian_obat AS no_transaksi,
																					stokobatalkes_t.tglstok_out AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					stokobatalkes_t.ruangan_id,
																					stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																					stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						pemakaianobatdetail_t.pemakaianobatdetail_id,
																						pemakaianobat_t.nopemakaian_obat,
																						pemakaianobat_t.tglpemakaianobat 
																					FROM
																						pemakaianobat_t
																						JOIN ( SELECT A.pemakaianobat_id, A.pemakaianobatdetail_id FROM pemakaianobatdetail_t A ) pemakaianobatdetail_t ON pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id 
																					) pemakaian_ruangan ON stokobatalkes_t.pemakaianobatdetail_id = pemakaian_ruangan.pemakaianobatdetail_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Pemusnahan Obat' :: TEXT AS keterangan,
																					pemusnahan_obat.nopemusnahan AS no_transaksi,
																					stokobatalkes_t.tglstok_out AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					stokobatalkes_t.ruangan_id,
																					stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																					stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						pemusnahanobatdetail_t.pemusnahanobatdetail_id,
																						pemusnahanobat_t.nopemusnahan,
																						pemusnahanobat_t.tglpemusnahan 
																					FROM
																						pemusnahanobat_t
																						JOIN ( SELECT A.pemusnahanobat_id, A.pemusnahanobatdetail_id FROM pemusnahanobatdetail_t A ) pemusnahanobatdetail_t ON pemusnahanobat_t.pemusnahanobat_id = pemusnahanobatdetail_t.pemusnahanobat_id 
																					) pemusnahan_obat ON stokobatalkes_t.pemusnahanobatdetail_id = pemusnahan_obat.pemusnahanobatdetail_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE UNION ALL
																				SELECT MAX
																					( stokobatalkes_t.stokobatalkes_id ),
																					'Stok Opname' :: TEXT AS keterangan,
																					stok_opname.nostokopname AS no_transaksi,
																					( CASE WHEN tglstok_in IS NULL THEN tglstok_out ELSE tglstok_in END ) AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					SUM ( stokobatalkes_t.qtystok_in ),
																					SUM ( stokobatalkes_t.qtystok_out ),
																					'-' :: CHARACTER VARYING AS reference,
																					stokobatalkes_t.ruangan_id,
																					stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																					stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						stokopnamedetail_t.stokopnamedetail_id,
																						stokopname_t.nostokopname,
																						stokopname_t.tglstokopname 
																					FROM
																						stokopname_t
																						JOIN ( SELECT A.stokopname_id, A.stokopnamedetail_id FROM stokopnamedetail_t A ) stokopnamedetail_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id 
																					) stok_opname ON stokobatalkes_t.stokopnamedetail_id = stok_opname.stokopnamedetail_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE 
																				GROUP BY
																					stok_opname.nostokopname,
																					( CASE WHEN tglstok_in IS NULL THEN tglstok_out ELSE tglstok_in END ),
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.ruangan_id UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Penerimaan Supplier' :: TEXT AS keterangan,
																					penerimaan_supp.no_penerimaan AS no_transaksi,
																					stokobatalkes_t.tglstok_in AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					stokobatalkes_t.ruangan_id,
																					stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																					stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						penerimaanobatdetail_t.penerimaanobatdetail_id,
																						penerimaanobat_t.no_penerimaan,
																						penerimaanobat_t.tgl_penerimaan 
																					FROM
																						penerimaanobat_t
																						JOIN ( SELECT A.penerimaanobat_id, A.penerimaanobatdetail_id FROM penerimaanobatdetail_t A ) penerimaanobatdetail_t ON penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id 
																					) penerimaan_supp ON stokobatalkes_t.penerimaanobatdetail_id = penerimaan_supp.penerimaanobatdetail_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Retur Penerimaan Supplier' :: TEXT AS keterangan,
																					retur_penerimaan.no_returpenerimaanobat AS no_transaksi,
																					stokobatalkes_t.tglstok_out AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					stokobatalkes_t.ruangan_id,
																					stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																					stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						returpenerimaanobatdetail_t.returpenerimaanobatdetail_id,
																						returpenerimaanobat_t.no_returpenerimaanobat,
																						returpenerimaanobat_t.tgl_retur 
																					FROM
																						returpenerimaanobat_t
																						JOIN ( SELECT A.returpenerimaanobat_id, A.returpenerimaanobatdetail_id FROM returpenerimaanobatdetail_t A ) returpenerimaanobatdetail_t ON returpenerimaanobatdetail_t.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id 
																					) retur_penerimaan ON stokobatalkes_t.returpenerimaanobatdetail_id = retur_penerimaan.returpenerimaanobatdetail_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Penerimaan Mutasi' :: TEXT AS keterangan,
																					terima_mutasi.noterimamutasi AS no_transaksi,
																					stokobatalkes_t.tglstok_in AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					terima_mutasi.ruanganasal_id AS ruangan_id,
																					terima_mutasi.ruanganasal_id AS ruangan_asal_id,
																					terima_mutasi.ruanganpenerima_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						terimamutasiobatdetail_t.terimamutasiobatdetail_id,
																						terimamutasiobat_t.noterimamutasi,
																						terimamutasiobat_t.tglterima,
																						terimamutasiobat_t.ruanganpenerima_id,
																						terimamutasiobat_t.ruanganasal_id 
																					FROM
																						terimamutasiobat_t
																						JOIN ( SELECT A.terimamutasiobat_id, A.terimamutasiobatdetail_id FROM terimamutasiobatdetail_t A ) terimamutasiobatdetail_t ON terimamutasiobat_t.terimamutasiobat_id = terimamutasiobatdetail_t.terimamutasiobat_id 
																					) terima_mutasi ON stokobatalkes_t.terimamutasidetail_id = terima_mutasi.terimamutasiobatdetail_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Mutasi Obat' :: TEXT AS keterangan,
																					mutasi_obat.nomutasioa AS no_transaksi,
																					stokobatalkes_t.tglstok_out AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					mutasi_obat.ruanganasal_id AS ruangan_id,
																					mutasi_obat.ruanganasal_id AS ruangan_asal_id,
																					mutasi_obat.ruangantujuan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						mutasiobatdetail_t.mutasiobatdetail_id,
																						mutasiobatruangan_t.nomutasioa,
																						mutasiobatruangan_t.tglmutasioa,
																						mutasiobatruangan_t.ruanganasal_id,
																						mutasiobatruangan_t.ruangantujuan_id 
																					FROM
																						mutasiobatruangan_t
																						JOIN ( SELECT A.mutasiobatruangan_id, A.mutasiobatdetail_id FROM mutasiobatdetail_t A ) mutasiobatdetail_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id 
																					) mutasi_obat ON stokobatalkes_t.mutasiobatdetail_id = mutasi_obat.mutasiobatdetail_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE 
																					UNION ALL
												
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'UDD' :: TEXT AS keterangan,
																					udd.no_udd AS no_transaksi,
																					stokobatalkes_t.tglstok_out AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					udd.ruanganproses_id AS ruangan_id,
																					udd.ruanganproses_id AS ruangan_asal_id,
																					udd.ruanganproses_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						obatalkespasien_t.obatalkespasien_id,
																						obatalkespasien_t.udd_detail_id,
																						udd_t.no_udd,
																						udd_t.ruanganproses_id 
																					FROM
																						obatalkespasien_t
																						JOIN ( SELECT A.udd_detail_id, A.udd_id FROM udd_detail_t A ) udd_detail_t ON obatalkespasien_t.udd_detail_id = udd_detail_t.udd_detail_id
																						JOIN ( SELECT A.udd_id, A.no_udd, A.ruanganproses_id FROM udd_t A ) udd_t ON udd_detail_t.udd_id = udd_t.udd_id 
																					) udd ON stokobatalkes_t.obatalkespasien_id = udd.obatalkespasien_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE
												
																					 UNION ALL
												 
																					--- UDD RETUR 
																						SELECT
																				stokobatalkes_t.stokobatalkes_id,
																				'RETUR UDD' :: TEXT AS keterangan,
																				udd.no_udd AS no_transaksi,
																				stokobatalkes_t.tglstok_in AS tanggal_transaksi,
																				stokobatalkes_t.obatalkes_id,
																				stokobatalkes_t.satuankecil_id AS satuanunit_id,
																				stokobatalkes_t.tglkadaluarsa,
																				stokobatalkes_t.qtystok_in,
																				stokobatalkes_t.qtystok_out,
																				'-' :: CHARACTER VARYING AS reference,
																				udd.ruanganproses_id AS ruangan_id,
																				udd.ruanganproses_id AS ruangan_asal_id,
																				udd.ruanganproses_id AS ruangan_tujuan_id 
																			FROM
																				stokobatalkes_t
																				JOIN (SELECT uddreturdetail_t.uddreturdetail_id,uddreturdetail_t.uddretur_id FROM uddreturdetail_t) uddreturdetail_t on uddreturdetail_t.uddreturdetail_id = stokobatalkes_t.uddreturdetail_id
																				JOIN (SELECT uddretur_t.uddretur_id,uddretur_t.nouddretur,uddretur_t.udd_id from uddretur_t) uddretur_t on uddretur_t.uddretur_id = uddreturdetail_t.uddretur_id
																				join (SELECT udd_t.udd_id,udd_t.ruanganproses_id,udd_t.no_udd from udd_t) udd on 	udd.udd_id = uddretur_t.udd_id
																			WHERE
																				stokobatalkes_t.is_deleted = FALSE 
											
																						UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Kirim CSSD' :: TEXT AS keterangan,
																					kirim_cssd.no_pengajuan_sterilisasi AS no_transaksi,
																					stokobatalkes_t.tglstok_out AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					kirim_cssd.ruanganasal_id AS ruangan_id,
																					kirim_cssd.ruanganasal_id AS ruangan_asal_id,
																					kirim_cssd.ruangantujuan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						cssddet_t.cssddet_id,
																						cssd_t.no_pengajuan_sterilisasi,
																						cssd_t.tgl_pengiriman,
																						cssd_t.ruanganasal_id,
																						cssd_t.ruangantujuan_id,
																						cssddet_t.is_alkes 
																					FROM
																						cssd_t
																						JOIN ( SELECT A.cssd_id, A.cssddet_id, A.is_alkes FROM cssddet_t A ) cssddet_t ON cssddet_t.cssd_id = cssd_t.cssd_id 
																					) kirim_cssd ON stokobatalkes_t.cssddet_id = kirim_cssd.cssddet_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE 
																					AND kirim_cssd.is_alkes = TRUE 
																					AND stokobatalkes_t.tglstok_out IS NOT NULL UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Terima CSSD' :: TEXT AS keterangan,
																					kirim_cssd.no_pengajuan_sterilisasi AS no_transaksi,
																					stokobatalkes_t.tglstok_in AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					kirim_cssd.ruanganasal_id AS ruangan_id,
																					kirim_cssd.ruanganasal_id AS ruangan_asal_id,
																					kirim_cssd.ruangantujuan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT
																						cssddet_t.cssddet_id,
																						cssd_t.no_pengajuan_sterilisasi,
																						cssd_t.tgl_pengiriman,
																						cssd_t.ruanganasal_id,
																						cssd_t.ruangantujuan_id,
																						cssddet_t.is_alkes 
																					FROM
																						cssd_t
																						JOIN ( SELECT A.cssd_id, A.cssddet_id, A.is_alkes FROM cssddet_t A ) cssddet_t ON cssddet_t.cssd_id = cssd_t.cssd_id 
																					) kirim_cssd ON stokobatalkes_t.cssddet_id = kirim_cssd.cssddet_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE 
																					AND kirim_cssd.is_alkes = TRUE 
																					AND stokobatalkes_t.tglstok_in IS NOT NULL UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Penerimaan Unit CSSD' :: TEXT AS keterangan,
																					'Penerimaan Unit ' || penerimaanunit_cssd.no_pengajuan_sterilisasi AS no_transaksi,
																					stokobatalkes_t.tglstok_in AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					penerimaanunit_cssd.ruanganpenerima_id AS ruangan_id,
																					penerimaanunit_cssd.ruanganasal_id AS ruangan_asal_id,
																					penerimaanunit_cssd.ruanganpenerima_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT A
																						.ruanganasal_id,
																						A.ruanganpenerima_id,
																						A.cssdpenerimaanunit_id,
																						cssd_t.no_pengajuan_sterilisasi,
																						cssdpenerimaanunitdet_t.cssdpenerimaanunitdet_id,
																						cssddet_t.is_alkes 
																					FROM
																						cssdpenerimaanunit_t
																						A JOIN ( SELECT A.cssdpenerimaanunitdet_id, A.cssddet_id, A.cssdpenerimaanunit_id FROM cssdpenerimaanunitdet_t A ) cssdpenerimaanunitdet_t ON A.cssdpenerimaanunit_id = cssdpenerimaanunitdet_t.cssdpenerimaanunit_id
																						JOIN ( SELECT A.cssddet_id, A.cssd_id, A.is_alkes FROM cssddet_t A ) cssddet_t ON cssdpenerimaanunitdet_t.cssddet_id = cssddet_t.cssddet_id
																						JOIN ( SELECT A.no_pengajuan_sterilisasi, A.cssd_id FROM cssd_t A ) cssd_t ON cssddet_t.cssd_id = cssd_t.cssd_id -- a.no_pengajuan_sterilisasi
													
																					) penerimaanunit_cssd ON stokobatalkes_t.cssdpenerimaanunitdet_id = penerimaanunit_cssd.cssdpenerimaanunitdet_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE 
																					AND penerimaanunit_cssd.is_alkes = TRUE 
																					AND stokobatalkes_t.tglstok_in IS NOT NULL
												
																			--		 UNION ALL
									-- 											SELECT
									-- 												stokobatalkes_t.stokobatalkes_id,
									-- 												'Penerimaan Unit CSSD' :: TEXT AS keterangan,
									-- 												'Penerimaan Unit ' || penerimaanunit_cssd.no_pengajuan_sterilisasi AS no_transaksi,
									-- 												stokobatalkes_t.tglstok_in AS tanggal_transaksi,
									-- 												stokobatalkes_t.obatalkes_id,
									-- 												stokobatalkes_t.satuankecil_id AS satuanunit_id,
									-- 												stokobatalkes_t.tglkadaluarsa,
									-- 												stokobatalkes_t.qtystok_in,
									-- 												stokobatalkes_t.qtystok_out,
									-- 												'-' :: CHARACTER VARYING AS reference,
									-- 												penerimaanunit_cssd.ruanganpenerima_id AS ruangan_id,
									-- 												penerimaanunit_cssd.ruanganasal_id AS ruangan_asal_id,
									-- 												penerimaanunit_cssd.ruanganpenerima_id AS ruangan_tujuan_id 
									-- 											FROM
									-- 												stokobatalkes_t
									-- 												JOIN (
									-- 												SELECT A
									-- 													.ruanganasal_id,
									-- 													A.ruanganpenerima_id,
									-- 													A.cssdpenerimaanunit_id,
									-- 													cssd_t.no_pengajuan_sterilisasi,
									-- 													cssdpenerimaanunitdet_t.cssdpenerimaanunitdet_id,
									-- 													cssddet_t.is_alkes 
									-- 												FROM
									-- 													cssdpenerimaanunit_t
									-- 													A JOIN ( SELECT A.cssdpenerimaanunitdet_id, A.cssddet_id, A.cssdpenerimaanunit_id FROM cssdpenerimaanunitdet_t A ) cssdpenerimaanunitdet_t ON A.cssdpenerimaanunit_id = cssdpenerimaanunitdet_t.cssdpenerimaanunit_id
									-- 													JOIN ( SELECT A.cssddet_id, A.cssd_id, A.is_alkes FROM cssddet_t A ) cssddet_t ON cssdpenerimaanunitdet_t.cssddet_id = cssddet_t.cssddet_id
									-- 													JOIN ( SELECT A.no_pengajuan_sterilisasi, A.cssd_id FROM cssd_t A ) cssd_t ON cssddet_t.cssd_id = cssd_t.cssd_id -- a.no_pengajuan_sterilisasi
									-- 													
									-- 												) penerimaanunit_cssd ON stokobatalkes_t.cssdpenerimaanunitdet_id = penerimaanunit_cssd.cssdpenerimaanunitdet_id 
									-- 											WHERE
									-- 												stokobatalkes_t.is_deleted = FALSE 
									-- 												AND penerimaanunit_cssd.is_alkes = TRUE 
									-- 												AND stokobatalkes_t.tglstok_in IS NOT NULL
												
																					 UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Kirim Unit CSSD' :: TEXT AS keterangan,
																					'Kirim Unit ' || penerimaanunit_cssd.no_pengajuan_sterilisasi AS no_transaksi,
																					stokobatalkes_t.tglstok_out AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					penerimaanunit_cssd.ruanganpenerima_id AS ruangan_id,
																					penerimaanunit_cssd.ruanganasal_id AS ruangan_asal_id,
																					penerimaanunit_cssd.ruanganpenerima_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					JOIN (
																					SELECT A
																						.ruanganasal_id,
																						A.ruanganpenerima_id,
																						A.cssdpenerimaanunit_id,
																						cssd_t.no_pengajuan_sterilisasi,
																						cssdpenerimaanunitdet_t.cssdpenerimaanunitdet_id,
																						cssddet_t.is_alkes 
																					FROM
																						cssdpenerimaanunit_t
																						A JOIN ( SELECT A.cssdpenerimaanunitdet_id, A.cssddet_id, A.cssdpenerimaanunit_id FROM cssdpenerimaanunitdet_t A ) cssdpenerimaanunitdet_t ON A.cssdpenerimaanunit_id = cssdpenerimaanunitdet_t.cssdpenerimaanunit_id
																						JOIN ( SELECT A.cssddet_id, A.cssd_id, A.is_alkes FROM cssddet_t A ) cssddet_t ON cssdpenerimaanunitdet_t.cssddet_id = cssddet_t.cssddet_id
																						JOIN ( SELECT A.no_pengajuan_sterilisasi, A.cssd_id FROM cssd_t A ) cssd_t ON cssddet_t.cssd_id = cssd_t.cssd_id 
																					) penerimaanunit_cssd ON stokobatalkes_t.cssdpenerimaanunitdet_id = penerimaanunit_cssd.cssdpenerimaanunitdet_id 
																				WHERE
																					stokobatalkes_t.is_deleted = FALSE 
																					AND penerimaanunit_cssd.is_alkes = TRUE 
																					AND stokobatalkes_t.tglstok_out IS NOT NULL UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Rusak CSSD' :: TEXT AS keterangan,
																					'Rusak ' || cssdsterilisasi_t.no_sterilisasi AS no_transaksi,
																					stokobatalkes_t.tglstok_out AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					stokobatalkes_t.ruangan_id AS ruangan_id,
																					stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																					stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t
																					LEFT JOIN ( SELECT cssdrusakdet_t.cssdrusakdet_id, cssdrusakdet_t.cssdrusak_id FROM cssdrusakdet_t ) cssdrusakdet_t ON stokobatalkes_t.cssdrusakdet_id = cssdrusakdet_t.cssdrusakdet_id
																					LEFT JOIN ( SELECT cssdrusak_t.cssdrusak_id, cssdrusak_t.cssdsterilisasi_id FROM cssdrusak_t ) cssdrusak_t ON cssdrusakdet_t.cssdrusak_id = cssdrusak_t.cssdrusak_id
																					LEFT JOIN ( SELECT cssdsterilisasi_t.cssdsterilisasi_id, cssdsterilisasi_t.no_sterilisasi FROM cssdsterilisasi_t ) cssdsterilisasi_t ON cssdrusak_t.cssdsterilisasi_id = cssdsterilisasi_t.cssdsterilisasi_id 
																				WHERE
																					stokobatalkes_t.cssdrusakdet_id IS NOT NULL UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Transaksi Tidak Diketahui' :: TEXT AS keterangan,
																					NULL :: TEXT AS no_transaksi,
																					stokobatalkes_t.tglstok_out AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					stokobatalkes_t.ruangan_id AS ruangan_id,
																					stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																					stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t 
																				WHERE
																					stokobatalkes_t.penerimaanobatdetail_id IS NULL 
																					AND stokobatalkes_t.terimamutasidetail_id IS NULL 
																					AND stokobatalkes_t.returresepdetail_id IS NULL 
																					AND stokobatalkes_t.mutasiobatdetail_id IS NULL 
																					AND stokobatalkes_t.obatalkespasien_id IS NULL 
																					AND stokobatalkes_t.pemusnahanobatdetail_id IS NULL 
																					AND stokobatalkes_t.pemakaianobatdetail_id IS NULL 
																					AND stokobatalkes_t.produksiobatdetail_id IS NULL 
																					AND stokobatalkes_t.storexpiredobatdetail_id IS NULL 
																					AND stokobatalkes_t.penerimaansuppdetail_id IS NULL 
																					AND stokobatalkes_t.adjusmenobatmasuk_id IS NULL 
																					AND stokobatalkes_t.adjusmenobatkeluar_id IS NULL 
																					AND stokobatalkes_t.pembatalanresep_id IS NULL 
																					AND stokobatalkes_t.returpenerimaanobatdetail_id IS NULL 
																					AND stokobatalkes_t.stokopnamedetail_id IS NULL 
																					AND stokobatalkes_t.cssddet_id IS NULL 
																					AND stokobatalkes_t.cssdrusakdet_id IS NULL 
																					AND stokobatalkes_t.cssdpenerimaanunitdet_id IS NULL 
																					AND stokobatalkes_t.tglstok_out IS NOT NULL UNION ALL
																				SELECT
																					stokobatalkes_t.stokobatalkes_id,
																					'Transaksi Tidak Diketahui' :: TEXT AS keterangan,
																					NULL :: TEXT AS no_transaksi,
																					stokobatalkes_t.tglstok_in AS tanggal_transaksi,
																					stokobatalkes_t.obatalkes_id,
																					stokobatalkes_t.satuankecil_id AS satuanunit_id,
																					stokobatalkes_t.tglkadaluarsa,
																					stokobatalkes_t.qtystok_in,
																					stokobatalkes_t.qtystok_out,
																					'-' :: CHARACTER VARYING AS reference,
																					stokobatalkes_t.ruangan_id AS ruangan_id,
																					stokobatalkes_t.ruangan_id AS ruangan_asal_id,
																					stokobatalkes_t.ruangan_id AS ruangan_tujuan_id 
																				FROM
																					stokobatalkes_t 
																				WHERE
																					stokobatalkes_t.penerimaanobatdetail_id IS NULL 
																					AND stokobatalkes_t.terimamutasidetail_id IS NULL 
																					AND stokobatalkes_t.returresepdetail_id IS NULL 
																					AND stokobatalkes_t.mutasiobatdetail_id IS NULL 
																					AND stokobatalkes_t.obatalkespasien_id IS NULL 
																					AND stokobatalkes_t.pemusnahanobatdetail_id IS NULL 
																					AND stokobatalkes_t.pemakaianobatdetail_id IS NULL 
																					AND stokobatalkes_t.produksiobatdetail_id IS NULL 
																					AND stokobatalkes_t.storexpiredobatdetail_id IS NULL 
																					AND stokobatalkes_t.penerimaansuppdetail_id IS NULL 
																					AND stokobatalkes_t.adjusmenobatmasuk_id IS NULL 
																					AND stokobatalkes_t.adjusmenobatkeluar_id IS NULL 
																					AND stokobatalkes_t.pembatalanresep_id IS NULL 
																					AND stokobatalkes_t.returpenerimaanobatdetail_id IS NULL 
																					AND stokobatalkes_t.stokopnamedetail_id IS NULL 
																					AND stokobatalkes_t.cssddet_id IS NULL 
																					AND stokobatalkes_t.cssdrusakdet_id IS NULL 
																					AND stokobatalkes_t.cssdpenerimaanunitdet_id IS NULL 
																					AND stokobatalkes_t.tglstok_in IS NOT NULL 
																				) kartu_stok
																				JOIN ( SELECT A.obatalkes_id, A.obatalkes_kode, A.obatalkes_nama FROM obatalkes_m A ) obatalkes_m ON kartu_stok.obatalkes_id = obatalkes_m.obatalkes_id
																				LEFT JOIN ( SELECT A.satuanunit_id, A.satuanunit_nama FROM satuanunit_m A ) satuanunit_m ON kartu_stok.satuanunit_id = satuanunit_m.satuanunit_id
																				LEFT JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_asal ON kartu_stok.ruangan_asal_id = ruangan_asal.ruangan_id
																				LEFT JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_tujuan ON kartu_stok.ruangan_tujuan_id = ruangan_tujuan.ruangan_id 
																			ORDER BY
																				(
																				CASE
													
																						WHEN kartu_stok.keterangan = 'Kirim CSSD' :: TEXT THEN
																						kartu_stok.ruangan_asal_id 
																						WHEN kartu_stok.keterangan = 'Terima CSSD' :: TEXT THEN
																						kartu_stok.ruangan_tujuan_id 
																						WHEN kartu_stok.keterangan = 'Penerimaan Unit CSSD' :: TEXT THEN
																						kartu_stok.ruangan_tujuan_id 
																						WHEN kartu_stok.keterangan = 'Kirim Unit CSSD' :: TEXT THEN
																						kartu_stok.ruangan_asal_id 
																						WHEN kartu_stok.keterangan = 'Penerimaan Mutasi' :: TEXT THEN
																						kartu_stok.ruangan_tujuan_id 
																						WHEN kartu_stok.keterangan = 'Mutasi Obat' :: TEXT THEN
																						kartu_stok.ruangan_asal_id ELSE kartu_stok.ruangan_asal_id 
																					END 
																					),
																					kartu_stok.obatalkes_id,
																					kartu_stok.tanggal_transaksi 
																				) ks 
																			WHERE
																				ks.tanggal_transaksi >= v_date_start :: DATE 
																				AND ks.tanggal_transaksi <= v_date_end :: DATE + INTERVAL '23 hours 59 minutes 59 seconds' 
																				AND ks.ruangan_id = v_ruangan 
																				AND ks.obat_id = v_obat;
						
									 END
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
        echo "m230321_051519_migrate_glbj_92_kartustokobat_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230321_051519_migrate_glbj_92_kartustokobat_fn cannot be reverted.\n";

        return false;
    }
    */
}
