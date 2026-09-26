<?php

use yii\db\Migration;

/**
 * Class m210902_140600_migrate_asesmenrdresikojatuhsydney_t
 */
class m210902_140600_migrate_asesmenrdresikojatuhsydney_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."asesmenrdresikojatuhsydney_t" ADD COLUMN if not exists "is_disorientasi" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."asesmenrdresikojatuhsydney_t" ADD COLUMN if not exists "disorientasi" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."asesmenrdresikojatuhsydney_t" ADD COLUMN if not exists "skor_disorientasi" float8;');

        $this->execute('ALTER TABLE "public"."asesmenrdresikojatuhsydney_t" ADD COLUMN if not exists "is_agitasi" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."asesmenrdresikojatuhsydney_t" ADD COLUMN if not exists "agitasi" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."asesmenrdresikojatuhsydney_t" ADD COLUMN if not exists "skor_agitasi" float8;');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210902_140600_migrate_asesmenrdresikojatuhsydney_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210902_140600_migrate_asesmenrdresikojatuhsydney_t cannot be reverted.\n";

        return false;
    }
    */
}
