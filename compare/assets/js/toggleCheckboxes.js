/**
 * Created by khalil on 8/13/16.
 *
 * Checkboxes Relater is a plugin to select and de-select all checkboxes
 */

(function ($) {

    $.fn.toggleCheckboxes = function () {

        // Children Action
        var childCheckboxes = this.find('tbody input[type=checkbox]');

        childCheckboxes.change(function () {
            var current = $(this);

            var parentCheckbox = current.parents('table').find('input[type=checkbox].toggle-checkboxes');

            var anyChildrenChecked = current.closest('table').find("tbody input[type=checkbox]").is(":checked");

            $(parentCheckbox).prop("checked", anyChildrenChecked);

            /** Specific for datatables */
            if(this.checked) {
                current.parents('tr').addClass('selected');
            } else {
                current.parents('tr').removeClass('selected');
            }
        });

        // Parent Action
        childCheckboxes.closest("table").find("input[type=checkbox].toggle-checkboxes").change(function () {
            var current = $(this);
            current.parents('table').find('tbody input[type=checkbox]').prop("checked", current.prop("checked"));

            /** Specific for datatables */
            if(current.prop("checked")) {
                current.parents('table').find('tbody tr').addClass('selected');
            } else {
                current.parents('table').find('tbody tr').removeClass('selected');
            }
        });

        return this;
    };

}(jQuery));

