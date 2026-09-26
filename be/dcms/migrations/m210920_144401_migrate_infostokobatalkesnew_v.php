<?php

use yii\db\Migration;

/**
 * Class m210920_144401_migrate_infostokobatalkesnew_v
 */
class m210920_144401_migrate_infostokobatalkesnew_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infokartustokobatnew_v;');
        
        $this->execute("
            CREATE VIEW \"public\".\"infokartustokobatnew_v\" AS  SELECT kartu_stok.stokobatalkes_id,
    kartu_stok.obatalkes_id,
    kartu_stok.tanggal_transaksi,
    kartu_stok.no_transaksi,
    obatalkes_m.obatalkes_nama,
    kartu_stok.qtystok_in,
    kartu_stok.qtystok_out,
    kartu_stok.stok_tersedia AS stok,
    satuanunit_m.satuanunit_nama,
    kartu_stok.tglkadaluarsa,
    kartu_stok.keterangan,
    kartu_stok.ruangan_asal_id,
    kartu_stok.ruangan_tujuan_id,
    ruangan_asal.ruangan_nama AS ruangan_asal_nama,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan_nama,
        CASE
            WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN ruangan_asal.ruangan_nama
            WHEN kartu_stok.keterangan = 'Mutasi Obat'::text THEN ruangan_tujuan.ruangan_nama
            ELSE kartu_stok.reference
        END AS reference,
    kartu_stok.stok_tersedia,
        CASE
            WHEN kartu_stok.keterangan = 'Penerimaan Mutasi'::text THEN kartu_stok.ruangan_tujuan_id
            WHEN kartu_stok.keterangan = 'Mutasi Obat'::text THEN kartu_stok.ruangan_asal_id
            ELSE kartu_stok.ruangan_asal_id
        END AS ruangan_id,
    obatalkes_m.obatalkes_kode
   FROM ( SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
                CASE
                    WHEN penjualan_resep.penjualanresep_id IS NULL AND stokobatalkes_t.tglstok_out IS NULL THEN 'BATAL BMHP'::text
                    WHEN penjualan_resep.penjualanresep_id IS NULL THEN 'BMHP'::text
                    WHEN penjualan_resep.penjualanresep_id IS NOT NULL THEN 'Penjualan Resep'::text
                    ELSE NULL::text
                END AS keterangan,
            penjualan_resep.no_transaksi,
                CASE
                    WHEN stokobatalkes_t.tglstok_out IS NULL THEN stokobatalkes_t.tglstok_in
                    ELSE stokobatalkes_t.tglstok_out
                END AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            min(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            penjualan_resep.reference,
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
                     LEFT JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
                     LEFT JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LEFT JOIN pasien_m pasien_pendaftaran ON pendaftaran_t.pasien_id = pasien_pendaftaran.pasien_id
                  WHERE obatalkespasien_t.udd_detail_id IS NULL) penjualan_resep ON stokobatalkes_t.obatalkespasien_id = penjualan_resep.obatalkespasien_id
          WHERE stokobatalkes_t.is_deleted = false AND stokobatalkes_t.returresepdetail_id IS NULL AND stokobatalkes_t.pembatalanresep_id IS NULL
          GROUP BY (
                CASE
                    WHEN penjualan_resep.penjualanresep_id IS NULL AND stokobatalkes_t.tglstok_out IS NULL THEN 'BATAL BMHP'::text
                    WHEN penjualan_resep.penjualanresep_id IS NULL THEN 'BMHP'::text
                    WHEN penjualan_resep.penjualanresep_id IS NOT NULL THEN 'Penjualan Resep'::text
                    ELSE NULL::text
                END), penjualan_resep.no_transaksi, stokobatalkes_t.tglstok_out, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.qtystok_in, penjualan_resep.reference, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
            'Pembatalan Resep'::text AS keterangan,
            pembatalan_resep.no_pembatalan AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            max(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            pembatalan_resep.reference,
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
                     JOIN penjualanresep_t ON pembatalanresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id) pembatalan_resep ON pembatalan_resep.pembatalanresep_id = stokobatalkes_t.pembatalanresep_id
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY pembatalan_resep.no_pembatalan, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, pembatalan_resep.reference, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Retur Resep'::text AS keterangan,
            retur_resep.no_returresep AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            retur_resep.reference,
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
                     JOIN returresep_t ON returresepdetail_t.returresep_id = returresep_t.returresep_id
                     JOIN penjualanresep_t ON returresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id) retur_resep ON stokobatalkes_t.returresepdetail_id = retur_resep.returresepdetail_id
          WHERE stokobatalkes_t.is_deleted = false
        UNION ALL
         SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
            'Adjusmen Masuk'::text AS keterangan,
            adjusmen_masuk.no_adjusmen AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            max(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT adjusmenobatmasuk_t.adjusmenobatmasuk_id,
                    adjusmenobat_t.no_adjusmen,
                    adjusmenobat_t.tgl_adjusmen
                   FROM adjusmenobatmasuk_t
                     JOIN adjusmenobat_t ON adjusmenobatmasuk_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id) adjusmen_masuk ON stokobatalkes_t.adjusmenobatmasuk_id = adjusmen_masuk.adjusmenobatmasuk_id
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY adjusmen_masuk.no_adjusmen, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id
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
            min(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT adjusmenobatkeluar_t.adjusmenobatkeluar_id,
                    adjusmenobat_t.no_adjusmen,
                    adjusmenobat_t.tgl_adjusmen
                   FROM adjusmenobatkeluar_t
                     JOIN adjusmenobat_t ON adjusmenobatkeluar_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id) adjusmen_keluar ON stokobatalkes_t.adjusmenobatkeluar_id = adjusmen_keluar.adjusmenobatkeluar_id
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY adjusmen_keluar.no_adjusmen, stokobatalkes_t.tglstok_out, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
            'Penerimaan Alternatif'::text AS keterangan,
            penerimaan_alternatif.no_penerimaan AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            min(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT penerimaansuppdetail_t.penerimaansuppdetail_id,
                    penerimaansupp_t.no_penerimaan,
                    penerimaansupp_t.tgl_penerimaan
                   FROM penerimaansupp_t
                     JOIN penerimaansuppdetail_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id) penerimaan_alternatif ON stokobatalkes_t.penerimaansuppdetail_id = penerimaan_alternatif.penerimaansuppdetail_id
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY 'Penerimaan Alternatif'::text, penerimaan_alternatif.no_penerimaan, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, '-'::character varying, stokobatalkes_t.ruangan_id
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
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT pemakaianobatdetail_t.pemakaianobatdetail_id,
                    pemakaianobat_t.nopemakaian_obat,
                    pemakaianobat_t.tglpemakaianobat
                   FROM pemakaianobat_t
                     JOIN pemakaianobatdetail_t ON pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id) pemakaian_ruangan ON stokobatalkes_t.pemakaianobatdetail_id = pemakaian_ruangan.pemakaianobatdetail_id
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
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT pemusnahanobatdetail_t.pemusnahanobatdetail_id,
                    pemusnahanobat_t.nopemusnahan,
                    pemusnahanobat_t.tglpemusnahan
                   FROM pemusnahanobat_t
                     JOIN pemusnahanobatdetail_t ON pemusnahanobat_t.pemusnahanobat_id = pemusnahanobatdetail_t.pemusnahanobat_id) pemusnahan_obat ON stokobatalkes_t.pemusnahanobatdetail_id = pemusnahan_obat.pemusnahanobatdetail_id
          WHERE stokobatalkes_t.is_deleted = false
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Stok Opname'::text AS keterangan,
            stok_opname.nostokopname AS no_transaksi,
            stok_opname.tglstokopname AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT stokopnamedetail_t.stokopnamedetail_id,
                    stokopname_t.nostokopname,
                    stokopname_t.tglstokopname
                   FROM stokopname_t
                     JOIN stokopnamedetail_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id) stok_opname ON stokobatalkes_t.stokopnamedetail_id = stok_opname.stokopnamedetail_id
          WHERE stokobatalkes_t.is_deleted = false
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
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT penerimaanobatdetail_t.penerimaanobatdetail_id,
                    penerimaanobat_t.no_penerimaan,
                    penerimaanobat_t.tgl_penerimaan
                   FROM penerimaanobat_t
                     JOIN penerimaanobatdetail_t ON penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id) penerimaan_supp ON stokobatalkes_t.penerimaanobatdetail_id = penerimaan_supp.penerimaanobatdetail_id
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
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT returpenerimaanobatdetail_t.returpenerimaanobatdetail_id,
                    returpenerimaanobat_t.no_returpenerimaanobat,
                    returpenerimaanobat_t.tgl_retur
                   FROM returpenerimaanobat_t
                     JOIN returpenerimaanobatdetail_t ON returpenerimaanobatdetail_t.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id) retur_penerimaan ON stokobatalkes_t.returpenerimaanobatdetail_id = retur_penerimaan.returpenerimaanobatdetail_id
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
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            terima_mutasi.ruanganasal_id AS ruangan_asal_id,
            terima_mutasi.ruanganpenerima_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT terimamutasiobatdetail_t.terimamutasiobatdetail_id,
                    terimamutasiobat_t.noterimamutasi,
                    terimamutasiobat_t.tglterima,
                    terimamutasiobat_t.ruanganpenerima_id,
                    terimamutasiobat_t.ruanganasal_id
                   FROM terimamutasiobat_t
                     JOIN terimamutasiobatdetail_t ON terimamutasiobat_t.terimamutasiobat_id = terimamutasiobatdetail_t.terimamutasiobat_id) terima_mutasi ON stokobatalkes_t.terimamutasidetail_id = terima_mutasi.terimamutasiobatdetail_id
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
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            mutasi_obat.ruanganasal_id AS ruangan_asal_id,
            mutasi_obat.ruangantujuan_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT mutasiobatdetail_t.mutasiobatdetail_id,
                    mutasiobatruangan_t.nomutasioa,
                    mutasiobatruangan_t.tglmutasioa,
                    mutasiobatruangan_t.ruanganasal_id,
                    mutasiobatruangan_t.ruangantujuan_id
                   FROM mutasiobatruangan_t
                     JOIN mutasiobatdetail_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id) mutasi_obat ON stokobatalkes_t.mutasiobatdetail_id = mutasi_obat.mutasiobatdetail_id
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
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            udd.ruanganproses_id AS ruangan_asal_id,
            udd.ruanganproses_id AS ruangan_tujuan_id
           FROM stokobatalkes_t
             JOIN ( SELECT obatalkespasien_t.obatalkespasien_id,
                    obatalkespasien_t.udd_detail_id,
                    udd_t.no_udd,
                    udd_t.ruanganproses_id
                   FROM obatalkespasien_t
                     JOIN udd_detail_t ON obatalkespasien_t.udd_detail_id = udd_detail_t.udd_detail_id
                     JOIN udd_t ON udd_detail_t.udd_id = udd_t.udd_id) udd ON stokobatalkes_t.obatalkespasien_id = udd.obatalkespasien_id
          WHERE stokobatalkes_t.is_deleted = false) kartu_stok
     JOIN obatalkes_m ON kartu_stok.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m ON kartu_stok.satuanunit_id = satuanunit_m.satuanunit_id
     LEFT JOIN ruangan_m ruangan_asal ON kartu_stok.ruangan_asal_id = ruangan_asal.ruangan_id
     LEFT JOIN ruangan_m ruangan_tujuan ON kartu_stok.ruangan_tujuan_id = ruangan_tujuan.ruangan_id;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210920_144401_migrate_infostokobatalkesnew_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210920_144401_migrate_infostokobatalkesnew_v cannot be reverted.\n";

        return false;
    }
    */
}
