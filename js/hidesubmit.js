$(window).on("load", function(){

    // The targetFields array should be defined by the PHP script
    // based on the fields in the current instrument that contain
    // the @HIDESUBMIT action tag.

    function hideBtn(targetFields) {
        // This is the main function that hides the submit button
        // if any of the fields in the targetFields array is visible
        hide = 0;
        targetFields.forEach(field => {
            if ($('#' + field + '-tr').is(':visible')) {
                hide += 1;
            };
        });
        if (hide > 0) {
            $('button[name="submit-btn-saverecord"]').hide();
        } else {
            $('button[name="submit-btn-saverecord"]').show();
        };
    };

    // Start by hiding first if needed.
    hideBtn(targetFields);

    const callback = function(mutation, observer) {
        hideBtn(targetFields);
    };

    // Create an observer instance linked to the callback function 
    const observer = new MutationObserver(callback);

    // Start observing the target node for attribute mutations 
    // Do for each node if it is on the current page.
    targetFields.forEach(field => {
        const node = document.getElementById(field+'-tr');
        if (node){
            observer.observe(node, {attributes: true});
        }
    });

});
