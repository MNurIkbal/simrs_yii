<?php

use yii\db\Migration;

/**
 * Class m230509_142235_odoo_mp_obatalkespasien_r
 */
class m230509_142235_odoo_mp_obatalkespasien_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.obatalkespasien_r ADD COLUMN IF NOT EXISTS diskon_payer float8 NULL;");

        $this->execute("ALTER TABLE public.obatalkespasien_r ADD COLUMN IF NOT EXISTS diskon_subpayer float8 NULL;");

        $this->execute("ALTER TABLE public.obatalkespasien_r ADD COLUMN IF NOT EXISTS diskon_pasien float8 NULL;");

        $this->execute("ALTER TABLE public.obatalkespasien_r ADD COLUMN IF NOT EXISTS gross_dijamin_payer float8 NULL;");

        $this->execute("ALTER TABLE public.obatalkespasien_r ADD COLUMN IF NOT EXISTS gross_dijamin_subpayer float8 NULL;");

        $this->execute("ALTER TABLE public.obatalkespasien_r ADD COLUMN IF NOT EXISTS gross_pasien float8 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_142235_odoo_mp_obatalkespasien_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_142235_odoo_mp_obatalkespasien_r cannot be reverted.\n";

        return false;
    }
    */
}
