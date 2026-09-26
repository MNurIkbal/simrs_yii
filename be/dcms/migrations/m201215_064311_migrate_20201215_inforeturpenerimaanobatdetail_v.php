<?php

use yii\db\Migration;

/**
 * Class m201215_064311_migrate_20201215_inforeturpenerimaanobatdetail_v
 */
class m201215_064311_migrate_20201215_inforeturpenerimaanobatdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if  exists "public"."inforeturpenerimaanobatdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inforeturpenerimaanobatdetail_v\" AS  SELECT returpenerimaanobat_t.returpenerimaanobat_id,
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
    COALESCE(penerimaanobatdetail_t.harga, penerimaansuppdetail_t.harga_netto_satuan) AS harga
   FROM returpenerimaanobat_t
     JOIN returpenerimaanobatdetail_t ON returpenerimaanobat_t.returpenerimaanobat_id = returpenerimaanobatdetail_t.returpenerimaanobat_id
     LEFT JOIN penerimaanobat_t ON returpenerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
     LEFT JOIN penerimaanobatdetail_t ON returpenerimaanobatdetail_t.penerimaanobatdetail_id = penerimaanobatdetail_t.penerimaanobatdetail_id
     LEFT JOIN validasipoobat_t ON penerimaanobat_t.validasipoobat_id = validasipoobat_t.validasipoobat_id
     LEFT JOIN supplier_m supp_po ON penerimaanobat_t.supplier_id = supp_po.supplier_id
     JOIN obatalkes_m ON obatalkes_m.obatalkes_id = returpenerimaanobatdetail_t.obatalkes_id
     JOIN satuanunit_m ON satuanunit_m.satuanunit_id = returpenerimaanobatdetail_t.satuanbesar_id
     LEFT JOIN penerimaansupp_t ON penerimaansupp_t.penerimaansupp_id = returpenerimaanobat_t.panerimaanobatsupp_id
     LEFT JOIN penerimaansuppdetail_t ON penerimaansuppdetail_t.penerimaansuppdetail_id = returpenerimaanobatdetail_t.penerimaansuppdetail_id
     LEFT JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT returpenerimaanobatdetail_t_1.penerimaansuppdetail_id,
            sum(returpenerimaanobatdetail_t_1.qty_retur) AS total_retur
           FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1
          GROUP BY returpenerimaanobatdetail_t_1.penerimaansuppdetail_id) retur ON penerimaansuppdetail_t.penerimaansuppdetail_id = retur.penerimaansuppdetail_id
     LEFT JOIN ( SELECT returpenerimaanobatdetail_t_1.penerimaanobatdetail_id,
            sum(returpenerimaanobatdetail_t_1.qty_retur) AS on_retur
           FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1
          GROUP BY returpenerimaanobatdetail_t_1.penerimaanobatdetail_id) returdetailjumlah ON penerimaanobatdetail_t.penerimaanobatdetail_id = returdetailjumlah.penerimaanobatdetail_id;");
        
        $this->execute('ALTER TABLE "public"."inforeturpenerimaanobatdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201215_064311_migrate_20201215_inforeturpenerimaanobatdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201215_064311_migrate_20201215_inforeturpenerimaanobatdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
