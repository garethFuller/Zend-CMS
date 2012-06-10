<?php

/*
 * This is where users can assign content to pages based on the content types allowed
 * within the template, add pages, remove pages and edit pages
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class IndexController extends Cms_Controllers_Default
{

    public function init()
    {
        parent::init();
        
    }
    
    
   /*
     * This is the action for showing all of the pages in the system
     */
    public function indexAction()
    {
        $this->view->pageTitle = 'Jiggly CMS';
        
        // Get all of the pages from the system
        $pages = $this->getFromApi('/pages');
        
        if ($pages === null){
            $this->_helper->flashMessenger->addMessage('No pages set up yet');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            return;
        }

            
        // Get all content types from the API to reduce the amount of calls
        $contentTypes = $this->getFromApi('contenttypes');

        // Get the page structure from the API and reshuffle the pages
        $structure = $this->getFromApi('/structure');
        $structure = unserialize($structure->structure);

        // Break them down into pages
        $structurePages = explode(':', $structure);

        // Remove the last one
        array_pop($structurePages);


        // Should not rly do html in the view, i know i know but there is enough logic to justify it ...
        $finalString = '<ul id="pages">';

        $i = 0;

        foreach($structurePages as $structurePage){

            $partsCurrent = explode('-', $structurePage);
            $currentLevel = $partsCurrent[0];
            $pageId = $partsCurrent[1];

            $nextLevel = '';

            if (isset($structurePages[$i +1])){
                $partsNext = explode('-', $structurePages[$i +1]);
                $nextLevel = $partsNext[0];
            }

            $element = $this->_generatePageElement($pages, $pageId, $contentTypes);

            if (is_string($element)){

                // Add element
                $finalString .= '<li class="page-item">';
                $finalString .= $element;

                // Work out if sub pages next
                if ($nextLevel != ''){
                    if ($nextLevel > $currentLevel){
                        $finalString .= '<ul class="sortable">';
                    }elseif($nextLevel == $currentLevel){
                        $finalString .= '</li>';
                    }else{
                        $finalString .= '</ul></li>';
                    }
                }

            }


            $i ++;

        } // End for each struture page

        $finalString .= '</ul>';

        $this->view->pageListString = $finalString;
        

        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }
    
    /*
     * This is where users can add pages to the system
     */
    public function addAction(){
        
        
        $this->view->pageTitle = 'Add Page';
        
        // Get all the templates form the API
        $templates = $this->getFromApi('/templates');
        
        if ($templates === null){
            $this->_helper->flashMessenger->addMessage('No templates defined in the system');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/');
            return;
        }
        
        // Get an instance of the page form
        $pageForm = new Application_Form_PageForm();
        $pageForm->setValues($templates);
        $pageForm->startForm();
        
        $pageForm->setElementDecorators($this->_formDecorators);
        
        // Send the form to the view
        $this->view->pageForm = $pageForm;
        
        // Add the template based on the form post
        if ($this->getRequest()->isPost()){
           
            // Check if the form data is valid
            if ($pageForm->isValid($_POST)) {
                
                // Run the add template function response from the api
                $addAction = $this->postToApi('/pages', 'add', $pageForm->getValues());
                
               
                // Error checking
                if ($addAction != 1){
                    
                    if ($addAction == 'Name Taken'){
                        $this->_helper->flashMessenger->addMessage('That page name is already taken, please try again');
                    }else{
                        $this->_helper->flashMessenger->addMessage('Could not add page, please try again');
                    }
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();

                }else{
                     // Set the flash message
                    $this->_helper->flashMessenger->addMessage('Page added');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/');
                    return;
                }
                
                return;
            }
        }
        
    }
    
    /*
     * This is where users can edit pages (just the name) in the system
     */
    public function editAction(){
        
        
        $this->view->pageTitle = 'Edit Page';
        
        // Get the id param (page id)
        $id = $this->getRequest()->getParam('id');
        
        // If the get param was sent and is in the correct format
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('Need page id to edit a page');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/');
            return;
        }
        
        // Get the page by the id from the API
        $page = $this->getFromApi('/pages/'.$id);
        if ($page === null){
            $this->_helper->flashMessenger->addMessage('Unable to find page from API');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/');
            return;
        }
        
        // Get all the templates form the API
        $templates = $this->getFromApi('/templates');
        
        if ($templates === null){
            $this->_helper->flashMessenger->addMessage('No templates defined in the system');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/');
            return;
        }
        
        
        // Get an instance of the page form
        $pageForm = new Application_Form_PageForm();
        $pageForm->setValues($templates);
        $pageForm->startForm();

        $pageForm->setElementDecorators($this->_formDecorators);
        

        // Add the template based on the form post
        if ($this->getRequest()->isPost()){
           
            // Check if the form data is valid
            if ($pageForm->isValid($_POST)) {
                
                // Run the edit template function response from the api
                $updateAction = $this->postToApi('/pages', 'update', $pageForm->getValues(), $page->id);

                // Error checking
                if ($updateAction != 1){
                    
                    if ($updateAction == 'Name Taken'){
                        $this->_helper->flashMessenger->addMessage('That page name is already taken, please try again');
                    }else{
                        $this->_helper->flashMessenger->addMessage('Could not edit page, please try again');
                    }
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();

                }else{
                     // Set the flash message
                    $this->_helper->flashMessenger->addMessage('Page edited');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/');
                    return;
                }
                
                return;
            }
        }

        // Populate the page form with the name of the page
        $pageForm->populate(array('name' => $page->name));
        
        // Send the form to the view
        $this->view->pageForm = $pageForm;
        
    }
    
    /*
     * This is where users can actually assign content to content slots
     */
    public function editassignmentAction(){
        
        $this->view->pageTitle = 'Edit Content Assignment';
        
        // First check to make sure we got the id correctly for the page
        $pageId = $this->getRequest()->getParam('page');
        
        // The type id is wo we know whcih item they have clicked on 0 for first 1 for second etc
        $slot = $this->getRequest()->getParam('id');
        
        if (!isset($pageId) || !is_numeric($pageId)){
            $this->_helper->flashMessenger->addMessage('Could not edit page assignment due to lack of page id');
            $this->_redirect('/');
            return;
        }
        
        if (!isset($slot) || !is_numeric($slot)){
            $this->_helper->flashMessenger->addMessage('Could not edit page assignment due to lack of id');
            $this->_redirect('/');
            return;
        }
        
        $currentPage = $this->getFromApi('/pages/'.$pageId);
        
        if ($currentPage === null){
            $this->_helper->flashMessenger->addMessage('Could not get current page from API');
            $this->_redirect('/');
            return;
        }
        
        // Work out based on the type and the page what content is availible to them
        $contentAssignment = unserialize($currentPage->content_assigned);
        $currentItem = $contentAssignment[$slot];
        $currentActive = $contentAssignment[$slot]['value'];
        $contentTypeId = $currentItem['type'];
        
        
        // Nw get all content for content type from system
        $content = $this->getFromApi('/content/type/'.$contentTypeId);

        // Get an instance of the edit assignment form
        $assignmentForm = new Application_Form_ContentAssignmentForm();
        $assignmentForm->setValues($content, $currentActive);
        $assignmentForm->startForm();
        
        // Send the content type id to the view
        $this->view->typeId = $contentTypeId;
        
       
        // Check if post
        if ($this->getRequest()->isPost()){
                
            // Check if the form data is valid
            if ($assignmentForm->isValid($_POST)) {
                
                // attempt to update content via API
                $updateAttempt = $this->postToApi('/pages', 'update-assignment',  $assignmentForm->getValues(), $currentPage->id, $slot);
                
                // check on status of update
                if ($updateAttempt != 1){
                    $this->_helper->flashMessenger->addMessage('Unable to update page assignment via the API');
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                    $this->_redirect('/');
                    return;
                }else{
                    $this->_helper->flashMessenger->addMessage('content assignment updated');
                    $this->_redirect('/');
                    return;
                }

            }       
        }
        
        // send the form to the view
        $this->view->assignmentForm = $assignmentForm;
        
    }
    
   
    /*
     * This is the view for confirming of the user wants to remove a page
     */
    public function removeConfirmAction(){
        
        $this->view->pageTitle = 'Remove Page';
        
        // Check to make sure get aram is set
        $id = $this->getRequest()->getParam('id');
        
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('You must pass a valid page id');
            $this->_redirect('/');
            return;
        }
        
        // Get the page from the api based on the id
        $page = $this->getFromApi('/pages/'.$id);
        
        if ($page === null){
            $this->_helper->flashMessenger->addMessage('Unable to find page in API');
            $this->_redirect('/');
            return;
        }
        
        if ($this->_isMobile){
            $this->_helper->layout->setLayout('dialog-mobile');
        }else{
            $this->_helper->layout->setLayout('dialog');
        }
        
        // Send the page to the view
        $this->view->page = $page;
    }
    
    
    /*
     * This is the actual process of removing a page from the system
     */
    public function removeAction(){
        
        // Get the id param passed
        $id = $this->getRequest()->getParam('id');
        
        // Sanity check the param
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('You must pass a valid id to remove a page');
            $this->_redirect('/');
            return;
        }
        
        // Attempt to remove the page from the api
        $removeAction = $this->postToApi('/pages', 'remove', $id);

        if ($removeAction == 1){
            $this->_helper->flashMessenger->addMessage('Page removed from the system');
        }else{
            $this->_helper->flashMessenger->addMessage('Could not find the page to remove');
        }
        $this->_redirect('/');
        return;
            

    }
    
    
    /*
     * This function takes the page id from the struture array and gives us the html
     * for the page element in the list
     * 
     * As a note when we get the content map the format of the dat returned is as follows 
     * gives us something like 
     * 
     * array[0] = array('type' = 7, 'value' = 0)
     *      [1] = array('type' = 7, 'value' = 0)
     *      [2] = array('type' = 3, 'value' = 0);
     * 
     * where type is the type of content and value is the content id
     * assigned to that type for this page (based on the template)
     * 
     * @param object $pages
     * @param int $pageId
     * @param object $contenTypes
     * @return string $element
     */
    public function _generatePageElement($pages, $pageId, $contentTypes){
        // First generate the page element, for this we will need the page object by this ID
        $pageFound = '';
        foreach($pages as $page){
            if ($page->id == $pageId){
                $pageFound = $page;
                break;
            }
        }

        // Should never happen
        if ($pageFound == ''){
            return false;
        }

        
        $element = '<span class="item-wrapper" id="'. $pageFound->id .'">';
        $element .=     '<div class="page-level-controlls">';
        $element .=         '<a class="indent" href="#">in</a> | ';
        $element .=         '<a class="outdent" href="#">out</a> | ';
        $element .=         '<a class="content-button-toggle" href="#" title="content assignment">C</a> | ';
        $element .=         '<a title="Remove page" href="index/remove-confirm/id/'.$pageFound->id.'">R</a> | ';
        $element .=         '<a title="Edit page" href="index/edit/id/'.$pageFound->id.'">E</a>';
        $element .=     '</div>';
        $element .=     '<h4>'.$pageFound->name.'</h4>';

        $currentMapping = unserialize($pageFound->content_assigned);

        $i = 0;
        $element .=     '<ul class="content-buttons">';

        foreach($currentMapping as $map){
            $element .=     '<li class="lock">';
            $element .=         '<span>';

            $name = '';

            // get the name of the content type
            foreach($contentTypes as $contentType){
                if ($contentType->id == $map['type']){
                    $name = $contentType->name;
                    break;
                }
            }
            if ($name != ''){
                $class = ($map['value'] != 0) ? 'class="content-button active"' : 'class="content-button"';
                $element .= '<a href="index/editassignment/page/'.$pageFound->id.'/id/'.$i.'" title="'.$name.'" '.$class.'>'.$name.'</a>';
            }
            $i ++;
            $element .=     '</span>';
            $element .= '</li>';

        }
        $element .= '</ul>';
        $element .= '</span>';
 
        return $element;
    }


}

