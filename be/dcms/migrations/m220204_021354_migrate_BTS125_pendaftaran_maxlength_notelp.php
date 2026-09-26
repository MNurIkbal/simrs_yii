<?php

use yii\db\Migration;

/**
 * Class m220204_021354_migrate_BTS125_pendaftaran_maxlength_notelp
 */
class m220204_021354_migrate_BTS125_pendaftaran_maxlength_notelp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            SELECT public.deps_save_and_drop_dependencies(\'public\', \'pendaftaranol_t\');
        ');

        $this->execute('
            ALTER TABLE "public"."pendaftaranol_t" 
            ALTER COLUMN "no_telepon_pasien" TYPE varchar(50) COLLATE "pg_catalog"."default";
        ');

        $this->execute('
            SELECT public.deps_restore_dependencies(\'public\', \'pendaftaranol_t\');
        ');

        $this->execute('
            DROP TRIGGER IF EXISTS "pasien_r_update" ON "public"."pasien_m";
        ');

        $this->execute('
            SELECT public.deps_save_and_drop_dependencies(\'public\', \'pasien_r\');
        ');

        $this->execute('
            ALTER TABLE "public"."pasien_r" 
            ALTER COLUMN "no_telepon_pasien" TYPE varchar(50) COLLATE "pg_catalog"."default";
        ');

        $this->execute('
            SELECT public.deps_restore_dependencies(\'public\', \'pasien_r\');
        ');

        $this->execute('
            SELECT public.deps_save_and_drop_dependencies(\'public\', \'pasien_m\');
        ');

        $this->execute('
            ALTER TABLE "public"."pasien_m" 
            ALTER COLUMN "no_telepon_pasien" TYPE varchar(50) COLLATE "pg_catalog"."default";
        ');

        $this->execute('
            SELECT public.deps_restore_dependencies(\'public\', \'pasien_m\');
        ');

        $this->execute('
            CREATE TRIGGER "pasien_r_update" AFTER UPDATE OF "tempat_lahir", "no_rekam_medik", "alamat_sekarang", "no_telepon_pasien", "no_mobile_pasien", "no_identitas_pasien", "nama_pasien", "alamatemail", "jenisidentitas", "namadepan", "alamat_pasien", "jeniskelamin", "tanggal_lahir" ON "public"."pasien_m"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."pasien_r_update"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220204_021354_migrate_BTS125_pendaftaran_maxlength_notelp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220204_021354_migrate_BTS125_pendaftaran_maxlength_notelp cannot be reverted.\n";

        return false;
    }
    */
}
