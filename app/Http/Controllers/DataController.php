<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 10); // Unit per page
        $page = $request->query('page', 1); // Current page
        $keyword = $request->query('keyword');

        $data = Data::getAllData();

        // Word filter
        if ($keyword) {
            $data = $data->filter(function ($item) use ($keyword) {
                return stripos($item['name'], $keyword) !== false
                || stripos($item['phone'], $keyword) !== false
                || stripos($item['email'], $keyword) !== false
                || stripos($item['username'], $keyword) !== false
                || stripos($item['company'], $keyword) !== false
                || stripos($item['nationality'], $keyword) !== false;
            });
        }

        $total = $data->count();

        // Pagination
        $data = $data->skip(($page - 1) * $perPage)
        ->take($perPage);

        $filteredData = $data->map(function ($item) {
            return collect($item)->only(['name', 'phone', 'email', 'username', 'company', 'nationality']);
        });

        return response()->json([
            'data' => $filteredData,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[A-Za-z0-9ก-๙\s]+$/u|max:80',
            'phone' => 'required|regex:/^[\d(), -]+$/|min:8|max:20',
            'email' => 'required|email|max:255',
            'username' => 'required|string|min:6|max:30',
            'company' => 'required|string|max:80',
            'nationality' => 'required|string|max:40',
            'password' => 'required|string|min:6|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = Data::getAllData();
        $data->push(new Data($request->all()));
        Data::saveAllData($data);

        return response()->json(['message' => 'Data inserted successfully']);
    }

    public function show($username)
    {
        $data = Data::getDataByUsername($username);

        if ($data->isNotEmpty()) {
            $filteredData = $data->map(function ($item) {
                return $item->only(['name', 'phone', 'email', 'username', 'company', 'nationality']);
            });

            return response()->json($filteredData);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function update(Request $request, $username)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[A-Za-z0-9ก-๙\s]+$/u|max:80',
            'phone' => 'required|regex:/^[\d(), -]+$/|min:8|max:20',
            'email' => 'required|email|max:255',
            'username' => 'required|string|min:6|max:30',
            'company' => 'required|string|max:80',
            'nationality' => 'required|string|max:40',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = Data::getDataByUsername($username)->first();

        if ($data) {
            $data->fill($request->except('password')); // Exclude the password field from the update

            $allData = Data::getAllData();
            $updatedData = $allData->map(function ($item) use ($data) {
                if ($item->username === $data->username) {
                    return $data;
                } else {
                    return $item;
                }
            });

            Data::saveAllData($updatedData);

            return response()->json(['message' => 'Data updated successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function destroy($username)
    {
        $data = Data::getAllData();
        $filteredData = $data->where('username', $username);

        if ($filteredData->isNotEmpty()) {
            $data = $data->reject(function ($item) use ($username) {
                return $item->username === $username;
            });

            Data::saveAllData($data);

            return response()->json(['message' => 'Data deleted successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }
}
