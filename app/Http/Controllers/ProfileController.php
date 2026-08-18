<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DigitalSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $departments = Department::where('is_active', true)->get();
        $digitalSignatures = DigitalSignature::with('workflowInstance.definition')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('profile.index', compact('user', 'departments', 'digitalSignatures'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'department_id' => 'nullable|exists:departments,id',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('department_id')) {
            $user->department_id = $request->department_id;
        }
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profile information updated successfully!');
    }
}
