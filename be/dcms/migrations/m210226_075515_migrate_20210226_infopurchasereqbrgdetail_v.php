<?php

use yii\db\Migration;

/**
 * Class m210226_075515_migrate_20210226_infopurchasereqbrgdetail_v
 */
class m210226_075515_migrate_20210226_infopurchasereqbrgdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqbrgdetail_v\" AS  SELECT purchasereqbrg_t.purchasereqbrg_id,
    purchasereqbrgdetail_t.purchasereqbrgdetail_id,
    purchasereqbrg_t.no_pr,
    purchasereqbrgdetail_t.barang_id,
    barang_m.barang_nama,
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
    kontrak_supplier.kontraksupplierbrgdetail_id,
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
    barang_m.barang_kode AS kode_barang,
    po.no_pobarang AS nomor_po
   FROM purchasereqbrg_t
     JOIN purchasereqbrgdetail_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
     LEFT JOIN barang_m ON purchasereqbrgdetail_t.barang_id = barang_m.barang_id
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
  WHERE purchasereqbrg_t.is_deleted = false AND purchasereqbrgdetail_t.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."infopurchasereqbrgdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_075515_migrate_20210226_infopurchasereqbrgdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_075515_migrate_20210226_infopurchasereqbrgdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
