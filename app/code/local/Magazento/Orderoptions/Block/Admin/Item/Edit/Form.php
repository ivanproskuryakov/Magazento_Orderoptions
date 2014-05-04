<?php
/*
* @category   Magazento
* @package    Magazento_Orderoptions
* @author     Magazento
* @copyright  Copyright (c) 2014 Magazento. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderoptions_Block_Admin_Item_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Prepare form data for selected item
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $itemId     = $this->getRequest()->getParam('itemid');
        $itemModel  = Mage::getModel('sales/order_item')->load($itemId);
        $options    = $itemModel->getProductOptions()['options'];
        $product    = Mage::getModel('catalog/product')->load($itemModel->getProductId());
        $curency    = Mage::app()->getStore()->getCurrentCurrencyCode();

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('item_additional', array(
            'legend' => Mage::helper('core')->__('Available Options')
        ));

        $fieldset->addField('itemid', 'hidden', array(
            'name' => 'itemid',
            'required' => true,
            'value' => $itemId,
        ));

        foreach ($options as $option) {
            $_optionId = 'id_'.$option['option_id'];
            $_type = $option['option_type'];
            $_values = array();

            foreach ($product->getOptions() as $o) {

                if ($o->getId() == $option['option_id']) {

                    foreach ($o->getValues() as $v) {
                        if ($this->isPriceEquals($o->getValues(), $option['value'] , $v)) {
                            $_values[] = array('value'=>$v->getData('title'),
                                'label'=> $v->getData('title') .' / '. round( $v->getData('price')). ' '. $curency);
                        }
                    }
                }
            }

            switch ($_type) {
                case 'checkbox': {
                    $v = explode(', ',$option['value']);
                    $v = array_combine($v,$v);
//                    var_dump($option['value']);
//                    var_dump($v);

                    $fieldset->addField($_optionId, 'checkboxes', array(
                        'name' => $_optionId.'[]',
                        'label' => $option['label'],
                        'required' => false,
                        'value' =>  $v,
                        'values' => $_values,
                    ));
                    break;
                }
                case 'radio': {
                    $fieldset->addField($_optionId, 'radios', array(
                        'name' => $_optionId,
                        'label' => $option['label'],
                        'required' => false,
                        'value' =>  $option['value'],
                        'values' => $_values,
                    ));
                    break;
                }
                case 'drop_down': {

                    $fieldset->addField($_optionId, 'select', array(
                        'name' => $_optionId,
                        'label' => $option['label'],
                        'required' => false,
                        'value' => $option['option_value'],
                        'values' => $_values,
                    ));
                    break;
                }
                case 'multiple': {

                    $v = explode(', ',$option['value']);
                    $v = array_combine($v,$v);
                    $fieldset->addField($_optionId, 'multiselect', array(
                        'name' => $_optionId,
                        'label' => $option['label'],
                        'required' => false,
                        'value' => $v,
                        'values' => $_values,
                    ));
                    break;
                }
                case 'field': {
                    $fieldset->addField($_optionId, 'text', array(
                        'name' => $_optionId,
                        'label' => $option['label'],
                        'required' => false,
                        'value' => $option['option_value'],
                    ));
                    break;
                }
                case 'area': {
                    $fieldset->addField($_optionId, 'textarea', array(
                        'name' => $_optionId,
                        'label' => $option['label'],
                        'required' => false,
                        'value' => $option['option_value'],
                    ));
                    break;
                }
                case 'date': {
                    $fieldset->addField($_optionId, 'date', array(
                        'name' => $_optionId,
                        'label' => $option['label'],
                        'required' => false,
                        'image' => $this->getSkinUrl('images/grid-cal.gif'),
                        'value' => $option['option_value'],
                        'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                    ));
                    break;
                }
                case 'date_time': {
                    $fieldset->addField($_optionId, 'date', array(
                        'name' => $_optionId,
                        'label' => $option['label'],
                        'required' => false,
                        'image' => $this->getSkinUrl('images/grid-cal.gif'),
                        'value' => $option['option_value'],
                        'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                    ));
                    break;
                }
                case 'file': {
                    $file = unserialize($option['option_value']);
//                    var_dump($file);
//                    var_dump($file['order_path']);

                    $fieldset->addField($_optionId, 'file', array(
                        'name' => $_optionId,
                        'label' => $option['label'],
                        'required' => false,
                        'value' => $file['order_path'],
                        'after_element_html' => $option['value'],
                    ));
                    break;
                }
                default: break; ;
            }

        }

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    private function isPriceEquals($options, $selected, $comparer) {
        $v = explode(', ',$selected);
        $v = array_combine($v,$v);

        foreach ($options as $o) {
            if (isset($v[$o['title']])) {
                $v[$o['title']] = $o->getPrice();
            }
        }
//        var_dump(round($v[$comparer->getTitle()]) .' '.round($comparer->getPrice()));
        if (round($comparer->getPrice()) == round($v[$comparer->getTitle()])) return true;

        return false;
    }
}