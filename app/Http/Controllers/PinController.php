<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PinController extends Controller
{
    public function showChangePinForm(): View
    {
        return view('auth.change-pin');
    }

    public function changePin(Request $request): JsonResponse
    {
        $request->validate([
            'current_pin' => 'required|digits:4',
            'new_pin' => 'required|digits:4|different:current_pin',
            'confirm_pin' => 'required|digits:4|same:new_pin'
        ]);

        // Get current PIN - check file first, then fallback to .env
        $currentStoredPin = file_exists(storage_path('app/system_pin.txt'))
            ? file_get_contents(storage_path('app/system_pin.txt'))
            : env('SYSTEM_PIN', '1234');

        // Verify current PIN
        if ($request->current_pin !== $currentStoredPin) {
            return response()->json([
                'success' => false,
                'message' => 'Current PIN is incorrect'
            ], 401);
        }

        $this->updateStoredPin($request->new_pin);

        return response()->json([
            'success' => true,
            'message' => 'PIN changed successfully',
            'redirect' => '/dashboard'
        ]);
    }

    /**
     * Update the stored PIN (you need to implement this based on your storage method)
     */
    private function updateStoredPin(string $newPin): void
    {
        //Simple file storage (easiest for now)
        file_put_contents(storage_path('app/system_pin.txt'), $newPin);
    }
}
