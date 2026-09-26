<?php

use yii\db\Migration;

/**
 * Class m220616_073836_migrate_MHG2201_laporanpemakaianbarang_v
 */
class m220616_073836_migrate_MHG2201_laporanpemakaianbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
				$this->execute('DROP VIEW IF EXISTS "public"."laporanpemakaianbarang_v";');
		        $this->execute("
					CREATE VIEW \"public\".\"laporanpemakaianbarang_v\" AS  SELECT pemakaianbarangdetail_t.pemakaianbarangdetail_id,
		    ruangan_m.ruangan_nama,
		    pemakaianbarang_t.tgl_pemakaianbarang AS tgl_transaksi,
		    pemakaianbarang_t.no_pemakaianbarang AS no_transaksi,
		    barang_m.barang_kode,
		    barang_m.barang_nama,
		    kelompokbarang_m.kelompokbarang_nama,
		    subkelompokbarang_m.subkelompok_nama,
		    pemakaianbarangdetail_t.jumlah_pakai AS qty,
		    satuan_kecil.satuanunit_nama AS satuan_kecil,
		    satuan_besar.satuanunit_nama AS satuan_besar,
		    pemakaianbarangdetail_t.harga_netto,
		    COALESCE(pemakaianbarangdetail_t.harga_netto, 0::double precision) * COALESCE(pemakaianbarangdetail_t.jumlah_pakai, 0)::double precision AS total_harga,
		    pegawai_m.nama_pegawai AS \"user\",
		    pemakaianbarangdetail_t.catatan_barang AS catatan
		   FROM pemakaianbarangdetail_t
		     LEFT JOIN ( SELECT barang_m_1.barang_id,
		            barang_m_1.barang_nama,
		            barang_m_1.barang_kode,
		            barang_m_1.kelompokbarang_id,
		            barang_m_1.subkelompokbarang_id
		           FROM barang_m barang_m_1) barang_m ON pemakaianbarangdetail_t.barang_id = barang_m.barang_id
		     LEFT JOIN ( SELECT pemakaianbarang_t_1.pemakaianbarang_id,
		            pemakaianbarang_t_1.no_pemakaianbarang,
		            pemakaianbarang_t_1.pegawai_id,
		            pemakaianbarang_t_1.ruangan_id,
		            pemakaianbarang_t_1.tgl_pemakaianbarang
		           FROM pemakaianbarang_t pemakaianbarang_t_1) pemakaianbarang_t ON pemakaianbarangdetail_t.pemakaianbarang_id = pemakaianbarang_t.pemakaianbarang_id
		     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
		            pegawai_m_1.nama_pegawai
		           FROM pegawai_m pegawai_m_1) pegawai_m ON pemakaianbarang_t.pegawai_id = pegawai_m.pegawai_id
		     LEFT JOIN ( SELECT ruangan_m_1.ruangan_id,
		            ruangan_m_1.ruangan_nama
		           FROM ruangan_m ruangan_m_1) ruangan_m ON pemakaianbarang_t.ruangan_id = ruangan_m.ruangan_id
		     LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
		            satuanunit_m.satuanunit_nama
		           FROM satuanunit_m) satuan_kecil ON pemakaianbarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
		     LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
		            satuanunit_m.satuanunit_nama
		           FROM satuanunit_m) satuan_besar ON pemakaianbarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
		     JOIN ( SELECT kelompokbarang_m_1.kelompokbarang_id,
		            kelompokbarang_m_1.kelompokbarang_nama
		           FROM kelompokbarang_m kelompokbarang_m_1) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
		     JOIN ( SELECT subkelompokbarang_m_1.subkelompokbarang_id,
		            subkelompokbarang_m_1.subkelompok_nama
		           FROM subkelompokbarang_m subkelompokbarang_m_1) subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
		  WHERE pemakaianbarangdetail_t.is_active = true AND pemakaianbarangdetail_t.is_deleted = false
		        ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220616_073836_migrate_MHG2201_laporanpemakaianbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220616_073836_migrate_MHG2201_laporanpemakaianbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
