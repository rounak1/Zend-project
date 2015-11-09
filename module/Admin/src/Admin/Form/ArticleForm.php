<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory;

class ArticleForm extends Form{
    public $inputFilter;

    function __construct($name=null){
        parent::__construct("Article Form");

        $factory=new Factory();
        $this->inputFilter=$factory->createInputFilter(array(
            'title'=>array(
                'name'=>'title',
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
            'description'=>array(
                'name'=>'description',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'StripTags'),
                    array('name'=>'StringTrim'),
                ),
                'validators'=>array(array('name'=>'not_empty'))
            ),
            'status'=>array(
                'name'=>'status',
                'required'=>true,
                'filters'=>array(array('name'=>'Int')),
                'validators'=>array(array('name'=>'not_empty'))
            )
        ));

        return $this;
    }
}