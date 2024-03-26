<?php
namespace Plugin\WebHook;


class Bootstrap
{
    public function boot()
    {
        $this->beforeOrderPay();
    }

    public function beforeOrderPay()
    {
        $eventCode = plugin_setting('web_hook.event_code');
        $callbackUrl = plugin_setting('web_hook.event_callback_url');
        add_hook_filter($eventCode, function ($data) use($eventCode, $callbackUrl) {
            // 补充字段
            if (!isset($data['event_code'])) {
                $data['event_code'] = $eventCode;
            }
            // 为订单支付补充产品和用户信息
            if ($eventCode === 'service.state_machine.change_status.after') {
                $data['order']->load(['orderProducts', 'customer']);
            }
            // 使用post将数据发送到回调地址
            $response = (new \GuzzleHttp\Client)->post($callbackUrl, [
                'json' => $data,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        });
    }
}
