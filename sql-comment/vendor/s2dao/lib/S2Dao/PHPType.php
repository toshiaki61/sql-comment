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
// | Authors: nowel                                                       |
// +----------------------------------------------------------------------+
// $Id: $
//
namespace S2Dao;
/**
 * @author nowel
 * @package org.seasar.s2dao
 */
final class S2Dao_PHPType {

    const Boolean = 'boolean';
    const Integer = 'integer';
    const Double = 'double';
    const Float = 'float';
    const String = 'string';
    const Object = 'object';
    const Resource = 'resource';
    const Null = 'NULL';
    const Unknown = 'unknown type';

    public static function getType($type, $value = null){
        if($type instanceof Reflector){
            $argClass = $type->getClass();
            if($argClass === null){
                return gettype($value);
            }
            return $argClass->getName();
        } else if(is_object($type)){
            return get_class($type);
        } else {
            return gettype($type);
        }
    }
}
?>