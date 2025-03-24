<?php

namespace App\Enums;

enum OrderPaymentMethodEnum: string
{
    case Cash = 'Cash';
    case InvoicedPaymentForecast = 'InvoicedPaymentForecast';
}
