<?php

use yii\db\Migration;

/**
 * Class m220809_034032_migrate_VCS323_infopenerimaansuppdetail_v
 */
class m220809_034032_migrate_VCS323_infopenerimaansuppdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopenerimaansuppdetail_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopenerimaansuppdetail_v
        AS SELECT penerimaansupp_t.penerimaansupp_id,
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
            penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision AS harga_input_satuan,
            penerimaansuppdetail_t.keterangan
           FROM penerimaansupp_t
             JOIN ( SELECT a.penerimaansuppdetail_id,
                    a.penerimaansupp_id,
                    a.obatalkes_id,
                    a.tgl_kadaluarsa,
                    a.satuanbesar_id,
                    a.satuankecil_id,
                    a.qty_besar,
                    a.qty_kecil,
                    a.harga_netto,
                    a.diskon,
                    a.no_batch,
                    a.harga_netto_satuan,
                    a.keterangan
                   FROM penerimaansuppdetail_t a) penerimaansuppdetail_t ON penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id
             JOIN ( SELECT a.supplier_id,
                    a.supplier_nama
                   FROM supplier_m a) supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_menyetujui ON penerimaansupp_t.peg_menyetujui = peg_menyetujui.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_mengetahui ON penerimaansupp_t.peg_mengetahui = peg_mengetahui.pegawai_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_nama,
                    a.obatalkes_kode
                   FROM obatalkes_m a) obatalkes_m ON penerimaansuppdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT a.satuanunit_id,
                    a.satuanunit_nama
                   FROM satuanunit_m a) sat_besar ON penerimaansuppdetail_t.satuanbesar_id = sat_besar.satuanunit_id
             JOIN ( SELECT a.satuanunit_id,
                    a.satuanunit_nama
                   FROM satuanunit_m a) sat_kecil ON penerimaansuppdetail_t.satuankecil_id = sat_kecil.satuanunit_id
             LEFT JOIN ( SELECT a.satuanbesar_id,
                    a.satuankecil_id,
                    a.nilai_konversi,
                    a.obatalkes_id,
                    a.is_deleted,
                    a.is_active
                   FROM satuankonversi_m a) satuankonversi_m ON penerimaansuppdetail_t.satuanbesar_id = satuankonversi_m.satuanbesar_id AND penerimaansuppdetail_t.satuankecil_id = satuankonversi_m.satuankecil_id AND penerimaansuppdetail_t.obatalkes_id = satuankonversi_m.obatalkes_id AND satuankonversi_m.is_deleted = false AND satuankonversi_m.is_active = true
             JOIN ( SELECT a.pajak_id,
                    a.pajak_name,
                    a.pajak_persen
                   FROM pajak_m a) pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
             LEFT JOIN ( SELECT returpenerimaanobat_t.panerimaanobatsupp_id,
                    sum(returpenerimaanobatdetail_t.qty_retur) AS total_retur,
                    returpenerimaanobatdetail_t.penerimaansuppdetail_id
                   FROM returpenerimaanobat_t
                     JOIN ( SELECT a.returpenerimaanobat_id,
                            a.penerimaansuppdetail_id,
                            a.qty_retur
                           FROM returpenerimaanobatdetail_t a) returpenerimaanobatdetail_t ON returpenerimaanobatdetail_t.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id
                  GROUP BY returpenerimaanobat_t.panerimaanobatsupp_id, returpenerimaanobatdetail_t.penerimaansuppdetail_id) retur ON retur.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220809_034032_migrate_VCS323_infopenerimaansuppdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220809_034032_migrate_VCS323_infopenerimaansuppdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
