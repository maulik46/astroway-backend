<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallRequest extends Model
{
    use HasFactory;
    protected $table = 'callrequest';
    protected $fillable = [
        'astrologerId',
        'callStatus',
        'userId',
        'totalMin',
        'callRate',
        'deductionFromAstrologer',
        'deduction',
        'sId',
        'channelName',
        'chatId',
        'created_at',
        'sId1',
		'isFreeSession',
        'call_type',
		'call_duration',
    ];
}
