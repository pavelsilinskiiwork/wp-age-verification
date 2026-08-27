document.addEventListener('DOMContentLoaded', function () {

    var overlay = document.getElementById('wav-overlay');
    if (!overlay) {
        return;
    }

    document.body.classList.add('wav-lock');

    var yesBtn = document.querySelector('.wav-btn-yes:not(#wav-submit)');
    var noBtn = document.querySelector('.wav-btn-no');
    var submitBtn = document.getElementById('wav-submit');

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
            var birthdate = document.getElementById('wav-birthdate').value;
            if (!birthdate) {
                showError(wavData.i18n && wavData.i18n.enterDate ? wavData.i18n.enterDate : 'Please enter your date of birth.');
                return;
            }
            sendVerification({ birthdate: birthdate });
        });
    }

    function sendVerification(data) {
        var body = { action: 'wav_verify', nonce: wavData.nonce };
        for (var key in data) {
            if (Object.prototype.hasOwnProperty.call(data, key)) {
                body[key] = data[key];
            }
        }

        fetch(wavData.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(body)
        })
            .then(function (r) { return r.json(); })
            .then(function (response) {
                if (response.success) {
                    overlay.style.display = 'none';
                    document.body.classList.remove('wav-lock');
                    return;
                }

                var payload = response.data || {};

                if (payload.action === 'redirect') {
                    window.location.href = payload.url;
                } else if (payload.action === 'block') {
                    showBlocked(payload.message);
                } else {
                    showError(payload.message);
                }
            })
            .catch(function () {
                showError('Verification failed. Please try again.');
            });
    }

    function showError(message) {
        var err = document.getElementById('wav-error');
        if (err) {
            err.textContent = message;
            err.style.display = 'block';
        }
    }

    function showBlocked(message) {
        var popup = document.createElement('div');
        popup.className = 'wav-popup wav-blocked';
        var text = document.createElement('p');
        text.textContent = message;
        popup.appendChild(text);
        overlay.innerHTML = '';
        overlay.appendChild(popup);
    }
});
