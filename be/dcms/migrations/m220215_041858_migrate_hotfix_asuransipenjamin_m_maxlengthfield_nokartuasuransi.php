<?php

use yii\db\Migration;

/**
 * Class m220215_041858_migrate_hotfix_asuransipenjamin_m_maxlengthfield_nokartuasuransi
 */
class m220215_041858_migrate_hotfix_asuransipenjamin_m_maxlengthfield_nokartuasuransi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            SELECT public.deps_save_and_drop_dependencies(\'public\', \'asuransipasien_m\');
        ');

        $this->execute('
            ALTER TABLE "public"."asuransipasien_m" 
            ALTER COLUMN "nokartuasuransi" TYPE varchar(255) COLLATE "pg_catalog"."default";
        ');

        $this->execute('
            SELECT public.deps_restore_dependencies(\'public\', \'asuransipasien_m\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220215_041858_migrate_hotfix_asuransipenjamin_m_maxlengthfield_nokartuasuransi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220215_041858_migrate_hotfix_asuransipenjamin_m_maxlengthfield_nokartuasuransi cannot be reverted.\n";

        return false;
    }
    */
}
