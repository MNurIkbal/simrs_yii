<?php
/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */
namespace app\modules\master\components\traits;

trait JenisPemeriksaanFisioterapiTrait
{
    public $jenisPemeriksaanFisioterapiRoutes = [
        'save-jenis-pemeriksaan-fisio' => 'Doco\master\actions\JenisPemeriksaanFisioterapi\SaveAction',
        'save-kelompok-pemeriksaan-fisio' => 'Doco\master\actions\JenisPemeriksaanFisioterapi\SaveKelompokAction',
        'save-tindakan-fisio' => 'Doco\master\actions\JenisPemeriksaanFisioterapi\SaveTindakanAction',
        'update-tindakan-fisio' => 'Doco\master\actions\JenisPemeriksaanFisioterapi\UpdateTindakanAction',
    ];
}
