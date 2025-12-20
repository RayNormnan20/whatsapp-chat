<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'allow_registration',
        'allow_send_messages',
        'allow_send_images',
        'allow_send_audio',
    ];

    public static function instance(): self
    {
        return static::query()->first() ?? static::create([
            'allow_registration' => true,
            'allow_send_messages' => true,
            'allow_send_images' => true,
            'allow_send_audio' => true,
        ]);
    }
}

