<?php

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class SettingsForm extends Form {

    public $inputFilter;

    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct("Seeker Form");
        
        $factory = new Factory();
        
        $this->inputFilter = $factory->createInputFilter(array(
            'user_name' => array(
                'name' => 'user_name',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ),
            'old_password' => array(
                'name' => 'old_password',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ),
            'new_password' => array(
                'name' => 'new_password',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                    array(
                        'name' => 'string_length',
                        'options' => array(
                            'min' => 5
                        ),
                    ),
                ),
            ),
            'con_password' => array(
                'name' => 'con_password',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'new_password',
                        ),
                    ),
                ),
            ),
        ));

        return $this;
    }

}
