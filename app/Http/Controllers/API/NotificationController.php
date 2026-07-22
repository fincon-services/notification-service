<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationTarget;
use App\Http\Requests\SendNotificationRequest;
use App\Jobs\SendFirebaseNotificationJob;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class NotificationController extends Controller
{
    #[OA\Post(
        path: '/api/send-notification',
        summary: 'Send a notification to specific users',
        tags: ['Notification'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['project_id', 'title', 'body', 'user_ids'],
                properties: [
                    new OA\Property(property: 'project_id', type: 'integer', example: 1),
                    new OA\Property(property: 'title', type: 'string', example: 'New Message'),
                    new OA\Property(property: 'body', type: 'string', example: 'You have received a new message.'),
                    new OA\Property(
                        property: 'user_ids',
                        type: 'array',
                        items: new OA\Items(type: 'string', example: 'user_123')
                    ),
                    new OA\Property(
                        property: 'data',
                        type: 'object',
                        additionalProperties: true,
                        example: ['url' => '/messages/1']
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Notification queued successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Notification queued successfully')
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function send(SendNotificationRequest $request): JsonResponse
    {
        $notification = Notification::create([
            'project_id' => $request->project_id,
            'title' => $request->title,
            'body' => $request->body,
            'data' => $request->data,
        ]);

        $targets = [];
        foreach ($request->user_ids as $userId) {
            $targets[] = [
                'notification_id' => $notification->id,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        NotificationTarget::insert($targets);

        // Queue the job to send push notifications via Firebase
        SendFirebaseNotificationJob::dispatch($notification, $request->user_ids);

        return response()->json(['message' => 'Notification queued successfully']);
    }

    #[OA\Get(
        path: '/api/notifications',
        summary: 'Get all notifications for a user in a project',
        tags: ['Notification'],
        parameters: [
            new OA\Parameter(name: 'project_id', in: 'query', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'user_id', in: 'query', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of notifications',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'title', type: 'string'),
                            new OA\Property(property: 'body', type: 'string'),
                            new OA\Property(property: 'data', type: 'object', nullable: true),
                            new OA\Property(property: 'is_read', type: 'boolean'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
                        ]
                    )
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|string',
        ]);

        $notifications = Notification::where('project_id', $request->project_id)
            ->whereHas('targets', function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            })
            ->with(['targets' => function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            }])
            ->latest()
            ->get()
            ->map(function ($notification) {
                $target = $notification->targets->first();
                return [
                    'id' => $target->id, // returning target id so we can mark it as read easily
                    'notification_id' => $notification->id,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'data' => $notification->data,
                    'is_read' => $target->is_read,
                    'created_at' => $notification->created_at,
                ];
            });

        return response()->json($notifications);
    }

    #[OA\Post(
        path: '/api/mark-read/{id}',
        summary: 'Mark a notification as read',
        tags: ['Notification'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Notification marked as read',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Notification marked as read')
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Notification not found')
        ]
    )]
    public function markAsRead($id): JsonResponse
    {
        $target = NotificationTarget::findOrFail($id);
        
        if (!$target->is_read) {
            $target->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Notification marked as read']);
    }
}
