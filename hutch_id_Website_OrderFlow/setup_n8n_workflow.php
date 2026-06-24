<?php
/**
 * Create Simple N8N Webhook Workflow
 * Untuk fixed webhook chatbot di N8N
 */

require 'vendor/autoload.php';

$webhookUrl = 'http://localhost:5678';
$client = new \GuzzleHttp\Client([
    'verify' => false,
    'timeout' => 15
]);

echo "\n🔧 Creating simple N8N webhook workflow...\n\n";

// Workflow definition
$workflow = [
    'name' => 'Hutch Chatbot',
    'description' => 'Simple webhook for Hutch.id chatbot',
    'nodes' => [
        [
            'parameters' => [
                'httpMethod' => 'POST',
                'path' => 'hutch-chatbot'
            ],
            'name' => 'webhook',
            'type' => 'n8n-nodes-base.webhook',
            'typeVersion' => 1,
            'position' => [250, 300]
        ],
        [
            'parameters' => [
                'message' => '={"reply":"Halo! Webhook connected successfully!","message":"{{ $json.message }}"}'
            ],
            'name' => 'respond',
            'type' => 'n8n-nodes-base.respondToWebhook',
            'typeVersion' => 1,
            'position' => [450, 300]
        ]
    ],
    'connections' => [
        'webhook' => [
            'main' => [
                [
                    [
                        'node' => 'respond',
                        'type' => 'main',
                        'index' => 0
                    ]
                ]
            ]
        ]
    ],
    'active' => true,
    'nodeTypes' => []
];

try {
    // Try create workflow via N8N API
    $response = $client->post($webhookUrl . '/api/v1/workflows', [
        'json' => $workflow,
        'auth' => ['adrianronald99@gmail.com', 'Drian11099']
    ]);
    
    echo "✅ Workflow created successfully!\n";
    echo "Response: " . $response->getBody() . "\n\n";
    
} catch (\Exception $e) {
    echo "Note: Could not create via API (might need manual setup)\n";
    echo "Please manually create webhook in N8N UI:\n";
    echo "1. Open: http://localhost:5678\n";
    echo "2. New Workflow\n";
    echo "3. Add Webhook node (POST, path: hutch-chatbot)\n";
    echo "4. Add Respond to Webhook node\n";
    echo "5. Connect: Webhook → Respond\n";
    echo "6. Save & Activate\n\n";
}

?>
