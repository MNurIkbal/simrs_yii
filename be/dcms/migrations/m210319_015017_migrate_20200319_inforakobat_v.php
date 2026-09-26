<?php

use yii\db\Migration;

/**
 * Class m210319_015017_migrate_20200319_inforakobat_v
 */
class m210319_015017_migrate_20200319_inforakobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.inforakobat_v;');

        $this->execute("
            CREATE VIEW \"public\".\"inforakobat_v\" AS  SELECT rakobat_m.rakobat_id,
    rakobat_m.rakobat_nama,
    rakobat_m.ruangan_id,
    ruangan_m.ruangan_nama,
    rakobat_m.is_active,
        CASE
            WHEN rakobat_m.is_active = true THEN 'AKTIF'::text
            ELSE 'NON AKTIF'::text
        END AS status,
    rakobat_m.is_deleted
   FROM rakobat_m
     JOIN ruangan_m ON rakobat_m.ruangan_id = ruangan_m.ruangan_id;");
        
        $this->execute('ALTER TABLE "public"."inforakobat_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210319_015017_migrate_20200319_inforakobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210319_015017_migrate_20200319_inforakobat_v cannot be reverted.\n";

        return false;
    }
    */
}
