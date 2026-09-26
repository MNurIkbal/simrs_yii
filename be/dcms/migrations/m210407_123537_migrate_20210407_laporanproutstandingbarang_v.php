<?php

use yii\db\Migration;

/**
 * Class m210407_123537_migrate_20210407_laporanproutstandingbarang_v
 */
class m210407_123537_migrate_20210407_laporanproutstandingbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanproutstandingbarang_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanproutstandingbarang_v\" AS  SELECT purchasereqbrg_t.no_pr,
    purchasereqbrg_t.created_date,
    purchasereqbrg_t.tgl_pr AS approval_date,
    barang_m.barang_kode AS item_code,
    barang_m.barang_nama AS item_name,
    kelompokbarang_m.kelompokbarang_nama AS category,
    purchasereqbrgdetail_t.qty_input AS qty,
    satuan_1.satuanunit_nama AS uom,
    satuan_1.satuanunit_nama AS from_uom,
    uom.nilai_konversi AS factor,
    satuan_2.satuanunit_nama AS to_uom,
        CASE
            WHEN purchasereqbrg_t.is_prcyto THEN 'CITO'::character varying
            ELSE NULL::character varying
        END AS remarks,
    purchasereqbrg_t.tgl_pr AS create_date
   FROM purchasereqbrg_t
     JOIN purchasereqbrgdetail_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
     LEFT JOIN barang_m ON purchasereqbrgdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     LEFT JOIN satuanunit_m satuan_1 ON purchasereqbrgdetail_t.satuan_id = satuan_1.satuanunit_id
     LEFT JOIN satuanunit_m satuan_2 ON purchasereqbrgdetail_t.satuankonversi_id = satuan_2.satuanunit_id
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
          GROUP BY stokbarang_r.barang_id, stokbarang_r.ruangan_id) barang_sisa ON purchasereqbrgdetail_t.barang_id = barang_sisa.barang_id AND purchasereqbrg_t.ruangan_id = barang_sisa.ruangan_id
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
     JOIN pegawai_m ON purchasereqbrg_t.pegawai_id = pegawai_m.pegawai_id
  WHERE purchasereqbrg_t.is_deleted = false AND purchasereqbrgdetail_t.is_deleted = false AND purchasereqbrgdetail_t.status <> 713;");

        $this->execute('ALTER TABLE "public"."laporanproutstandingbarang_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210407_123537_migrate_20210407_laporanproutstandingbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210407_123537_migrate_20210407_laporanproutstandingbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
