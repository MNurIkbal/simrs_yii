<?php

use yii\db\Migration;

/**
 * Class m220627_091248_migrate_mhg_2002_historypemakaianobat_fn
 */
class m220627_091248_migrate_mhg_2002_historypemakaianobat_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
			CREATE OR REPLACE FUNCTION \"public\".\"historypemakaianobat_fn\"(\"v_jumlah_date\" text, \"v_obat\" int4=0)
  RETURNS TABLE(\"stokobatalkes_id\" int4, \"tanggal_transaksi\" timestamp, \"ruangan_id\" int4, \"ruangan_nama\" varchar, \"obatalkes_id\" int4, \"obatalkes_kode\" varchar, \"tglkadaluarsa\" date, \"no_transaksi\" varchar, \"obatalkes_nama\" varchar, \"satuanunit_nama\" varchar, \"keterangan\" text, \"reference\" varchar, \"qtystok_out\" float8) AS \$BODY\$ BEGIN
		RETURN QUERY
select ks.* from  (

							
								SELECT 
										detail.stokobatalkes_id,
                    detail.obatalkes_id,
										detail.ruangan_id,
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
												stokobatalkes_t.ruangan_id,
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
                    join (SELECT a.obatalkespasien_id,a.penjualanresep_id,a.is_deleted from obatalkespasien_t a) obatalkespasien_t 
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
												stokobatalkes_t.ruangan_id,
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
                                pemakaianobatdetail_t.pemakaianobatdetail_id,
                                pemakaianobat_t.ruangan_id
                            from pemakaianobatdetail_t
                            join (SELECT a.ruangan_id,a.pemakaianobat_id,a.is_deleted from pemakaianobat_t a) pemakaianobat_t
                                on pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
                            where pemakaianobat_t.is_deleted = false
                            and pemakaianobatdetail_t.is_deleted = false
                        ) pemakaianobatdetail_t on stokobatalkes_t.pemakaianobatdetail_id = pemakaianobatdetail_t.pemakaianobatdetail_id -- and vpemakaianruangan
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
												stokobatalkes_t.ruangan_id,
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
                            join (SELECT a.instruksitindakanbmhp_id,a.ruangan_id,a.is_ditagihkan,a.is_deleted from instruksitindakanbmhp_t a)instruksitindakanbmhp_t on obatalkespasien_t.instruksitindakanbmhp_id = instruksitindakanbmhp_t.instruksitindakanbmhp_id
                            where instruksitindakanbmhp_t.is_ditagihkan = true
                            and instruksitindakanbmhp_t.is_deleted = false
                        ) obatalkespasien_t on stokobatalkes_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id --and vbmhp
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
												stokobatalkes_t.ruangan_id,
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
                            join (SELECT a.mutasiobatruangan_id,a.ruanganasal_id,a.ruangantujuan_id,a.is_deleted from mutasiobatruangan_t a) mutasiobatruangan_t on mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
                            where mutasiobatruangan_t.ruanganasal_id = (select kode_id from lookuptransaksi_m where kode_transaksi = 'gudang_farmasi')
                            and mutasiobatruangan_t.ruangantujuan_id not in (select rm.ruangan_id from ruangan_m rm where rm.instalasi_id = (select lm.kode_id from lookuptransaksi_m lm where lm.kode_transaksi = 'FARMASI'))
                            and mutasiobatruangan_t.is_deleted = false
                            and mutasiobatdetail_t.is_deleted = false
                        )mutasiobatdetail_t on stokobatalkes_t.mutasiobatdetail_id = mutasiobatdetail_t.mutasiobatdetail_id --and vmutasi
                    where stokobatalkes_t.tglstok_out is not null
                    AND (
                        stokobatalkes_t.tglstok_out >= current_date::timestamp without time zone AND stokobatalkes_t.tglstok_out <= (current_date - '30 days'::interval) 
                        OR 
                        stokobatalkes_t.tglstok_out >= (current_date - '30 days'::interval) AND stokobatalkes_t.tglstok_out <= current_date::timestamp without time zone
                    )
                ) detail
                GROUP BY detail.obatalkes_id, detail.tanggal,	detail.ruangan_id,	detail.stokobatalkes_id
                ORDER BY detail.obatalkes_id, detail.tanggal,	detail.ruangan_id,	detail.stokobatalkes_id
          ) bs
					
					 JOIN (SELECT 
kartu_stok.stokobatalkes_id,
kartu_stok.tanggal_transaksi,
CASE
    WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN kartu_stok.ruangan_tujuan_id
    WHEN kartu_stok.keterangan = 'Mutasi Obat'::text THEN kartu_stok.ruangan_asal_id
    ELSE kartu_stok.ruangan_asal_id
end as ruangan_id,
ruangan_asal.ruangan_nama AS ruangan_nama,
kartu_stok.obatalkes_id as obatalkes_id, 
obatalkes_m.obatalkes_kode,
kartu_stok.tglkadaluarsa,
kartu_stok.no_transaksi,
obatalkes_m.obatalkes_nama,
satuanunit_m.satuanunit_nama,
kartu_stok.keterangan,
CASE
    WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN ruangan_asal.ruangan_nama
	WHEN kartu_stok.keterangan = 'Mutasi Obat'::text THEN ruangan_tujuan.ruangan_nama
    ELSE kartu_stok.reference
END AS reference,
kartu_stok.qtystok_out

from 
(
	SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
                CASE
                    WHEN penjualan_resep.penjualanresep_id IS NULL AND stokobatalkes_t.tglstok_in IS not NULL THEN 'BATAL BMHP'::text
                    WHEN penjualan_resep.penjualanresep_id IS NULL THEN 'BMHP'::text
                    WHEN penjualan_resep.penjualanresep_id IS NOT NULL THEN 'Penjualan Resep'::text
                    ELSE NULL::text
                END AS keterangan,
            penjualan_resep.no_transaksi,
                ( case when tglstok_in is null then tglstok_out else tglstok_in end) as tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) as qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            penjualan_resep.reference,
            stokobatalkes_t.ruangan_id,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT obatalkespasien_t.obatalkespasien_id,
                    obatalkespasien_t.penjualanresep_id,
                        CASE
                            WHEN penjualanresep_t.noresep IS NOT NULL THEN penjualanresep_t.noresep
                            ELSE pendaftaran_t.no_pendaftaran
                        END AS no_transaksi,
                        CASE
                            WHEN obatalkespasien_t.penjualanresep_id IS NULL THEN pasien_pendaftaran.nama_pasien
                            WHEN penjualanresep_t.pasien_id IS NOT NULL THEN pasien_m.nama_pasien
                            ELSE penjualanresep_t.nama_pembeli
                        END AS reference
                   FROM obatalkespasien_t
                     LEFT JOIN (SELECT a.noresep,a.pasien_id,a.nama_pembeli,a.penjualanresep_id from penjualanresep_t a) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                     LEFT JOIN (SELECT a.pasien_id,a.nama_pasien from pasien_m a ) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
                     LEFT JOIN (SELECT a.no_pendaftaran,a.pasien_id,a.pendaftaran_id from pendaftaran_t a) pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LEFT JOIN (SELECT a.pasien_id,a.nama_pasien from pasien_m a) pasien_pendaftaran ON pendaftaran_t.pasien_id = pasien_pendaftaran.pasien_id
                  WHERE obatalkespasien_t.udd_detail_id IS NULL) penjualan_resep ON stokobatalkes_t.obatalkespasien_id = penjualan_resep.obatalkespasien_id
          WHERE stokobatalkes_t.is_deleted = false AND stokobatalkes_t.returresepdetail_id IS NULL AND stokobatalkes_t.pembatalanresep_id IS NULL
          GROUP BY (
                CASE
                    WHEN penjualan_resep.penjualanresep_id IS NULL AND stokobatalkes_t.tglstok_in is NOT NULL THEN 'BATAL BMHP'::text
                    WHEN penjualan_resep.penjualanresep_id IS NULL THEN 'BMHP'::text
                    WHEN penjualan_resep.penjualanresep_id IS NOT NULL THEN 'Penjualan Resep'::text
                    ELSE NULL::text
                END), penjualan_resep.no_transaksi, ( case when tglstok_in is null then tglstok_out else tglstok_in end), stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.qtystok_in, penjualan_resep.reference, stokobatalkes_t.ruangan_id
	union ALL
	SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
            'Pembatalan Resep'::text AS keterangan,
            pembatalan_resep.no_pembatalan AS no_transaksi,
            ( case when tglstok_in is null then tglstok_out else tglstok_in end) AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            pembatalan_resep.reference,
            stokobatalkes_t.ruangan_id,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT pembatalanresep_t.pembatalanresep_id,
                    pembatalanresep_t.tgl_pembatalan,
                    pembatalanresep_t.no_pembatalan,
                        CASE
                            WHEN penjualanresep_t.pasien_id IS NOT NULL THEN pasien_m.nama_pasien
                            ELSE penjualanresep_t.nama_pembeli
                        END AS reference
                   FROM pembatalanresep_t
                     JOIN (SELECT a.pasien_id,a.nama_pembeli,a.penjualanresep_id from penjualanresep_t a)penjualanresep_t ON pembatalanresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                     LEFT JOIN (SELECT a.pasien_id,a.nama_pasien from pasien_m a )pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id) pembatalan_resep ON pembatalan_resep.pembatalanresep_id = stokobatalkes_t.pembatalanresep_id
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY pembatalan_resep.no_pembatalan, ( case when tglstok_in is null then tglstok_out else tglstok_in end), stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, pembatalan_resep.reference, stokobatalkes_t.ruangan_id
	union all 
	SELECT max(stokobatalkes_t.stokobatalkes_id),
            'Retur Resep'::text AS keterangan,
            retur_resep.no_returresep AS no_transaksi,
            stokobatalkes_t.tglstok_in as tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in),
            sum(stokobatalkes_t.qtystok_out),
            retur_resep.reference,
            stokobatalkes_t.ruangan_id,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT returresepdetail_t.returresepdetail_id,
                    returresep_t.no_returresep,
                    returresep_t.tgl_retur,
                        CASE
                            WHEN penjualanresep_t.pasien_id IS NOT NULL THEN pasien_m.nama_pasien
                            ELSE penjualanresep_t.nama_pembeli
                        END AS reference
                   FROM returresepdetail_t
                     JOIN (SELECT a.returresep_id,a.no_returresep,a.tgl_retur,a.penjualanresep_id from returresep_t a)returresep_t ON returresepdetail_t.returresep_id = returresep_t.returresep_id
                     JOIN (SELECT a.pasien_id,a.nama_pembeli,a.penjualanresep_id from penjualanresep_t a) penjualanresep_t ON returresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                     LEFT JOIN (SELECT a.pasien_id,a.nama_pasien from pasien_m a) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id) retur_resep ON stokobatalkes_t.returresepdetail_id = retur_resep.returresepdetail_id
          WHERE stokobatalkes_t.is_deleted = false
          group by retur_resep.no_returresep,stokobatalkes_t.tglstok_in,stokobatalkes_t.obatalkes_id ,stokobatalkes_t.satuankecil_id ,stokobatalkes_t.tglkadaluarsa,retur_resep.reference,stokobatalkes_t.ruangan_id 
	union all 
          SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
            'Adjusmen Masuk'::text AS keterangan,
            adjusmen_masuk.no_adjusmen AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            adjusmen_masuk.keterangan::character varying AS reference,
            stokobatalkes_t.ruangan_id,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT adjusmenobatmasuk_t.adjusmenobatmasuk_id,
                    adjusmenobat_t.no_adjusmen,
                    adjusmenobat_t.tgl_adjusmen,
                    adjusmenobatmasuk_t.keterangan
                   FROM adjusmenobatmasuk_t
                     JOIN (SELECT a.no_adjusmen,a.tgl_adjusmen,a.adjusmenobat_id from adjusmenobat_t a )adjusmenobat_t ON adjusmenobatmasuk_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id) adjusmen_masuk ON stokobatalkes_t.adjusmenobatmasuk_id = adjusmen_masuk.adjusmenobatmasuk_id
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY adjusmen_masuk.no_adjusmen, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id,adjusmen_masuk.keterangan
        UNION ALL
         SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
            'Adjusmen Keluar'::text AS keterangan,
            adjusmen_keluar.no_adjusmen AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT adjusmenobatkeluar_t.adjusmenobatkeluar_id,
                    adjusmenobat_t.no_adjusmen,
                    adjusmenobat_t.tgl_adjusmen
                   FROM adjusmenobatkeluar_t
                     JOIN (SELECT a.no_adjusmen,a.tgl_adjusmen,a.adjusmenobat_id from adjusmenobat_t a)adjusmenobat_t ON adjusmenobatkeluar_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id) adjusmen_keluar ON stokobatalkes_t.adjusmenobatkeluar_id = adjusmen_keluar.adjusmenobatkeluar_id
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY adjusmen_keluar.no_adjusmen, stokobatalkes_t.tglstok_out, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id
	union all 

SELECT MAX
	( stokobatalkes_t.stokobatalkes_id ) AS stokobatalkes_id,
-- 	'Penerimaan Alternatif' :: TEXT AS keterangan,
case
		WHEN penerimaan_alternatif.is_consigment = TRUE AND penerimaan_alternatif.is_donasi = FALSE THEN 'Penerimaan Consigment'::TEXT
		WHEN (penerimaan_alternatif.is_consigment = FALSE OR penerimaan_alternatif.is_consigment is null) AND penerimaan_alternatif.is_donasi = FALSE  THEN 'Penerimaan Manual'::TEXT
		WHEN (penerimaan_alternatif.is_consigment = FALSE or penerimaan_alternatif.is_consigment is null) AND penerimaan_alternatif.is_donasi = TRUE THEN 'Penerimaan Donasi'::TEXT
		END as keterangan,
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
	stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
-- 	penerimaan_alternatif.is_consigment,
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
		JOIN (SELECT a.penerimaansuppdetail_id,a.penerimaansupp_id from penerimaansuppdetail_t a) penerimaansuppdetail_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id 
	) penerimaan_alternatif ON stokobatalkes_t.penerimaansuppdetail_id = penerimaan_alternatif.penerimaansuppdetail_id 
WHERE
	stokobatalkes_t.is_deleted = FALSE 
GROUP BY
-- 	'Penerimaan Alternatif' :: TEXT,

	penerimaan_alternatif.no_penerimaan,
	stokobatalkes_t.tglstok_in,
	stokobatalkes_t.obatalkes_id,
	stokobatalkes_t.satuankecil_id,
	stokobatalkes_t.tglkadaluarsa,
	'-' :: CHARACTER VARYING,
	stokobatalkes_t.ruangan_id,
	penerimaan_alternatif.is_consigment,
	penerimaan_alternatif.is_donasi
      
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Pemakaian Ruangan'::text AS keterangan,
            pemakaian_ruangan.nopemakaian_obat AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT pemakaianobatdetail_t.pemakaianobatdetail_id,
                    pemakaianobat_t.nopemakaian_obat,
                    pemakaianobat_t.tglpemakaianobat
                   FROM pemakaianobat_t
                     JOIN (SELECT a.pemakaianobatdetail_id,a.pemakaianobat_id FROM pemakaianobatdetail_t a )pemakaianobatdetail_t ON pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id) pemakaian_ruangan ON stokobatalkes_t.pemakaianobatdetail_id = pemakaian_ruangan.pemakaianobatdetail_id
          WHERE stokobatalkes_t.is_deleted = false
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Pemusnahan Obat'::text AS keterangan,
            pemusnahan_obat.nopemusnahan AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT pemusnahanobatdetail_t.pemusnahanobatdetail_id,
                    pemusnahanobat_t.nopemusnahan,
                    pemusnahanobat_t.tglpemusnahan
                   FROM pemusnahanobat_t
                     JOIN (SELECT a.pemusnahanobatdetail_id,a.pemusnahanobat_id from pemusnahanobatdetail_t a )pemusnahanobatdetail_t ON pemusnahanobat_t.pemusnahanobat_id = pemusnahanobatdetail_t.pemusnahanobat_id) pemusnahan_obat ON stokobatalkes_t.pemusnahanobatdetail_id = pemusnahan_obat.pemusnahanobatdetail_id
          WHERE stokobatalkes_t.is_deleted = false
        UNION ALL
         SELECT max(stokobatalkes_t.stokobatalkes_id),
            'Stok Opname'::text AS keterangan,
            stok_opname.nostokopname AS no_transaksi,
            ( case when tglstok_in is null then tglstok_out else tglstok_in end) AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in),
            sum(stokobatalkes_t.qtystok_out),
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT stokopnamedetail_t.stokopnamedetail_id,
                    stokopname_t.nostokopname,
                    stokopname_t.tglstokopname
                   FROM stokopname_t
                     JOIN (SELECT a.stokopnamedetail_id, a.stokopname_id from stokopnamedetail_t a)stokopnamedetail_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id) stok_opname ON stokobatalkes_t.stokopnamedetail_id = stok_opname.stokopnamedetail_id
          WHERE stokobatalkes_t.is_deleted = false
          group by stok_opname.nostokopname,( case when tglstok_in is null then tglstok_out else tglstok_in end),stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Penerimaan Supplier'::text AS keterangan,
            penerimaan_supp.no_penerimaan AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT penerimaanobatdetail_t.penerimaanobatdetail_id,
                    penerimaanobat_t.no_penerimaan,
                    penerimaanobat_t.tgl_penerimaan
                   FROM penerimaanobat_t
                     JOIN (SELECT a.penerimaanobat_id,a.penerimaanobatdetail_id from penerimaanobatdetail_t a) penerimaanobatdetail_t ON penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id) penerimaan_supp ON stokobatalkes_t.penerimaanobatdetail_id = penerimaan_supp.penerimaanobatdetail_id
          WHERE stokobatalkes_t.is_deleted = false
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Retur Penerimaan Supplier'::text AS keterangan,
            retur_penerimaan.no_returpenerimaanobat AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT returpenerimaanobatdetail_t.returpenerimaanobatdetail_id,
                    returpenerimaanobat_t.no_returpenerimaanobat,
                    returpenerimaanobat_t.tgl_retur
                   FROM returpenerimaanobat_t
                     JOIN (SELECT a.returpenerimaanobatdetail_id,a.returpenerimaanobat_id from returpenerimaanobatdetail_t a  )returpenerimaanobatdetail_t ON returpenerimaanobatdetail_t.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id) retur_penerimaan ON stokobatalkes_t.returpenerimaanobatdetail_id = retur_penerimaan.returpenerimaanobatdetail_id
          WHERE stokobatalkes_t.is_deleted = false
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Penerimaan Mutasi'::text AS keterangan,
            terima_mutasi.noterimamutasi AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            '-'::character varying AS reference,
            terima_mutasi.ruanganasal_id as ruangan_id,
            terima_mutasi.ruanganasal_id AS ruangan_asal_id,
            terima_mutasi.ruanganpenerima_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT terimamutasiobatdetail_t.terimamutasiobatdetail_id,
                    terimamutasiobat_t.noterimamutasi,
                    terimamutasiobat_t.tglterima,
                    terimamutasiobat_t.ruanganpenerima_id,
                    terimamutasiobat_t.ruanganasal_id
                   FROM terimamutasiobat_t
                     JOIN (SELECT a.terimamutasiobatdetail_id,a.terimamutasiobat_id from terimamutasiobatdetail_t a )terimamutasiobatdetail_t ON terimamutasiobat_t.terimamutasiobat_id = terimamutasiobatdetail_t.terimamutasiobat_id) terima_mutasi ON stokobatalkes_t.terimamutasidetail_id = terima_mutasi.terimamutasiobatdetail_id
          WHERE stokobatalkes_t.is_deleted = false
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Mutasi Obat'::text AS keterangan,
            mutasi_obat.nomutasioa AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            '-'::character varying AS reference,
            mutasi_obat.ruanganasal_id as ruangan_id,
            mutasi_obat.ruanganasal_id AS ruangan_asal_id,
            mutasi_obat.ruangantujuan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT mutasiobatdetail_t.mutasiobatdetail_id,
                    mutasiobatruangan_t.nomutasioa,
                    mutasiobatruangan_t.tglmutasioa,
                    mutasiobatruangan_t.ruanganasal_id,
                    mutasiobatruangan_t.ruangantujuan_id
                   FROM mutasiobatruangan_t
                     JOIN (SELECT a.mutasiobatdetail_id,a.mutasiobatruangan_id from mutasiobatdetail_t a) mutasiobatdetail_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id) mutasi_obat ON stokobatalkes_t.mutasiobatdetail_id = mutasi_obat.mutasiobatdetail_id
          WHERE stokobatalkes_t.is_deleted = false
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'UDD'::text AS keterangan,
            udd.no_udd AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            '-'::character varying AS reference,
            udd.ruanganproses_id as ruangan_id,
            udd.ruanganproses_id AS ruangan_asal_id,
            udd.ruanganproses_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT obatalkespasien_t.obatalkespasien_id,
                    obatalkespasien_t.udd_detail_id,
                    udd_t.no_udd,
                    udd_t.ruanganproses_id
                   FROM obatalkespasien_t
                     JOIN (SELECT a.udd_detail_id ,a.udd_id from udd_detail_t a ) udd_detail_t ON obatalkespasien_t.udd_detail_id = udd_detail_t.udd_detail_id
                     JOIN (SELECT a.no_udd,a.ruanganproses_id,a.udd_id from  udd_t a )udd_t ON udd_detail_t.udd_id = udd_t.udd_id) udd ON stokobatalkes_t.obatalkespasien_id = udd.obatalkespasien_id
          WHERE stokobatalkes_t.is_deleted = false
) kartu_stok
JOIN (SELECT a.obatalkes_id,a.obatalkes_kode,a.obatalkes_nama FROM obatalkes_m a)obatalkes_m ON kartu_stok.obatalkes_id = obatalkes_m.obatalkes_id
LEFT JOIN (SELECT a.satuanunit_id,a.satuanunit_nama from satuanunit_m a) satuanunit_m ON kartu_stok.satuanunit_id = satuanunit_m.satuanunit_id
LEFT JOIN (SELECT r1.ruangan_id,r1.ruangan_nama from ruangan_m r1) ruangan_asal ON kartu_stok.ruangan_asal_id = ruangan_asal.ruangan_id
LEFT JOIN (SELECT r2.ruangan_id,r2.ruangan_nama from ruangan_m r2) ruangan_tujuan ON kartu_stok.ruangan_tujuan_id = ruangan_tujuan.ruangan_id
order by (CASE
            WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN kartu_stok.ruangan_tujuan_id
            WHEN kartu_stok.keterangan = 'Mutasi Obat'::text THEN kartu_stok.ruangan_asal_id
            ELSE kartu_stok.ruangan_asal_id
        end),kartu_stok.obatalkes_id ,kartu_stok.tanggal_transaksi) ks
				
				on bs.stokobatalkes_id = ks.stokobatalkes_id
WHERE
ks.obatalkes_id = v_obat::INTEGER and 
 ks.tanggal_transaksi >= (current_date - v_jumlah_date::interval) ;
 END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;               ");
        
        $this->execute('ALTER FUNCTION "public"."historypemakaianobat_fn"("v_jumlah_date" text, "v_obat" int4) OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220627_091248_migrate_mhg_2002_historypemakaianobat_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220627_091248_migrate_mhg_2002_historypemakaianobat_fn cannot be reverted.\n";

        return false;
    }
    */
}
