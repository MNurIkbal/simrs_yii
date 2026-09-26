<?php

use yii\db\Migration;

/**
 * Class m220329_100711_migrate_CDH288_insertandupdate_ppn
 */
class m220329_100711_migrate_CDH288_insertandupdate_ppn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
			INSERT INTO public.pajak_m ( pajak_kode, pajak_name, pajak_persen, akunmasuk_id, akunkeluar_id, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES ( 'PPN01042022', 'PPN11', 11, NULL, NULL, NULL, CURRENT_TIMESTAMP, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);"
		);

        $this->execute("
			UPDATE pajak_m set is_active = false where pajak_persen = 10;
		");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220329_100711_migrate_CDH288_insertandupdate_ppn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220329_100711_migrate_CDH288_insertandupdate_ppn cannot be reverted.\n";

        return false;
    }
    */
}
