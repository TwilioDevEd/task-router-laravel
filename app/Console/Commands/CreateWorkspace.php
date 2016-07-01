<?php

namespace App\Console\Commands;

use App\TaskRouter\WorkspaceFacade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use TaskRouter_Services_Twilio;
use Twilio\Rest\Client;
use Twilio\Rest\Taskrouter;
use WorkflowRuleTarget;

class CreateWorkspace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workspace:create 
                                        {host : Server hostname in Internet} 
                                        {bob_phone : Phone of the first agent (Bob)} 
                                        {alice_phone : Phone of the secondary agent (Alice)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a workspace in Twilio for routing calls to a call center of 2 agents';

    private $_twilioClient;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $twilioClient)
    {
        parent::__construct();
        $this->_twilioClient = $twilioClient;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Create workspace.");
        $this->line("- Server: \t{$this->argument('host')}");
        $this->line("- Bob phone: \t{$this->argument('bob_phone')}");
        $this->line("- Alice phone: \t{$this->argument('alice_phone')}");

        //Get the configuration
        $workspaceConfig = $this->createWorkspaceConfig();

        //Create the workspace
        $params = array();
        $params['friendlyName'] = $workspaceConfig->name;
        $params['eventCallbackUrl'] = $workspaceConfig->event_callback;
        $workspace = new WorkspaceFacade($this->_twilioClient->taskrouter, $params);
        $this->addWorkersToWorkspace($workspace, $workspaceConfig);
        $this->addTaskQueuesToWorkspace($workspace, $workspaceConfig);
        $workflow = $this->addWorkflowToWorkspace($workspace, $workspaceConfig);

        $this->printSuccessAndInstructions($workspace, $workflow);
    }

    /**
     * Get the json configuration of the Workspace
     *
     * @return mixed
     */
    public function createWorkspaceConfig()
    {
        $fileContent = File::get("resources/workspace.json");
        $interpolatedContent = sprintfn($fileContent, $this->argument());
        return json_decode($interpolatedContent);
    }

    /**
     * Add workers to workspace
     * @param $workspace
     * @param $workspaceConfig
     */
    function addWorkersToWorkspace($workspace, $workspaceConfig)
    {
        $this->line("Add Workers.");
        $idleActivity = $workspace->findActivityByName("Idle")
        or die("The activity 'Idle' was not found. Workers cannot be added");
        foreach ($workspaceConfig->workers as $workerJson) {
            $params = array();
            $params['friendlyName'] = $workerJson->name;
            $params['activitySid'] = $idleActivity->sid;
            $params['attributes'] = json_encode($workerJson->attributes);
            $workspace->addWorker($params);
        }
    }

    /**
     * Add the Task Queues to the workspace
     * @param $workspace
     * @param $workspaceConfig
     */
    function addTaskQueuesToWorkspace($workspace, $workspaceConfig)
    {
        $this->line("Add Task Queues.");
        $reservedActivity = $workspace->findActivityByName("Reserved");
        $assignmentActivity = $workspace->findActivityByName("Busy");
        foreach ($workspaceConfig->task_queues as $taskQueueJson) {
            $params = array();
            $params['friendlyName'] = $taskQueueJson->name;
            $params['targetWorkers'] = $taskQueueJson->targetWorkers;
            $params['reservationActivitySid'] = $reservedActivity->sid;
            $params['assignmentActivitySid'] = $assignmentActivity->sid;
            $workspace->addTaskQueue($params);
        }
    }

    /**
     * Create and configure the workflow to use in the workspace
     * @param $workspace
     * @param $workspaceConfig
     */
    function addWorkflowToWorkspace($workspace, $workspaceConfig)
    {
        $this->line("Add Worflow.");
        $workflowJson = $workspaceConfig->workflow;
        $params = array();
        $params['friendlyName'] = $workflowJson->name;
        $params['assignmentCallbackUrl'] = $workflowJson->callback;
        $params['fallbackAssignmentCallbackUrl'] = $workflowJson->callback;
        $params['taskReservationTimeout'] = $workflowJson->timeout;
        $params['configuration'] = $this->createWorkFlowJsonConfig($workspace, $workflowJson);
        return $workspace->addWorkflow($params);
    }

    /**
     * Create the workflow configuration in json format
     * @param $workspace
     * @param $workspaceConfig
     * @return string configuration of workflow in json format
     */
    function createWorkFlowJsonConfig($workspace, $workspaceConfig)
    {
        $params = array();
        $defaultTaskQueue = $workspace->findTaskQueueByName("Default")
        or die("The 'Default' task queue was not found. The Workflow cannot be created.");
        $smsTaskQueue = $workspace->findTaskQueueByName("SMS")
        or die("The 'SMS' task queue was not found. The Workflow cannot be created.");
        $voiceTaskQueue = $workspace->findTaskQueueByName("Voice")
        or die("The 'Voice' task queue was not found. The Workflow cannot be created.");

        $params["default_task_queue_sid"] = $defaultTaskQueue->sid;
        $params["sms_task_queue_sid"] = $smsTaskQueue->sid;
        $params["voice_task_queue_sid"] = $voiceTaskQueue->sid;

        $fileContent = File::get("resources/workflow.json");
        $interpolatedContent = sprintfn($fileContent, $params);
        return $interpolatedContent;
    }

    /**
     * Prints the message indicating the workspace was successfully created and
     * shows the commands to export the workspace variables into the environment.
     * @param $workspace
     * @param $workflow
     */
    function printSuccessAndInstructions($workspace, $workflow)
    {
        $idleActivity = $workspace->findActivityByName("Idle")
        or die("Somehow the activity 'Idle' was not found.");
        $successMsg = "Workspace \"{$workspace->friendlyName}\" was created successfully.";
        $this->printTitle($successMsg);
        $this->line("You need to set the following environment vars:");
        $this->warn("export WORKFLOW_SID={$workflow->sid}");
        $this->warn("export POST_WORK_ACTIVITY_SID={$idleActivity->sid}");
    }

    /**
     * Prints a text separated up and down by a token based line, usually "*"
     */
    function printTitle($text)
    {
        $lineLength = strlen($text) + 2;
        $this->line(str_repeat("*", $lineLength));
        $this->line(" $text ");
        $this->line(str_repeat("*", $lineLength));
    }
}