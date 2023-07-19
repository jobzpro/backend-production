<?php

namespace App\Nova\Actions;

use App\Models\UserCompany;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Http\Controllers\AdminMailerController as AdminMailerController;
use Carbon\Carbon;

class ApproveCompany extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach($models as $model){
            $model->update([
                'status' => "approved",
            ]);
            
            $usersCompany = UserCompany::where('company_id', $model->id)->pluck('user_id');

            $employees = User::whereIn('id', $usersCompany)->get();

            foreach($employees as $employee){
                $employee->account()->update([
                    'email_verified_at' => Carbon::now(),
                ]);

                $employee_email = $employee->account->email;
                (new AdminMailerController)->sendApprovalMail($employee_email);
            }

        }

        return Action::message('The company was successfully updated.');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
