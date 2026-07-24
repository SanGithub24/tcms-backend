<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evidence;

class EvidenceController extends Controller
{
    public function uploadEvidence(Request $request)
    {
        if (!$request->hasFile('evidence')) {
            return response()->json([
                'message' => 'No file uploaded'
            ], 400);
        }

        $file = $request->file('evidence');

        $path = $file->store('evidences', 'public');

        $evidence = Evidence::create([
            'complaintID' => $request->complaintID,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'uploaded_time' => now(),
        ]);

        return response()->json([
            'message' => 'Evidence uploaded successfully',
            'evidence' => $evidence
        ]);
    }
}