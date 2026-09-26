<?php

use yii\db\Migration;

/**
 * Class m191007_074853_info_penerimaan_supp_brg
 */
class m191007_074853_info_penerimaan_supp_brg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."penerimaansupp_t" 
  ADD COLUMN "is_tipe" int2 DEFAULT 0;');

         $this->execute('COMMENT ON COLUMN "public"."penerimaansupp_t"."is_tipe" IS \'0=obat, 1=barang\';');

        $this->execute('ALTER TABLE "public"."penerimaansupp_t" 
  ADD COLUMN "no_suratjalan" varchar(100);');

        $this->execute('ALTER TABLE "public"."penerimaansuppdetail_t" 
  ADD COLUMN "barang_id" int4;');

        $this->execute('DROP VIEW if exists public.infopenerimaansupp_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopenerimaansupp_v AS 
 SELECT penerimaansupp_t.penerimaansupp_id,
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
    concat(pajak_m.pajak_name, ' (', pajak_m.pajak_persen, '%)') AS pajak_label,
    payterm_m.payterm_nama
   FROM penerimaansupp_t
     JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pegawai_m peg_menyetujui ON penerimaansupp_t.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m peg_mengetahui ON penerimaansupp_t.peg_mengetahui = peg_mengetahui.pegawai_id
     JOIN ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
     LEFT JOIN pajak_m ON pajak_m.pajak_id = penerimaansupp_t.pajak_id
     LEFT JOIN payterm_m ON payterm_m.payterm_id = penerimaansupp_t.payterm_id
  WHERE penerimaansupp_t.is_tipe = 0;");

        $this->execute('ALTER TABLE public.infopenerimaansupp_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopenerimaansuppbrg_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopenerimaansuppbrg_v AS 
 SELECT penerimaansupp_t.penerimaansupp_id,
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
    concat(pajak_m.pajak_name, ' (', pajak_m.pajak_persen, '%)') AS pajak_label,
    payterm_m.payterm_nama,
    penerimaansupp_t.no_suratjalan
   FROM penerimaansupp_t
     JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pegawai_m peg_menyetujui ON penerimaansupp_t.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m peg_mengetahui ON penerimaansupp_t.peg_mengetahui = peg_mengetahui.pegawai_id
     JOIN ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
     LEFT JOIN pajak_m ON pajak_m.pajak_id = penerimaansupp_t.pajak_id
     LEFT JOIN payterm_m ON payterm_m.payterm_id = penerimaansupp_t.payterm_id
  WHERE penerimaansupp_t.is_tipe = 1;");

        $this->execute('ALTER TABLE public.infopenerimaansuppbrg_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopenerimaansuppbrgdetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopenerimaansuppbrgdetail_v AS 
 SELECT penerimaansupp_t.penerimaansupp_id,
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
     LEFT JOIN ( SELECT returpenerimaanobat_t.panerimaanobatsupp_id,
            sum(returpenerimaanobatdetail_t.qty_retur) AS total_retur,
            returpenerimaanobatdetail_t.penerimaansuppdetail_id
           FROM returpenerimaanobat_t
             JOIN returpenerimaanobatdetail_t ON returpenerimaanobatdetail_t.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id
          GROUP BY returpenerimaanobat_t.panerimaanobatsupp_id, returpenerimaanobatdetail_t.penerimaansuppdetail_id) retur ON retur.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id;
");

        $this->execute('ALTER TABLE public.infopenerimaansuppbrgdetail_v
  OWNER TO postgres;');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191007_074853_info_penerimaan_supp_brg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191007_074853_info_penerimaan_supp_brg cannot be reverted.\n";

        return false;
    }
    */
}
