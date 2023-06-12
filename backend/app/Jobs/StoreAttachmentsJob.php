<?php

namespace App\Jobs;

use App\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Repositories\TicketReplyRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreAttachmentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private TicketRepositoryInterface $ticketRepository;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Ticket|TicketReply $object,
        public array              $attachments
    )
    {
        $this->ticketRepository = app(TicketRepositoryInterface::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->attachments as $attachment) {
            $fileName  = pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME);
            $path      = $this->object instanceof Ticket
                ? "{$this->object->reference}"
                : "{$this->object->ticket->reference}/replies";
            $savePath  = "public/attachments/tickets/$path";
            $finalPath = str_replace(
                'public',
                'storage',
                $attachment->storePublicly($savePath)
            );

            $this->ticketRepository->createAttachment(
                $this->object,
                ['filename' => $fileName, 'path' => $finalPath]
            );
        }
    }
}
