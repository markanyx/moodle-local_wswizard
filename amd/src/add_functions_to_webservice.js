import ModalForm from 'core_form/modalform';
import {get_string as getString} from 'core/str';

// .....

// In this example we will open the modal dialogue with the form when user clicks on the edit link:

export const initModal = () => {
    const add_function_button = document.querySelectorAll("#add_function_button");

    add_function_button.forEach(button => {
        button.addEventListener('click', e => {
            e.preventDefault();

            const element = e.target;
            const modalForm = new ModalForm({
                // Name of the class where form is defined (must extend \core_form\dynamic_form):
                formClass: 'local_wswizard\\forms\\add_functions_to_webservice_form',
                // Add as many arguments as you need, they will be passed to the form:
                args: {webservice_id: element.parentElement.getAttribute('data-id'),
                    ws_role_id: element.parentElement.getAttribute('data-roleid')},
                // Pass any configuration settings to the modal dialogue, for example, the title:
                modalConfig: {title: getString('add_new_functions', 'local_wswizard')},
                // DOM element that should get the focus after the modal dialogue is closed:
                returnFocus: element,
            });
            // Listen to events if you want to execute something on form submit.
            // Event detail will contain everything the process() function returned:
            modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, function() {
                window.location.reload();
            });
            // Show the form.
            modalForm.show();
        });
    });
};
