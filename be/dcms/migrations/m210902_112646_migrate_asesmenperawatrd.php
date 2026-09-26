<?php

use yii\db\Migration;

/**
 * Class m210902_112646_migrate_asesmenperawatrd
 */
class m210902_112646_migrate_asesmenperawatrd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ALTER COLUMN "skala_nyeri" TYPE int2 USING "skala_nyeri"::int2;');

        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN if not exists "pilih_skala" varchar(20) COLLATE "pg_catalog"."default" DEFAULT NULL::character varying;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210902_112646_migrate_asesmenperawatrd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210902_112646_migrate_asesmenperawatrd cannot be reverted.\n";

        return false;
    }
    */
}
