document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('buy-cow-form');
    var bcDiv = document.getElementById('buy-cow-div');
    if (!form || !bcDiv) {
        return;
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var button = form.querySelector('button');
        button.disabled = true;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-Token': form.querySelector('input[name="_csrf"]').value
            },
            body: new FormData(form)
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Failed with status ' + response.status);
            }

            bcDiv.innerHTML = '<p class="thank-you">thankYou</p>';
        }).catch(function () {
            button.disabled = false;
            form.submit();
        });
    });
});
