<?php

use yii\db\Migration;

/**
 * Class m210303_054942_migrate_20210303_harganettoobat_v
 */
class m210303_054942_migrate_20210303_harganettoobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
 $this->execute("
    CREATE VIEW  \"public\".\"harganettoobat_v\" AS  SELECT 'ADJ_MASUK'::text AS tipe,
    adjusmenobatmasuk_t.adjusmenobat_id AS transaksi_id,
    adjusmenobatmasuk_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    satuan_input.satuanunit_nama AS satuan_transaksi,
    adjusmenobatmasuk_t.harga_netto AS harga_netto_transaksi,
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
    penerimaansuppdetail_t.harga_netto AS harga_netto_transaksi,
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
  WHERE penerimaansuppdetail_t.is_deleted = false
UNION ALL
 SELECT 'PO'::text AS tipe,
    penerimaanobatdetail_t.penerimaanobat_id AS transaksi_id,
    penerimaanobatdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    satuan_input.satuanunit_nama AS satuan_transaksi,
    penerimaanobatdetail_t.harga AS harga_netto_transaksi,
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
  WHERE penerimaanobatdetail_t.is_deleted = false;");

 $this->execute('ALTER TABLE "public"."harganettoobat_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210303_054942_migrate_20210303_harganettoobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210303_054942_migrate_20210303_harganettoobat_v cannot be reverted.\n";

        return false;
    }
    */
}
