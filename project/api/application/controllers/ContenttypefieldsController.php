<?php

/*
 * This is the content type fields controller, only admin users have access to the
 * content type fields content
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class ContenttypefieldsController extends Api_Default
{
    
    protected $_contentTypeFieldsModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
       
        $this->_contentTypeFieldsModel = new Application_Model_ContentTypeFields();
        $this->_helper->viewRenderer->setNoRender(true); 
    }



    public function indexAction()
    {
        if ($this->_isAdmin){
            $data = $this->_contentTypeFieldsModel->getAllContentTypeFields();
            
            $this->returnData($data);
            
        }else{
            $this->returnNoAuth();
        }

    }

    public function getAction()
    {
        
        // If they have an admin api key
        if ($this->_isAdmin){
            
            $data = 'Operation not found';
            
            // Try Getting the Content Type By Id
            if ($this->getRequest()->getParam('id')){
                $data = $this->_contentTypeFieldsModel->getContentTypeFieldById($this->getRequest()->getParam('id'));
            }
            
            // Get content type by name
            if ($this->getRequest()->getParam('name')){
                $data = $this->_contentTypeFieldsModel->getContentFieldByName($this->getRequest()->getParam('name')); 
            }
            
            // Get the content fields for the content type parsed
            if ($this->getRequest()->getParam('contenttype')){
                $data = $this->_contentTypeFieldsModel->getContentFieldsForContentType($this->getRequest()->getParam('contenttype')); 
            }


            $this->returnData($data);
            
        }else{
            
            $this->returnNoAuth();
        }
        
    }
    
    public function postAction()
    {
        
        // If they have an admin api key
        if ($this->_isAdmin){
            
            // Work out the type of action they want to perform
            switch($_POST['operation']){
                case 'update':
                    $data = $this->_contentTypeFieldsModel->updateContentTypeField(unserialize(base64_decode($_POST['data'])), $_POST['argOne']);
                    break;
                case 'add':
                    $data = $this->_contentTypeFieldsModel->addContentTypeField(unserialize(base64_decode($_POST['data'])));
                    break;
                case 'remove':
                    $data = $this->_contentTypeFieldsModel->removeContentTypeField(unserialize(base64_decode($_POST['data'])));
                    break;
                default:
                    $data = 'Operation not found';
                    break;
            }
            
            $this->returnPostResult($data);
            
        }else{
            $this->returnNoAuth();
        }

    }
    
    public function putAction()
    {
      
    }
    
    public function deleteAction()
    {

    }
    
    

}