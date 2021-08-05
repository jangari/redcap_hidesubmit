<?php namespace INTERSECT\HideSubmit;

use \REDCap as REDCap;

class HideSubmit extends \ExternalModules\AbstractExternalModule {

protected static $Tags = array(
            '@HIDESUBMIT' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides the Submit or Next Page button on a survey <em>and</em> all Save record buttons on a data entry form <em>if the field is visible due to branching logic</em>. <br/>Typically this action tag would be added to a descriptive text field that is displayed when some condition should prevent the respondent from submitting, or prevent the user from saving. E.g., \'Required fields missing!\' or \'Age must be greater than 18 years to continue\'.'),
            '@HIDESUBMIT-FORM' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides all Save record buttons on a data entry form <em>if the field is visible due to branching logic</em>. <br/>Typically this action tag would be added to a descriptive text field that is displayed when some condition should prevent the respondent from submitting, or prevent the user from saving. E.g., \'Required fields missing!\' or \'Age must be greater than 18 years to continue\'.'),
            '@HIDESUBMIT-SURVEY' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides the Submit or Next Page button on a survey <em>if the field is visible due to branching logic</em>. <br/>Typically this action tag would be added to a descriptive text field, and branched on some logic that should prevent the user from submitting the survey or saving the record. E.g., \'Required fields missing!\' or \'Age must be greater than 18 years to continue\'.'),
        );

protected function makeTagTR($tag, $description) {
                global $isAjax, $lang;
                return \RCView::tr(array(),
			\RCView::td(array('class'=>'nowrap', 'style'=>'text-align:center;background-color:#f5f5f5;color:#912B2B;padding:7px 15px 7px 12px;font-weight:bold;border:1px solid #ccc;border-bottom:0;border-right:0;'),
				((!$isAjax || (isset($_POST['hideBtns']) && $_POST['hideBtns'] == '1')) ? '' :
					\RCView::button(array('class'=>'btn btn-xs btn-rcred', 'style'=>'', 'onclick'=>"$('#field_annotation').val(trim('".js_escape($tag)." '+$('#field_annotation').val())); highlightTableRowOb($(this).parentsUntil('tr').parent(),2500);"), $lang['design_171'])
				)
			) .
			\RCView::td(array('class'=>'nowrap', 'style'=>'background-color:#f5f5f5;color:#912B2B;padding:7px;font-weight:bold;border:1px solid #ccc;border-bottom:0;border-left:0;border-right:0;'),
				$tag
			) .
			\RCView::td(array('style'=>'line-height:1.3;font-size:13px;background-color:#f5f5f5;padding:7px;border:1px solid #ccc;border-bottom:0;border-left:0;'),
				'<i class="fas fa-cube mr-1"></i>'.$description
			)
		);

}

public function redcap_every_page_before_render($project_id) {
    if (PAGE==='Design/action_tag_explain.php') {
        global $lang;
        $lastActionTagDesc = end(\Form::getActionTags());

        // which $lang element is this?
        $langElement = array_search($lastActionTagDesc, $lang);

        foreach (static::$Tags as $tag => $tagAttr) {
            $lastActionTagDesc .= "</td></tr>";
            $lastActionTagDesc .= $this->makeTagTR($tag, $tagAttr['description']);
        }
        $lang[$langElement] = rtrim(rtrim(rtrim(trim($lastActionTagDesc), '</tr>')),'</td>');
    }
}


    function getTags($tag) {
        // This is straight out of Andy Martin's example post on this:
        // https://community.projectredcap.org/questions/32001/custom-action-tags-or-module-parameters.html
        if (!class_exists('INTERSECT\HideSubmit\ActionTagHelper')) include_once('classes/ActionTagHelper.php');
        $action_tag_results = ActionTagHelper::getActionTags($tag);
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
        echo "<script type=\"text/javascript\" src=\"" . $this->getUrl('js/hidesubmit_form.js'). "\"></script>";
    }
}
