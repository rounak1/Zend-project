<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class ContactUsForm extends Form{
    public $inputFilter;    

    public function __construct($name=null){
        parent::__construct("Contact Us Form");

        $factory=new Factory();

        $this->inputFilter=$factory->createInputFilter(array(
            'name'=>array(
                'name'=>'name',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'StripTags'),
                    array('name'=>'StringTrim'),
                ),
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'encoding'=>'UTF-8',
                            'min'=>1,
                            'max'=>255
                        )
                    ),
                    array('name'=>'not_empty')
                )
            ),
            'email'=>array(
                'name'=>'email',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'StripTags'),
                    array('name'=>'StringTrim'),
                ),
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'encoding'=>'UTF-8',
                            'min'=>1,
                            'max'=>255
                        )
                    ),
                    array('name'=>'not_empty')
                )
            ),
            'message'=>array(
                'name'=>'message',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'StripTags'),
                    array('name'=>'StringTrim'),
                ),
                'validators'=>array(array('name'=>'not_empty'))
            ),
            'issue'=>array(
                'name'=>'issue',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'StripTags'),
                    array('name'=>'StringTrim'),
                ),
                'validators'=>array(array('name'=>'not_empty'))
            )
        ));

        return $this;
    }
}