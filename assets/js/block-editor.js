(function (wp) {
    var { __ } = wp.i18n;
    var AfFootnotesButton = function (props) {
        return wp.element.createElement(
            wp.blockEditor.RichTextToolbarButton, {
            icon: wp.element.createElement('span', { 'className': 'af-admin-button' }),
            title: __('Add a Footnote', 'footnotes'),
            onClick: function () {
                props.onChange(wp.richText.toggleFormat(
                    props.value,
                    { type: 'fn/footnote' }
                ));
            },
            isActive: props.isActive,
        }
        );
    }
    wp.richText.registerFormatType(
        'fn/footnote', {
        title: 'Amebae Footnote',
        tagName: 'fn',
        className: null,
        edit: AfFootnotesButton
    }
    );
})(window.wp);
