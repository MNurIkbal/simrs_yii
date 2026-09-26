<?php

use yii\db\Migration;

/**
 * Class m201022_044400_migrate_mhkn_20201022_pendaftaranol_t_2991
 */
class m201022_044400_migrate_mhkn_20201022_pendaftaranol_t_2991 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaranol_t" 
  ADD IF NOT EXISTS "jenisidentitas" varchar(20) COLLATE "pg_catalog"."default" DEFAULT 9999,
  ADD IF NOT EXISTS "no_identitas_pasien" varchar(30) COLLATE "pg_catalog"."default",
  ADD IF NOT EXISTS "namadepan" varchar(20) COLLATE "pg_catalog"."default",
  ADD IF NOT EXISTS "nama_pasien" varchar(50) COLLATE "pg_catalog"."default",
  ADD IF NOT EXISTS "tempat_lahir" varchar(25) COLLATE "pg_catalog"."default",
  ADD IF NOT EXISTS "tanggal_lahir" date,
  ADD IF NOT EXISTS "jeniskelamin" varchar(20) COLLATE "pg_catalog"."default" DEFAULT 9999,
  ADD IF NOT EXISTS "no_telepon_pasien" varchar(15) COLLATE "pg_catalog"."default",
  ADD IF NOT EXISTS "alamat_pasien" text COLLATE "pg_catalog"."default";
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_044400_migrate_mhkn_20201022_pendaftaranol_t_2991 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_044400_migrate_mhkn_20201022_pendaftaranol_t_2991 cannot be reverted.\n";

        return false;
    }
    */
}
