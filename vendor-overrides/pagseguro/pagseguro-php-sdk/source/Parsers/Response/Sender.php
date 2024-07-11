<?php
/**
 * 2007-2016 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    PagSeguro Internet Ltda.
 * @copyright 2007-2016 PagSeguro Internet Ltda.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 *
 */

namespace PagSeguro\Parsers\Response;

use PagSeguro\Domains\Document;
use PagSeguro\Domains\Phone;

/**
 * Trait Sender
 * @package PagSeguro\Parsers\Response
 */
trait Sender
{
    /**
     * @var
     */
    private $sender;

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param $sender
     * @return $this
     */
    public function setSender($sender)
    {
        $phone = new Phone();

        if (isset($sender->phone)) {
            $phone
                ->setAreaCode(lw_current_func($sender->phone->areaCode))
                ->setNumber(lw_current_func($sender->phone->number));
        }

        $senderClass = new \PagSeguro\Domains\Sender();
        $this->sender = $senderClass->setName(lw_current_func($sender->name))
            ->setEmail(lw_current_func($sender->email))
            ->setPhone($phone)
            ->setDocuments(new Document());

        return $this;
    }
}