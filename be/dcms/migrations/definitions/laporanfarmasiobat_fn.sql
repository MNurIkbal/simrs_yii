CREATE OR REPLACE FUNCTION "public"."laporanfarmasiobat_fn"("v_date_start" date, "v_date_end" date)
  RETURNS TABLE("ruangan_id" int4, "ruangan_nama" text, "obatalkes_id" int4, "obatalkes_kode" text, "obatalkes_nama" text, "satuankecil" text, "qtystok_before" float8, "penerimaan_blud" float8, "penerimaan_apbd" float8, "penerimaan_dinkes" float8, "penerimaan_lainlain" float8, "penerimaan_rslain" float8, "persediaan" float8, "qtymutasi_keluar" float8, "adjusmenobat_masuk" float8, "djusmenobat_keluar" float8, "stokopname_masuk" float8, "stokopname_keluar" float8, "total_pengeluaran" float8, "stok_akhir" float8, "harga_satuan" float8, "weighted_avg" float8, "total_harga" float8) AS $BODY$ BEGIN
 RETURN QUERY 

SELECT 
x.ruangan_id::int4,
x.ruangan_nama::text,
x.obatalkes_id::int4,
x.obatalkes_kode::text,
x.obatalkes_nama::text,
x.satuankecil::text,
stokbefore.qtystok_before::FLOAT8,
sum(x.penerimaan_blud)::FLOAT8 as penerimaan_blud,
sum(x.penerimaan_apbd)::FLOAT8 as penerimaan_apbd,
sum(x.penerimaan_dinkes)::FLOAT8 as penerimaan_dinkes,
sum(x.penerimaan_lainlain)::FLOAT8 as penerimaan_lainlain,
sum(x.penerimaan_rslain)::FLOAT8 as penerimaan_rslain ,
(stokbefore.qtystok_before + sum(x.penerimaan_blud) + sum(x.penerimaan_apbd) + sum(x.penerimaan_dinkes) + sum(x.penerimaan_lainlain) +sum(x.penerimaan_rslain))::FLOAT8 as persediaan,
coalesce(pengeluaran.qtystok_in,0)::FLOAT8 as qtymutasi_keluar,
coalesce(adjusmenobat.qtystok_in,0)::FLOAT8 as adjusmenobat_masuk,
coalesce(adjusmenobat.qtystok_out,0)::FLOAT8 as adjusmenobat_keluar,
coalesce(stokopname.qtystok_in,0)::FLOAT8 as stokopname_masuk,
coalesce(stokopname.qtystok_out,0)::FLOAT8 as stokopname_keluar,
( coalesce(pengeluaran.qtystok_in,0) + coalesce(adjusmenobat.qtystok_out,0) + coalesce(stokopname.qtystok_out,0))::FLOAT8  as total_pengeluaran,
((stokbefore.qtystok_before + sum(x.penerimaan_blud) + sum(x.penerimaan_apbd) + sum(x.penerimaan_dinkes) + sum(x.penerimaan_lainlain) +sum(x.penerimaan_rslain)) 
+ (coalesce(adjusmenobat.qtystok_in,0) + coalesce(stokopname.qtystok_in,0)) - ( coalesce(pengeluaran.qtystok_in,0) + coalesce(adjusmenobat.qtystok_out,0) + coalesce(stokopname.qtystok_out,0)))::FLOAT8 as stok_akhir,
x.harganetto::float as harga_satuan,
lr.weighted_avg::float8 as weighted_avg,
(((stokbefore.qtystok_before + sum(x.penerimaan_blud) + sum(x.penerimaan_apbd) + sum(x.penerimaan_dinkes) + sum(x.penerimaan_lainlain) +sum(x.penerimaan_rslain)) 
+ (coalesce(adjusmenobat.qtystok_in,0) + coalesce(stokopname.qtystok_in,0)) - ( coalesce(pengeluaran.qtystok_in,0) + coalesce(adjusmenobat.qtystok_out,0) + coalesce(stokopname.qtystok_out,0))) * x.harganetto)::FLOAT8 total_harga
from (

SELECT a.ruangan_id::int4,
a.ruangan_nama::text,
a.obatalkes_id::int4,
a.obatalkes_kode::text,
a.obatalkes_nama::text,
a.satuankecil::text,
COALESCE(a.penerimaan_blud,0)::FLOAT8 as penerimaan_blud,
COALESCE(a.penerimaan_apbd,0)::FLOAT8 as penerimaan_apbd,
COALESCE(a.penerimaan_dinkes,0)::FLOAT8 as penerimaan_dinkes,
COALESCE(a.penerimaan_lainlain,0)::FLOAT8 as penerimaan_lainlain,
COALESCE(a.penerimaan_rslain,0)::FLOAT8 as penerimaan_rslain,
a.harganetto
 from (

SELECT 
stokobatalkes_r.ruangan_id,
ruangan_m.ruangan_nama,
stokobatalkes_r.obatalkes_id,
obatalkes_m.obatalkes_kode,
obatalkes_m.obatalkes_nama,
satuankecil.satuanunit_nama as satuankecil,
obatalkes_m.harganetto,
case when penerimaan.sumber_penerimaan = 2161 then penerimaan.qtystok_in
ELSE null end as penerimaan_blud ,
case when penerimaan.sumber_penerimaan = 2162 then penerimaan.qtystok_in
ELSE null end as penerimaan_apbd,
case when penerimaan.sumber_penerimaan = 2164 then penerimaan.qtystok_in
ELSE null end as penerimaan_dinkes,
case when penerimaan.sumber_penerimaan is null or penerimaan.sumber_penerimaan = 2171 then penerimaan.qtystok_in
ELSE null end as penerimaan_lainlain,
case when penerimaan.sumber_penerimaan = 2170 then penerimaan.qtystok_in
ELSE null end as penerimaan_rslain
FROM stokobatalkes_r
 JOIN (SELECT a.obatalkes_id,a.obatalkes_kode,a.obatalkes_nama,a.satuankecil_id,a.harganetto FROM obatalkes_m a WHERE a.is_deleted = false) obatalkes_m on obatalkes_m.obatalkes_id = stokobatalkes_r.obatalkes_id
 JOIN (SELECT a.ruangan_id,a.ruangan_nama,a.instalasi_id from ruangan_m a WHERE a.is_deleted = false) ruangan_m on ruangan_m.ruangan_id = stokobatalkes_r.ruangan_id
 JOIN satuanunit_m satuankecil on satuankecil.satuanunit_id = obatalkes_m.satuankecil_id

 --- penerimaan 
 LEFT JOIN (
SELECT x.obatalkes_id,x.ruangan_id,
sum(x.qtystok_in) as qtystok_in,
x.sumber_penerimaan
from (
SELECT a.obatalkes_id,a.ruangan_id,a.qtystok_in,penerimaanobat_t.sumber_penerimaan from stokobatalkes_t a 
LEFT JOIN (SELECT a.penerimaanobatdetail_id,a,penerimaanobat_id from penerimaanobatdetail_t a ) penerimaanobatdetail_t on  penerimaanobatdetail_t.penerimaanobatdetail_id = a.penerimaanobatdetail_id
LEFT JOIN (SELECT a.penerimaanobat_id,a.sumber_penerimaan from penerimaanobat_t a ) penerimaanobat_t on penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id
WHERE a.penerimaanobatdetail_id is not null 
AND a.tglstok_in :: DATE >= v_date_start :: DATE 
AND a.tglstok_in :: DATE <= v_date_end :: DATE 
union all 
SELECT a.obatalkes_id,a.ruangan_id,a.qtystok_in,penerimaansupp_t.sumber_penerimaan  from stokobatalkes_t a 
LEFT JOIN (SELECT a.penerimaansuppdetail_id,a,penerimaansupp_id from penerimaansuppdetail_t a ) penerimaansuppdetail_t on  penerimaansuppdetail_t.penerimaansuppdetail_id = a.penerimaansuppdetail_id
LEFT JOIN (SELECT a.penerimaansupp_id,a.sumber_penerimaan from penerimaansupp_t a ) penerimaansupp_t on penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id
WHERE a.penerimaansuppdetail_id is not null 
AND a.tglstok_in :: DATE >= v_date_start :: DATE 
AND a.tglstok_in :: DATE <= v_date_end :: DATE 
) x GROUP BY x.obatalkes_id,x.ruangan_id,x.sumber_penerimaan) penerimaan on penerimaan.obatalkes_id = stokobatalkes_r.obatalkes_id and penerimaan.ruangan_id = stokobatalkes_r.ruangan_id
WHERE stokobatalkes_r.ruangan_id = 25
) a 
-- WHERE a.obatalkes_nama = '1 MEDICAL GRADE GLUE'
	) x 
	
	LEFT JOIN ( 
SELECT stokobatalkes_t.ruangan_id, stokobatalkes_t.obatalkes_id, 
	SUM(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) as qtystok_before
	FROM 
	stokobatalkes_t 
	WHERE (
	CASE WHEN stokobatalkes_t.tglstok_in is not null 
	THEN stokobatalkes_t.tglstok_in 
	ELSE stokobatalkes_t.tglstok_out END
	) <= v_date_start :: date + interval '23 hours 59 minutes 59 seconds' 
	and stokobatalkes_t.is_deleted = false
	GROUP BY 
	stokobatalkes_t.ruangan_id, 
	stokobatalkes_t.obatalkes_id
	) stokbefore on stokbefore.obatalkes_id = x.obatalkes_id and x.ruangan_id = stokbefore.ruangan_id
	
	--pengeluaran 
	
	LEFT JOIN (
SELECT 
a.obatalkes_id,
ruangan_asal.ruangan_id as ruangan_asalid,
COALESCE(sum(a.qtystok_in),0) as qtystok_in
 from ruangan_m 
 LEFT JOIN (SELECT a.obatalkes_id,a.ruangan_id,a.qtystok_in, a.terimamutasidetail_id ,a.tglstok_in from stokobatalkes_t a  WHERE a.terimamutasidetail_id is not null
  AND a.tglstok_in :: DATE >= v_date_start :: DATE 
 AND a.tglstok_in :: DATE <= v_date_end :: DATE  
 ) a on ruangan_m.ruangan_id = a.ruangan_id
 LEFT JOIN (SELECT a.terimamutasiobatdetail_id,a.terimamutasiobat_id,a.mutasiobatdetail_id from terimamutasiobatdetail_t a) terimamutasiobatdetail_t on terimamutasiobatdetail_t.terimamutasiobatdetail_id = a.terimamutasidetail_id 
 LEFT JOIN (SELECT a.mutasiobatdetail_id,a.mutasiobatruangan_id from mutasiobatdetail_t a) mutasiobatdetail_t on mutasiobatdetail_t.mutasiobatdetail_id = terimamutasiobatdetail_t.mutasiobatdetail_id 
 LEFT JOIN (SELECT a.mutasiobatruangan_id,a.ruanganasal_id,a.ruangantujuan_id from mutasiobatruangan_t a) mutasiobatruangan_t on mutasiobatruangan_t.mutasiobatruangan_id = mutasiobatdetail_t.mutasiobatruangan_id 
LEFT JOIN  (SELECT a.ruangan_id,a.ruangan_nama,a.instalasi_id from ruangan_m a WHERE a.is_deleted = false) ruangan_asal on ruangan_asal.ruangan_id = mutasiobatruangan_t.ruanganasal_id
LEFT JOIN  (SELECT a.instalasi_id,a.instalasi_nama from instalasi_m a WHERE a.is_deleted = false) instalasi_tujuan on instalasi_tujuan.instalasi_id = ruangan_m.instalasi_id
WHERE ruangan_asal.ruangan_id  = 25
GROUP BY
a.obatalkes_id,
ruangan_asal.ruangan_id
) pengeluaran on pengeluaran.obatalkes_id = x.obatalkes_id and pengeluaran.ruangan_asalid = x.ruangan_id
	
	---adjusmen 
LEFT JOIN (
SELECT x.obatalkes_id,
x.ruangan_id,
sum(x.qtystok_in) as qtystok_in,
sum(x.qtystok_out) as qtystok_out
FROM (
SELECT stokobatalkes_t.obatalkes_id,
stokobatalkes_t.ruangan_id,
sum(stokobatalkes_t.qtystok_in) as qtystok_in,
0 as qtystok_out
FROM stokobatalkes_t WHERE stokobatalkes_t.ruangan_id = 25 and adjusmenobatmasuk_id is not null
AND stokobatalkes_t.tglstok_in :: DATE >= v_date_start :: DATE 
AND stokobatalkes_t.tglstok_in :: DATE <= v_date_end :: DATE 
GROUP BY stokobatalkes_t.obatalkes_id,
stokobatalkes_t.ruangan_id
UNION ALL 
 SELECT stokobatalkes_t.obatalkes_id,
stokobatalkes_t.ruangan_id,
0 as qtystok_in,
sum(stokobatalkes_t.qtystok_out) as qtystok_out
FROM stokobatalkes_t WHERE stokobatalkes_t.ruangan_id = 25 and adjusmenobatkeluar_id is not null
AND stokobatalkes_t.tglstok_out :: DATE >= v_date_start :: DATE 
AND stokobatalkes_t.tglstok_out :: DATE <= v_date_end :: DATE 
GROUP BY stokobatalkes_t.obatalkes_id,
stokobatalkes_t.ruangan_id) x GROUP BY x.obatalkes_id,
x.ruangan_id
 )adjusmenobat on adjusmenobat.obatalkes_id = x.obatalkes_id and x.ruangan_id = adjusmenobat.ruangan_id
	
	--STOKOPNAME
LEFT JOIN (
SELECT x.obatalkes_id,
x.ruangan_id,
sum(x.qtystok_in) as qtystok_in,
sum(x.qtystok_out) as qtystok_out
FROM (
SELECT stokobatalkes_t.obatalkes_id,
stokobatalkes_t.ruangan_id,
sum(stokobatalkes_t.qtystok_in) as qtystok_in,
sum(stokobatalkes_t.qtystok_out) as qtystok_out
FROM stokobatalkes_t WHERE stokobatalkes_t.ruangan_id = 25 and stokopnamedetail_id is not null
AND ((stokobatalkes_t.tglstok_in :: DATE >= v_date_start :: DATE AND stokobatalkes_t.tglstok_in :: DATE <= v_date_end :: DATE) or (stokobatalkes_t.tglstok_out :: DATE >= v_date_start :: DATE AND stokobatalkes_t.tglstok_out :: DATE <= v_date_end :: DATE))
GROUP BY stokobatalkes_t.obatalkes_id,
stokobatalkes_t.ruangan_id
) x GROUP BY x.obatalkes_id,
x.ruangan_id
 )stokopname on stokopname.obatalkes_id = x.obatalkes_id and stokopname.ruangan_id = x.ruangan_id
	
		----- START weighted_avg ---
   LEFt JOIN (
							select 
				            stok.obatalkes_id, 
				            stok.ruangan_id, 
				            coalesce(logstok.weighted_avg, om.harganetto,0) as weighted_avg 
				        FROM (
				            SELECT 
				                stokobatalkes_t.ruangan_id, 
				                stokobatalkes_t.obatalkes_id, 
				                max(stokobatalkes_id) as id_stok, 
				                SUM(qtystok_in - qtystok_out) as fisik 
				            FROM 
				              stokobatalkes_t 
				            WHERE (
				                CASE WHEN stokobatalkes_t.tglstok_in is not null 
				                THEN stokobatalkes_t.tglstok_in 
				                ELSE stokobatalkes_t.tglstok_out END
				            ) <= v_date_end :: date + interval '23 hours 59 minutes 59 seconds' 
				            GROUP BY 
				              stokobatalkes_t.ruangan_id, 
				              stokobatalkes_t.obatalkes_id
				        ) stok 
    
				        left join (
				            select 
				                o.obatalkes_id, 
				                o.obatalkes_kode, 
				                o.obatalkes_nama, 
				                o.harganetto 
				            from 
				                obatalkes_m o
				        ) om on om.obatalkes_id = stok.obatalkes_id 
          
				        left join (
				            SELECT 
				                l1.logasetobat_id, 
				                l1.weighted_avg, 
				                l1.obatalkes_id, 
				                l1.ruangan_id, 
				                l1.stokobatalkes_id 
				            from 
				                logasetobat_r l1
				        ) logstok on logstok.stokobatalkes_id = stok.id_stok
								where stok.ruangan_id = 	
								( SELECT lookuptransaksi_m.kode_id
				                   FROM lookuptransaksi_m
				                  WHERE lookuptransaksi_m.kode_transaksi::text = 'gudang_farmasi'::text)::integer
   
					
	
						) lr on lr.obatalkes_id = x.obatalkes_id 
				 	-----End  weighted_avg ---
	
	GROUP BY 
x.ruangan_id::int4,
x.ruangan_nama::text,
x.obatalkes_id::int4,
x.obatalkes_kode::text,
x.obatalkes_nama::text,
x.satuankecil::text,
stokbefore.qtystok_before::FLOAT8,
coalesce(pengeluaran.qtystok_in,0)::FLOAT8,
coalesce(adjusmenobat.qtystok_in,0)::FLOAT8 ,
coalesce(adjusmenobat.qtystok_out,0)::FLOAT8,
coalesce(stokopname.qtystok_in,0)::FLOAT8 ,
coalesce(stokopname.qtystok_out,0)::FLOAT8 ,
x.harganetto::float,
lr.weighted_avg::float8 ;
 
	END; 
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;