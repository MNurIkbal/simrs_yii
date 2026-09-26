<?php

use yii\db\Migration;

/**
 * Class m200810_011450_migrate_mhkn_20200810_optimize
 */
class m200810_011450_migrate_mhkn_20200810_optimize extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."antrianfarmasi_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."apgarscore_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."asesmenmedis_t" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."asuhankeperawatan_t" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."batalmutasibarang_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."bayaruangmuka_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."bodymassindex_m" ALTER COLUMN "last_modified_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."caramasuk_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."chat_r" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."closingkasir_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."diagnosaobat_mp" ALTER COLUMN "last_modified_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."gantidokterpj_t" ALTER COLUMN "last_modified_date" DROP NOT NULL;');

         $this->execute('ALTER TABLE "public"."gcs_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."gelarbelakang_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."golonganumurlab_m" ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."hasilbridgingradiologi_t" ALTER COLUMN "created_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."implementasi_t" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."indexing_m" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."instruksi_t" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."instruksitindakan_t" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."instruksitindakanbmhp_t" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."invoicetagihan_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."invoicetagihandetail_t" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."jenisanastesi_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."jenisjabatan_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."jeniskelas_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."jenistarif_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."jenistarifpenjamin_mp" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."kabupaten_m" ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."kasuspenyakitdiagnosa_mp" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."kasuspenyakitobat_mp" 
                            ALTER COLUMN "last_modified_date" DROP NOT NULL,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."kategoritindakan_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."kelaspelayanan_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."kelompokdiagnosa_m" 
                            ALTER COLUMN "last_modified_date" DROP DEFAULT,
                            ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."kelompokmenu_k" 
                           ALTER COLUMN "last_modified_date" DROP DEFAULT,
                           ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."kelompoktindakan_m" 
                           ALTER COLUMN "last_modified_date" DROP DEFAULT,
                           ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."klasifikasipasien_m" 
                           ALTER COLUMN "last_modified_date" DROP DEFAULT,
                           ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."klasifikasitekanandarah_m" 
                           ALTER COLUMN "last_modified_date" DROP DEFAULT,
                           ALTER COLUMN "deleted_date" DROP DEFAULT;');

         $this->execute('ALTER TABLE "public"."konfigantrian_m" 
                           ALTER COLUMN "last_modified_date" DROP DEFAULT,
                           ALTER COLUMN "deleted_date" DROP DEFAULT;');

       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200810_011450_migrate_mhkn_20200810_optimize cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200810_011450_migrate_mhkn_20200810_optimize cannot be reverted.\n";

        return false;
    }
    */
}
