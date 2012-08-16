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
final class BeanDescImpl implements \S2Container\Beans\BeanDesc {
    private static $EMPTY_ARGS = array();
    private $beanClass_;
    private $constructors_;
    private $propertyDescCache_ = array();
    private $propertyDescCacheIndex_ = array();
    private $methodsCache_ = array();
    private $fieldCache_ = array();
    private $constCache_ = array();

    /**
     * @param \ReflectionClass
     */
    public function __construct(\ReflectionClass $beanClass) {
        $this->beanClass_ = $beanClass;
        $this->constructors_ = $this->beanClass_
            ->getConstructor();
        $this->_setupMethods();
        $this->_setupPropertyDescs();
        $this->_setupField();
        $this->_setupConstant();
    }

    /**
     * @see \S2Container\Beans\BeanDesc::getBeanClass()
     */
    public function getBeanClass() {
        return $this->beanClass_;
    }

    /**
     * @param string property name
     */
    public function hasPropertyDesc($propertyName) {
        return array_key_exists($propertyName, $this->propertyDescCache_);
    }

    /**
     * @see \S2Container\Beans\BeanDesc::getPropertyDesc()
     */
    public function getPropertyDesc($propertyName) {
        if (is_int($propertyName)) {
            if ($propertyName < 0 || $propertyName >= count($this->propertyDescCacheIndex_)) {
                throw new \S2Container\Beans\Exception\PropertyNotFoundRuntimeException($this->beanClass_, 'index ' . $propertyName);
            }
            return $this->propertyDescCache_[$this->propertyDescCacheIndex_[$propertyName]];
        }

        if (array_key_exists($propertyName, $this->propertyDescCache_)) {
            return $this->propertyDescCache_[$propertyName];
        } else {
            throw new \S2Container\Beans\Exception\PropertyNotFoundRuntimeException($this->beanClass_, $propertyName);
        }
    }

    /**
     *
     */
    private function _getPropertyDesc0($propertyName) {
        if (array_key_exists($propertyName, $this->propertyDescCache_)) {
            return $this->propertyDescCache_[$propertyName];
        } else {
            return null;
        }
    }

    /**
     * @see \S2Container\Beans\BeanDesc::getPropertyDescSize()
     */
    public function getPropertyDescSize() {
        return count($this->propertyDescCache_);
    }

    /**
     * @see \S2Container\Beans\BeanDesc::hasField()
     */
    public function hasField($fieldName) {
        return array_key_exists($fieldName, $this->fieldCache_);
    }

    /**
     * @see \S2Container\Beans\BeanDesc::getField()
     */
    public function getField($fieldName) {
        if (array_key_exists($fieldName, $this->fieldCache_)) {
            $field = $this->fieldCache_[$fieldName];
        } else {
            throw new \S2Container\Beans\Exception\FieldNotFoundRuntimeException($this->beanClass_, $fieldName);
        }
        return $field;
    }

    /**
     * @see \S2Container\Beans\BeanDesc::hasConstant()
     */
    public function hasConstant($constName) {
        return array_key_exists($constName, $this->constCache_);
    }

    /**
     * @see \S2Container\Beans\BeanDesc::getConstant()
     */
    public function getConstant($constName) {
        if (array_key_exists($constName, $this->constCache_)) {
            $constant = $this->constCache_[$constName];
        } else {
            throw new \S2Container\Beans\Exception\ConstantNotFoundRuntimeException($this->beanClass_, $constName);
        }
        return $constant;
    }

    /**
     * @see \S2Container\Beans\BeanDesc::newInstance()
     */
    public function newInstance($args, $componentDef = null) {
        return \S2Container\Util\ConstructorUtil::newInstance($this->beanClass_,
            $args);
    }

    /**
     * @see \S2Container\Beans\BeanDesc::invoke()
     */
    public function invoke($target, $methodName, $args) {
        $method = $this->getMethods($methodName);
        return \S2Container\Util\MethodUtil::invoke($method, $target, $args);
    }

    /**
     * @see \S2Container\Beans\BeanDesc::getSuitableConstructor()
     */
    public function getSuitableConstructor() {
        return $this->constructors_;
    }

    /**
     * @see \S2Container\Beans\BeanDesc::getMethods()
     */
    public function getMethods($methodName) {
        if (array_key_exists($methodName, $this->methodsCache_)) {
            $methods = $this->methodsCache_[$methodName];
        } else {
            throw new \S2Container\Beans\Exception\MethodNotFoundRuntimeException($this->beanClass_, $methodName, null);
        }
        return $methods;
    }

    /**
     * @see \S2Container\Beans\BeanDesc::hasMethod()
     */
    public function hasMethod($methodName) {
        if (array_key_exists($methodName, $this->methodsCache_)) {
            return $this->methodsCache_[$methodName] != null;
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getMethodNames() {
        return array_keys($this->methodsCache_);
    }

    /**
     * @param string
     * @return boolean
     */
    private function _isFirstCapitalize($str) {
        $top = substr($str, 0, 1);
        $upperTop = strtoupper($top);
        return $upperTop == $top;
    }

    /**
     *
     */
    private function _setupPropertyDescs() {
        $methods = $this->beanClass_
            ->getMethods();
        $o = count($methods);
        for ($i = 0; $i < $o; $i++) {
            $mRef = $methods[$i];
            $methodName = $mRef->getName();
            if (preg_match("/^get(.+)/", $methodName, $regs)) {
                if (count($mRef->getParameters()) != 0) {
                    continue;
                }
                if (!$this->_isFirstCapitalize($regs[1])) {
                    continue;
                }
                $propertyName = $this->_decapitalizePropertyName($regs[1]);
                $this->_setupReadMethod($mRef, $propertyName);
            } else if (preg_match("/^is(.+)/", $methodName, $regs)) {
                if (count($mRef->getParameters()) != 0) {
                    continue;
                }
                if (!$this->_isFirstCapitalize($regs[1])) {
                    continue;
                }
                $propertyName = $this->_decapitalizePropertyName($regs[1]);
                $this->_setupReadMethod($mRef, $propertyName);
            } else if (preg_match("/^set(.+)/", $methodName, $regs)) {
                if (count($mRef->getParameters()) != 1) {
                    continue;
                }
                if (!$this->_isFirstCapitalize($regs[1])) {
                    continue;
                }
                $propertyName = $this->_decapitalizePropertyName($regs[1]);
                $this->_setupWriteMethod($mRef, $propertyName);
            } else if (preg_match("/^(__set)$/", $methodName, $regs)) {
                $propertyName = $regs[1];
                $this->_setupWriteMethod($mRef, $propertyName);
            }
        }
    }

    /**
     * @param string property name
     * @return string
     */
    private function _decapitalizePropertyName($name) {
        $top = substr($name, 0, 1);
        $top = strtolower($top);
        return substr_replace($name, $top, 0, 1);
    }

    /**
     * @param \S2Container\Beans\PropertyDesc
     */
    private function _addPropertyDesc(\S2Container\Beans\PropertyDesc $propertyDesc) {
        $this->propertyDescCache_[$propertyDesc->getPropertyName()] = $propertyDesc;
        $this->propertyDescCacheIndex_[] = $propertyDesc->getPropertyName();
    }

    /**
     * @param \ReflectionMethod
     * @param string property name
     */
    private function _setupReadMethod($readMethod, $propertyName) {
        $propDesc = $this->_getPropertyDesc0($propertyName);
        if ($propDesc != null) {
            $propDesc->setReadMethod($readMethod);
        } else {
            $writeMethod = null;
            $propDesc = new \S2Container\Beans\Impl\PropertyDescImpl($propertyName, null, $readMethod, null, $this);
            $this->_addPropertyDesc($propDesc);
        }
    }

    /**
     * @param \ReflectionMethod writeMethod
     * @param string propertyName
     */
    private function _setupWriteMethod($writeMethod, $propertyName) {
        $propDesc = $this->_getPropertyDesc0($propertyName);
        if ($propDesc != null) {
            $propDesc->setWriteMethod($writeMethod);

        } else {
            if ($propertyName == "__set") {
                $propDesc = new \S2Container\Beans\Impl\UuSetPropertyDescImpl($propertyName, null, null, $writeMethod, $this);
            } else {
                $propertyTypes = $writeMethod->getParameters();
                $propDesc = new \S2Container\Beans\Impl\PropertyDescImpl($propertyName, $propertyTypes[0]->getClass(), null, $writeMethod, $this);
            }
            $this->_addPropertyDesc($propDesc);
        }
    }

    /**
     *
     */
    private function _setupMethods() {
        $methods = $this->beanClass_
            ->getMethods();
        $o = count($methods);
        for ($i = 0; $i < $o; $i++) {
            $this->methodsCache_[$methods[$i]->getName()] = $methods[$i];
        }
    }

    /**
     *
     */
    private function _setupField() {
        $fields = $this->beanClass_
            ->getProperties();
        $o = count($fields);
        for ($i = 0; $i < $o; $i++) {
            if ($fields[$i]->isStatic()) {
                $this->fieldCache_[$fields[$i]->getName()] = $fields[$i];
            }
        }
    }

    /**
     *
     */
    private function _setupConstant() {
        $this->constCache_ = $this->beanClass_
            ->getConstants();
    }
}
