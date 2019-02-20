<?php

namespace App\Http\Controllers;

use Laravel\Passport\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientsController extends Controller
{
    protected function validator(array $data)
    {
        $rules = [
            'client_name' => 'required|string',
            'client_uri' => 'url',
            'client_description' => 'string',
            'logo_uri' => 'url', 
            'redirect_uris' => 'required|array',
            'redirect_uris.*' => 'url',
            'token_endpoint_auth_method' => [
                'required',
                Rule::in(['none', 'client_secret_post', 'client_secret_basic']),
            ],
            'grant_types' => 'array',
            'grant_types.*' => [
                Rule::in([
                    'authorization_code',
                    'implicit',
                    'password',
                    'client_credentials',
                    'refresh_token',
                    'urn:...',
                    'urn:...',
                ]),
            ],
            'response_types' => 'array',
            'response_types.*' => Rule::in(['code','token']),
        ];

        return Validator::make($data, $rules);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $client = (new ClientRepository)->create(
            NULL, 
            $request->client_name, 
            $request->redirect_uris[0]
        );

        $responseData = [
            'client_id' => $client->id,
            'client_name' => $client->name,
            'redirect_uris' => [ $client->redirect ],
            'client_secret' => $client->secret,
            'client_secret_expires_at' => 0,
            'token_endpoint_auth_method' => 'client_secret_basic',
            'grant_types' => ['authorization_code'],
            'response_type' => ['code'],
        ];

        return response()->json($responseData)->setStatusCode(201);
    }
}
