<?php

namespace Database\Seeders;

use App\Models\CannedResponse;
use App\Models\Category;
use App\Models\Department;
use App\Models\Faq;
use App\Models\ReplyAttachment;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketReply;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->admin()
            ->create([
                'name'     => 'OmaghD',
                'email'    => 'contact@omaghd.com',
                'password' => 'OTickets@00'
            ]);

        $mainAgentCreated = false;

        Department::factory()
            ->count(4)
            ->create()
            ->each(function (Department $department) use (&$mainAgentCreated) {
                $department->agents()->save(
                    User::factory()
                        ->agent()
                        ->create(['email' => $mainAgentCreated ? fake()->unique()->safeEmail() : 'agent@omaghd.com'])
                );

                $mainAgentCreated = true;

                $department->agents()->saveMany(
                    User::factory()
                        ->agent()
                        ->count(2)
                        ->create()
                );

                $department->categories()->saveMany(
                    Category::factory()
                        ->count(2)
                        ->create()
                        ->each(function (Category $category) {
                            $category->faqs()->saveMany(
                                Faq::factory()
                                    ->count(5)
                                    ->create()
                            );
                        })
                );
            });

        User::factory()
            ->client()
            ->create([
                'name'  => 'Client',
                'email' => 'client@omaghd.com'
            ]);

        $randomCategories = Category::query()
            ->inRandomOrder()
            ->limit(3)
            ->get()
            ->each(function (Category $category) {
                $category->cannedResponses()->saveMany(
                    CannedResponse::factory()
                        ->count(2)
                        ->create()
                );

                $category->tickets()->saveMany(
                    Ticket::factory()
                        ->count(2)
                        ->create()
                );
            });


        $ticketService = app(TicketService::class);

        Ticket::query()
            ->get()
            ->each(function (Ticket $ticket) use ($ticketService) {
                $ticketService->assignToAgent($ticket);

                $attachments = TicketAttachment::factory()
                    ->count(fake()->numberBetween(0, 2))
                    ->create();

                $ticket
                    ->attachments()
                    ->saveMany($attachments);

                $clientReply = TicketReply::factory()
                    ->create([
                        'ticket_id' => $ticket->id,
                        'user_id'   => $ticket->client_id
                    ]);

                $clientReplyAttachments = ReplyAttachment::factory()
                    ->count(fake()->numberBetween(0, 2))
                    ->create();

                $clientReply
                    ->attachments()
                    ->saveMany($clientReplyAttachments);

                $ticket
                    ->replies()
                    ->save($clientReply);

                if ($ticket->agents()->exists()) {
                    $agentReply = TicketReply::factory()
                        ->create([
                            'ticket_id' => $ticket->id,
                            'user_id'   => $ticket->agents->random()->id
                        ]);

                    $agentReplyAttachments = ReplyAttachment::factory()
                        ->count(fake()->numberBetween(0, 2))
                        ->create();

                    $agentReply
                        ->attachments()
                        ->saveMany($agentReplyAttachments);

                    $ticket
                        ->replies()
                        ->save($agentReply);
                }
            });
    }
}
