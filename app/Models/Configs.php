<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configs extends Model
{
    use HasFactory, ModelTrait;
    protected $table = 'configs';

    protected $fillable = [
        'appname',
        'subname',
        'skin',
        'copyright',
        'version',
        'logo',
        'poster',
        'bg',
        'url_sync_bjtm',
        'url_proxy_sync_bjtm ',
        'port_proxy_sync_bjtm',
        'user_auth',
        'pass_auth',
        'url_sync_va_bjtm',
        'identity_bjtm_va',
        'identity_kasda_va',
        'url_sync_api_ecounting',
        'tahapan_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
