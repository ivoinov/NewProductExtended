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

/**
 * Class IV_NewProduct_Model_System_Config_Source_NewProductCategories
 */
class IV_NewProduct_Model_System_Config_Source_NewProductCategories
{
    /**
     * @var array
     */
    protected $_categories = array();

    public function toOptionArray()
    {
        if (empty($this->_categories)) {
            $options = array();
            $options[] = array(
                'value' => '',
                'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
            );
            /** @var Mage_Catalog_Model_Resource_Category_Collection $categories */
            $categories = Mage::getResourceModel('catalog/category_collection');
            $categories->addAttributeToSelect('name');
            foreach ($categories as $category) {
                /** @var Mage_Catalog_Model_Category $category */
                $options[] = array('value' => $category->getId(), 'label' => $category->getName());
            }
            $this->_categories = $options;
        }

        return $this->_categories;
    }
}