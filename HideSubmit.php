<?php namespace INTERSECT\HideSubmit;

use \REDCap as REDCap;

class HideSubmit extends \ExternalModules\AbstractExternalModule {

protected static $Tags = array(
    '@HIDESUBMIT' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides all save buttons on data entry forms and both the Submit/Next Page button and \'Take this survey again\' button on surveys <em>if the field is visible due to branching logic</em>.'),
    '@HIDESUBMIT-FORM' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides all save buttons on data entry forms <em>if the field is visible due to branching logic</em>.'), 
    '@HIDESUBMIT-SURVEY' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides the Submit/Next Page button and the \'Take this survey again\' button on surveys <em>if the field is visible due to branching logic</em>.'),
    '@HIDESUBMITONLY' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides all save buttons on data entry forms except for \'Save & Add New Instance\', and the Submit/Next Page button on surveys <em>if the field is visible due to branching logic</em>.'), 
    '@HIDESUBMITONLY-FORM' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides all save buttons except for \'Save & Go To Next Instance\' on data entry forms <em>if the field is visible due to branching logic</em>.'),
    '@HIDESUBMITONLY-SURVEY' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides the Submit/Next Page button on surveys <em>if the field is visible due to branching logic</em>.'),
    '@HIDEREPEAT' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides the \'Save & Go To Next Instance\' button on data entry forms and the \'Take this survey again\' button on surveys <em>if the field is visible due to branching logic</em>.'), 
    '@HIDEREPEAT-FORM' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides the \'Save & Go To Next Instance\' button on data entry forms <em>if the field is visible due to branching logic</em>.'), 
    '@HIDEREPEAT-SURVEY' => array('description'=>'HIDESUBMIT Action Tags<br/>Hides the \'Take this survey again\' button on surveys <em>if the field is visible due to branching logic</em>.'), 
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

    function redcap_survey_page_top($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance) {

        // Get array of fields in current instruments
        $currInstrumentFields = REDCap::getFieldNames($instrument);

        // Begin Submit block
        $hideSubmitTags = array("@HIDESUBMIT","@HIDESUBMIT-SURVEY","@HIDESUBMITONLY","@HIDESUBMITONLY-SURVEY");
        $hideSubmitFields = array();

        foreach ($hideSubmitTags as $tag){
            $fields = $this->getTags($tag);
            $fields = array_keys($fields[$tag]);
            $hideSubmitFields = array_merge((array)$hideSubmitFields,(array)$fields); 
        };

        $hideSubmitFields = array_values(array_intersect((array)$hideSubmitFields, (array)$currInstrumentFields));
        // End Submit block

        // Begin Repeat block
        $hideRepeatTags = array("@HIDESUBMIT","@HIDESUBMIT-SURVEY","@HIDEREPEAT","@HIDEREPEAT-SURVEY");
        $hideRepeatFields = array();

        foreach ($hideRepeatTags as $tag){
            $fields = $this->getTags($tag);
            $fields = array_keys($fields[$tag]);
            $hideRepeatFields = array_merge((array)$hideRepeatFields,(array)$fields); 
        };

        $hideRepeatFields = array_values(array_intersect((array)$hideRepeatFields, (array)$currInstrumentFields));
        // End Repeat block

        // Decide whether to continue
        if (count($hideSubmitFields) + count($hideRepeatFields) === 0) { 
            return; 
        }

        // Create a JS array to feed into our JS script
        echo "<script>const hideSubmitFields = [];";
        for ($i = 0; $i < count($hideSubmitFields); $i++){
            // Push each field to the JS array
            echo "hideSubmitFields.push('". $hideSubmitFields[$i] ."');";
        }
        echo "const hideRepeatFields = [];";
        for ($i = 0; $i < count($hideRepeatFields); $i++){
            // Push each field to the JS array
            echo "hideRepeatFields.push('". $hideRepeatFields[$i] ."');";
        }
        echo "</script>";
        echo "<script type=\"text/javascript\" src=\"" . $this->getUrl('js/hidesubmit.js'). "\"/></script>";
    }

    function redcap_data_entry_form_top($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance)
    {
        // Get array of fields in current instruments
        $currInstrumentFields = REDCap::getFieldNames($instrument);

        // Begin Submit block
        $hideSubmitTags = array("@HIDESUBMIT","@HIDESUBMIT-FORM","@HIDESUBMITONLY","@HIDESUBMITONLY-FORM");
        $hideSubmitFields = array();

        foreach ($hideSubmitTags as $tag){
            $fields = $this->getTags($tag);
            $fields = array_keys($fields[$tag]);
            $hideSubmitFields = array_merge((array)$hideSubmitFields,(array)$fields); 
        };

        $hideSubmitFields = array_values(array_intersect((array)$hideSubmitFields, (array)$currInstrumentFields));
        // End Submit block

        // Begin Repeat block
        $hideRepeatTags = array("@HIDESUBMIT","@HIDESUBMIT-FORM","@HIDEREPEAT","@HIDEREPEAT-FORM");
        $hideRepeatFields = array();

        foreach ($hideRepeatTags as $tag){
            $fields = $this->getTags($tag);
            $fields = array_keys($fields[$tag]);
            $hideRepeatFields = array_merge((array)$hideRepeatFields,(array)$fields); 
        };

        $hideRepeatFields = array_values(array_intersect((array)$hideRepeatFields, (array)$currInstrumentFields));
        // End Repeat block

        // Decide whether to continue
        if (count($hideSubmitFields) + count($hideRepeatFields) === 0) { 
            return; 
        }

        // Create a JS array to feed into our JS script
        echo "<script>const hideSubmitFields = [];";
        for ($i = 0; $i < count($hideSubmitFields); $i++){
            // Push each field to the JS array
            echo "hideSubmitFields.push('". $hideSubmitFields[$i] ."');";
        }
        echo "const hideRepeatFields = [];";
        for ($i = 0; $i < count($hideRepeatFields); $i++){
            // Push each field to the JS array
            echo "hideRepeatFields.push('". $hideRepeatFields[$i] ."');";
        }
        echo "</script>";
        echo "<script type=\"text/javascript\" src=\"" . $this->getUrl('js/hidesubmit_form.js'). "\"></script>";
    }
}
