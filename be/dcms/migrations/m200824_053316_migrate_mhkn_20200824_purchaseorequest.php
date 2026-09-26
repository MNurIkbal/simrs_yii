<?php

use yii\db\Migration;

/**
 * Class m200824_053316_migrate_mhkn_20200824_purchaseorequest
 */
class m200824_053316_migrate_mhkn_20200824_purchaseorequest extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN "status" int2 DEFAULT 712;');
         $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN "alasan" text COLLATE "pg_catalog"."default";');
         $this->execute('COMMENT ON COLUMN "public"."purchasereqdetail_t"."status" IS \'lookup_type=status_purchaserequest\';');

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
    purchasereqdetail_t.alasan
   FROM (((((((purchasereq_t
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
          GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuankecil_id, satuankonversi_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m.nilai_konversi) uom ON (((purchasereqdetail_t.obatalkes_id = uom.obatalkes_id) AND (obatalkes_m.satuanbesar_id = uom.satuanbesar_id) AND (obatalkes_m.satuankecil_id = uom.satuankecil_id))))
     LEFT JOIN ( SELECT stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.ruangan_id,
            sum(stokobatalkes_r.qty_sisa) AS qty_sisa
           FROM stokobatalkes_r
          GROUP BY stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id) obat_sisa ON (((purchasereqdetail_t.obatalkes_id = obat_sisa.obatalkes_id) AND (purchasereq_t.ruangan_id = obat_sisa.ruangan_id))))
  WHERE ((purchasereq_t.is_deleted = false) AND (purchasereqdetail_t.is_deleted = false));");

         $this->execute('ALTER TABLE "public"."infopurchasereqdetail_v" OWNER TO "postgres";');

         $this->execute('DELETE from lookup_m WHERE lookup_type=\'status_purchaserequest\'');

         $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(712, 'status_purchaserequest', 'Belum PO', 'Belum PO', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(713, 'status_purchaserequest', 'Sudah PO', 'Sudah PO', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(719, 'status_purchaserequest', 'PO Sebagian', 'PO Sebagian', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(720, 'status_purchaserequest', 'Batal PR', 'Batal PR', 4, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200824_053316_migrate_mhkn_20200824_purchaseorequest cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200824_053316_migrate_mhkn_20200824_purchaseorequest cannot be reverted.\n";

        return false;
    }
    */
}
