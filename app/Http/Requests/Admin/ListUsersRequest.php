<?php

namespace App\Http\Requests\Admin;

use App\DTOs\Admin\ListUsersRequestDTO;
use App\DTOs\Admin\LoginAdminRequestDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ListUsersRequest extends FormRequest
{
    public ListUsersRequestDTO $dto;
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
            throw new HttpResponseException(response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }


    /**
     * Handle a passed validation attempt.
     *
     */
    protected function passedValidation(): void
    {
        $validated = (array)$this->validated();

        array_walk($validated, function (&$value) {
            if (is_numeric($value)) {
                $value = intval($value);
            }

            if (in_array($value, ["true", "false"])) {
                $value = $value === "true" ? true : false;
            }
        });

        $this->dto = new ListUsersRequestDTO($validated);
    }
}
