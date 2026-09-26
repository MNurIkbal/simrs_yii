<?php

use yii\db\Migration;

/**
 * Class m220117_053023_migrate_US2113_kontraksupplier
 */
class m220117_053023_migrate_US2113_kontraksupplier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.kontraksupplierheader_v;');
        $this->execute("
            CREATE VIEW \"public\".\"kontraksupplierheader_v\" AS
            SELECT kontraksupplier_m.kontraksupplier_id,
            kontraksupplier_m.supplier_id,
            supplier_m.supplier_nama,
            kontraksupplier_m.payterm_id,
            kontraksupplier_m.jumlah_hari,
            kontraksupplier_m.pajak_id,
            kontraksupplier_m.persen_ppn,
            supplier_m.supplier_kode,
            kontraksupplier_m.kontraksupplier_no,
            kontraksupplier_m.tgl_berlaku,
            kontraksupplier_m.metode_bayar,
            kontraksupplier_m.dikirim_ke,
            kontraksupplier_m.contact_person,
            kontraksupplier_m.catatan,
            kontraksupplier_m.is_active,
            payterm_m.payterm_nama,
            pajak_m.pajak_name AS pajak_nama
            FROM (((kontraksupplier_m
            LEFT JOIN supplier_m ON ((kontraksupplier_m.supplier_id = supplier_m.supplier_id)))
            LEFT JOIN payterm_m ON ((kontraksupplier_m.payterm_id = payterm_m.payterm_id)))
            LEFT JOIN pajak_m ON ((kontraksupplier_m.pajak_id = pajak_m.pajak_id)))
            ;");
        $this->execute('
            ALTER TABLE public.kontraksupplierheader_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.kontraksupplier_v;');
        $this->execute("
            CREATE VIEW \"public\".\"kontraksupplier_v\" AS
            SELECT kontraksupplier_m.kontraksupplier_id,
            kontraksupplier_m.supplier_id,
            supplier_m.supplier_nama,
            kontraksupplier_m.payterm_id,
            kontraksupplier_m.jumlah_hari,
            kontraksupplier_m.pajak_id,
            kontraksupplier_m.persen_ppn,
            kontraksupplierdetail_m.obatalkes_id,
            kontraksupplierdetail_m.kode_obat,
            kontraksupplierdetail_m.nama_obat,
            kontraksupplierdetail_m.harga,
            kontraksupplierdetail_m.pengurang AS diskon,
            sat_kecil.satuanunit_nama AS satuan_kecil,
            sat_konv1.satuanunit_nama AS satuan_konversi1,
            sat_konv2.satuanunit_nama AS satuan_konversi2,
            kontraksupplierdetail_m.kontraksupplierdetail_id,
            kontraksupplierdetail_m.satuankecil_id,
            kontraksupplierdetail_m.satuankonv1_id,
            kontraksupplierdetail_m.satuankonv2_id,
            kontraksupplierdetail_m.qty_min,
            kontraksupplierdetail_m.penambah,
            kontraksupplierdetail_m.pengurang,
            kontraksupplierdetail_m.total_harga,
            NULL::text AS uom_id,
            concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom_text,
            kontraksupplier_m.kontraksupplier_no,
            kontraksupplier_m.is_active AS aktif_supplier,
            uom.aktif_obat,
            kontraksupplierdetail_m.last_modified_date AS last_updated_time
            FROM ((((((kontraksupplier_m
            JOIN supplier_m ON ((kontraksupplier_m.supplier_id = supplier_m.supplier_id)))
            JOIN kontraksupplierdetail_m ON ((kontraksupplier_m.kontraksupplier_id = kontraksupplierdetail_m.kontraksupplier_id)))
            LEFT JOIN satuanunit_m sat_kecil ON ((kontraksupplierdetail_m.satuankecil_id = sat_kecil.satuanunit_id)))
            LEFT JOIN satuanunit_m sat_konv1 ON ((kontraksupplierdetail_m.satuankonv1_id = sat_konv1.satuanunit_id)))
            LEFT JOIN satuanunit_m sat_konv2 ON ((kontraksupplierdetail_m.satuankonv2_id = sat_konv2.satuanunit_id)))
            LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m.nilai_konversi,
            obatalkes_m.is_active AS aktif_obat
            FROM (((satuankonversi_m
            LEFT JOIN satuanunit_m uom_besar ON ((satuankonversi_m.satuanbesar_id = uom_besar.satuanunit_id)))
            LEFT JOIN satuanunit_m uom_kecil ON ((satuankonversi_m.satuankecil_id = uom_kecil.satuanunit_id)))
            LEFT JOIN obatalkes_m ON ((satuankonversi_m.obatalkes_id = obatalkes_m.obatalkes_id)))
            WHERE (satuankonversi_m.is_deleted = false)
            GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuankecil_id, satuankonversi_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m.nilai_konversi, obatalkes_m.is_active) uom ON (((kontraksupplierdetail_m.obatalkes_id = uom.obatalkes_id) AND (kontraksupplierdetail_m.satuankonv1_id = uom.satuanbesar_id) AND (kontraksupplierdetail_m.satuankecil_id = uom.satuankecil_id))))
            WHERE ((kontraksupplier_m.is_deleted = false) AND (kontraksupplierdetail_m.is_deleted = false) AND (supplier_m.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.kontraksupplier_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220117_053023_migrate_US2113_kontraksupplier cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220117_053023_migrate_US2113_kontraksupplier cannot be reverted.\n";

        return false;
    }
    */
}
