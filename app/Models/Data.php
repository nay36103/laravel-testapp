<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Data extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'password', 'username', 'company', 'nationality'];

    public function getJWTIdentifier()
    {
        return $this->username;
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getAllData()
    {
        $data = json_decode(File::get(resource_path('json/data.json')), true);
        return collect($data)->map(function ($item) {
            return new self([
                'name' => $item['name'],
                'phone' => $item['phone'],
                'email' => $item['email'],
                'password' => $item['password'],
                'username' => $item['username'],
                'company' => $item['company'],
                'nationality' => $item['nationality'],
            ]);
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

        File::put(resource_path('json/data.json'), json_encode($modifiedData));
    }

    public static function getDataByUsername($username)
    {
        $data = self::getAllData();
        return $data->where('username', $username);
    }
}
