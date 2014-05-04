<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Magazento
* @copyright  Copyright (c) 2014 Magazento. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderoptions_Model_Item extends Mage_Core_Model_Abstract
{

    /**
     * Update options for selected item
     * We load all product options for item, then update single option from POST
     * If variable does not set, option remains the same
     */
    public function update($data)
    {

        $itemId     = $data['itemid'];
        $itemModel  = Mage::getModel('sales/order_item')->load($itemId);
        $options    = $itemModel->getProductOptions();
        foreach ($options['options'] as $k => $v) {
            $_key = 'id_'.$v['option_id'];

            if (($v['option_type'] == 'file') && (isset($_FILES[$_key]))) {

                $fileData = unserialize($v['option_value']);


                $ext        = pathinfo($_FILES[$_key]['name'])['extension'];
                $md5name    = md5($_FILES[$_key]['name']).'.'.$ext;
                $quotePath  = dirname($fileData['quote_path']).DS.$md5name;
                $orderPath  = dirname($fileData['quote_path']).DS.$md5name;
                $fullPath   = dirname($fileData['fullpath']).DS.$md5name;
                $optionsId  = $fileData['url']['params']['id'];

                $_width = 0;
                $_height = 0;
                if (is_readable($_FILES[$_key]['tmp_name'])) {
                    $_imageSize = getimagesize($_FILES[$_key]['tmp_name']);
                    if ($_imageSize) {
                        $_width = $_imageSize[0];
                        $_height = $_imageSize[1];
                    }
                }

                // Set values
                $fileData['title']      = $_FILES[$_key]['name'];
                $fileData['type']       = $_FILES[$_key]['type'];
                $fileData['size']       = $_FILES[$_key]['size'];
                $fileData['quote_path'] = $quotePath;
                $fileData['order_path'] = $orderPath;
                $fileData['fullpath']   = $fullPath;
                $fileData['width']      = $_width;
                $fileData['height']     = $_height;
                $downloadCustomOption   = Mage::getUrl('sales/download/downloadCustomOption',
                                                array(
                                                    'id'  => $optionsId,
                                                    'key' => $fileData['secret_key']
                                                )
                                            );
                $href = '<a href="'.$downloadCustomOption.'" target="_blank">'.$fileData['title'].'</a>';
                move_uploaded_file($_FILES[$_key]['tmp_name'],$fullPath);

                $options['options'][$k]['value']        = $href;
                $options['options'][$k]['print_value']  = $fileData['title'];
                $options['options'][$k]['option_value'] = serialize($fileData);

                // do not forget to update quote options
                $option = Mage::getModel('sales/quote_item_option')->load($optionsId);
                $option->setValue(serialize($fileData));
                $option->save();

            }

            // All other normal field types
            if (isset($data[$_key])) {
                $_newvalue = $data[$_key];
                if (is_array($_newvalue)) $_newvalue = implode(', ',$_newvalue);
                $options['options'][$k]['value']        = $_newvalue;
                $options['options'][$k]['print_value']  = $_newvalue;
                $options['options'][$k]['option_value'] = $_newvalue;
            }

        }

        $itemModel->setProductOptions($options);
        $itemModel->save();

    }



}
