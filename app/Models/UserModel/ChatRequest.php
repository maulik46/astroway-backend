<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRequest extends Model
{
    use HasFactory;
    protected $table = 'chatrequest';
    protected $fillable = [
        'astrologerId',
        'chatStatus',
        'userId',
        'chatRate',
        'totalMin',
        'deductionFromAstrologer',
        'deduction',
		 'isFreeSession',
		'chat_duration'
    ];
}
