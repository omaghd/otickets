<?php

namespace App\Jobs;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveProfilePictureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private UserRepositoryInterface $userRepository;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User         $user,
        public UploadedFile $picture
    )
    {
        $this->userRepository = app(UserRepositoryInterface::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $folder    = $this->user->isClient() ? 'clients' : 'users';
        $savePath  = "public/$folder/{$this->user->id}/profile-pictures";
        $finalPath = str_replace(
            'public',
            'storage',
            $this->picture->storePublicly($savePath)
        );

        $this->userRepository->update(
            $this->user->id,
            ['picture' => $finalPath]
        );
    }
}
