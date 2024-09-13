<?php

namespace App\Enums;

enum OrderTypeEnum: string
{
    case Order = 'Order';
    case Reimbursement = 'Reimbursement';
    case Service = 'Service';
    case Material = 'Material';
}