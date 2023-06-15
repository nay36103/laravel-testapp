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

    /**
     * Get the JWT identifier for the user.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the additional custom claims to be added to the JWT.
     *
     * @return array
     */

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get all data from the JSON file.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAllData()
    {
        $data = json_decode(File::get(resource_path('json/data.json')), true);
        return collect($data)->map(function ($item) {
            return new self($item);
        });
    }

    /**
     * Save all data to the JSON file.
     *
     * @param  \Illuminate\Support\Collection  $data
     * @return void
     */
    public static function saveAllData($data)
    {
        $modifiedData = $data->map(function ($item) {
            return $item->toArray();
        });

        File::put(resource_path('json/data.json'), json_encode($modifiedData));
    }

    /**
     * Get data by username.
     *
     * @param  string  $username
     * @return \Illuminate\Support\Collection
     */
    public static function getDataByUsername($username)
    {
        $data = self::getAllData();
        return $data->where('username', $username);
    }
}
