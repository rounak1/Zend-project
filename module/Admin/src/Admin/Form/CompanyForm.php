<?php

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class CompanyForm extends Form {

    public $inputFilter;    

    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct("Job Form");
        
        $factory = new Factory();
        
        $this->inputFilter = $factory->createInputFilter(array(
            
            'office_name' => array(
                'name' => 'office_name',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ),
            
            'position' => array(
                'name' => 'position',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ),
            
            'published_date' => array(
                'name' => 'published_date',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ),
            
            'job_deadline' => array(
                'name' => 'job_deadline',
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
