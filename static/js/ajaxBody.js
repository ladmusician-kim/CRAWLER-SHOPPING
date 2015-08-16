function getJson(url, data, success, error, dataType) {
    var data_type = "json";
    if(dataType) {
        data_type = dataType
    } 

    $.ajax({
        'url': url,
        'type': "POST",
        'dataType': data_type,
        'contentType': 'application/json; charset=utf-8',
        'data': JSON.stringify(data),
        'xhrFields': {
            withCredentials: true
        },
        'success': function (data) {
            success(data)
        },
        'error': function (arg) {
            if (error) {
               error(arg);
            } else {
                alert("error");
            }
        },
    });
}