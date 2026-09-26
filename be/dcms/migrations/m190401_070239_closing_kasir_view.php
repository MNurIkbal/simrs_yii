<?php

use yii\db\Migration;

/**
 * Class m190401_070239_closing_kasir_view
 */
class m190401_070239_closing_kasir_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW closing_kasir_view;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW closing_kasir_view AS 
             SELECT agr_bukti_bayar.tandabuktibayar_id,
                agr_bukti_bayar.ruangan_id,
                agr_bukti_bayar.bayaruangmuka_id,
                agr_bukti_bayar.closingkasir_id,
                agr_bukti_bayar.pembayaranpelayanan_id,
                agr_bukti_bayar.shift_id,
                agr_bukti_bayar.nourutkasir,
                agr_bukti_bayar.nobuktibayar,
                agr_bukti_bayar.tglbuktibayar,
                agr_bukti_bayar.uangditerima,
                agr_bukti_bayar.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pasien_m.nama_pasien,
                agr_bukti_bayar.pegawai1_id,
                agr_bukti_bayar.no_pembayaran,
                pasien_m.no_rekam_medik,
                agr_bukti_bayar.carabayar_id,
                agr_bukti_bayar.carabayar_nama,
                agr_bukti_bayar.penjamin_id,
                agr_bukti_bayar.penjamin_nama,
                agr_bukti_bayar.jmlpembayaran
               FROM ( SELECT tandabuktibayar_t.tandabuktibayar_id,
                        tandabuktibayar_t.ruangan_id,
                        tandabuktibayar_t.bayaruangmuka_id,
                        tandabuktibayar_t.closingkasir_id,
                        tandabuktibayar_t.pembayaranpelayanan_id,
                        tandabuktibayar_t.shift_id,
                        tandabuktibayar_t.nourutkasir,
                        tandabuktibayar_t.nobuktibayar,
                        tandabuktibayar_t.tglbuktibayar,
                        tandabuktibayar_t.uangditerima,
                        tandabuktibayar_t.pegawai1_id,
                        pembayaranpelayanan_t.no_pembayaran,
                            CASE
                                WHEN tandabuktibayar_t.bayaruangmuka_id IS NOT NULL THEN bayaruangmuka_t.pendaftaran_id
                                WHEN tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL THEN pembayaranpelayanan_t.pendaftaran_id
                                ELSE NULL::integer
                            END AS pendaftaran_id,
                        pembayaranpelayanan_t.carabayar_id,
                        carabayar_m_1.carabayar_nama,
                        pembayaranpelayanan_t.penjamin_id,
                        penjamin_m_1.penjamin_nama,
                        tandabuktibayar_t.jmlpembayaran,
                        pembayaranpelayanan_t.penjualanresep_id
                       FROM tandabuktibayar_t
                         LEFT JOIN bayaruangmuka_t ON bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
                         LEFT JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
                         LEFT JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         LEFT JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                      WHERE tandabuktibayar_t.is_deleted = false) agr_bukti_bayar
                 LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id
                 LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                 LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                 LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                 LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                 LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                 LEFT JOIN penjualanresep_t ON penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id;
        ');

        $this->execute('
            ALTER TABLE closing_kasir_view
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_070239_closing_kasir_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_070239_closing_kasir_view cannot be reverted.\n";

        return false;
    }
    */
}
