<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Http\Requests\RegisterDeviceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class DeviceController extends Controller
{
    #[OA\Post(
        path: '/api/register-device',
        summary: 'Register a device for push notifications',
        tags: ['Device'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['project_id', 'user_id', 'token', 'platform'],
                properties: [
                    new OA\Property(property: 'project_id', type: 'integer', example: 1),
                    new OA\Property(property: 'user_id', type: 'string', example: 'user_123'),
                    new OA\Property(property: 'token', type: 'string', example: 'firebase_fcm_token_here'),
                    new OA\Property(property: 'platform', type: 'string', example: 'android')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Device registered successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Device registered successfully')
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function register(RegisterDeviceRequest $request): JsonResponse
    {
        // 1. Ensure a user only has one active token per platform/project combination
        // We delete any old tokens for this specific user/project/platform combination
        // that differ from the current incoming token.
        Device::where('project_id', $request->project_id)
            ->where('user_id', $request->user_id)
            ->where('platform', $request->platform)
            ->where('token', '!=', $request->token)
            ->delete();

        // 2. Register or update the current token
        // Keyed by token to ensure we don't hit a UNIQUE constraint if this token
        // was previously assigned to a different user/project on this same physical device.
        $device = Device::updateOrCreate(
            [
                'token' => $request->token,
            ],
            [
                'project_id' => $request->project_id,
                'user_id' => $request->user_id,
                'platform' => $request->platform,
            ]
        );

        return response()->json([
            'message' => 'Device registered successfully',
            'token' => $device->token
        ]);
    }

    #[OA\Get(
        path: '/api/device-token',
        summary: 'Get or generate a device token',
        tags: ['Device'],
        parameters: [
            new OA\Parameter(name: 'project_id', in: 'query', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'user_id', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'platform', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['web', 'android', 'ios']))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token retrieved or generated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: 'backend_generated_abc123')
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function getToken(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|string|max:255',
            'platform' => 'required|string|in:web,android,ios'
        ]);

        $device = Device::where('project_id', $request->project_id)
            ->where('user_id', $request->user_id)
            ->where('platform', $request->platform)
            ->first();

        if ($device && $device->token) {
            return response()->json([
                'message' => 'Token retrieved successfully',
                'token' => $device->token
            ]);
        }

        return response()->json([
            'message' => 'Token not found for this device',
            'token' => null
        ], 404);
    }
}
