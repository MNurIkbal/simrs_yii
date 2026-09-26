<?php

use yii\db\Migration;

/**
 * Class m190502_100857_laporan10besarpenyakit_v_update
 */
class m190502_100857_laporan10besarpenyakit_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      DROP VIEW laporan10besarpenyakit_v;
        ');

        $this->execute('
         CREATE OR REPLACE VIEW laporan10besarpenyakit_v AS 
 SELECT koreksidiagnosa_t.diagnosa_id,
    diagnosa_m.diagnosa_kode,
    diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
    koreksidiagnosa_t.tgl_koreksidiagnosa AS tglmorbiditas,
    koreksidiagnosa_t.koreksidiagnosa_id AS pasienmorbiditas_id,
        CASE
            WHEN koreksidiagnosa_t.pasienadmisi_id IS NULL THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN koreksidiagnosa_t.pasienadmisi_id IS NULL THEN ruangan_rjrd.ruangan_nama
            ELSE ruangan_ri.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN koreksidiagnosa_t.pasienadmisi_id IS NULL THEN ruangan_rjrd.instalasi_id
            ELSE ruangan_ri.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN koreksidiagnosa_t.pasienadmisi_id IS NULL THEN instalasi_rj_rd.instalasi_nama
            ELSE instalasi_ri.instalasi_nama
        END AS instalasi_nama,
    koreksidiagnosa_t.is_deleted,
    klasifikasidiagnosa_m.klasifikasidiagnosa_id,
    klasifikasidiagnosa_m.klasifikasidiagnosa_nama
   FROM koreksidiagnosa_t
     JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
     JOIN pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ruangan_m ruangan_rjrd ON pendaftaran_t.ruangan_id = ruangan_rjrd.ruangan_id
     LEFT JOIN instalasi_m instalasi_rj_rd ON ruangan_rjrd.instalasi_id = instalasi_rj_rd.instalasi_id
     LEFT JOIN ruangan_m ruangan_ri ON pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id
     LEFT JOIN instalasi_m instalasi_ri ON ruangan_ri.instalasi_id = instalasi_ri.instalasi_id
     JOIN klasifikasidiagnosa_m ON diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id
  WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 AND koreksidiagnosa_t.is_deleted = false;
        ');

        $this->execute('
    ALTER TABLE laporan10besarpenyakit_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190502_100857_laporan10besarpenyakit_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190502_100857_laporan10besarpenyakit_v_update cannot be reverted.\n";

        return false;
    }
    */
}
