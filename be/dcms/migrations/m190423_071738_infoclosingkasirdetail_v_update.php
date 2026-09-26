<?php

use yii\db\Migration;

/**
 * Class m190423_071738_infoclosingkasirdetail_v_update
 */
class m190423_071738_infoclosingkasirdetail_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW infoclosingkasirdetail_v AS 
             SELECT hit.closingkasir_id,
                hit.tgl_closingkasir,
                hit.no_closingkasir,
                hit.nama_pegawai,
                hit.tgl_pembayaran,
                hit.shift_nama,
                hit.no_pendaftaran,
                hit.nama_pasien,
                hit.total_terbayar,
                hit.nilaiuang,
                hit.banyakuang,
                hit.jumlahuang
               FROM ( SELECT closingkasir_t.closingkasir_id,
                        closingkasir_t.tgl_closingkasir,
                        closingkasir_t.no_closingkasir,
                        pegawai_m.nama_pegawai,
                        closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
                        shift_m.shift_nama,
                        \'null\'::text AS no_pendaftaran,
                        \'null\'::text AS nama_pasien,
                        closingkasir_t.total_setoran AS total_terbayar,
                        rincianclosing_t.nilaiuang,
                        rincianclosing_t.banyakuang,
                        rincianclosing_t.jumlahuang
                       FROM closingkasir_t
                         JOIN rincianclosing_t ON closingkasir_t.closingkasir_id = rincianclosing_t.closingkasir_id
                         JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
                                tandabuktibayar_t_1.pembayaranpelayanan_id,
                                sum(tandabuktibayar_t_1.uangditerima) AS uangditerima
                               FROM tandabuktibayar_t tandabuktibayar_t_1
                              GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.pembayaranpelayanan_id) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
                         JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
                         LEFT JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
                      WHERE closingkasir_t.is_active = true AND closingkasir_t.is_deleted = false
                      GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, pegawai_m.nama_pegawai, shift_m.shift_nama, \'null\'::text, closingkasir_t.total_setoran, rincianclosing_t.nilaiuang, rincianclosing_t.banyakuang, rincianclosing_t.jumlahuang
                    UNION ALL
                     SELECT closingkasir_t.closingkasir_id,
                        closingkasir_t.tgl_closingkasir,
                        closingkasir_t.no_closingkasir,
                        pegawai_m.nama_pegawai,
                        closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
                        shift_m.shift_nama,
                        \'null\'::text AS no_pendaftaran,
                        \'null\'::text AS nama_pasien,
                        closingkasir_t.total_setoran AS total_terbayar,
                        rincianclosing_t.nilaiuang,
                        rincianclosing_t.banyakuang,
                        rincianclosing_t.jumlahuang
                       FROM closingkasir_t
                         JOIN ( SELECT tandabuktibayar_t_1.closingkasir_id,
                                tandabuktibayar_t_1.bayaruangmuka_id,
                                sum(tandabuktibayar_t_1.uangditerima) AS uangditerima
                               FROM tandabuktibayar_t tandabuktibayar_t_1
                              GROUP BY tandabuktibayar_t_1.closingkasir_id, tandabuktibayar_t_1.bayaruangmuka_id) tandabuktibayar_t ON closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id
                         JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
                         JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
                         LEFT JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
                         JOIN rincianclosing_t ON closingkasir_t.closingkasir_id = rincianclosing_t.closingkasir_id
                      WHERE closingkasir_t.is_active = true AND closingkasir_t.is_deleted = false
                      GROUP BY closingkasir_t.closingkasir_id, closingkasir_t.tgl_closingkasir, closingkasir_t.no_closingkasir, pegawai_m.nama_pegawai, shift_m.shift_nama, \'null\'::text, closingkasir_t.total_setoran, rincianclosing_t.nilaiuang, rincianclosing_t.banyakuang, rincianclosing_t.jumlahuang) hit
              GROUP BY hit.closingkasir_id, hit.tgl_closingkasir, hit.no_closingkasir, hit.nama_pegawai, hit.tgl_pembayaran, hit.shift_nama, hit.no_pendaftaran, hit.nama_pasien, hit.total_terbayar, hit.nilaiuang, hit.banyakuang, hit.jumlahuang;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190423_071738_infoclosingkasirdetail_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190423_071738_infoclosingkasirdetail_v_update cannot be reverted.\n";

        return false;
    }
    */
}
