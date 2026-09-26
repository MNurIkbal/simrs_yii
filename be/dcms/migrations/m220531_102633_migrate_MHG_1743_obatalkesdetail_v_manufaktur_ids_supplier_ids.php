<?php

use yii\db\Migration;

/**
 * Class m220531_102633_migrate_MHG_1743_obatalkesdetail_v_manufaktur_ids_supplier_ids
 */
class m220531_102633_migrate_MHG_1743_obatalkesdetail_v_manufaktur_ids_supplier_ids extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->execute('DROP VIEW if exists "public"."obatalkesdetail_v";');

	    $this->execute("
	               CREATE VIEW \"public\".\"obatalkesdetail_v\" AS SELECT obatalkes_m.obatalkes_id,
	       obatalkes_m.obatalkes_nama,
	       obatalkes_m.obatalkes_namalain,
	       obatalkes_m.jenisobatalkes_id,
	       jenisobatalkes_m.jenisobatalkes_nama,
	       obatalkes_m.ven AS ven_id,
	       lookup_ven.lookup_name AS ven,
	       obatalkes_m.obatalkes_kode,
	       obatalkes_m.kekuatan_obat,
	       obatalkes_m.satuankecil_id,
	       satuankecil.satuanunit_nama AS satuan_kecil,
	       obatalkes_m.satuansedang_id,
	       satuan_1.satuanunit_nama AS satuan_1,
	       obatalkes_m.satuanbesar_id,
	       satuan_2.satuanunit_nama AS satuan_2,
	       obatalkes_m.kemasan_sedang,
	       obatalkes_m.kemasan_besar,
	       obatalkes_m.is_generik,
	       obatalkes_m.tglkadaluarsa,
	       obatalkes_m.obatalkes_nobatch,
	       obatalkes_m.obatalkes_kategori,
	       lookup_kategori.lookup_name AS kategori,
	       obatalkes_m.maksimalstok,
	       obatalkes_m.supplier_id,
	       supplier_m.supplier_nama,
	       obatalkes_m.harga_beli,
	       obatalkes_m.discount,
	       obatalkes_m.ppn_persen,
	       obatalkes_m.harganetto,
	       obatalkes_m.hargamaksimum,
	       obatalkes_m.hargaminimum,
	       obatalkes_m.hargaratarata,
	       obatalkes_m.hargaterakhir,
	       obatalkes_m.indikasi,
	       obatalkes_m.interaksi,
	       obatalkes_m.kontradiksi,
	       obatalkes_m.efek_samping,
	       obatalkes_m.satuankekuatan,
	       lookup_kekuatan.lookup_name,
	       obatalkes_m.groupinacbg_id,
	       obatalkes_m.lead_time,
	       obatalkes_m.minimalstok,
	       obatalkes_m.avg_usage,
	       obatalkes_m.min_order,
	       obatalkes_m.max_order,
	       obatalkes_m.nilai_ro,
	       obatalkes_m.on_po,
	       obatalkes_m.on_ro,
	       obatalkes_m.is_oral,
	       obatalkes_m.manufaktur_id,
	       manufaktur_m.nama AS manufaktur_nama,
	       obatalkes_m.is_formularium,
	       obatalkes_m.is_antibiotic,
	       obatalkes_m.is_psycothropica,
	       obatalkes_m.zataktif_id,
	       zataktif_m.zataktif_nama,
	       obatalkes_m.is_narcotic,
	       obatalkes_m.ruteobat_id,
	       obatalkes_m.reorder,
	       obatalkes_m.is_consigment,
	       obatalkes_m.atccode_id,
	       obatalkesmims_m.obatalkesmims_id AS mims_id,
	       obatalkesmims_m.obatalkesmims_nama,
	       obatalkessubmims_m.obatalkesmims_id AS submims_id,
	       obatalkessubmims_m.obatalkesmims_nama AS obatalkessubmims_nama,
	       obatalkes_m.manufacture_ids,
	       obatalkes_m.supplier_ids
	      FROM obatalkes_m
	        JOIN ( SELECT jenisobatalkes_m_1.jenisobatalkes_id,
	               jenisobatalkes_m_1.jenisobatalkes_nama
	              FROM jenisobatalkes_m jenisobatalkes_m_1) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
	        LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
	               satuanunit_m.satuanunit_nama
	              FROM satuanunit_m) satuankecil ON obatalkes_m.satuankecil_id = satuankecil.satuanunit_id
	        LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
	               satuanunit_m.satuanunit_nama
	              FROM satuanunit_m) satuan_1 ON obatalkes_m.satuansedang_id = satuan_1.satuanunit_id
	        LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
	               satuanunit_m.satuanunit_nama
	              FROM satuanunit_m) satuan_2 ON obatalkes_m.satuanbesar_id = satuan_2.satuanunit_id
	        LEFT JOIN ( SELECT supplier_m_1.supplier_id,
	               supplier_m_1.supplier_nama
	              FROM supplier_m supplier_m_1) supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
	        LEFT JOIN ( SELECT manufaktur_m_1.manufaktur_id,
	               manufaktur_m_1.nama
	              FROM manufaktur_m manufaktur_m_1) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
	        LEFT JOIN ( SELECT zataktif_m_1.zataktif_id,
	               zataktif_m_1.zataktif_nama
	              FROM zataktif_m zataktif_m_1) zataktif_m ON zataktif_m.zataktif_id = obatalkes_m.zataktif_id
	        LEFT JOIN ( SELECT obatalkesmims_m_1.obatalkesmims_id,
	               obatalkesmims_m_1.obatalkesmims_nama
	              FROM obatalkesmims_m obatalkesmims_m_1
	             WHERE obatalkesmims_m_1.parent_id = 0) obatalkesmims_m ON obatalkesmims_m.obatalkesmims_id = obatalkes_m.obatalkesmims_id
	        LEFT JOIN ( SELECT obatalkesmims_m_1.obatalkesmims_id,
	               obatalkesmims_m_1.obatalkesmims_nama
	              FROM obatalkesmims_m obatalkesmims_m_1
	             WHERE obatalkesmims_m_1.parent_id <> 0) obatalkessubmims_m ON obatalkesmims_m.obatalkesmims_id = obatalkes_m.obatalkesmims_id
	        LEFT JOIN ( SELECT lookup_m.lookup_id,
	               lookup_m.lookup_type,
	               lookup_m.lookup_name,
	               lookup_m.lookup_value,
	               lookup_m.lookup_urutan,
	               lookup_m.lookup_kode
	              FROM lookup_m) lookup_kekuatan ON obatalkes_m.satuankekuatan::integer = lookup_kekuatan.lookup_id
	        LEFT JOIN ( SELECT lookup_m.lookup_id,
	               lookup_m.lookup_type,
	               lookup_m.lookup_name,
	               lookup_m.lookup_value,
	               lookup_m.lookup_urutan,
	               lookup_m.lookup_kode
	              FROM lookup_m) lookup_kategori ON obatalkes_m.obatalkes_kategori::integer = lookup_kekuatan.lookup_id
	        LEFT JOIN ( SELECT lookup_m.lookup_id,
	               lookup_m.lookup_type,
	               lookup_m.lookup_name,
	               lookup_m.lookup_value,
	               lookup_m.lookup_urutan,
	               lookup_m.lookup_kode
	              FROM lookup_m) lookup_ven ON obatalkes_m.ven = lookup_ven.lookup_id
	     WHERE obatalkes_m.is_deleted = false;");
        
	           $this->execute('ALTER TABLE "public"."obatalkesdetail_v" OWNER TO "postgres";');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220531_102633_migrate_MHG_1743_obatalkesdetail_v_manufaktur_ids_supplier_ids cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220531_102633_migrate_MHG_1743_obatalkesdetail_v_manufaktur_ids_supplier_ids cannot be reverted.\n";

        return false;
    }
    */
}
