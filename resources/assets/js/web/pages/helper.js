export function getFormData(domQuery){
    const out = {};
    const data = $(domQuery).serializeArray();

    for(let i = 0; i < data.length; i++){
        const record = data[i];
        out[record.name] = record.value;
    }
    return out;
}

export function getResponseMessage(data) {
    let message = '';
    if (typeof data === 'object') {
        for (let key in data) {
            if (data.hasOwnProperty(key)) {
                message += data[key] + '</br>';
            }
        }
    } else {
        message = data;
    }

    return message;
}