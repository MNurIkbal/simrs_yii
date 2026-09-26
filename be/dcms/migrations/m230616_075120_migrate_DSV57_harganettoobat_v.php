<?php

use yii\db\Migration;

/**
 * Class m230616_075120_migrate_DSV57_harganettoobat_v
 */
class m230616_075120_migrate_DSV57_harganettoobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS public.harganettoobat_v;");

        $this->execute("CREATE OR REPLACE VIEW public.harganettoobat_v
            AS SELECT 'ADJ_MASUK'::text AS tipe,
                adjusmenobatmasuk_t.adjusmenobat_id AS transaksi_id,
                adjusmenobatmasuk_t.obatalkes_id,
                obatalkes_m.obatalkes_nama,
                satuan_input.satuanunit_nama AS satuan_transaksi,
                adjusmenobatmasuk_t.harga_netto_satuan AS harga_netto_transaksi,
                obatalkes_m.harganetto AS harga_netto_sekarang,
                    CASE
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MAX'::text THEN obatalkes_m.hargamaksimum
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MIN'::text THEN obatalkes_m.hargaminimum
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'AVG'::text THEN obatalkes_m.hargaratarata
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'LAST'::text THEN obatalkes_m.hargaterakhir
                        ELSE 0::double precision
                    END AS harga_disarankan,
                satuan_disarankan.satuanunit_nama AS satuan_disarankan
            FROM adjusmenobatmasuk_t
                JOIN obatalkes_m ON adjusmenobatmasuk_t.obatalkes_id = obatalkes_m.obatalkes_id
                JOIN satuanunit_m satuan_input ON adjusmenobatmasuk_t.satuanbesar_id = satuan_input.satuanunit_id
                JOIN satuanunit_m satuan_disarankan ON obatalkes_m.satuankecil_id = satuan_disarankan.satuanunit_id
                LEFT JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
            WHERE adjusmenobatmasuk_t.is_deleted = false
            UNION ALL
            SELECT 'PEN_SUPP'::text AS tipe,
                penerimaansuppdetail_t.penerimaansupp_id AS transaksi_id,
                penerimaansuppdetail_t.obatalkes_id,
                obatalkes_m.obatalkes_nama,
                satuan_input.satuanunit_nama AS satuan_transaksi,
                    CASE
                        WHEN penerimaansuppdetail_t.satuanbesar_id = penerimaansuppdetail_t.satuankecil_id THEN fcalculatehargatranspenerimaan(konfigfarmasi_k.use_discount, konfigfarmasi_k.use_ppn, penerimaansuppdetail_t.harga_netto_satuan, penerimaansuppdetail_t.diskon::double precision, pajak_m.pajak_persen::integer)
                        WHEN penerimaansuppdetail_t.satuanbesar_id <> penerimaansuppdetail_t.satuankecil_id THEN fcalculatehargatranspenerimaan(konfigfarmasi_k.use_discount, konfigfarmasi_k.use_ppn, penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision, penerimaansuppdetail_t.diskon::double precision, pajak_m.pajak_persen::integer)
                        ELSE NULL::double precision
                    END AS harga_netto_transaksi,
                obatalkes_m.harganetto AS harga_netto_sekarang,
                    CASE
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MAX'::text THEN obatalkes_m.hargamaksimum
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MIN'::text THEN obatalkes_m.hargaminimum
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'AVG'::text THEN obatalkes_m.hargaratarata
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'LAST'::text THEN obatalkes_m.hargaterakhir
                        ELSE 0::double precision
                    END AS harga_disarankan,
                satuan_disarankan.satuanunit_nama AS satuan_disarankan
            FROM penerimaansuppdetail_t
                JOIN obatalkes_m ON penerimaansuppdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                JOIN satuanunit_m satuan_input ON penerimaansuppdetail_t.satuanbesar_id = satuan_input.satuanunit_id
                JOIN satuanunit_m satuan_disarankan ON obatalkes_m.satuankecil_id = satuan_disarankan.satuanunit_id
                LEFT JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                LEFT JOIN ( SELECT pt.penerimaansupp_id,
                        pt.pajak_id
                    FROM penerimaansupp_t pt) penerimaansupp_t ON penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id
                LEFT JOIN ( SELECT pm.pajak_id,
                        pm.pajak_persen
                    FROM pajak_m pm) pajak_m ON pajak_m.pajak_id = penerimaansupp_t.pajak_id
            WHERE penerimaansuppdetail_t.is_deleted = false
            UNION ALL
            SELECT 'PO'::text AS tipe,
                penerimaanobatdetail_t.penerimaanobat_id AS transaksi_id,
                penerimaanobatdetail_t.obatalkes_id,
                obatalkes_m.obatalkes_nama,
                satuan_input.satuanunit_nama AS satuan_transaksi,
                fcalculatehargatranspenerimaan(konfigfarmasi_k.use_discount, konfigfarmasi_k.use_ppn, penerimaanobatdetail_t.harga, penerimaanobatdetail_t.discount::double precision, pajak_m.pajak_persen::integer) AS harga_netto_transaksi,
                obatalkes_m.harganetto AS harga_netto_sekarang,
                    CASE
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MAX'::text THEN obatalkes_m.hargamaksimum
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MIN'::text THEN obatalkes_m.hargaminimum
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'AVG'::text THEN obatalkes_m.hargaratarata
                        WHEN konfigfarmasi_k.hargaygdigunakan::text = 'LAST'::text THEN obatalkes_m.hargaterakhir
                        ELSE 0::double precision
                    END AS harga_disarankan,
                satuan_disarankan.satuanunit_nama AS satuan_disarankan
            FROM penerimaanobatdetail_t
                JOIN obatalkes_m ON penerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                JOIN satuankonversi_m ON penerimaanobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id AND satuankonversi_m.is_deleted = false AND satuankonversi_m.is_active = true
                JOIN satuanunit_m satuan_input ON satuankonversi_m.satuanbesar_id = satuan_input.satuanunit_id
                JOIN satuanunit_m satuan_disarankan ON obatalkes_m.satuankecil_id = satuan_disarankan.satuanunit_id
                LEFT JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                LEFT JOIN ( SELECT penerimaanobat_t_1.penerimaanobat_id,
                        penerimaanobat_t_1.validasipoobat_id
                    FROM penerimaanobat_t penerimaanobat_t_1) penerimaanobat_t ON penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id
                LEFT JOIN ( SELECT validasipoobat_t_1.validasipoobat_id,
                        validasipoobat_t_1.pajak_id
                    FROM validasipoobat_t validasipoobat_t_1) validasipoobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
                LEFT JOIN ( SELECT pm.pajak_id,
                        pm.pajak_persen
                    FROM pajak_m pm) pajak_m ON pajak_m.pajak_id = validasipoobat_t.pajak_id
            WHERE penerimaanobatdetail_t.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230616_075120_migrate_DSV57_harganettoobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230616_075120_migrate_DSV57_harganettoobat_v cannot be reverted.\n";

        return false;
    }
    */
}
