/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$('#id_wsuser').change(wsUserdisable);
$('#id_wsroleselect').change(wsRoledisable);

function wsUserdisable() {
    var wschoice = $('#id_wsuser').find(":selected").text();
    if (wschoice === 'Choose existing') {
//        console.log($('#id_wsuser').find(":selected").text());
        $('#id_wsusers').prop('required', true);
        $('#id_username').prop('required', false);
        $('#id_email').prop('required', false);
        $('#id_firstname').prop('required', false);
        $('#id_lastname').prop('required', false);
    } else if (wschoice === 'Add new') {
        //disable existing
        $('#id_wsusers').prop('required', false);
        $('#id_username').prop('required', true);
        $('#id_email').prop('required', true);
        $('#id_firstname').prop('required', true);
        $('#id_lastname').prop('required', true);

//        console.log($('#id_wsuser').find(":selected").text());
    }
}
function wsRoledisable() {
    var wsrolechoice = $('#id_wsroleselect').find(":selected").text();
    if (wsrolechoice === 'Choose existing') {
//        console.log($('#id_wsroleselect').find(":selected").text());
        $('#id_wsroles').prop('required', true);
        $('#id_rolename').prop('required', false);
        $('#id_shortname').prop('required', false);
        $('#id_description').prop('required', false);
    } else if (wsrolechoice === 'Add new') {
        //disable existing
        $('#id_wsroles').prop('required', false);
        $('#id_rolename').prop('required', true);
        $('#id_shortname').prop('required', true);
        $('#id_description').prop('required', true);

//        console.log($('#id_wsroleselect').find(":selected").text());
    }

}

