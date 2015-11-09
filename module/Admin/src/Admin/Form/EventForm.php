<?php

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EventForm extends Form {

    public $inputFilter;    

    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct("Event Form");
        
        $factory = new Factory();
        
        $this->inputFilter = $factory->createInputFilter(array(
            
            'event_title'=>array('name'=>'event_title','required'=>true,'validators'=>array(array('name'=>'not_empty'))),
            'event_logo'=>array('name'=>'event_logo','required'=>true,'validators'=>array(array('name'=>'not_empty'))),
            'program_type'=>array('name'=>'program_type','required'=>true,'validators'=>array(array('name'=>'not_empty'))),
            'venue'=>array('name'=>'venue','required'=>true,'validators'=>array(array('name'=>'not_empty'))),
            'event_date'=>array('name'=>'event_date','required'=>true,'validators'=>array(array('name'=>'not_empty'))),
            'event_expire_date'=>array('name'=>'event_expire_date','required'=>true,'validators'=>array(array('name'=>'not_empty'))),
            'event_conduct_by'=>array('name'=>'event_conduct_by','required'=>true,'validators'=>array(array('name'=>'not_empty')))
        ));

        return $this;
    }

}
