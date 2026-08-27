document.addEventListener('DOMContentLoaded', function () {

    var overlay = document.getElementById('avps-overlay');
    if (!overlay) {
        return;
    }

    var i18n = (avpsData && avpsData.i18n) || {};

    document.body.classList.add('avps-lock');

    var yesBtn = document.querySelector('.avps-btn-yes:not(#avps-submit)');
    var noBtn = document.querySelector('.avps-btn-no');
    var submitBtn = document.getElementById('avps-submit');

    if (yesBtn) {
        yesBtn.addEventListener('click', function () {
            sendVerification({ confirmed: true });
        });
    }

    if (noBtn) {
        noBtn.addEventListener('click', function () {
            sendVerification({ confirmed: false });
        });
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            var birthdate = document.getElementById('avps-birthdate').value;
            if (!birthdate) {
                showError(i18n.enterDate || 'Please enter your date of birth.');
                return;
            }
            sendVerification({ birthdate: birthdate });
        });
    }

    function sendVerification(data) {
        var body = { action: 'avps_verify', nonce: avpsData.nonce };
        for (var key in data) {
            if (Object.prototype.hasOwnProperty.call(data, key)) {
                body[key] = data[key];
            }
        }

        fetch(avpsData.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(body)
        })
            .then(function (r) { return r.json(); })
            .then(function (response) {
                if (response.success) {
                    overlay.style.display = 'none';
                    document.body.classList.remove('avps-lock');
                    return;
                }

                var payload = response.data || {};

                if (payload.action === 'redirect') {
                    window.location.href = payload.url;
                } else if (payload.action === 'block') {
                    showBlocked(payload.message);
                } else {
                    showError(payload.message);
                    if (payload.lock) {
                        lockBirthdate();
                    }
                }
            })
            .catch(function () {
                showError(i18n.failed || 'Verification failed. Please try again.');
            });
    }

    function showError(message) {
        var err = document.getElementById('avps-error');
        if (err) {
            err.textContent = message;
            err.style.display = 'block';
        }
    }

    /**
     * Age check failed: disable the date field and turn the submit button
     * into a "refresh page" action so the visitor cannot keep retrying.
     */
    function lockBirthdate() {
        var input = document.getElementById('avps-birthdate');
        if (input) {
            input.disabled = true;
        }

        var btn = document.getElementById('avps-submit');
        if (!btn || btn.dataset.avpsLocked === '1') {
            return;
        }

        // Replace the node to drop the original submit listener.
        var reloadBtn = btn.cloneNode(false);
        reloadBtn.dataset.avpsLocked = '1';
        reloadBtn.textContent = i18n.reload || 'Refresh page';
        reloadBtn.addEventListener('click', function () {
            window.location.reload();
        });
        btn.parentNode.replaceChild(reloadBtn, btn);
    }

    function showBlocked(message) {
        var popup = document.createElement('div');
        popup.className = 'avps-popup avps-blocked';
        var text = document.createElement('p');
        // Server-sanitized with wp_kses_post(), safe to render as HTML.
        text.innerHTML = message;
        popup.appendChild(text);
        overlay.innerHTML = '';
        overlay.appendChild(popup);
    }
});
