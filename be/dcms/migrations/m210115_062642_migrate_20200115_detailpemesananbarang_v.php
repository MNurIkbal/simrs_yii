<?php

use yii\db\Migration;

/**
 * Class m210115_062642_migrate_20200115_detailpemesananbarang_v
 */
class m210115_062642_migrate_20200115_detailpemesananbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.detailpemesananbarang_v;');

        $this->execute("
            CREATE VIEW \"public\".\"detailpemesananbarang_v\" AS  SELECT pesanbarang_t.pesanbarang_id,
    pesanbarang_t.tgl_pesanbarang,
    pesanbarang_t.ruangantujuan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanbarang_t.no_pemesanan,
    pesanbarang_t.keterangan_pesan,
    pesanbarang_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    pesanbarangdetail_t.barang_id,
    barang_m.barang_namalainnya,
    barang_m.barang_nama,
    pesanbarangdetail_t.qty_pesan,
    COALESCE(qty_diterima.qty_diterima, 0::double precision) AS jumlah_diterima,
    pesanbarang_t.tgl_mintadikirim,
    mutasi.mutasibarangdetail_id,
    pesanbarangdetail_t.satuankecil_id,
    pesanbarangdetail_t.pesanbarangdetail_id,
    pesanbarangdetail_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    pesanbarangdetail_t.jumlah_input AS qty_besar,
    barang_m.barang_harganetto AS harga_netto,
    barang_m.barang_max AS hargamaksimum,
    barang_m.barang_min AS hargaminimum,
    barang_m.barang_average AS hargaratarata,
    barang_m.barang_persendiskon,
    pesanbarangdetail_t.qty_pesan AS qty_kecil,
    pesanbarang_t.statuspesan,
    COALESCE(stok_pengirim.qty_tersedia::double precision, 0::double precision) AS stok_pengirim,
    COALESCE(stok_pemesan.qty_tersedia::double precision, 0::double precision) AS stok_pemesan,
    mutasi.jumlah_mutasi,
    mutasi.satuan_kirim,
    COALESCE(mutasi.jumlah_mutasi, 0::double precision) / (COALESCE(NULLIF(pesanbarangdetail_t.qty_pesan, 0::double precision), 1::double precision) / COALESCE(NULLIF(pesanbarangdetail_t.jumlah_input, 0::double precision), 1::double precision)) AS jumlah_input_mutasi
   FROM pesanbarangdetail_t
     JOIN pesanbarang_t ON pesanbarangdetail_t.pesanbarang_id = pesanbarang_t.pesanbarang_id
     JOIN ruangan_m ON pesanbarang_t.ruangantujuan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangpemesan ON pesanbarang_t.ruanganpemesan_id = ruangpemesan.ruangan_id
     JOIN instalasi_m instalasipesan ON ruangpemesan.instalasi_id = instalasipesan.instalasi_id
     JOIN barang_m ON pesanbarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN satuanunit_m satuan_besar ON pesanbarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
     LEFT JOIN satuanunit_m satuan_kecil ON pesanbarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN ( SELECT stokbarang_r.ruangan_id,
            stokbarang_r.barang_id,
            stokbarang_r.qty_tersedia
           FROM stokbarang_r
          WHERE stokbarang_r.is_deleted = false) stok_pengirim ON pesanbarang_t.ruangantujuan_id = stok_pengirim.ruangan_id AND pesanbarangdetail_t.barang_id = stok_pengirim.barang_id
     LEFT JOIN ( SELECT stokbarang_r.ruangan_id,
            stokbarang_r.barang_id,
            stokbarang_r.qty_tersedia
           FROM stokbarang_r
          WHERE stokbarang_r.is_deleted = false) stok_pemesan ON pesanbarang_t.ruanganpemesan_id = stok_pemesan.ruangan_id AND pesanbarangdetail_t.barang_id = stok_pemesan.ruangan_id
     LEFT JOIN ( SELECT mutasibarangdetail_t.pesanbarangdetail_id,
            terimamutasibarangdetail_t.jmlterima AS qty_diterima
           FROM terimamutasibarangdetail_t
             JOIN mutasibarangdetail_t ON terimamutasibarangdetail_t.mutasibarangdetail_id = mutasibarangdetail_t.mutasibarangdetail_id
             JOIN mutasibarang_t ON mutasibarangdetail_t.mutasibarang_id = mutasibarang_t.mutasibarang_id
          WHERE mutasibarang_t.status_mutasi = 400) qty_diterima ON pesanbarangdetail_t.pesanbarangdetail_id = qty_diterima.pesanbarangdetail_id
     LEFT JOIN ( SELECT mutasibarangdetail_t.pesanbarangdetail_id,
            mutasibarangdetail_t.mutasibarangdetail_id,
            mutasibarangdetail_t.jumlah_input,
            mutasibarangdetail_t.qty_mutasi AS jumlah_mutasi,
            mutasibarangdetail_t.satuankecil_id,
            satuanunit_m.satuanunit_nama AS satuan_kirim
           FROM mutasibarangdetail_t
             LEFT JOIN satuanunit_m ON mutasibarangdetail_t.satuankecil_id = satuanunit_m.satuanunit_id) mutasi ON pesanbarangdetail_t.pesanbarangdetail_id = mutasi.pesanbarangdetail_id
  WHERE pesanbarangdetail_t.is_active = true AND pesanbarangdetail_t.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."detailpemesananbarang_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210115_062642_migrate_20200115_detailpemesananbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210115_062642_migrate_20200115_detailpemesananbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
