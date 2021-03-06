<?php
namespace MyBlog\Form;
use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
class BlogPostForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('blogpost');
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new \MyBlog\Form\BlogPostInputFilter());
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'created',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Заголовок',
            ),
            'options' => array(
                'min' => 3,
                'max' => 25
            ),
        ));
        $this->add(array(
            'name' => 'text',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Текст',
            ),
        ));
        $this->add(array(
            'name' => 'state',
            'type' => 'Checkbox',
			'options' => array(
				'label' => 'Опубликовать'
			)
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ),
        ));
    }
}