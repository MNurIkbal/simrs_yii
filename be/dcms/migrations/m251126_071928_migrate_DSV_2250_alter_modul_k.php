<?php

use yii\db\Migration;

/**
 * Class m251126_071928_migrate_DSV_2250_alter_modul_k
 */
class m251126_071928_migrate_DSV_2250_alter_modul_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.modul_k ALTER COLUMN url_modul TYPE TEXT;');

        $this->execute('ALTER TABLE public.modul_k ADD COLUMN IF NOT EXISTS open_newtab BOOL DEFAULT true;');

        $this->execute('ALTER TABLE public.modul_k ADD COLUMN IF NOT EXISTS is_external_link BOOL DEFAULT false;'); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251126_071928_migrate_DSV_2250_alter_modul_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251126_071928_migrate_DSV_2250_alter_modul_k cannot be reverted.\n";

        return false;
    }
    */
}
