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
class IV_NewProduct_Helper_Data extends Mage_Core_Helper_Abstract
{
    CONST NEW_FROM_PRODUCT_ATTRIBUTE_CODE = 'news_from_date';
    CONST NEW_TO_PRODUCT_ATTRIBUTE_CODE   = 'news_to_date';
    CONST XML_PATH_ASSIGN_TO_CATEGORY     = 'catalog/frontend/new_product_category';

    /**
     * Return ID of magento category where new product should be assigned.
     *
     * @return integer
     */
    public function getNewProductCategory()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_ASSIGN_TO_CATEGORY);
    }

    /**
     * Check is product new accourding to new from and to date.
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function isNew(Mage_Catalog_Model_Product $product)
    {
        $isNew = false;
        /** @var Mage_Core_Model_Locale $locale */
        $locale = Mage::app()->getLocale();
        /** @var Zend_Date $date */
        $localeDate = $locale->date();
        $newFromDate = $product->getData(self::NEW_FROM_PRODUCT_ATTRIBUTE_CODE);
        $newToDate = $product->getData(self::NEW_TO_PRODUCT_ATTRIBUTE_CODE);
        if (!empty($newFromDate) || !empty($newToDate)) {
            $todayStartOfDayDate = $localeDate->setTime('00:00:00')->getTimestamp();
            $todayEndOfDayDate = $localeDate->setTime('23:59:59')->getTimestamp();
            $isNew = empty($product->getData(self::NEW_FROM_PRODUCT_ATTRIBUTE_CODE))
                || $locale->date($newFromDate)->getTimestamp() <= $todayEndOfDayDate;
            $isNew = $isNew
                && (empty($product->getData(self::NEW_TO_PRODUCT_ATTRIBUTE_CODE))
                    || $locale->date($newToDate)->getTimestamp()) >= $todayStartOfDayDate;
        }

        return $isNew;
    }
}