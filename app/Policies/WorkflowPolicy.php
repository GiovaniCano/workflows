<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workflow;
use Illuminate\Auth\Access\Response;

class WorkflowPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Workflow $workflow): bool
    {
        $this->ensureIsWorkflow($workflow);
        return $workflow->user_id = $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Workflow $workflow): bool
    {
        $this->ensureIsWorkflow($workflow);
        return $workflow->user_id = $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Workflow $workflow): bool
    {
        $this->ensureIsWorkflow($workflow);
        // return $workflow->user_id = $user->id;
    }


    /**
     * Ensure that the requested workflow is actually a workflow and not a normal section
     * @param \App\Models\Workflow $workflow
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function ensureIsWorkflow(Workflow $workflow): void
    {
        if($workflow->type !== 0) abort(500, 'Only type 0 is allowed for workflows');
    }
}
