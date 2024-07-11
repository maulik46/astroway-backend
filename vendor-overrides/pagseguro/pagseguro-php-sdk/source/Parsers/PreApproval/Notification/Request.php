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

namespace PagSeguro\Parsers\PreApproval\Notification;

use PagSeguro\Domains\Address;
use PagSeguro\Domains\Phone;
use PagSeguro\Domains\PreApproval\Sender;
use PagSeguro\Parsers\Error;
use PagSeguro\Parsers\Parser;
use PagSeguro\Parsers\PreApproval\Search\Code\Response;
use PagSeguro\Resources\Http;

/**
 * Class Request
 * @package PagSeguro\Parsers\PreApproval\Notification
 */
class Request extends Error implements Parser
{
    /**
     * @param \PagSeguro\Resources\Http $http
     * @return Response
     */
    public static function success(Http $http)
    {
        $xml = simplexml_load_string($http->getResponse());

        $response = new Response();
        $response->setName(lw_current_func($xml->name))
            ->setCode(lw_current_func($xml->code))
            ->setDate(lw_current_func($xml->date))
            ->setTracker(lw_current_func($xml->tracker))
            ->setStatus(lw_current_func($xml->status))
            ->setReference(lw_current_func($xml->reference))
            ->setLastEventDate(lw_current_func($xml->lastEventDate))
            ->setCharge(lw_current_func($xml->charge))
            ->setSender(
                (new Sender)->setName(lw_current_func($xml->sender->name))
                    ->setEmail(lw_current_func($xml->sender->email))
                    ->setPhone(
                        (new Phone)->setAreaCode(lw_current_func($xml->sender->phone->areaCode))
                            ->setNumber(lw_current_func($xml->sender->phone->areaCode))
                    )->setAddress(
                        (new Address)->setStreet(lw_current_func($xml->sender->address->street))
                            ->setNumber(lw_current_func($xml->sender->address->number))
                            ->setComplement(lw_current_func($xml->sender->address->complement))
                            ->setDistrict(lw_current_func($xml->sender->address->district))
                            ->setCity(lw_current_func($xml->sender->address->city))
                            ->setState(lw_current_func($xml->sender->address->state))
                            ->setCountry(lw_current_func($xml->sender->address->country))
                            ->setPostalCode(lw_current_func($xml->sender->address->postalCode))
                    )
            );


        return $response;
    }

    /**
     * @param \PagSeguro\Resources\Http $http
     * @return \PagSeguro\Domains\Error
     */
    public static function error(Http $http)
    {
        $error = parent::error($http);
        return $error;
    }
}
