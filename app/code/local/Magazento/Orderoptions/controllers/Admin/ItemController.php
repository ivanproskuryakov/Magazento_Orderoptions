<?php
/*
* @category   Magazento
* @package    Magazento_Orderoptions
* @author     Magazento
* @copyright  Copyright (c) 2014 Magazento. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderoptions_Admin_ItemController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Grabs data from POST and updates selected item
     * Throws an error, if POST empty
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            Mage::getModel('orderoptions/item')->update($data);
            $this->loadLayout()->renderLayout();
        } else {
            echo $this->__('Something went wrong, probably empty data has been sent');
        }
    }

    /**
     * Displays available options for selected item
     */
    public function contentAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()
             ->createBlock('orderoptions/admin_item_edit'))
             ->renderLayout();
    }

    /**
     * For AJAX update when Modal window closed
     * Returns blank page with HTML for selected item
     */
    public function ajaxcontentAction()
    {
        $this->loadLayout()->renderLayout();
    }

}