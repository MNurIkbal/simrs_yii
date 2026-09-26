<?php

use yii\db\Migration;

/**
 * Class m181220_093219_alter_view_cetakjadwalpoli_v
 */
class m181220_093219_alter_view_cetakjadwalpoli_v extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("
            DROP VIEW IF EXISTS cetakjadwalpoli_v
        ");
        $this->execute("
            CREATE OR REPLACE VIEW cetakjadwalpoli_v
            AS
            SELECT jadwalbukapoli_m.ruangan_id,
                ruangan_m.ruangan_nama,
                jadwalbukapoli_m.hari,
                jadwalbukapoli_m.jam_mulai,
                jadwalbukapoli_m.jam_tutup,
                jadwalbukapoli_m.is_active,
                jadwalbukapoli_m.shift_id,
                shift_m.shift_nama,
                shift_m.shift_jamawal,
                shift_m.shift_jamakhir,
                hari.lookup_name AS hari_nama
            FROM (((jadwalbukapoli_m
                LEFT JOIN ruangan_m ON ((jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id)))
                LEFT JOIN shift_m ON ((jadwalbukapoli_m.shift_id = shift_m.shift_id)))
                JOIN lookup_m hari ON ((jadwalbukapoli_m.hari = hari.lookup_id)))
            WHERE ((jadwalbukapoli_m.is_deleted = false) AND (ruangan_m.instalasi_id = 1));
        ");

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("
            DROP VIEW IF EXISTS cetakjadwalpoli_v
        ");
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181220_093219_alter_view_cetakjadwalpoli_v cannot be reverted.\n";

        return false;
    }
    */
}
