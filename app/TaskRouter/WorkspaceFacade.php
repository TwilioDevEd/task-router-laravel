<?php

namespace App\TaskRouter;


use Twilio\Exceptions\DeserializeException;
use Twilio\Exceptions\TwilioException;

class WorkspaceFacade
{
    private $_taskRouterClient;

    private $_workspace;

    private $_activities;

    public function __construct($taskRouterClient, $workspace)
    {
        $this->_taskRouterClient = $taskRouterClient;
        $this->_workspace = $workspace;
    }

    public static function createNewWorkspace($taskRouterClient, $params)
    {
        $workspaceName = $params["friendlyName"];
        foreach ($taskRouterClient->workspaces()->read() as $workspace) {
            if ($workspace->friendlyName === $workspaceName) {
                $taskRouterClient->workspaces()->getContext($workspace->sid)->delete();
                break;
            }
        }
        $workspace = $taskRouterClient->workspaces()->create($workspaceName, $params);
        return new WorkspaceFacade($taskRouterClient, $workspace);
    }

    public static function createBySid($taskRouterClient, $workspaceSid)
    {
        $workspace = $taskRouterClient->workspaces()->getContext($workspaceSid);
        return new WorkspaceFacade($taskRouterClient, $workspace);
    }

    /**
     * Magic method to read the attributes to the wrapped workspace
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->_workspace->$property;
    }

    /**
     * Magic method to write the attributes into the wrapped workspace
     * @param $property
     * @param $value
     * @return mixed
     */
    public function __set($property, $value)
    {
        $this->_workspace->$property = $value;
        return $this;
    }

    /**
     * @param $activityName Name of the activity to search for
     * @return the activity found or null
     */
    function findActivityByName($activityName)
    {
        $this->cacheActivitiesByName();
        return $this->_activities[$activityName];
    }

    protected function cacheActivitiesByName()
    {
        if(!$this->_activities)
        {
            $this->_activities = array();
            foreach ($this->_workspace->activities->read() as $activity) {
                $this->_activities[$activity->friendlyName] = $activity;
            }
        }
    }

    /**
     * @param $sid Worker SID
     * @return mixed worker found or null
     */
    function findWorkerBySid($sid)
    {
        return $this->_workspace->workers->getContext($sid);
    }

    /**
     * @return array with the relation phone -> workerSid
     */
    function getWorkerPhones()
    {
        $worker_phones = array();
        foreach ($this->_workspace->workers->read() as $worker) {
            $workerAttribs = json_decode($worker->attributes);
            $worker_phones[$workerAttribs->contact_uri] = $worker->sid;
        }
        return $worker_phones;
    }

    /**
     * @param $taskQueueName Name of the task queue to search for
     * @return the activity found or null
     */
    function findTaskQueueByName($taskQueueName)
    {
        foreach ($this->_workspace->taskQueues->read() as $taskQueue) {
            if ($taskQueue->friendlyName === $taskQueueName)
                return $taskQueue;
        }
    }

    function updateWorkerActivity($worker, $activitySid)
    {
        $worker->update(['activitySid' => $activitySid ]);
    }

    /**
     * @param $params Attributes to define the new Worker in the workspace
     * @return worker or null
     */
    function addWorker($params)
    {
        $this->_workspace->workers->create($params['friendlyName'], $params);
    }

    /**
     * @param $params Attributes to define the new Task Queue in the workspace
     * @return TaskQueue or null
     */
    function addTaskQueue($params)
    {
        $this->_workspace->taskQueues->create(
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
        return $this->_workspace->workflows->create($name, $configJson, $assignmentCallbackUrl, $params);
    }

}