<?php

use yii\db\Migration;

/**
 * Class m200810_023915_migrate_mhkn_20200810_optimize_2
 */
class m200810_023915_migrate_mhkn_20200810_optimize_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."kasuspenyakitobat_mp" ALTER COLUMN "last_modified_date" DROP DEFAULT');
    
    $this->execute('ALTER TABLE "public"."layarantrian_m" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."layarantriandetail_m" ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."loginmobile_k" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."loginpemakai_k" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."lokasirak_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."loket_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."loket_mp" ALTER COLUMN "last_modified_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."masukkamar_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."matauang_m" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."menumodul_k" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."metodeapgar_m" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."metodegcs_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."migration_k" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."mutasibarang_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."mutasiobatdetail_t" ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."mutasiobatruangan_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."notifikasi_m" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."notifikasi_r" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."obatalkespasien_t" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."obatsupplier_m" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pasienbatalperiksa_t" ALTER COLUMN "last_modified_date" DROP NOT NULL;');
    
    $this->execute('ALTER TABLE "public"."pasienbatalpulang_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pasiendirujukkeluar_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                             ALTER COLUMN "last_modified_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pasienpulang_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pbf_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pegawai_m" ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pemakaianbarang_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pemakaianbarangdetail_t" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pemakaianobatdetail_t" ALTER COLUMN "last_modified_date" DROP DEFAULT;');
   
    $this->execute('ALTER TABLE "public"."pemakaianuangmuka_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pembebasantarif_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pemeriksaanlabdetail_m" 
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."penanggungjawab_m" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pendidikankualifikasi_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."penjualanresep_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."peranpengguna_k" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."perujuk_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pesanambulan_t" 
                           ALTER COLUMN "last_modified_date" DROP NOT NULL,
                           ALTER COLUMN "last_modified_date" DROP DEFAULT,
                           ALTER COLUMN "deleted_date" DROP NOT NULL,
                           ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pesanobatalkes_t" 
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

    $this->execute('ALTER TABLE "public"."pesanobatdetail_t" 
                             ALTER COLUMN "last_modified_date" DROP NOT NULL,
                             ALTER COLUMN "last_modified_date" DROP DEFAULT,
                             ALTER COLUMN "deleted_date" DROP DEFAULT;');
  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200810_023915_migrate_mhkn_20200810_optimize_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200810_023915_migrate_mhkn_20200810_optimize_2 cannot be reverted.\n";

        return false;
    }
    */
}
