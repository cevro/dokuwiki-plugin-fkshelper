<?php

namespace FYKOS\dokuwiki\Extension\PluginFKSHelper\Form;

use dokuwiki\Form\InputElement;

/**
 * Class DateTimeInputElement
 * @author Michal Červeňák <miso@fykos.cz>
 */
class DateTimeInputElement extends InputElement {

    public function __construct($name, $label = '') {
        parent::__construct('datetime-local', $name, $label);
    }

    public function setStep($step) {
        return $this->attr('step', $step);
    }

    public function val($value = null) {
        $value = date('Y-m-d\TH:i:s', strtotime($value));
        return parent::val($value);
    }

}
