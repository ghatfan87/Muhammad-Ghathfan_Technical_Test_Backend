<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule as ValidationRule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user = User::all();
        
            if ($user->isEmpty()) {
                return ApiFormatter::createAPI(404, 'No Leads Found');
            } else {
                return ApiFormatter::createAPI(200, 'Success', $user);
            }
        } catch (Exception $e) {
            return ApiFormatter::createAPI(500, 'Internal Server Error');
        }
        
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', ValidationRule::in(['super_admin', 'customer_service', 'salesperson', 'operational', 'client'])],
            'sales_type' => ['nullable', ValidationRule::in(['residential', 'commercial', 'both'])], // Optional for Salesperson only
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        
        if ($user->role === 'salesperson') {
            $user->sales_type = $request->sales_type;
        }

        $user->save();

        try {
            $TambahData = User::find($user->id);
        
            if ($TambahData) {
                return ApiFormatter::createAPI(200, 'success', $user);
            } else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'failed', $error->getMessage());
        }
        
    }

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Cari pengguna berdasarkan ID
            $user = User::findOrFail($id);
    
            // Hapus pengguna
            $user->delete();
    
            return ApiFormatter::createAPI(200, 'User deleted successfully');
    
        } catch (Exception $e) {
            return ApiFormatter::createAPI(404, 'User not found');
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'Failed to delete user', $error->getMessage());
        }
    }
}
