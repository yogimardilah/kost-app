<?php

namespace App\Http\Controllers;

use App\Models\Consumer;
use App\Http\Requests\StoreConsumerRequest;
use App\Http\Requests\UpdateConsumerRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;

class ConsumerController extends Controller
{
    public function index()
    {
        $search = request('search');
        
        $consumers = Consumer::query()
            ->when($search, function($query) use ($search) {
                $query->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('nik', 'LIKE', "%{$search}%")
                      ->orWhere('no_hp', 'LIKE', "%{$search}%")
                      ->orWhere('kendaraan', 'LIKE', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return view('consumers.index', compact('consumers'));
    }

    public function create()
    {
        return view('consumers.create');
    }

    public function store(StoreConsumerRequest $request)
    {
        $data = $request->validated();

        $data['nik'] = trim((string) ($data['nik'] ?? ''));

        // Lock per NIK to avoid race condition when the submit request is sent twice.
        $lock = Cache::lock('consumer:create:nik:' . md5($data['nik']), 10);

        try {
            $lock->block(3);

            $existing = Consumer::where('nik', $data['nik'])->first();
            if ($existing) {
                if ($this->isSameConsumerPayload($existing, $data) && $existing->created_at && $existing->created_at->gt(now()->subMinutes(2))) {
                    return redirect()->route('consumers.index')->with('success', 'Data penyewa sudah tersimpan. Permintaan ganda diabaikan.');
                }

                return back()->withErrors(['nik' => 'NIK sudah terdaftar'])->withInput();
            }

            if ($request->hasFile('tanda_pengenal')) {
                $data['tanda_pengenal'] = $this->compressAndSaveImage($request->file('tanda_pengenal'));
            }

            Consumer::create($data);
            return redirect()->route('consumers.index')->with('success', 'Penyewa berhasil ditambahkan');
        } catch (QueryException $e) {
            // Fallback protection if duplicate insert happens at DB layer.
            if ((string) $e->getCode() === '23000') {
                $existing = Consumer::where('nik', $data['nik'])->first();

                if ($existing && $this->isSameConsumerPayload($existing, $data)) {
                    return redirect()->route('consumers.index')->with('success', 'Data penyewa sudah tersimpan. Permintaan ganda diabaikan.');
                }

                return back()->withErrors(['nik' => 'NIK sudah terdaftar'])->withInput();
            }

            throw $e;
        } finally {
            optional($lock)->release();
        }
    }

    public function edit(Consumer $consumer)
    {
        return view('consumers.edit', compact('consumer'));
    }

    public function update(UpdateConsumerRequest $request, Consumer $consumer)
    {
        $data = $request->validated();
        
        if ($request->hasFile('tanda_pengenal')) {
            // Delete old file if exists
            if ($consumer->tanda_pengenal && Storage::disk('public')->exists($consumer->tanda_pengenal)) {
                Storage::disk('public')->delete($consumer->tanda_pengenal);
            }
            $data['tanda_pengenal'] = $this->compressAndSaveImage($request->file('tanda_pengenal'));
        }
        
        $consumer->update($data);
        return redirect()->route('consumers.index')->with('success', 'Penyewa berhasil diperbarui');
    }

    public function destroy(Consumer $consumer)
    {
        // Delete file if exists
        if ($consumer->tanda_pengenal && Storage::disk('public')->exists($consumer->tanda_pengenal)) {
            Storage::disk('public')->delete($consumer->tanda_pengenal);
        }
        
        $consumer->delete();
        return redirect()->route('consumers.index')->with('success', 'Penyewa berhasil dihapus');
    }

    /**
     * Compress and save uploaded image
     */
    private function compressAndSaveImage($file, $directory = 'tanda_pengenal')
    {
        try {
            // Check if file is an image
            $mimeType = $file->getMimeType();
            
            if (!str_starts_with($mimeType, 'image/')) {
                // If PDF or other file, just store normally
                return $file->store($directory, 'public');
            }

            // For non-JPEG/PNG, store normally (GIF, WebP might have issues)
            if (!in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png'])) {
                return $file->store($directory, 'public');
            }

            // Increase memory limit temporarily
            $oldMemoryLimit = ini_get('memory_limit');
            ini_set('memory_limit', '256M');

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.jpg';
            $path = $directory . '/' . $filename;
            $fullPath = storage_path('app/public/' . $path);

            // Create directory if not exists
            $dir = dirname($fullPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            // Create image resource based on type
            if ($mimeType === 'image/png') {
                $image = @imagecreatefrompng($file->getRealPath());
            } else {
                $image = @imagecreatefromjpeg($file->getRealPath());
            }

            if (!$image) {
                ini_set('memory_limit', $oldMemoryLimit);
                return $file->store($directory, 'public');
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Only resize if width is larger than 1200px
            if ($width <= 1200) {
                // No need to resize, just save with compression
                $success = @imagejpeg($image, $fullPath, 80);
                imagedestroy($image);
                ini_set('memory_limit', $oldMemoryLimit);
                
                return $success ? $path : $file->store($directory, 'public');
            }

            // Calculate new dimensions
            $ratio = 1200 / $width;
            $newWidth = 1200;
            $newHeight = (int)($height * $ratio);

            // Create new image
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            if (!$newImage) {
                imagedestroy($image);
                ini_set('memory_limit', $oldMemoryLimit);
                return $file->store($directory, 'public');
            }

            // White background for PNG transparency
            if ($mimeType === 'image/png') {
                $white = imagecolorallocate($newImage, 255, 255, 255);
                imagefill($newImage, 0, 0, $white);
            }

            // Resize
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Save
            $success = @imagejpeg($newImage, $fullPath, 80);

            // Cleanup
            imagedestroy($image);
            imagedestroy($newImage);
            ini_set('memory_limit', $oldMemoryLimit);

            return $success ? $path : $file->store($directory, 'public');

        } catch (\Exception $e) {
            Log::error('Image compression failed: ' . $e->getMessage());
            return $file->store($directory, 'public');
        }
    }

    /**
     * Compare important fields to detect duplicate submit of same payload.
     */
    private function isSameConsumerPayload(Consumer $consumer, array $data): bool
    {
        $normalize = function ($value) {
            return trim((string) ($value ?? ''));
        };

        return $normalize($consumer->nik) === $normalize($data['nik'] ?? '')
            && strcasecmp($normalize($consumer->nama), $normalize($data['nama'] ?? '')) === 0
            && $normalize($consumer->no_hp) === $normalize($data['no_hp'] ?? '')
            && strcasecmp($normalize($consumer->kendaraan), $normalize($data['kendaraan'] ?? '')) === 0;
    }
}
