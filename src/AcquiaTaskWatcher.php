<?php

namespace Lullabot\RoboAcquia;

use AcquiaCloudApi\CloudApi\ClientInterface;

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
     * @var callable
     */
    protected $callback;

    /**
     * The Acquia Cloud API Client.
     *
     * @var \AcquiaCloudApi\CloudApi\ClientInterface
     */
    protected $client;

    /**
     * The name of the task. Use the constants on this class.
     *
     * @var string
     */
    protected $taskName;

    /**
     * The number of seconds before timing out.
     *
     * @var int
     */
    protected $timeout;

    /**
     * Constructor.
     *
     * @param \AcquiaCloudApi\CloudApi\ClientInterface $client
     *   The Acquia Cloud API Client.
     * @param string $applicationUuid
     *   The Acquia application UUID to check for tasks on.
     * @param string $taskName
     *   The name of the task to wait for completion.
     * @param callable $callback
     *   An optional callback to provide feedback during the watch loop.
     * @param int $timeout
     *   The timeout in seconds to wait. Defaults to 120 (2 minutes).
     */
    public function __construct(ClientInterface $client, $applicationUuid, $taskName, callable $callback = null, $timeout = 120)
    {
        $this->client = $client;
        $this->applicationUuid = $applicationUuid;
        $this->taskName = $taskName;
        $this->callback = $callback;
        $this->timeout = $timeout;
    }

    /**
     * Watch a task for completion.
     */
    public function watch()
    {
        $start = time();
        $task_id = null;
        // Store any current query on this client.
        $current_query = $this->client->getQuery();
        $this->client->clearQuery();
        $this->client->addQuery('sort', '-created');
        $this->client->addQuery('filter', 'name=' . $this->taskName);
        $this->client->addQuery('limit', 1);
        try {
            do {
                // Whoa there fella, don't stampede the API.
                sleep(1);
                if (time() - $start > $this->timeout) {
                    throw new \Exception('The timeout was exceeded waiting for the Acquia task to complete.');
                }
                $result = $this->client->tasks($this->applicationUuid);
                if (!empty($result[0]->uuid)) {
                    $task_id = $result[0]->uuid;
                    if ($result[0]->status == 'failed') {
                        throw new \Exception(sprintf('The %s task on Acquia failed.', $this->taskName));
                    }
                }
                if ($this->callback && is_callable($this->callback)) {
                    call_user_func($this->callback, $result);
                }
            } while (!isset($result[0]->status) || $result[0]->status !== 'completed');
        } catch (\Exception $e) {
            // Restore the query.
            $this->reapplyQuery($current_query);
            throw $e;
        }
        $this->reapplyQuery($current_query);
        return $task_id;
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
