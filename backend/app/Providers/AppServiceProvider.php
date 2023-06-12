<?php

namespace App\Providers;

use App\Interfaces\CannedResponseRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\FaqRepositoryInterface;
use App\Interfaces\NewsletterRepositoryInterface;
use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\TicketRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\CannedResponseRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\FaqRepository;
use App\Repositories\NewsletterRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        CannedResponseRepositoryInterface::class => CannedResponseRepository::class,
        CategoryRepositoryInterface::class       => CategoryRepository::class,
        DepartmentRepositoryInterface::class     => DepartmentRepository::class,
        FaqRepositoryInterface::class            => FaqRepository::class,
        NewsletterRepositoryInterface::class     => NewsletterRepository::class,
        NotificationRepositoryInterface::class   => NotificationRepository::class,
        TicketRepositoryInterface::class         => TicketRepository::class,
        UserRepositoryInterface::class           => UserRepository::class,
    ];
}
