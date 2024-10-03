<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    use HasFactory;

    // public function getUser()
    // {
    //     return TelegramUser::where('chat_id', $this->getChatId())->first();
    // }
}
