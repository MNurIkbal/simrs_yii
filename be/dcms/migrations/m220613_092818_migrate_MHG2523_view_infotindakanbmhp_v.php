<?php

use yii\db\Migration;

/**
 * Class m220613_092818_migrate_MHG2523_view_infotindakanbmhp_v
 */
class m220613_092818_migrate_MHG2523_view_infotindakanbmhp_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infotindakanbmhp_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infotindakanbmhp_v" AS  SELECT tindakanbmhp_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                ( SELECT array_to_json(array_agg(row_to_json(detail.*))) AS array_to_json
                       FROM ( SELECT x.daftartindakan_id,
                                x.obatalkes_id,
                                obatalkes_m.obatalkes_nama,
                                x.satuaninput_id,
                                satuan_input.satuanunit_nama AS satuaninput_nama,
                                x.satuanunit_id,
                                satuanunit_m.satuanunit_nama,
                                x.nilai_konversi,
                                x.qty_input,
                                x.qty_konversi,
                                grup.lookup_name AS nama_grup
                               FROM tindakanbmhp_mp x
                                 JOIN ( SELECT a.obatalkes_id, 
                                        a.obatalkes_nama,
                                        a.jenisobatalkes_id
                                       FROM obatalkes_m a) obatalkes_m ON x.obatalkes_id = obatalkes_m.obatalkes_id
                                 JOIN ( SELECT a.satuanunit_id,
                                        a.satuanunit_nama
                                       FROM satuanunit_m a) satuanunit_m ON x.satuanunit_id = satuanunit_m.satuanunit_id
                                 JOIN ( SELECT a.satuanunit_id,
                                        a.satuanunit_nama
                                       FROM satuanunit_m a) satuan_input ON x.satuaninput_id = satuan_input.satuanunit_id
                                 LEFT JOIN ( SELECT a.jenisobatalkes_id,
                                        a.group_jenisobat
                                       FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                                 LEFT JOIN ( SELECT a.lookup_id,
                                        a.lookup_name
                                       FROM lookup_m a) grup ON jenisobatalkes_m.group_jenisobat = grup.lookup_id
                              WHERE tindakanbmhp_mp.daftartindakan_id = x.daftartindakan_id AND x.is_deleted = false) detail) AS detail_bmhp
               FROM tindakanbmhp_mp
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama
                       FROM daftartindakan_m a) daftartindakan_m ON tindakanbmhp_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
              WHERE tindakanbmhp_mp.is_deleted = false
              GROUP BY tindakanbmhp_mp.daftartindakan_id, daftartindakan_m.daftartindakan_nama
            UNION ALL
             SELECT tindakanalkes_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                ( SELECT array_to_json(array_agg(row_to_json(detail.*))) AS array_to_json
                       FROM ( SELECT x.daftartindakan_id,
                                x.obatalkes_id,
                                obatalkes_m.obatalkes_nama,
                                x.satuaninput_id,
                                satuan_input.satuanunit_nama AS satuaninput_nama,
                                x.satuanunit_id,
                                satuanunit_m.satuanunit_nama,
                                x.nilai_konversi,
                                x.qty_input,
                                x.qty_konversi,
                                grup.lookup_name AS nama_grup
                               FROM tindakanalkes_mp x
                                 JOIN ( SELECT a.obatalkes_id,
                                        a.obatalkes_nama,
                                        a.jenisobatalkes_id
                                       FROM obatalkes_m a) obatalkes_m ON x.obatalkes_id = obatalkes_m.obatalkes_id
                                 JOIN ( SELECT a.satuanunit_id,
                                        a.satuanunit_nama
                                       FROM satuanunit_m a) satuanunit_m ON x.satuanunit_id = satuanunit_m.satuanunit_id
                                 JOIN ( SELECT a.satuanunit_id,
                                        a.satuanunit_nama
                                       FROM satuanunit_m a) satuan_input ON x.satuaninput_id = satuan_input.satuanunit_id
                                 LEFT JOIN ( SELECT a.jenisobatalkes_id,
                                        a.group_jenisobat
                                       FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                                 LEFT JOIN ( SELECT a.lookup_id,
                                        a.lookup_name
                                       FROM lookup_m a) grup ON jenisobatalkes_m.group_jenisobat = grup.lookup_id
                              WHERE tindakanalkes_mp.daftartindakan_id = x.daftartindakan_id AND x.is_deleted = false) detail) AS detail_bmhp
               FROM tindakanalkes_mp
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama
                       FROM daftartindakan_m a) daftartindakan_m ON tindakanalkes_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
              WHERE tindakanalkes_mp.is_deleted = false
              GROUP BY tindakanalkes_mp.daftartindakan_id, daftartindakan_m.daftartindakan_nama;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220613_092818_migrate_MHG2523_view_infotindakanbmhp_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220613_092818_migrate_MHG2523_view_infotindakanbmhp_v cannot be reverted.\n";

        return false;
    }
    */
}
