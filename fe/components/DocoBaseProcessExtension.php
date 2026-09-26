<?php

/**
 * @author : Setyabudi Dwisandi Arifin
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components;

abstract class DocoBaseProcessExtension implements IProcessExtension
{
    use DocoYiiInitialize;

    protected $_requestData = null;

    public function execute($controller)
    {
        $this->initialize();
        $response =  $this->processFlow($controller);
        return $response;
    }

    /**
     * @return array|null output nya array atau null, kalau null nanti proses di json response nya hanya sukses biasa.
     */
    abstract protected function processFlow($controller);
}
