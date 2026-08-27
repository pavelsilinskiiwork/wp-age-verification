document.addEventListener('DOMContentLoaded', function () {

    var form = document.getElementById('avps-settings-form');
    if (!form) {
        return;
    }

    function setRowsVisible(selector, visible) {
        form.querySelectorAll(selector).forEach(function (row) {
            row.style.display = visible ? '' : 'none';
        });
    }

    function refreshConditionalFields() {
        var scope = form.querySelector('input[name="scope"]:checked');
        setRowsVisible('.avps-scope-specific', scope && scope.value === 'specific');

        var type = form.querySelector('input[name="verification_type"]:checked');
        setRowsVisible('.avps-type-buttons', type && type.value === 'buttons');

        var decline = form.querySelector('input[name="decline_action"]:checked');
        setRowsVisible('.avps-decline-redirect', decline && decline.value === 'redirect');
        setRowsVisible('.avps-decline-block', decline && decline.value === 'block');
    }

    form.addEventListener('change', function (e) {
        if (['scope', 'verification_type', 'decline_action'].indexOf(e.target.name) !== -1) {
            refreshConditionalFields();
        }
    });

    refreshConditionalFields();

    var notice = document.getElementById('avps-notice');
    var spinner = document.getElementById('avps-spinner');

    function showNotice(message, isError) {
        notice.className = 'notice ' + (isError ? 'notice-error' : 'notice-success');
        notice.querySelector('p').textContent = message;
        notice.style.display = '';
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var data = new FormData(form);
        data.append('action', 'avps_save_settings');
        data.append('nonce', avpsAdmin.nonce);

        spinner.classList.add('is-active');

        fetch(avpsAdmin.ajaxUrl, { method: 'POST', body: new URLSearchParams(data) })
            .then(function (r) { return r.json(); })
            .then(function (response) {
                if (response.success) {
                    showNotice((response.data && response.data.message) || avpsAdmin.saved, false);
                } else {
                    showNotice((response.data && response.data.message) || avpsAdmin.error, true);
                }
            })
            .catch(function () {
                showNotice(avpsAdmin.error, true);
            })
            .finally(function () {
                spinner.classList.remove('is-active');
            });
    });
});
