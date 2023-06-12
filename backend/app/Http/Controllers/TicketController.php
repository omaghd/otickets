<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tickets\StoreTicketRequest;
use App\Http\Requests\Tickets\UpdateTicketRequest;
use App\Services\TicketService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    public function __construct(protected TicketService $service)
    {
    }

    public function index(): JsonResponse
    {
        $tickets = $this->service->getAllTickets();

        return $this->successResponse(data: $tickets);
    }

    public function countByStatus(): JsonResponse
    {
        return $this->successResponse(data: $this->service->countByStatus());
    }

    public function getStats(): JsonResponse
    {
        return $this->successResponse(data: $this->service->getStats());
    }

    public function show($reference): JsonResponse
    {
        try {
            $ticket = $this->service->findByReference($reference);

            $this->authorize('view', $ticket);

            $ticket = $this->service->getTicketByReference($reference);

            return $this->successResponse(data: $ticket);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This ticket does not exist', 404);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $agentId = $request->user('sanctum')->isManager() && $request->agent_id
            ? $request->agent_id
            : null;

        $ticket = $this->service->createTicket(
            $request->safe()->except(['attachments', 'agent_id']),
            $agentId
        );

        if ($request->hasFile('attachments')) {
            $this->service->storeAttachments($ticket, $request->file('attachments'));
        }

        return $this->successResponse('Ticket created successfully', $ticket);
    }

    public function update(UpdateTicketRequest $request, $id): JsonResponse
    {
        try {
            $ticket = $this->service->find($id);

            $this->authorize('update', $ticket);

            $ticket = $this->service->update($ticket, $request->validated());

            return $this->successResponse('Ticket updated successfully', $ticket);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This ticket does not exist', 404);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $ticket = $this->service->find($id);

            $this->authorize('delete', $ticket);

            $this->service->delete($ticket);

            return $this->successResponse('Ticket deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This ticket does not exist', 404);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }

    public function forceDelete($id): JsonResponse
    {
        try {
            $ticket = $this->service->findInTrash($id);

            $this->authorize('forceDelete', $ticket);

            $this->service->forceDelete($ticket);

            return $this->successResponse('Ticket permanently deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This ticket does not exist', 404);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $ticket = $this->service->findInTrash($id);

            $this->authorize('restore', $ticket);

            $this->service->restore($ticket);

            return $this->successResponse('Ticket restored successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This ticket does not exist', 404);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }
}
