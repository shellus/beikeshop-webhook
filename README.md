# 贝壳商城(beikeshop)的WEBHOOK插件
用于便捷的对接外部系统，例如在订单支付完成后将订单的sku编号传递到业务系统，从而实现在商城购买的虚拟物品可以实时的增加余量到业务系统，

开源地址：https://github.com/shellus/beikeshop-webhook

## 应用示例
1. 将业务系统的用户和beikeshop打通，可以通过记录对方用户ID，或者通过手机号、邮箱、userFlag等方式关联
2. beikeshop新增一个VIP服务的商品，SKU编号为"vip-1"
3. 在本插件配置事件为 service.state_machine.change_status.after
4. 在业务系统的回调判断event_code==='service.state_machine.change_status.after' && status === 'paid'，否则忽略
5. 循环$data['order']['orderProducts'] 判断product_sku==='xxx'为$data['order']['customer']['id'] 增加VIP余量

## !!! 对接Laravel业务系统注意事项
1. 将回调地址定义在需要用户认证的路由组之外
2. 将回调地址定义在`\App\Http\Middleware\VerifyCsrfToken::$except` 数组中

## 未来计划：
1. 可配置多个钩子
2. 可选异步执行
