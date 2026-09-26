<?php

use yii\db\Migration;

/**
 * Class m190401_062143_update_jeniskegiatandetail_v
 */
class m190401_062143_update_jeniskegiatandetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW jeniskegiatandetail_v AS 
             SELECT jeniskegiatantindakan_m.jeniskegiatantindakan_id,
                jeniskegiatantindakan_m.jeniskegiatantindakan_nama,
                jeniskegiatantindakan_m.is_active,
                daftartindakan_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama
               FROM jeniskegiatantindakan_m
                 JOIN daftartindakan_m ON jeniskegiatantindakan_m.jeniskegiatantindakan_id = daftartindakan_m.jeniskegiatantindakan_id
              WHERE jeniskegiatantindakan_m.is_deleted = false AND daftartindakan_m.is_deleted = false
              ORDER BY jeniskegiatantindakan_m.jeniskegiatantindakan_id;
        ');

        $this->execute('
            ALTER TABLE jeniskegiatandetail_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_062143_update_jeniskegiatandetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_062143_update_jeniskegiatandetail_v cannot be reverted.\n";

        return false;
    }
    */
}
