export function getFormData(domQuery){
    const out = {};
    const data = $(domQuery).serializeArray();

    for(let i = 0; i < data.length; i++){
        const record = data[i];
        out[record.name] = record.value;
    }
    return out;
}

export function getResponseMessage(errorData) {
    let message = '';
    if (typeof errorData === 'object') {
        for (let key in errorData) {
            if (errorData.hasOwnProperty(key)) {
                message += errorData[key] + '</br>';
            }
        }
    } else {
        message = errorData;
    }

    return message;
}