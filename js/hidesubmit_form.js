$(document).ready(function(){
    $(function(){

        // Start by hiding everything immediately to cut down on any lag.
        // If they need to be, they will be shown later.
        $('button[name="submit-btn-saverecord"]').hide();
        $('button[id="submit-btn-savecontinue"]').hide();
        $('button[id="submit-btn-savenextrecord"]').hide();
        $('button[id="submit-btn-savenextform"]').hide();
        $('button[id="submit-btn-savecompresp"]').hide();
        $('button[id="submit-btn-saveexitrecord"]').hide();
        $('button[id="submit-btn-placeholder"]').hide();
        $('a[id="submit-btn-savenextrecord"]').hide();
        $('a[id="submit-btn-savenextform"]').hide();
        $('a[id="submit-btn-savecompresp"]').hide();
        $('a[id="submit-btn-saveexitrecord"]').hide();
        $('a[id="submit-btn-savecontinue"]').hide();
        $('button[id="submit-btn-dropdown"]').hide();
        $('button[id="submit-btn-savenextinstance"]').hide();
        $('a[id="submit-btn-savenextinstance"]').hide();

        // The targetFields array should be defined by the PHP script
        // based on the fields in the current instrument that contain
        // the @HIDESUBMIT action tag.
        // hideSubmitFields = ['missed_error', 'age_error'];
        // hideRepeatFields = ['missed_error', 'age_error','repeathidden'];
        containsRpt = false;

        if ($('a[id="submit-btn-savenextinstance"]').length + $('button[id="submit-btn-savenextinstance"]').length){
            containsRpt = true;
        };
        function hideBtn(hideSubmitFields,hideRepeatFields) {
            // This is the main function that hides the submit button
            // if any of the fields in the targetFields array is visible
            hideSubmit = 0;
            hideSubmitFields.forEach(field => {
                if ($('#' + field + '-tr').is(':visible')) {
                    hideSubmit += 1;
                };
            });

            if (hideSubmit) {
                $('button[name="submit-btn-saverecord"]').hide();
                $('button[id="submit-btn-savecontinue"]').hide();
                $('button[id="submit-btn-savenextrecord"]').hide();
                $('button[id="submit-btn-savenextform"]').hide();
                $('button[id="submit-btn-savecompresp"]').hide();
                $('button[id="submit-btn-saveexitrecord"]').hide();
                $('button[id="submit-btn-placeholder"]').hide();
                $('a[id="submit-btn-savenextrecord"]').hide();
                $('a[id="submit-btn-savenextform"]').hide();
                $('a[id="submit-btn-savecompresp"]').hide();
                $('a[id="submit-btn-saveexitrecord"]').hide();
                $('a[id="submit-btn-savecontinue"]').hide();
                $('button[id="submit-btn-dropdown"]').hide();
            } else {
                $('button[name="submit-btn-saverecord"]').show();
                $('button[id="submit-btn-savecontinue"]').show();
                $('button[id="submit-btn-savenextrecord"]').show();
                $('button[id="submit-btn-savenextform"]').show();
                $('button[id="submit-btn-savecompresp"]').show();
                $('button[id="submit-btn-saveexitrecord"]').show();
                $('button[id="submit-btn-placeholder"]').show();
                $('a[id="submit-btn-savenextrecord"]').show();
                $('a[id="submit-btn-savenextform"]').show();
                $('a[id="submit-btn-savecompresp"]').show();
                $('a[id="submit-btn-saveexitrecord"]').show();
                $('a[id="submit-btn-savecontinue"]').show();
                $('button[id="submit-btn-dropdown"]').show();
            };

            if (containsRpt) {
                hideRepeat = 0;
                hideRepeatFields.forEach(field => {
                    if ($('#' + field + '-tr').is(':visible')) {
                        hideRepeat += 1;
                    };
                });
                if (hideRepeat) {
                    // Determine if save and add instance button is active
                    if ($('button[id="submit-btn-savenextinstance"]').length) {
                        $('button[id="submit-btn-savenextinstance"]').hide();
                    } else {
                        $('a[id="submit-btn-savenextinstance"]').hide();
                    };
                } else {
                    if ($('button[id="submit-btn-savenextinstance"]').length) {
                        $('button[id="submit-btn-savenextinstance"]').show();
                    } else {
                        $('a[id="submit-btn-savenextinstance"]').show();
                        $('button[id="submit-btn-dropdown"]').show();
                    };
                };
            };
        };

        // Start by hiding first if needed.
        hideBtn(hideSubmitFields,hideRepeatFields);

        const callback = function(mutation, observer) {
            hideBtn(hideSubmitFields,hideRepeatFields);
        };

        // Create an observer instance linked to the callback function 
        const observer = new MutationObserver(callback);

        // Start observing the target node for attribute mutations 
        // Do for each node if it is on the current page.
        targetFields = hideSubmitFields.concat(hideRepeatFields);
        targetFields.forEach(field => {
            const node = document.getElementById(field+'-tr');
            if (node){
                observer.observe(node, {attributes: true});
            }
        });

    });
});
