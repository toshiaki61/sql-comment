<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright 2005-2007 the Seasar Foundation and the Others.            |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the "License");      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// |                                                                      |
// |     http://www.apache.org/licenses/LICENSE-2.0                       |
// |                                                                      |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an "AS IS" BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,                        |
// | either express or implied. See the License for the specific language |
// | governing permissions and limitations under the License.             |
// +----------------------------------------------------------------------+
// | Authors: klove                                                       |
// +----------------------------------------------------------------------+
//
// $Id: $
namespace S2Container\Beans\Impl;
/**
 * @package org.seasar.framework.beans.impl
 * @author klove
 */
final class UuSetPropertyDescImpl extends
    \S2Container\Beans\Impl\PropertyDescImpl {
    private $setterPropertyName_;

    public function __construct($propertyName, $propertyType, $readMethod, $writeMethod, \S2Container\Beans\BeanDesc $beanDesc) {
        parent::__construct($propertyName,
            $propertyType,
            $readMethod,
            $writeMethod,
            $beanDesc);

    }

    /**
     *
     */
    public final function getSetterPropertyName() {
        return $this->setterPropertyName_;
    }

    /**
     * @param string property name
     */
    public final function setSetterPropertyName($name) {
        $this->setterPropertyName_ = $name;
    }

    /**
     *
     */
    public function setValue($target, $value) {
        try {
            \S2Container\Util\MethodUtil::invoke($this->writeMethod_,
                $target,
                array($this->setterPropertyName_, $value));
        } catch (Exception $t) {
            throw new \S2Container\Beans\Exception\IllegalPropertyRuntimeException($this->beanDesc_
                ->getBeanClass(), $this->propertyName_, $t);
        }
    }
}
