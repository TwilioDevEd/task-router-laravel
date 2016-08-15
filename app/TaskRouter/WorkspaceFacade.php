<?php

namespace App\TaskRouter;


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
        $existingWorkspace = $taskRouterClient->workspaces->read(
            array(
                "friendlyName" => $workspaceName
            )
        );
        if ($existingWorkspace) {
            $existingWorkspace[0]->delete();
        }

        $workspace = $taskRouterClient->workspaces
            ->create($workspaceName, $params);
        return new WorkspaceFacade($taskRouterClient, $workspace);
    }

    public static function createBySid($taskRouterClient, $workspaceSid)
    {
        $workspace = $taskRouterClient->workspaces($workspaceSid);
        return new WorkspaceFacade($taskRouterClient, $workspace);
    }

    /**
     * Magic getter to lazy load subresources
     *
     * @param string $property Subresource to return
     *
     * @return \Twilio\ListResource The requested subresource
     *
     * @throws \Twilio\Exceptions\TwilioException For unknown subresources
     */
    public function __get($property)
    {
        return $this->_workspace->$property;
    }

    /**
     * Gets an activity instance by its friendly name
     *
     * @param $activityName Friendly name of the activity to search for
     *
     * @return ActivityInstance of the activity found or null
     */
    function findActivityByName($activityName)
    {
        $this->cacheActivitiesByName();
        return $this->_activities[$activityName];
    }

    /**
     * Caches the activities in an associative array which links friendlyName with
     * its ActivityInstance
     */
    protected function cacheActivitiesByName()
    {
        if (!$this->_activities) {
            $this->_activities = array();
            foreach ($this->_workspace->activities->read() as $activity) {
                $this->_activities[$activity->friendlyName] = $activity;
            }
        }
    }

    /**
     * Looks for a worker by its SID
     *
     * @param $sid string with the Worker SID
     *
     * @return mixed worker found or null
     */
    function findWorkerBySid($sid)
    {
        return $this->_workspace->workers($sid);
    }

    /**
     * Returns an associative array with
     *
     * @return mixed array with the relation phone -> workerSid
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
     * Looks for a Task Queue by its friendly name
     *
     * @param $taskQueueName string with the friendly name of the task queue to
     * search for
     *
     * @return the activity found or null
     */
    function findTaskQueueByName($taskQueueName)
    {
        $result = $this->_workspace->taskQueues->read(
            array(
                "friendlyName" => $taskQueueName
            )
        );
        if ($result) {
            return $result[0];
        }
    }

    function updateWorkerActivity($worker, $activitySid)
    {
        $worker->update(['activitySid' => $activitySid]);
    }

    /**
     * Adds workers to the workspace
     *
     * @param $params mixed with the attributes to define the new Worker in the
     * workspace
     *
     * @return worker or null
     */
    function addWorker($params)
    {
        $this->_workspace->workers->create($params['friendlyName'], $params);
    }

    /**
     * Adds a Task Queue to the workspace
     *
     * @param $params mixed with attributes to define the new Task Queue in the
     * workspace
     *
     * @return TaskQueue or null
     */
    function addTaskQueue($params)
    {
        $this->_workspace->taskQueues->create(
            $params['friendlyName'],
            $params['reservationActivitySid'],
            $params['assignmentActivitySid'],
            $params
        );
    }


    /**
     * Adds a workflow to the workspace
     *
     * @param $params mixed with attributes to define the new Workflow in the
     * workspace
     *
     * @return object instance of Workflow
     */
    function addWorkFlow($params)
    {
        $configJson = $params["configuration"];
        $name = $params["friendlyName"];
        $assignmentCallbackUrl = $params["assignmentCallbackUrl"];
        return $this->_workspace->workflows->create(
            $name, $configJson, $assignmentCallbackUrl, $params
        );
    }

}
