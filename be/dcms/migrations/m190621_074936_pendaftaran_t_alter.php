<?php

use yii\db\Migration;

/**
 * Class m190621_074936_pendaftaran_t_alter
 */
class m190621_074936_pendaftaran_t_alter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
     ALTER TABLE "public"."pendaftaran_t" 
  ADD COLUMN "is_skl" bool DEFAULT false;
        ');

          $this->execute('
     COMMENT ON COLUMN "public"."pendaftaran_t"."is_skl" IS \'surat keterangan lahir\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190621_074936_pendaftaran_t_alter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190621_074936_pendaftaran_t_alter cannot be reverted.\n";

        return false;
    }
    */
}
