<?php

use yii\db\Migration;

/**
 * Class m250828_094003_add_new_columns_soapfisioterapi_t
 */
class m250828_094003_add_new_columns_soapfisioterapi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute("BEGIN;");
        $this->execute("ALTER TABLE public.soapfisioterapi_t
                            ADD COLUMN IF NOT EXISTS diagnosa_fungsi json,
                            ADD COLUMN IF NOT EXISTS prosedur_kerja json,
                            ADD COLUMN IF NOT EXISTS goal text;");
        $this->execute("COMMIT;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250828_094003_add_new_columns_soapfisioterapi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250828_094003_add_new_columns_soapfisioterapi_t cannot be reverted.\n";

        return false;
    }
    */
}
