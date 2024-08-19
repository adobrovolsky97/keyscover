<?php

namespace App\Http\Requests\VisitTracker;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class VisitTrackerRequest
 */
class VisitTrackerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'url' => ['required', 'string'],
        ];
    }
}
