<?php

/*
 * This is the user controller for the API here we handle any requests
 * that are user orientated in the API
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class UserController extends Api_Default
{
    
    protected $_userModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
        
        // As we connect to the user model many times inthis controller we will create a global instance
        $this->_userModel = new Application_Model_User();
        $this->_helper->viewRenderer->setNoRender(true); 
    }



    public function indexAction()
    {
        if ($this->_isAdmin){
            $data = $this->_userModel->getAllUsers();
            
            $this->returnData($data);
            
        }else{
            $this->getResponse()
            ->setHttpResponseCode(403)
            ->appendBody("You do not have access to this data");
        }

    }

    public function getAction()
    {

        // If they have an admin api key
        if ($this->_isAdmin){
            
            // Try Getting the User By Id
            if ($this->getRequest()->getParam('id')){
                $data = $this->_userModel->getUserById($this->getRequest()->getParam('id'));
            }
            
            // Get user by username
            if ($this->getRequest()->getParam('name')){
                $data = $this->_userModel->getUserByUsername($this->getRequest()->getParam('name')); 
            }
            
            // Get user by email address
            if ($this->getRequest()->getParam('email')){
                $data = $this->_userModel->getByEmailAddress($this->getRequest()->getParam('email')); 
            }
            
            
            $this->returnData($data);
            
        }else{
            
            $this->getResponse()
            //->setHttpResponseCode(403)
            ->appendBody("You do not have access to this data");
        }
        
        

    }
    
    public function postAction()
    {
        // If they have an admin api key
        if ($this->_isAdmin){
            
            // Work out the type of action they want to perform
            switch($_POST['operation']){
                case 'update':
                    
                    // Run the update user commmand 
                    $data = $this->_userModel->updateUser(unserialize($_POST['data']), $_POST['argOne']);
                    
                    $this->getResponse()
                        ->setHttpResponseCode(200)
                        ->appendBody($data);
                    break;
                default:
                    break;
            }
            

            //$this->returnData($data);
            
        }else{
            $this->getResponse()
            ->setHttpResponseCode(403)
            ->appendBody("You do not have access to this data");
        }

    }
    
    public function putAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody("From putAction() updating the requested article");

    }
    
    public function deleteAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody("From deleteAction() deleting the requested article");

    }
    
    

}