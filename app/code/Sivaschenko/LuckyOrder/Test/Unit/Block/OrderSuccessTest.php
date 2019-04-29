<?php

namespace Sivaschenko\LuckyOrder\Test\Unit\Block;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Sivaschenko\LuckyOrder\Block\OrderSuccess;
use Sivaschenko\LuckyOrder\Model\LuckInfo;
use Magento\Checkout\Model\Session;

class OrderSuccessTest extends \PHPUnit\Framework\TestCase //\PHPUnit_Framework_TestCase
    // issue occured while running unit test "magento 2 PHPUnit_Framework_TestCase not found" .Solution in
    // https://magento.stackexchange.com/questions/262824/phpunit-framework-testcase-not-found
{
    /**
     * @var OrderSuccess
     */
    private $block;

    /**
     * @var LuckInfo|\PHPUnit_Framework_MockObject_MockObject
     */
    private $luckInfo;

    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $session;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->luckInfo = $this->getMock('Sivaschenko\LuckyOrder\Model\LuckInfo');
        $this->session = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);

        $this->block = $objectManager->getObject(
            'Sivaschenko\LuckyOrder\Block\OrderSuccess',
            [
                'luckInfo' => $this->luckInfo,
                'session' => $this->session
            ]
        );
    }

    /**
     * @param $isLucky
     * @param $html
     * @dataProvider luckyProvider
     */
    public function testToHtml($isLucky, $html)
    {
        $amount = 1.24;

        $order = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $order->expects($this->once())
            ->method('getGrandTotal')
            ->willReturn($amount);

        $this->session->expects($this->once())
            ->method('getLastRealOrder')
            ->willReturn($order);

        $this->luckInfo->expects($this->once())
            ->method('isAmountLucky')
            ->with($amount)
            ->willReturn($isLucky);

        $this->assertEquals($html, $this->block->toHtml());
    }

    public function luckyProvider()
    {
        return [
            [true, __('Your order is lucky!')],
            [false, '']
        ];
    }
}