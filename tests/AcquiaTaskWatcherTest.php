<?php

namespace Lullabot\RoboAcquia\Tests;

use Lullabot\RoboAcquia\AcquiaTaskWatcher;
use Lullabot\RoboAcquia\Exceptions\AcquiaTaskTimeoutExceededException;
use AcquiaCloudApi\Tests\CloudApiTestCase;

/**
 * @coversDefaultClass \Lullabot\RoboAcquia\AcquiaTaskWatcher
 */
class AcquiaTaskWatcherTest extends CloudApiTestCase
{

    /**
     * @covers ::watch
     */
    public function testWatchTimeoutExceeded()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/getTasks.json');
        $client = $this->getMockClient($response);
        $watcher = new AcquiaTaskWatcher($client, '0c7e79ab-1c4a-424e-8446-76ae8be7e851');
        $this->expectException(AcquiaTaskTimeoutExceededException::class);
        $this->expectExceptionMessage('The timeout was exceeded waiting for the Acquia task to complete.');
        $watcher->watch('OperationStarted', 2);
    }
}
