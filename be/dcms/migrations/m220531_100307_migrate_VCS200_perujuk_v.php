<?php

use yii\db\Migration;

/**
 * Class m220531_100307_migrate_VCS200_perujuk_v
 */
class m220531_100307_migrate_VCS200_perujuk_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."perujuk_v";');
        $this->execute("CREATE VIEW \"public\".\"perujuk_v\" AS  SELECT perujuk_m.perujuk_id,
        perujuk_m.asalrujukan_id,
        asalrujukan_m.asalrujukan_nama,
        perujuk_m.namaperujuk,
        perujuk_m.spesialis,
        perujuk_m.alamatlengkap,
        perujuk_m.notelp,
        perujuk_m.is_active,
        asalrujukan_m.is_active AS is_active_asalrujukan,
        perujuk_m.perujuk_kode
       FROM perujuk_m
         LEFT JOIN ( SELECT a.asalrujukan_nama,
                a.is_active,
                a.asalrujukan_id,
                a.is_deleted
               FROM asalrujukan_m a) asalrujukan_m ON asalrujukan_m.asalrujukan_id = perujuk_m.asalrujukan_id
      WHERE perujuk_m.is_deleted = false AND asalrujukan_m.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220531_100307_migrate_VCS200_perujuk_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220531_100307_migrate_VCS200_perujuk_v cannot be reverted.\n";

        return false;
    }
    */
}
