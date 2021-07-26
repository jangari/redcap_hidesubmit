<?php namespace INTERSECT\HideSubmit;

use \REDCap as REDCap;

class HideSubmit extends \ExternalModules\AbstractExternalModule {

    function getTags($tag) {
        // This is straight out of Andy Martin's example post on this:
        // https://community.projectredcap.org/questions/32001/custom-action-tags-or-module-parameters.html
        if (!class_exists('\Stanford\Utility\ActionTagHelper')) include_once('classes/ActionTagHelper.php');
        $action_tag_results = ActionTagHelper::getActionTags($tag);

        return $action_tag_results;
    }

    function redcap_survey_page_top($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance)
    {
        // Tag to watch for
        $tag = "@HIDESUBMIT";

        // Get an array of fields that contain this tag
        $actionTags = $this->getTags($tag);
        $fields = array_keys($actionTags[$tag]);

        // Get the fields from the current instrument
        $currInstrumentFields = REDCap::getFieldNames($instrument);

        // Compute the intersection of both those arrays to only return
        // fields with the action tag in the current instrument.
        $targetFields = array_intersect($fields, $currInstrumentFields);

        // Don't bother if there aren't any fields
        if (count($targetFields) === 0) { 
            return; 
        }

        // Create a JS array to feed into our JS script
        echo "<script>const targetFields = [];";
        for ($i = 0; $i < count($targetFields); $i++){
            // Push each field to the JS array
            echo "targetFields.push('". $targetFields[$i] ."');";
        }
        echo "</script>";
        echo "<script>console.log(targetFields);</script>";
        echo "<script type=\"text/javascript\" src=\"" . $this->getUrl('js/hidesubmit.js'). "></script>";
    }
}
