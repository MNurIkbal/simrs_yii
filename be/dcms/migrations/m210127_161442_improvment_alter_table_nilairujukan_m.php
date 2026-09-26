<?php

use yii\db\Migration;

/**
 * Class m210127_161442_improvment_alter_table_nilairujukan_m
 */
class m210127_161442_improvment_alter_table_nilairujukan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            select public.deps_save_and_drop_dependencies(\'public\', \'nilairujukan_m\');
        ');

        $this->execute('
            ALTER TABLE "public"."nilairujukan_m" ALTER COLUMN "nilai_rujukan" TYPE text COLLATE "pg_catalog"."default" USING "nilai_rujukan"::text;
        ');

        $this->execute('
            select public.deps_restore_dependencies(\'public\', \'nilairujukan_m\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_161442_improvment_alter_table_nilairujukan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_161442_improvment_alter_table_nilairujukan_m cannot be reverted.\n";

        return false;
    }
    */
}
