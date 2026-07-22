<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;

class FirebaseService
{
    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Send notification to multiple device tokens.
     *
     * @param array $tokens
     * @param string $title
     * @param string $body
     * @param array|null $data
     * @return array
     */
    public function sendMulticast(array $tokens, string $title, string $body, ?array $data = []): array
    {
        if (empty($tokens)) {
            return [];
        }

        $messageData = $data ?? [];
        $messageData['title'] = $title;
        $messageData['body'] = $body;

        // Ensure all data values are strings as required by Firebase
        $stringData = array_map(function ($value) {
            return is_string($value) ? $value : json_encode($value);
        }, $messageData);

        $message = CloudMessage::new()
            ->withData($stringData)
            ->withAndroidConfig(AndroidConfig::fromArray([
                'priority' => 'high'
            ]))
            ->withApnsConfig(ApnsConfig::fromArray([
                'headers' => [
                    'apns-priority' => '10'
                ]
            ]));

        $report = $this->messaging->sendMulticast($message, $tokens);

        $invalidTokens = [];
        if ($report->hasFailures()) {
            foreach ($report->failures()->getItems() as $failure) {
                if ($failure->error() !== null) {
                    $invalidTokens[] = $failure->target()->value();
                }
            }
        }

        // Return invalid tokens so we can optionally delete them from DB
        return [
            'success_count' => $report->successes()->count(),
            'failure_count' => $report->failures()->count(),
            'invalid_tokens' => $invalidTokens,
        ];
    }
}
