<?php

/*
 * This form is used for both adding and editing api users within the system
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */
class Application_Form_ApiForm extends Zend_Form
{
    
    public function init()
    {
       
        $this->setAttrib('class', 'user');
        $this->setAttrib('id', 'apiForm');
        
        // Ref input field
        $ref = new Zend_Form_Element_Text('ref');
        $ref->addFilter('StringTrim')  
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setRequired(true)
                ->addErrorMessage('Ref is required')
                ->setLabel('Ref:');
        
        // Key input field
        $key = new Zend_Form_Element_Text('key');
        $key->addFilter('StringTrim')  
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setRequired(true)
                ->addErrorMessage('Key is required')
                ->setLabel('Key:');
        
        // Type input field
        $type = new Zend_Form_Element_Select('type');
        $type->setLabel('Type of access:')
                ->addMultiOption('2', 'Public')
                ->addMultiOption('1', 'Admin')
                ->setAttrib('data-native-menu', 'false')
                ->setAttrib('data-theme', 'a');
        
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('Save');
        $submit->setValue('Save')
                ->setAttrib('data-theme', 'e')
                ->setAttrib('class', 'submit');
        
        
        $this->addElements(array($ref, $key, $type, $submit));

      
    }
    
    
}

