<?php

namespace App\Enums;

enum MessageType: string
{
    case Text = 'Text';
    case Audio = 'Audio';
    case Midea = 'Midea';
    case File = 'File';
}