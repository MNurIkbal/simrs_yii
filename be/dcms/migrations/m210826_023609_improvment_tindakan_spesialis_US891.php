<?php

use yii\db\Migration;

/**
 * Class m210826_023609_improvment_tindakan_spesialis_US891
 */
class m210826_023609_improvment_tindakan_spesialis_US891 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('
            DROP VIEW IF EXISTS tindakanspesialis_v;
        ');


        $this->execute('
            CREATE VIEW "public"."tindakanspesialis_v" AS  
            SELECT spesialis_m.spesialis_id,
                spesialis_m.spesialis_nama,
                daftartindakan_m.daftartindakan_kode,
                daftartindakan_m.daftartindakan_id, 
                daftartindakan_m.daftartindakan_nama,
                daftartindakan_m.daftartindakan_namalainnya,
                tindakanspesialis_mp.is_active,
                tindakanspesialis_mp.is_deleted,
                tindakanspesialis_mp.created_date,
                kategoritindakan_m.kategoritindakan_nama,
                kelompoktindakan_m.kelompoktindakan_nama,
                jeniskegiatantindakan_m.jeniskegiatantindakan_nama,
                groupinacbg_m.groupinacbg_nama
               FROM ((((((tindakanspesialis_mp
                 JOIN spesialis_m ON ((tindakanspesialis_mp.spesialis_id = spesialis_m.spesialis_id)))
                 JOIN daftartindakan_m ON ((tindakanspesialis_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN kategoritindakan_m ON ((daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id)))
                 LEFT JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                 LEFT JOIN jeniskegiatantindakan_m ON ((daftartindakan_m.jeniskegiatantindakan_id = jeniskegiatantindakan_m.jeniskegiatantindakan_id)))
                 LEFT JOIN groupinacbg_m ON ((daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id)))
              WHERE ((tindakanspesialis_mp.is_deleted IS FALSE) AND (tindakanspesialis_mp.is_active IS TRUE));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210826_023609_improvment_tindakan_spesialis_US891 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210826_023609_improvment_tindakan_spesialis_US891 cannot be reverted.\n";

        return false;
    }
    */
}
