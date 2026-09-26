<?php

use yii\db\Migration;

/**
 * Class m220624_122341_migrate_cssd_infokonfirmasiselesai_v
 */
class m220624_122341_migrate_cssd_infokonfirmasiselesai_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infokonfirmasiselesai_v;');

        $this->execute("CREATE VIEW \"public\".\"infokonfirmasiselesai_v\" AS
           SELECT konfirmasiselesai.type,
           konfirmasiselesai.barangalkes_id,
           konfirmasiselesai.satuanunit_id,
           konfirmasiselesai.cssdsterilisasi_id,
           konfirmasiselesai.no_sterilisasi,
           konfirmasiselesai.barangalkes_nama,
           konfirmasiselesai.barangalkes_kode,
           konfirmasiselesai.satuanunit_nama,
           sum(konfirmasiselesai.qty) AS qty
           FROM ( SELECT 'BARANG'::text AS type,
           cssd_t.cssd_id,
           cssddet_t.barangalkes_id,
           cssddet_t.satuanunit_id,
           cssdsterilisasi_t.cssdsterilisasi_id,
           cssd_t.no_pengajuan_sterilisasi,
           cssdsterilisasi_t.no_sterilisasi,
           barang_m.barangalkes_nama,
           barang_m.barangalkes_kode,
           satuanunit_m.satuanunit_nama,
           cssddet_t.qty
           FROM ((((cssd_t
           JOIN ( SELECT a.cssd_id,
           a.is_alkes,
           a.barangalkes_id,
           a.satuanunit_id,
           sum(a.qty) AS qty
           FROM cssddet_t a
           GROUP BY a.cssd_id, a.is_alkes, a.barangalkes_id, a.satuanunit_id) cssddet_t ON (((cssd_t.cssd_id = cssddet_t.cssd_id) AND (cssddet_t.is_alkes = false))))
           JOIN ( SELECT a.barang_id AS barangalkes_id,
           a.barang_nama AS barangalkes_nama,
           a.barang_kode AS barangalkes_kode
           FROM barang_m a) barang_m ON ((cssddet_t.barangalkes_id = barang_m.barangalkes_id)))
           JOIN ( SELECT a.satuanunit_id,
           a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON ((cssddet_t.satuanunit_id = satuanunit_m.satuanunit_id)))
           JOIN ( SELECT a.cssdsterilisasi_id,
           a.no_sterilisasi
           FROM cssdsterilisasi_t a) cssdsterilisasi_t ON ((cssd_t.cssdsterilisasi_id = cssdsterilisasi_t.cssdsterilisasi_id)))
           UNION ALL
           SELECT 'ALKES'::text AS type,
           cssd_t.cssd_id,
           cssddet_t.barangalkes_id,
           cssddet_t.satuanunit_id,
           cssdsterilisasi_t.cssdsterilisasi_id,
           cssd_t.no_pengajuan_sterilisasi,
           cssdsterilisasi_t.no_sterilisasi,
           obatalkes_m.barangalkes_nama,
           obatalkes_m.barangalkes_kode,
           satuanunit_m.satuanunit_nama,
           cssddet_t.qty
           FROM ((((cssd_t
           JOIN ( SELECT a.cssd_id,
           a.is_alkes,
           a.barangalkes_id,
           a.satuanunit_id,
           sum(a.qty) AS qty
           FROM cssddet_t a
           GROUP BY a.cssd_id, a.is_alkes, a.barangalkes_id, a.satuanunit_id) cssddet_t ON (((cssd_t.cssd_id = cssddet_t.cssd_id) AND (cssddet_t.is_alkes = true))))
           JOIN ( SELECT a.obatalkes_id AS barangalkes_id,
           a.obatalkes_nama AS barangalkes_nama,
           a.obatalkes_kode AS barangalkes_kode
           FROM obatalkes_m a) obatalkes_m ON ((cssddet_t.barangalkes_id = obatalkes_m.barangalkes_id)))
           JOIN ( SELECT a.satuanunit_id,
           a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON ((cssddet_t.satuanunit_id = satuanunit_m.satuanunit_id)))
           JOIN ( SELECT a.cssdsterilisasi_id,
           a.no_sterilisasi
           FROM cssdsterilisasi_t a) cssdsterilisasi_t ON ((cssd_t.cssdsterilisasi_id = cssdsterilisasi_t.cssdsterilisasi_id)))) konfirmasiselesai
           GROUP BY konfirmasiselesai.type, konfirmasiselesai.barangalkes_id, konfirmasiselesai.satuanunit_id, konfirmasiselesai.cssdsterilisasi_id, konfirmasiselesai.no_sterilisasi, konfirmasiselesai.barangalkes_nama, konfirmasiselesai.barangalkes_kode, konfirmasiselesai.satuanunit_nama
        ");
        
        $this->execute('ALTER TABLE public.infokonfirmasiselesai_v OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_122341_migrate_cssd_infokonfirmasiselesai_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_122341_migrate_cssd_infokonfirmasiselesai_v cannot be reverted.\n";

        return false;
    }
    */
}
