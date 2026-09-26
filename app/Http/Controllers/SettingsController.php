<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $storeName = Setting::where('key', 'store_name')->value('value') ?? 'النظام المالي';
        return view('settings', compact('storeName'));
    }

    public function updateStoreName(Request $request)
    {
        $request->validate(['store_name' => 'required|string|max:255']);
        Setting::updateOrCreate(['key' => 'store_name'], ['value' => $request->store_name]);
        return redirect()->back()->with('success', 'تم تحديث اسم النظام بنجاح!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);
        $user = User::find(Auth::id());
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }
        $user->update(['password' => Hash::make($request->new_password)]);
        return redirect()->back()->with('success', 'تم تغيير كلمة المرور بنجاح!');
    }
}
