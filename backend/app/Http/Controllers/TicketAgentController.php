<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tickets\TicketAgentsRequest;
use App\Services\TicketService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketAgentController extends Controller
{
    public function __construct(protected TicketService $service)
    {
    }

    public function __invoke(TicketAgentsRequest $request)
    {
        try {
            $ticket = $this->service->find($request->ticket_id);

            if ($request->transfer_to == 'agent') {
                $this->authorize('assignAgent', $ticket);

                $agentId = $request->agent_id;

                $message = 'Ticket assigned to agent successfully';
            } else {
                $this->authorize('assignToMe', $ticket);

                $agentId = $request->user('sanctum')->id;

                $message = 'Ticket assigned to you successfully';
            }

            $currentAgent = $ticket->currentAgent();

            if ($currentAgent && $currentAgent->id == $agentId) {
                return $this->errorResponse('This agent is already the current agent', 422);
            }

            if ($currentAgent) {
                $this->service->unassignAgent($ticket, $currentAgent->id);
            }

            $this->service->assignAgent($ticket, $agentId);

            $this->service->notifyAssignedAgent($ticket, $agentId);

            return $this->successResponse($message);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This ticket does not exist', 404);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }
}
