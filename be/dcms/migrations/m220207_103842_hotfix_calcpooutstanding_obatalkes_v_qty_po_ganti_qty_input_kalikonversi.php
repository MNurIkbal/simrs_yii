<?php

use yii\db\Migration;

/**
 * Class m220207_103842_hotfix_calcpooutstanding_obatalkes_v_qty_po_ganti_qty_input_kalikonversi
 */
class m220207_103842_hotfix_calcpooutstanding_obatalkes_v_qty_po_ganti_qty_input_kalikonversi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->execute('DROP VIEW if exists public.calcpooutstanding_obatalkes_v;');
	
	      $this->execute("
	          CREATE VIEW \"public\".\"calcpooutstanding_obatalkes_v\" AS  SELECT a.obatalkes_id,
    COALESCE(sum(a.qty_outstanding), 0::double precision) AS qty_outstanding,
    COALESCE(sum(a.qty_po)::double precision, 0::double precision) AS qty_po,
    COALESCE(sum(a.qty_diterima), 0::double precision) AS qty_diterima,
    COALESCE(sum(a.qty_final), 0::double precision) AS qty_final,
    COALESCE(sum(a.qty_retur)::double precision, 0::double precision) AS qty_retur,
    COALESCE(sum(a.qty_input), 0::double precision) AS qty_input
   FROM ( SELECT vt.validasipoobatdetail_id,
            vt.validasipoobat_id,
            vt.obatalkes_id,
            vt.qty_po,
            ret.qty_diterima,
            ret.qty_retur,
            ret.qty_final,
            vt.qty_input::double precision * satuankonversi_m.nilai_konversi AS qty_input,
            COALESCE(vt.qty_input::double precision * satuankonversi_m.nilai_konversi) - COALESCE(ret.qty_final, 0::double precision) AS qty_outstanding
           FROM validasipoobatdetail_t vt
             JOIN satuankonversi_m ON vt.s_konversiobt_id = satuankonversi_m.satuankonversi_id
             JOIN ( SELECT validasipoobat_t.validasipoobat_id,
                    validasipoobat_t.no_poobat,
                    validasipoobat_t.status_penerimaan
                   FROM validasipoobat_t
                  WHERE validasipoobat_t.is_deleted = false AND (validasipoobat_t.status_penerimaan <> ALL (ARRAY[575, 686, 580])) AND validasipoobat_t.is_closing = false) po ON vt.validasipoobat_id = po.validasipoobat_id
             LEFT JOIN ( SELECT pd.validasipoobatdetail_id,
                    COALESCE(sum(pd.qty_diterima::double precision * COALESCE(kf.nilai_konversi)), 0::double precision) AS qty_diterima,
                    COALESCE(sum(rt.qty_retur), 0::numeric) AS qty_retur,
                    COALESCE(sum(pd.qty_diterima)::double precision * COALESCE(kf.nilai_konversi) - COALESCE(sum(rt.qty_retur), 0::numeric)::double precision, 0::double precision) AS qty_final
                   FROM penerimaanobat_t pt
                     JOIN penerimaanobatdetail_t pd ON pt.penerimaanobat_id = pd.penerimaanobat_id
                     LEFT JOIN ( SELECT returpenerimaanobatdetail_t.penerimaanobatdetail_id,
                            sum(returpenerimaanobatdetail_t.qty_retur) AS qty_retur
                           FROM returpenerimaanobatdetail_t
                          WHERE returpenerimaanobatdetail_t.is_deleted = false
                          GROUP BY returpenerimaanobatdetail_t.penerimaanobatdetail_id) rt ON pd.penerimaanobatdetail_id = rt.penerimaanobatdetail_id
                     LEFT JOIN satuankonversi_m kf ON pd.s_konversiobt_id = kf.satuankonversi_id
                  WHERE pd.is_deleted = false AND pt.is_deleted = false AND pd.is_batal IS NOT TRUE
                  GROUP BY pd.validasipoobatdetail_id, kf.nilai_konversi) ret ON vt.validasipoobatdetail_id = ret.validasipoobatdetail_id
          WHERE vt.is_deleted = false) a
  GROUP BY a.obatalkes_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220207_103842_hotfix_calcpooutstanding_obatalkes_v_qty_po_ganti_qty_input_kalikonversi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220207_103842_hotfix_calcpooutstanding_obatalkes_v_qty_po_ganti_qty_input_kalikonversi cannot be reverted.\n";

        return false;
    }
    */
}
