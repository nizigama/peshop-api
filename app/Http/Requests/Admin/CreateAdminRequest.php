<?php

namespace App\Http\Requests\Admin;

use Illuminate\Http\Response;
use App\DTOs\Admin\CreateAdminRequestDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateAdminRequest extends FormRequest
{
    public CreateAdminRequestDTO $dto;
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
            'first_name' => ["required", "string"],
            'last_name' => ["required", "string"],
            'email' => ["required", "email:rfc,dns", "unique:users"],
            'password' => ["required", "string", "min:6", "confirmed"],
            'avatar' => ["required", "string"],
            'address' => ["required", "string"],
            'phone_number' => ["required", "string"],
            "marketing" => ["nullable", "boolean"],
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
        $this->dto = new CreateAdminRequestDTO($this->validated());
    }
}
