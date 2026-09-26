<?php

use yii\db\Migration;

/**
 * Class m240124_135650_migrate_gls_307_prescribe_v
 */
class m240124_135650_migrate_gls_307_prescribe_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->execute("DROP VIEW IF EXISTS prescribe_v");
	           $prescribe_v = file_get_contents(__DIR__ . '/definitions/prescribe_v.view.sql');
	           $this->execute($prescribe_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240124_135650_migrate_gls_307_prescribe_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240124_135650_migrate_gls_307_prescribe_v cannot be reverted.\n";

        return false;
    }
    */
}
