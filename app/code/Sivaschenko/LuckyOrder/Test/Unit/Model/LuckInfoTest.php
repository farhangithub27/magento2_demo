<?php

namespace Sivaschenko\LuckyOrder\Test\Unit\Model;

use Sivaschenko\LuckyOrder\Model\LuckInfo;

class LuckInfoTest extends \PHPUnit\Framework\TestCase //\PHPUnit_Framework_TestCase
    // issue occured while running unit test "magento 2 PHPUnit_Framework_TestCase not found" .Solution in
    // https://magento.stackexchange.com/questions/262824/phpunit-framework-testcase-not-found
{
    /**
     * @var LuckInfo
     */
    private $luckInfo;

    protected function setUp()
    {
        // In this setUp function assign instance of class under test to our property.
        // Since our class doesnt have any dependencies we can create it with new operator.
        $this->luckInfo = new LuckInfo();
    }

    /**
     * @param $isLucky
     * @param $amount
     * @dataProvider amountProvider
     *
     */
    public function testIsAmountLucky($isLucky, $amount)
    {
        $this->assertEquals($isLucky, $this->luckInfo->isAmountLucky($amount));
    }

    // In order to use this php built in data provider function we have add it to annotations of testIsAmountLucky method.
    public function amountProvider()
    {
        return [
            'lucky' => [true, 65.56],
            'not lucky' => [false, 66.56]
        ];
    }
}