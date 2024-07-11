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

use PagSeguro\Resources\Factory\Shipping\Address;
use PagSeguro\Resources\Factory\Shipping\Cost;
use PagSeguro\Resources\Factory\Shipping\Type;

/**
 * Trait Shipping
 * @package PagSeguro\Parsers\Response
 */
trait Shipping
{
    /**
     * @var
     */
    private $shipping;

    /**
     * @return mixed
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param mixed $shipping
     * @return Response
     */
    public function setShipping($shipping)
    {
        if (lw_current_func($shipping) !== false) {
            $shippingClass = new \PagSeguro\Domains\Shipping();

            $shippingAddress = new Address($shippingClass);

            $shippingAddress->withParameters(
                lw_current_func($shipping->address->street),
                lw_current_func($shipping->address->number),
                lw_current_func($shipping->address->district),
                lw_current_func($shipping->address->postalCode),
                lw_current_func($shipping->address->city),
                lw_current_func($shipping->address->state),
                lw_current_func($shipping->address->country),
                lw_current_func($shipping->address->complement)
            );

            $shippingType = new Type($shippingClass);
            $shippingType->withParameters(lw_current_func($shipping->type));

            $shippingCost = new Cost($shippingClass);
            $shippingCost->withParameters(lw_current_func($shipping->cost));
            $this->shipping = $shippingClass;
        }
        return $this;
    }
}
