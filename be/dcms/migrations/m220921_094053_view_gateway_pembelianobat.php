<?php

use yii\db\Migration;

/**
 * Class m220921_094053_view_gateway_pembelianobat
 */
class m220921_094053_view_gateway_pembelianobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_pembelianobat_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_pembelianobat_v\"
        AS SELECT validasipoobat_t.no_poobat,
            validasipoobat_t.tgl_validasi AS tanggal_pembelian,
            supplier_m.supplier_nama,
            data_pembelian.data_detail_pembelian
           FROM validasipoobat_t
             LEFT JOIN ( SELECT a.supplier_id,
                    a.supplier_nama
                   FROM supplier_m a) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
             JOIN ( SELECT validasipoobat.validasipoobat_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                           FROM ( SELECT validasipoobatdetail_t.validasipoobat_id,
                                    penerimaanobatdetail_t.no_batch,
                                    validasipoobatdetail_t.obatalkes_id,
                                    jenisobatalkes_m.jenisobatalkes_nama AS jenis_obat,
                                    obatalkes_m.obatalkes_nama AS nama_obatalkes,
                                    satuanbesar.satuanunit_nama AS satuan,
                                    COALESCE(penerimaanobatdetail_t.harga, validasipoobatdetail_t.harga) AS harga_satuan,
                                    COALESCE(penerimaanobatdetail_t.harga, validasipoobatdetail_t.qty_po::double precision) AS quantity,
                                    satuankonversi_m.nilai_konversi AS quantity_konversi,
                                    COALESCE(penerimaanobatdetail_t.harga, validasipoobatdetail_t.discount::double precision) AS discount,
                                    COALESCE(penerimaanobatdetail_t.harga, validasipoobatdetail_t.discount_rp) AS discount_rp,
                                    COALESCE(penerimaanobatdetail_t.harga, validasipoobatdetail_t.jumlah) AS sub_total,
                                    penerimaanobatdetail_t.tgl_kadaluarsa
                                   FROM validasipoobatdetail_t
                                     LEFT JOIN ( SELECT a.penerimaanobatdetail_id,
                                            a.no_batch,
                                            a.harga,
                                            a.qty_po,
                                            a.tgl_kadaluarsa,
                                            a.s_konversiobt_id,
                                            a.validasipoobatdetail_id,
                                            a.discount,
                                            a.discount_rp,
                                            a.jumlah
                                           FROM penerimaanobatdetail_t a) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
                                     LEFT JOIN ( SELECT a.obatalkes_id,
                                            a.obatalkes_nama,
                                            a.jenisobatalkes_id
                                           FROM obatalkes_m a) obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                                     LEFT JOIN ( SELECT a.jenisobatalkes_id,
                                            a.jenisobatalkes_nama
                                           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                                     LEFT JOIN ( SELECT a.obatalkes_id,
                                            a.satuanbesar_id,
                                            a.satuankonversi_id,
                                            a.nilai_konversi
                                           FROM satuankonversi_m a) satuankonversi_m ON COALESCE(penerimaanobatdetail_t.s_konversiobt_id, validasipoobatdetail_t.s_konversiobt_id) = satuankonversi_m.satuankonversi_id
                                     LEFT JOIN ( SELECT a.satuanunit_id,
                                            a.satuanunit_nama
                                           FROM satuanunit_m a) satuanbesar ON satuankonversi_m.satuanbesar_id = satuanbesar.satuanunit_id
                                  WHERE validasipoobat.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id) d) AS data_detail_pembelian
                   FROM validasipoobat_t validasipoobat
                  GROUP BY validasipoobat.validasipoobat_id) data_pembelian ON validasipoobat_t.validasipoobat_id = data_pembelian.validasipoobat_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_094053_view_gateway_pembelianobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_094053_view_gateway_pembelianobat cannot be reverted.\n";

        return false;
    }
    */
}
