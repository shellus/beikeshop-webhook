<?php
return [
    [
        'name'      => 'event_code',
        'label_key' => 'common.event_code',
        'type'      => 'string',
        'description' => '事件代码，例如 checkout.order.confirm.after，请查看：https://docs.beikeshop.com/dev/6_hook.html',
        'required'  => true,
    ],
    [
        'name'      => 'event_callback_url',
        'label_key' => 'common.event_callback_url',
        'type'      => 'string',
        'description' => '事件回调地址，例如 https://www.example.com/callback',
        'required'  => true,
    ],
];
