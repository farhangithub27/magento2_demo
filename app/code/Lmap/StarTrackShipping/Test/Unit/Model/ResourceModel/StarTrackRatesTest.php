<?php
namespace Lmap\StarTrackShipping\Test\Model\ResourceModel;

use Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates;
use PHPUnit\Framework\TestCase;

class StarTrackRatesTest extends TestCase
{
    // \PHPUnit_Framework_TestCase
    // issue occured while running unit test "magento 2 PHPUnit_Framework_TestCase not found" .Solution in
    // https://magento.stackexchange.com/questions/262824/phpunit-framework-testcase-not-found

    /**
     * @var StarTrackRates
     */
    private $resourceModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resource;

    private $loggerMock;


    protected function setUp()
    {

        $this->loggerMock = $this->createMock(\Psr\Log\LoggerInterface::class);
        $coreConfigMock = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $starTrackRatesMock = $this->createMock(\Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates::class);
        $this->resource = $this->createMock(\Magento\Framework\App\ResourceConnection::class);
        $contextMock = $this->createMock(\Magento\Framework\Model\ResourceModel\Db\Context::class);
        $contextMock->expects($this->once())->method('getResources')->willReturn($this->resource);


        $this->resourceModel = $this->getMockBuilder(StarTrackRates::class)
            ->setMethods(['__Construct'])
            ->setConstructorArgs([$contextMock,$this->loggerMock])
            ->getMock();
        /*
        $this->resourceModel = new StarTrackRates(
            $contextMock,
            $this->loggerMock,
            $connectionName = null
        );
        */
    }

    public function testgetRates()
    {

        // TODO


    }


}



