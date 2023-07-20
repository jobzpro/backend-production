<?php

namespace App\Nova;

use App\Nova\Actions\ApproveCompany;
use App\Nova\Actions\resetCompany;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Company extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Company>
     */
    public static $model = \App\Models\Company::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id','name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Select::make('Status','status')->options([
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'disabled' => 'Disabled',
            ]),
            Text::make('Name','name'),
            Text::make('Company Email','company_email'),
            Text::make('Address Line','address_line')->hideFromIndex(),
            Text::make('City','city')->hideFromIndex(),
            Text::make('State','state')->hideFromIndex(),
            Text::make('Zip Code','zip_code')->hideFromIndex(),
            Text::make('Introduction','introduction')->hideFromIndex(),
            Text::make('Services','services'),
            BelongsTo::make('Business Type','businessType'),
            BelongsTo::make('Industry', 'industry'),
            Text::make('Years of Operation', 'years_of_operation'),
            Text::make('Owner Full Name','owner_full_name'),
            Text::make('Owner Contact Number', 'owner_contact_no'),
            HasMany::make('User Company','userCompany'),
            Text::make('Referral Code', 'referral_code')->hideFromIndex(),
        ];
    }


    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            new ApproveCompany,
            new resetCompany,
        ];
    }
}
