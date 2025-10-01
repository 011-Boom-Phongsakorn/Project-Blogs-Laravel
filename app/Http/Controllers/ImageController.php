<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:10240', // 10MB max
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'
            ],
        ]);

        try {
            $image = $request->file('image');

            // Generate unique filename
            $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();

            // Initialize ImageManager with GD driver
            $manager = new ImageManager(new Driver());

            // Create different sizes
            $originalImage = $manager->read($image->getRealPath());

            // Original size (max 1920px width)
            if ($originalImage->width() > 1920) {
                $originalImage->scaleDown(width: 1920);
            }

            // Save original
            $originalPath = 'uploads/images/' . $filename;
            Storage::disk('public')->put($originalPath, $originalImage->encode());

            // Create thumbnail (400px width)
            $thumbnail = $manager->read($image->getRealPath())->scaleDown(width: 400);

            $thumbnailFilename = 'thumb_' . $filename;
            $thumbnailPath = 'uploads/images/thumbnails/' . $thumbnailFilename;
            Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'url' => Storage::url($originalPath),
                'thumbnail_url' => Storage::url($thumbnailPath),
                'filename' => $filename,
                'path' => $originalPath,
                'thumbnail_path' => $thumbnailPath,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120', // 5MB max
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
        ]);

        try {
            $image = $request->file('image');

            // Generate unique filename
            $filename = 'avatar_' . auth()->id() . '_' . time() . '.' . $image->getClientOriginalExtension();

            // Initialize ImageManager with GD driver
            $manager = new ImageManager(new Driver());

            // Create square avatar (300x300)
            $avatar = $manager->read($image->getRealPath())
                ->cover(300, 300);

            $avatarPath = 'uploads/avatars/' . $filename;
            Storage::disk('public')->put($avatarPath, $avatar->encode());

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'url' => Storage::url($avatarPath),
                'path' => $avatarPath,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Avatar upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            $path = $request->input('path');

            // Security check - only allow deletion of files in uploads directory
            if (!str_starts_with($path, 'uploads/')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file path',
                ], 403);
            }

            // Delete original and thumbnail if exists
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);

                // Try to delete thumbnail
                $thumbnailPath = str_replace('uploads/images/', 'uploads/images/thumbnails/thumb_', $path);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image deletion failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
