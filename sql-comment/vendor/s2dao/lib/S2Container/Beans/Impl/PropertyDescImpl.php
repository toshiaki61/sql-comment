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
class PropertyDescImpl implements \S2Container\Beans\PropertyDesc {
    protected $propertyName_ = null;
    protected $propertyType_ = null;
    protected $readMethod_ = null;
    protected $writeMethod_ = null;
    protected $beanDesc_ = null;

    /**
     *
     */
    public function __construct($propertyName, $propertyType, $readMethod, $writeMethod, \S2Container\Beans\BeanDesc $beanDesc) {
        if ($propertyName == null) {
            throw new \S2Container\Exception\EmptyRuntimeException("propertyName");
        }

        $this->propertyName_ = $propertyName;
        $this->propertyType_ = $propertyType;
        $this->readMethod_ = $readMethod;
        $this->writeMethod_ = $writeMethod;
        $this->beanDesc_ = $beanDesc;
    }

    /**
     * @return string property name
     */
    public final function getPropertyName() {
        return $this->propertyName_;
    }

    /**
     *
     */
    public final function getPropertyType() {
        return $this->propertyType_;
    }

    /**
     *
     */
    public final function getReadMethod() {
        return $this->readMethod_;
    }

    /**
     *
     */
    public final function setReadMethod($readMethod) {
        $this->readMethod_ = $readMethod;
    }

    /**
     *
     */
    public final function hasReadMethod() {
        return $this->readMethod_ != null;
    }

    /**
     *
     */
    public final function getWriteMethod() {
        return $this->writeMethod_;
    }

    /**
     *
     */
    public final function setWriteMethod($writeMethod) {
        $this->writeMethod_ = $writeMethod;
        $propertyTypes = $writeMethod->getParameters();
        $this->propertyType_ = $propertyTypes[0]->getClass();
    }

    /**
     *
     */
    public final function hasWriteMethod() {
        return $this->writeMethod_ != null;
    }

    /**
     *
     */
    public final function getValue($target) {
        return \S2Container\Util\MethodUtil::invoke($this->readMethod_, $target, null);
    }

    /**
     *
     */
    public function setValue($target, $value) {
        try {
            \S2Container\Util\MethodUtil::invoke($this->writeMethod_,
                $target,
                array($value));
        } catch (Exception $t) {
            throw new S2Container_IllegalPropertyRuntimeException($this->beanDesc_
                ->getBeanClass(), $this->propertyName_, $t);
        }
    }

    /**
     *
     */
    public final function getBeanDesc() {
        return $this->beanDesc_;
    }

    /**
     *
     */
    public final function __toString() {
        $buf = "";
        $buf .= "propertyName=";
        $buf .= $this->propertyName_;
        $buf .= ",propertyType=";
        $buf .= $this->propertyType_ != null ? $this->propertyType_
            ->getName() : "null";
        $buf .= ",readMethod=";
        $buf .= $this->readMethod_ != null ? $this->readMethod_
            ->getName() : "null";
        $buf .= ",writeMethod=";
        $buf .= $this->writeMethod_ != null ? $this->writeMethod_
            ->getName() : "null";
        return $buf;
    }

    /**
     *
     */
    public function convertIfNeed($arg) {
    }
}
