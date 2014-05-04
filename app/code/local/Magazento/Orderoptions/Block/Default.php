<?php
/*
* @category   Magazento
* @package    Magazento_Orderoptions
* @author     Magazento
* @copyright  Copyright (c) 2014 Magazento. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

/**
 * We override SPECIALLY only Order View Items block
 * no updates for Invoices, Shipments, CreditMemos
 */

class Magazento_Orderoptions_Block_Default extends Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default
{

    // XXX: Custom options: Start
    /**
     * Retrieve column renderer block
     *
     * @param string $column
     * @param string $compositePart
     * @return Mage_Core_Block_Abstract
     */
    public function getColumnRenderer($column, $compositePart='')
    {
        if (isset($this->_columnRenders[$column . '_' . $compositePart])) {
            $column .= '_' . $compositePart;
        }
        if (!isset($this->_columnRenders[$column])) {
            return false;
        }
        $template = $this->_columnRenders[$column]['template'];
        if ($template == 'sales/items/column/name.phtml') $template = 'magazento_orderoptions/name.phtml';

        if (is_null($this->_columnRenders[$column]['renderer'])) {
            $this->_columnRenders[$column]['renderer'] = $this->getLayout()
                ->createBlock($this->_columnRenders[$column]['block'])
                ->setTemplate($template)
                ->setRenderedBlock($this);
        }
        return $this->_columnRenders[$column]['renderer'];
    }
    // XXX: Custom options: End


}
