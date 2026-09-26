<?php

use yii\db\Migration;

/**
 * Class m211222_124225_migrate_adjustmenobatalkes_master22122021
 */
class m211222_124225_migrate_adjustmenobatalkes_master22122021 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
  	   	$this->execute('DROP VIEW if exists public.laporanadjustmentobatalkes_v;');
		
         $this->execute("
             CREATE VIEW \"public\".\"laporanadjustmentobatalkes_v\" AS
		 SELECT adjusmenobat_t.adjusmenobat_id,
		    adjusmenobat_t.ruangan_adjusmen_id,
		    ruangan_m.ruangan_nama,
		    adjusmenobat_t.tgl_adjusmen,
		    adjusmenobat_t.no_adjusmen,
		    adjusmenobat_t.jenis_adjusmen,
		        CASE
		            WHEN (adjusmenobat_t.jenis_adjusmen = 0) THEN 'Masuk'::text
		            ELSE 'Keluar'::text
		        END AS jenis_adjusmen_nama,
		    jenisobatalkes_m.jenisobatalkes_id,
		    jenisobatalkes_m.jenisobatalkes_kode,
		    jenisobatalkes_m.jenisobatalkes_nama,
		    obatalkes_m.obatalkes_kode,
		    obatalkes_m.obatalkes_nama,
		    adjusmenobatmasuk_t.qty AS qty_input,
		    adjusmenobatmasuk_t.qty_konversi,
		    adjusmenobatmasuk_t.satuankecil_id,
		    adjusmenobatmasuk_t.satuanbesar_id,
		    satuan_masuk_kecil.satuanunit_nama AS satuan_kecil,
		    satuan_masuk_besar.satuanunit_nama AS satuan_besar,
		    peg_adjusmen.nama_pegawai AS pegawai_adjusmen
		   FROM (((((((((((adjusmenobat_t
		     LEFT JOIN ( SELECT a.pegawai_id,
		            a.nama_pegawai
		           FROM pegawai_m a) peg_mengetahui ON ((adjusmenobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
		     LEFT JOIN ( SELECT a.pegawai_id,
		            a.nama_pegawai
		           FROM pegawai_m a) peg_menyetujui ON ((adjusmenobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
		     JOIN ( SELECT a.ruangan_id,
		            a.ruangan_nama
		           FROM ruangan_m a) ruangan_m ON ((adjusmenobat_t.ruangan_adjusmen_id = ruangan_m.ruangan_id)))
		     LEFT JOIN ( SELECT a.loginpemakai_id,
		            a.pegawai_id
		           FROM loginpemakai_k a) loginpemakai_k ON ((adjusmenobat_t.created_by = loginpemakai_k.loginpemakai_id)))
		     LEFT JOIN ( SELECT a.pegawai_id,
		            a.nama_pegawai
		           FROM pegawai_m a) peg_adjusmen ON ((loginpemakai_k.pegawai_id = peg_adjusmen.pegawai_id)))
		     JOIN ( SELECT a.adjusmenobat_id,
		            a.obatalkes_id,
		            a.satuankecil_id,
		            a.satuanbesar_id,
		            a.adjusmenobatmasuk_id,
		            a.qty,
		            a.qty_konversi,
		            a.tgl_kadaluarsa,
		            a.harga_netto,
		            a.no_batch,
		            a.keterangan
		           FROM adjusmenobatmasuk_t a
		          WHERE (a.is_deleted = false)) adjusmenobatmasuk_t ON ((adjusmenobat_t.adjusmenobat_id = adjusmenobatmasuk_t.adjusmenobat_id)))
		     JOIN ( SELECT a.obatalkes_id,
		            a.obatalkes_kode,
		            a.obatalkes_nama,
		            a.jenisobatalkes_id
		           FROM obatalkes_m a) obatalkes_m ON ((adjusmenobatmasuk_t.obatalkes_id = obatalkes_m.obatalkes_id)))
		     JOIN ( SELECT a.jenisobatalkes_id,
		            a.jenisobatalkes_kode,
		            a.jenisobatalkes_nama
		           FROM jenisobatalkes_m a) jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
		     JOIN ( SELECT a.satuanunit_id,
		            a.satuanunit_nama
		           FROM satuanunit_m a) satuan ON ((adjusmenobatmasuk_t.satuankecil_id = satuan.satuanunit_id)))
		     JOIN ( SELECT a.satuanunit_id,
		            a.satuanunit_nama
		           FROM satuanunit_m a) satuan_masuk_kecil ON ((adjusmenobatmasuk_t.satuankecil_id = satuan_masuk_kecil.satuanunit_id)))
		     JOIN ( SELECT a.satuanunit_id,
		            a.satuanunit_nama
		           FROM satuanunit_m a) satuan_masuk_besar ON ((adjusmenobatmasuk_t.satuanbesar_id = satuan_masuk_besar.satuanunit_id)))
		  WHERE (adjusmenobat_t.is_deleted = false)
		UNION ALL
		 SELECT adjusmenobat_t.adjusmenobat_id,
		    adjusmenobat_t.ruangan_adjusmen_id,
		    ruangan_m.ruangan_nama,
		    adjusmenobat_t.tgl_adjusmen,
		    adjusmenobat_t.no_adjusmen,
		    adjusmenobat_t.jenis_adjusmen,
		        CASE
		            WHEN (adjusmenobat_t.jenis_adjusmen = 0) THEN 'Masuk'::text
		            ELSE 'Keluar'::text
		        END AS jenis_adjusmen_nama,
		    jenisobatalkes_m.jenisobatalkes_id,
		    jenisobatalkes_m.jenisobatalkes_kode,
		    jenisobatalkes_m.jenisobatalkes_nama,
		    obatalkes_m.obatalkes_kode,
		    obatalkes_m.obatalkes_nama,
		    adjusmenobatkeluar_t.qty AS qty_input,
		    adjusmenobatkeluar_t.qty_konversi,
		    adjusmenobatkeluar_t.satuankecil_id,
		    adjusmenobatkeluar_t.satuanbesar_id,
		    satuan_keluar_kecil.satuanunit_nama AS satuan_kecil,
		    satuan_keluar_besar.satuanunit_nama AS satuan_besar,
		    peg_adjusmen.nama_pegawai AS pegawai_adjusmen
		   FROM (((((((((((adjusmenobat_t
		     LEFT JOIN ( SELECT a.pegawai_id,
		            a.nama_pegawai
		           FROM pegawai_m a) peg_mengetahui ON ((adjusmenobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
		     LEFT JOIN ( SELECT a.pegawai_id,
		            a.nama_pegawai
		           FROM pegawai_m a) peg_menyetujui ON ((adjusmenobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
		     JOIN ( SELECT a.ruangan_id,
		            a.ruangan_nama
		           FROM ruangan_m a) ruangan_m ON ((adjusmenobat_t.ruangan_adjusmen_id = ruangan_m.ruangan_id)))
		     LEFT JOIN ( SELECT a.loginpemakai_id,
		            a.pegawai_id
		           FROM loginpemakai_k a) loginpemakai_k ON ((adjusmenobat_t.created_by = loginpemakai_k.loginpemakai_id)))
		     LEFT JOIN ( SELECT a.pegawai_id,
		            a.nama_pegawai
		           FROM pegawai_m a) peg_adjusmen ON ((loginpemakai_k.pegawai_id = peg_adjusmen.pegawai_id)))
		     JOIN ( SELECT b.adjusmenobat_id,
		            b.obatalkes_id,
		            b.satuankecil_id,
		            b.satuanbesar_id,
		            b.adjusmenobatkeluar_id,
		            b.qty,
		            b.qty_konversi,
		            b.no_batch,
		            b.keterangan
		           FROM adjusmenobatkeluar_t b
		          WHERE (b.is_deleted = false)) adjusmenobatkeluar_t ON ((adjusmenobat_t.adjusmenobat_id = adjusmenobatkeluar_t.adjusmenobat_id)))
		     JOIN ( SELECT a.obatalkes_id,
		            a.obatalkes_kode,
		            a.obatalkes_nama,
		            a.jenisobatalkes_id
		           FROM obatalkes_m a) obatalkes_m ON ((adjusmenobatkeluar_t.obatalkes_id = obatalkes_m.obatalkes_id)))
		     JOIN ( SELECT a.jenisobatalkes_id,
		            a.jenisobatalkes_kode,
		            a.jenisobatalkes_nama
		           FROM jenisobatalkes_m a) jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
		     JOIN ( SELECT b.satuanunit_id,
		            b.satuanunit_nama
		           FROM satuanunit_m b) satuan ON ((adjusmenobatkeluar_t.satuankecil_id = satuan.satuanunit_id)))
		     JOIN ( SELECT b.satuanunit_id,
		            b.satuanunit_nama
		           FROM satuanunit_m b) satuan_keluar_besar ON ((adjusmenobatkeluar_t.satuanbesar_id = satuan_keluar_besar.satuanunit_id)))
		     JOIN ( SELECT b.satuanunit_id,
		            b.satuanunit_nama
		           FROM satuanunit_m b) satuan_keluar_kecil ON ((adjusmenobatkeluar_t.satuankecil_id = satuan_keluar_kecil.satuanunit_id)));");
    
             $this->execute('
                 ALTER TABLE public.laporanadjustmentobatalkes_v OWNER TO postgres;');
			 
	   	   	$this->execute('DROP VIEW if exists public.laporanadjustmenbarang_v;');
		
	          $this->execute("
	              CREATE VIEW \"public\".\"laporanadjustmenbarang_v\" AS
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
			     satuan_masuk_kecil.satuanunit_nama AS satuan_kecil,
			     satuan_masuk_besar.satuanunit_nama AS satuan_besar,
			     peg_adjusmen.nama_pegawai AS pegawai_adjusmen
			    FROM ((((((((((adjusmenbarang_t
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
			            FROM satuanunit_m a) satuan_masuk_kecil ON ((adjusmenbarangmasuk_t.satuankecil_id = satuan_masuk_kecil.satuanunit_id)))
			      JOIN ( SELECT a.satuanunit_id,
			             a.satuanunit_nama
			            FROM satuanunit_m a) satuan_masuk_besar ON ((adjusmenbarangmasuk_t.satuanbesar_id = satuan_masuk_besar.satuanunit_id)))
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
			     satuan_keluar_kecil.satuanunit_nama AS satuan_kecil,
			     satuan_keluar_besar.satuanunit_nama AS satuan_besar,
			     peg_adjusmen.nama_pegawai AS pegawai_adjusmen
			    FROM ((((((((((adjusmenbarang_t
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
			            FROM satuanunit_m a) satuan_keluar_kecil ON ((adjusmenbarangkeluar_t.satuankecil_id = satuan_keluar_kecil.satuanunit_id)))
			      JOIN ( SELECT a.satuanunit_id,
			             a.satuanunit_nama
			            FROM satuanunit_m a) satuan_keluar_besar ON ((adjusmenbarangkeluar_t.satuanbesar_id = satuan_keluar_besar.satuanunit_id)))
			   WHERE (adjusmenbarang_t.is_deleted = false);");
    
	              $this->execute('
	                  ALTER TABLE public.laporanadjustmenbarang_v OWNER TO postgres;');
    
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211222_124225_migrate_adjustmenobatalkes_master22122021 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211222_124225_migrate_adjustmenobatalkes_master22122021 cannot be reverted.\n";

        return false;
    }
    */
}
