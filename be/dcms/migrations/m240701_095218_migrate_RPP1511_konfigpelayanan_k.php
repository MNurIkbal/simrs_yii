<?php

use yii\db\Migration;

/**
 * Class m240701_095218_migrate_RPP1511_konfigpelayanan_k
 */
class m240701_095218_migrate_RPP1511_konfigpelayanan_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('UPDATE 
	konfigpelayanan_k
SET additional_data = \'{
                "url": "/api/usg/form-modal-usg?id=#pendaftaran_id#&ruangan_id=#ruangan_id#&type=usg",
                "wrapper": "#modal-lab .modal-content",
                "icon": "fa-plus"
                }\'
WHERE nama_fitur = \'usg\'
AND instalasi_id IN (1,2,3);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240701_095218_migrate_RPP1511_konfigpelayanan_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240701_095218_migrate_RPP1511_konfigpelayanan_k cannot be reverted.\n";

        return false;
    }
    */
}
