<?php

use yii\db\Migration;

/**
 * Class m201223_043353_improve_update_resumemedisri_update_column
 */
class m201223_043353_improve_update_resumemedisri_update_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('UPDATE resumemedisri_t SET diag_awal = NULL;');

        $this->execute('ALTER TABLE "public"."resumemedisri_t" ALTER COLUMN "diag_awal" TYPE json USING "diag_awal"::json;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201223_043353_improve_update_resumemedisri_update_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201223_043353_improve_update_resumemedisri_update_column cannot be reverted.\n";

        return false;
    }
    */
}
