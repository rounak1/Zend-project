<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class FeedbackForm extends Form{
    public $inputFilter;    

    public function __construct($name=null){
        parent::__construct("Feedback Form");

        $factory=new Factory();

        $this->inputFilter=$factory->createInputFilter(array(
            'type'=>array('name'=>'type','required'=>true,'validators'=>array(array('name'=>'not_empty'))),
            'email'=>array('name'=>'email','required'=>true,'validators'=>array(array('name'=>'not_empty'))),
            'subject'=>array('name'=>'subject','required'=>true,'validators'=>array(array('name'=>'not_empty'))),
            'message'=>array('name'=>'message','required'=>true,'validators'=>array(array('name'=>'not_empty'))),
            'captcha'=>array('name'=>'captcha','required'=>true,'validators'=>array(array('name'=>'not_empty')))
        ));

        return $this;
    }
}