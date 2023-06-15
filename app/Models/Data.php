<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Data extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'password', 'username', 'company', 'nationality'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getAllData()
    {
        $data = File::get(resource_path('json/data.json'));
        return collect(json_decode($data, true))->map(function ($item) {
            return new self($item);
        });
    }

    public static function saveAllData($data)
    {
        $modifiedData = $data->map(function ($item) {
            return [
                'name' => $item->name,
                'phone' => $item->phone,
                'email' => $item->email,
                'password' => $item->password,
                'username' => $item->username,
                'company' => $item->company,
                'nationality' => $item->nationality,
            ];
        });

        File::put(resource_path('json/data.json'), json_encode($modifiedData, JSON_PRETTY_PRINT));
    }

    public static function getDataByUsername($username)
    {
        $data = self::getAllData();
        return $data->where('username', $username);
    }
}
