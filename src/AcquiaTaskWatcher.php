<?php

namespace Lullabot\RoboAcquia;

use AcquiaCloudApi\Connector\Client;
use Robo\ResultData;
use Lullabot\RoboAcquia\Exceptions\AcquiaTaskTimeoutExceededException;

/**
 * A class to watch for Acquia task(s) to complete.
 */
class AcquiaTaskWatcher
{
    const CODE_SWITCHED = 'CodeSwitched';
    const DATABASE_BACKUP_CREATED = 'DatabaseBackupCreated';
    const DATABASE_BACKUP_RESTORED = 'DatabaseBackupRestored';
    const DATABASE_COPIED = 'DatabaseCopied';
    const VARNISH_CLEARED = 'VarnishCleared';

    /**
     * The Acquia application UUID.
     *
     * @var string
     */
    protected $applicationUuid;

    /**
     * The Acquia Cloud API Client.
     *
     * @var \AcquiaCloudApi\Connector\Client
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param \AcquiaCloudApi\Connector\Client $client
     *   The Acquia Cloud API Client.
     * @param string                                   $applicationUuid
     *   The Acquia application UUID to check for tasks on.
     */
    public function __construct(Client $client, $applicationUuid)
    {
        $this->client = $client;
        $this->applicationUuid = $applicationUuid;
    }

    /**
     * Watch a task for completion.
     *
     * @param string   $taskname
     *   The name of the Acquia task. Use the constants in this class.
     * @param int      $timeout
     *   The timeout in seconds to wait. Defaults to 120 (2 minutes).
     * @param callable $callback
     *   An optional callback to provide feedback during the watch loop.
     *
     * @return \Robo\ResultData
     *   If successful, the message will contain the task id of the completed
     *   task.
     *
     * @throws \Lullabot\RoboAcquia\Exceptions\AcquiaTaskTimeoutExceededException
     *   If timeout was exceeded.
     * @throws \Exception
     *   If any other error was encountered.
     */
    public function watch($taskname, $timeout = 120, callable $callback = null)
    {
        $start = time();
        $task_id = null;
        // Since we share the query on the client, we save it and restore it.
        $current_query = $this->client->getQuery();
        $this->client->clearQuery();
        $this->client->addQuery('sort', '-created');
        $this->client->addQuery('filter', 'name=' . $taskname . ';status=in-progress');
        $this->client->addQuery('limit', 1);
        try {
            do {
                // Whoa there fella, don't stampede the API.
                sleep(1);
                if (time() - $start > $timeout) {
                    throw new AcquiaTaskTimeoutExceededException('The timeout was exceeded waiting for the Acquia task to complete.');
                }
                $result = $this->client->request('get', "/applications/" . $this->applicationUuid . "/tasks");
                if (!empty($result[0]->uuid)) {
                    $task_id = $result[0]->uuid;
                }
                if ($callback && is_callable($callback)) {
                    call_user_func($callback, $result);
                }
            } while (!empty($result[0]->status));
        } catch (\Exception $e) {
            // Restore the query.
            $this->reapplyQuery($current_query);
            throw $e;
        }
        $this->reapplyQuery($current_query);
        return new ResultData(ResultData::EXITCODE_OK, $task_id);
    }

    /**
     * Reapply the query prior to running our task watcher.
     *
     * @param iterable $current_query
     *   The query array.
     */
    protected function reapplyQuery(iterable $current_query)
    {
        $this->client->clearQuery();
        foreach ($current_query as $key => $value) {
            $this->client->addQuery($key, $value);
        }
    }
}
