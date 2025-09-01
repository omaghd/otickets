<?php

namespace App\Jobs;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SaveProfilePictureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public array $fileData
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(UserRepositoryInterface $userRepository): void
    {
        $user = $userRepository->find($this->userId);

        $contents = base64_decode($this->fileData['contents']);

        $filename = time() . '_' . $this->userId . '.' . $this->fileData['extension'];

        $folder = $user->isClient() ? 'clients' : 'users';
        $path = "public/$folder/$this->userId/profile-pictures/$filename";

        Storage::put($path, $contents);

        $publicPath = str_replace('public/', 'storage/', $path);

        if ($user->picture && Storage::exists(str_replace('storage/', 'public/', $user->picture))) {
            Storage::delete(str_replace('storage/', 'public/', $user->picture));
        }

        $userRepository->update($this->userId, ['picture' => $publicPath]);
    }
}
