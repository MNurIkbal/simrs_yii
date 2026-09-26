<?php

use yii\db\Migration;

/**
 * Class m220829_064315_hotfix_kramat_inforeturpenerimaanobatdetail_v
 */
class m220829_064315_hotfix_kramat_inforeturpenerimaanobatdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."inforeturpenerimaanobatdetail_v";');
        $this->execute("CREATE OR REPLACE VIEW public.inforeturpenerimaanobatdetail_v
        AS SELECT returpenerimaanobat_t.returpenerimaanobat_id,
            returpenerimaanobat_t.tgl_retur,
            returpenerimaanobat_t.no_returpenerimaanobat,
                CASE
                    WHEN returpenerimaanobatdetail_t.penerimaanobat_id IS NOT NULL THEN penerimaanobat_t.no_penerimaan
                    ELSE penerimaansupp_t.no_penerimaan
                END AS no_penerimaan,
                CASE
                    WHEN returpenerimaanobatdetail_t.penerimaanobat_id IS NOT NULL THEN penerimaanobat_t.no_faktur
                    ELSE penerimaansupp_t.no_faktur
                END AS no_faktur,
                CASE
                    WHEN returpenerimaanobatdetail_t.penerimaanobat_id IS NOT NULL THEN supp_po.supplier_nama
                    ELSE supplier_m.supplier_nama
                END AS supplier_nama,
            obatalkes_m.obatalkes_nama,
            satuanunit_m.satuanunit_nama,
            COALESCE(returpenerimaanobatdetail_t.qty_retur) AS qty_retur,
            COALESCE(penerimaanobatdetail_t.qty_diterima, 0) AS qty_diterima,
            COALESCE(returdetailjumlah.on_retur, 0::bigint) AS on_retur,
            returpenerimaanobatdetail_t.tgl_kadaluarsa,
            returpenerimaanobat_t.alasan_retur,
            COALESCE(penerimaanobatdetail_t.no_batch, penerimaansuppdetail_t.no_batch) AS no_batch,
            validasipoobat_t.no_poobat,
                CASE
                    WHEN returpenerimaanobatdetail_t.penerimaanobat_id IS NOT NULL THEN penerimaanobatdetail_t.qty_diterima
                    ELSE penerimaansuppdetail_t.qty_besar
                END AS qty_besar,
            penerimaansuppdetail_t.qty_besar - COALESCE(retur.total_retur, 0::bigint) AS qty_sisa,
            returpenerimaanobatdetail_t.returpenerimaanobatdetail_id,
            obatalkes_m.obatalkes_id,
            returpenerimaanobatdetail_t.qty_input,
            COALESCE(penerimaanobatdetail_t.harga, penerimaansuppdetail_t.harga_netto_satuan) AS harga,
            penerimaanobatdetail_t.penerimaanobatdetail_id
           FROM returpenerimaanobat_t
             JOIN ( SELECT a.returpenerimaanobatdetail_id,
                    a.returpenerimaanobat_id,
                    a.penerimaanobat_id,
                    a.qty_retur,
                    a.tgl_kadaluarsa,
                    a.qty_input,
                    a.penerimaanobatdetail_id,
                    a.obatalkes_id,
                    a.satuanbesar_id,
                    a.penerimaansuppdetail_id
                   FROM returpenerimaanobatdetail_t a) returpenerimaanobatdetail_t ON returpenerimaanobat_t.returpenerimaanobat_id = returpenerimaanobatdetail_t.returpenerimaanobat_id
             LEFT JOIN ( SELECT a.penerimaanobat_id,
                    a.no_penerimaan,
                    a.no_faktur,
                    a.validasipoobat_id,
                    a.supplier_id
                   FROM penerimaanobat_t a) penerimaanobat_t ON returpenerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
             LEFT JOIN ( SELECT a.penerimaanobatdetail_id,
                    a.qty_diterima,
                    a.no_batch,
                    a.harga
                   FROM penerimaanobatdetail_t a) penerimaanobatdetail_t ON returpenerimaanobatdetail_t.penerimaanobatdetail_id = penerimaanobatdetail_t.penerimaanobatdetail_id
             LEFT JOIN ( SELECT a.validasipoobat_id,
                    a.no_poobat
                   FROM validasipoobat_t a) validasipoobat_t ON penerimaanobat_t.validasipoobat_id = validasipoobat_t.validasipoobat_id
             LEFT JOIN ( SELECT a.supplier_id,
                    a.supplier_nama
                   FROM supplier_m a) supp_po ON penerimaanobat_t.supplier_id = supp_po.supplier_id
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_nama
                   FROM obatalkes_m a) obatalkes_m ON obatalkes_m.obatalkes_id = returpenerimaanobatdetail_t.obatalkes_id
             JOIN ( SELECT a.satuanunit_id,
                    a.satuanunit_nama
                   FROM satuanunit_m a) satuanunit_m ON satuanunit_m.satuanunit_id = returpenerimaanobatdetail_t.satuanbesar_id
             LEFT JOIN ( SELECT a.penerimaansupp_id,
                    a.no_penerimaan,
                    a.no_faktur,
                    a.supplier_id
                   FROM penerimaansupp_t a) penerimaansupp_t ON penerimaansupp_t.penerimaansupp_id = returpenerimaanobat_t.panerimaanobatsupp_id
             LEFT JOIN ( SELECT a.penerimaansuppdetail_id,
                    a.no_batch,
                    a.qty_besar,
                    a.harga_netto_satuan
                   FROM penerimaansuppdetail_t a) penerimaansuppdetail_t ON penerimaansuppdetail_t.penerimaansuppdetail_id = returpenerimaanobatdetail_t.penerimaansuppdetail_id
             LEFT JOIN ( SELECT a.supplier_id,
                    a.supplier_nama
                   FROM supplier_m a) supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
             LEFT JOIN ( SELECT returpenerimaanobatdetail_t_1.penerimaansuppdetail_id,
                    sum(returpenerimaanobatdetail_t_1.qty_retur) AS total_retur
                   FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1
                  GROUP BY returpenerimaanobatdetail_t_1.penerimaansuppdetail_id) retur ON penerimaansuppdetail_t.penerimaansuppdetail_id = retur.penerimaansuppdetail_id
             LEFT JOIN ( SELECT returpenerimaanobatdetail_t_1.penerimaanobatdetail_id,
                    sum(returpenerimaanobatdetail_t_1.qty_retur) AS on_retur
                   FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1
                  GROUP BY returpenerimaanobatdetail_t_1.penerimaanobatdetail_id) returdetailjumlah ON penerimaanobatdetail_t.penerimaanobatdetail_id = returdetailjumlah.penerimaanobatdetail_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220829_064315_hotfix_kramat_inforeturpenerimaanobatdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220829_064315_hotfix_kramat_inforeturpenerimaanobatdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
