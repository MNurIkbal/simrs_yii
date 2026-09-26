<?php

use yii\db\Migration;

/**
 * Class m230705_094319_laporan_penerimaan_dan_pengeluaran_kasir_view
 */
class m230705_094319_laporan_penerimaan_dan_pengeluaran_kasir_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    { 
        $this->execute("DROP VIEW IF EXISTS public.laporanpembayarantransaksi_v;");
        $this->execute("CREATE OR REPLACE VIEW public.laporanpembayarantransaksi_v
            AS  SELECT
                CASE
                    WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN penerimaan.tglbuktibayar
                    WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN pengeluaran.tgl_buktikeluar
                    ELSE NULL::timestamp without time zone
                END AS tanggal,
                CASE
                    WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN penerimaan.nobuktibayar
                    WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN pengeluaran.no_buktikeluar
                    ELSE NULL::character varying
                END AS nobuktibayar,
                CASE
                    WHEN pembayarantransaksi_t.jenis_transaksi = 668 THEN 'PENERIMAAN'::text
                    WHEN pembayarantransaksi_t.jenis_transaksi = 669 THEN 'PENGELUARAN'::text
                    ELSE NULL::text
                END AS jenis,
                CASE
                    WHEN pembayarantransaksi_t.tipe_transaksi = 700 THEN supplier_m.supplier_kode
                    WHEN pembayarantransaksi_t.tipe_transaksi = 701 THEN pegawai_m.nomorindukpegawai
                    WHEN pembayarantransaksi_t.tipe_transaksi = 702 THEN pasien.no_rekam_medik
                    ELSE NULL::character varying
                END AS no_identitas,
                CASE
                    WHEN pembayarantransaksi_t.tipe_transaksi = 700 THEN supplier_m.supplier_nama
                    WHEN pembayarantransaksi_t.tipe_transaksi = 701 THEN pegawai_m.nama_pegawai
                    WHEN pembayarantransaksi_t.tipe_transaksi = 702 THEN pasien.nama_pasien
                    ELSE NULL::character varying
                END AS nama_identitas,
                    lkp_tipe.lookup_name AS tipe,
                    pembayarantransaksi_t.deskripsi,
                        CASE
                            WHEN pembayarantransaksi_t.jenisnontunai_id IS NULL THEN 'Tunai'::text
                            ELSE 'Bank'::text
                        END AS metode_pembayaran,
                    pembayarantransaksi_t.jumlah,
                    kasir.nama_pegawai AS kasir
                FROM pembayarantransaksi_t
                    LEFT JOIN ( SELECT f.penerimaanumum_id,
                            f.tandabuktibayar_id,
                            f.ruangan_id,
                            f.closingkasir_id,
                            f.shift_id,
                            f.nobuktibayar,
                            f.tglbuktibayar,
                            f.uangditerima
                        FROM tandabuktibayar_t f) penerimaan ON pembayarantransaksi_t.pembayarantransaksi_id = penerimaan.penerimaanumum_id
                    LEFT JOIN ( SELECT f.tandabuktikeluar_id,
                            f.pembayarantransaksi_id,
                            f.ruangan_id,
                            f.closingkasir_id,
                            f.shift_id,
                            f.no_buktikeluar,
                            f.tgl_buktikeluar,
                            f.uang_diterima
                        FROM tandabuktikeluar_t f) pengeluaran ON pembayarantransaksi_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id
                    LEFT JOIN ( SELECT f.pasien_id,
                            f.nama_pasien,
                            f.no_rekam_medik
                        FROM pasien_m f) pasien ON pembayarantransaksi_t.pasien_id = pasien.pasien_id
                    LEFT JOIN ( SELECT f.pegawai_id,
                            f.nama_pegawai,
                            f.nomorindukpegawai
                        FROM pegawai_m f) pegawai_m ON pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id
                    LEFT JOIN ( SELECT f.supplier_id,
                            f.supplier_nama,
                            f.supplier_kode
                        FROM supplier_m f) supplier_m ON pembayarantransaksi_t.supplier_id = supplier_m.supplier_id
                    JOIN ( SELECT f.loginpemakai_id,
                            f.pegawai_id
                        FROM loginpemakai_k f) loginpemakai_k ON pembayarantransaksi_t.created_by = loginpemakai_k.loginpemakai_id
                    LEFT JOIN ( SELECT a.kategoritransaksi_id,
                            a.kategoritransaksi_nama
                        FROM kategoritransaksi_m a) kategoritransaksi_m ON pembayarantransaksi_t.kategoritransaksi_id = kategoritransaksi_m.kategoritransaksi_id
                    LEFT JOIN ( SELECT lookup_m.lookup_id,
                            lookup_m.lookup_name
                        FROM lookup_m) lkp_tipe ON pembayarantransaksi_t.tipe_transaksi = lkp_tipe.lookup_id
                    LEFT JOIN ( SELECT a.loginpemakai_id,
                            pegawai.nama_pegawai
                        FROM loginpemakai_k a
                            JOIN ( SELECT pegawai_m_1.pegawai_id,
                                    pegawai_m_1.nama_pegawai
                                FROM pegawai_m pegawai_m_1) pegawai ON a.pegawai_id = pegawai.pegawai_id) kasir ON pembayarantransaksi_t.created_by = kasir.loginpemakai_id
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230705_094319_laporan_penerimaan_dan_pengeluaran_kasir_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230705_094319_laporan_penerimaan_dan_pengeluaran_kasir_view cannot be reverted.\n";

        return false;
    }
    */
}
