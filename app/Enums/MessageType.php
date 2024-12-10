<?php

namespace App\Enums;

enum MessageType: string
{
    case Text = 'Text';
    case Audio = 'Audio';
    case File = 'File';    
}