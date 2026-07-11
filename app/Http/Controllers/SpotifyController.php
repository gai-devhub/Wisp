<?php

namespace App\Http\Controllers;

use Aerni\Spotify\Facades\Spotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SpotifyController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        if (!$query) {
            return response()->json(['albums' => [], 'tracks' => []]);
        }

        try {
            $results = Spotify::searchItems($query, 'album,track')->limit(10)->get();
            
            return response()->json([
                'albums' => $results['albums']['items'] ?? [],
                'tracks' => $results['tracks']['items'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Spotify Search Error: ' . $e->getMessage());
            return response()->json(['error' => 'Could not fetch from Spotify API', 'details' => $e->getMessage()], 500);
        }
    }

    public function albumTracks($id)
    {
        try {
            $album = Spotify::album($id)->get();
            return response()->json($album);
        } catch (\Exception $e) {
            Log::error('Spotify Album Error: ' . $e->getMessage());
            return response()->json(['error' => 'Could not fetch album details', 'details' => $e->getMessage()], 500);
        }
    }
}
