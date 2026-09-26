<?php

use yii\db\Migration;

/**
 * Class m210329_065239_migrate_20210329_infopurchasereqgabungdetail_v
 */
class m210329_065239_migrate_20210329_infopurchasereqgabungdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopurchasereqgabungdetail_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqgabungdetail_v\" AS  SELECT 'OBAT'::text AS tipe,
    purchasereq_t.purchasereq_id,
    purchasereqdetail_t.purchasereqdetail_id,
    purchasereq_t.no_pr,
    purchasereqdetail_t.obatalkes_id AS item_id,
    obatalkes_m.obatalkes_nama AS item_nama,
    purchasereqdetail_t.qty_input,
    purchasereqdetail_t.qty_konversi,
    purchasereqdetail_t.satuan_id,
    satuan_1.satuanunit_nama AS satuan,
    purchasereqdetail_t.satuankonversi_id,
    satuan_2.satuanunit_nama AS satuan_konversi,
    purchasereqdetail_t.catatan,
    COALESCE(obat_sisa.qty_sisa, 0::double precision) AS stok,
    satuan_stok.satuanunit_nama AS satuan_stok,
    fgetnamalookup(purchasereqdetail_t.status::integer) AS status,
    purchasereqdetail_t.status AS status_id,
    uom.nilai_konversi,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    purchasereqdetail_t.alasan,
    kontrak_supplier.kontraksupplierdetail_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NOT NULL THEN kontrak_supplier.satuankonv1_id
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NULL THEN uom.satuanbesar_id
            ELSE NULL::integer
        END AS satuanbesar_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NOT NULL THEN kontrak_supplier.satuan_besar
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NULL THEN uom.uom_besar
            ELSE NULL::character varying
        END AS satuan_besar,
    obatalkes_m.obatalkes_kode AS item_kode,
    po.no_poobat AS nomor_po,
    obatalkes_m.harganetto AS baseprice,
    obatalkes_m.supplier_id AS defaultsupplier_id,
    defaultsupplier.pajak_id AS defaultsupplierpajak_id,
    defaultsupplierpajak.pajak_persen AS defaultsupplierpajakpersen,
    obat_sisa.min_stok,
    obat_sisa.max_stok
   FROM purchasereq_t
     JOIN purchasereqdetail_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     LEFT JOIN obatalkes_m ON purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN supplier_m defaultsupplier ON defaultsupplier.supplier_id = obatalkes_m.supplier_id
     LEFT JOIN pajak_m defaultsupplierpajak ON defaultsupplierpajak.pajak_id = defaultsupplier.pajak_id
     LEFT JOIN satuanunit_m satuan_1 ON purchasereqdetail_t.satuan_id = satuan_1.satuanunit_id
     LEFT JOIN satuanunit_m satuan_2 ON purchasereqdetail_t.satuankonversi_id = satuan_2.satuanunit_id
     LEFT JOIN satuanunit_m satuan_stok ON obatalkes_m.satuankecil_id = satuan_stok.satuanunit_id
     LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m.nilai_konversi
           FROM satuankonversi_m
             LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversi_m.is_deleted = false AND satuankonversi_m.is_active = true
          GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuankecil_id, satuankonversi_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m.nilai_konversi) uom ON purchasereqdetail_t.obatalkes_id = uom.obatalkes_id AND purchasereqdetail_t.satuan_id = uom.satuanbesar_id AND purchasereqdetail_t.satuankonversi_id = uom.satuankecil_id
     LEFT JOIN ( SELECT stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.ruangan_id,
            konfigrak_m.min_stok,
            konfigrak_m.max_stok,
            sum(stokobatalkes_r.qty_sisa) AS qty_sisa
           FROM stokobatalkes_r
             JOIN konfigrak_m ON stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id
          GROUP BY stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id, konfigrak_m.min_stok, konfigrak_m.max_stok) obat_sisa ON purchasereqdetail_t.obatalkes_id = obat_sisa.obatalkes_id AND purchasereq_t.ruangan_id = obat_sisa.ruangan_id
     LEFT JOIN ( SELECT kontraksupplierdetail_m.obatalkes_id,
            kontraksupplierdetail_m.satuankecil_id,
            kontraksupplierdetail_m.satuankonv1_id,
            array_agg(kontraksupplierdetail_m.kontraksupplierdetail_id) AS kontraksupplierdetail_id,
            satuan_besar.satuanunit_nama AS satuan_besar
           FROM kontraksupplierdetail_m
             LEFT JOIN satuanunit_m satuan_besar ON kontraksupplierdetail_m.satuankonv1_id = satuan_besar.satuanunit_id
          WHERE kontraksupplierdetail_m.is_deleted = false AND kontraksupplierdetail_m.is_active = true
          GROUP BY kontraksupplierdetail_m.obatalkes_id, kontraksupplierdetail_m.satuankecil_id, kontraksupplierdetail_m.satuankonv1_id, satuan_besar.satuanunit_nama) kontrak_supplier ON purchasereqdetail_t.obatalkes_id = kontrak_supplier.obatalkes_id AND purchasereqdetail_t.satuan_id = kontrak_supplier.satuankonv1_id AND purchasereqdetail_t.satuankonversi_id = kontrak_supplier.satuankecil_id
     LEFT JOIN ( SELECT validasipoobat_t.no_poobat,
            validasipoobatdetail_t.purchasereqdetail_id
           FROM validasipoobat_t
             JOIN validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
          WHERE validasipoobat_t.is_deleted = false AND validasipoobatdetail_t.is_deleted = false) po ON purchasereqdetail_t.purchasereqdetail_id = po.purchasereqdetail_id
  WHERE purchasereq_t.is_deleted = false AND purchasereqdetail_t.is_deleted = false
UNION ALL
 SELECT 'BARANG'::text AS tipe,
    purchasereqbrg_t.purchasereqbrg_id AS purchasereq_id,
    purchasereqbrgdetail_t.purchasereqbrgdetail_id AS purchasereqdetail_id,
    purchasereqbrg_t.no_pr,
    purchasereqbrgdetail_t.barang_id AS item_id,
    barang_m.barang_nama AS item_nama,
    purchasereqbrgdetail_t.qty_input,
    purchasereqbrgdetail_t.qty_konversi,
    purchasereqbrgdetail_t.satuan_id,
    uom.uom_besar AS satuan,
    purchasereqbrgdetail_t.satuankonversi_id,
    uom.uom_kecil AS satuan_konversi,
    purchasereqbrgdetail_t.catatan,
    COALESCE(obat_sisa.qty_sisa::double precision, 0::double precision) AS stok,
    satuan_stok.satuanunit_nama AS satuan_stok,
    fgetnamalookup(purchasereqbrgdetail_t.status::integer) AS status,
    purchasereqbrgdetail_t.status AS status_id,
    uom.nilai_konversi,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    purchasereqbrgdetail_t.alasan,
    kontrak_supplier.kontraksupplierbrgdetail_id AS kontraksupplierdetail_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NOT NULL THEN kontrak_supplier.satuankonv1_id
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NULL THEN uom.satuanbesar_id
            ELSE NULL::integer
        END AS satuanbesar_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NOT NULL THEN kontrak_supplier.satuan_besar
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NULL THEN uom.uom_besar
            ELSE NULL::character varying
        END AS satuan_besar,
    barang_m.barang_kode AS item_kode,
    po.no_pobarang AS nomor_po,
    barang_m.barang_harganetto AS baseprice,
    barang_m.supplier_id AS defaultsupplier_id,
    defaultsupplier.pajak_id AS defaultsupplierpajak_id,
    defaultsupplierpajak.pajak_persen AS defaultsupplierpajakpersen,
    0 AS min_stok,
    0 AS max_stok
   FROM purchasereqbrg_t
     JOIN purchasereqbrgdetail_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
     LEFT JOIN barang_m ON purchasereqbrgdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN supplier_m defaultsupplier ON defaultsupplier.supplier_id = barang_m.supplier_id
     LEFT JOIN pajak_m defaultsupplierpajak ON defaultsupplierpajak.pajak_id = defaultsupplier.pajak_id
     LEFT JOIN satuanunit_m satuan_stok ON barang_m.satuankecil_id = satuan_stok.satuanunit_id
     LEFT JOIN ( SELECT satuankonversibrg_m.barang_id,
            satuankonversibrg_m.satuankecil_id,
            satuankonversibrg_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversibrg_m.nilai_konversi
           FROM satuankonversibrg_m
             LEFT JOIN satuanunit_m uom_besar ON satuankonversibrg_m.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversibrg_m.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversibrg_m.is_deleted = false AND satuankonversibrg_m.is_active = true
          GROUP BY satuankonversibrg_m.barang_id, satuankonversibrg_m.satuankecil_id, satuankonversibrg_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversibrg_m.nilai_konversi) uom ON purchasereqbrgdetail_t.barang_id = uom.barang_id AND purchasereqbrgdetail_t.satuan_id = uom.satuanbesar_id AND purchasereqbrgdetail_t.satuankonversi_id = uom.satuankecil_id
     LEFT JOIN ( SELECT stokbarang_r.barang_id,
            stokbarang_r.ruangan_id,
            sum(stokbarang_r.qty_sisa) AS qty_sisa
           FROM stokbarang_r
          GROUP BY stokbarang_r.barang_id, stokbarang_r.ruangan_id) obat_sisa ON purchasereqbrgdetail_t.barang_id = obat_sisa.barang_id AND purchasereqbrg_t.ruangan_id = obat_sisa.ruangan_id
     LEFT JOIN ( SELECT kontraksupplierbrgdetail_m.barang_id,
            kontraksupplierbrgdetail_m.satuankecil_id,
            kontraksupplierbrgdetail_m.satuankonv1_id,
            array_agg(kontraksupplierbrgdetail_m.kontraksupplierbrgdetail_id) AS kontraksupplierbrgdetail_id,
            satuan_besar.satuanunit_nama AS satuan_besar
           FROM kontraksupplierbrgdetail_m
             LEFT JOIN satuanunit_m satuan_besar ON kontraksupplierbrgdetail_m.satuankonv1_id = satuan_besar.satuanunit_id
          WHERE kontraksupplierbrgdetail_m.is_deleted = false AND kontraksupplierbrgdetail_m.is_active = true
          GROUP BY kontraksupplierbrgdetail_m.barang_id, kontraksupplierbrgdetail_m.satuankecil_id, kontraksupplierbrgdetail_m.satuankonv1_id, satuan_besar.satuanunit_nama) kontrak_supplier ON purchasereqbrgdetail_t.barang_id = kontrak_supplier.barang_id AND purchasereqbrgdetail_t.satuan_id = kontrak_supplier.satuankonv1_id AND purchasereqbrgdetail_t.satuankonversi_id = kontrak_supplier.satuankecil_id
     LEFT JOIN ( SELECT validasipobarang_t.no_pobarang,
            validasipobarangdetail_t.purchasereqbrgdetail_id
           FROM validasipobarang_t
             JOIN validasipobarangdetail_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
          WHERE validasipobarang_t.is_deleted = false AND validasipobarangdetail_t.is_deleted = false) po ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = po.purchasereqbrgdetail_id
  WHERE purchasereqbrg_t.is_deleted = false AND purchasereqbrgdetail_t.is_deleted = false;
");

        $this->execute('ALTER TABLE "public"."infopurchasereqgabungdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210329_065239_migrate_20210329_infopurchasereqgabungdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210329_065239_migrate_20210329_infopurchasereqgabungdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
