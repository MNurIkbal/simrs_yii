<?php

use yii\db\Migration;

/**
 * Class m200310_093308_informasi_closing_kasir
 */
class m200310_093308_informasi_closing_kasir extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infoclosingkasir_v;');
        $this->execute("
            CREATE OR REPLACE VIEW public.infoclosingkasir_v AS 
             SELECT 'pembayaran'::text AS tipe,
                closingkasir_t.closingkasir_id,
                closingkasir_t.shift_id,
                shift_m.shift_nama,
                closingkasir_t.pegawai_id,
                pegawai_m.nama_pegawai,
                tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
                closingkasir_t.no_closingkasir,
                closingkasir_t.ruangan_id,
                ruangan_m.ruangan_nama,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama,
                closingkasir_t.nilai_closingtransaksi,
                tandabuktibayar_t.uangditerima AS total_setoran,
                setorbank_t.setorbank_id,
                setorbank_t.no_struksetor,
                setorbank_t.tgl_disetor,
                setorbank_t.nama_bank,
                setorbank_t.no_rekening,
                setorbank_t.jumlah_setoran,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pasien_m.pasien_id,
                pasien_m.nama_pasien,
                tandabuktibayar_t.uangditerima AS total_terbayar,
                closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
                (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
                pembayaran_t.total_nontunai,
                pembayaran_t.total_dijamin
               FROM ((((((((((((closingkasir_t
                 JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
                        tandabuktibayar_t_1.pembayaranpelayanan_id,
                        tandabuktibayar_t_1.uangditerima,
                        tandabuktibayar_t_1.tglbuktibayar
                       FROM tandabuktibayar_t tandabuktibayar_t_1
                      GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
                 JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
                 JOIN pembayaran_t ON (((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) AND (pembayaran_t.is_deleted = false))))
                 JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
                 JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
              GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.total_kembalian, pembayaran_t.total_dibayar, pembayaran_t.total_administrasi, pembayaran_t.total_discount, pembayaran_t.total_discountpembayaran
            UNION ALL
             SELECT 'uang_muka'::text AS tipe,
                closingkasir_t.closingkasir_id,
                closingkasir_t.shift_id,
                shift_m.shift_nama,
                closingkasir_t.pegawai_id,
                pegawai_m.nama_pegawai,
                tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
                closingkasir_t.no_closingkasir,
                closingkasir_t.ruangan_id,
                ruangan_m.ruangan_nama,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama,
                closingkasir_t.nilai_closingtransaksi,
                tandabuktibayar_t.uangditerima AS total_setoran,
                setorbank_t.setorbank_id,
                setorbank_t.no_struksetor,
                setorbank_t.tgl_disetor,
                setorbank_t.nama_bank,
                setorbank_t.no_rekening,
                setorbank_t.jumlah_setoran,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pasien_m.pasien_id,
                pasien_m.nama_pasien,
                tandabuktibayar_t.uangditerima AS total_terbayar,
                closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                0 AS total_tagihan,
                0 AS total_tunai,
                0 AS total_nontunai,
                0 AS total_dijamin
               FROM (((((((((((closingkasir_t
                 JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
                        tandabuktibayar_t_1.bayaruangmuka_id,
                        tandabuktibayar_t_1.uangditerima,
                        tandabuktibayar_t_1.tglbuktibayar
                       FROM tandabuktibayar_t tandabuktibayar_t_1
                      GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.bayaruangmuka_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
                 JOIN bayaruangmuka_t ON ((tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id)))
                 JOIN pendaftaran_t ON ((bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
                 JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
              GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.tglbuktibayar, tandabuktibayar_t.uangditerima, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama
            UNION ALL
             SELECT 'resep_bebas'::text AS tipe,
                closingkasir_t.closingkasir_id,
                closingkasir_t.shift_id,
                shift_m.shift_nama,
                closingkasir_t.pegawai_id,
                pegawai_m.nama_pegawai,
                tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
                closingkasir_t.no_closingkasir,
                closingkasir_t.ruangan_id,
                ruangan_m.ruangan_nama,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama,
                closingkasir_t.nilai_closingtransaksi,
                tandabuktibayar_t.uangditerima AS total_setoran,
                setorbank_t.setorbank_id,
                setorbank_t.no_struksetor,
                setorbank_t.tgl_disetor,
                setorbank_t.nama_bank,
                setorbank_t.no_rekening,
                setorbank_t.jumlah_setoran,
                penjualanresep_t.penjualanresep_id AS pendaftaran_id,
                penjualanresep_t.noresep AS no_pendaftaran,
                pasien_m.pasien_id,
                    CASE
                        WHEN (pasien_m.nama_pasien IS NULL) THEN penjualanresep_t.nama_pembeli
                        ELSE pasien_m.nama_pasien
                    END AS nama_pasien,
                tandabuktibayar_t.uangditerima AS total_terbayar,
                closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                0 AS total_tagihan,
                0 AS total_tunai,
                0 AS total_nontunai,
                0 AS total_dijamin
               FROM (((((((((((closingkasir_t
                 JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
                        tandabuktibayar_t_1.pembayaranpelayanan_id,
                        tandabuktibayar_t_1.uangditerima,
                        tandabuktibayar_t_1.tglbuktibayar
                       FROM tandabuktibayar_t tandabuktibayar_t_1
                      GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
                 JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
                 JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                 LEFT JOIN carabayar_m ON ((penjualanresep_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
                 JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
              GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, penjualanresep_t.penjualanresep_id, penjualanresep_t.noresep, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama
            UNION ALL
             SELECT 'retur'::text AS tipe,
                closingkasir_t.closingkasir_id,
                closingkasir_t.shift_id,
                shift_m.shift_nama,
                closingkasir_t.pegawai_id,
                pegawai_m.nama_pegawai,
                closingkasir_t.tgl_closingkasir,
                closingkasir_t.no_closingkasir,
                closingkasir_t.ruangan_id,
                ruangan_m.ruangan_nama,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama,
                closingkasir_t.nilai_closingtransaksi,
                tandabuktikeluar_t.jml_pembayaran AS total_setoran,
                setorbank_t.setorbank_id,
                setorbank_t.no_struksetor,
                setorbank_t.tgl_disetor,
                setorbank_t.nama_bank,
                setorbank_t.no_rekening,
                setorbank_t.jumlah_setoran,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pasien_m.pasien_id,
                pasien_m.nama_pasien,
                tandabuktikeluar_t.uang_diterima AS total_terbayar,
                closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                0 AS total_tagihan,
                (- returbayarpelayanan_t.total_biayaretur) AS total_tunai,
                (- returbayarpelayanan_t.total_nontunai) AS total_nontunai,
                0 AS total_dijamin
               FROM ((((((((((((((closingkasir_t
                 JOIN ( SELECT tandabuktikeluar_t_1.closingkasir_id,
                        tandabuktikeluar_t_1.tgl_buktikeluar,
                        tandabuktikeluar_t_1.returbayarpelayanan_id,
                        tandabuktikeluar_t_1.jml_pembayaran,
                        tandabuktikeluar_t_1.uang_diterima
                       FROM tandabuktikeluar_t tandabuktikeluar_t_1
                      GROUP BY tandabuktikeluar_t_1.closingkasir_id, tandabuktikeluar_t_1.tgl_buktikeluar, tandabuktikeluar_t_1.returbayarpelayanan_id, tandabuktikeluar_t_1.jml_pembayaran, tandabuktikeluar_t_1.uang_diterima) tandabuktikeluar_t ON ((closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id)))
                 JOIN returbayarpelayanan_t ON ((tandabuktikeluar_t.returbayarpelayanan_id = returbayarpelayanan_t.returbayarpelayanan_id)))
                 JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
                 JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
                 JOIN pembayaran_t ON (((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) AND (pembayaran_t.is_deleted = false))))
                 JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
                 JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
              GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktikeluar_t.uang_diterima, tandabuktikeluar_t.jml_pembayaran, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pembayaran_t.total_tagihan, returbayarpelayanan_t.total_biayaretur, returbayarpelayanan_t.total_nontunai, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.total_kembalian, pembayaran_t.total_dibayar;");
        $this->execute('ALTER TABLE public.infoclosingkasir_v OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_093308_informasi_closing_kasir cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200310_093308_informasi_closing_kasir cannot be reverted.\n";

        return false;
    }
    */
}
