<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * Powered by Sirs
 */

namespace Integrasi\Classes;

class ExcelColumn {
    public $name;
    public $type;
    public $label;

    function __construct($name, $type, $label) {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;

        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type
        ];
    }
}
