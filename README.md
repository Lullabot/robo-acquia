# Robo Acquia Cloud API Task runner

This composer package integrates the [Robo](https://robo.li) task runner with
the [Acquia Cloud API v2 PHP SDK](https://github.com/typhonius/acquia-php-sdk-v2/)
to facilitate running Acquia CLI tasks either in Robo or Drush.

## Installation

From your project's composer root directory, require this library:

```
$ composer require lullabot/robo-acquia
```

## Usage in a Robofile.php

First, generate an [Acquia Cloud API token](https://docs.acquia.com/acquia-cloud/develop/api/auth/).
Once done, you should store that token in a secure place for retrieval in your
command. For the purposes of this documentation, we will use the [robo.yml](https://robo.li/getting-started/#configuration)
in our project root.

`./robo.yml`
```yml
acquia:
  key: 'd0697bfc-7f56-4942-9205-b5686bf5b3f5'
  secret: 'D5UfO/4FfNBWn4+0cUwpLOoFzfP7Qqib4AoY+wYGsKE='
```
You may want to specify a custom config yml and set
appropriate permissions so that unpriveleged users cannot read the key / secret.

Then, in our RoboFile.php, we can add a new command to print out a list of
tasks.

`./RoboFile.php`
```php
<?php

use Consolidation\OutputFormatters\StructuredData\UnstructuredListData;

class RoboFile extends \Robo\Tasks
{
    use Lullabot\RoboAcquia\LoadRoboAcquiaTasks;

    public function __construct()
    {
        $this->acquiaKey = \Robo\Robo::Config()->get('acquia.key');
        $this->acquiaSecret = \Robo\Robo::Config()->get('acquia.secret');
        $this->acquiaApplicationUuid = '[your-acquia-application-uuid]';
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

}
```

Running the command should result in a list of tasks:

```
➜ vendor/bin/robo acquia:task-list
 [Lullabot\RoboAcquia\AcquiaCloudApiStack] tasks ["[your-acquia-application-uuid]"]
cd7ed2f4-fd8c-46d2-bc63-a14d9abed6e1: 'CodeSwitched: completed'
14a981c6-eeb0-46df-b965-ce4d74e5214b: 'VarnishCleared: completed'
d3dcafab-89db-4e72-abd2-d2448a0408dc: 'CodeSwitched: completed'
6e73e741-6d2a-475f-a3b8-1ed29563c71d: 'DatabaseBackupCreated: completed'
08417f0b-3957-470e-8400-f39b090bf269: 'DatabaseBackupCreated: completed'
2967397d-e7b0-4155-8107-6eae3818a39f: 'DatabaseBackupCreated: completed'
d26ea593-d863-4bcd-8178-d6566b8cad88: 'DatabaseBackupCreated: completed'
f82374db-7695-49e2-b15b-45c0e3311c36: 'DatabaseBackupCreated: completed'
e4b03419-4300-411e-89f8-e015a7e3a9ab: 'DatabaseBackupCreated: completed'
ee4b0286-7e7c-4e51-a7cc-d9690b9778b3: 'DatabaseBackupCreated: completed'
0308b62b-aa1c-4fd3-8437-61f30625cc9a: 'DatabaseBackupCreated: completed'
5a1e7f71-926f-4d16-8874-e257952223a1: 'DatabaseBackupCreated: completed'
db4e3e35-cd74-4350-8474-cdc09e1dcbeb: 'DatabaseBackupCreated: completed'
55debdad-7bf4-4da5-a293-899bd7f96203: 'DatabaseBackupCreated: completed'
e158de43-336e-4f58-8203-779ea6f4bf7d: 'DatabaseBackupCreated: completed'
```

## Waiting for task completion

Many Acquia Cloud API operations queue long-running tasks in the background.
Unfortunately, the API does not respond with the task ID to allow for
programmatic monitoring of the task execution. The Acquia Robo task provides for
a rudimentary workaround for this. This method is not fool proof, since the
program cannot be sure that the task it finds and monitors is the exact one
triggered. If you have a busy Acquia task queu with the potential for a mismatch
of Acquia tasks with the current running command list, do *not* use this.

If you are still here, here's how you can wait for a task to complete. In this
example, we will be using Robo and waiting for the completion of a backup to
complete and a code deploy to occur before proceeding.

```php
<?php

use Lullabot\RoboAcquia\AcquiaTaskWatcher;
use Lullabot\RoboAcquia\LoadRoboAcquiaTasks;

class RoboFile extends \Robo\Tasks
{
    use LoadRoboAcquiaTasks;

    public function __construct()
    {
        $this->acquiaKey = \Robo\Robo::Config()->get('acquia.key');
        $this->acquiaSecret = \Robo\Robo::Config()->get('acquia.secret');
        $this->acquiaApplicationUuid = '[your-application-uuid]';
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
```
