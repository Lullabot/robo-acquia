<?php

namespace Lullabot\RoboAcquia;

trait LoadRoboAcquiaTasks
{

    /**
     * A Robo task to interact with the Acquia Cloud API.
     *
     * @param string   $key
     *   The Acquia Cloud API Key.
     * @param string   $secret
     *   The Acquia Cloud API Secret.
     * @param iterable $config
     *   Optional. Any additional config beyond the key and secret.
     *
     * @return \Lullabot\RoboAcquia\AcquiaCloudApiStack
     *   The task object.
     */
    public function taskAcquiaCloudApiStack($key, $secret, iterable $config = [])
    {
        $config['key'] = $key;
        $config['secret'] = $secret;
        return $this->task(AcquiaCloudApiStack::class, $config);
    }
}
