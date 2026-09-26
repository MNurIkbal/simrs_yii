CREATE OR REPLACE FUNCTION public.fgetketersediaanobat(xruangan_id integer, xarrayobat text)
 RETURNS TABLE(instalasi_id smallint, instalasi_nama text, ruangan_id integer, ruangan_nama text, obatalkes_id bigint, obatalkes_nama text, obatalkes_namalain text, obatalkes_kode text, qty_masuk double precision, qty_keluar double precision, nilai_ro double precision, satuanbesar_id integer, satuanbesar_nama text, satuankecil_id integer, satuankecil_nama text, satuansedang_id integer, satuansedang_nama text, jenisobatalkes_id integer, jenisobatalkes_nama text, group_jenisobat text, min_stok double precision, max_stok double precision, qty_stok double precision, jml_mutasi double precision, jml_resep_farmasi double precision, jml_resep_dokter double precision, qty_dipesan double precision, qty_tersedia double precision, hargaygdigunakan text, hargamaksimum double precision, hargaminimum double precision, hargaratarata double precision, hargaterakhir double precision, harganetto double precision, ven_id integer, ven_name text)
 LANGUAGE plpgsql
AS $function$ DECLARE
	varray INT [];
BEGIN
		xarrayobat := CONCAT ( '{', xarrayobat, '}' );
	varray := xarrayobat :: INT [];
	RETURN QUERY WITH resepdokter AS (
		SELECT A
			.ruangan_id,
			A.obatalkes_id,
			SUM ( A.jml ) AS jml
-- 			string_agg ( A.reference, ',' :: text ) AS reference 
		FROM
			(
			SELECT
				rt.ruangan_id,
				dt.obatalkes_id,
				SUM ( COALESCE ( dt.det_konversi, dt.qty_konversi ) ) AS jml
-- 				,
-- 			CASE
-- 					
-- 					WHEN penjualanresep_t.noresep IS NULL THEN
-- 					rt.noresep :: text ELSE NULL :: text 
-- 				END AS reference 
			FROM
				reseptur_t rt
				JOIN (
				SELECT
					a_1.resepturdetail_id,
					a_1.obatalkes_id,
					a_1.det_konversi,
					a_1.qty_konversi,
					a_1.reseptur_id,
					a_1.is_deleted 
				FROM
					resepturdetail_calc a_1 
				) dt ON dt.reseptur_id = rt.reseptur_id
				LEFT JOIN ( SELECT a_1.penjualanresep_id, a_1.noresep, a_1.reseptur_id FROM penjualanresep_calc a_1 ) penjualanresep_t ON rt.reseptur_id = penjualanresep_t.reseptur_id 
			WHERE
				rt.status_reseptur <> 660 
				AND rt.status_reseptur <> 432 
				AND rt.ruangan_id = xruangan_id 
				AND rt.is_deleted IS FALSE 
				AND dt.is_deleted IS FALSE 
				AND penjualanresep_t.noresep IS NULL 
				AND dt.obatalkes_id = ANY ( varray ) 
			GROUP BY
				rt.ruangan_id,
				dt.obatalkes_id,
				rt.noresep,
				penjualanresep_t.noresep 
			) A 
		GROUP BY
			A.ruangan_id,
			A.obatalkes_id 
		),
		resepfarmasi AS (
		SELECT A
			.ruangan_id,
			A.obatalkes_id,
			SUM ( A.jml ) AS jml
-- 			,
-- 			string_agg ( A.reference :: text, ',' :: text ) AS reference 
		FROM
			(
			SELECT
				pt.ruangan_id,
				ot.obatalkes_id,
				SUM ( COALESCE ( ot.det_konversi, ot.qty_konversi ) ) AS jml
-- 				,
-- 				pt.noresep AS reference 
			FROM
				obatalkespasien_calc ot
				LEFT JOIN ( SELECT a_1.obatalkes_id FROM obatalkes_m a_1 ) om ON om.obatalkes_id = ot.obatalkes_id
				LEFT JOIN (
				SELECT
					a_1.ruangan_id,
					a_1.noresep,
					a_1.penjualanresep_id,
					a_1.is_deleted,
					a_1.status_reseptur,
					a_1.reseptur_id 
				FROM
					penjualanresep_calc a_1 
				) pt ON pt.penjualanresep_id = ot.penjualanresep_id 
			WHERE
				-- ot.is_deleted IS FALSE 
				pt.is_deleted IS FALSE 
				AND pt.status_reseptur <> 660 
				AND pt.status_reseptur <> 432 
				AND pt.ruangan_id = xruangan_id 
				AND ot.obatalkes_id = ANY ( varray ) 
			GROUP BY
				pt.ruangan_id,
				ot.obatalkes_id,
				pt.noresep 
			) A 
		GROUP BY
			A.ruangan_id,
			A.obatalkes_id 
		),
		kartustok AS (
		SELECT
			st.ruangan_id,
			st.obatalkes_id,
			SUM ( round( st.qtystok_in :: NUMERIC, 3 ) - round( st.qtystok_out :: NUMERIC, 3 ) ) AS total 
		FROM
			stokobatalkes_t st 
		WHERE
			st.is_deleted IS FALSE 
			AND st.ruangan_id = xruangan_id 
			AND st.obatalkes_id = ANY ( varray ) 
		GROUP BY
			st.ruangan_id,
			st.obatalkes_id 
		) SELECT
		hit.instalasi_id :: INT2,
		hit.instalasi_nama :: text,
		hit.ruangan_id :: int4,
		hit.ruangan_nama :: text,
		hit.obatalkes_id :: INT8,
		hit.obatalkes_nama :: text,
		hit.obatalkes_nama :: text AS obatalkes_namalain,
		hit.obatalkes_kode :: text,
		hit.qty_masuk :: FLOAT8,
		hit.qty_keluar :: FLOAT8,
		hit.nilai_ro :: FLOAT8,
		hit.satuanbesar_id :: int4,
		hit.satuanbesar_nama :: text,
		hit.satuankecil_id :: int4,
		hit.satuankecil_nama :: text,
		hit.satuansedang_id :: int4,
		hit.satuansedang_nama :: text,
		hit.jenisobatalkes_id :: int4,
		hit.jenisobatalkes_nama :: text,
		hit.group_jenisobat :: text,
	CASE
			
			WHEN hit.min_stok IS NULL THEN
			0 :: FLOAT8 ELSE hit.min_stok :: FLOAT8 
		END AS min_stok,
	CASE
			
			WHEN hit.max_stok IS NULL THEN
			0 :: FLOAT8 ELSE hit.max_stok :: FLOAT8 
		END AS max_stok,
		hit.total_stok :: FLOAT8 AS qty_stok,
		COALESCE ( hit.jml_mutasi, 0 :: DOUBLE PRECISION ) :: FLOAT8 AS jml_mutasi,
		COALESCE ( hit.jml_resep_farmasi :: DOUBLE PRECISION, 0 :: DOUBLE PRECISION ) :: FLOAT8 AS jml_resep_farmasi,
		COALESCE ( hit.jml_resep_dokter :: DOUBLE PRECISION, 0 :: DOUBLE PRECISION ) :: FLOAT8 AS jml_resep_dokter,
		(
			COALESCE ( hit.jml_mutasi, 0 :: DOUBLE PRECISION ) + COALESCE ( hit.jml_resep_farmasi :: DOUBLE PRECISION, 0 :: DOUBLE PRECISION ) + COALESCE ( hit.jml_resep_dokter :: DOUBLE PRECISION, 0 :: DOUBLE PRECISION ) + COALESCE ( hit.qty_cssd :: BIGINT, 0 :: BIGINT ) :: DOUBLE PRECISION + COALESCE ( hit.qty_udd :: DOUBLE PRECISION, 0 :: BIGINT :: REAL :: DOUBLE PRECISION ) 
		) :: FLOAT8 AS qty_dipesan,
		(
			hit.total_stok :: DOUBLE PRECISION - (
				COALESCE ( hit.jml_mutasi, 0 :: DOUBLE PRECISION ) + COALESCE ( hit.jml_resep_farmasi :: DOUBLE PRECISION, 0 :: DOUBLE PRECISION ) + COALESCE ( hit.jml_resep_dokter :: DOUBLE PRECISION, 0 :: DOUBLE PRECISION ) + COALESCE ( hit.qty_cssd :: BIGINT, 0 :: BIGINT ) :: DOUBLE PRECISION + COALESCE ( hit.qty_udd :: DOUBLE PRECISION, 0 :: BIGINT :: REAL :: DOUBLE PRECISION ) 
			) 
		) :: FLOAT8 AS qty_tersedia,
-- 		hit.reference_mutasi :: text,
-- 		hit.reference_resep_farmasi :: text,
-- 		hit.reference_udd_detail_t :: text AS reference_udd,
-- 		hit.reference_cssd :: text,
-- 		hit.reference_resep_dokter :: text,
		hit.hargaygdigunakan :: text,
		hit.hargamaksimum :: FLOAT8,
		hit.hargaminimum :: FLOAT8,
		hit.hargaratarata :: FLOAT8,
		hit.hargaterakhir :: FLOAT8,
		hit.harganetto :: FLOAT8,
		hit.ven_id :: int4,
		hit.ven_name :: text 
	FROM
		(
		SELECT
			instalasi_m.instalasi_id,
			instalasi_m.instalasi_nama,
			stokobatalkes_r.ruangan_id,
			ruangan_m.ruangan_nama,
			stokobatalkes_r.obatalkes_id,
			obatalkes_m.obatalkes_namalain,
			obatalkes_m.obatalkes_kode,
			obatalkes_m.hargajual,
			obatalkes_m.satuankecil_id,
			satuan_besar.satuanunit_nama AS satuanbesar_nama,
			obatalkes_m.satuansedang_id,
			satuan_sedang.satuanunit_nama AS satuansedang_nama,
			satuan_kecil.satuanunit_nama AS satuankecil_nama,
			obatalkes_m.satuanbesar_id,
			obatalkes_m.jenisobatalkes_id,
			jenisobatalkes_m.jenisobatalkes_nama,
			jenisobatalkes_m.group_jenisobat,
			konfigfarmasi_k.persenppn AS ppn,
			konfigfarmasi_k.persenmargin AS margin,
			konfigfarmasi_k.persen_diskon AS disc,
			konfigfarmasi_k.hargaygdigunakan,
			obatalkes_m.harganetto,
			obatalkes_m.hargamaksimum,
			obatalkes_m.hargaminimum,
			obatalkes_m.hargaratarata,
			obatalkes_m.hargaterakhir,
			stokobatalkes_r.qty_masuk,
			stokobatalkes_r.qty_keluar,
			stokobatalkes_r.qty_dipesan,
			stokobatalkes_r.qty_tersedia,
			stokobatalkes_r.qty_sisa AS qty_stok,
			obatalkes_m.obatalkes_nama,
			obatalkes_m.nilai_ro,
			konfigrak_m.min_stok,
			konfigrak_m.max_stok,
			kartustok.total AS total_stok,
			(
			SELECT SUM
				( mt.jumlah_mutasi ) AS jml 
			FROM
				mutasiobatdetail_t mt
				LEFT JOIN (
				SELECT A
					.ruanganasal_id,
					A.nomutasioa,
					A.mutasiobatruangan_id,
					A.is_deleted,
					A.status_mutasi 
				FROM
					mutasiobatruangan_t A 
				WHERE
					A.status_mutasi = 401 
					AND A.is_deleted IS FALSE 
				) mt2 ON mt2.mutasiobatruangan_id = mt.mutasiobatruangan_id 
			WHERE
				mt.is_deleted IS FALSE 
				AND mt2.ruanganasal_id = stokobatalkes_r.ruangan_id 
				AND mt.obatalkes_id = stokobatalkes_r.obatalkes_id 
			) AS jml_mutasi,
			round( resepfarmasi.jml :: NUMERIC, 3 ) AS jml_resep_farmasi,
			round( resepdokter.jml :: NUMERIC, 3 ) AS jml_resep_dokter,
-- 			(
-- 			SELECT
-- 				string_agg ( mt2.nomutasioa :: text, ',' :: text ) AS reference 
-- 			FROM
-- 				mutasiobatdetail_t mt
-- 				LEFT JOIN (
-- 				SELECT A
-- 					.ruanganasal_id,
-- 					A.nomutasioa,
-- 					A.mutasiobatruangan_id,
-- 					A.is_deleted,
-- 					A.status_mutasi 
-- 				FROM
-- 					mutasiobatruangan_t A 
-- 				WHERE
-- 					A.status_mutasi = 401 
-- 					AND A.is_deleted IS FALSE 
-- 					AND A.mutasiobatruangan_id = xruangan_id 
-- 				) mt2 ON mt2.mutasiobatruangan_id = mt.mutasiobatruangan_id 
-- 			WHERE
-- 				mt.is_deleted IS FALSE 
-- 				AND mt2.ruanganasal_id = stokobatalkes_r.ruangan_id 
-- 				AND mt.obatalkes_id = stokobatalkes_r.obatalkes_id 
-- 			) AS reference_mutasi,
-- 			resepfarmasi.reference AS reference_resep_farmasi,
-- 			resepdokter.reference AS reference_resep_dokter,
			obatalkes_m.ven AS ven_id,
			look_ven.lookup_name AS ven_name,
			0 AS qty_cssd,
			0 AS qty_udd
-- 			NULL :: text AS reference_cssd,
-- 			NULL :: text AS reference_udd_detail_t 
		FROM
			stokobatalkes_r
			JOIN ( SELECT A.ruangan_id, A.ruangan_nama, A.instalasi_id FROM ruangan_m A WHERE A.ruangan_id = xruangan_id ) ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
			JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
			JOIN (
			SELECT A
				.obatalkes_id,
				A.obatalkes_namalain,
				A.obatalkes_kode,
				A.hargajual,
				A.satuankecil_id,
				A.satuansedang_id,
				A.satuanbesar_id,
				A.jenisobatalkes_id,
				A.harganetto,
				A.hargamaksimum,
				A.hargaminimum,
				A.hargaratarata,
				A.hargaterakhir,
				A.obatalkes_nama,
				A.nilai_ro,
				A.ven,
				A.is_active,
				A.is_deleted 
			FROM
				obatalkes_m A 
			) obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
			LEFT JOIN ( SELECT A.satuanunit_id, A.satuanunit_nama FROM satuanunit_m A ) satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
			LEFT JOIN ( SELECT A.satuanunit_id, A.satuanunit_nama FROM satuanunit_m A ) satuan_besar ON obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id
			LEFT JOIN ( SELECT A.satuanunit_id, A.satuanunit_nama FROM satuanunit_m A ) satuan_sedang ON obatalkes_m.satuansedang_id = satuan_sedang.satuanunit_id
			JOIN ( SELECT A.jenisobatalkes_id, A.jenisobatalkes_nama, A.group_jenisobat FROM jenisobatalkes_m A ) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
			LEFT JOIN (
			SELECT A
				.konfigfarmasi_id,
				A.is_deleted,
				A.persenppn,
				A.persenmargin,
				A.persen_diskon,
				A.hargaygdigunakan 
			FROM
				konfigfarmasi_k A 
			) konfigfarmasi_k ON konfigfarmasi_k.is_deleted
			IS FALSE LEFT JOIN ( SELECT A.stokobatr_id, A.obatalkes_id, A.min_stok, A.max_stok, A.is_deleted FROM konfigrak_m A ) konfigrak_m ON stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id 
			AND stokobatalkes_r.obatalkes_id = konfigrak_m.obatalkes_id 
			AND konfigrak_m.is_deleted
			IS FALSE LEFT JOIN kartustok ON kartustok.ruangan_id = stokobatalkes_r.ruangan_id 
			AND kartustok.obatalkes_id = stokobatalkes_r.obatalkes_id
			LEFT JOIN resepfarmasi ON resepfarmasi.ruangan_id = stokobatalkes_r.ruangan_id 
			AND resepfarmasi.obatalkes_id = stokobatalkes_r.obatalkes_id
			LEFT JOIN resepdokter ON resepdokter.ruangan_id = stokobatalkes_r.ruangan_id 
			AND resepdokter.obatalkes_id = stokobatalkes_r.obatalkes_id
			LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_ven ON obatalkes_m.ven = look_ven.lookup_id 
		WHERE
			obatalkes_m.is_active IS TRUE 
			AND obatalkes_m.is_deleted IS FALSE 
			AND obatalkes_m.obatalkes_id = ANY ( varray ) 
		) hit;
	
END; 
$function$
;