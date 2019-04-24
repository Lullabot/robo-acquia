<?php

/**
 * @file
 * An example RoboFile to show how to use the Acquia task stack.
 */

use Consolidation\OutputFormatters\StructuredData\UnstructuredListData;
use Lullabot\RoboAcquia\AcquiaTaskWatcher;

class RoboFile extends \Robo\Tasks
{
    use Lullabot\RoboAcquia\LoadRoboAcquiaTasks;

    private $acquiaKey;
    private $acquiaSecret;
    private $acquiaApplicationUuid;

    public function __construct()
    {
        $this->acquiaKey = \Robo\Robo::Config()->get('acquia.key');
        $this->acquiaSecret = \Robo\Robo::Config()->get('acquia.secret');
        $this->acquiaApplicationUuid = '[your-acquia-environment-uuid]';
    }

    /**
     * List out tasks.
     *
     * @return \Consolidation\OutputFormatters\StructuredData\UnstructuredListData
     *
     * @command acquia:task-list
     */
    public function acquiaTaskList()
    {
        $response = $this->taskAcquiaCloudApiStack($this->acquiaKey, $this->acquiaSecret)
            ->tasks($this->acquiaApplicationUuid)
            ->run();
        $tasks = [];
        foreach ($response['result'] as $task) {
            /* @var \AcquiaCloudApi\Response\TaskResponse $task */
            $tasks[$task->uuid] = sprintf('%s: %s', $task->name, $task->status);
        }
        return new UnstructuredListData($tasks);
    }

    /**
     * Perform a code deploy.
     *
     * @param string $env_uuid
     *   The Acquia environment UUID to deploy to.
     * @param string $branch
     *   The tag or branch to deploy. Prefix a tag with tags/, e.g. tags/[tag].
     * @param string $database_name
     *   The name of the database to backup. Defaults to www.
     *
     * @return \Robo\Result
     *
     * @command acquia:deploy
     */
    public function deploy($env_uuid, $branch, $database_name = 'www')
    {
        $stack = $this->taskAcquiaCloudApiStack($this->acquiaKey, $this->acquiaSecret);
        // Simple waitForTaskCompletion with no callback. This will wait quietly
        // before proceeding to the next item in the stack.
        $stack->createDatabaseBackup($env_uuid, $database_name)
            ->waitForTaskCompletion($this->acquiaApplicationUuid, AcquiaTaskWatcher::DATABASE_BACKUP_CREATED);

        // Deploy code and provide feedback with a callback. This will print a
        // message and provide feedback with a dot every 3 seconds. Once
        // complete, it will provide a confirmation dialog before continuing.
        $callback = function ($result)
        {
            static $i = 0;
            if ($i === 0) {
                $this->output()->write("\nWaiting for task completion.");
            }
            $i++;
            // Print a dot every 3 seconds.
            if ($i % 3 === 0) {
                $this->output()->write('.');
            }
            if (isset($result[0]->status) && $result[0]->status === 'completed') {
                $this->writeln("\nTask completion detected!");
                if (!$this->confirm('Would you like to continue?')) {
                    throw new \Robo\Exception\TaskExitException(static::class, 'Cancelled.', \Robo\Result::EXITCODE_USER_CANCEL);
                }
            }
        };
        $stack->switchCode($env_uuid, $branch)
            ->waitForTaskCompletion($this->acquiaApplicationUuid, AcquiaTaskWatcher::CODE_SWITCHED, 240, $callback);

        // Run the stack of Acquia tasks now.
        $result = $stack->run();
        if ($result->wasSuccessful()) {
            $this->yell("Backup created and code deployed!");
        }
        return $result;
    }

}
