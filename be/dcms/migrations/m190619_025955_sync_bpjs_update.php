<?php

use yii\db\Migration;

/**
 * Class m190619_025955_sync_bpjs_update
 */
class m190619_025955_sync_bpjs_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
   DROP VIEW sync_bpjs;
        ');

        $this->execute('
    CREATE OR REPLACE VIEW sync_bpjs AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.bpjs_id,
    pendaftaran_t.no_pendaftaran AS no_reg,
    bpjs_t.tglsep,
    bpjs_t.nosep AS no_rujukan,
    bpjs_t.nokartuasuransi,
    bpjs_t.tglrujukan,
    bpjs_t.norujukan,
    bpjs_t.catatansep AS catatan,
    bpjs_t.additional_data AS response,
    bpjs_t.additional_request AS request
   FROM pendaftaran_t
     JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id;
        ');

        $this->execute('
    ALTER TABLE sync_bpjs
  OWNER TO postgres;
        ');

/**
     * sync_bpjs_bu
     */

          $this->execute('
    DROP VIEW sync_bpjs_bu;
        ');

            $this->execute('
    CREATE OR REPLACE VIEW sync_bpjs_bu AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.bpjs_id,
    pendaftaran_t.no_pendaftaran AS no_reg,
    bpjs_t.tglsep,
    bpjs_t.nosep AS no_rujukan,
    bpjs_t.nokartuasuransi,
    bpjs_t.tglrujukan,
    bpjs_t.norujukan,
    bpjs_t.catatansep AS catatan,
    bpjs_t.additional_data AS response,
    bpjs_t.additional_request AS request
   FROM pendaftaran_t
     JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id;
        ');

             $this->execute('
    ALTER TABLE sync_bpjs_bu
  OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190619_025955_sync_bpjs_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190619_025955_sync_bpjs_update cannot be reverted.\n";

        return false;
    }
    */
}
