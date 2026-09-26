<?php

use yii\db\Migration;

/**
 * Class m220921_094347_view_gateway_satuankonversi
 */
class m220921_094347_view_gateway_satuankonversi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_satuankonversi_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_satuankonversi_v\"
        AS SELECT obatalkes_m.obatalkes_id,
            obatalkes_m.obatalkes_kode,
            obatalkes_m.obatalkes_nama,
            satuankonversi_m.data_konversi
        FROM obatalkes_m
            JOIN ( SELECT obatalkes.obatalkes_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                        FROM ( SELECT besar.satuanunit_id AS satuanbesar_id,
                                    besar.satuanunit_nama AS satuan_besar,
                                    kecil.satuanunit_id AS satuankecil_id,
                                    kecil.satuanunit_nama AS satuan_kecil,
                                    satuankonversi_m_1.nilai_konversi
                                FROM satuankonversi_m satuankonversi_m_1
                                    JOIN ( SELECT a.satuanunit_id,
                                            a.satuanunit_nama,
                                            a.satuanunit_singkatan
                                        FROM satuanunit_m a) besar ON satuankonversi_m_1.satuanbesar_id = besar.satuanunit_id
                                    JOIN ( SELECT a.satuanunit_id,
                                            a.satuanunit_nama,
                                            a.satuanunit_singkatan
                                        FROM satuanunit_m a) kecil ON satuankonversi_m_1.satuankecil_id = kecil.satuanunit_id
                                WHERE satuankonversi_m_1.is_deleted = false AND satuankonversi_m_1.is_active = true AND obatalkes.obatalkes_id = satuankonversi_m_1.obatalkes_id) d) AS data_konversi
                FROM obatalkes_m obatalkes
                GROUP BY obatalkes.obatalkes_id) satuankonversi_m ON obatalkes_m.obatalkes_id = satuankonversi_m.obatalkes_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_094347_view_gateway_satuankonversi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_094347_view_gateway_satuankonversi cannot be reverted.\n";

        return false;
    }
    */
}
