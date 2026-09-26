<?php

use yii\db\Migration;

/**
 * Class m201007_063951_migrate_20201007_asesmenperawatrd
 */
class m201007_063951_migrate_20201007_asesmenperawatrd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" DROP COLUMN IF EXISTS "gangguan_thermoregulasi_hypertermi";');

        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" DROP COLUMN IF EXISTS "gangguan_thermoregulasi_hypotermi";');

        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "gangguan_thermoregulasi_tipe" text ;');
        
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "gangguan_thermoregulasi_nilai" text ;');
        
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "survey_kepala_utuh" text ;');
       
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "ireguler_tipe" text ;');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201007_063951_migrate_20201007_asesmenperawatrd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201007_063951_migrate_20201007_asesmenperawatrd cannot be reverted.\n";

        return false;
    }
    */
}
