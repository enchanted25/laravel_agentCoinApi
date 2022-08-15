<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class allowedSender implements Rule
{

    private const ALLOWED_SENDER = ['agent', 'masteragent'];
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, self::ALLOWED_SENDER);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Sender must be an agent or masteragent";
    }
}
