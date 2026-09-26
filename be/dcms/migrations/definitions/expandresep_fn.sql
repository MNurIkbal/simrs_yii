-- DROP FUNCTION public.expandresep_fn();

CREATE OR REPLACE FUNCTION public.expandresep_fn(x_noresep text)
	RETURNS TABLE(
		det_transaksi float,
		qty_transaksi float,
		nama_racikan character varying,
		qty_racikan float,
		is_kronis boolean,
		satuan_racikan_nama character varying,
		rke int,
		obatalkes_nama character varying,
		signa_nama character varying,
		hargajual_satuan float,
		satuan_input character varying,
		etiket character varying
	)
	LANGUAGE plpgsql
    STABLE
AS $function$
	BEGIN
		RETURN QUERY
		WITH reseptur AS (
			SELECT
				resepturdetail_t.det_medis AS det_transaksi,
				coalesce(resepturdetail_t.qty_medis, resepturdetail_t.qty_reseptur) AS qty_transaksi,
				resepturdetail_t.nama_racikan,
				resepturdetail_t.qty_racikan,
				resepturdetail_t.is_kronis,
				satuanunit_m.satuanunit_nama AS satuan_racikan_nama,
				resepturdetail_t.rke,
				obatalkes_m.obatalkes_nama,
				signaobat_m.signa_nama,
				resepturdetail_t.hargasatuan_reseptur AS hargajual_satuan,
				(resepturdetail_t.additional_data::json ->> 'satuan_input'::text)::character varying AS satuan_input,
				resepturdetail_t.etiket,
				resepturdetail_t.racikan_id
			from resepturdetail_t
			left join satuanunit_m on resepturdetail_t.satuan_racikan_id = satuanunit_m.satuanunit_id
			left join obatalkes_m on resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
			left join signaobat_m on resepturdetail_t.signa_id = signaobat_m.signa_id
			where resepturdetail_t.reseptur_id = (select reseptur_id from reseptur_t where reseptur_t.noresep = x_noresep)
			and resepturdetail_t.is_deleted is false
		), penjualanresep as (
			select
				coalesce(obatalkespasien_t.det_medis, obatalkespasien_t.det) as det_transaksi,
				coalesce(obatalkespasien_t.qty_medis, obatalkespasien_t.qty_oa) as qty_transaksi,
				obatalkespasien_t.nama_racikan,
			    obatalkespasien_t.qty_racikan,
			    obatalkespasien_t.is_kronis,
			    satuanunit_m.satuanunit_nama as satuan_racikan_nama,
			    obatalkespasien_t.rke,
			    obatalkes_m.obatalkes_nama,
			    CASE
			        WHEN obatalkespasien_t.signa IS NOT NULL THEN obatalkespasien_t.signa ->> 'text'::text
			        ELSE signaobat_m.signa_nama::text
			    END AS signa_nama,
			    obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
			    (obatalkespasien_t.additional_data::json ->> 'satuan_input'::text)::character varying AS satuan_input,
			    obatalkespasien_t.etiket,
				obatalkespasien_t.racikan_id
			from obatalkespasien_t
			left join satuanunit_m on obatalkespasien_t.satuan_racikan_id = satuanunit_m.satuanunit_id
			left join obatalkes_m on obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
			left join signaobat_m on obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
			where obatalkespasien_t.penjualanresep_id = (select penjualanresep_id from penjualanresep_t where penjualanresep_t.noresep = x_noresep)
			and obatalkespasien_t.is_deleted is false
		)
		select
			expand_resep.det_transaksi::float,
			expand_resep.qty_transaksi::float,
			expand_resep.nama_racikan::character varying,
			expand_resep.qty_racikan::float,
			expand_resep.is_kronis::boolean,
			expand_resep.satuan_racikan_nama::character varying,
			expand_resep.rke::int,
			expand_resep.obatalkes_nama::character varying,
			expand_resep.signa_nama::character varying,
			expand_resep.hargajual_satuan::float,
			expand_resep.satuan_input::character varying,
			expand_resep.etiket::character varying
		from (
			select * from reseptur
			union all
			select * from penjualanresep
		) expand_resep
		order by expand_resep.racikan_id ASC, expand_resep.rke ASC;

	END;
$function$
;