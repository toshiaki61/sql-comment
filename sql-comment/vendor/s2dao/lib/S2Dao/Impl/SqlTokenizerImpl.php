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
namespace S2Dao\Impl;
/**
 * @author nowel
 * @package org.seasar.s2dao.parser
 */
class S2Dao_SqlTokenizerImpl implements S2Dao_SqlTokenizer {

    private $sql = '';
    private $position = 0;
    private $token = null;
    private $tokenType = self::SQL;
    private $nextTokenType = self::SQL;
    private $bindVariableNum = 0;

    public function __construct($sql) {
        $this->sql = $sql;
    }

    public function getPosition() {
        return $this->position;
    }

    public function getToken() {
        return $this->token;
    }

    public function getBefore() {
        return substr($this->sql, 0, $this->position);
    }

    public function getAfter() {
        return substr($this->sql, $this->position);
    }

    public function getTokenType() {
        return $this->tokenType;
    }

    public function getNextTokenType() {
        return $this->nextTokenType;
    }

    public function next() {
        if ($this->position >= strlen($this->sql)) {
            $this->token = null;
            $this->tokenType = self::EOF;
            $this->nextTokenType = self::EOF;
            return $this->tokenType;
        }
        switch ($this->nextTokenType) {
        case self::SQL:
            $this->parseSql();
            break;
        case self::COMMENT:
            $this->parseComment();
            break;
        case self::ELSE_:
            $this->parseElse();
            break;
        case self::BIND_VARIABLE:
            $this->parseBindVariable();
            break;
        default:
            $this->parseEof();
            break;
        }
        return $this->tokenType;
    }

    protected function parseSql() {
        $commentStartPos = strpos($this->sql, '/*', $this->position);
        $lineCommentStartPos = strpos($this->sql, '--', $this->position);
        $bindVariableStartPos = strpos($this->sql, '?', $this->position);
        $elseCommentStartPos = -1;
        $elseCommentLength = -1;
        if ($lineCommentStartPos !== false && 0 <= $lineCommentStartPos) {
            $skipPos = $this->skipWhitespace($lineCommentStartPos + 2);
            if ($skipPos + 4 < strlen($this->sql)
                    && 'ELSE' === substr($this->sql, $skipPos, 4)) {
                $elseCommentStartPos = $lineCommentStartPos;
                $elseCommentLength = $skipPos + 4 - $lineCommentStartPos;
            }
        }
        $nextStartPos = $this->getNextStartPos($commentStartPos,
                                               $elseCommentStartPos,
                                               $bindVariableStartPos);

        if ($nextStartPos === false || $nextStartPos < 0) {
            $this->token = substr($this->sql, $this->position);
            $nextTokenType = self::EOF;
            $this->position = strlen($this->sql);
            $this->tokenType = self::SQL;
        } else {
            $endPos = $nextStartPos - $this->position;
            $this->token = substr($this->sql, $this->position, $endPos);
            $this->tokenType = self::SQL;
            $needNext = $nextStartPos == $this->position;

            if ($nextStartPos == $commentStartPos) {
                $this->nextTokenType = self::COMMENT;
                $this->position = $commentStartPos + 2;
            } else if ($nextStartPos == $elseCommentStartPos) {
                $this->nextTokenType = self::ELSE_;
                $this->position = $elseCommentStartPos + $elseCommentLength + 1;
            } else if ($bindVariableStartPos !== false &&
                        $nextStartPos == $bindVariableStartPos) {
                $this->nextTokenType = self::BIND_VARIABLE;
                $this->position = $bindVariableStartPos;
            }
            if ($needNext) {
                $this->next();
            }
        }
    }

    protected function getNextStartPos($commentStartPos,
                                       $elseCommentStartPos,
                                       $bindVariableStartPos) {

        $nextStartPos = -1;
        if ($commentStartPos !== false && $commentStartPos >= 0) {
            $nextStartPos = $commentStartPos;
        }
        if ($elseCommentStartPos >= 0
                && ($nextStartPos < 0 || $elseCommentStartPos < $nextStartPos)) {
            $nextStartPos = $elseCommentStartPos;
        }
        if ($bindVariableStartPos !== false && $bindVariableStartPos >= 0
                && ($nextStartPos < 0 || $bindVariableStartPos < $nextStartPos)) {
            $nextStartPos = $bindVariableStartPos;
        }
        return $nextStartPos;
    }

    protected function nextBindVariableName() {
        return '$' . ++$this->bindVariableNum;
    }

    /**
     * @throws S2Dao_TokenNotClosedRuntimeException
     */
    protected function parseComment() {
        $commentEndPos = strpos($this->sql, '*/', $this->position);
        if ($commentEndPos === false || $commentEndPos < 0) {
            throw new S2Dao_TokenNotClosedRuntimeException('*/',
                                                     substr($this->sql,
                                                     $this->position));
        }
        $endPos = $commentEndPos - $this->position;
        $this->token = substr($this->sql, $this->position, $endPos);
        $this->nextTokenType = self::SQL;
        $this->position = $commentEndPos + 2;
        $this->tokenType = self::COMMENT;
    }

    protected function parseBindVariable() {
        $this->token = $this->nextBindVariableName();
        $this->nextTokenType = self::SQL;
        $this->position += 1;
        $this->tokenType = self::BIND_VARIABLE;
    }

    protected function parseElse() {
        $this->token = null;
        $this->nextTokenType = self::SQL;
        $this->tokenType = self::ELSE_;
    }

    protected function parseEof() {
        $this->token = null;
        $this->tokenType = self::EOF;
        $this->nextTokenType = self::EOF;
    }

    public function skipToken() {
        $index = strlen($this->sql);
        $quote = $this->position < strlen($this->sql) ?
             substr($this->sql, $this->position, 1) : '';
        $quoting = $quote === '\'' || $quote === '(';
        if ($quote === '(') {
            $quote = ')';
        }
        $i = $quoting ? ($this->position + 1) : $this->position;
        $len = strlen($this->sql);
        for (; $i < $len; ++$i) {
            $c = substr($this->sql, $i, 1);

            if (preg_match('/(\s|,|\)|\()/', $c) && !$quoting) {
                $index = $i;
                break;
            } else if ($c == '/' && ($i + 1) < $len
                        && substr($this->sql, ($i + 1), 1) == '*') {
                $index = $i;
                break;
            } else if ($c == '-' && ($i + 1) < $len
                        && substr($this->sql, ($i + 1), 1) == '-') {
                $index = $i;
                break;
            } else if ($quoting && $quote == '\'' && $c == '\''
                        && ($len <= ($i + 1) ||
                            substr($this->sql, ($i + 1), 1) != '\'') ) {
                $index = $i + 1;
                break;
            } else if ($quoting && $c == $quote) {
                $index = $i + 1;
                break;
            }
        }
        $tok = substr($this->sql, $this->position, $index - $this->position);
        $this->token = $tok;
        $this->tokenType = self::SQL;
        $this->nextTokenType = self::SQL;
        $this->position = $index;
        return $this->token;
    }

    public function skipWhitespace($position = null) {
        if($position === null){
            $index = $this->skipWhitespace($this->position);
            $this->token = substr($this->sql, $this->position, $index);
            $this->position = $index;
            return $this->token;
        } else {
            $index = strlen($this->sql);
            $len = strlen($this->sql);
            for ($i = $position; $i < $len; ++$i) {
                $c = substr($this->sql, $i, 1);
                if (preg_match('/\W?/', $c)) {
                    $index = $i;
                    break;
                }
            }
            return $index;
        }
    }
}
?>
