<?php

use yii\db\Migration;

/**
 * Class m220921_091223_view_gateway_alkes
 */
class m220921_091223_view_gateway_alkes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_alkes_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_alkes_v\"
        AS SELECT obatalkes_m.obatalkes_id,
            obatalkes_m.obatalkes_kode,
            obatalkes_m.obatalkes_nama,
            obatalkes_m.obatalkes_namalain,
            jenis.jenisobatalkes_id,
            jenis.jenisobatalkes_nama,
            jenis.group_obat
           FROM obatalkes_m
             JOIN ( SELECT a.jenisobatalkes_id,
                    a.jenisobatalkes_nama,
                    group_jenisobat.lookup_name AS group_obat
                   FROM jenisobatalkes_m a
                     JOIN ( SELECT a1.lookup_id,
                            a1.lookup_name
                           FROM lookup_m a1) group_jenisobat ON a.group_jenisobat = group_jenisobat.lookup_id
                  WHERE a.group_jenisobat = 620) jenis ON obatalkes_m.jenisobatalkes_id = jenis.jenisobatalkes_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_091223_view_gateway_alkes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_091223_view_gateway_alkes cannot be reverted.\n";

        return false;
    }
    */
}
