<?php

use yii\db\Migration;

/**
 * Class m251031_082952_plafonbpjs_m_add_column_ruangan
 */
class m251031_082952_plafonbpjs_m_add_column_ruangan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("BEGIN;");
        $this->execute("ALTER TABLE public.plafonbpjs_m
                            ADD COLUMN IF NOT EXISTS list_ruangan_id text;");
        $this->execute("COMMIT;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251031_082952_plafonbpjs_m_add_column_ruangan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251031_082952_plafonbpjs_m_add_column_ruangan cannot be reverted.\n";

        return false;
    }
    */
}
