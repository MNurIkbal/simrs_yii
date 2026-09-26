<?php

use yii\db\Migration;

/**
 * Class m231220_135122_MHG5466_CREATE_VIEW_satusehat_pasien_v
 */
class m231220_135122_MHG5466_CREATE_VIEW_satusehat_pasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."satusehat_pasien_v"');

        $this->execute("CREATE OR REPLACE VIEW public.satusehat_pasien_v
        AS SELECT data.pasien_id,
            data.no_rekam_medik,
            data.nama_pasien,
            COALESCE(data.jenisidentitas::text, data.jenis_id_pasien) AS jenis_identitas,
            COALESCE(data.no_identitas_pasien::text, data.nomor_id_pasien) AS nomor_id_pasien,
            data.satusehat_pasien_id
           FROM ( SELECT pasien_m.pasien_id,
                    pasien_m.no_rekam_medik,
                    pasien_m.nama_pasien,
                    pasien_m.jenisidentitas,
                    pasien_m.no_identitas_pasien,
                    pasien_m.additional_pasien,
                    replace(((pasien_m.additional_pasien::json -> 0) -> 'jenisidentitas'::text)::text, ''::text, ''::text) AS jenis_id_pasien,
                    replace(((pasien_m.additional_pasien::json -> 0) -> 'no_identitas_pasien'::text)::text, ''::text, ''::text) AS nomor_id_pasien,
                    satusehat_pasien.satusehat_pasien_id
                   FROM pasien_m
                     LEFT JOIN ( SELECT p_satusehat.pasien_id,
                            p_satusehat.satusehat_pasien_id,
                            p_satusehat.satusehat_integration_id
                           FROM pasien_satusehat_m p_satusehat
                          WHERE p_satusehat.satusehat_pasien_id IS NOT NULL AND p_satusehat.is_active = true AND p_satusehat.is_deleted = false) satusehat_pasien ON satusehat_pasien.pasien_id = pasien_m.pasien_id) data
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231220_135122_MHG5466_CREATE_VIEW_satusehat_pasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231220_135122_MHG5466_CREATE_VIEW_satusehat_pasien_v cannot be reverted.\n";

        return false;
    }
    */
}
