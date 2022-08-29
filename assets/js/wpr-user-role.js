
document.addEventListener("DOMContentLoaded", function () {

    let roles_dom = document.querySelector('#wpr_user_role_price_tab_data');
    let submit_button = roles_dom.querySelector('.save-wpr-user-role');

    submit_button.addEventListener('click', (e) => {

        let rules = [];

        let saving_roles_status = document.querySelector('.saving-roles-status');
        
        if( saving_roles_status ) {
            saving_roles_status.parentNode.removeChild(saving_roles_status);
        }

        let alert = document.createElement('div');
        alert.classList.add('saving-roles-status');
        alert.classList.add('success');

        alert.innerHTML +=
            `<div class="in-progress">
                <span class="message">Updating !</span>
                <div class="loader loader--style3" title="2">
                    <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" xml:space="preserve">
                        <path fill="#000" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
                            <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/>
                        </path>
                    </svg>
                </div>
            </div>
            <span class="errors"></span>`;

        document.querySelector('body').append(alert);

        roles_dom.querySelectorAll('.row').forEach(el => {
            rules.push({
                role: el.getAttribute('data-key'),
                regular: el.querySelector('.role-regular-price').value,
                sale: el.querySelector('.role-sale-price').value,
            });
        });

        let xhr = new XMLHttpRequest();
        xhr.open('POST', wpr_user_role_js.root + '/update', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-WP-Nonce', wpr_user_role_js.nonce)
        xhr.onreadystatechange = function () {
            if (this.readyState != 4) return;

            if (this.status == 200) {
                let data = JSON.parse(this.responseText);

                let loader = document.querySelector('.saving-roles-status .in-progress .loader');

                if ('error' !== data.status) {
                    document.querySelector('.saving-roles-status .message').innerText = `Updated !`;

                    if( loader ) {
                        loader.parentNode.removeChild(loader);
                    }
                } else {
                    document.querySelector('.saving-roles-status').classList.add('error');

                    let error = document.createElement('span');
                    error.classList.add('error');
                    error.innerText = data.message;

                    document.querySelector('.saving-roles-status .errors').append(error);

                    if( loader ) {
                        loader.parentNode.removeChild(loader);
                    }
                }
            }

            setTimeout(function () {
                saving_roles_status = document.querySelector('.saving-roles-status');
                if( saving_roles_status ) {
                    saving_roles_status.parentNode.removeChild(saving_roles_status);
                }
            }, 1000);
        };
        xhr.send(JSON.stringify({ post_id: acf.data.post_id, rules: rules }));

    });
});
