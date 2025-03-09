<?php

return [

    /* Available Drivers: "none", "slack", "telegram" */
    'driver' => env('NOTIFICATION_DRIVER', 'slack'),

    'slack' => [
        'webhook_url' => env('NOTIFICATION_SLACK_WEBHOOK_URL',"https://hooks.slack.com/services/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"),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
    ],

    'slack_tbp' => [
        'webhook_url' => env('NOTIFICATION_SLACK_WEBHOOK_URL_TBP',"https://hooks.slack.com/services/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"),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
    ],

    'slack_sts' => [
        'webhook_url' => env('NOTIFICATION_SLACK_WEBHOOK_URL_STS',"https://hooks.slack.com/services/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"),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
    ],

    'slack_bku' => [
        'webhook_url' => env('NOTIFICATION_SLACK_WEBHOOK_URL_BKU',"https://hooks.slack.com/services/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"),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
    ],

    'slack_mutasi' => [
        'webhook_url' => env('NOTIFICATION_SLACK_WEBHOOK_URL_MUTASI',"https://hooks.slack.com/services/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"),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
    ],

    'slack_kantorku' => [
        'webhook_url' => env('NOTIFICATION_SLACK_WEBHOOK_URL_KANTORKU','https://hooks.slack.com/services/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'),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
    ],

    'telegram' => [
        /* Token for bot */
        'bot_token' => env('NOTIFICATION_TELEGRAM_BOT_TOKEN',"5250606083:AAEIIytvHj_FIK9VHUo34gnwBIgkFmYmGA4"),

        /* group / chanel destination  */
        'group_chat_id' => env('NOTIFICATION_TELEGRAM_GROUP_CHAT_ID',"-707272180"),

        /* Available Drivers: "html", "MarkdownV2", "markdown"  */
        'parse_mode' => env('NOTIFICATION_TELEGRAM_PARSE_MODE',"html"),
    ],

];
