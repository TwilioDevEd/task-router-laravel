<?php

namespace App\TaskRouter;


class WorkspaceFacade
{
    private $taskRouterClient;

    private $workspace;

    public function __construct($taskRouterClient, $params)
    {
        $this->taskRouterClient = $taskRouterClient;
        $workspaceName = $params["friendlyName"];
        foreach ($taskRouterClient->workspaces()->read() as $workspace) {
            if ($workspace->friendlyName === $workspaceName) {
                $taskRouterClient->workspaces()->getContext($workspace->sid)->delete();
                break;
            }
        }
        $this->workspace = $taskRouterClient->workspaces()->create($workspaceName, $params);
    }

    /**
     * Magic method to read the attributes to the wrapped workspace
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->workspace->$property;
    }

    /**
     * Magic method to write the attributes into the wrapped workspace
     * @param $property
     * @param $value
     * @return mixed
     */
    public function __set($property, $value)
    {
        $this->workspace->$property = $value;
        return $this;
    }

    /**
     * @param $activityName Name of the activity to search for
     * @return the activity found or null
     */
    function findActivityByName($activityName)
    {
        foreach ($this->workspace->activities->read() as $activity) {
            if ($activity->friendlyName === $activityName)
                return $activity;
        }
    }

    /**
     * @param $taskQueueName Name of the task queue to search for
     * @return the activity found or null
     */
    function findTaskQueueByName($taskQueueName)
    {
        foreach ($this->workspace->taskQueues->read() as $taskQueue) {
            if ($taskQueue->friendlyName === $taskQueueName)
                return $taskQueue;
        }
    }

    /**
     * @param $params Attributes to define the new Worker in the workspace
     * @return worker or null
     */
    function addWorker($params)
    {
        $this->workspace->workers->create($params['friendlyName'], $params);
    }

    /**
     * @param $params Attributes to define the new Task Queue in the workspace
     * @return TaskQueue or null
     */
    function addTaskQueue($params)
    {
        $this->workspace->taskQueues->create(
            $params['friendlyName'],
            $params['assignmentActivitySid'],
            $params['reservationActivitySid'],
            $params);
    }


    /**
     * @param $params Attributes to define the new Workflow in the workspace
     * @return object instance of Workflow
     */
    function addWorkFlow($params)
    {
        $configJson = $params["configuration"];
        $name = $params["friendlyName"];
        $assignmentCallbackUrl = $params["assignmentCallbackUrl"];
        return $this->workspace->workflows->create($name, $configJson, $assignmentCallbackUrl, $params);
    }

}