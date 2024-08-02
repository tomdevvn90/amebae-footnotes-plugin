jQuery(function ($) {

    $(document).ready(function() {
        if ( $('div.helpful').length > 0 ) {
            $("#af-reference-ls").insertBefore("div.helpful");
        }
    });

    $(document).on('mouseenter', '.af-footnote.af-footnote--hover-on-desktop a', null, function (e) {
        if ($(window).width() >= 768) {
            window.modernFootnotesActivelyHovering = true;
            window.modernFootnotesOpenedFootnoteViaHover = true;
            af_show_tooltip_footnote($(this).parent(), true, 'af-footnote__note--opened-by-hover'); //don't transfer focus when hovering - this messes up text highlighting
        }
    });


    $(document).on('mouseenter', '.af-footnote__connector,.af-footnote__note', null, function (e) {
        window.modernFootnotesActivelyHovering = true;
    });


    $(document).on('mouseleave',
        '.af-footnote.af-footnote--hover-on-desktop,' +
        '.af-footnote.af-footnote--hover-on-desktop .af-footnote__connector,' +
        '.af-footnote__note--opened-by-hover', null, function (e) {
            if (window.modernFootnotesHoverCloseTimeout != null) {
                clearTimeout(window.modernFootnotesHoverCloseTimeout);
            }
            if (window.modernFootnotesActivelyHovering) {
                window.modernFootnotesHoverCloseTimeout = setTimeout(function () {
                    window.modernFootnotesHoverCloseTimeout = null;
                    if (!window.modernFootnotesActivelyHovering) {
                        af_hide_footnotes();
                        $(".af-footnote__note--opened-by-hover").removeClass("af-footnote__note--opened-by-hover");
                    }
                }, 600);
            }
            window.modernFootnotesActivelyHovering = false;
        });


    $(document).on('click', '.af-footnote a', null, function (e) {
        e.preventDefault();
        e.stopPropagation();
        next = '.af-footnote__note[data-af="' + $(this).parent().attr("data-af") + '"]';
        var $footnoteContent = $(this).parent().nextAll(next).eq(0);
        if ($footnoteContent.is(":hidden")) {
            if ($(window).width() >= 768 && $(this).parent().is(":not(.af-footnote--expands-on-desktop)")) { //use same size as bootstrap for mobile
                af_show_tooltip_footnote($(this).parent());
                $(this).attr("aria-pressed", "true");
            } else if ($(window).width() < 768 || $(this).parent().is(":not(.af-footnote--hover-on-desktop)")) {
                //expandable style
                $(this).attr("aria-pressed", "true");
                $footnoteContent
                    .removeClass('af-footnote__note--tooltip')
                    .addClass('af-footnote__note--expandable')
                    .css('display', 'block');
                $(this).data('unopenedContent', $(this).html());
                $(this).html('x');
            }
        } else {
            af_hide_footnotes($(this));
        }
    }).on('click', '.af-footnote__note', null, function (e) {
        e.stopPropagation();
    }).on('click', function () {
        if ($(window).width() >= 768 && $(".af-footnote--expands-on-desktop").length == 0) {
            af_hide_footnotes();
        }
    });

    
    $(window).resize(function () {
        af_hide_footnotes();
    });


    var $footnotesAnchorLinks = $("body .af-footnote a");
    var usedReferenceNumbers = {};
    if ($footnotesAnchorLinks.length > 1) {
        $footnotesAnchorLinks.each(function () {
            var postScope = $(this).parent().attr("data-af-post-scope");
            if (typeof usedReferenceNumbers[postScope] === 'undefined') {
                usedReferenceNumbers[postScope] = [0];
            }
            if ($(this).is("a[data-af-reset]")) {
                usedReferenceNumbers[postScope] = [0];
            }
            if ($(this).is("a[refnum]")) {
                var manualRefNum = $(this).attr("refnum");
                if ($(this).html() != manualRefNum) {
                    $(this).html(manualRefNum);
                }
                if (!isNaN(parseFloat(manualRefNum)) && isFinite(manualRefNum)) { //prevent words from being added to this array
                    usedReferenceNumbers[postScope].push(manualRefNum);
                }
            }
            else {
                var refNum = Math.max.apply(null, usedReferenceNumbers[postScope]) + 1;
                if ($(this).html() != refNum) {
                    $(this).html(refNum);
                }
                usedReferenceNumbers[postScope].push(refNum);
            }
        });
    }

});



function af_hide_footnotes($footnoteAnchor) {

    window.modernFootnotesOpenedFootnoteViaHover = false;
    if ($footnoteAnchor != null) {
        if ($footnoteAnchor.data('unopenedContent')) {
            $footnoteAnchor.html($footnoteAnchor.data('unopenedContent'));
        }
        let next = '.af-footnote__note[data-af="' + $footnoteAnchor.parent().attr("data-af") + '"]';
        let $note = $footnoteAnchor.parent().nextAll(next).eq(0);
        $note.hide().css({ 'left': '', 'top': '' });
        $note.next(".af-footnote__connector").remove();
        $footnoteAnchor.removeClass("af-footnote--selected");
        $footnoteAnchor.attr("aria-pressed", "false");
        $footnoteAnchor.focus();
    } else {
        jQuery(".af-footnote a").each(function () {
            var $this = jQuery(this);
            if ($this.data('unopenedContent')) {
                $this.html($this.data('unopenedContent'));
            }
        });
        jQuery(".af-footnote > a").attr("aria-pressed", "false");
        jQuery(".af-footnote__note").hide().css({ 'left': '', 'top': '' });
        jQuery(".af-footnote__connector").remove();
        jQuery(".af-footnote--selected").removeClass("af-footnote--selected");
    }
}

function af_show_tooltip_footnote($footnoteElement, doNotTransferFocus, additionalClass) {

    af_hide_footnotes();
    $footnoteElement.toggleClass('af-footnote--selected');
    let next = '.af-footnote__note[data-af="' + $footnoteElement.attr("data-af") + '"]';
    var $footnoteContent = $footnoteElement.nextAll(next).eq(0);
    $footnoteContent
        .show()
        .addClass('af-footnote__note--tooltip')
        .removeClass('af-footnote__note--expandable');
    if (additionalClass) {
        $footnoteContent.addClass(additionalClass);
    }
    if (!doNotTransferFocus) {
        $footnoteContent.focus();
    }
    //accessibility - close footnote on escape key
    $footnoteContent
        .unbind('keydown')
        .bind('keydown', function (event) {
            if (event.key == 'Escape') {
                af_hide_footnotes($footnoteElement.children('a'));
            }
        });

    var position = $footnoteElement.position();
    var fontHeight = Math.floor(parseInt($footnoteElement.parent().css('font-size').replace(/px/, '')) * 1.5);
    var footnoteWidth = $footnoteContent.outerWidth();
    var windowWidth = jQuery(window).width();
    var left = position.left - footnoteWidth / 2
    if (left < 0) left = 8 // leave some margin on left side of screen
    if (left + footnoteWidth > jQuery(window).width()) left = jQuery(window).width() - footnoteWidth;
    var top = (parseInt(position.top) + parseInt(fontHeight));
    $footnoteContent.css({
        top: top + 'px',
        left: left + 'px'
    });

    $footnoteContent.after('<div class="af-footnote__connector"></div>');
    var superscriptPosition = $footnoteElement.position();
    var superscriptHeight = $footnoteElement.outerHeight();
    var superscriptWidth = $footnoteElement.outerWidth();
    var connectorHeight = top - superscriptPosition.top - superscriptHeight;
    jQuery(".af-footnote__connector").css({
        top: (superscriptPosition.top + superscriptHeight) + 'px',
        height: connectorHeight,
        left: (superscriptPosition.left + superscriptWidth / 2) + 'px'
    });
}
