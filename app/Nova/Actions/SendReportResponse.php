<?php

namespace App\Nova\Actions;

use App\Http\Controllers\AdminMailerController;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class SendReportResponse extends Action
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
        foreach ($models as $model) {
            // $model->update([
            //     'status' => "approved",
            // ]);

            $reporter = User::find($model->reporter_id);
            $reporter_email = $reporter->account->email;
            if ($reporter_email == null) {
                return Action::message('Email not found' . $reporter_email);
            } else {
                (new AdminMailerController)->sendReportResponseMail($reporter_email, $fields->subject, $fields->response);
            }
        }

        return Action::message('Response sent');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        // return [];
        return [
            Text::make('Subject'),
            Text::make('Response'),
        ];
    }
}
