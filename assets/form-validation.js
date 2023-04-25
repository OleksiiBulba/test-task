// jQuery's library is available by webpack, so we don't need to import it manually
// import $ from 'jquery'

function showError($element, errMsg) {
    if (0 === $element.siblings('.invalid-feedback').length) {
        $('<div>').addClass('invalid-feedback').insertAfter($element);
    }
    $element.siblings('.invalid-feedback').text(errMsg);
    $element.addClass('is-invalid');
}

function clearError($element) {
    $element.removeClass('is-invalid');
    $element.siblings('.invalid-feedback').text('');
}

function isEmail(email) {
    const regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(email);
}

$(document).ready(function () {
    let $symbolSelect = $('#history_data_form_symbol');
    let $startDateSelect = $('#history_data_form_startDate');
    let $endDateSelect = $('#history_data_form_endDate');
    let $emailInput = $('#history_data_form_email');

    $('#history_data_form_submit').on('click', function (e) {
        $('.invalid-feedback').text('');

        if ('' === $symbolSelect.val()) {
            showError($symbolSelect, 'Company Symbol is required.');
            e.preventDefault();
        }

        const startDate = $startDateSelect.val();
        if ('' === startDate) {
            showError($startDateSelect, 'Start date is required.');
            e.preventDefault();
        } else {
            if (Date.parse(startDate) > Date.now()) {
                showError($startDateSelect, 'Start date should be less or equal than today.')
            }
        }

        const endDate = $endDateSelect.val();
        if ('' === endDate) {
            showError($endDateSelect, 'End date is required.');
            e.preventDefault();
        } else {
            if (Date.parse(endDate) > Date.now()) {
                showError($endDateSelect, 'End date should be less or equal than today.')
            }
        }

        if ('' !== startDate && '' !== endDate) {
            if (Date.parse(startDate) > Date.parse(endDate)) {
                showError($startDateSelect, 'Start date should be less or equal then end date.');
                showError($endDateSelect, 'End date should be later or equal then start date.');
            }
        }

        const email = $emailInput.val().trim();
        if ('' === email) {
            showError($emailInput, 'Email is required.');
            e.preventDefault();
        } else if (!isEmail(email)) {
            showError($emailInput, 'Email is invalid.');
            e.preventDefault();
        }
    });

    $symbolSelect.on('change', function () {
        // When symbol select is changed to some non-empty value, we can clear error.
        if ('' !== $(this).val().trim()) {
            clearError($(this));
        }
    });

    $startDateSelect.on('change', function () {
        if ('' !== $(this).val()) {
            clearError($(this));
        }
    });

    $endDateSelect.on('change', function () {
        if ('' !== $(this).val()) {
            clearError($(this));
        }
    });

    $emailInput.on('input', function () {
        if ('' !== $(this).val()) {
            clearError($(this));
        }
    });
});
