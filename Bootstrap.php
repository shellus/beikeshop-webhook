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
            app('log')->info('WEBHOOK插件: 监听到事件', [$eventCode, $callbackUrl]);
            // 补充事件类型字段
            if (!isset($data['event_code'])) {
                $data['event_code'] = $eventCode;
            }
            // 为订单支付补充产品和用户信息
            if ($eventCode === 'service.state_machine.change_status.after') {
                $data['order']->load(['orderProducts', 'customer']);
            }

            try{
                $response = (new \GuzzleHttp\Client)->post($callbackUrl, [
                    'json' => $data,
                ]);
                // 判断状态是否200
                if ($response->getStatusCode() !== 200) {
                    throw new \Exception('响应状态码' . $response->getStatusCode());
                }
                // 判断是否json
                if (!str_contains($response->getHeaderLine('Content-Type'), 'application/json')) {
                    throw new \Exception('响应类型不是json：' . $response->getBody()->getContents());
                }
                // 判断json的code是否是0
                $result = json_decode($response->getBody()->getContents(), true);
                if ($result['code'] !== 0) {
                    throw new \Exception('响应code不是0：' . $response->getBody()->getContents());
                }
            } catch (\Exception $exception) {
                app('log')->info('WEBHOOK插件: 请求回调地址异常', [$eventCode, $callbackUrl, $data, $exception->getMessage()]);
                return;
            }

            app('log')->info('WEBHOOK插件: 回调结果', [$eventCode, $callbackUrl, $data, $result]);
        });
    }
}
