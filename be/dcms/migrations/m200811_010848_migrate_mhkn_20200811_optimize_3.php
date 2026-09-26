<?php

use yii\db\Migration;

/**
 * Class m200811_010848_migrate_mhkn_20200811_optimize_3
 */
class m200811_010848_migrate_mhkn_20200811_optimize_3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."bayaruangmuka_t" ALTER COLUMN "deleted_date" DROP NOT NULL;');

        $this->execute('ALTER TABLE "public"."profilpicture_m" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."racikan_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."racikandetail_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."resepturdetail_t" ALTER COLUMN "last_modified_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."resumemedis_t" ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."resumemedisri_t" ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."returbayarpelayanan_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."returresep_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."ruanganpegawai_mp" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."rujukankeluar_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."samplelab_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."satuanlab_m" ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."setorbank_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."shift_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."stokbarang_r" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."stokdarah_r" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."subrak_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."sumberdana_m" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."sysdia_m" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."terimamutasiobatdetail_t" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."tindakanbmhp_mp" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."tugaspengguna_k" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."unitkerja_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

        $this->execute('ALTER TABLE "public"."warnadokrekammedik_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200811_010848_migrate_mhkn_20200811_optimize_3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200811_010848_migrate_mhkn_20200811_optimize_3 cannot be reverted.\n";

        return false;
    }
    */
}
