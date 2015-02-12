// BEWARE: this clears the $ variable!
var $j = jQuery.noConflict(),
    qcubed,
    qc;

$j.fn.extend({
    wait: function(time, type) {
        time = time || 1000;
        type = type || "fx";
        return this.queue(type, function() {
            var self = this;

            setTimeout(function() {
                $j(self).dequeue();
            }, time);
        });
    }
});

/**
 * Queued Ajax requests.
 * A new Ajax request won't be started until the previous queued
 * request has finished.
 * @param object o Options.
 */
$j.ajaxQueue = function(o) {
    if (typeof $j.ajaxq === "undefined") {
        $j.ajax(o);
    } else {
        // see http://code.google.com/p/jquery-ajaxq/ for details
        $j.ajaxq("qcu.be", o);
    }
};

/**
 * Synced Ajax requests.
 * The Ajax request will happen as soon as you call this method, but
 * the callbacks (success/error/complete) won't fire until all previous
 * synced requests have been completed.
 * @param object o Options.
 * @return object The callback.
 * @deprecacted Core no longer uses this. Uses ajaxq instead.
 */
$j.ajaxSync = function(o) {
    var fn = $j.ajaxSync.fn,
        data = $j.ajaxSync.data;

    pos = fn.length;

    fn[ pos ] = {
        error: o.error,
        success: o.success,
        complete: o.complete,
        done: false
    };

    data[ pos ] = {
        error: [],
        success: [],
        complete: []
    };

    o.error = function() {
        data[ pos ].error = arguments;
    };
    o.success = function() {
        data[ pos ].success = arguments;
    };
    o.complete = function() {
        var i;

        data[ pos ].complete = arguments;
        fn[ pos ].done = true;

        if (pos === 0 || !fn[ pos - 1 ])
            for (i = pos; i < fn.length && fn[i].done; i++) {
                if (fn[i].error) {
                    fn[i].error.apply($j, data[i].error);
                }
                if (fn[i].success) {
                    fn[i].success.apply($j, data[i].success);
                }
                if (fn[i].complete) {
                    fn[i].complete.apply($j, data[i].complete);
                }

                fn[i] = null;
                data[i] = null;
            }
    };

    return $j.ajax(o);
};

$j.ajaxSync.fn = [];
$j.ajaxSync.data = [];

/**
 * @namespace qcubed
 */
qcubed = {
    /**
     * @param {string} strControlId
     * @param {string} strProperty
     * @param {string} strNewValue
     */
    recordControlModification: function(strControlId, strProperty, strNewValue) {
        if (!qcubed.controlModifications[strControlId]) {
            qcubed.controlModifications[strControlId] = {};
        }
        qcubed.controlModifications[strControlId][strProperty] = strNewValue;
    },
    /**
     * Given a control, returns the correct index to use in the formObjsModified array.
     * @param ctl
     * @private
     */
    _formObjChangeIndex: function (ctl) {
        var id = $j(ctl).attr('id');
        var strType = $j(ctl).prop("type");
        var name = $j(ctl).attr("name");
        var ret;

        if (((strType === 'checkbox') || (strType === 'radio')) &&
           id && ((indexOffset = id.lastIndexOf('_')) >= 0)) { // a member of a control list
            return id.substr(0, indexOffset); // use the id of the group
        }
        else if (id && strType === 'radio' && name !== id) { // a radio button with a group name
            return id; // these buttons are changed individually
        }
        else if (strType === 'hidden') { // a hidden field, possibly associated with a different widget
            if ((indexOffset = id.lastIndexOf('_')) >= 0) {
                return id.substr(0, indexOffset); // use the id of the parent control
            }
            return name;
        }
        return id;
    },
    /**
     * Records that a control has changed in order to synchronize the control with
     * the php version on the next request.
     * @param event
     */
    formObjChanged: function (event) {
        var ctl = event.target;
        var id = qc._formObjChangeIndex(ctl);
        var strType = $j(ctl).prop("type");
        var name = $j(ctl).attr("name");

        if (strType === 'radio' && name !== id) { // a radio button with a group name
            // since html does not submit a changed event on the deselected radio, we are going to invalidate all the controls in the group
            var group = $j('input[name=' + name + ']');
            group.each(function () {
                id = $j(this).attr('id');
                qcubed.formObjsModified[id] = true;
            });
        }
        else {
            qcubed.formObjsModified[id] = true;
        }
    },
    /**
     * Initialize form related scripts
     * @param strFormId
     */
    initForm: function (strFormId) {
        // Allow any control to trigger a change and post of its data.
        // Particularly useful for custom controls that use hidden inputs to transfer data
        $j('#' + strFormId).on ('qformObjChanged', this.formObjChanged);
    },

    /**
     * @param {string} strForm The QForm Id, gets overwritten.
     * @param {string} strControl The Control Id.
     * @param {string} strEvent The Event.
     * @param {mixed} mixParameter
     */
    postBack: function(strForm, strControl, strEvent, mixParameter) {
        var $objForm;

        strForm = $j("#Qform__FormId").val();
        $objForm = $j('#' + strForm);

        if (mixParameter && (typeof mixParameter !== "string")) {
            mixParameter = $j.param({Qform__FormParameter: mixParameter});
            $objForm.append('<input type="hidden" name="Qform__FormParameterType" value="obj">');
        }

        $j('#Qform__FormControl').val(strControl);
        $j('#Qform__FormEvent').val(strEvent);
        $j('#Qform__FormParameter').val(mixParameter);
        $j('#Qform__FormCallType').val("Server");
        $j('#Qform__FormUpdates').val(this.formUpdates());
        $j('#Qform__FormCheckableControls').val(this.formCheckableControls(strForm, "Server"));

        // have $j trigger the submit event (so it can catch all submit events)
        $objForm.trigger("submit");
    },

    /**
     * Return the updates to properties in form objects. Note that once you call this, you MUST post the data returned, as this
     * code has the side effect of resetting the update cache.
     * @return {string} The form's control modifications.
     */
    formUpdates: function() {
        var strToReturn = "",
            strControlId,
            strProperty;

        for (strControlId in qcubed.controlModifications) {
            for (strProperty in qcubed.controlModifications[strControlId]) {
                strToReturn += strControlId + " " + strProperty + " " + qcubed.controlModifications[strControlId][strProperty] + "\n";
            }
        }
        qcubed.controlModifications = {};
        return strToReturn;
    },

    /**
     * @param {string} strForm The QForm Id
     * @param {string} strCallType Server or Ajax
     * @return {string}
     */
    formCheckableControls: function(strForm, strCallType) {
        // Select the QCubed Form
        var objFormElements = $j('#' + strForm).find('input'),
            strToReturn = "";

        objFormElements.each(function(i) {
            var $element = $j(this),
                strType = $element.prop("type"),
                strControlId;

            if (((strType === "checkbox") || (strType === "radio")) &&
                ((strCallType === "Ajax") || (!$element.prop("disabled")))) {

                strControlId = $element.attr("id");

                // RadioButtonList or CheckBoxList
                if (strControlId) {
                    if (strControlId.lastIndexOf('_') >= 0) {
                        if (strControlId.lastIndexOf('_0') >= 0) {
                            strToReturn += " " + strControlId.substring(0, strControlId.length - 2);
                        }
                        // Standard Radio or Checkbox
                    } else {
                        strToReturn += " " + strControlId;
                    }
                }
            }
        });

        return (strToReturn.length) ? strToReturn.substring(1) : '';
    },

    /**
     * Gets the data to be sent to an ajax call as post data. Note that once you call this, you MUST post this data, as
     * it has the side effect of resetting the cache of changed objects.
     *
     * @param {string} strForm The Form Id
     * @param {string} strControl The Control Id
     * @param {string} strEvent The Event
     * @param {mixed} mixParameter An array of parameters or a string value.
     * @param {string} strWaitIconControlId Not used, probably legacy code.
     * @return {string} Post Data
     */
    getPostData: function(strForm, strControl, strEvent, mixParameter, strWaitIconControlId) {
        var objFormElements = $j('#' + strForm).find('input,select,textarea'),
            strPostData = '',
            formParamSelector = "#Qform__FormParameter";

        if (mixParameter && (typeof mixParameter !== "string")) {
            strPostData = $j.param({Qform__FormParameter: mixParameter});
            objFormElements = objFormElements.not(formParamSelector);
        } else {
            $j(formParamSelector).val(mixParameter);
        }

        $j('#Qform__FormControl').val(strControl);
        $j('#Qform__FormEvent').val(strEvent);
        $j('#Qform__FormCallType').val("Ajax");
        $j('#Qform__FormUpdates').val(this.formUpdates());
        //$j('#Qform__FormCheckableControls').val(this.formCheckableControls(strForm, "Ajax"));

        objFormElements.each(function() {
            var $element = $j(this),
                strType = $element.prop("type"),
                strControlId = $element.attr("id"),
                strControlName = $element.attr("name"),
                objChangeIndex = qc._formObjChangeIndex($element),
                blnQform,
                index = -1,
                offset,
                strPostValue = $element.val();

            blnQform = (strControlId && (strControlId.substr(0, 7) == 'Qform__'));

            if (strControlId &&
                (strType === 'checkbox' || strType === 'radio') &&
                (offset = strControlId.lastIndexOf('_')) != -1) {
                // A control group
                index = strControlId.substr (offset + 1);
                strControlId = strControlId.substr (0, offset);
            }

            if (!qcubed.inputSupport || // if not oninput support, then post all the controls, rather than just the modified ones
                qcubed.ajaxError || // Ajax error would mean that formObjsModified is invalid. We need to submit everything.
                (objChangeIndex && qcubed.formObjsModified[objChangeIndex]) ||
                blnQform   // all controls with Qform__ at the beginning of the id are always posted
            /* || strType == 'hidden'*/) {
                switch (strType) {
                    case "checkbox":
                        if (index >= 0) {
                            if ($element.is(":checked")) {
                                strPostData += "&" + strControlName + "=" + $element.is(":checked");
                            }
                        } else {
                            strPostData += "&" + strControlName + "=" + $element.is(":checked");
                        }
                        break;

                    case "radio":
                        if (index >= 0) {
                            if ($element.is(":checked")) {
                                strPostData += "&" + strControlName + "=" + index;
                            }
                        } else {
                            // control name MIGHT be a group name, which we don't want here, so we use control id instead
                            strPostData += "&" + strControlId + "=" + $element.is(":checked");
                        }
                        break;

                    case "select-multiple":
                        $element.find(':selected').each(function() {
                            strPostData += "&" + strControlName + "=" + $j(this).val();
                        });
                        break;

                    default:
                        if (strControlName) {   // this is what gets posted on a server post
                            strPostData += "&" + strControlName + "=";
                        } else {
                            strPostData += "&" + strControlId + "=";
                        }

                        // For Internationalization -- we must escape the element's value properly
                        if (strPostValue) {
                            strPostValue = strPostValue.replace(/\%/g, "%25");
                            strPostValue = strPostValue.replace(/&/g, encodeURIComponent('&'));
                            strPostValue = strPostValue.replace(/\+/g, "%2B");
                        }
                        strPostData += strPostValue;
                        break;
                }
            }
        });
        qcubed.ajaxError = false;
        qcubed.formObjsModified = {};

        return strPostData;
    },

    /**
     * @param {string} strForm The QForm Id
     * @param {string} strControl The Control Id
     * @param {string} strEvent
     * @param {mixed} mixParameter
     * @param {string} strWaitIconControlId The id of the control's spinner.
     * @return {void}
     * @todo There is an eval() in here. We need to find a way around that.
     */
    postAjax: function(strForm, strControl, strEvent, mixParameter, strWaitIconControlId) {
        var objForm = $j('#' + strForm),
            strFormAction = objForm.attr("action"),
            qFormParams = {};

        qFormParams.form = strForm;
        qFormParams.control = strControl;
        qFormParams.event = strEvent;
        qFormParams.param = mixParameter;
        qFormParams.waitIcon = strWaitIconControlId;

        if (strWaitIconControlId) {
            this.objAjaxWaitIcon = this.getWrapper(strWaitIconControlId);
            if (this.objAjaxWaitIcon) {
                this.objAjaxWaitIcon.style.display = 'inline';
            }
        }

        // Use a modified ajax queue so ajax requests happen synchronously
        $j.ajaxQueue({
            url: strFormAction,
            type: "POST",
            qFormParams: qFormParams,
            fnInit: function(o) {
                // Get the data at the last possible instant in case the formstate changes between ajax calls
                o.data = qcubed.getPostData(
                    o.qFormParams.form,
                    o.qFormParams.control,
                    o.qFormParams.event,
                    o.qFormParams.param,
                    o.qFormParams.waitIcon);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                var result = XMLHttpRequest.responseText,
                    objErrorWindow,
                    $dialog;

                qcubed.ajaxError = true;
                if (XMLHttpRequest.status !== 0 || result.length > 0) {
                    if (result.substr(0, 6) === '<html>') {
                        alert("An error occurred during AJAX Response parsing.\r\n\r\nThe error response will appear in a new popup.");
                        objErrorWindow = window.open('about:blank', 'qcubed_error', 'menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=1000,height=700,left=50,top=50');
                        objErrorWindow.focus();
                        objErrorWindow.document.write(result);
                        return false;
                    } else {
                        $dialog = $j('<div id="Qcubed_AJAX_Error" />')
                            .html(result)
                            .dialog({
                                modal: true,
                                width: 'auto',
                                autoOpen: true,
                                title: 'An Error Occurred',
                                buttons: {
                                    Ok: function() {
                                        $dialog.dialog("close");
                                    }
                                }
                            });
                        return false;
                    }
                }
            },
            success: function(json) {
                var strCommands = [];

                qcubed._prevUpdateTime = new Date().getTime();
                if (json.controls) $j.each(json.controls, function() {
                    var strControlId = '#' + this.id,
                        $control = $j(strControlId);

                    if (this.value !== undefined) {
                        $control.val(this.value);
                    }

                    if (this.attributes !== undefined) {
                        $control.attr (this.attributes);
                    }

                    if (this.html !== undefined) {
                        if ($control.length && !$control.get(0).wrapper) {
                            //remove related controls (error, name ...) for wrapper-less controls
                            if ($control.data("hasrel")) {
                                var relSelector = "[data-rel='" + strControlId + "']",
                                    $relParent;

                                //ensure that the control is not wrapped in an element related to it (it would be removed)
                                $relParent = $control.parents(relSelector).last();
                                if ($relParent.length) {
                                    $control.insertBefore($relParent);
                                }
                                $j(relSelector).remove();
                            }

                            $control.before(this.html).remove();
                        } else {
                            $j(strControlId + '_ctl').html(this.html);
                        }
                    }
                });

                if (json.watcher) {
                    if (qFormParams.control) {
                        qcubed.broadcastChange();
                    }
                }
                if (json.commands) {
                    /** @todo eval is evil, do no evil */
                    eval (json.commands);
                }
                if (json.winclose) {
                    window.close();
                }
                if (json.loc) {
                    if (json.loc == 'reload') {
                        window.location.reload(true);
                    } else {
                        document.location = json.loc;
                    }
                }

                if (qcubed.objAjaxWaitIcon) {
                    $j(qcubed.objAjaxWaitIcon).hide();
                }

            }
        });

    },

    /**
     * Start me up.
     */
    initialize: function() {

        ////////////////////////////////
        // Browser-related functionality
        ////////////////////////////////

        this.loadJavaScriptFile = function(strScript, objCallback) {
            if (strScript.indexOf("/") === 0) {
                strScript = qc.baseDir + strScript;
            } else if (strScript.indexOf("http") !== 0) {
                strScript = qc.jsAssets + "/" + strScript;
            }
            $j.ajax({
                url: strScript,
                success: objCallback,
                dataType: "script",
                cache: true
            });
        };

        this.loadStyleSheetFile = function(strStyleSheetFile, strMediaType) {
            if (strStyleSheetFile.indexOf("/") === 0) {
                strStyleSheetFile = qc.baseDir + strStyleSheetFile;
            } else if (strStyleSheetFile.indexOf("http") !== 0) {
                strStyleSheetFile = qc.cssAssets + "/" + strStyleSheetFile;
            }
            if (strMediaType){
                strMediaType = " media="+strMediaType;
            }
            $j('head').append('<link rel="stylesheet"'+strMediaType+' href="' + strStyleSheetFile + '" type="text/css" />');
        };

        /////////////////////////////
        // QForm-related functionality
        /////////////////////////////

        this.wrappers = [];

        if ('localStorage' in window && window['localStorage'] !== null) {
            $j(window).on ("storage", function (o) {
                if (o.originalEvent.key == "qcubed.broadcast") {
                    qcubed.updateForm();
                }
            });
        }

        this.inputSupport = 'oninput' in document;

        // Detect browsers that do not correctly support the oninput event, even though they say they do.
        // IE 9 in particular has a major bug
        var ua = window.navigator.userAgent;
        var intIeOffset = ua.indexOf ('MSIE');
        if (intIeOffset > -1) {
            var intOffset2 = ua.indexOf ('.', intIeOffset + 5);
            var strVersion = ua.substr (intIeOffset + 5, intOffset2 - intIeOffset - 5);
            if (strVersion < 10) {
                this.inputSupport = false;
            }
        }

        return this;
    }
};

///////////////////////////////
// Timers-related functionality
///////////////////////////////

qcubed._objTimers = {};

qcubed.clearTimeout = function(strTimerId) {
    if (qcubed._objTimers[strTimerId]) {
        clearTimeout(qcubed._objTimers[strTimerId]);
        qcubed._objTimers[strTimerId] = null;
    }
};

qcubed.setTimeout = function(strTimerId, action, intDelay) {
    qcubed.clearTimeout(strTimerId);
    qcubed._objTimers[strTimerId] = setTimeout(action, intDelay);
};

///////////////////////////////
// QWatcher support
///////////////////////////////
qcubed._prevUpdateTime = 0;
qcubed.minUpdateInterval = 1000; // milliseconds to limit broadcast updates. Feel free to change this.
qcubed.broadcastChange = function () {
    if ('localStorage' in window && window['localStorage'] !== null) {
        var newTime = new Date().getTime();
        localStorage.setItem("qcubed.broadcast", newTime); // must change value to induce storage event in other windows
    }
};

qcubed.updateForm = function() {
    // call this whenever you generally just need the form to update without a specific action.
    var newTime = new Date().getTime();

    // the following code prevents too many updates from happening in a short amount of time.
    // the default will update no faster than once per second.
    if (newTime - qcubed._prevUpdateTime >= qcubed.minUpdateInterval) {
        //refresh immediately
        var strForm = $j('#Qform__FormId').val();
        qcubed.postAjax (strForm, '', '', '', '');
        qcubed.clearTimeout ('qcubed.update');
    } else if (!qcubed._objTimers['qcubed.update']) {
        // delay to let multiple fast actions only trigger periodic refreshes
        qcubed.setTimeout ('qcubed.update', 'qcubed.updateForm', qcubed.minUpdateInterval);
    }
}


/////////////////////////////////////
// Event Object-related functionality
/////////////////////////////////////

// You may still use this function but be advised
// we no longer use it in core.  All event terminations
// and event bubbling are handled through jQuery.
// see http://trac.qcu.be/projects/qcubed/ticket/681
/**
 * @deprecated
 */
qcubed.terminateEvent = function(objEvent) {
    objEvent = qcubed.handleEvent(objEvent);

    if (objEvent) {
        // Stop Propogation
        if (objEvent.preventDefault) {
            objEvent.preventDefault();
        }
        if (objEvent.stopPropagation) {
            objEvent.stopPropagation();
        }
        objEvent.cancelBubble = true;
        objEvent.returnValue = false;
    }

    return false;
};

/////////////////////////////////
// Controls-related functionality
/////////////////////////////////

qcubed.getControl = function(mixControl) {
    if (typeof mixControl === 'string') {
        return document.getElementById(mixControl);
    } else {
        return mixControl;
    }
};

qcubed.getWrapper = function(mixControl) {
    var objControl = qcubed.getControl(mixControl);

    if (!objControl) {
        //maybe it doesn't have a child control, just the wrapper
        if (typeof mixControl === 'string') {
            return this.getControl(mixControl + "_ctl");
        }
        return null;
    } else if (objControl.wrapper) {
        return objControl.wrapper;
    }

    return objControl; //a wrapper-less control, return the control itself
};

/////////////////////////////
// Register Control - General
/////////////////////////////

qcubed.controlModifications = {};
qcubed.javascriptStyleToQcodo = {};
qcubed.formObjsModified = {};
qcubed.ajaxError = false;
qcubed.javascriptStyleToQcodo.backgroundColor = "BackColor";
qcubed.javascriptStyleToQcodo.borderColor = "BorderColor";
qcubed.javascriptStyleToQcodo.borderStyle = "BorderStyle";
qcubed.javascriptStyleToQcodo.border = "BorderWidth";
qcubed.javascriptStyleToQcodo.height = "Height";
qcubed.javascriptStyleToQcodo.width = "Width";
qcubed.javascriptStyleToQcodo.text = "Text";

qcubed.javascriptWrapperStyleToQcodo = {};
qcubed.javascriptWrapperStyleToQcodo.position = "Position";
qcubed.javascriptWrapperStyleToQcodo.top = "Top";
qcubed.javascriptWrapperStyleToQcodo.left = "Left";

/*
 qcubed.recordControlModification = function(strControlId, strProperty, strNewValue) {
 if (!qcubed.controlModifications[strControlId]) {
 qcubed.controlModifications[strControlId] = {};
 }
 qcubed.controlModifications[strControlId][strProperty] = strNewValue;
 };*/

qcubed.registerControl = function(mixControl) {
    var objControl = qcubed.getControl(mixControl),
        objWrapper;

    if (!objControl) {
        return;
    }

    // Link the Wrapper and the Control together
    objWrapper = this.getControl(objControl.id + "_ctl");
    if (!objWrapper) {
        objWrapper = objControl; //wrapper-less control
    } else {
        objWrapper.control = objControl;
        objControl.wrapper = objWrapper;

        // Add the wrapper to the global qcodo wrappers array
        qcubed.wrappers[objWrapper.id] = objWrapper;
    }


    // Create New Methods, etc.
    // Like: objWrapper.something = xyz;

    /**
     * This function was originally intended to be used by javascript to manipulate QControl objects and have the result
     * reported back to the PHP side. Modern jQuery objects now have events that can be hooked to catch changes to
     * objects, and using those events is probably a better approach in most cases. Various jQuery UI base QControls
     * use this method. In any case, you can use this as a model for how to use the recordControlModification function
     * to send results to PHP objects.
     *
     * @param strStyleName
     * @param strNewValue
     */
    objWrapper.updateStyle = function(strStyleName, strNewValue) {
        var objControl = (this.control) ? this.control : this,
            objNewParentControl,
            objParentControl,
            $this;

        switch (strStyleName) {
            case "className":
                objControl.className = strNewValue;
                qcubed.recordControlModification(objControl.id, "CssClass", strNewValue);
                break;

            case "parent":
                if (strNewValue) {
                    objNewParentControl = qcubed.getControl(strNewValue);
                    objNewParentControl.appendChild(this);
                    qcubed.recordControlModification(objControl.id, "Parent", strNewValue);
                } else {
                    objParentControl = this.parentNode;
                    objParentControl.removeChild(this);
                    qcubed.recordControlModification(objControl.id, "Parent", "");
                }
                break;

            case "displayStyle":
                objControl.style.display = strNewValue;
                qcubed.recordControlModification(objControl.id, "DisplayStyle", strNewValue);
                break;

            case "display":
                $this = $j(this);
                if (strNewValue) {
                    $this.show();
                    qcubed.recordControlModification(objControl.id, "Display", "1");
                } else {
                    $this.hide();
                    qcubed.recordControlModification(objControl.id, "Display", "0");
                }
                break;

            case "enabled":
                if (strNewValue) {
                    objControl.disabled = false;
                    qcubed.recordControlModification(objControl.id, "Enabled", "1");
                } else {
                    objControl.disabled = true;
                    qcubed.recordControlModification(objControl.id, "Enabled", "0");
                }
                break;

            case "width":
            case "height":
                objControl.style[strStyleName] = strNewValue;
                if (qcubed.javascriptStyleToQcodo[strStyleName]) {
                    qcubed.recordControlModification(objControl.id, qcubed.javascriptStyleToQcodo[strStyleName], strNewValue);
                }
                if (this.handle) {
                    this.updateHandle();
                }
                break;

            case "text":
                objControl.innerHTML = strNewValue;
                qcubed.recordControlModification(objControl.id, "Text", strNewValue);
                break;

            default:
                if (qcubed.javascriptWrapperStyleToQcodo[strStyleName]) {
                    this.style[strStyleName] = strNewValue;
                    qcubed.recordControlModification(objControl.id, qcubed.javascriptWrapperStyleToQcodo[strStyleName], strNewValue);
                } else {
                    objControl.style[strStyleName] = strNewValue;
                    if (qcubed.javascriptStyleToQcodo[strStyleName]) {
                        qcubed.recordControlModification(objControl.id, qcubed.javascriptStyleToQcodo[strStyleName], strNewValue);
                    }
                }
                break;
        }
    };

    // Positioning-related functions

    objWrapper.getAbsolutePosition = function() {
        var objControl = (this.control) ? this.control : this,
            pos = $j(objControl).offset();

        return {x: pos.left, y: pos.top};
    };

    objWrapper.setAbsolutePosition = function(intNewX, intNewY, blnBindToParent) {
        var objControl = this.offsetParent;

        while (objControl) {
            intNewX -= objControl.offsetLeft;
            intNewY -= objControl.offsetTop;
            objControl = objControl.offsetParent;
        }

        if (blnBindToParent) {
            if (this.parentNode.nodeName.toLowerCase() !== 'form') {
                // intNewX and intNewY must be within the parent's control
                intNewX = Math.max(intNewX, 0);
                intNewY = Math.max(intNewY, 0);

                intNewX = Math.min(intNewX, this.offsetParent.offsetWidth - this.offsetWidth);
                intNewY = Math.min(intNewY, this.offsetParent.offsetHeight - this.offsetHeight);
            }
        }

        this.updateStyle("left", intNewX + "px");
        this.updateStyle("top", intNewY + "px");
    };

    // Toggle Display / Enabled
    objWrapper.toggleDisplay = function(strShowOrHide) {
        var strDisplay = "display";
        // Toggles the display/hiding of the entire control (including any design/wrapper HTML)
        // If ShowOrHide is blank, then we toggle
        // Otherwise, we'll execute a "show" or a "hide"
        if (strShowOrHide) {
            if (strShowOrHide === "show") {
                this.updateStyle(strDisplay, true);
            } else {
                this.updateStyle(strDisplay, false);
            }
        } else
            this.updateStyle(strDisplay, (this.style.display === "none"));
    };

    objWrapper.toggleEnabled = function(strEnableOrDisable) {
        var objControl = (this.control) ? this.control : this,
            strEnabled = "enabled";

        if (strEnableOrDisable) {
            if (strEnableOrDisable === "enable") {
                this.updateStyle(strEnabled, true);
            } else {
                this.updateStyle(strEnabled, false);
            }
        } else {
            this.updateStyle(strEnabled, objControl.disabled);
        }
    };

    objWrapper.registerClickPosition = function(objEvent) {
        var objControl = (this.control) ? this.control : this,
            intX = objEvent.pageX - objControl.offsetLeft,
            intY = objEvent.pageY - objControl.offsetTop;

        $j('#' + objControl.id + "_x").val(intX);
        $j('#' + objControl.id + "_y").val(intY);
        $j(objControl).trigger('qformObjChanged');
    };

    // Focus
    if (objWrapper.control) {
        objWrapper.focus = function() {
            $j(this.control).focus();
        };
    }

    // Select All (will only work for textboxes)
    if (objWrapper.control) {
        objWrapper.select = function() {
            $j(this.control).select();
        };
    }

    // Blink
    objWrapper.blink = function(strFromColor, strToColor) {
        var objControl = (this.control) ? this.control : this;

        $j(objControl)
            .css('background-color', '' + strFromColor)
            .animate({backgroundColor: '' + strToColor}, 500);
    };
};

qcubed.registerControlArray = function(mixControlArray) {
    var intLength = mixControlArray.length,
        intIndex;

    for (intIndex = 0; intIndex < intLength; intIndex++) {
        this.registerControl(mixControlArray[intIndex]);
    }
};

////////////////////////////////
// QCubed Shortcuts and Initialize
////////////////////////////////

qc = qcubed;
qc.pB = qc.postBack;
qc.pA = qc.postAjax;
qc.getC = qc.getControl;
qc.getW = qc.getWrapper;
qc.regC = qc.registerControl;
qc.regCA = qc.registerControlArray;
qc.recCM = qc.recordControlModification;

qc.initialize();
