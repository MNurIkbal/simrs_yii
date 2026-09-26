<?php

use yii\db\Migration;

/**
 * Class m201201_045643_hotfix_suhu_tubuh_asesmen_medis
 */
class m201201_045643_hotfix_suhu_tubuh_asesmen_medis extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->execute('
            select public.deps_save_and_drop_dependencies(\'public\', \'asesmenmedis_t\');
        ');

        $this->execute('
            ALTER TABLE "public"."asesmenmedis_t" ALTER COLUMN "suhu_tubuh" TYPE float8 USING "suhu_tubuh"::float8;
        ');

        $this->execute('
            select public.deps_restore_dependencies(\'public\', \'asesmenmedis_t\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201201_045643_hotfix_suhu_tubuh_asesmen_medis cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201201_045643_hotfix_suhu_tubuh_asesmen_medis cannot be reverted.\n";

        return false;
    }
    */
}
