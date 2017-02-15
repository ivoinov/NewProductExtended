<?php

/**
 * Copyright [2017] Illia Voinov <ilya.voinov@yahoo.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */
class IV_NewProduct_Model_Crontab_NewProductsUpdater implements IV_NewProduct_Model_Crontab_Interface
{
    /**
     * Select all products, which have news_to_date before now and assign to custom category.
     *
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            /** @var IV_NewProduct_Helper_Data $helper */
            $helper = Mage::helper('newproduct');
            /** @var Mage_Catalog_Model_Category $category */
            $category = $this->_getCategoryToAssign();
            $positions = $category->getProductsPosition();
            /** @var Mage_Catalog_Model_Resource_Product_Collection $products */
            $products = $this->_getNewProducts();
            if (!empty($products)) {
                foreach ($products as $product) {
                    $positions[$product->getId()] = 0;
                }
                $category->setPostedProducts($positions);
                $category->save();
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return instance of category.
     *
     * @return Mage_Catalog_Model_Category
     * @throws Exception
     */
    protected function _getCategoryToAssign()
    {
        /** @var IV_NewProduct_Helper_Data $helper */
        $helper = Mage::helper('newproduct');
        if (empty($helper->getNewProductCategory())) {
            throw  new Exception('Category for new products has\'t been selected');
        }
        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::getModel('catalog/category')->load($helper->getNewProductCategory());
        if (!$category->getId()) {
            throw new Exception('Can not load selected category.');
        }

        return $category;
    }

    /**
     * Return new products collection which should be assigned.
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getNewProducts()
    {
        $todayStartOfDayDate = Mage::app()->getLocale()->date()->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $todayEndOfDayDate = Mage::app()->getLocale()->date()->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        /** @var Mage_Catalog_Model_Resource_Product_Collection $products */
        $products = Mage::getResourceModel('catalog/product_collection');
        $products->addAttributeToSelect(array(
            IV_NewProduct_Helper_Data::NEW_FROM_PRODUCT_ATTRIBUTE_CODE,
            IV_NewProduct_Helper_Data::NEW_TO_PRODUCT_ATTRIBUTE_CODE,
        ));
        $products->addAttributeToFilter(IV_NewProduct_Helper_Data::NEW_FROM_PRODUCT_ATTRIBUTE_CODE, array(
            'or' => array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')),
            ),
        ), 'left')->addAttributeToFilter(IV_NewProduct_Helper_Data::NEW_TO_PRODUCT_ATTRIBUTE_CODE, array(
            'or' => array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')),
            ),
        ), 'left')->addAttributeToFilter(array(
            array(
                'attribute' => IV_NewProduct_Helper_Data::NEW_FROM_PRODUCT_ATTRIBUTE_CODE,
                'is'        => new Zend_Db_Expr('not null'),
            ),
            array(
                'attribute' => IV_NewProduct_Helper_Data::NEW_TO_PRODUCT_ATTRIBUTE_CODE,
                'is'        => new Zend_Db_Expr('not null'),
            ),
        ));

        return $products;
    }
}