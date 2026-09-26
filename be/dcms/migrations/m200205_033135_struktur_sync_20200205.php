<?php

use yii\db\Migration;

/**
 * Class m200205_033135_struktur_sync_20200205
 */
class m200205_033135_struktur_sync_20200205 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."diagnosakep_m" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');

        $this->execute('ALTER TABLE "public"."groupmargin_m" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');

        $this->execute('ALTER TABLE "public"."logactivity_r" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');

        $this->execute('ALTER TABLE "public"."marginkhusus_k" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');

        $this->execute('ALTER TABLE "public"."marginkhususdetail_k" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."monitorbpjs_m" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."monitorbpjsdetail_m" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('COMMENT ON COLUMN "public"."monitorsetdiagnosa_t"."diag_penyerta" IS \'json format\';');
        
        $this->execute('COMMENT ON COLUMN "public"."monitorsetdiagnosa_t"."diag_tindakan" IS \'json format\';');
        
        $this->execute('COMMENT ON COLUMN "public"."monitorsetdiagnosa_t"."total_naikkelas" IS \'naik kelas\';');
        
        $this->execute('COMMENT ON COLUMN "public"."monitorsetdiagnosa_t"."total_kelaspelayanan" IS \'kelas saat ini\';');
        
        $this->execute('ALTER TABLE "public"."monitorsetdiagnosa_t" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."kabupaten_m" ADD CONSTRAINT "fk_kabupaten_propinsi" FOREIGN KEY ("propinsi_id") REFERENCES "public"."propinsi_m" ("propinsi_id") ON DELETE RESTRICT ON UPDATE CASCADE;');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200205_033135_struktur_sync_20200205 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200205_033135_struktur_sync_20200205 cannot be reverted.\n";

        return false;
    }
    */
}
