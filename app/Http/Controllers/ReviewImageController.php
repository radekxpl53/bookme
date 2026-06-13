<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\BusinessReview;
use App\Models\EmployeeReview;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewImageController extends Controller
{
    public function storeBusiness(Request $request, Business $business)
    {
        $hasReview = BusinessReview::where('business_id', $business->id)
            ->where('user_id', Auth::id())
            ->exists();

        if (! $hasReview) {
            abort(403);
        }

        $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120']);

        $image = $this->storeImage($request->file('photo'));
        $business->reviewImages()->attach($image->id, ['user_id' => Auth::id()]);

        return back()->with('success', 'Zdjęcie zostało dodane do opinii.');
    }

    public function storeEmployee(Request $request, Appointment $appointment)
    {
        if ($appointment->client_id !== Auth::id()) {
            abort(403);
        }

        $hasReview = EmployeeReview::where('employee_id', $appointment->employee_id)
            ->where('user_id', Auth::id())
            ->exists();

        if (! $hasReview) {
            abort(403);
        }

        $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120']);

        $image = $this->storeImage($request->file('photo'));
        $appointment->employee->reviewImages()->attach($image->id, ['user_id' => Auth::id()]);

        return back()->with('success', 'Zdjęcie zostało dodane do opinii.');
    }

    private function storeImage($file): Image
    {
        $path = $file->store('review_images', 'public');

        return Image::create([
            'file_name' => $path,
            'original_file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
        ]);
    }
}
