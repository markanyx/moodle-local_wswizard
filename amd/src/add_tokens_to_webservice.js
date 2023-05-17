import ModalForm from 'core_form/modalform';
import {get_string as getString} from 'core/str';

export const tokensModal = () => {
    const addToken = document.querySelectorAll("#add_tokens_button");
    addToken.forEach(button => {
        button.addEventListener('click', e => {
            e.preventDefault();
            const element = e.target;
            const modalForm = new ModalForm({
                // Name of the class where form is defined (must extend \core_form\dynamic_form).
                formClass: 'local_wswizard\\forms\\add_token_to_webservice_form',
                // Add as many arguments as you need, they will be passed to the form.
                args: {webserviceid: element.parentElement.getAttribute('data-id')},
                // Pass any configuration settings to the modal dialogue, for example, the title.
                modalConfig: {title: getString('add_new_token', 'local_wswizard')},
                // DOM element that should get the focus after the modal dialogue is closed.
                returnFocus: element,
            });
            // Listen to events if you want to execute something on form submit.
            // Event detail will contain everything the process() function returned.
            modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, function() {
                    window.location.reload();
                }
            );
            // Show the form.
            modalForm.show();
        });
    });
};
