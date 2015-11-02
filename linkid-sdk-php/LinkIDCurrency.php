<?php

abstract class LinkIDCurrency
{

    const EUR = 0;

}

function linkIDCurrencyToString($currency)
{
    if (LinkIDCurrency::EUR == $currency) return "EUR";

    return "EUR";
}

function parseLinkIDCurrency($currency)
{

    if (null == $currency) return null;

    if ($currency == "EUR") {
        return LinkIDCurrency::EUR;
    }

    throw new Exception("Unexpected currency: " . $currency);

}
