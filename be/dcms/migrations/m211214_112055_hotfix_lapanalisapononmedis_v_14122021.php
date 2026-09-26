<?php

use yii\db\Migration;

/**
 * Class m211214_112055_hotfix_lapanalisapononmedis_v_14122021
 */
class m211214_112055_hotfix_lapanalisapononmedis_v_14122021 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
 	   	$this->execute('DROP VIEW if exists public.lapanalisapononmedis_v;');
		
        $this->execute("
            CREATE VIEW \"public\".\"lapanalisapononmedis_v\" AS
	    	SELECT adjusmenbarang_t.adjusmenbarang_id,
	       adjusmenbarang_t.ruangan_adjusmen_id,
	       ruangan_m.ruangan_nama,
	       adjusmenbarang_t.no_adjusmen,
	       adjusmenbarang_t.tgl_adjusmen,
	       adjusmenbarang_t.jenis_adjusmen,
	           CASE
	               WHEN (adjusmenbarang_t.jenis_adjusmen = 0) THEN 'Masuk'::text
	               ELSE 'Keluar'::text
	           END AS jenis_adjusmen_nama,
	       barang_m.barang_kode,
	       barang_m.barang_nama,
	       adjusmenbarangmasuk_t.qty,
	       adjusmenbarangmasuk_t.satuankecil_id,
	       adjusmenbarangmasuk_t.satuanbesar_id,
	       adjusmenbarangmasuk_t.qty_konversi,
	       satuan.satuanunit_nama AS satuan_kecil,
	       satuan_masuk.satuanunit_nama AS satuan_besar,
	       peg_adjusmen.nama_pegawai AS pegawai_adjusmen
	      FROM (((((((((adjusmenbarang_t
	        JOIN ( SELECT a.ruangan_id,
	               a.ruangan_nama
	              FROM ruangan_m a) ruangan_m ON ((adjusmenbarang_t.ruangan_adjusmen_id = ruangan_m.ruangan_id)))
	        LEFT JOIN ( SELECT a.pegawai_id,
	               a.nama_pegawai
	              FROM pegawai_m a) peg_mengetahui ON ((adjusmenbarang_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
	        LEFT JOIN ( SELECT a.pegawai_id,
	               a.nama_pegawai
	              FROM pegawai_m a) peg_menyetujui ON ((adjusmenbarang_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
	        LEFT JOIN ( SELECT a.loginpemakai_id,
	               a.pegawai_id
	              FROM loginpemakai_k a) loginpemakai_k ON ((adjusmenbarang_t.created_by = loginpemakai_k.loginpemakai_id)))
	        LEFT JOIN ( SELECT a.pegawai_id,
	               a.nama_pegawai
	              FROM pegawai_m a) peg_adjusmen ON ((loginpemakai_k.pegawai_id = peg_adjusmen.pegawai_id)))
	        JOIN ( SELECT a.adjusmenbarang_id,
	               a.barang_id,
	               a.qty,
	               a.satuankecil_id,
	               a.satuanbesar_id,
	               a.qty_konversi,
	               a.satuankonversibrg_id
	              FROM adjusmenbarangmasuk_t a
	             WHERE (a.is_deleted = false)) adjusmenbarangmasuk_t ON ((adjusmenbarang_t.adjusmenbarang_id = adjusmenbarangmasuk_t.adjusmenbarang_id)))
	        JOIN ( SELECT a.barang_id,
	               a.barang_kode,
	               a.barang_nama
	              FROM barang_m a) barang_m ON ((adjusmenbarangmasuk_t.barang_id = barang_m.barang_id)))
	        JOIN ( SELECT a.satuanunit_id,
	               a.satuanunit_nama
	              FROM satuanunit_m a) satuan ON ((adjusmenbarangmasuk_t.satuankecil_id = satuan.satuanunit_id)))
	        JOIN ( SELECT a.satuanunit_id,
	               a.satuanunit_nama
	              FROM satuanunit_m a) satuan_masuk ON ((adjusmenbarangmasuk_t.satuankecil_id = satuan_masuk.satuanunit_id)))
	     WHERE (adjusmenbarang_t.is_deleted = false)
	   UNION ALL
	    SELECT adjusmenbarang_t.adjusmenbarang_id,
	       adjusmenbarang_t.ruangan_adjusmen_id,
	       ruangan_m.ruangan_nama,
	       adjusmenbarang_t.no_adjusmen,
	       adjusmenbarang_t.tgl_adjusmen,
	       adjusmenbarang_t.jenis_adjusmen,
	           CASE
	               WHEN (adjusmenbarang_t.jenis_adjusmen = 0) THEN 'Masuk'::text
	               ELSE 'Keluar'::text
	           END AS jenis_adjusmen_nama,
	       barang_m.barang_kode,
	       barang_m.barang_nama,
	       adjusmenbarangkeluar_t.qty,
	       adjusmenbarangkeluar_t.satuankecil_id,
	       adjusmenbarangkeluar_t.satuanbesar_id,
	       adjusmenbarangkeluar_t.qty_konversi,
	       satuan.satuanunit_nama AS satuan_kecil,
	       satuan_keluar.satuanunit_nama AS satuan_besar,
	       peg_adjusmen.nama_pegawai AS pegawai_adjusmen
	      FROM (((((((((adjusmenbarang_t
	        JOIN ( SELECT a.ruangan_id,
	               a.ruangan_nama
	              FROM ruangan_m a) ruangan_m ON ((adjusmenbarang_t.ruangan_adjusmen_id = ruangan_m.ruangan_id)))
	        LEFT JOIN ( SELECT a.pegawai_id,
	               a.nama_pegawai
	              FROM pegawai_m a) peg_mengetahui ON ((adjusmenbarang_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
	        LEFT JOIN ( SELECT a.pegawai_id,
	               a.nama_pegawai
	              FROM pegawai_m a) peg_menyetujui ON ((adjusmenbarang_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
	        LEFT JOIN ( SELECT a.loginpemakai_id,
	               a.pegawai_id
	              FROM loginpemakai_k a) loginpemakai_k ON ((adjusmenbarang_t.created_by = loginpemakai_k.loginpemakai_id)))
	        LEFT JOIN ( SELECT a.pegawai_id,
	               a.nama_pegawai
	              FROM pegawai_m a) peg_adjusmen ON ((loginpemakai_k.pegawai_id = peg_adjusmen.pegawai_id)))
	        JOIN ( SELECT a.adjusmenbarang_id,
	               a.barang_id,
	               a.qty,
	               a.satuankecil_id,
	               a.satuanbesar_id,
	               a.qty_konversi,
	               a.satuankonversibrg_id
	              FROM adjusmenbarangkeluar_t a
	             WHERE (a.is_deleted = false)) adjusmenbarangkeluar_t ON ((adjusmenbarang_t.adjusmenbarang_id = adjusmenbarangkeluar_t.adjusmenbarang_id)))
	        JOIN ( SELECT a.barang_id,
	               a.barang_kode,
	               a.barang_nama
	              FROM barang_m a) barang_m ON ((adjusmenbarangkeluar_t.barang_id = barang_m.barang_id)))
	        JOIN ( SELECT a.satuanunit_id,
	               a.satuanunit_nama
	              FROM satuanunit_m a) satuan ON ((adjusmenbarangkeluar_t.satuankecil_id = satuan.satuanunit_id)))
	        JOIN ( SELECT a.satuanunit_id,
	               a.satuanunit_nama
	              FROM satuanunit_m a) satuan_keluar ON ((adjusmenbarangkeluar_t.satuankecil_id = satuan_keluar.satuanunit_id)))
	     WHERE (adjusmenbarang_t.is_deleted = false);");
    
            $this->execute('
                ALTER TABLE public.lapanalisapononmedis_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211214_112055_hotfix_lapanalisapononmedis_v_14122021 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211214_112055_hotfix_lapanalisapononmedis_v_14122021 cannot be reverted.\n";

        return false;
    }
    */
}
