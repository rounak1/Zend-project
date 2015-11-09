<?php

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class NewspaperJobForm extends Form {
    
     public $inputFilter; 
     
     public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct("Newspaper Job Form");
        
        $factory = new Factory();
        
        $this->inputFilter = $factory->createInputFilter(array(
            
                'job_company_name' => array(
                    'name' => 'job_company_name',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'not_empty',
                        ),
                    ),
                )
            
            ));

        return $this;
     }
    
}
