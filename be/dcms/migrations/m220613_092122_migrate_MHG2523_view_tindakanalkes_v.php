<?php

use yii\db\Migration;

/**
 * Class m220613_092122_migrate_MHG2523_view_tindakanalkes_v
 */
class m220613_092122_migrate_MHG2523_view_tindakanalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."tindakanalkes_v";
        ');

        $this->execute('
            CREATE VIEW "public"."tindakanalkes_v" AS  
            SELECT tindakanalkes_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanalkes_mp.obatalkes_id,
                obatalkes_m.obatalkes_nama,
                tindakanalkes_mp.satuaninput_id,
                satuan_input.satuanunit_nama AS satuaninput_nama,
                tindakanalkes_mp.satuanunit_id,
                satuanunit_m.satuanunit_nama,
                tindakanalkes_mp.nilai_konversi,
                tindakanalkes_mp.qty_input, 
                tindakanalkes_mp.qty_konversi
               FROM tindakanalkes_mp
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama
                       FROM daftartindakan_m a) daftartindakan_m ON tindakanalkes_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN ( SELECT a.obatalkes_id,
                        a.obatalkes_nama
                       FROM obatalkes_m a) obatalkes_m ON tindakanalkes_mp.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN ( SELECT a.satuanunit_id,
                        a.satuanunit_nama
                       FROM satuanunit_m a) satuanunit_m ON tindakanalkes_mp.satuanunit_id = satuanunit_m.satuanunit_id
                 JOIN ( SELECT a.satuanunit_id,
                        a.satuanunit_nama
                       FROM satuanunit_m a) satuan_input ON tindakanalkes_mp.satuaninput_id = satuan_input.satuanunit_id
              WHERE tindakanalkes_mp.is_deleted = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220613_092122_migrate_MHG2523_view_tindakanalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220613_092122_migrate_MHG2523_view_tindakanalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
