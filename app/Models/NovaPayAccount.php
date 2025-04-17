<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $account_name
 * @property string $login
 * @property string $password
 * @property string|null $principal
 * @property string|null $temp_principal
 * @property string|null $code_operation_otp
 * @property string|null $principal_valid_until
 */
class NovaPayAccount extends Model
{
    protected $table = 'novapay_accounts';

    protected $fillable = [
        'account_name',
        'login',
        'password',
        'principal',
        'temp_principal',
        'code_operation_otp',
        'principal_valid_until',
    ];
}
