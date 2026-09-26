<?php

use yii\db\Migration;

/**
 * Class m220728_073432_tambah_field_is_sep_mandatory_on_edit
 */
class m220728_073432_tambah_field_is_sep_mandatory_on_edit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."konfigsystem_k" 
            ADD COLUMN IF NOT EXISTS "is_sep_mandatory_on_edit" bool DEFAULT true;
        ');

        $this->execute('
            COMMENT ON COLUMN "public"."konfigsystem_k"."is_sep_mandatory_on_edit" IS \'Kebutuhan untuk mandatory atau tidaknya SEP ketika edit pendaftaran\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220728_073432_tambah_field_is_sep_mandatory_on_edit cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220728_073432_tambah_field_is_sep_mandatory_on_edit cannot be reverted.\n";

        return false;
    }
    */
}
