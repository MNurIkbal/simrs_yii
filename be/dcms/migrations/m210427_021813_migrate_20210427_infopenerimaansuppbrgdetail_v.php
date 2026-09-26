<?php

use yii\db\Migration;

/**
 * Class m210427_021813_migrate_20210427_infopenerimaansuppbrgdetail_v
 */
class m210427_021813_migrate_20210427_infopenerimaansuppbrgdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopenerimaansuppbrgdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopenerimaansuppbrgdetail_v\" AS  SELECT penerimaansupp_t.penerimaansupp_id,
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
    penerimaansuppdetail_t.barang_id,
    barang_m.barang_nama,
    penerimaansuppdetail_t.tgl_kadaluarsa,
    penerimaansuppdetail_t.satuanbesar_id,
    sat_besar.satuanunit_nama AS satuan_besar,
    penerimaansuppdetail_t.satuankecil_id,
    sat_kecil.satuanunit_nama AS satuan_kecil,
    penerimaansuppdetail_t.qty_besar,
    penerimaansuppdetail_t.qty_kecil,
    penerimaansuppdetail_t.harga_netto,
    penerimaansuppdetail_t.ppn,
    penerimaansuppdetail_t.diskon,
    penerimaansuppdetail_t.qty_besar - COALESCE(retur.total_retur, 0::bigint) AS qty_sisa,
    penerimaansuppdetail_t.no_batch,
    penerimaansuppdetail_t.harga_netto_satuan,
    penerimaansuppdetail_t.keterangan
   FROM penerimaansupp_t
     JOIN penerimaansuppdetail_t ON penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id
     JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pegawai_m peg_menyetujui ON penerimaansupp_t.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m peg_mengetahui ON penerimaansupp_t.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
     JOIN barang_m ON penerimaansuppdetail_t.barang_id = barang_m.barang_id
     JOIN satuanunit_m sat_besar ON penerimaansuppdetail_t.satuanbesar_id = sat_besar.satuanunit_id
     JOIN satuanunit_m sat_kecil ON penerimaansuppdetail_t.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id,
            sum(returpenerimaanbarangdetail_t.qty_input) AS total_retur
           FROM returpenerimaanbarangdetail_t
          GROUP BY returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id) retur ON retur.penerimaansuppbrgdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
     LEFT JOIN satuankonversibrg_m ON penerimaansuppdetail_t.barang_id = satuankonversibrg_m.barang_id AND penerimaansuppdetail_t.satuankecil_id = satuankonversibrg_m.satuankecil_id AND penerimaansuppdetail_t.satuanbesar_id = satuankonversibrg_m.satuanbesar_id
     LEFT JOIN stokbarang_t ON stokbarang_t.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id;");
        
        $this->execute('ALTER TABLE "public"."infopenerimaansuppbrgdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210427_021813_migrate_20210427_infopenerimaansuppbrgdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210427_021813_migrate_20210427_infopenerimaansuppbrgdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
