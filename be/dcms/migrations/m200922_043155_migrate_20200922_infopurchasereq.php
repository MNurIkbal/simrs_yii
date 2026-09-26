<?php

use yii\db\Migration;

/**
 * Class m200922_043155_migrate_20200922_infopurchasereq
 */
class m200922_043155_migrate_20200922_infopurchasereq extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopurchasereqdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqdetail_v\" AS  SELECT purchasereq_t.purchasereq_id,
    purchasereqdetail_t.purchasereqdetail_id,
    purchasereq_t.no_pr,
    purchasereqdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    purchasereqdetail_t.qty_input,
    purchasereqdetail_t.qty_konversi,
    purchasereqdetail_t.satuan_id,
    satuan_1.satuanunit_nama AS satuan,
    purchasereqdetail_t.satuankonversi_id,
    satuan_2.satuanunit_nama AS satuan_konversi,
    purchasereqdetail_t.catatan,
    COALESCE(obat_sisa.qty_sisa, (0)::double precision) AS stok,
    satuan_stok.satuanunit_nama AS satuan_stok,
    fgetnamalookup((purchasereqdetail_t.status)::integer) AS status,
    purchasereqdetail_t.status AS status_id,
    uom.nilai_konversi,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    purchasereqdetail_t.alasan,
    kontrak_supplier.kontraksupplierdetail_id,
        CASE
            WHEN (kontrak_supplier.kontraksupplierdetail_id IS NOT NULL) THEN kontrak_supplier.satuankonv1_id
            WHEN (kontrak_supplier.kontraksupplierdetail_id IS NULL) THEN uom.satuanbesar_id
            ELSE NULL::integer
        END AS satuanbesar_id,
        CASE
            WHEN (kontrak_supplier.kontraksupplierdetail_id IS NOT NULL) THEN kontrak_supplier.satuan_besar
            WHEN (kontrak_supplier.kontraksupplierdetail_id IS NULL) THEN uom.uom_besar
            ELSE NULL::character varying
        END AS satuan_besar
   FROM ((((((((purchasereq_t
     JOIN purchasereqdetail_t ON ((purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id)))
     LEFT JOIN obatalkes_m ON ((purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_1 ON ((purchasereqdetail_t.satuan_id = satuan_1.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_2 ON ((purchasereqdetail_t.satuankonversi_id = satuan_2.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_stok ON ((obatalkes_m.satuankecil_id = satuan_stok.satuanunit_id)))
     LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m.nilai_konversi
           FROM ((satuankonversi_m
             LEFT JOIN satuanunit_m uom_besar ON ((satuankonversi_m.satuanbesar_id = uom_besar.satuanunit_id)))
             LEFT JOIN satuanunit_m uom_kecil ON ((satuankonversi_m.satuankecil_id = uom_kecil.satuanunit_id)))
          WHERE ((satuankonversi_m.is_deleted = false) AND (satuankonversi_m.is_active = true))
          GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuankecil_id, satuankonversi_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m.nilai_konversi) uom ON (((purchasereqdetail_t.obatalkes_id = uom.obatalkes_id) AND (purchasereqdetail_t.satuan_id = uom.satuanbesar_id) AND (purchasereqdetail_t.satuankonversi_id = uom.satuankecil_id))))
     LEFT JOIN ( SELECT stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.ruangan_id,
            sum(stokobatalkes_r.qty_sisa) AS qty_sisa
           FROM stokobatalkes_r
          GROUP BY stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id) obat_sisa ON (((purchasereqdetail_t.obatalkes_id = obat_sisa.obatalkes_id) AND (purchasereq_t.ruangan_id = obat_sisa.ruangan_id))))
     LEFT JOIN ( SELECT kontraksupplierdetail_m.obatalkes_id,
            kontraksupplierdetail_m.satuankecil_id,
            kontraksupplierdetail_m.satuankonv1_id,
            array_agg(kontraksupplierdetail_m.kontraksupplierdetail_id) AS kontraksupplierdetail_id,
            satuan_besar.satuanunit_nama AS satuan_besar
           FROM (kontraksupplierdetail_m
             LEFT JOIN satuanunit_m satuan_besar ON ((kontraksupplierdetail_m.satuankonv1_id = satuan_besar.satuanunit_id)))
          WHERE ((kontraksupplierdetail_m.is_deleted = false) AND (kontraksupplierdetail_m.is_active = true))
          GROUP BY kontraksupplierdetail_m.obatalkes_id, kontraksupplierdetail_m.satuankecil_id, kontraksupplierdetail_m.satuankonv1_id, satuan_besar.satuanunit_nama) kontrak_supplier ON (((purchasereqdetail_t.obatalkes_id = kontrak_supplier.obatalkes_id) AND (purchasereqdetail_t.satuan_id = kontrak_supplier.satuankonv1_id) AND (purchasereqdetail_t.satuankonversi_id = kontrak_supplier.satuankecil_id))))
  WHERE ((purchasereq_t.is_deleted = false) AND (purchasereqdetail_t.is_deleted = false));");

        $this->execute('ALTER TABLE "public"."infopurchasereqdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200922_043155_migrate_20200922_infopurchasereq cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200922_043155_migrate_20200922_infopurchasereq cannot be reverted.\n";

        return false;
    }
    */
}
