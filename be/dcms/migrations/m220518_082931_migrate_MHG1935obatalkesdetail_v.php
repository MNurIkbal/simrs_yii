<?php

use yii\db\Migration;

/**
 * Class m220518_082931_migrate_MHG1935obatalkesdetail_v
 */
class m220518_082931_migrate_MHG1935obatalkesdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."obatalkesdetail_v";
        ');

        $this->execute('
			CREATE VIEW "public"."obatalkesdetail_v" AS  SELECT obatalkes_m.obatalkes_id,
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
			    obatalkesmims_m.parent_id AS mims_id,
			    obatalkes_m.obatalkesmims_id AS submims_id
			   FROM obatalkes_m
			     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
			     LEFT JOIN satuanunit_m satuankecil ON obatalkes_m.satuankecil_id = satuankecil.satuanunit_id
			     LEFT JOIN satuanunit_m satuan_1 ON obatalkes_m.satuansedang_id = satuan_1.satuanunit_id
			     LEFT JOIN satuanunit_m satuan_2 ON obatalkes_m.satuanbesar_id = satuan_2.satuanunit_id
			     LEFT JOIN supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
			     LEFT JOIN manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
			     LEFT JOIN zataktif_m ON zataktif_m.zataktif_id = obatalkes_m.zataktif_id
			     LEFT JOIN obatalkesmims_m ON obatalkesmims_m.obatalkesmims_id = obatalkes_m.obatalkesmims_id
			     LEFT JOIN ( SELECT lookup_m.lookup_id,
			            lookup_m.lookup_type,
			            lookup_m.lookup_name,
			            lookup_m.lookup_value,
			            lookup_m.lookup_urutan,
			            lookup_m.lookup_kode,
			            lookup_m.additional_data,
			            lookup_m.created_date,
			            lookup_m.created_by,
			            lookup_m.modified_count,
			            lookup_m.last_modified_date,
			            lookup_m.last_modified_by,
			            lookup_m.is_deleted,
			            lookup_m.is_active,
			            lookup_m.deleted_date,
			            lookup_m.deleted_by
			           FROM lookup_m) lookup_kekuatan ON obatalkes_m.satuankekuatan::integer = lookup_kekuatan.lookup_id
			     LEFT JOIN ( SELECT lookup_m.lookup_id,
			            lookup_m.lookup_type,
			            lookup_m.lookup_name,
			            lookup_m.lookup_value,
			            lookup_m.lookup_urutan,
			            lookup_m.lookup_kode,
			            lookup_m.additional_data,
			            lookup_m.created_date,
			            lookup_m.created_by,
			            lookup_m.modified_count,
			            lookup_m.last_modified_date,
			            lookup_m.last_modified_by,
			            lookup_m.is_deleted,
			            lookup_m.is_active,
			            lookup_m.deleted_date,
			            lookup_m.deleted_by
			           FROM lookup_m) lookup_kategori ON obatalkes_m.obatalkes_kategori::integer = lookup_kekuatan.lookup_id
			     LEFT JOIN ( SELECT lookup_m.lookup_id,
			            lookup_m.lookup_type,
			            lookup_m.lookup_name,
			            lookup_m.lookup_value,
			            lookup_m.lookup_urutan,
			            lookup_m.lookup_kode,
			            lookup_m.additional_data,
			            lookup_m.created_date,
			            lookup_m.created_by,
			            lookup_m.modified_count,
			            lookup_m.last_modified_date,
			            lookup_m.last_modified_by,
			            lookup_m.is_deleted,
			            lookup_m.is_active,
			            lookup_m.deleted_date,
			            lookup_m.deleted_by
			           FROM lookup_m) lookup_ven ON obatalkes_m.ven = lookup_ven.lookup_id
			  WHERE obatalkes_m.is_deleted = false;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220518_082931_migrate_MHG1935obatalkesdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220518_082931_migrate_MHG1935obatalkesdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
