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
// $Id: S2Container_S2RuntimeException.class.php 429 2007-03-04 10:53:06Z klove $
namespace S2Dao;
/**
 * @package org.seasar.framework.exception
 * @author klove
 */
class S2RuntimeException extends Exception
{
    private $messageCode_;
    private $args_;
    private $message_;
    private $simpleMessage_;

    /**
     *
     */
    public function __construct($messageCode,
                                $args = null,
                                $cause = null)
    {
        $cause instanceof Exception ?
            $msg = $cause->getMessage() . "\n" :
            $msg = "";
        $msg .= S2ContainerMessageUtil::getMessageWithArgs($messageCode,$args);
        parent::__construct($msg);
    }
}

