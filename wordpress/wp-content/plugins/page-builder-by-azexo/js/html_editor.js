function createEditor(editWindow, editDoc) {

    if (!Array.prototype.map) {
        Array.prototype.map = function(fun) {
            var collect = [];
            for (var ix = 0; ix < this.length; ix++) {
                collect[ix] = fun(this[ix]);
            }
            return collect;
        }
    }

    function bindEvent(target, eventName, fun) {
        if (target.addEventListener) {
            target.addEventListener(eventName, fun, false);
        } else {
            target.attachEvent("on" + eventName, function(event) {
                fun(event);
            });
        }
    }

    /*
     Editlib.js
     ----------
     Various functions for manipulating selections, used by editing commands
     */

    var getContaining = (window.getSelection) ? w3_getContaining : ie_getContaining;
    var overwriteWithNode = (window.getSelection) ? w3_overwriteWithNode : ie_overwriteWithNode;

    function createElementFilter(tagName) {
        return function(elem) {
            return elem.tagName == tagName;
        }
    }

    /* walks up the hierachy until an element with the tagName if found.
     Returns null if no element is found before BODY */
    function getAncestor(elem, filter) {
        while (elem.tagName != "BODY") {
            if (filter(elem))
                return elem;
            elem = elem.parentNode;
        }
        return null;
    }

    function includes(elem1, elem2) {
        if (elem2 == elem1)
            return true;
        while (elem2.parentNode && elem2.parentNode) {
            if (elem2 == elem1)
                return true;
            elem2 = elem2.parentNode;
        }
        return false;
    }

    function ie_getContaining(editWindow, filter) {
        var selection = editWindow.document.selection;
        if (selection.type == "Control") {
            // control selection
            var range = selection.createRange();
            if (range.length == 1) {
                var elem = range.item(0);
            }
            else {
                // multiple control selection 
                return null;
            }
        } else {
            var range = selection.createRange();
            var elem = range.parentElement();
        }
        return getAncestor(elem, filter);
    }

    function ie_overwriteWithNode(editWindow, node) {
        var rng = editWindow.document.selection.createRange();
        var marker = writeMarkerNode(editWindow, rng);
        marker.appendChild(node);
        marker.removeNode(); // removes node but not children
    }

// writes a marker node on a range and returns the node.
    function writeMarkerNode(editWindow, rng) {
        var id = editWindow.document.uniqueID;
        var html = "<span id='" + id + "'></span>";
        rng.pasteHTML(html);
        var node = editWindow.document.getElementById(id);
        return node;
    }

// overwrites the current selection with a node
    function w3_overwriteWithNode(editWindow, node) {
        if (editWindow.getSelection() && editWindow.getSelection().rangeCount > 0) {
            var rng = editWindow.getSelection().getRangeAt(0);
            rng.deleteContents();
            if (isTextNode(rng.startContainer)) {
                var refNode = rightPart(rng.startContainer, rng.startOffset)
                refNode.parentNode.insertBefore(node, refNode);
            } else {
                if (rng.startOffset == rng.startContainer.childNodes.length) {
                    refNode.parentNode.appendChild(node);
                } else {
                    var refNode = rng.startContainer.childNodes[rng.startOffset];
                    refNode.parentNode.insertBefore(node, refNode);
                }
            }
        }
    }

    function w3_getContaining(editWindow, filter) {
        if (editWindow.getSelection() && editWindow.getSelection().rangeCount > 0) {
            var range = editWindow.getSelection().getRangeAt(0);
            var container = range.commonAncestorContainer;
            return getAncestor(container, filter);
        } else {
            return false;
        }
    }

    function isTextNode(node) {
        return node.nodeType == 3;
    }

    function rightPart(node, ix) {
        return node.splitText(ix);
    }
    function leftPart(node, ix) {
        node.splitText(ix);
        return node;
    }

    /*
     Commands
     --------
     */

    function Command(command, editDoc) {
        this.execute = function() {
            editDoc.execCommand(command, false, null);
        };
        this.queryState = function() {
            return editDoc.queryCommandState(command);
        };
    }

    function ValueCommand(command, editDoc) {
        this.execute = function(value) {
            editDoc.execCommand(command, false, value ? value : null);
        };
        this.queryValue = function() {
            return editDoc.queryCommandValue(command);
        };
    }

    function LinkCommand(editDoc) {
        var tagFilter = createElementFilter("A");
        this.execute = function() {
            var a = getContaining(editWindow, tagFilter);
            var initialUrl = a ? a.href : "http://";
            var url = window.prompt("Enter an URL:", initialUrl);
            if (url === null)
                return;
            if (url === "") {
                editDoc.execCommand("unlink", false, null);
            } else {
                editDoc.execCommand("createLink", false, url);
            }
        };
        this.queryState = function() {
            return !!getContaining(editWindow, tagFilter);
        };
    }

    function InsertHelloWorldCommand() {
        this.execute = function() {
            var elem = editWindow.document.createElement("SPAN");
            elem.style.backgroundColor = "red";
            elem.innerHTML = "Hello world!";
            overwriteWithNode(editWindow, elem);
        }
        this.queryState = function() {
            return false;
        }
    }

    /*
     
     Controllers
     -----------
     Connects Command-obejcts to DOM nodes which works as UI
     
     */

    function TogglCommandController(command, elem) {
        this.updateUI = function() {
            if (command.queryState()) {
                elem.classList.add("azh-active");
            } else {
                elem.classList.remove("azh-active");
            }
        }
        var self = this;
        elem.unselectable = "on"; // IE, prevent focus
        bindEvent(elem, "mousedown", function(evt) {
            // we cancel the mousedown default to prevent the button from getting focus
            // (doesn't work in IE)
            if (evt.preventDefault)
                evt.preventDefault();
        });
        bindEvent(elem, "click", function(evt) {
            command.execute();
            updateToolbar();
            evt.stopPropagation();
        });
    }
    function ValueSelectorController(command, elem) {
        this.updateUI = function() {
            var value = command.queryValue();
            elem.value = value;
        }
        var self = this;
        elem.unselectable = "on"; // IE, prevent focus		
        bindEvent(elem, "change", function(evt) {
            editWindow.focus();
            command.execute(elem.value);
            updateToolbar();
        });
    }
    function ValueController(command, elem) {
        this.updateUI = function() {
            var value = command.queryValue();
            elem.value = value;
        }
        var self = this;
        bindEvent(elem, "change", function(evt) {
            editWindow.focus();
            command.execute(elem.value);
            updateToolbar();
        });
    }

    function ColorController(command, elem) {
        this.updateUI = function() {
            function rgb2hex(rgb) {
                function hex(x) {
                    return ("0" + parseInt(x).toString(16)).slice(-2);
                }
                return "#" + hex(rgb[0]) + hex(rgb[1]) + hex(rgb[2]);
            }
            var value = command.queryValue();
            var rgba = value.replace(/^rgba?\(|\s+|\)$/g, '').split(',');
            elem.value = rgb2hex(rgba);
        }
        var self = this;
        bindEvent(elem, "change", function(evt) {
            editWindow.focus();
            command.execute(elem.value);
            updateToolbar();
        });
    }


    var updateListeners = [];

    var toolbarCommands = [
        ["boldButton", TogglCommandController, new Command("bold", editDoc)],
        ["italicButton", TogglCommandController, new Command("italic", editDoc)],
        ["leftButton", TogglCommandController, new Command("justifyLeft", editDoc)],
        ["rightButton", TogglCommandController, new Command("justifyRight", editDoc)],
        ["centerButton", TogglCommandController, new Command("justifyCenter", editDoc)],
        ["linkButton", TogglCommandController, new LinkCommand(editDoc)],
        ["orderedListButton", TogglCommandController, new Command("insertOrderedList", editDoc)],
        ["unorderedListButton", TogglCommandController, new Command("insertUnorderedList", editDoc)],
        ["sizeSelector", ValueSelectorController, new ValueCommand("fontSize", editDoc)],
        ["colorInput", ColorController, new ValueCommand("foreColor", editDoc)],
        ["colorBgInput", ColorController, new ValueCommand("backColor", editDoc)],
        ["removeFormatButton", TogglCommandController, new Command("removeFormat", editDoc)]
    ];

    toolbarCommands.map(function(binding) {
        var elemId = binding[0], ControllerConstructor = binding[1], command = binding[2];
        var elem = document.getElementById(elemId);
        if (elem) {
            var controller = new ControllerConstructor(command, elem);
            updateListeners.push(controller);
        }
    });

    function updateToolbar() {
        updateListeners.map(function(controller) {
            controller.updateUI();
        });
    }

    bindEvent(editDoc, "keyup", updateToolbar);
    bindEvent(editDoc, "mouseup", updateToolbar);
}

(function($) {
    var font_sizes = {
        '': 'default',
        1: 'xx-small',
        2: 'x-small',
        3: 'small',
        4: 'medium',
        5: 'large',
        6: 'x-large',
        7: 'xx-large'
    };
    var fontSize = '<select id="sizeSelector">';
    for (var value in font_sizes) {
        fontSize += '<option value="' + value + '" style="font-size:' + font_sizes[value] + ';">' + font_sizes[value] + '</option>';
    }
    fontSize += '</select>';
    var $toolbar = $('<div class="azh-editor-toolbar"><span id="boldButton" class="dashicons dashicons-editor-bold"></span><span id="italicButton" class="dashicons dashicons-editor-italic"></span><span id="linkButton" class="dashicons dashicons-admin-links"></span><span id="leftButton" class="dashicons dashicons-editor-alignleft"></span><span id="centerButton" class="dashicons dashicons-editor-aligncenter"></span><span id="rightButton" class="dashicons dashicons-editor-alignright"></span><span id="orderedListButton" class="dashicons dashicons-editor-ol"></span><span id="unorderedListButton" class="dashicons dashicons-editor-ul"></span><input id="colorInput" type="color"><input id="colorBgInput" type="color">' + fontSize + '<span id="removeFormatButton" class="dashicons dashicons-editor-removeformatting"></span></div>').appendTo('body').hide();
    $(document).off('click.azh-editor-toolbar').on('click.azh-editor-toolbar', function(event) {
        if (!$(event.target).closest('.azh-editor-toolbar, [contenteditable="true"]').length) {
            $toolbar.hide();
        }
    });
    $(function() {
        if (azh.editor_toolbar.length) {
            $toolbar.find('[id]').each(function() {
                var $this = $(this);
                if (azh.editor_toolbar.indexOf($this.attr('id')) < 0) {
                    $this.remove();
                }
            });
        }
    })
})(window.jQuery);