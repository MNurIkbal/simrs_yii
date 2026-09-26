<?php

use yii\db\Migration;

/**
 * Class m211007_063803_oddo_penyesuaianschema
 */
class m211007_063803_oddo_penyesuaianschema extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasien_r" ALTER COLUMN "tgl_proses" SET DEFAULT (to_char(now(), \'YYYY-MM-DD hh24:mm:ss\'::text))::timestamp without time zone;');

        $this->execute('ALTER TABLE "public"."pendaftaran_r" ALTER COLUMN "tgl_proses" SET DEFAULT (to_char(now(), \'YYYY-MM-DD hh24:mm:ss\'::text))::timestamp without time zone;');

        $this->execute('ALTER TABLE "public"."penjualanresep_r" ALTER COLUMN "tgl_proses" SET DEFAULT (to_char(now(), \'YYYY-MM-DD hh24:mm:ss\'::text))::timestamp without time zone;');

        $this->execute('DROP VIEW if exists "public"."int_obat";');

        $this->execute("
            CREATE VIEW \"public\".\"int_obat\" AS  SELECT concat('OBT', obatalkes_m.obatalkes_id) AS sync_id_api,
    true AS active,
    true AS sale_ok,
    true AS purchase_ok,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS categ_id,
    COALESCE(obatalkes_m.satuanbesar_id, obatalkes_m.satuankecil_id) AS uom_po_id,
    COALESCE(obatalkes_m.satuanbesar_id, obatalkes_m.satuankecil_id) AS uom2_id,
    obatalkes_m.satuankecil_id AS uom_id,
    obatalkes_m.obatalkes_kode AS default_code,
    'product'::text AS type,
        CASE
            WHEN obatalkes_m.is_active IS TRUE AND obatalkes_m.is_deleted IS TRUE THEN true
            WHEN obatalkes_m.is_active IS TRUE AND obatalkes_m.is_deleted IS FALSE THEN false
            WHEN obatalkes_m.is_active IS FALSE AND obatalkes_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    obatalkes_m.strength,
    NULL::text AS catalog_code,
    '-'::text AS brand,
    NULL::text AS manufacturer_code,
    '-'::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
        CASE
            WHEN obatalkes_m.satuanbesar_id IS NULL THEN 1
            ELSE obatalkes_m.kemasan_besar
        END AS conversion_rate,
    6 AS sync_type,
    'OBAT'::text AS jenis,
    obatalkes_m.obatalkes_id AS id,
    obatalkes_m.additional_data,
        CASE
            WHEN (obatalkes_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (obatalkes_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'BELUM KIRIM'::text
        END AS status,
    obatalkes_m.obatalkes_id,
    false AS sync_is_package,
    false AS sync_is_service
   FROM obatalkes_m;");

        $this->execute('DROP VIEW if exists "public"."int_barang";');
        
        $this->execute("
            CREATE VIEW \"public\".\"int_barang\" AS  SELECT concat('BRG', barang_m.barang_id) AS sync_id_api,
    true AS active,
    true AS sale_ok,
    true AS purchase_ok,
    barang_m.barang_nama AS name,
    concat('BRG', barang_m.kelompokbarang_id) AS categ_id,
    COALESCE(barang_m.satuankecil_id, barang_m.satuan1_id) AS uom_po_id,
    COALESCE(barang_m.satuankecil_id, barang_m.satuan1_id) AS uom2_id,
    barang_m.satuankecil_id AS uom_id,
    barang_m.barang_kode AS default_code,
    'product'::text AS type,
        CASE
            WHEN barang_m.is_active IS TRUE AND barang_m.is_deleted IS TRUE THEN true
            WHEN barang_m.is_active IS TRUE AND barang_m.is_deleted IS FALSE THEN false
            WHEN barang_m.is_active IS FALSE AND barang_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    NULL::text AS strength,
    NULL::text AS catalog_code,
    '-'::text AS brand,
    NULL::text AS manufacturer_code,
    '-'::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
        CASE
            WHEN barang_m.satuan1_id IS NULL THEN 1
            ELSE barang_m.isi_satuan1
        END AS conversion_rate,
    6 AS sync_type,
    'BARANG'::text AS jenis,
    barang_m.barang_id AS id,
    barang_m.additional_data,
        CASE
            WHEN (barang_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (barang_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'BELUM KIRIM'::text
        END AS status,
    barang_m.barang_id,
    false AS sync_is_package,
    false AS sync_is_service
   FROM barang_m;");
        $this->execute('');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211007_063803_oddo_penyesuaianschema cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211007_063803_oddo_penyesuaianschema cannot be reverted.\n";

        return false;
    }
    */
}
