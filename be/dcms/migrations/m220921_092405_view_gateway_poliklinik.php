<?php

use yii\db\Migration;

/**
 * Class m220921_092405_view_gateway_poliklinik
 */
class m220921_092405_view_gateway_poliklinik extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_poliklinik_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_poliklinik_v\"
        AS SELECT ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama
        FROM ruangan_m
            JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
        WHERE ruangan_m.instalasi_id = 1 AND ruangan_m.is_deleted = false AND ruangan_m.is_active = true;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_092405_view_gateway_poliklinik cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_092405_view_gateway_poliklinik cannot be reverted.\n";

        return false;
    }
    */
}
