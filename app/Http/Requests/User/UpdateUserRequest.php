<?php

namespace App\Http\Requests\User;

use App\DTOs\User\UpdateUserRequestDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => ["required", "string"],
            'last_name' => ["required", "string"],
            'email' => ["required", "email:rfc,dns","unique:users"],
            'password' => ["required", "string", "min:6", "confirmed"],
            'avatar' => ["required", "string"],
            'address' => ["required", "string"],
            'phone_number' => ["required", "string"],
            "marketing" => ["nullable", "boolean"]
        ];
    }

        /**
     * Handle a failed validation attempt.
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }



    /**
     * Handle a passed validation attempt.
     *
     */
    protected function passedValidation(): void
    {
        $this->dto = new UpdateUserRequestDTO($this->validated());
    }
}
