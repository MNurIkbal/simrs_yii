<?php

use yii\db\Migration;

/**
 * Class m210208_053920_migrate_20210208_infopemesananbarangdetail_v
 */
class m210208_053920_migrate_20210208_infopemesananbarangdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW if exists "public"."infopemesananbarangdetail_v";');

    $this->execute("
        CREATE VIEW \"public\".\"infopemesananbarangdetail_v\" AS  SELECT pesanbarangdetail_t.pesanbarangdetail_id,
    pesanbarangdetail_t.pesanbarang_id,
    pesanbarang_t.tgl_pesanbarang,
    pesanbarang_t.tgl_mintadikirim,
    pesanbarang_t.no_pemesanan,
    pesanbarang_t.ruanganpemesan_id,
    ruanganpemesan.ruangan_nama AS ruangan_pemesan,
    pesanbarang_t.ruangantujuan_id,
    ruangantujuan.ruangan_nama AS ruangan_tujuan,
    pesanbarangdetail_t.barang_id,
    barang_m.barang_nama,
    pesanbarangdetail_t.jumlah_input,
    pesanbarangdetail_t.qty_pesan,
    stokbarang_r.qty_tersedia,
    pesanbarangdetail_t.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    pesanbarangdetail_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar,
    pesanbarangdetail_t.is_deleted,
    satuankonversibrg_m.nilai_konversi,
    fgetharganettobarang(pesanbarangdetail_t.barang_id) AS harga_netto,
    COALESCE(stokpemesan.qty_tersedia, 0) AS stok_pemesan
   FROM pesanbarangdetail_t
     JOIN pesanbarang_t ON pesanbarangdetail_t.pesanbarang_id = pesanbarang_t.pesanbarang_id
     LEFT JOIN stokbarang_r ON pesanbarangdetail_t.barang_id = stokbarang_r.barang_id AND pesanbarang_t.ruangantujuan_id = stokbarang_r.ruangan_id
     LEFT JOIN stokbarang_r stokpemesan ON pesanbarangdetail_t.barang_id = stokpemesan.barang_id AND pesanbarang_t.ruanganpemesan_id = stokpemesan.ruangan_id
     JOIN barang_m ON pesanbarangdetail_t.barang_id = barang_m.barang_id
     JOIN ruangan_m ruangantujuan ON pesanbarang_t.ruangantujuan_id = ruangantujuan.ruangan_id
     JOIN ruangan_m ruanganpemesan ON pesanbarang_t.ruanganpemesan_id = ruanganpemesan.ruangan_id
     JOIN satuanunit_m satuan_kecil ON pesanbarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     JOIN satuanunit_m satuan_besar ON pesanbarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
     JOIN satuankonversibrg_m ON pesanbarangdetail_t.satuanbesar_id = satuankonversibrg_m.satuanbesar_id AND pesanbarangdetail_t.satuankecil_id = satuankonversibrg_m.satuankecil_id AND pesanbarangdetail_t.barang_id = satuankonversibrg_m.barang_id
  WHERE pesanbarangdetail_t.is_active = true;");
    
    $this->execute('ALTER TABLE "public"."infopemesananbarangdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210208_053920_migrate_20210208_infopemesananbarangdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210208_053920_migrate_20210208_infopemesananbarangdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
