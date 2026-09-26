<?php

use yii\db\Migration;

/**
 * Class m220613_092132_migrate_MHG2523_view_tindakanluarbedah_v
 */
class m220613_092132_migrate_MHG2523_view_tindakanluarbedah_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."tindakanluarbedah_v";
        ');

        $this->execute('
             CREATE VIEW "public"."tindakanluarbedah_v" AS  
             SELECT tindakanluarbedah_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_kode,
                daftartindakan_m.daftartindakan_nama,
                tindakanluarbedah_mp.tindakanluarbedah_id,
                tindakanluarbedah.daftartindakan_kode AS tindakanluarbedah_kode,
                tindakanluarbedah.daftartindakan_nama AS tindakanluarbedah_nama,
                tindakanluarbedah_mp.qty, 
                tindakanluarbedah_mp.is_ditagihkan
               FROM tindakanluarbedah_mp
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama,
                        a.daftartindakan_kode
                       FROM daftartindakan_m a) daftartindakan_m ON tindakanluarbedah_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama,
                        a.daftartindakan_kode
                       FROM daftartindakan_m a) tindakanluarbedah ON tindakanluarbedah_mp.tindakanluarbedah_id = tindakanluarbedah.daftartindakan_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220613_092132_migrate_MHG2523_view_tindakanluarbedah_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220613_092132_migrate_MHG2523_view_tindakanluarbedah_v cannot be reverted.\n";

        return false;
    }
    */
}
