$(document).ready(function(){
    $(function(){

        // Start by hiding everything immediately to cut down on any lag.
        // If they need to be, they will be shown later.
        $('button[name="submit-btn-saverecord"]').hide();
        $('button[name="submit-btn-saverepeat"]').hide();
        $('button[name="submit-btn-saverepeat"]').parent().prev().hide();
        $('button[name="submit-btn-saverepeat"]').parent().next().hide();

        // The targetFields array should be defined by the PHP script
        // based on the fields in the current instrument that contain
        // the @HIDESUBMIT action tag.
        // hideSubmitFields = ['missed_error', 'age_error'];
        // hideRepeatFields = ['repeathidden'];

        function hideBtn(hideSubmitFields,hideRepeatFields) {
            // This is the main function that hides the submit button
            // if any of the fields in the targetFields array is visible
            hideSubmit = 0;
            hideRepeat = 0;
            hideSubmitFields.forEach(function(field) {
                if ($('#' + field + '-tr').is(':visible')) {
                    hideSubmit += 1;
                };
            });
            hideRepeatFields.forEach(function(field) {
                if ($('#' + field + '-tr').is(':visible')) {
                    hideRepeat += 1;
                }
            });

                if (hideSubmit > 0) {
                    $('button[name="submit-btn-saverecord"]').hide();
                } else {
                    $('button[name="submit-btn-saverecord"]').show();
                };
                if($('button[name="submit-btn-saverepeat"]').length){
                    if (hideRepeat > 0) {
                        $('button[name="submit-btn-saverepeat"]').hide();
                        $('button[name="submit-btn-saverepeat"]').parent().prev().hide();
                    } else {
                        $('button[name="submit-btn-saverepeat"]').show();
                        $('button[name="submit-btn-saverepeat"]').parent().prev().show();
                    };
                };
                if (hideRepeat + hideSubmit == 0) {
                    $('button[name="submit-btn-saverepeat"]').parent().next().show();
                } else {
                    $('button[name="submit-btn-saverepeat"]').parent().next().hide();
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
        targetFields.forEach(function(field) {
            const node = document.getElementById(field+'-tr');
            if (node){
                observer.observe(node, {attributes: true});
            }
        });

    });
});
