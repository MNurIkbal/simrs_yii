<?php

use yii\db\Migration;

/**
 * Class m230112_160825_migrate_gm_20_infokartustokobatalkes2_v
 */
class m230112_160825_migrate_gm_20_infokartustokobatalkes2_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infokartustokobatalkes2_v";
        ');

        $this->execute("
            CREATE OR REPLACE VIEW public.infokartustokobatalkes2_v
            AS SELECT stok_oa.stokobatalkes_id,
                stok_oa.obatalkes_id,
                stok_oa.tanggal_transaksi,
                stok_oa.no_transakasi,
                obatalkes_m.obatalkes_nama,
                stok_oa.qtystok_in,
                stok_oa.qtystok_out,
                stok_oa.stok,
                satuanunit_m.satuanunit_nama,
                stok_oa.tglkadaluarsa,
                stok_oa.keterangan,
                stok_oa.ruangan_asal_id,
                stok_oa.ruangan_tujuan_id,
                ruangan_asal.ruangan_nama AS ruangan_asal_nama,
                ruangan_tujuan.ruangan_nama AS ruangan_tujuan_nama,
                stok_oa.ruangan_id,
                    CASE stok_oa.keterangan
                        WHEN 'Penerimaan Mutasi'::text THEN ruangan_asal.ruangan_nama
                        WHEN 'Mutasi Obat'::text THEN ruangan_tujuan.ruangan_nama
                        WHEN 'Penjualan Resep'::text THEN stok_oa.nama_pasien
                        WHEN 'Batal Penjualan'::text THEN stok_oa.nama_pasien_batal
                        WHEN 'Retur Resep'::text THEN stok_oa.nama_pasien_retur
                        ELSE '-'::character varying
                    END AS reference,
                stok_oa.stok_tersedia,
                obatalkes_m.obatalkes_kode,
                obatalkes_m.is_active
            FROM ( SELECT stokobatalkes_t.stokobatalkes_id,
                        stokobatalkes_t.obatalkes_id,
                            CASE
                                WHEN stokobatalkes_t.is_deleted = true THEN 0::double precision
                                ELSE round(stokobatalkes_t.qtystok_in::numeric, 3)::double precision
                            END AS qtystok_in,
                            CASE
                                WHEN stokobatalkes_t.is_deleted = true THEN 0::double precision
                                ELSE round(stokobatalkes_t.qtystok_out::numeric, 3)::double precision
                            END AS qtystok_out,
                        round(stokobatalkes_t.stok::numeric, 3) AS stok,
                        stokobatalkes_t.tglkadaluarsa,
                        stokobatalkes_t.ruangan_id,
                            CASE
                                WHEN stokobatalkes_t.tglstok_in IS NOT NULL THEN stokobatalkes_t.tglstok_in::text
                                WHEN stokobatalkes_t.tglstok_out IS NOT NULL THEN stokobatalkes_t.tglstok_out::text
                                ELSE ''::text
                            END AS tanggal_transaksi,
                            CASE
                                WHEN penerimaan_obat.no_penerimaan IS NOT NULL THEN penerimaan_obat.no_penerimaan::text
                                WHEN terima_mutasi.noterimamutasi IS NOT NULL THEN terima_mutasi.noterimamutasi::text
                                WHEN retur_resep.no_returresep IS NOT NULL THEN retur_resep.no_returresep::text
                                WHEN retur_penerimaan.no_returpenerimaanobat IS NOT NULL THEN retur_penerimaan.no_returpenerimaanobat::text
                                WHEN mutasi_obat.nomutasioa IS NOT NULL THEN mutasi_obat.nomutasioa::text
                                WHEN penjualan.no_penjualan IS NOT NULL AND retur_resep.no_returresep IS NULL AND batal_resep.no_pembatalan IS NULL THEN penjualan.no_penjualan::text
                                WHEN pemusnahan_obat.nopemusnahan IS NOT NULL THEN pemusnahan_obat.nopemusnahan::text
                                WHEN stok_opname.nostokopname IS NOT NULL THEN stok_opname.nostokopname::text
                                WHEN pemakaian_obat.nopemakaian_obat IS NOT NULL THEN pemakaian_obat.nopemakaian_obat::text
                                WHEN produksi_obat.no_produksiobat IS NOT NULL THEN produksi_obat.no_produksiobat::text
                                WHEN store_expire.no_storexpired IS NOT NULL THEN store_expire.no_storexpired::text
                                WHEN penerimaan_supp.no_penerimaan IS NOT NULL THEN penerimaan_supp.no_penerimaan::text
                                WHEN adjustment_masuk.no_adjusmen IS NOT NULL THEN adjustment_masuk.no_adjusmen::text
                                WHEN adjustment_keluar.no_adjusmen IS NOT NULL THEN adjustment_keluar.no_adjusmen::text
                                WHEN batal_resep.no_pembatalan IS NOT NULL THEN batal_resep.no_pembatalan::text
                                ELSE ''::text
                            END AS no_transakasi,
                            CASE
                                WHEN stokobatalkes_t.is_deleted = true THEN 'Batal Transaksi'::text
                                WHEN penerimaan_obat.penerimaanobatdetail_id IS NOT NULL THEN 'Penerimaan Supplier'::text
                                WHEN terima_mutasi.terimamutasiobatdetail_id IS NOT NULL THEN 'Penerimaan Mutasi'::text
                                WHEN retur_resep.returresepdetail_id IS NOT NULL THEN 'Retur Resep'::text
                                WHEN retur_penerimaan.returpenerimaanobatdetail_id IS NOT NULL THEN 'Retur Penerimaan Supplier'::text
                                WHEN mutasi_obat.mutasiobatdetail_id IS NOT NULL THEN 'Mutasi Obat'::text
                                WHEN penjualan.obatalkespasien_id IS NOT NULL AND penjualan.penjualanresep_id IS NOT NULL AND batal_resep.pembatalanresep_id IS NULL AND retur_resep.returresepdetail_id IS NULL THEN 'Penjualan Resep'::text
                                WHEN penjualan.obatalkespasien_id IS NOT NULL AND penjualan.penjualanresep_id IS NULL THEN 'BMHP'::text
                                WHEN pemusnahan_obat.pemusnahanobatdetail_id IS NOT NULL THEN 'Pemusnahan Obat'::text
                                WHEN stok_opname.stokopnamedetail_id IS NOT NULL THEN 'Stok Opname'::text
                                WHEN pemakaian_obat.pemakaianobatdetail_id IS NOT NULL THEN 'Pemakaian Ruangan'::text
                                WHEN produksi_obat.produksiobatdetail_id IS NOT NULL THEN 'Produksi Obat'::text
                                WHEN store_expire.storexpiredobatdetail_id IS NOT NULL THEN 'Store Expired Obat'::text
                                WHEN penerimaan_supp.penerimaansuppdetail_id IS NOT NULL THEN 'Penerimaan Alternatif'::text
                                WHEN adjustment_masuk.adjusmenobatmasuk_id IS NOT NULL THEN 'Adjusmen Obat Masuk'::text
                                WHEN adjustment_keluar.adjusmenobatkeluar_id IS NOT NULL THEN 'Adjusmen Obat Keluar'::text
                                WHEN batal_resep.pembatalanresep_id IS NOT NULL THEN 'Batal Penjualan'::text
                                ELSE '-'::text
                            END AS keterangan,
                            CASE
                                WHEN mutasi_obat.ruanganasal_id IS NOT NULL THEN COALESCE(mutasi_obat.ruanganasal_id, 0)
                                WHEN terima_mutasi.ruanganasal_id IS NOT NULL THEN COALESCE(terima_mutasi.ruanganasal_id, 0)
                                ELSE COALESCE(stokobatalkes_t.ruangan_id, 0)
                            END AS ruangan_asal_id,
                            CASE
                                WHEN mutasi_obat.ruangantujuan_id IS NOT NULL THEN COALESCE(mutasi_obat.ruangantujuan_id, 0)
                                WHEN terima_mutasi.ruanganpenerima_id IS NOT NULL THEN COALESCE(terima_mutasi.ruanganpenerima_id, 0)
                                ELSE COALESCE(stokobatalkes_t.ruangan_id, 0)
                            END AS ruangan_tujuan_id,
                        penjualan.nama_pasien,
                        batal_resep.nama_pasien_batal,
                        retur_resep.nama_pasien_retur,
                        round(stokobatalkes_t.stok_tersedia::numeric, 3) AS stok_tersedia
                    FROM stokobatalkes_t
                        LEFT JOIN ( SELECT penerimaanobatdetail_t.penerimaanobatdetail_id,
                                penerimaanobat_t.no_penerimaan
                            FROM penerimaanobat_t
                                JOIN penerimaanobatdetail_t ON penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id) penerimaan_obat ON stokobatalkes_t.penerimaanobatdetail_id = penerimaan_obat.penerimaanobatdetail_id
                        LEFT JOIN ( SELECT terimamutasiobatdetail_t.terimamutasiobatdetail_id,
                                terimamutasiobat_t.noterimamutasi,
                                terimamutasiobat_t.ruanganpenerima_id,
                                terimamutasiobat_t.ruanganasal_id
                            FROM terimamutasiobat_t
                                JOIN terimamutasiobatdetail_t ON terimamutasiobat_t.terimamutasiobat_id = terimamutasiobatdetail_t.terimamutasiobat_id) terima_mutasi ON terima_mutasi.terimamutasiobatdetail_id = stokobatalkes_t.terimamutasidetail_id
                        LEFT JOIN ( SELECT returresepdetail_t.returresepdetail_id,
                                returresep_t.no_returresep,
                                    CASE
                                        WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli
                                        ELSE pasien_m.nama_pasien
                                    END AS nama_pasien_retur
                            FROM returresep_t
                                JOIN returresepdetail_t ON returresep_t.returresep_id = returresepdetail_t.returresep_id
                                LEFT JOIN obatalkespasien_t ON obatalkespasien_t.obatalkespasien_id = returresepdetail_t.obatalkespasien_id
                                LEFT JOIN penjualanresep_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
                                LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
                                LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = penjualanresep_t.pendaftaran_id) retur_resep ON retur_resep.returresepdetail_id = stokobatalkes_t.returresepdetail_id
                        LEFT JOIN ( SELECT returpenerimaanobat_t.no_returpenerimaanobat,
                                returpenerimaanobatdetail_t.returpenerimaanobatdetail_id
                            FROM returpenerimaanobat_t
                                JOIN returpenerimaanobatdetail_t ON returpenerimaanobatdetail_t.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id) retur_penerimaan ON retur_penerimaan.returpenerimaanobatdetail_id = stokobatalkes_t.returpenerimaanobatdetail_id
                        LEFT JOIN ( SELECT mutasiobatruangan_t.nomutasioa,
                                mutasiobatdetail_t.mutasiobatdetail_id,
                                mutasiobatruangan_t.ruanganasal_id,
                                mutasiobatruangan_t.ruangantujuan_id
                            FROM mutasiobatruangan_t
                                JOIN mutasiobatdetail_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id) mutasi_obat ON mutasi_obat.mutasiobatdetail_id = stokobatalkes_t.mutasiobatdetail_id
                        LEFT JOIN ( SELECT obatalkespasien_t.obatalkespasien_id,
                                    CASE
                                        WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli
                                        ELSE pasien_m.nama_pasien
                                    END AS nama_pasien,
                                obatalkespasien_t.penjualanresep_id,
                                    CASE
                                        WHEN penjualanresep_t.noresep IS NOT NULL THEN penjualanresep_t.noresep
                                        ELSE pendaftaran_t.no_pendaftaran
                                    END AS no_penjualan
                            FROM obatalkespasien_t
                                LEFT JOIN pasien_m ON obatalkespasien_t.pasien_id = pasien_m.pasien_id
                                LEFT JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                                LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id) penjualan ON penjualan.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id
                        LEFT JOIN ( SELECT pemusnahanobat_t.nopemusnahan,
                                pemusnahanobatdetail_t.pemusnahanobatdetail_id
                            FROM pemusnahanobat_t
                                JOIN pemusnahanobatdetail_t ON pemusnahanobat_t.pemusnahanobat_id = pemusnahanobatdetail_t.pemusnahanobat_id) pemusnahan_obat ON pemusnahan_obat.pemusnahanobatdetail_id = stokobatalkes_t.pemusnahanobatdetail_id
                        LEFT JOIN ( SELECT stokopnamedetail_t.stokopnamedetail_id,
                                stokopname_t.nostokopname
                            FROM stokopname_t
                                JOIN stokopnamedetail_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id) stok_opname ON stok_opname.stokopnamedetail_id = stokobatalkes_t.stokopnamedetail_id
                        LEFT JOIN ( SELECT pemakaianobat_t.nopemakaian_obat,
                                pemakaianobatdetail_t.pemakaianobatdetail_id
                            FROM pemakaianobat_t
                                JOIN pemakaianobatdetail_t ON pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id) pemakaian_obat ON pemakaian_obat.pemakaianobatdetail_id = stokobatalkes_t.pemakaianobatdetail_id
                        LEFT JOIN ( SELECT produksiobat_t.no_produksiobat,
                                produksiobatdetail_t.produksiobatdetail_id
                            FROM produksiobat_t
                                JOIN produksiobatdetail_t ON produksiobatdetail_t.produksiobat_id = produksiobat_t.produksiobat_id) produksi_obat ON produksi_obat.produksiobatdetail_id = stokobatalkes_t.produksiobatdetail_id
                        LEFT JOIN ( SELECT storexpiredobat_t.no_storexpired,
                                storexpiredobatdetail_t.storexpiredobatdetail_id
                            FROM storexpiredobat_t
                                JOIN storexpiredobatdetail_t ON storexpiredobatdetail_t.storexpiredobat_id = storexpiredobat_t.storexpiredobat_id) store_expire ON store_expire.storexpiredobatdetail_id = stokobatalkes_t.storexpiredobatdetail_id
                        LEFT JOIN ( SELECT penerimaansupp_t.no_penerimaan,
                                penerimaansuppdetail_t.penerimaansuppdetail_id
                            FROM penerimaansupp_t
                                JOIN penerimaansuppdetail_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id) penerimaan_supp ON penerimaan_supp.penerimaansuppdetail_id = stokobatalkes_t.penerimaansuppdetail_id
                        LEFT JOIN ( SELECT adjusmenobat_t.no_adjusmen,
                                adjusmenobatmasuk_t.adjusmenobatmasuk_id
                            FROM adjusmenobat_t
                                JOIN adjusmenobatmasuk_t ON adjusmenobatmasuk_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id) adjustment_masuk ON adjustment_masuk.adjusmenobatmasuk_id = stokobatalkes_t.adjusmenobatmasuk_id
                        LEFT JOIN ( SELECT adjusmenobat_t.no_adjusmen,
                                adjusmenobatkeluar_t.adjusmenobatkeluar_id
                            FROM adjusmenobat_t
                                JOIN adjusmenobatkeluar_t ON adjusmenobatkeluar_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id) adjustment_keluar ON adjustment_keluar.adjusmenobatkeluar_id = stokobatalkes_t.adjusmenobatkeluar_id
                        LEFT JOIN ( SELECT penjualanresep_t.pembatalanresep_id,
                                pembatalanresep_t.no_pembatalan,
                                    CASE
                                        WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli
                                        ELSE pasien_m.nama_pasien
                                    END AS nama_pasien_batal
                            FROM pembatalanresep_t
                                JOIN penjualanresep_t ON penjualanresep_t.pembatalanresep_id = pembatalanresep_t.pembatalanresep_id
                                LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
                                LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = penjualanresep_t.pendaftaran_id) batal_resep ON batal_resep.pembatalanresep_id = stokobatalkes_t.pembatalanresep_id) stok_oa
                JOIN obatalkes_m ON obatalkes_m.obatalkes_id = stok_oa.obatalkes_id
                JOIN satuanunit_m ON obatalkes_m.satuankecil_id = satuanunit_m.satuanunit_id
                JOIN ruangan_m ruangan_asal ON ruangan_asal.ruangan_id = stok_oa.ruangan_asal_id
                JOIN ruangan_m ruangan_tujuan ON ruangan_tujuan.ruangan_id = stok_oa.ruangan_tujuan_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230112_160825_migrate_gm_20_infokartustokobatalkes2_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230112_160825_migrate_gm_20_infokartustokobatalkes2_v cannot be reverted.\n";

        return false;
    }
    */
}
