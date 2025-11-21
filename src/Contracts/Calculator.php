<?php

namespace Linecore\Shoppingcart\Contracts;

use Linecore\Shoppingcart\CartItem;

interface Calculator
{
    public static function getAttribute(string $attribute, CartItem $cartItem);
}
