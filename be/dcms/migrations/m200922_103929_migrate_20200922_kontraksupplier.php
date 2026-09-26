<?php

use yii\db\Migration;

/**
 * Class m200922_103929_migrate_20200922_kontraksupplier
 */
class m200922_103929_migrate_20200922_kontraksupplier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
     $this->execute('DROP VIEW if exists "public"."kontraksupplier_v";');

     $this->execute("
        CREATE VIEW \"public\".\"kontraksupplier_v\" AS  SELECT kontraksupplier_m.kontraksupplier_id,
    kontraksupplier_m.supplier_id,
    supplier_m.supplier_nama,
    kontraksupplier_m.payterm_id,
    kontraksupplier_m.jumlah_hari,
    kontraksupplier_m.pajak_id,
    kontraksupplier_m.persen_ppn,
    kontraksupplierdetail_m.obatalkes_id,
    kontraksupplierdetail_m.kode_obat,
    kontraksupplierdetail_m.nama_obat,
    kontraksupplierdetail_m.harga,
    kontraksupplierdetail_m.pengurang AS diskon,
    sat_kecil.satuanunit_nama AS satuan_kecil,
    sat_konv1.satuanunit_nama AS satuan_konversi1,
    sat_konv2.satuanunit_nama AS satuan_konversi2,
    kontraksupplierdetail_m.kontraksupplierdetail_id,
    kontraksupplierdetail_m.satuankecil_id,
    kontraksupplierdetail_m.satuankonv1_id,
    kontraksupplierdetail_m.satuankonv2_id,
    kontraksupplierdetail_m.qty_min,
    kontraksupplierdetail_m.penambah,
    kontraksupplierdetail_m.pengurang,
    kontraksupplierdetail_m.total_harga,
    NULL::text AS uom_id,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom_text
   FROM ((((((kontraksupplier_m
     JOIN supplier_m ON ((kontraksupplier_m.supplier_id = supplier_m.supplier_id)))
     JOIN kontraksupplierdetail_m ON ((kontraksupplier_m.kontraksupplier_id = kontraksupplierdetail_m.kontraksupplier_id)))
     LEFT JOIN satuanunit_m sat_kecil ON ((kontraksupplierdetail_m.satuankecil_id = sat_kecil.satuanunit_id)))
     LEFT JOIN satuanunit_m sat_konv1 ON ((kontraksupplierdetail_m.satuankonv1_id = sat_konv1.satuanunit_id)))
     LEFT JOIN satuanunit_m sat_konv2 ON ((kontraksupplierdetail_m.satuankonv2_id = sat_konv2.satuanunit_id)))
     LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m.nilai_konversi
           FROM ((satuankonversi_m
             LEFT JOIN satuanunit_m uom_besar ON ((satuankonversi_m.satuanbesar_id = uom_besar.satuanunit_id)))
             LEFT JOIN satuanunit_m uom_kecil ON ((satuankonversi_m.satuankecil_id = uom_kecil.satuanunit_id)))
          WHERE (satuankonversi_m.is_deleted = false)
          GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuankecil_id, satuankonversi_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m.nilai_konversi) uom ON (((kontraksupplierdetail_m.obatalkes_id = uom.obatalkes_id) AND (kontraksupplierdetail_m.satuankonv1_id = uom.satuanbesar_id) AND (kontraksupplierdetail_m.satuankecil_id = uom.satuankecil_id))))
  WHERE ((kontraksupplier_m.is_deleted = false) AND (kontraksupplierdetail_m.is_deleted = false) AND (supplier_m.is_deleted = false));");
     
     $this->execute('ALTER TABLE "public"."kontraksupplier_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200922_103929_migrate_20200922_kontraksupplier cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200922_103929_migrate_20200922_kontraksupplier cannot be reverted.\n";

        return false;
    }
    */
}
