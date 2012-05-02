<?php

/*
 * This form is used for editing system settings in the CMS
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */
class Application_Form_Settings extends Zend_Form
{
    
    public function init()
    {
        $customDecorators = array(
                                'ViewHelper',
                                'Description',
                                'Errors',
                                array(array('Input' => 'HtmlTag'), array('tag' => 'dd')),
                                array('Label', array('tag' => 'dt')),
                                array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'item-wrapper')));
       
        $this->setAttrib('class', 'user');
        $this->setAttrib('id', 'settingsForm');
        
        // Theme input field
        $theme = new Zend_Form_Element_Text('theme_path');
        $theme->addFilter('StringTrim')  
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setRequired(true)
                ->addErrorMessage('A theme is required')
                ->setDecorators($customDecorators)
                ->setLabel('Theme Folder Name:');

        // Submit input field
        $submit = new Zend_Form_Element_Submit('Save');
        $submit->setValue('Save')
                ->setAttrib('data-theme', 'a');

        $this->addElements(array($theme, $submit));
  
      
    }
    
    
}

