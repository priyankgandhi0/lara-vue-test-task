<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\SubmissionImage;
use App\Models\SubmissionFile;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        $response = ['status' => 0, 'msg' => '', 'data' => []];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:10',
            'email' => 'required|email|not_in:example@gmail.com',
            'phone' => 'required|regex:/^\+?[1-9]\d{1,14}$/',
            'message' => 'required|string|min:10',
            'street' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string',
            'country' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|mimes:jpg,jpeg|max:2048',
            'files' => 'nullable|array',
            'files.*' => 'nullable|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            $response['msg'] = $validator->errors()->first();
            return response()->json($response);
        }

        $sanitizedInput = filter_var_array($request->all(), FILTER_SANITIZE_STRING);

        try {
            $submission = Submission::create([
                'name' => $sanitizedInput['name'],
                'email' => $sanitizedInput['email'],
                'phone' => $sanitizedInput['phone'],
                'message' => $sanitizedInput['message'],
                'street' => $sanitizedInput['street'],
                'state' => $sanitizedInput['state'],
                'zip' => $sanitizedInput['zip'],
                'country' => $sanitizedInput['country'],
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('Files/images', 'public');
                    SubmissionImage::create([
                        'submission_id' => $submission->id,
                        'image' => basename($imagePath),
                    ]);
                }
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filePath = $file->store('Files/documents', 'public');
                    SubmissionFile::create([
                        'submission_id' => $submission->id,
                        'file' => basename($filePath),
                    ]);
                }
            }

            $response['status'] = 1;
            $response['msg'] = "Submission successfully saved!";
        } catch (\Exception $e) {
            $response['msg'] = 'Error: ' . $e->getMessage();
        }

        return response()->json($response);
    }
}