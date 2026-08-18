<?php

namespace App\Services;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowStep;
use Illuminate\Support\Str;

class BpmnEngineService
{
    /**
     * Export WorkflowDefinition & Steps to standard BPMN 2.0 XML.
     */
    public function exportToBpmnXml(WorkflowDefinition $definition): string
    {
        $processId = "Process_" . Str::slug($definition->code, '_');
        $xml = new \SimpleXMLElement('<bpmn:definitions xmlns:bpmn="http://www.omg.org/spec/BPMN/20100524/MODEL" xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI" xmlns:dc="http://www.omg.org/spec/DD/20100524/DC" id="Definitions_1" targetNamespace="http://bpmn.io/schema/bpmn"/>');

        $process = $xml->addChild('bpmn:process');
        $process->addAttribute('id', $processId);
        $process->addAttribute('name', $definition->name);
        $process->addAttribute('isExecutable', 'true');

        // 1. Start Event
        $startEvent = $process->addChild('bpmn:startEvent');
        $startEvent->addAttribute('id', 'StartEvent_1');
        $startEvent->addAttribute('name', 'Request Submitted');

        $previousElementId = 'StartEvent_1';
        $flowCounter = 1;

        // 2. User Tasks for each Step
        foreach ($definition->steps as $index => $step) {
            $taskId = "Activity_Step_" . $step->id;
            $taskNode = $process->addChild('bpmn:userTask');
            $taskNode->addAttribute('id', $taskId);
            $taskNode->addAttribute('name', $step->name);

            // Sequence Flow from previous to current
            $flowId = "Flow_" . $flowCounter++;
            $flow = $process->addChild('bpmn:sequenceFlow');
            $flow->addAttribute('id', $flowId);
            $flow->addAttribute('sourceRef', $previousElementId);
            $flow->addAttribute('targetRef', $taskId);

            $previousElementId = $taskId;
        }

        // 3. End Event
        $endEvent = $process->addChild('bpmn:endEvent');
        $endEvent->addAttribute('id', 'EndEvent_1');
        $endEvent->addAttribute('name', 'Workflow Completed');

        $finalFlow = $process->addChild('bpmn:sequenceFlow');
        $finalFlow->addAttribute('id', "Flow_" . $flowCounter);
        $finalFlow->addAttribute('sourceRef', $previousElementId);
        $finalFlow->addAttribute('targetRef', 'EndEvent_1');

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }

    /**
     * Import BPMN 2.0 XML Content and create a WorkflowDefinition with steps.
     */
    public function importFromBpmnXml(string $xmlContent, int $userId): WorkflowDefinition
    {
        $xml = simplexml_load_string($xmlContent);
        if (!$xml) {
            throw new \InvalidArgumentException("Invalid BPMN 2.0 XML string provided.");
        }

        $xml->registerXPathNamespace('bpmn', 'http://www.omg.org/spec/BPMN/20100524/MODEL');
        $processes = $xml->xpath('//bpmn:process');

        if (empty($processes)) {
            throw new \InvalidArgumentException("No valid <bpmn:process> element found in XML.");
        }

        $processNode = $processes[0];
        $name = (string) ($processNode['name'] ?? 'Imported BPMN Process');
        $code = 'BPMN-' . strtoupper(Str::random(6));

        $definition = WorkflowDefinition::create([
            'name' => $name,
            'code' => $code,
            'category' => 'general',
            'description' => 'Imported via BPMN 2.0 XML engine',
            'version' => 1,
            'is_active' => true,
            'sla_hours' => 48,
            'created_by' => $userId,
        ]);

        // Extract User Tasks
        $tasks = $processNode->xpath('.//bpmn:userTask');
        $order = 1;

        foreach ($tasks as $task) {
            WorkflowStep::create([
                'workflow_definition_id' => $definition->id,
                'step_order' => $order++,
                'name' => (string) $task['name'] ?: 'Step ' . $order,
                'type' => 'approval',
                'assignee_type' => 'manager',
                'sla_hours' => 24,
            ]);
        }

        if (empty($tasks)) {
            // Create default step if XML had generic tasks
            WorkflowStep::create([
                'workflow_definition_id' => $definition->id,
                'step_order' => 1,
                'name' => 'Initial Review Step',
                'type' => 'approval',
                'assignee_type' => 'manager',
                'sla_hours' => 24,
            ]);
        }

        return $definition;
    }
}
