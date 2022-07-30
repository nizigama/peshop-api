<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\ListUsersRequest;
use App\Http\Requests\Admin\LoginAdminRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\AdminUserService;
use App\Services\NormalUserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function __construct(protected AdminUserService $adminUserService, protected NormalUserService $normalUserService)
    {
        $this->middleware('protector:admin')->except(['login', 'store']);
    }

    /**
     * @OA\Post(
     * path="/api/v1/admin/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="adminLogin",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="password"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Missing request data or invalid request data",
     *      @OA\JsonContent(
     *       type="string", example={"The email must be a valid email address.", "The password must be at least 6 characters."}
     *    )
     *     ),
     * @OA\Response(
     *    response=404,
     *    description="User with provided email was not found",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User not found"),
     *    )
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Login successful",
     * @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Login successful"),
     *       @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJiYXNlNjQ6TXVQak8yRWtxVllkTk1hbWRGckdkT0ZvV2szNkkxTm5wTnd4K0NJUHExND0iLCJqdGkiOiI0N2E4MjMyYi0zMmJhLTQ0ZWItODMyNS00YjFhYWI0MDRkMjQiLCJhdWQiOiJodHRwOi8vcGV0c2hvcC1hcGkudGVzdCIsImlhdCI6MTY1OTIwMjkzMS4yNDAyMzgsImV4cCI6MTY1OTI4OTMzMS4yNDAyODksInVpZCI6ImMzYzAwNjRkLWY3NDktMzZhZi05ZDI4LTk3OGJiY2I0MGNjYyJ9.iJniVe4eyPcMoYWpZ6gsEnH3-xUTVPdcD8jfo2olHzeaXSQMwctMqCs0UZ0xTHWNHLmt_aP3CE5RqNXylWXwOBEe-aQL0mWrpsOVqlfC93kYGHIcEx1Kch121gmrzqhr_hH-C2CAISxUKHBAK3o9Vfmm3o2V6dxmIQEBZKgANqc"),
     *    )
     *     ),
     * @OA\Response(
     *    response=405,
     *    description="Wrong password",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Wrong password"),
     *    )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal server error",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Internal server error"),
     *    )
     *     )
     * )
     */
    public function login(LoginAdminRequest $request)
    {
        try {
            $token = $this->adminUserService->loginAdminUser($request->dto);

            return response()->json(["message" => "Login successful", "token" => $token]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], $e->getCode());
        }
    }


    /**
     *  @OA\Get(
     * path="/api/v1/admin/logout",
     * summary="Logout",
     * description="Logout admin user",
     * operationId="adminLogout",
     * tags={"auth"},
     * security={ {"sanctum": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Logged out successfully")
     *        )
     *     ),
     * @OA\Response(
     *    response=401,
     *    description="Invalid auth token",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated"),
     *    )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal server error",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Internal server error"),
     *    )
     *     )
     * )
     * )
     */
    public function logout()
    {
        try {
            $this->adminUserService->logoutUser(Auth::user());

            return response()->json(["message" => "Logged out successfully"]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/admin/user-listing",
     * summary="List normal users",
     * description="List all normal users",
     * operationId="adminUserList",
     * tags={"users"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="Sort by, allowed values: id,uuid,first_name,last_name,email,avatar,address,phone_number,created_at,last_login_at",
     *    in="query",
     *    name="sortBy",
     *    required=false,
     *    example="uuid",
     *    @OA\Schema(
     *       type="string",
     *    )
     * ),
     * @OA\Parameter(
     *    description="limit of items per page",
     *    in="query",
     *    name="limit",
     *    required=false,
     *    example="10",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     * @OA\Parameter(
     *    description="the page to fetch",
     *    in="query",
     *    name="page",
     *    required=false,
     *    example="3",
     *    @OA\Schema(
     *       type="integer"
     *    )
     * ),
     * @OA\Parameter(
     *    description="the order of sort by",
     *    in="query",
     *    name="desc",
     *    required=false,
     *    example="true",
     *    @OA\Schema(
     *       type="boolean",
     *    )
     * ),
     * @OA\Parameter(
     *    description="the first name of the user",
     *    in="query",
     *    name="first_name",
     *    required=false,
     *    example="user",
     *    @OA\Schema(
     *       type="string",
     *    )
     * ),
     * @OA\Parameter(
     *    description="the email of the user",
     *    in="query",
     *    name="email",
     *    required=false,
     *    example="admin@gmail.com",
     *    @OA\Schema(
     *       type="string",
     *       format="email"
     *    )
     * ),
     * @OA\Parameter(
     *    description="the phone number of the user",
     *    in="query",
     *    name="phone",
     *    required=false,
     *    example="1-435-549860-324",
     *    @OA\Schema(
     *       type="string"
     *    )
     * ),
     * @OA\Parameter(
     *    description="the address of the user",
     *    in="query",
     *    name="address",
     *    required=false,
     *    example="KG 934 ST",
     *    @OA\Schema(
     *       type="string"
     *    )
     * ),
     * @OA\Parameter(
     *    description="the date of creation of the user",
     *    in="query",
     *    name="created_at",
     *    required=false,
     *    example="2022-01-01",
     *    @OA\Schema(
     *       type="string"
     *    )
     * ),
     * @OA\Parameter(
     *    description="if the user belongs to the marketing department,allowed: 1,0",
     *    in="query",
     *    name="marketing",
     *    required=false,
     *    example="1",
     *    @OA\Schema(
     *       type="integer"
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       type="string", example={
     *  "current_page": 1,
     *  "data": {
     *    {
     *      "uuid": "e1a06c06-608d-31cf-9d65-05b77b505346",
     *      "first_name": "Brooks",
     *      "last_name": "Schimmel",
     *      "email": "yokon@example.org",
     *      "avatar": "f6c814cb-9bad-3940-91f8-50116522a85f",
     *      "address": "4289 Shields Square Suite 863\nEast Hermann, WA 86507-9551",
     *      "phone_number": "(408) 606-3850",
     *      "created_at": "2022-07-29 04:17:14",
     *      "last_login_at": "1988-09-22 01:47:31"
     *    },
     *    {
     *      "uuid": "c511b32e-1894-3a57-9026-6078233c63db",
     *      "first_name": "Adah",
     *      "last_name": "Heathcote",
     *      "email": "wklocko@example.net",
     *      "avatar": "f91abcf9-c3e5-38ea-ae48-bcc6fb60c3cb",
     *      "address": "7275 Rutherford Walks\nNolanstad, GA 32563",
     *      "phone_number": "+1.743.437.4465",
     *      "created_at": "2022-07-29 04:17:17",
     *      "last_login_at": "1999-09-12 19:45:19"
     *    },
     *    {
     *      "uuid": "987ff177-c281-31e3-8499-6faef818db7e",
     *      "first_name": "Jamir",
     *      "last_name": "Donnelly",
     *      "email": "vonrueden.novella@example.com",
     *      "avatar": "819c836a-15a8-3f53-ba20-4fe9f9a0598e",
     *      "address": "4498 Bartoletti Fall\nGaylordmouth, DC 81414",
     *      "phone_number": "+1-856-306-1238",
     *      "created_at": "2022-07-29 04:17:15",
     *      "last_login_at": "2020-07-20 02:22:39"
     *    },
     *    {
     *      "uuid": "7527a02a-5598-3549-971e-e9e28f752024",
     *      "first_name": "Lee",
     *      "last_name": "Orn",
     *      "email": "vlangosh@example.net",
     *      "avatar": "75238d3d-4bd4-3060-8eb9-7ac62a3a05b5",
     *      "address": "31640 Emmerich Forge\nWiltonbury, NJ 56237",
     *      "phone_number": "+16366747190",
     *      "created_at": "2022-07-29 04:17:17",
     *      "last_login_at": "2013-04-13 00:43:56"
     *    },
     *    {
     *      "uuid": "22a9c9c6-1234-3a10-95d9-415e5841d870",
     *      "first_name": "Kayleigh",
     *      "last_name": "Brekke",
     *      "email": "viva79@example.org",
     *      "avatar": "717bbc1a-e1f1-316f-abde-952ab84157d5",
     *      "address": "8448 Langosh Shore Apt. 343\nEmilianomouth, ID 95001-4209",
     *      "phone_number": "+1-469-809-1505",
     *      "created_at": "2022-07-29 04:17:17",
     *      "last_login_at": "2016-07-06 10:43:37"
     *    },
     *    {
     *      "uuid": "888e437a-fda1-39f0-b03b-951350c9fe21",
     *      "first_name": "Waino",
     *      "last_name": "Cremin",
     *      "email": "vbaumbach@example.com",
     *      "avatar": "0859bdc3-42ee-3e14-ad04-bdeaf436099c",
     *      "address": "409 Gulgowski Forges\nNew Stephanie, TN 77871",
     *      "phone_number": "+1.303.651.0903",
     *      "created_at": "2022-07-29 04:17:13",
     *      "last_login_at": "2006-01-04 15:07:32"
     *    },
     *    {
     *      "uuid": "af667918-42fa-3f39-9b47-350b9287e361",
     *      "first_name": "Kameron",
     *      "last_name": "Padberg",
     *      "email": "urodriguez@example.org",
     *      "avatar": "261bc88f-1652-37b5-ae4b-98c2de75525c",
     *      "address": "885 Casandra Falls Apt. 478\nSchmittbury, MT 51743-4081",
     *      "phone_number": "848.730.5305",
     *      "created_at": "2022-07-29 04:17:15",
     *      "last_login_at": "2015-06-14 17:24:42"
     *    },
     *    {
     *      "uuid": "4665e46b-48e6-30d5-876f-0ffe27b960ed",
     *      "first_name": "Cornell",
     *      "last_name": "Morar",
     *      "email": "unique.cartwright@example.com",
     *      "avatar": "ca6c7d89-d6b3-3596-99a7-b08db61c4e5c",
     *      "address": "8891 Jones Crescent\nRatkestad, LA 06590",
     *      "phone_number": "650.446.7426",
     *      "created_at": "2022-07-29 04:17:17",
     *      "last_login_at": "2001-07-04 00:53:36"
     *    },
     *    {
     *      "uuid": "afe00262-c781-3a1d-bc2f-993f3930cd56",
     *      "first_name": "Eugene",
     *      "last_name": "Ebert",
     *      "email": "teagan.daniel@example.net",
     *      "avatar": "489fc7f9-2bef-368f-b024-1e64c5186580",
     *      "address": "485 Watsica Motorway Apt. 753\nRosenbaumberg, HI 96339",
     *      "phone_number": "813-726-1554",
     *      "created_at": "2022-07-29 04:17:16",
     *      "last_login_at": "2018-08-30 16:26:50"
     *    },
     *    {
     *      "uuid": "1756d814-44d0-3a22-8cb3-b555ef635070",
     *      "first_name": "Earl",
     *      "last_name": "Brown",
     *      "email": "sienna80@example.com",
     *      "avatar": "d2ca5cfe-1562-309a-82e7-061ecd1e4fbd",
     *      "address": "94328 Kulas Route\nSouth Sibylport, MN 87987-2261",
     *      "phone_number": "+1.423.987.3952",
     *      "created_at": "2022-07-29 04:17:14",
     *      "last_login_at": "2003-12-17 17:36:26"
     *    }
     *},
     *  "first_page_url": "http://localhost/api/v1/admin/user-listing?page=1",
     *  "from": 1,
     *  "last_page": 6,
     *  "last_page_url": "http://localhost/api/v1/admin/user-listing?page=6",
     *  "links": {
     *    {
     *      "url": null,
     *      "label": "&laquo; Previous",
     *      "active": false
     *    },
     *    {
     *      "url": "http://localhost/api/v1/admin/user-listing?page=1",
     *      "label": "1",
     *      "active": true
     *    },
     *    {
     *      "url": "http://localhost/api/v1/admin/user-listing?page=2",
     *      "label": "2",
     *      "active": false
     *    },
     *    {
     *      "url": "http://localhost/api/v1/admin/user-listing?page=3",
     *      "label": "3",
     *      "active": false
     *    },
     *    {
     *      "url": "http://localhost/api/v1/admin/user-listing?page=4",
     *      "label": "4",
     *      "active": false
     *    },
     *    {
     *      "url": "http://localhost/api/v1/admin/user-listing?page=5",
     *      "label": "5",
     *      "active": false
     *    },
     *    {
     *      "url": "http://localhost/api/v1/admin/user-listing?page=6",
     *      "label": "6",
     *      "active": false
     *    },
     *    {
     *      "url": "http://localhost/api/v1/admin/user-listing?page=2",
     *      "label": "Next &raquo;",
     *      "active": false
     *    }
     *},
     *  "next_page_url": "http://localhost/api/v1/admin/user-listing?page=2",
     *  "path": "http://localhost/api/v1/admin/user-listing",
     *  "per_page": 10,
     *  "prev_page_url": null,
     *  "to": 10,
     *  "total": 52
     *}
     *        )
     *     ),
     * @OA\Response(
     *    response=422,
     *    description="Invalid request data",
     *      @OA\JsonContent(
     *       type="string", example={"The desc field must be true or false.","..."},
     *    )
     *     ),
     * @OA\Response(
     *    response=403,
     *    description="Invalid sort by value",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You are not allowed to sort by that value"),
     *    )
     *     ),
     * @OA\Response(
     *    response=401,
     *    description="Invalid auth token",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated"),
     *    )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal server error",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Internal server error"),
     *    )
     *     )
     * )
     */
    public function listUsers(ListUsersRequest $request)
    {
        try {
            $users = $this->adminUserService->listNormalUsers($request->dto)->paginate($request->dto->limit, [
                'uuid',
                'first_name',
                'last_name',
                'email',
                'avatar',
                'address',
                'phone_number',
                'created_at',
                'last_login_at'
            ], 'page', $request->dto->page);

            return response()->json($users);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @OA\Post(
     * path="/api/v1/admin/create",
     * summary="Create new admin user",
     * description="Create admin user",
     * operationId="adminCreate",
     * tags={"admin"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user details",
     *    @OA\JsonContent(
     *       required={"first_name","last_name","email","password","password_confirmation","avatar","address","phone_number"},
     *       @OA\Property(property="first_name", type="string", example="first name"),
     *       @OA\Property(property="last_name", type="string", example="last name"),
     *       @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="password"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
     *       @OA\Property(property="avatar", type="string", example="53a0026f-c1f5-4b9a-a0cb-c63d9d027ee3"),
     *       @OA\Property(property="address", type="string", example="KG 123 ST"),
     *       @OA\Property(property="phone_number", type="string", example="1-2343-6590-5456"),
     *       @OA\Property(property="marketing", type="boolean", example="true"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Missing request data or invalid request data",
     *      @OA\JsonContent(
     *       type="string", example={"The email must be a valid email address.", "The password must be at least 6 characters."}
     *    )
     *     ),
     * @OA\Response(
     *    response=404,
     *    description="Avatar not found",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Avatar not found"),
     *    )
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Admin user created successfully",
     * @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Admin user created successfully"),
     *    )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal server error",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Internal server error"),
     *    )
     *     )
     * )
     */
    public function store(CreateAdminRequest $request)
    {
        try {
            $this->adminUserService->createAdminUser($request->dto);

            return response()->json(["message" => "Admin user created successfully"]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], $e->getCode());
        }
    }


    /**
     * @OA\Put(
     * path="/api/v1/admin/user-edit/{uuid}",
     * summary="Update normal user",
     * description="Update a normal user",
     * operationId="UserUpdate",
     * tags={"users"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user details",
     *    @OA\JsonContent(
     *       required={"first_name","last_name","email","password","password_confirmation","avatar","address","phone_number"},
     *       @OA\Property(property="first_name", type="string", example="first name"),
     *       @OA\Property(property="last_name", type="string", example="last name"),
     *       @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="password"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
     *       @OA\Property(property="avatar", type="string", example="53a0026f-c1f5-4b9a-a0cb-c63d9d027ee3"),
     *       @OA\Property(property="address", type="string", example="KG 123 ST"),
     *       @OA\Property(property="phone_number", type="string", example="1-2343-6590-5456"),
     *       @OA\Property(property="marketing", type="boolean", example="true"),
     *    ),
     * ),
     *     @OA\Parameter(
     *    description="the user uuid",
     *    in="path",
     *    name="uuid",
     *    required=true,
     *    example="c3c0064d-f749-36af-9d28-978bbcb40ccc",
     *    @OA\Schema(
     *       type="string"
     *    )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Missing request data or invalid request data",
     *      @OA\JsonContent(
     *       type="string", example={"The email must be a valid email address.", "The password must be at least 6 characters."}
     *    )
     *     ),
     * @OA\Response(
     *    response=401,
     *    description="Not logged in",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized"),
     *    )
     *     ),
     * @OA\Response(
     *    response=403,
     *    description="Admin users can't be edited",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Admin users can't be edited"),
     *    )
     *     ),
     * @OA\Response(
     *    response=404,
     *    description="User?Avatar not found",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User/Avatar not found"),
     *    )
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="User updated successfully",
     * @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Admin user created successfully"),
     *    )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal server error",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Internal server error"),
     *    )
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, string $uuid)
    {
        try {
            $this->normalUserService->updateUser($request->dto, $uuid);

            return response()->json(["message" => "Normal user updated successfully"]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], $e->getCode());
        }
    }

/**
     * @OA\Delete(
     * path="/api/v1/admin/user-delete/{uuid}",
     * summary="Delete normal user",
     * description="Delete a normal user",
     * operationId="UserDelete",
     * tags={"users"},
     * security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *    description="the user uuid",
     *    in="path",
     *    name="uuid",
     *    required=true,
     *    example="c3c0064d-f749-36af-9d28-978bbcb40ccc",
     *    @OA\Schema(
     *       type="string"
     *    )
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Not logged in",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized"),
     *    )
     *     ),
     * @OA\Response(
     *    response=403,
     *    description="Admin users can't be deleted",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Admin users can't be edited"),
     *    )
     *     ),
     * @OA\Response(
     *    response=404,
     *    description="User not found",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User not found"),
     *    )
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="User deleted successfully",
     * @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Admin user deleted successfully"),
     *    )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal server error",
     *      @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Internal server error"),
     *    )
     *     )
     * )
     */
    public function destroy(string $uuid)
    {
        try {
            $this->normalUserService->deleteUser($uuid);

            return response()->json(["message" => "Normal user deleted successfully"]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], $e->getCode());
        }
    }
}
