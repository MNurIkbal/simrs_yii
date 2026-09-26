<?php

use yii\db\Migration;

/**
 * Class m210428_085442_migrate_20210428_inforeturpenerimaanbarang_v
 */
class m210428_085442_migrate_20210428_inforeturpenerimaanbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."inforeturpenerimaanbarang_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inforeturpenerimaanbarang_v\" AS  SELECT returpenerimaanbarang_t.returpenerimaanbarang_id,
    returpenerimaanbarang_t.no_returpenerimaanbarang,
    returpenerimaanbarang_t.tgl_retur,
    returpenerimaanbarang_t.alasan_retur,
    pegawai_m.nama_pegawai AS pegawai_retur,
    supplier.supplier_nama,
    penerimaansupp_t.no_penerimaan,
    penerimaansupp_t.no_faktur
   FROM returpenerimaanbarang_t
     LEFT JOIN penerimaansupp_t ON penerimaansupp_t.penerimaansupp_id = returpenerimaanbarang_t.penerimaansupp_id
     LEFT JOIN pegawai_m ON returpenerimaanbarang_t.pegawairetur_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT returpenerimaanbarangdetail_t.returpenerimaanbarang_id,
            supplier_m.supplier_nama
           FROM returpenerimaanbarangdetail_t
             JOIN penerimaansuppdetail_t ON returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
             JOIN penerimaansupp_t penerimaansupp_t_1 ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t_1.penerimaansupp_id
             JOIN supplier_m ON penerimaansupp_t_1.supplier_id = supplier_m.supplier_id
          GROUP BY returpenerimaanbarangdetail_t.returpenerimaanbarang_id, supplier_m.supplier_nama) supplier ON returpenerimaanbarang_t.returpenerimaanbarang_id = supplier.returpenerimaanbarang_id;");
        
        $this->execute('ALTER TABLE "public"."inforeturpenerimaanbarang_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210428_085442_migrate_20210428_inforeturpenerimaanbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210428_085442_migrate_20210428_inforeturpenerimaanbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
