<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(User::all(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function register(Request $request)
    {
        Log::info('Incoming Request Data:', $request->all());
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8'
            ]);
            Log::info('Validated Data:', $validated); //

            $user = User::create(
                ['name' =>$validated['name'] ,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])]);

            $token =  $user->createToken('token-name')->plainTextToken;

            Log::info("USER -- ". $user);
            Log::info("TOKEN -- ". $token);

            return response()->json(["user"=>$user,"token"=>$token], 201);
        } catch (\Illuminate\Validation\ValidationException $ValidationException) {
            Log::info($ValidationException->errors());
            return response()->json(['errors' => $ValidationException->errors()], 422);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception->getMessage()], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function authenticate(Request $request)
    {
        Log::info('Incoming Request Data:', $request->all());
        try {
            $validated = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string|min:8'
            ]);
            Log::info('Validated Data:', $validated); //

            $user = User::where('email', $validated['email'])->first();

            if(empty($user) || !Hash::check($validated['password'],$user->password)){
                return response()->json(['message' => 'User not found'], 404);
            }

            $token =  $user->createToken('token-name')->plainTextToken;

            Log::info("USER -- ". $user);
            Log::info("TOKEN -- ". $token);

            return response()->json(["user"=>$user,"token"=>$token], 201);
        } catch (\Illuminate\Validation\ValidationException $ValidationException) {
            Log::info($ValidationException->errors());
            return response()->json(['errors' => $ValidationException->errors()], 422);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
