<?php

namespace App\Http\Requests\Admin;

use Illuminate\Http\Response;
use App\DTOs\Admin\LoginAdminRequestDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginAdminRequest extends FormRequest
{
    public LoginAdminRequestDTO $dto;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "email" => ["required", "email:rfc,dns"],
            "password" => ["required", "string", "min:6"],
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
            throw new HttpResponseException(
                response()->json(
                    $validator->errors()->all(),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $this->dto = new LoginAdminRequestDTO($this->validated());
    }
}
