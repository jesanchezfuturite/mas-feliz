<?php

namespace App\Providers\Filament;

use Filament\Facades\Filament;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class CustomAvatarProvider implements AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        $name = str(Filament::getNameForDefaultAvatar($record))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&format=svg&color=FFFFFF&background=556ee6';
    }
}
