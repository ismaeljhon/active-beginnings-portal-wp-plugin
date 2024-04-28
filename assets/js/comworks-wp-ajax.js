function checkUsername(username, currentUserId) {
    return new Promise(function(resolve, reject) {
        var data = {
            'action': 'check_username',
            'username': username,
            'current_user_id': currentUserId
        };

        jQuery.post(my_ajax_object.ajax_url, data, function(response) {
            console.log(response)
            if (response.success) {
                resolve(response.data);
            } else {
                reject(response.error);
            }
        })
    })
}


function check_username(username, currentUserId) {
    var data = {
        'action': 'check_username',
        'username': username,
        'current_user_id': currentUserId
    };

    jQuery.post(my_ajax_object.ajax_url, data, function(response) {
        console.log(response)
        if (response.data) {
            return true
        }

        return false
    })
}