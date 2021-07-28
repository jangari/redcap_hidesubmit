<?php namespace INTERSECT\HideSubmit;

use \REDCap as REDCap;

class HideSubmit extends \ExternalModules\AbstractExternalModule {

    function getTags($tag) {
        // This is straight out of Andy Martin's example post on this:
        // https://community.projectredcap.org/questions/32001/custom-action-tags-or-module-parameters.html
        if (!class_exists('\Stanford\Utility\ActionTagHelper')) include_once('classes/ActionTagHelper.php');
        $action_tag_results = ActionTagHelper::getActionTags($tag);
        /* print "<pre>From tag helper function:<br/>"; */
        /* print_r($action_tag_results); */
        /* print "</pre>"; */
        return $action_tag_results;
    }

    function redcap_survey_page_top($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance)
    {
        // Tag to watch for
        $tag = "@HIDESUBMIT";
        $tag_survey = "@HIDESUBMIT-SURVEY";

        // Get an array of fields that contain either tag
        $actionTags = $this->getTags($tag);
        $fields = array_keys($actionTags[$tag]);
        $actionTagsSurvey = $this->getTags($tag_survey);
        $fieldsSurvey = array_keys($actionTagsSurvey[$tag_survey]);

        // Join the two arrays returning all fields with either tag
        $allFields = array_unique(array_merge((array)$fields,(array)$fieldsSurvey));

        // Get the fields from the current instrument
        $currInstrumentFields = REDCap::getFieldNames($instrument);

        // Compute the intersection of both those arrays to only return
        // fields with the action tag in the current instrument.
        $targetFields = array_values(array_intersect((array)$allFields, (array)$currInstrumentFields));

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
        echo "<script type=\"text/javascript\" src=\"" . $this->getUrl('js/hidesubmit.js'). "\"/></script>";
    }

    function redcap_data_entry_form_top($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance)
    {
        /* print "Working?"; */
        // Tag to watch for
        $tag = "@HIDESUBMIT";
        $tag_form = "@HIDESUBMIT-FORM";

        // Get an array of fields that contain either tag
        $actionTags = $this->getTags($tag);
        $fields = array_keys($actionTags[$tag]);
        $actionTagsForm = $this->getTags($tag_form);
        $fieldsForm = array_keys($actionTagsForm[$tag_form]);

        // Join the two arrays returning all fields with either tag
        $allFields = array_unique(array_merge((array)$fields,(array)$fieldsForm));

        // Get the fields from the current instrument
        $currInstrumentFields = REDCap::getFieldNames($instrument);

        // Compute the intersection of both those arrays to only return
        // fields with the action tag in the current instrument.
        $targetFields = array_values(array_intersect((array)$allFields, (array)$currInstrumentFields));

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
        echo "<script type=\"text/javascript\" src=\"" . $this->getUrl('js/hidesubmit.js'). "\"></script>";
    }
}
