document.addEventListener('DOMContentLoaded', function () {

    var form = document.getElementById('wav-settings-form');
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
        setRowsVisible('.wav-scope-specific', scope && scope.value === 'specific');

        var type = form.querySelector('input[name="verification_type"]:checked');
        setRowsVisible('.wav-type-buttons', type && type.value === 'buttons');

        var decline = form.querySelector('input[name="decline_action"]:checked');
        setRowsVisible('.wav-decline-redirect', decline && decline.value === 'redirect');
        setRowsVisible('.wav-decline-block', decline && decline.value === 'block');
    }

    form.addEventListener('change', function (e) {
        if (['scope', 'verification_type', 'decline_action'].indexOf(e.target.name) !== -1) {
            refreshConditionalFields();
        }
    });

    refreshConditionalFields();

    var notice = document.getElementById('wav-notice');
    var spinner = document.getElementById('wav-spinner');

    function showNotice(message, isError) {
        notice.className = 'notice ' + (isError ? 'notice-error' : 'notice-success');
        notice.querySelector('p').textContent = message;
        notice.style.display = '';
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var data = new FormData(form);
        data.append('action', 'wav_save_settings');
        data.append('nonce', wavAdmin.nonce);

        spinner.classList.add('is-active');

        fetch(wavAdmin.ajaxUrl, { method: 'POST', body: new URLSearchParams(data) })
            .then(function (r) { return r.json(); })
            .then(function (response) {
                if (response.success) {
                    showNotice((response.data && response.data.message) || wavAdmin.saved, false);
                } else {
                    showNotice((response.data && response.data.message) || wavAdmin.error, true);
                }
            })
            .catch(function () {
                showNotice(wavAdmin.error, true);
            })
            .finally(function () {
                spinner.classList.remove('is-active');
            });
    });
});
