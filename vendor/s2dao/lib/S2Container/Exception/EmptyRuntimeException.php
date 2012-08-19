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
namespace S2Container\Exception;
/**
 * @package org.seasar.framework.exception
 * @author klove
 */
final class EmptyRuntimeException extends
    \S2Container\Exception\S2RuntimeException {
    private $targetName_;

    /**
     * @param string
     */
    public function __construct($targetName) {
        parent::__construct("ESSR0007", array($targetName));
        $this->targetName_ = $targetName;
    }

    /**
     *
     */
    public function getTargetName() {
        return $this->targetName_;
    }
}
