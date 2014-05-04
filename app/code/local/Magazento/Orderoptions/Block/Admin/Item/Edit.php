<?php
/*
* @category   Magazento
* @package    Magazento_Orderoptions
* @author     Magazento
* @copyright  Copyright (c) 2014 Magazento. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderoptions_Block_Admin_Item_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'item_id';
        $this->_controller = 'admin_item';
        $this->_blockGroup = 'orderoptions';

        parent::__construct();
        $this->_removeButton('back');
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        $this->_updateButton('save', 'label', Mage::helper('orderoptions')->__('Save'));
    }

    public function getHeaderText()
    {
        $itemId = $this->getRequest()->getParam('itemid');
        $itemModel = Mage::getModel('sales/order_item')->load($itemId);
        return Mage::helper('orderoptions')->__('Ordered qty: '.round($itemModel->getQtyOrdered()));
    }

}
