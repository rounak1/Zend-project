<?php

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class CmsForm extends Form {

    public $inputFilter;

    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct("Seeker Form");
        
        $factory = new Factory();
        
        $this->inputFilter = $factory->createInputFilter(array(
            'title' => array(
                'name' => 'user_name',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ),
            'alias' => array(
                'name' => 'old_password',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ),
            'content' => array(
                'name' => 'old_password',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ),
            
        ));

        return $this;
    }

}
