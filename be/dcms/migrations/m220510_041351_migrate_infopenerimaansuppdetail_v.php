<?php

use yii\db\Migration;

/**
 * Class m220510_041351_migrate_infopenerimaansuppdetail_v
 */
class m220510_041351_migrate_infopenerimaansuppdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."infopenerimaansuppdetail_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infopenerimaansuppdetail_v\" AS  SELECT penerimaansupp_t.penerimaansupp_id,
    penerimaansuppdetail_t.penerimaansuppdetail_id,
    penerimaansupp_t.no_penerimaan,
    penerimaansupp_t.tgl_penerimaan,
    penerimaansupp_t.no_faktur,
    penerimaansupp_t.supplier_id,
    supplier_m.supplier_nama,
    penerimaansupp_t.peg_menyetujui,
    peg_menyetujui.nama_pegawai AS peg_menyetujui_nama,
    penerimaansupp_t.peg_mengetahui,
    peg_mengetahui.nama_pegawai AS peg_mengetahui_nama,
    penerimaansupp_t.ruanganpenerima_id,
    ruangan_m.ruangan_nama,
    penerimaansuppdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    penerimaansuppdetail_t.tgl_kadaluarsa,
    penerimaansuppdetail_t.satuanbesar_id,
    sat_besar.satuanunit_nama AS satuan_besar,
    penerimaansuppdetail_t.satuankecil_id,
    sat_kecil.satuanunit_nama AS satuan_kecil,
    penerimaansuppdetail_t.qty_besar,
    penerimaansuppdetail_t.qty_kecil,
    penerimaansuppdetail_t.harga_netto,
    pajak_m.pajak_name AS nama_ppn,
    pajak_m.pajak_persen AS ppn,
    penerimaansuppdetail_t.diskon,
    penerimaansuppdetail_t.qty_besar - COALESCE(retur.total_retur, 0::bigint) AS qty_sisa,
    penerimaansuppdetail_t.no_batch,
    penerimaansuppdetail_t.harga_netto_satuan,
    obatalkes_m.obatalkes_kode,
    concat('1 ', sat_besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', sat_kecil.satuanunit_nama) AS satuanunit_nama,
    penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision AS harga_input_satuan
   FROM penerimaansupp_t
     JOIN penerimaansuppdetail_t ON penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id
     JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pegawai_m peg_menyetujui ON penerimaansupp_t.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m peg_mengetahui ON penerimaansupp_t.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
     JOIN obatalkes_m ON penerimaansuppdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuanunit_m sat_besar ON penerimaansuppdetail_t.satuanbesar_id = sat_besar.satuanunit_id
     JOIN satuanunit_m sat_kecil ON penerimaansuppdetail_t.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN satuankonversi_m ON penerimaansuppdetail_t.satuanbesar_id = satuankonversi_m.satuanbesar_id AND penerimaansuppdetail_t.satuankecil_id = satuankonversi_m.satuankecil_id AND penerimaansuppdetail_t.obatalkes_id = satuankonversi_m.obatalkes_id AND satuankonversi_m.is_deleted = false AND satuankonversi_m.is_active = true
     JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT returpenerimaanobat_t.panerimaanobatsupp_id,
            sum(returpenerimaanobatdetail_t.qty_retur) AS total_retur,
            returpenerimaanobatdetail_t.penerimaansuppdetail_id
           FROM returpenerimaanobat_t
             JOIN returpenerimaanobatdetail_t ON returpenerimaanobatdetail_t.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id
          GROUP BY returpenerimaanobat_t.panerimaanobatsupp_id, returpenerimaanobatdetail_t.penerimaansuppdetail_id) retur ON retur.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220510_041351_migrate_infopenerimaansuppdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220510_041351_migrate_infopenerimaansuppdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
