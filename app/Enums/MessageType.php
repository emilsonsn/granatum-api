<?php

namespace App\Enums;

enum MessageType: string
{
    case Text = 'Text';
    case Audio = 'Audio';
    case Image = 'Image';
    case Video = 'Video';
    case File = 'File';
}