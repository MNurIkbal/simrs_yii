<?php

use yii\db\Migration;

/**
 * Class m200914_032220_migrate_20200914_kontraksupplier
 */
class m200914_032220_migrate_20200914_kontraksupplier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."kontraksupplier_m" ADD IF NOT EXISTS "kontraksupplier_no" varchar(100) COLLATE "pg_catalog"."default";');
    $this->execute('ALTER TABLE "public"."kontraksupplier_m" ADD IF NOT EXISTS "tgl_berlaku" date;');
    $this->execute('ALTER TABLE "public"."kontraksupplier_m" ADD IF NOT EXISTS "metode_bayar" varchar(255) COLLATE "pg_catalog"."default";');
    $this->execute('ALTER TABLE "public"."kontraksupplier_m" ADD IF NOT EXISTS "dikirim_ke" varchar(64) COLLATE "pg_catalog"."default";');
    $this->execute('ALTER TABLE "public"."kontraksupplier_m" ADD IF NOT EXISTS "contact_person" varchar(64) COLLATE "pg_catalog"."default";');
    $this->execute('ALTER TABLE "public"."kontraksupplier_m" ADD IF NOT EXISTS "catatan" varchar(255) COLLATE "pg_catalog"."default";');

    $this->execute('DROP VIEW if exists "public"."kontraksupplier_v";');
 

    $this->execute('ALTER TABLE "public"."kontraksupplierdetail_m" DROP COLUMN "diskon";');
    $this->execute('ALTER TABLE "public"."kontraksupplierdetail_m" ADD IF NOT EXISTS "pengurang" numeric(15,2) DEFAULT 0;');
    $this->execute('ALTER TABLE "public"."kontraksupplierdetail_m" ADD IF NOT EXISTS "qty_min" numeric(15,2);');
    $this->execute('ALTER TABLE "public"."kontraksupplierdetail_m" ADD IF NOT EXISTS "penambah" numeric(15,2);');
    $this->execute('ALTER TABLE "public"."kontraksupplierdetail_m" ADD IF NOT EXISTS "total_harga" numeric(15,2);');


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
    kontraksupplierdetail_m.satuankecil_id,
    kontraksupplierdetail_m.satuankonv1_id,
    kontraksupplierdetail_m.satuankonv2_id
   FROM (((((kontraksupplier_m
     JOIN supplier_m ON ((kontraksupplier_m.supplier_id = supplier_m.supplier_id)))
     JOIN kontraksupplierdetail_m ON ((kontraksupplier_m.kontraksupplier_id = kontraksupplierdetail_m.kontraksupplier_id)))
     LEFT JOIN satuanunit_m sat_kecil ON ((kontraksupplierdetail_m.satuankecil_id = sat_kecil.satuanunit_id)))
     LEFT JOIN satuanunit_m sat_konv1 ON ((kontraksupplierdetail_m.satuankonv1_id = sat_konv1.satuanunit_id)))
     LEFT JOIN satuanunit_m sat_konv2 ON ((kontraksupplierdetail_m.satuankonv2_id = sat_konv2.satuanunit_id)));");

    $this->execute('ALTER TABLE "public"."kontraksupplier_v" OWNER TO "postgres";');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200914_032220_migrate_20200914_kontraksupplier cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200914_032220_migrate_20200914_kontraksupplier cannot be reverted.\n";

        return false;
    }
    */
}
