<?php

namespace App\Http\Requests\Admin;

use Illuminate\Http\Response;
use App\DTOs\Admin\ListUsersRequestDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ListUsersRequest extends FormRequest
{
    public ListUsersRequestDTO $dto;
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
            "page" => ["nullable", "numeric"],
            "limit" => ["nullable", "numeric"],
            "sortBy" => ["nullable", "string"],
            "desc" => ["nullable", "in:true,false"],
            "first_name" => ["nullable", "string"],
            "email" => ["nullable", "email"],
            "phone" => ["nullable", "string"],
            "address" => ["nullable", "string"],
            "created_at" => ["nullable", "date"],
            "marketing" => ["nullable", "in:0,1"],
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
        $validated = (array) $this->validated();

        array_walk($validated, function (&$value, $key): void {
            if (is_numeric($value)) {
                $value = intval($value);
            }

            if (in_array($value, ["true", "false"])) {
                $value = $value === "true" ? true : false;
            }

            if ($key === "marketing") {
                $value = boolval($value);
            }
        });

        $this->dto = new ListUsersRequestDTO($validated);
    }
}
