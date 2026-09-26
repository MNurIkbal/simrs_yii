<?php

use yii\db\Migration;

/**
 * Class m190327_101420_infoclosingkasir_v
 */
class m190327_101420_infoclosingkasir_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW infoclosingkasir_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW infoclosingkasir_v AS 
             SELECT closingkasir_t.closingkasir_id,
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
                closingkasir_t.tgl_closingkasir AS tgl_pembayaran
               FROM closingkasir_t
                 JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
                        tandabuktibayar_t_1.pembayaranpelayanan_id,
                        tandabuktibayar_t_1.uangditerima,
                        tandabuktibayar_t_1.tglbuktibayar
                       FROM tandabuktibayar_t tandabuktibayar_t_1
                      GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
                 JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                 JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
                 JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
                 JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 LEFT JOIN setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
              GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.uangditerima, tandabuktibayar_t.tglbuktibayar
            UNION ALL
             SELECT closingkasir_t.closingkasir_id,
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
                closingkasir_t.tgl_closingkasir AS tgl_pembayaran
               FROM closingkasir_t
                 JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
                        tandabuktibayar_t_1.bayaruangmuka_id,
                        tandabuktibayar_t_1.uangditerima,
                        tandabuktibayar_t_1.tglbuktibayar
                       FROM tandabuktibayar_t tandabuktibayar_t_1
                      GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.bayaruangmuka_id, tandabuktibayar_t_1.uangditerima, tandabuktibayar_t_1.tglbuktibayar) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
                 JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
                 JOIN pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
                 JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
                 JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 LEFT JOIN setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
              GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.shift_id, shift_m.shift_nama, closingkasir_t.pegawai_id, pegawai_m.nama_pegawai, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, closingkasir_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, closingkasir_t.nilai_closingtransaksi, closingkasir_t.total_setoran, setorbank_t.setorbank_id, setorbank_t.no_struksetor, setorbank_t.tgl_disetor, setorbank_t.nama_bank, setorbank_t.no_rekening, setorbank_t.jumlah_setoran, pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, tandabuktibayar_t.tglbuktibayar, tandabuktibayar_t.uangditerima;
        ');

        $this->execute('
            ALTER TABLE infoclosingkasir_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_101420_infoclosingkasir_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_101420_infoclosingkasir_v cannot be reverted.\n";

        return false;
    }
    */
}
