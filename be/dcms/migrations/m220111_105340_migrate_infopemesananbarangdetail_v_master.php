<?php

use yii\db\Migration;

/**
 * Class m220111_105340_migrate_infopemesananbarangdetail_v_master
 */
class m220111_105340_migrate_infopemesananbarangdetail_v_master extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopemesananbarangdetail_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infopemesananbarangdetail_v\" AS 
	    SELECT pesanbarangdetail_t.pesanbarangdetail_id,
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
	       (kartustok.total - COALESCE(mutasi.jml, (0)::double precision)) AS qty_tersedia,
	       pesanbarangdetail_t.satuankecil_id,
	       satuan_kecil.satuanunit_nama AS satuan_kecil,
	       pesanbarangdetail_t.satuanbesar_id,
	       satuan_besar.satuanunit_nama AS satuan_besar,
	       pesanbarangdetail_t.is_deleted,
	       satuankonversibrg_m.nilai_konversi,
	       fgetharganettobarang(pesanbarangdetail_t.barang_id) AS harga_netto,
	       COALESCE(stokpemesan.qty_tersedia, 0) AS stok_pemesan,
	       kartustok.total AS stok_tujuan
	      FROM (((((((((((pesanbarangdetail_t
	        JOIN pesanbarang_t ON ((pesanbarangdetail_t.pesanbarang_id = pesanbarang_t.pesanbarang_id)))
	        LEFT JOIN stokbarang_r ON (((pesanbarangdetail_t.barang_id = stokbarang_r.barang_id) AND (pesanbarang_t.ruangantujuan_id = stokbarang_r.ruangan_id))))
	        LEFT JOIN stokbarang_r stokpemesan ON (((pesanbarangdetail_t.barang_id = stokpemesan.barang_id) AND (pesanbarang_t.ruanganpemesan_id = stokpemesan.ruangan_id))))
	        JOIN barang_m ON ((pesanbarangdetail_t.barang_id = barang_m.barang_id)))
	        JOIN ruangan_m ruangantujuan ON ((pesanbarang_t.ruangantujuan_id = ruangantujuan.ruangan_id)))
	        JOIN ruangan_m ruanganpemesan ON ((pesanbarang_t.ruanganpemesan_id = ruanganpemesan.ruangan_id)))
	        JOIN satuanunit_m satuan_kecil ON ((pesanbarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
	        JOIN satuanunit_m satuan_besar ON ((pesanbarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id)))
	        JOIN satuankonversibrg_m ON (((pesanbarangdetail_t.satuanbesar_id = satuankonversibrg_m.satuanbesar_id) AND (pesanbarangdetail_t.satuankecil_id = satuankonversibrg_m.satuankecil_id) AND (pesanbarangdetail_t.barang_id = satuankonversibrg_m.barang_id) AND (satuankonversibrg_m.is_deleted IS FALSE) AND (satuankonversibrg_m.is_active IS TRUE))))
	        LEFT JOIN ( SELECT st.ruangan_id,
	               st.barang_id,
	               sum((st.qtystok_in - st.qtystok_out)) AS total
	              FROM stokbarang_t st
	             GROUP BY st.ruangan_id, st.barang_id) kartustok ON (((kartustok.ruangan_id = pesanbarang_t.ruangantujuan_id) AND (kartustok.barang_id = pesanbarangdetail_t.barang_id))))
	        LEFT JOIN ( SELECT mt2.ruanganasal_id AS ruangan_id,
	               mt.barang_id,
	               sum(mt.qty_mutasi) AS jml,
	               string_agg((mt2.nomutasi_barang)::text, ','::text) AS reference
	              FROM (mutasibarangdetail_t mt
	                LEFT JOIN mutasibarang_t mt2 ON ((mt2.mutasibarang_id = mt.mutasibarang_id)))
	             WHERE ((mt.is_deleted IS FALSE) AND (mt2.is_deleted IS FALSE) AND (mt2.status_mutasi = 401))
	             GROUP BY mt2.ruanganasal_id, mt.barang_id) mutasi ON (((mutasi.ruangan_id = pesanbarang_t.ruangantujuan_id) AND (mutasi.barang_id = pesanbarangdetail_t.barang_id))))
	     WHERE (pesanbarangdetail_t.is_active = true);");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220111_105340_migrate_infopemesananbarangdetail_v_master cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220111_105340_migrate_infopemesananbarangdetail_v_master cannot be reverted.\n";

        return false;
    }
    */
}
